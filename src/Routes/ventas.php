<?php

declare(strict_types=1);

use Slim\App;
use App\Controllers\VentaController;
use App\Middleware\AuthMiddleware;
use App\Middleware\RolMiddleware;

return function (App $app): void {
    $authMiddleware = new AuthMiddleware();
    $ventaRoles = new RolMiddleware(['vendedor', 'gerente', 'administrador']);

    $app->group('/ventas', function ($group) {
        // Punto de venta presencial (POS)
        $group->get('/pos', [VentaController::class, 'pos']);
        $group->post('/pos/procesar', [VentaController::class, 'procesarVenta']);

        // Comprobante de venta
        $group->get('/comprobante/{id}', [VentaController::class, 'comprobante']);
        $group->post('/comprobante/{id}/enviar-correo', [VentaController::class, 'enviarComprobante']);

        // Historial de ventas del vendedor
        $group->get('/mis-ventas', [VentaController::class, 'misVentas']);

        // Buscar producto (AJAX — autocompletado POS)
        $group->get('/buscar-producto', [VentaController::class, 'buscarProducto']);

    })->add($ventaRoles)->add($authMiddleware);
};
