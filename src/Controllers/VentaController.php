<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Models\VentaModel;
use App\Models\ProductoModel;
use App\Services\FEFOService;
use App\Services\EmailService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use PDO;

/**
 * VentaController — Punto de venta presencial (POS).
 *
 * Módulo 7: Ventas Presenciales (RF-5.1 a RF-5.5)
 * - Control especial requiere fórmula médica (RF-5.2, RN-02)
 * - Descuento FEFO automático al confirmar venta (RF-4.4, RN-06)
 * - Comprobante con número único formato FP-{AÑO}-{SEQ} (RN-08)
 */
class VentaController
{
    private VentaModel $ventaModel;
    private ProductoModel $productoModel;
    private FEFOService $fefoService;
    private EmailService $emailService;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $this->ventaModel    = new VentaModel($db);
        $this->productoModel = new ProductoModel($db);
        $this->fefoService   = new FEFOService($db);
        $this->emailService  = new EmailService();
    }

    /** GET /ventas/pos — Pantalla POS */
    public function pos(Request $request, Response $response): Response
    {
        ob_start();
        include __DIR__ . '/../../views/ventas/pos.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    /** POST /ventas/pos/procesar — Confirmar venta */
    public function procesarVenta(Request $request, Response $response): Response
    {
        $body = (array) $request->getParsedBody();
        if (empty($body)) {
            // Si el body es un JSON puro, getParsedBody() puede fallar o estar vacío.
            $body = json_decode((string)$request->getBody(), true) ?? [];
        }

        $items = $body['items'] ?? [];
        $metodoPago = $body['metodo_pago'] ?? 'efectivo';
        $formula = trim($body['formula_medica'] ?? '');
        $vendedorId = (int) ($_SESSION['usuario_id'] ?? 1); // Fallback iterativo

        if (empty($items)) {
            $response->getBody()->write(json_encode(['success' => false, 'error' => 'El carrito está vacío']));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }

        try {
            $this->ventaModel->iniciarTransaccion();

            // 1. Validar control especial e inicializar totales
            $subtotalVenta = 0.0;
            foreach ($items as $item) {
                $pid = (int) $item['producto_id'];
                $cant = (int) $item['cantidad'];
                $producto = $this->productoModel->obtenerPorId($pid);
                
                if (!$producto) throw new \Exception("Producto NO encontrado (ID: $pid).");
                if ($producto['control_especial'] == 1 && empty($formula)) {
                    throw new \Exception("El producto {$producto['nombre']} exige registro de fórmula médica.");
                }

                $subtotalVenta += ($producto['precio_venta'] * $cant);
            }

            // 2. Crear cabecera de la venta (RN-08: FP-YYYY-SEQ)
            $nroComprobante = $this->ventaModel->generarNumeroComprobante();
            $datosVenta = [
                ':vendedor_id'        => $vendedorId,
                ':numero_comprobante' => $nroComprobante,
                ':subtotal'           => $subtotalVenta,
                ':total'              => $subtotalVenta,
                ':metodo_pago'        => $metodoPago,
                ':formula_medica'     => $formula
            ];
            $ventaId = (int) $this->ventaModel->crear($datosVenta);

            // 3. Descontar stock FEFO e insertar detalle (RF-4.4, RN-06)
            foreach ($items as $item) {
                $pid = (int) $item['producto_id'];
                $cant = (int) $item['cantidad'];
                $producto = $this->productoModel->obtenerPorId($pid);
                
                // FEFO: Descuenta stock y nos dice de qué lotes se descontó
                $movimientos = $this->fefoService->descontarStock($pid, $cant);
                
                foreach ($movimientos as $mov) {
                    $loteId = (int) $mov['lote_id'];
                    $qty = (int) $mov['cantidad_descontada'];
                    $precio = (float) $producto['precio_venta'];
                    
                    $this->ventaModel->insertarDetalle([
                        ':venta_id'        => $ventaId,
                        ':producto_id'     => $pid,
                        ':lote_id'         => $loteId,
                        ':cantidad'        => $qty,
                        ':precio_unitario' => $precio,
                        ':subtotal'        => $qty * $precio
                    ]);
                }
            }

            $this->ventaModel->confirmarTransaccion();

            $response->getBody()->write(json_encode([
                'success' => true,
                'venta_id' => $ventaId,
                'comprobante' => $nroComprobante,
                'redirect' => rtrim($_ENV['APP_BASEPATH'] ?? '', '/') . '/ventas/comprobante/' . $ventaId
            ]));
            return $response->withHeader('Content-Type', 'application/json');

        } catch (\Exception $e) {
            $this->ventaModel->revertirTransaccion();
            $response->getBody()->write(json_encode(['success' => false, 'error' => $e->getMessage()]));
            return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
        }
    }

    /** GET /ventas/comprobante/{id} */
    public function comprobante(Request $request, Response $response, array $args): Response
    {
        $ventaId = (int) $args['id'];
        $venta = $this->ventaModel->obtenerConDetalle($ventaId);
        if (!$venta) {
            $response->getBody()->write('Venta no encontrada');
            return $response->withStatus(404);
        }
        $detalle = $this->ventaModel->obtenerDetalle($ventaId);

        ob_start();
        include __DIR__ . '/../../views/ventas/comprobante.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    /** POST /ventas/comprobante/{id}/enviar-correo */
    public function enviarComprobante(Request $request, Response $response, array $args): Response
    {
        // TODO: Implementar en Semana 3
        // Enviar comprobante por correo vía PHPMailer (RF-5.5)
        return $response;
    }

    /** GET /ventas/mis-ventas */
    public function misVentas(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 3
        // Solo ventas del usuario autenticado (HU-VEND-04)
        return $response;
    }

    /** GET /ventas/buscar-producto — AJAX autocompletado POS */
    public function buscarProducto(Request $request, Response $response): Response
    {
        $params  = $request->getQueryParams();
        $termino = trim($params['q'] ?? '');

        if (strlen($termino) > 0 && strlen($termino) < 2) {
            $response->getBody()->write(json_encode([]));
            return $response->withHeader('Content-Type', 'application/json');
        }

        $db   = Database::getInstance()->getConnection();
        $like = '%' . $termino . '%';
        $sql  = "SELECT p.producto_id, p.nombre, p.codigo_invima,
                        p.control_especial, p.precio_venta,
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
        $productos = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $response->getBody()->write(json_encode($productos));
        return $response->withHeader('Content-Type', 'application/json');
    }
}
