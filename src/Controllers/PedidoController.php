<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Models\PedidoModel;
use App\Models\DetallePedidoModel;
use App\Services\EmailService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * PedidoController — Gestión de pedidos en línea desde el panel admin/gerente.
 * Módulo 9: Pedidos en Línea (RF-7.1)
 */
class PedidoController
{
    private PedidoModel        $pedidoModel;
    private DetallePedidoModel $detallePedidoModel;
    private EmailService       $emailService;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $this->pedidoModel        = new PedidoModel($db);
        $this->detallePedidoModel = new DetallePedidoModel($db);
        $this->emailService       = new EmailService();
    }

    /**
     * GET /pedidos — Lista de todos los pedidos con filtros y paginación.
     */
    public function listar(Request $request, Response $response): Response
    {
        $params  = $request->getQueryParams();
        $filtros = [];
        if (!empty($params['estado'])) {
            $filtros['estado'] = $params['estado'];
        }

        $pagina    = max(1, (int)($params['pagina'] ?? 1));
        $limit     = 20;
        $offset    = ($pagina - 1) * $limit;
        $pedidos   = $this->pedidoModel->listarConFiltros($filtros, $limit, $offset);
        $total     = $this->pedidoModel->contarPedidos($filtros);
        $paginas   = (int)ceil($total / $limit);
        $repartidores = $this->pedidoModel->obtenerRepartidoresDisponibles();

        $titulo = 'Pedidos en Línea';
        ob_start();
        require __DIR__ . '/../../views/pedidos/lista.php';
        $contenido = ob_get_clean();

        $response->getBody()->write($contenido);
        return $response;
    }

    /**
     * GET /pedidos/{id} — Detalle de un pedido específico.
     */
    public function detalle(Request $request, Response $response, array $args): Response
    {
        $id     = (int)$args['id'];
        $pedido = $this->pedidoModel->obtenerPorId($id);
        $items  = $this->detallePedidoModel->obtenerPorPedido($id);
        $repartidores = $this->pedidoModel->obtenerRepartidoresDisponibles();

        if (!$pedido) {
            return $response->withHeader('Location', ($_ENV['APP_BASEPATH'] ?? '') . '/pedidos')
                            ->withStatus(302);
        }

        $titulo = "Pedido #{$id}";
        ob_start();
        require __DIR__ . '/../../views/pedidos/detalle.php';
        $contenido = ob_get_clean();

        $response->getBody()->write($contenido);
        return $response;
    }

    /**
     * POST /pedidos/{id}/asignar-repartidor
     * Asigna un repartidor y cambia el estado a en_preparacion.
     * Notifica al repartidor por correo (RF-7.4).
     */
    public function asignarRepartidor(Request $request, Response $response, array $args): Response
    {
        $pedidoId   = (int)$args['id'];
        $data       = (array)$request->getParsedBody();
        $repartidorId = (int)($data['repartidor_id'] ?? 0);

        if ($repartidorId <= 0) {
            return $this->json($response, ['success' => false, 'error' => 'Repartidor inválido.'], 400);
        }

        $pedido = $this->pedidoModel->obtenerPorId($pedidoId);
        if (!$pedido) {
            return $this->json($response, ['success' => false, 'error' => 'Pedido no encontrado.'], 404);
        }

        // Solo se pueden asignar pedidos en estado 'pagado'
        if (!in_array($pedido['estado'], ['pagado', 'pendiente'])) {
            return $this->json($response, [
                'success' => false,
                'error'   => "No se puede asignar repartidor a un pedido en estado '{$pedido['estado']}'."
            ], 400);
        }

        $this->pedidoModel->asignarRepartidor($pedidoId, $repartidorId);

        // Obtener datos del repartidor para notificar
        $repartidores = $this->pedidoModel->obtenerRepartidoresDisponibles();
        $repartidor   = current(array_filter($repartidores, fn($r) => (int)$r['usuario_id'] === $repartidorId));

        if ($repartidor && !empty($repartidor['correo'])) {
            $direccion = ($pedido['direccion'] ?? 'No especificada') . ', ' . ($pedido['ciudad'] ?? '');
            $this->emailService->notificarRepartidorAsignado(
                $repartidor['correo'],
                $repartidor['nombre'],
                $pedidoId,
                $direccion,
                $pedido['cliente_nombre'] ?? 'Cliente'
            );
        }

        return $this->json($response, [
            'success' => true,
            'mensaje' => 'Repartidor asignado correctamente. El pedido está en preparación.',
        ]);
    }

    /**
     * POST /pedidos/{id}/cancelar
     * Cancela un pedido pendiente o pagado.
     */
    public function cancelar(Request $request, Response $response, array $args): Response
    {
        $pedidoId = (int)$args['id'];
        $pedido   = $this->pedidoModel->obtenerPorId($pedidoId);

        if (!$pedido) {
            return $response->withHeader('Location', ($_ENV['APP_BASEPATH'] ?? '') . '/pedidos?error=Pedido+no+encontrado')
                            ->withStatus(302);
        }

        $estadosPermitidos = ['pendiente', 'pagado'];
        if (!in_array($pedido['estado'], $estadosPermitidos)) {
            return $response->withHeader(
                'Location',
                ($_ENV['APP_BASEPATH'] ?? '') . "/pedidos/{$pedidoId}?error=No+se+puede+cancelar+en+estado+{$pedido['estado']}"
            )->withStatus(302);
        }

        $this->pedidoModel->actualizarEstado($pedidoId, 'cancelado');

        // Notificar al cliente
        if (!empty($pedido['cliente_correo'])) {
            $this->emailService->notificarEstadoPedido(
                $pedido['cliente_correo'],
                $pedido['cliente_nombre'] ?? '',
                (string)$pedidoId,
                'Cancelado'
            );
        }

        return $response->withHeader('Location', ($_ENV['APP_BASEPATH'] ?? '') . '/pedidos?success=Pedido+cancelado')
                        ->withStatus(302);
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
