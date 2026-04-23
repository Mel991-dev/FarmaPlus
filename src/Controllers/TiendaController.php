<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Models\PedidoModel;
use App\Models\ProductoModel;
use App\Models\DetallePedidoModel;
use App\Models\ClienteModel;
use App\Services\MercadoPagoService;
use App\Services\DomicilioService;
use App\Services\EmailService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * TiendaController — Catálogo, carrito y checkout del e-commerce.
 * Módulo 8: E-commerce (RF-6.1 a RF-6.6)
 */
class TiendaController
{
    private ProductoModel     $productoModel;
    private PedidoModel       $pedidoModel;
    private DetallePedidoModel $detallePedidoModel;
    private ClienteModel      $clienteModel;
    private MercadoPagoService $mpService;
    private DomicilioService  $domicilioService;
    private EmailService      $emailService;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $this->productoModel      = new ProductoModel($db);
        $this->pedidoModel        = new PedidoModel($db);
        $this->detallePedidoModel = new DetallePedidoModel($db);
        $this->clienteModel       = new ClienteModel($db);
        $this->mpService          = new MercadoPagoService($db);
        $this->domicilioService   = new DomicilioService($db);
        $this->emailService       = new EmailService();
    }

    /**
     * GET /tienda — Catálogo público de productos sin control especial.
     * RF-6.1, HU-CLI-03
     */
    public function catalogo(Request $request, Response $response): Response
    {
        $params     = $request->getQueryParams();
        $categoriaId = isset($params['categoria']) ? (int)$params['categoria'] : null;
        $busqueda   = $params['q'] ?? '';

        // Productos disponibles: activos, sin control especial, con stock > 0
        $productos  = $this->productoModel->listarParaTienda($categoriaId, $busqueda);
        $categorias = $this->productoModel->listarCategorias();

        // Cantidad de items en sesión para el badge del carrito
        $carrito    = $_SESSION['carrito'] ?? [];
        $totalItems = array_sum(array_column($carrito, 'cantidad'));

        $titulo    = 'Tienda — FarmaPlus';
        ob_start();
        require __DIR__ . '/../../views/tienda/catalogo.php';
        $contenido = ob_get_clean();

        $response->getBody()->write($contenido);
        return $response;
    }

    /**
     * GET /tienda/producto/{id} — Ficha individual de un producto.
     */
    public function fichaProducto(Request $request, Response $response, array $args): Response
    {
        $id = (int)$args['id'];
        $producto = $this->productoModel->obtenerPorId($id);

        if (!$producto || $producto['control_especial'] || !$producto['activo']) {
            return $response->withHeader('Location', ($_ENV['APP_BASEPATH'] ?? '') . '/tienda')
                            ->withStatus(302);
        }

        $db       = Database::getInstance()->getConnection();
        $imgModel = new \App\Models\ImagenProductoModel($db);
        $imagenes = $imgModel->obtenerPorProducto($id);

        $titulo     = htmlspecialchars($producto['nombre']) . ' — FarmaPlus Tienda';
        $carrito    = $_SESSION['carrito'] ?? [];
        $totalItems = array_sum(array_column($carrito, 'cantidad'));
        $enCarrito  = isset($carrito[$id]) ? (int)$carrito[$id]['cantidad'] : 0;

        ob_start();
        require __DIR__ . '/../../views/tienda/ficha_producto.php';
        $contenido = ob_get_clean();

        $response->getBody()->write($contenido);
        return $response;
    }

    /**
     * GET /tienda/carrito — Mostrar carrito de compras.
     * HU-CLI-04
     */
    public function carrito(Request $request, Response $response): Response
    {
        $carrito    = $_SESSION['carrito'] ?? [];
        $totalItems = array_sum(array_column($carrito, 'cantidad'));
        $subtotal   = 0;

        if (!empty($carrito)) {
            // Enriquecer ítems con imagen_principal y es_medicamento desde la BD
            $db   = Database::getInstance()->getConnection();
            $ids  = implode(',', array_map('intval', array_keys($carrito)));
            $stmt = $db->query(
                "SELECT p.producto_id, p.es_medicamento,
                        pi.nombre_archivo AS imagen_principal
                 FROM productos p
                 LEFT JOIN producto_imagenes pi
                   ON pi.producto_id = p.producto_id AND pi.orden = 1
                 WHERE p.producto_id IN ({$ids})"
            );
            $datos = $stmt->fetchAll(\PDO::FETCH_ASSOC);
            $datosMap = array_column($datos, null, 'producto_id');

            foreach ($carrito as $pid => &$item) {
                $item['subtotal']       = $item['precio_unitario'] * $item['cantidad'];
                $subtotal              += $item['subtotal'];
                $item['imagen']         = $datosMap[$pid]['imagen_principal'] ?? null;
                $item['es_medicamento'] = (int)($datosMap[$pid]['es_medicamento'] ?? 1);
            }
            unset($item);
        }

        $titulo   = 'Mi Carrito — FarmaPlus';
        ob_start();
        require __DIR__ . '/../../views/tienda/carrito.php';
        $contenido = ob_get_clean();

        $response->getBody()->write($contenido);
        return $response;
    }

    /**
     * POST /tienda/carrito/agregar — Añadir o incrementar producto en el carrito.
     * Devuelve JSON para AJAX.
     */
    public function agregarAlCarrito(Request $request, Response $response): Response
    {
        $data       = (array)$request->getParsedBody();
        $productoId = (int)($data['producto_id'] ?? 0);
        $cantidad   = (int)($data['cantidad']   ?? 1);

        if ($productoId <= 0 || $cantidad <= 0) {
            return $this->json($response, ['success' => false, 'error' => 'Datos inválidos.'], 400);
        }

        $producto = $this->productoModel->obtenerPorId($productoId);
        if (!$producto || $producto['control_especial'] || !$producto['activo']) {
            return $this->json($response, ['success' => false, 'error' => 'Producto no disponible.'], 404);
        }

        // Verificar stock disponible
        $stockActual = (int)($producto['stock_actual'] ?? 0);
        if (!isset($_SESSION['carrito'])) {
            $_SESSION['carrito'] = [];
        }

        $enCarrito = (int)($_SESSION['carrito'][$productoId]['cantidad'] ?? 0);
        if (($enCarrito + $cantidad) > $stockActual) {
            return $this->json($response, [
                'success' => false,
                'error'   => "Solo hay {$stockActual} unidades disponibles."
            ], 400);
        }

        if (isset($_SESSION['carrito'][$productoId])) {
            $_SESSION['carrito'][$productoId]['cantidad'] += $cantidad;
        } else {
            $_SESSION['carrito'][$productoId] = [
                'producto_id'     => $productoId,
                'nombre'          => $producto['nombre'],
                'precio_unitario' => (float)$producto['precio_venta'],
                'cantidad'        => $cantidad,
                'imagen'          => $producto['imagen_principal'] ?? null,
                'es_medicamento'  => (int)($producto['es_medicamento'] ?? 1),
            ];
        }

        $totalItems = array_sum(array_column($_SESSION['carrito'], 'cantidad'));

        return $this->json($response, [
            'success'   => true,
            'mensaje'   => "«{$producto['nombre']}» añadido al carrito.",
            'totalItems' => $totalItems,
        ]);
    }

    /**
     * POST /tienda/carrito/actualizar — Cambiar cantidad o eliminar ítem.
     * Devuelve JSON para AJAX.
     */
    public function actualizarCarrito(Request $request, Response $response): Response
    {
        $data       = (array)$request->getParsedBody();
        $productoId = (int)($data['producto_id'] ?? 0);
        $cantidad   = (int)($data['cantidad']   ?? 0);

        if (!isset($_SESSION['carrito'])) {
            return $this->json($response, ['success' => false, 'error' => 'Carrito vacío.'], 400);
        }

        if ($cantidad <= 0) {
            unset($_SESSION['carrito'][$productoId]);
        } else {
            if (isset($_SESSION['carrito'][$productoId])) {
                $_SESSION['carrito'][$productoId]['cantidad'] = $cantidad;
            }
        }

        $subtotal = 0;
        foreach ($_SESSION['carrito'] as $item) {
            $subtotal += $item['precio_unitario'] * $item['cantidad'];
        }

        return $this->json($response, [
            'success'   => true,
            'totalItems' => array_sum(array_column($_SESSION['carrito'], 'cantidad')),
            'subtotal'  => $subtotal,
        ]);
    }

    /**
     * POST /tienda/carrito/vaciar — Vaciar carrito.
     */
    public function vaciarCarrito(Request $request, Response $response): Response
    {
        $_SESSION['carrito'] = [];
        return $this->json($response, ['success' => true]);
    }

    /**
     * GET /tienda/checkout — Checkout con selección de dirección y cálculo de domicilio.
     * RF-6.3, HU-CLI-05
     */
    public function checkout(Request $request, Response $response): Response
    {
        $carrito = $_SESSION['carrito'] ?? [];

        if (empty($carrito)) {
            return $response->withHeader('Location', ($_ENV['APP_BASEPATH'] ?? '') . '/tienda/carrito')
                            ->withStatus(302);
        }

        // Obtener cliente
        $usuarioId = $_SESSION['usuario_id'] ?? 0;
        $cliente   = $this->clienteModel->obtenerPorUsuarioId($usuarioId);
        $direcciones = $cliente ? $this->clienteModel->obtenerDirecciones((int)$cliente['cliente_id']) : [];

        // Calcular subtotal
        $subtotal = 0;
        foreach ($carrito as $item) {
            $subtotal += $item['precio_unitario'] * $item['cantidad'];
        }

        // Tarifa de envío estimada (sin distancia exacta = tarifa base + volumen)
        $totalItems    = array_sum(array_column($carrito, 'cantidad'));
        $costoEnvio    = $this->domicilioService->calcular(1.0, (int)$totalItems); // ~1km por defecto
        $total         = $subtotal + $costoEnvio;
        $totalItems    = array_sum(array_column($carrito, 'cantidad'));

        $titulo = 'Checkout — FarmaPlus';
        ob_start();
        require __DIR__ . '/../../views/tienda/checkout.php';
        $contenido = ob_get_clean();

        $response->getBody()->write($contenido);
        return $response;
    }

    /**
     * POST /tienda/checkout/procesar — Crear pedido en BD y redirigir a MercadoPago.
     * RN-05: El pedido se crea ANTES del pago. Estado inicial: 'pendiente'.
     */
    public function procesarCheckout(Request $request, Response $response): Response
    {
        $carrito = $_SESSION['carrito'] ?? [];

        if (empty($carrito)) {
            return $response->withHeader('Location', ($_ENV['APP_BASEPATH'] ?? '') . '/tienda')
                            ->withStatus(302);
        }

        $data        = (array)$request->getParsedBody();
        $direccionId = (int)($data['direccion_entrega_id'] ?? 0);

        if ($direccionId <= 0) {
            return $response->withHeader('Location', ($_ENV['APP_BASEPATH'] ?? '') . '/tienda/checkout?error=Selecciona+una+dirección')
                            ->withStatus(302);
        }

        // Obtener cliente
        $usuarioId = $_SESSION['usuario_id'] ?? 0;
        $cliente   = $this->clienteModel->obtenerPorUsuarioId($usuarioId);

        if (!$cliente) {
            return $response->withHeader('Location', ($_ENV['APP_BASEPATH'] ?? '') . '/login')
                            ->withStatus(302);
        }

        // Calcular totales
        $subtotal   = 0;
        $totalItems = 0;
        $mpItems    = [];

        foreach ($carrito as $item) {
            $subtotal   += $item['precio_unitario'] * $item['cantidad'];
            $totalItems += $item['cantidad'];
            $mpItems[]   = [
                'id'            => (string)$item['producto_id'],
                'nombre'        => $item['nombre'],
                'titulo'        => $item['nombre'],
                'cantidad'      => $item['cantidad'],
                'unit_price'    => (float)$item['precio_unitario'],
                'precio_unitario' => (float)$item['precio_unitario'],
            ];
        }

        $costoEnvio = $this->domicilioService->calcular(1.0, $totalItems);
        $total      = $subtotal + $costoEnvio;

        // Crear el pedido en la BD (estado: pendiente)
        $pedidoId = (int)$this->pedidoModel->crear([
            ':cliente_id'          => (int)$cliente['cliente_id'],
            ':direccion_entrega_id' => $direccionId,
            ':subtotal'            => $subtotal,
            ':costo_envio'         => $costoEnvio,
            ':total'               => $total,
            ':mp_referencia'       => (string)time(), // referencia temporal
        ]);

        // Actualizar la mp_referencia con el pedido_id real
        $this->pedidoModel->actualizarEstado($pedidoId, 'pendiente'); // sin cambio de estado, solo para trigger

        // Guardar detalle del pedido
        foreach ($carrito as $item) {
            $this->detallePedidoModel->crear([
                'pedido_id'       => $pedidoId,
                'producto_id'     => (int)$item['producto_id'],
                'cantidad'        => (int)$item['cantidad'],
                'precio_unitario' => (float)$item['precio_unitario'],
                'subtotal'        => (float)($item['precio_unitario'] * $item['cantidad']),
            ]);
        }

        // Actualizar mp_referencia con el pedido_id definitivo
        $db = Database::getInstance()->getConnection();
        $db->prepare("UPDATE pedidos SET mp_referencia = :ref WHERE pedido_id = :id")
           ->execute([':ref' => (string)$pedidoId, ':id' => $pedidoId]);

        // Crear preferencia de pago en MercadoPago
        try {
            $checkoutUrl = $this->mpService->crearPreferencia($mpItems, $pedidoId, $costoEnvio);

            // Limpiar carrito ANTES de redirigir a MP
            $_SESSION['carrito'] = [];

            return $response->withHeader('Location', $checkoutUrl)->withStatus(302);

        } catch (\RuntimeException $e) {
            // Modo sin credenciales: redirigir a confirmación simulada para poder probar
            $_SESSION['carrito'] = [];
            $_SESSION['mp_sandbox_skip'] = true;

            return $response->withHeader(
                'Location',
                ($_ENV['APP_BASEPATH'] ?? '') . "/tienda/confirmacion/{$pedidoId}?status=sandbox_skip"
            )->withStatus(302);
        }
    }

    /**
     * GET /tienda/confirmacion/{id} — Pantalla post-pago.
     * Muestra el resumen del pedido según el status devuelto por MercadoPago.
     */
    public function confirmacion(Request $request, Response $response, array $args): Response
    {
        $pedidoId = (int)$args['id'];
        $params   = $request->getQueryParams();
        $status   = $params['status'] ?? 'pending';

        $pedido = $this->pedidoModel->obtenerPorId($pedidoId);
        $items  = $this->detallePedidoModel->obtenerPorPedido($pedidoId);

        if (!$pedido) {
            return $response->withHeader('Location', ($_ENV['APP_BASEPATH'] ?? '') . '/tienda')
                            ->withStatus(302);
        }

        // Si el status viene como sandbox_skip, actualizar a pagado automáticamente (modo dev)
        if ($status === 'sandbox_skip' || $status === 'approved') {
            if ($pedido['estado'] === 'pendiente') {
                $this->pedidoModel->actualizarMpPago($pedidoId, 'sandbox_' . $pedidoId, 'approved');

                // Enviar email de confirmación
                $usuarioId = $_SESSION['usuario_id'] ?? 0;
                $cliente   = $this->clienteModel->obtenerPorUsuarioId($usuarioId);
                if ($cliente) {
                    $this->emailService->enviarConfirmacionPedido(
                        $_SESSION['correo'] ?? '',
                        $_SESSION['nombres'] ?? 'Cliente',
                        $pedidoId,
                        array_map(fn($i) => [
                            'nombre'           => $i['producto_nombre'],
                            'cantidad'         => $i['cantidad'],
                            'precio_unitario'  => $i['precio_unitario'],
                        ], $items),
                        (float)$pedido['subtotal'],
                        (float)$pedido['costo_envio'],
                        (float)$pedido['total']
                    );
                }
            }
        }

        $carrito    = $_SESSION['carrito'] ?? [];
        $totalItems = array_sum(array_column($carrito, 'cantidad'));
        $titulo     = 'Pedido Confirmado — FarmaPlus';

        ob_start();
        require __DIR__ . '/../../views/tienda/pedido_confirmado.php';
        $contenido = ob_get_clean();

        $response->getBody()->write($contenido);
        return $response;
    }

    /** Helper: responder en JSON. */
    private function json(Response $response, array $data, int $status = 200): Response
    {
        $response->getBody()->write(json_encode($data));
        return $response
            ->withHeader('Content-Type', 'application/json')
            ->withStatus($status);
    }
}
