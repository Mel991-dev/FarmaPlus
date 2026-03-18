<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Models\PedidoModel;
use App\Services\EmailService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * RepartidorController — Panel del repartidor.
 *
 * Módulo 11: Panel Repartidor (RF-7.2, RF-7.3)
 * - Solo ve sus pedidos asignados
 * - Actualiza estados: Preparando / En camino / Entregado / Devuelto-Fallido
 * - Pedido devuelto exige observación + notifica al Gerente (HU-REP-02)
 */
class RepartidorController
{
    private PedidoModel $pedidoModel;
    private EmailService $emailService;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $this->pedidoModel  = new PedidoModel($db);
        $this->emailService = new EmailService();
    }

    /** GET /repartidor/pedidos */
    public function misPedidos(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 4
        // Solo pedidos donde repartidor_id = $_SESSION['usuario_id'] (RF-7.2)
        return $response;
    }

    /** GET /repartidor/pedidos/{id} */
    public function detallePedido(Request $request, Response $response, array $args): Response
    {
        // TODO: Implementar en Semana 4
        return $response;
    }

    /** POST /repartidor/pedidos/{id}/actualizar-estado */
    public function actualizarEstado(Request $request, Response $response, array $args): Response
    {
        // TODO: Implementar en Semana 4
        // 1. Validar estado permitido (RF-7.3)
        // 2. Actualizar estado en pedidos
        // 3. Notificar al cliente por correo (RF-7.4)
        // 4. Si devuelto: registrar observación + notificar gerente
        return $response;
    }
}
