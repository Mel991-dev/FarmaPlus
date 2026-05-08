<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Models\DevolucionModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * DevolucionController — Gestión de devoluciones por el gerente.
 *
 * Rutas:
 *   GET  /gerente/devoluciones              → listar()
 *   GET  /gerente/devoluciones/crear        → crear()
 *   POST /gerente/devoluciones/crear        → guardar()
 *   GET  /gerente/devoluciones/{id}         → detalle()
 *   POST /gerente/devoluciones/{id}/aprobar → aprobar()
 *   POST /gerente/devoluciones/{id}/rechazar→ rechazar()
 */
class DevolucionController
{
    private DevolucionModel $model;

    public function __construct()
    {
        $db          = Database::getInstance()->getConnection();
        $this->model = new DevolucionModel($db);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /gerente/devoluciones
    // ─────────────────────────────────────────────────────────────────────────
    public function listar(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $estado = $params['estado'] ?? 'pendiente';      // Tab activo
        $allowedEstados = ['pendiente', 'aprobada', 'rechazada', ''];
        if (!in_array($estado, $allowedEstados, true)) {
            $estado = 'pendiente';
        }

        $devoluciones = $this->model->listar($estado);
        $contadores   = $this->model->contarPorEstado();
        $estadoActual = $estado;

        $titulo = 'Devoluciones — FarmaPlus';
        ob_start();
        require __DIR__ . '/../../views/gerente/devoluciones/index.php';
        $contenido = ob_get_clean();

        $layout = (($_SESSION['rol'] ?? '') === 'gerente')
            ? __DIR__ . '/../../views/layouts/base_gerente.php'
            : __DIR__ . '/../../views/layouts/base.php';

        ob_start();
        require $layout;
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /gerente/devoluciones/crear
    // ─────────────────────────────────────────────────────────────────────────
    public function crear(Request $request, Response $response): Response
    {
        $params   = $request->getQueryParams();
        $tipoOrigen = $params['tipo'] ?? 'pedido';

        $pedidos  = $this->model->pedidosEntregados();
        $ventas   = $this->model->ventasRecientes();

        // Si ya se seleccionó un origen, cargar sus productos
        $productos = [];
        if (!empty($params['pedido_id'])) {
            $productos = $this->model->productosDePedido((int)$params['pedido_id']);
        } elseif (!empty($params['venta_id'])) {
            $productos = $this->model->productosDeVenta((int)$params['venta_id']);
        }

        $error  = $_SESSION['dev_error'] ?? null;
        unset($_SESSION['dev_error']);

        $titulo = 'Nueva Devolución — FarmaPlus';
        ob_start();
        require __DIR__ . '/../../views/gerente/devoluciones/crear.php';
        $contenido = ob_get_clean();

        $layout = (($_SESSION['rol'] ?? '') === 'gerente')
            ? __DIR__ . '/../../views/layouts/base_gerente.php'
            : __DIR__ . '/../../views/layouts/base.php';

        ob_start();
        require $layout;
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /gerente/devoluciones/crear
    // ─────────────────────────────────────────────────────────────────────────
    public function guardar(Request $request, Response $response): Response
    {
        $data    = (array)$request->getParsedBody();
        $base    = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');

        $tipoOrigen = $data['tipo_origen'] ?? 'pedido';
        $pedidoId   = !empty($data['pedido_id']) ? (int)$data['pedido_id'] : null;
        $ventaId    = !empty($data['venta_id'])  ? (int)$data['venta_id']  : null;
        $productoId = (int)($data['producto_id'] ?? 0);
        $cantidad   = (int)($data['cantidad']    ?? 1);
        $motivo     = $data['motivo']             ?? 'otro';
        $observacion = trim($data['observacion'] ?? '');

        // Validación mínima
        if ($productoId <= 0 || $cantidad <= 0) {
            $_SESSION['dev_error'] = 'Debes seleccionar un producto y una cantidad válida.';
            return $response->withHeader('Location', $base . '/gerente/devoluciones/crear')->withStatus(302);
        }

        $this->model->crear([
            ':tipo_origen'  => $tipoOrigen,
            ':pedido_id'    => $pedidoId,
            ':venta_id'     => $ventaId,
            ':producto_id'  => $productoId,
            ':cantidad'     => $cantidad,
            ':motivo'       => $motivo,
            ':observacion'  => $observacion ?: null,
        ]);

        return $response->withHeader('Location', $base . '/gerente/devoluciones?success=Solicitud+registrada')->withStatus(302);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // GET /gerente/devoluciones/{id}
    // ─────────────────────────────────────────────────────────────────────────
    public function detalle(Request $request, Response $response, array $args): Response
    {
        $id          = (int)$args['id'];
        $devolucion  = $this->model->obtenerPorId($id);
        $base        = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');

        if (!$devolucion) {
            return $response->withHeader('Location', $base . '/gerente/devoluciones')->withStatus(302);
        }

        $success = $_SESSION['dev_success'] ?? null;
        $error   = $_SESSION['dev_error']   ?? null;
        unset($_SESSION['dev_success'], $_SESSION['dev_error']);

        $titulo = 'Detalle Devolución #' . $id . ' — FarmaPlus';
        ob_start();
        require __DIR__ . '/../../views/gerente/devoluciones/detalle.php';
        $contenido = ob_get_clean();

        $layout = (($_SESSION['rol'] ?? '') === 'gerente')
            ? __DIR__ . '/../../views/layouts/base_gerente.php'
            : __DIR__ . '/../../views/layouts/base.php';

        ob_start();
        require $layout;
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /gerente/devoluciones/{id}/aprobar
    // ─────────────────────────────────────────────────────────────────────────
    public function aprobar(Request $request, Response $response, array $args): Response
    {
        $id       = (int)$args['id'];
        $base     = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $gerenteId = (int)($_SESSION['usuario_id'] ?? 0);

        try {
            $ok = $this->model->aprobar($id, $gerenteId);
            if ($ok) {
                $_SESSION['dev_success'] = 'Devolución aprobada. El inventario ha sido actualizado.';
            } else {
                $_SESSION['dev_error'] = 'No se pudo aprobar. La devolución ya fue procesada o no existe.';
            }
        } catch (\Throwable $e) {
            $_SESSION['dev_error'] = 'Error al aprobar la devolución: ' . $e->getMessage();
        }

        return $response->withHeader('Location', $base . '/gerente/devoluciones/' . $id)->withStatus(302);
    }

    // ─────────────────────────────────────────────────────────────────────────
    // POST /gerente/devoluciones/{id}/rechazar
    // ─────────────────────────────────────────────────────────────────────────
    public function rechazar(Request $request, Response $response, array $args): Response
    {
        $id        = (int)$args['id'];
        $base      = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $gerenteId = (int)($_SESSION['usuario_id'] ?? 0);
        $data      = (array)$request->getParsedBody();
        $razon     = trim($data['razon_rechazo'] ?? '');

        if (empty($razon)) {
            $_SESSION['dev_error'] = 'Debes documentar la razón del rechazo.';
            return $response->withHeader('Location', $base . '/gerente/devoluciones/' . $id)->withStatus(302);
        }

        $ok = $this->model->rechazar($id, $gerenteId, $razon);
        if ($ok) {
            $_SESSION['dev_success'] = 'Devolución rechazada y documentada correctamente.';
        } else {
            $_SESSION['dev_error'] = 'No se pudo rechazar. Verifique que la devolución esté pendiente.';
        }

        return $response->withHeader('Location', $base . '/gerente/devoluciones/' . $id)->withStatus(302);
    }
}
