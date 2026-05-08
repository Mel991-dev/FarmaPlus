<?php

declare(strict_types=1);

use Slim\App;
use App\Controllers\ReporteController;
use App\Controllers\DashboardController;
use App\Controllers\DevolucionController;
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

        // ── Módulo de Devoluciones ──────────────────────────────────────────
        $group->get('/devoluciones',                  [DevolucionController::class, 'listar']);
        $group->get('/devoluciones/crear',            [DevolucionController::class, 'crear']);
        $group->post('/devoluciones/crear',           [DevolucionController::class, 'guardar']);
        $group->get('/devoluciones/{id:[0-9]+}',      [DevolucionController::class, 'detalle']);
        $group->post('/devoluciones/{id:[0-9]+}/aprobar',  [DevolucionController::class, 'aprobar']);
        $group->post('/devoluciones/{id:[0-9]+}/rechazar', [DevolucionController::class, 'rechazar']);

    })->add($gerenteRoles)->add($authMiddleware);
};

