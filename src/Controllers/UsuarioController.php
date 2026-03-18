<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Models\UsuarioModel;
use App\Services\AuthService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * UsuarioController — CRUD de empleados (solo Administrador).
 *
 * Módulo 13: Gestión de Usuarios (RF-3.2, HU-ADMIN-01 a 02)
 * - Crear, editar, suspender y eliminar cuentas de empleados
 * - Solo el Administrador puede ejecutar estas acciones (RN-13)
 */
class UsuarioController
{
    private UsuarioModel $usuarioModel;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $this->usuarioModel = new UsuarioModel($db);
    }

    /** GET /admin/usuarios — Listar empleados */
    public function listar(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 1
        return $response;
    }

    /** GET /admin/usuarios/crear */
    public function mostrarCrear(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 1
        return $response;
    }

    /** POST /admin/usuarios/crear */
    public function crear(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 1
        // Validar: sin duplicados correo/cédula (HU-ADMIN-01)
        // Hash bcrypt cost 12 antes de guardar
        return $response;
    }

    /** GET /admin/usuarios/{id}/editar */
    public function mostrarEditar(Request $request, Response $response, array $args): Response
    {
        // TODO: Implementar en Semana 1
        return $response;
    }

    /** POST /admin/usuarios/{id}/editar */
    public function actualizar(Request $request, Response $response, array $args): Response
    {
        // TODO: Implementar en Semana 1
        // Cambio de rol efectivo en próxima sesión (HU-ADMIN-02)
        return $response;
    }

    /** POST /admin/usuarios/{id}/suspender */
    public function suspender(Request $request, Response $response, array $args): Response
    {
        // TODO: Implementar en Semana 1
        // Suspensión inmediata — invalidar sesión activa si existe
        return $response;
    }

    /** POST /admin/usuarios/{id}/eliminar */
    public function eliminar(Request $request, Response $response, array $args): Response
    {
        // TODO: Implementar en Semana 1
        // FK RESTRICT: no se puede borrar si tiene ventas asociadas
        return $response;
    }
}
