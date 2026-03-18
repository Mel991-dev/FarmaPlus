<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Models\PedidoModel;
use App\Services\EmailService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * PedidoController — Pedidos en línea y asignación de repartidor.
 *
 * Módulo 9: Pedidos en Línea (RF-7.1)
 * - Listado con estados diferenciados
 * - Asignación de repartidor
 * - Notificación por correo en cambio de estado (RF-7.4)
 */
class PedidoController
{
    private PedidoModel $pedidoModel;
    private EmailService $emailService;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $this->pedidoModel  = new PedidoModel($db);
        $this->emailService = new EmailService();
    }

    /** GET /pedidos */
    public function listar(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 4
        return $response;
    }

    /** GET /pedidos/{id} */
    public function detalle(Request $request, Response $response, array $args): Response
    {
        // TODO: Implementar en Semana 4
        return $response;
    }

    /** POST /pedidos/{id}/asignar-repartidor */
    public function asignarRepartidor(Request $request, Response $response, array $args): Response
    {
        // TODO: Implementar en Semana 4
        return $response;
    }

    /** POST /pedidos/{id}/cancelar */
    public function cancelar(Request $request, Response $response, array $args): Response
    {
        // TODO: Implementar en Semana 4
        return $response;
    }
}
