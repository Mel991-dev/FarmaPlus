<?php

declare(strict_types=1);

use Slim\App;
use App\Controllers\UsuarioController;
use App\Middleware\AuthMiddleware;
use App\Middleware\RolMiddleware;

return function (App $app): void {
    $authMiddleware = new AuthMiddleware();
    $adminOnly = new RolMiddleware(['administrador']);

    $app->group('/admin', function ($group) {
        // Gestión de usuarios/empleados
        $group->get('/usuarios', [UsuarioController::class, 'listar']);
        $group->get('/usuarios/crear', [UsuarioController::class, 'mostrarCrear']);
        $group->post('/usuarios/crear', [UsuarioController::class, 'crear']);
        $group->get('/usuarios/{id}/editar', [UsuarioController::class, 'mostrarEditar']);
        $group->post('/usuarios/{id}/editar', [UsuarioController::class, 'actualizar']);
        $group->post('/usuarios/{id}/suspender', [UsuarioController::class, 'suspender']);
        $group->post('/usuarios/{id}/eliminar', [UsuarioController::class, 'eliminar']);

        // Configuración del sistema
        $group->get('/configuracion', [\App\Controllers\ConfiguracionController::class, 'mostrar']);
        $group->post('/configuracion', [\App\Controllers\ConfiguracionController::class, 'actualizar']);

        // Logs de auditoría
        $group->get('/logs', [\App\Controllers\LogAuditoriaController::class, 'listar']);

    })->add($adminOnly)->add($authMiddleware);
};
