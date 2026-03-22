<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Models\LoteModel;
use App\Models\ProveedorModel;
use App\Models\AlertaModel;
use App\Models\AjusteStockModel;
use App\Models\ProductoModel;
use App\Services\AlertaService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * InventarioController — Lotes FEFO, alertas y proveedores.
 *
 * Módulo 5: Control de Lotes FEFO (RF-4.2, RF-4.4, RF-4.5)
 * Módulo 6: Alertas de Inventario (RN-04)
 * Módulo 4 parte: Proveedores (RF-4.6)
 */
class InventarioController
{
    private LoteModel $loteModel;
    private ProveedorModel $proveedorModel;
    private AlertaModel $alertaModel;
    private AlertaService $alertaService;
    private AjusteStockModel $ajusteModel;
    private ProductoModel $productoModel;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $this->loteModel      = new LoteModel($db);
        $this->proveedorModel = new ProveedorModel($db);
        $this->alertaModel    = new AlertaModel($db);
        $this->alertaService  = new AlertaService($db);
        $this->ajusteModel    = new AjusteStockModel($db);
        $this->productoModel  = new ProductoModel($db);
    }

    // =========================================================
    // LOTES
    // =========================================================

    /** GET /inventario/lotes */
    public function listarLotes(Request $request, Response $response): Response
    {
        $basePath  = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $params    = $request->getQueryParams();
        $productoId = isset($params['producto_id']) ? (int)$params['producto_id'] : null;

        $lotes = $this->loteModel->listarConProducto();

        // Filtrar por producto si viene en la query
        if ($productoId) {
            $lotes = array_filter($lotes, fn($l) => (int)$l['producto_id'] === $productoId);
            $lotes = array_values($lotes);
        }

        $iniciales = strtoupper(substr($_SESSION['nombres'] ?? 'U', 0, 1) . substr($_SESSION['apellidos'] ?? 'S', 0, 1));
        $nombre    = htmlspecialchars($_SESSION['nombres'] ?? '');
        $rol       = htmlspecialchars($_SESSION['rol'] ?? '');

        ob_start();
        include __DIR__ . '/../../views/inventario/lotes.php';
        $contenido = ob_get_clean();

        $response->getBody()->write($contenido);
        return $response;
    }

    /** GET /inventario/lotes/registrar */
    public function mostrarRegistroLote(Request $request, Response $response): Response
    {
        $proveedores = $this->proveedorModel->listar();
        $basePath    = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');

        ob_start();
        include __DIR__ . '/../../views/inventario/lote_form.php';
        $contenido = ob_get_clean();

        $response->getBody()->write($contenido);
        return $response;
    }

    /** POST /inventario/lotes/registrar */
    public function registrarLote(Request $request, Response $response): Response
    {
        $data     = $request->getParsedBody();
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');

        // 1. Validar fecha_vencimiento no pasada (HU-AUX-02)
        // Comparamos como string YYYY-MM-DD para evitar problemas de timezone
        $fechaVencimiento = $data['fecha_vencimiento'] ?? '';
        if (empty($fechaVencimiento) || $fechaVencimiento <= date('Y-m-d')) {
            return $response
                ->withHeader('Location', $basePath . '/inventario/lotes/registrar?error=' . urlencode('La fecha de vencimiento debe ser posterior a hoy'))
                ->withStatus(302);
        }

        // 2. Validar campos obligatorios
        if (empty($data['producto_id']) || empty($data['numero_lote']) || empty($data['cantidad_inicial'])) {
            return $response
                ->withHeader('Location', $basePath . '/inventario/lotes/registrar?error=' . urlencode('Faltan campos obligatorios'))
                ->withStatus(302);
        }

        try {
            $db = Database::getInstance()->getConnection();
            if (!$db->inTransaction()) {
                $db->beginTransaction();
            }

            // 3. Insertar en lotes
            $loteId = $this->loteModel->registrar([
                ':producto_id'      => (int)$data['producto_id'],
                ':proveedor_id'     => !empty($data['proveedor_id']) ? (int)$data['proveedor_id'] : null,
                ':numero_lote'      => trim($data['numero_lote']),
                ':cantidad_inicial' => (int)$data['cantidad_inicial'],
                ':fecha_vencimiento'=> $fechaVencimiento,
                ':registrado_por'   => $_SESSION['usuario_id'] ?? 1,
            ]);

            // 4. Registrar en ajustes_stock (trazabilidad — no crítico)
            // Si la tabla aún no existe en la BD, ignorar el error sin cancelar todo
            try {
                $this->ajusteModel->registrar(
                    productoId:  (int)$data['producto_id'],
                    loteId:      (int)$loteId,
                    usuarioId:   (int)($_SESSION['usuario_id'] ?? 1),
                    tipo:        'entrada',
                    cantidad:    (int)$data['cantidad_inicial'],
                    observacion: 'Entrada de lote n\u00ba ' . trim($data['numero_lote'])
                );
            } catch (\Throwable) {
                // Tabla ajustes_stock puede no existir en BD antigua — continuar
            }

            // 5. Verificar alertas de stock y vencimiento
            $this->alertaService->verificarProducto((int)$data['producto_id']);

            $db->commit();

            return $response
                ->withHeader('Location', $basePath . '/inventario/lotes?success=' . urlencode('Lote registrado exitosamente'))
                ->withStatus(302);

        } catch (\Exception $e) {
            try {
                if (isset($db) && $db->inTransaction()) {
                    $db->rollBack();
                }
            } catch (\Throwable) {}
            return $response
                ->withHeader('Location', $basePath . '/inventario/lotes/registrar?error=' . urlencode($e->getMessage()))
                ->withStatus(302);
        }
    }

    // =========================================================
    // ALERTAS
    // =========================================================

    /** GET /inventario/alertas */
    public function alertas(Request $request, Response $response): Response
    {
        // ── Disparar evaluación automática de todos los productos ─────────────────
        // Esto garantiza que las alertas estén al día cada vez que se abre la vista,
        // sin necesidad de un cron job. Se ignoran errores para no romper la carga.
        $this->alertaService->verificarTodos();

        $alertas            = $this->alertaModel->listarActivas();

        $totalAlertas       = count($alertas);
        $alertasStock       = array_values(array_filter($alertas, fn($a) => $a['tipo'] === 'stock_minimo'));
        $alertasVencimiento = array_values(array_filter($alertas, fn($a) => $a['tipo'] === 'vencimiento'));

        $basePath  = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $iniciales = strtoupper(substr($_SESSION['nombres'] ?? 'U', 0, 1) . substr($_SESSION['apellidos'] ?? 'S', 0, 1));
        $nombre    = htmlspecialchars($_SESSION['nombres'] ?? '');
        $rol       = htmlspecialchars($_SESSION['rol'] ?? '');

        ob_start();
        include __DIR__ . '/../../views/inventario/alertas.php';
        $contenido = ob_get_clean();

        $response->getBody()->write($contenido);
        return $response;
    }

    /** POST /inventario/alertas/{id}/resolver */
    public function resolverAlerta(Request $request, Response $response, array $args): Response
    {
        $alertaId = (int) $args['id'];
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');

        try {
            $this->alertaModel->resolver($alertaId);
            return $response
                ->withHeader('Location', $basePath . '/inventario/alertas?success=' . urlencode('Alerta marcada como resuelta'))
                ->withStatus(302);
        } catch (\Exception $e) {
            return $response
                ->withHeader('Location', $basePath . '/inventario/alertas?error=' . urlencode($e->getMessage()))
                ->withStatus(302);
        }
    }

    // =========================================================
    // PROVEEDORES
    // =========================================================

    /** GET /inventario/proveedores */
    public function listarProveedores(Request $request, Response $response): Response
    {
        $proveedores = $this->proveedorModel->listar();
        $basePath    = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $iniciales   = strtoupper(substr($_SESSION['nombres'] ?? 'U', 0, 1) . substr($_SESSION['apellidos'] ?? 'S', 0, 1));
        $nombre      = htmlspecialchars($_SESSION['nombres'] ?? '');
        $rol         = htmlspecialchars($_SESSION['rol'] ?? '');

        ob_start();
        include __DIR__ . '/../../views/inventario/proveedores.php';
        $contenido = ob_get_clean();

        $response->getBody()->write($contenido);
        return $response;
    }

    /** GET /inventario/proveedores/crear */
    public function mostrarCrearProveedor(Request $request, Response $response): Response
    {
        $basePath  = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $proveedor = null; // sin datos (modo crear)

        ob_start();
        include __DIR__ . '/../../views/inventario/proveedor_form.php';
        $contenido = ob_get_clean();

        $response->getBody()->write($contenido);
        return $response;
    }

    /** POST /inventario/proveedores/crear */
    public function crearProveedor(Request $request, Response $response): Response
    {
        $data     = $request->getParsedBody();
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');

        // Validar NIT único (HU-AUX-03)
        if (empty($data['nit'])) {
            return $response
                ->withHeader('Location', $basePath . '/inventario/proveedores/crear?error=' . urlencode('El NIT es obligatorio'))
                ->withStatus(302);
        }

        try {
            $this->proveedorModel->crear([
                ':nit'         => trim($data['nit']),
                ':nombre'      => trim($data['nombre']),
                ':pais_origen' => $data['pais_origen'] ?? 'Colombia',
                ':telefono'    => $data['telefono'] ?? '',
                ':correo'      => $data['correo'] ?? '',
                ':sitio_web'   => $data['sitio_web'] ?? '',
            ]);
            return $response
                ->withHeader('Location', $basePath . '/inventario/proveedores?success=' . urlencode('Proveedor creado exitosamente'))
                ->withStatus(302);
        } catch (\Exception $e) {
            $msg = str_contains($e->getMessage(), '1062') 
                ? 'Ya existe un proveedor con ese NIT'
                : $e->getMessage();
            return $response
                ->withHeader('Location', $basePath . '/inventario/proveedores/crear?error=' . urlencode($msg))
                ->withStatus(302);
        }
    }

    /** GET /inventario/proveedores/{id}/editar */
    public function mostrarEditarProveedor(Request $request, Response $response, array $args): Response
    {
        $id        = (int) $args['id'];
        $basePath  = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $proveedor = $this->proveedorModel->obtenerPorId($id);

        if (!$proveedor) {
            return $response
                ->withHeader('Location', $basePath . '/inventario/proveedores?error=' . urlencode('Proveedor no encontrado'))
                ->withStatus(302);
        }

        ob_start();
        include __DIR__ . '/../../views/inventario/proveedor_form.php';
        $contenido = ob_get_clean();

        $response->getBody()->write($contenido);
        return $response;
    }

    /** POST /inventario/proveedores/{id}/editar */
    public function actualizarProveedor(Request $request, Response $response, array $args): Response
    {
        $id       = (int) $args['id'];
        $data     = $request->getParsedBody();
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');

        if (empty($data['nit'])) {
            return $response
                ->withHeader('Location', $basePath . '/inventario/proveedores/' . $id . '/editar?error=' . urlencode('El NIT es obligatorio'))
                ->withStatus(302);
        }

        try {
            $this->proveedorModel->actualizar($id, [
                ':nit'      => trim($data['nit']),
                ':nombre'   => trim($data['nombre']),
                ':telefono' => $data['telefono'] ?? '',
                ':correo'   => $data['correo'] ?? '',
            ]);
            return $response
                ->withHeader('Location', $basePath . '/inventario/proveedores?success=' . urlencode('Proveedor actualizado'))
                ->withStatus(302);
        } catch (\Exception $e) {
            return $response
                ->withHeader('Location', $basePath . '/inventario/proveedores/' . $id . '/editar?error=' . urlencode($e->getMessage()))
                ->withStatus(302);
        }
    }
}
