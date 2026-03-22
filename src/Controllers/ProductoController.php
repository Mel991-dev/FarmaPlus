<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Models\ProductoModel;
use App\Services\AlertaService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * ProductoController — CRUD del catálogo farmacéutico.
 *
 * Módulo 4: Gestión de Productos e Inventario (RF-4.1, RF-4.3)
 * - Registro con INVIMA obligatorio (RN-01)
 * - Tipo de venta: libre / control especial (RN-02)
 * - Búsqueda por nombre, categoría, laboratorio y lote
 */
class ProductoController
{
    private ProductoModel $productoModel;
    private AlertaService $alertaService;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $this->productoModel = new ProductoModel($db);
        $this->alertaService = new AlertaService($db);
    }

    /** GET /inventario/productos */
    public function listar(Request $request, Response $response): Response
    {
        $db = Database::getInstance()->getConnection();
        
        // Obtener productos con stock
        $productos = $this->productoModel->listarConStock();
        
        // Obtener categorías y proveedores para filtros
        $categoriaModel = new \App\Models\CategoriaModel($db);
        $proveedorModel = new \App\Models\ProveedorModel($db);
        $categorias = $categoriaModel->listar();
        $proveedores = $proveedorModel->listar();
        
        // Calcular estadísticas
        $totalProductos = count($productos);
        $productosStockBajo = 0;
        $productosStockOk = 0;
        $valorInventario = 0;
        $productosAlerta = [];
        
        foreach ($productos as $p) {
            $stockActual = (int)($p['stock_actual'] ?? 0);
            $stockMinimo = (int)($p['stock_minimo'] ?? 10);
            $valorInventario += $stockActual * ($p['precio_compra'] ?? 0);
            
            if ($stockActual <= $stockMinimo) {
                $productosStockBajo++;
                $productosAlerta[] = $p;
            } else {
                $productosStockOk++;
            }
        }
        
        $valorInventario = $valorInventario / 1000000; // Convertir a millones
        
        // Variables para la vista
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        
        // Renderizar vista
        ob_start();
        include __DIR__ . '/../../views/inventario/productos.php';
        $contenido = ob_get_clean();
        
        $response->getBody()->write($contenido);
        return $response;
    }

    /** GET /inventario/productos/crear */
    public function mostrarCrear(Request $request, Response $response): Response
    {
        $db = Database::getInstance()->getConnection();
        
        // Obtener categorías y proveedores para el formulario
        $categoriaModel = new \App\Models\CategoriaModel($db);
        $proveedorModel = new \App\Models\ProveedorModel($db);
        $categorias = $categoriaModel->listar();
        $proveedores = $proveedorModel->listar();
        
        // Variables para la vista
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        
        // Renderizar vista
        ob_start();
        include __DIR__ . '/../../views/inventario/producto_form.php';
        $contenido = ob_get_clean();
        
        $response->getBody()->write($contenido);
        return $response;
    }

    /** POST /inventario/productos/crear */
    public function crear(Request $request, Response $response): Response
    {
        $data     = $request->getParsedBody();
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');

        // Validar que INVIMA esté presente (RN-01)
        if (empty($data['codigo_invima'])) {
            return $response
                ->withHeader('Location', $basePath . '/inventario/productos/crear?error=' . urlencode('El código INVIMA es obligatorio'))
                ->withStatus(302);
        }

        try {
            $db = Database::getInstance()->getConnection();
            if (!$db->inTransaction()) {
                $db->beginTransaction();
            }

            $datos = [
                ':nombre'            => $data['nombre'],
                ':principio_activo'  => $data['principio_activo'] ?? null,
                ':concentracion'     => $data['concentracion'] ?? null,
                ':forma_farmaceutica'=> $data['forma_farmaceutica'] ?? null,
                ':codigo_invima'     => $data['codigo_invima'],
                ':categoria_id'      => !empty($data['categoria_id']) ? (int)$data['categoria_id'] : null,
                ':proveedor_id'      => !empty($data['proveedor_id']) ? (int)$data['proveedor_id'] : null,
                ':control_especial'  => isset($data['control_especial']) ? 1 : 0,
                ':precio_compra'     => $data['precio_compra'] ?? 0,
                ':precio_venta'      => $data['precio_venta'] ?? 0,
                ':stock_minimo'      => $data['stock_minimo'] ?? 10,
            ];

            $productoId = (int) $this->productoModel->crear($datos);

            // ── Lote de apertura automático ──────────────────────────────────────
            // Si el formulario incluye stock_inicial > 0, creamos un lote inicial
            // con fecha de vencimiento a 10 años. El usuario podrá ajustarlo luego.
            $stockInicial = (int)($data['stock_inicial'] ?? 0);
            if ($stockInicial > 0) {
                $loteModel  = new \App\Models\LoteModel($db);
                $ajusteModel = new \App\Models\AjusteStockModel($db);

                $fechaVencimiento = date('Y-m-d', strtotime('+10 years'));
                $numeroLote       = 'APERTURA-' . strtoupper(substr(md5($productoId . time()), 0, 6));

                $loteId = (int) $loteModel->registrar([
                    ':producto_id'       => $productoId,
                    ':proveedor_id'      => !empty($data['proveedor_id']) ? (int)$data['proveedor_id'] : null,
                    ':numero_lote'       => $numeroLote,
                    ':cantidad_inicial'  => $stockInicial,
                    ':fecha_vencimiento' => $fechaVencimiento,
                    ':registrado_por'    => (int)($_SESSION['usuario_id'] ?? 1),
                ]);

                // Registrar movimiento para trazabilidad (no crítico)
                try {
                    $ajusteModel->registrar(
                        productoId:  $productoId,
                        loteId:      $loteId,
                        usuarioId:   (int)($_SESSION['usuario_id'] ?? 1),
                        tipo:        'entrada',
                        cantidad:    $stockInicial,
                        observacion: 'Stock inicial al crear el producto'
                    );
                } catch (\Throwable) {
                    // Tabla ajustes_stock puede no existir en BD antigua — continuar
                }
                // Verificar si ya hay alertas de stock
                $this->alertaService->verificarProducto($productoId);
            }

            $db->commit();

            return $response
                ->withHeader('Location', $basePath . '/inventario/productos?success=' . urlencode('Producto creado exitosamente'))
                ->withStatus(302);

        } catch (\Exception $e) {
            try {
                if (isset($db) && $db->inTransaction()) {
                    $db->rollBack();
                }
            } catch (\Throwable) {}
            return $response
                ->withHeader('Location', $basePath . '/inventario/productos/crear?error=' . urlencode($e->getMessage()))
                ->withStatus(302);
        }
    }


    /** GET /inventario/productos/{id} */
    public function detalle(Request $request, Response $response, array $args): Response
    {
        $productoId = (int) $args['id'];
        $producto = $this->productoModel->obtenerPorId($productoId);
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        
        if (!$producto) {
            return $response
                ->withHeader('Location', $basePath . '/inventario/productos?error=' . urlencode('Producto no encontrado'))
                ->withStatus(302);
        }
        
        // Obtener lotes del producto
        $db = Database::getInstance()->getConnection();
        $loteModel = new \App\Models\LoteModel($db);
        $lotes = $loteModel->obtenerLotesFEFO($productoId);
        
        // Renderizar vista de detalle (por ahora redirigir a editar)
        return $response
            ->withHeader('Location', $basePath . '/inventario/productos/' . $productoId . '/editar')
            ->withStatus(302);
    }

    /** GET /inventario/productos/{id}/editar */
    public function mostrarEditar(Request $request, Response $response, array $args): Response
    {
        $productoId = (int) $args['id'];
        $producto = $this->productoModel->obtenerPorId($productoId);
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        
        if (!$producto) {
            return $response
                ->withHeader('Location', $basePath . '/inventario/productos?error=' . urlencode('Producto no encontrado'))
                ->withStatus(302);
        }
        
        $db = Database::getInstance()->getConnection();
        
        // Obtener categorías y proveedores para el formulario
        $categoriaModel = new \App\Models\CategoriaModel($db);
        $proveedorModel = new \App\Models\ProveedorModel($db);
        $categorias = $categoriaModel->listar();
        $proveedores = $proveedorModel->listar();
        
        // Renderizar vista
        ob_start();
        include __DIR__ . '/../../views/inventario/producto_form.php';
        $contenido = ob_get_clean();
        
        $response->getBody()->write($contenido);
        return $response;
    }

    /** POST /inventario/productos/{id}/editar */
    public function actualizar(Request $request, Response $response, array $args): Response
    {
        $productoId = (int) $args['id'];
        $data = $request->getParsedBody();
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        
        // Validar que INVIMA esté presente (RN-01)
        if (empty($data['codigo_invima'])) {
            return $response
                ->withHeader('Location', $basePath . '/inventario/productos/' . $productoId . '/editar?error=' . urlencode('El código INVIMA es obligatorio'))
                ->withStatus(302);
        }
        
        try {
            $datos = [
                ':nombre'            => $data['nombre'],
                ':principio_activo'  => $data['principio_activo'] ?? null,
                ':concentracion'     => $data['concentracion'] ?? null,
                ':forma_farmaceutica'=> $data['forma_farmaceutica'] ?? null,
                ':codigo_invima'     => $data['codigo_invima'],
                ':categoria_id'      => $data['categoria_id'] ?? null,
                ':proveedor_id'      => $data['proveedor_id'] ?? null,
                ':control_especial'  => isset($data['control_especial']) ? 1 : 0,
                ':precio_compra'     => $data['precio_compra'] ?? 0,
                ':precio_venta'      => $data['precio_venta'] ?? 0,
                ':stock_minimo'      => $data['stock_minimo'] ?? 10
            ];
            
            $this->productoModel->actualizar($productoId, $datos);
            
            return $response
                ->withHeader('Location', $basePath . '/inventario/productos?success=' . urlencode('Producto actualizado exitosamente'))
                ->withStatus(302);
        } catch (\Exception $e) {
            return $response
                ->withHeader('Location', $basePath . '/inventario/productos/' . $productoId . '/editar?error=' . urlencode($e->getMessage()))
                ->withStatus(302);
        }
    }

    /**
     * GET /api/productos/buscar?q=termino
     * Endpoint JSON para búsqueda AJAX desde formularios (lote_form, POS, etc.).
     */
    public function buscarJson(Request $request, Response $response): Response
    {
        $params  = $request->getQueryParams();
        $termino = trim($params['q'] ?? '');

        if (strlen($termino) < 2) {
            $response->getBody()->write(json_encode([]));
            return $response->withHeader('Content-Type', 'application/json');
        }

        // Búsqueda por nombre, principio activo o código INVIMA
        $db   = Database::getInstance()->getConnection();
        $like = '%' . $termino . '%';
        $sql  = "SELECT p.producto_id, p.nombre, p.codigo_invima,
                        p.control_especial, p.stock_minimo,
                        pr.nombre AS proveedor_nombre,
                        COALESCE(SUM(l.cantidad_actual), 0) AS stock_actual
                 FROM productos p
                 LEFT JOIN proveedores pr ON p.proveedor_id = pr.proveedor_id
                 LEFT JOIN lotes l ON p.producto_id = l.producto_id AND l.activo = 1
                 WHERE p.activo = 1
                   AND (p.nombre LIKE :t1 OR p.principio_activo LIKE :t2 OR p.codigo_invima LIKE :t3)
                 GROUP BY p.producto_id
                 ORDER BY p.nombre ASC
                 LIMIT 15";
        $stmt = $db->prepare($sql);
        $stmt->execute([':t1' => $like, ':t2' => $like, ':t3' => $like]);
        $productos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($productos));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
