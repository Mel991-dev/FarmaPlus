<?php

declare(strict_types=1);

use Slim\App;
use App\Controllers\ReporteController;
use App\Controllers\DashboardController;
use App\Middleware\AuthMiddleware;
use App\Middleware\RolMiddleware;

return function (App $app): void {
    $authMiddleware = new AuthMiddleware();
    $gerenteRoles = new RolMiddleware(['gerente', 'administrador']);

    $app->group('/gerente', function ($group) {
        // Dashboard gerencial (KPIs, alertas, rendimiento)
        $group->get('/dashboard', [DashboardController::class, 'gerente']);

        // Reportes y exportación
        $group->get('/reportes/ventas', [ReporteController::class, 'ventas']);
        $group->get('/reportes/inventario', [ReporteController::class, 'inventario']);
        $group->get('/reportes/exportar/{tipo}/{formato}', [ReporteController::class, 'exportar']);

        // Devoluciones
        $group->get('/devoluciones', [\App\Controllers\DevolucionController::class, 'listar']);
        $group->post('/devoluciones/{id}/aprobar', [\App\Controllers\DevolucionController::class, 'aprobar']);
        $group->post('/devoluciones/{id}/rechazar', [\App\Controllers\DevolucionController::class, 'rechazar']);

    })->add($gerenteRoles)->add($authMiddleware);
};
