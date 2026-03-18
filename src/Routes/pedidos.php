<?php

declare(strict_types=1);

use Slim\App;
use App\Controllers\PedidoController;
use App\Controllers\RepartidorController;
use App\Middleware\AuthMiddleware;
use App\Middleware\RolMiddleware;

return function (App $app): void {
    $authMiddleware = new AuthMiddleware();

    // Gestión de pedidos (gerente, administrador)
    $app->group('/pedidos', function ($group) {
        $group->get('', [PedidoController::class, 'listar']);
        $group->get('/{id}', [PedidoController::class, 'detalle']);
        $group->post('/{id}/asignar-repartidor', [PedidoController::class, 'asignarRepartidor']);
        $group->post('/{id}/cancelar', [PedidoController::class, 'cancelar']);
    })->add(new RolMiddleware(['gerente', 'administrador']))->add($authMiddleware);

    // Panel del repartidor
    $app->group('/repartidor', function ($group) {
        $group->get('/pedidos', [RepartidorController::class, 'misPedidos']);
        $group->get('/pedidos/{id}', [RepartidorController::class, 'detallePedido']);
        $group->post('/pedidos/{id}/actualizar-estado', [RepartidorController::class, 'actualizarEstado']);
    })->add(new RolMiddleware(['repartidor', 'gerente', 'administrador']))->add($authMiddleware);
};
