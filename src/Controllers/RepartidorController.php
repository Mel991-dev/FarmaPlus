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
 * RepartidorController — Panel del repartidor.
 *
 * Módulo 11: Panel Repartidor (RF-7.2, RF-7.3)
 * - Solo ve sus pedidos asignados
 * - Actualiza estados: en_camino / entregado / devuelto_fallido
 * - Pedido devuelto exige observación + notifica al Gerente (HU-REP-02)
 */
class RepartidorController
{
    private PedidoModel        $pedidoModel;
    private DetallePedidoModel $detallePedidoModel;
    private EmailService       $emailService;

    // Estados permitidos y sus transiciones válidas
    private const TRANSICIONES = [
        'en_preparacion' => ['en_camino'],
        'en_camino'      => ['entregado', 'devuelto'],
    ];

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $this->pedidoModel        = new PedidoModel($db);
        $this->detallePedidoModel = new DetallePedidoModel($db);
        $this->emailService       = new EmailService();
    }

    /**
     * GET /repartidor/pedidos — Lista de mis pedidos asignados.
     * RF-7.2: Solo ve los suyos, excluyendo entregados/cancelados.
     */
    public function misPedidos(Request $request, Response $response): Response
    {
        $repartidorId = (int)($_SESSION['usuario_id'] ?? 0);
        $pedidos      = $this->pedidoModel->misPedidosRepartidor($repartidorId);
        $titulo       = 'Mis Entregas';

        ob_start();
        require __DIR__ . '/../../views/repartidor/pedidos.php';
        $contenido = ob_get_clean();

        $response->getBody()->write($contenido);
        return $response;
    }

    /**
     * GET /repartidor/pedidos/{id} — Detalle de un pedido propio.
     */
    public function detallePedido(Request $request, Response $response, array $args): Response
    {
        $id           = (int)$args['id'];
        $repartidorId = (int)($_SESSION['usuario_id'] ?? 0);

        $pedido = $this->pedidoModel->obtenerPorId($id);

        // Validar que el pedido pertenece al repartidor
        if (!$pedido || (int)($pedido['repartidor_id'] ?? 0) !== $repartidorId) {
            return $response->withHeader('Location', ($_ENV['APP_BASEPATH'] ?? '') . '/repartidor/pedidos')
                            ->withStatus(302);
        }

        $items  = $this->detallePedidoModel->obtenerPorPedido($id);
        $titulo = "Entrega #{$id}";

        ob_start();
        require __DIR__ . '/../../views/repartidor/detalle.php';
        $contenido = ob_get_clean();

        $response->getBody()->write($contenido);
        return $response;
    }

    /**
     * POST /repartidor/pedidos/{id}/actualizar-estado
     * RF-7.3: Validar transición, actualizar, notificar cliente.
     * HU-REP-02: Si devuelto, registrar observación y notificar gerente.
     */
    public function actualizarEstado(Request $request, Response $response, array $args): Response
    {
        $pedidoId     = (int)$args['id'];
        $repartidorId = (int)($_SESSION['usuario_id'] ?? 0);
        $data         = (array)$request->getParsedBody();
        $nuevoEstado  = trim($data['estado']    ?? '');
        $observacion  = trim($data['observacion'] ?? '');

        $pedido = $this->pedidoModel->obtenerPorId($pedidoId);

        // Validar pertenencia
        if (!$pedido || (int)($pedido['repartidor_id'] ?? 0) !== $repartidorId) {
            return $this->json($response, ['success' => false, 'error' => 'Pedido no autorizado.'], 403);
        }

        $estadoActual    = $pedido['estado'];
        $permitidos      = self::TRANSICIONES[$estadoActual] ?? [];

        if (!in_array($nuevoEstado, $permitidos)) {
            return $this->json($response, [
                'success' => false,
                'error'   => "No puede pasar de '{$estadoActual}' a '{$nuevoEstado}'.",
            ], 400);
        }

        // Si es devolución, registrar observación obligatoria
        if ($nuevoEstado === 'devuelto') {
            if (empty($observacion)) {
                return $this->json($response, [
                    'success' => false,
                    'error'   => 'Debe ingresar el motivo de la devolución.',
                ], 400);
            }
            $this->pedidoModel->registrarDevolucion($pedidoId, $observacion);

            // Notificar al gerente
            $emailGerente = $this->pedidoModel->obtenerEmailGerente();
            if ($emailGerente) {
                $this->emailService->notificarEstadoPedido(
                    $emailGerente,
                    'Gerente',
                    (string)$pedidoId,
                    "DEVUELTO — Obs: {$observacion} (cliente: {$pedido['cliente_nombre']})"
                );
            }
        } else {
            $this->pedidoModel->actualizarEstado($pedidoId, $nuevoEstado);
        }

        // Notificar al cliente del cambio de estado
        if (!empty($pedido['cliente_correo'])) {
            $etiquetas = [
                'en_camino'       => 'En camino 🚚',
                'entregado'       => 'Entregado ✅',
                'devuelto'        => 'No entregado ❌',
            ];
            $this->emailService->notificarEstadoPedido(
                $pedido['cliente_correo'],
                $pedido['cliente_nombre'] ?? '',
                (string)$pedidoId,
                $etiquetas[$nuevoEstado] ?? $nuevoEstado
            );
        }

        return $this->json($response, [
            'success'       => true,
            'nuevoEstado'   => $nuevoEstado,
            'mensaje'       => 'Estado actualizado correctamente.',
        ]);
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
