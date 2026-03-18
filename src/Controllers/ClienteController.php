<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Models\ClienteModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * ClienteController — Registro y perfil del cliente.
 *
 * Módulo 2: Gestión de Clientes (RF-2.1 a RF-2.5)
 * - Autoregistro con consentimiento Ley 1581/2012 (RN-16, RN-17)
 * - Gestión de múltiples direcciones de entrega
 * - Historial de pedidos
 */
class ClienteController
{
    private ClienteModel $clienteModel;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $this->clienteModel = new ClienteModel($db);
    }

    /** GET /registro */
    public function mostrarRegistro(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 3
        return $response;
    }

    /** POST /registro */
    public function registrar(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 3
        // 1. Validar campos (RF-2.1)
        // 2. Registrar consentimiento: fecha, hora, IP (RF-2.2, RN-16, RN-17)
        // 3. Hash bcrypt cost 12
        return $response;
    }

    /** GET /mi-cuenta */
    public function perfil(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 3
        return $response;
    }

    /** POST /mi-cuenta/actualizar */
    public function actualizar(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 3
        return $response;
    }

    /** GET /mi-cuenta/pedidos */
    public function misPedidos(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 4
        return $response;
    }

    /** GET /mi-cuenta/direcciones */
    public function misDirectiones(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 3
        return $response;
    }

    /** POST /mi-cuenta/direcciones/crear */
    public function crearDireccion(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 3
        return $response;
    }

    /** POST /mi-cuenta/direcciones/{id}/eliminar */
    public function eliminarDireccion(Request $request, Response $response, array $args): Response
    {
        // TODO: Implementar en Semana 3
        // Historial de pedidos preservado aunque se elimine la dirección (HU-CLI-02)
        return $response;
    }
}
