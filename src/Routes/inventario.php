<?php

declare(strict_types=1);

use Slim\App;
use App\Controllers\ProductoController;
use App\Controllers\InventarioController;
use App\Middleware\AuthMiddleware;
use App\Middleware\RolMiddleware;

return function (App $app): void {
    $authMiddleware = new AuthMiddleware();
    $stockRoles = new RolMiddleware(['auxiliar', 'gerente', 'administrador']);

    $app->group('/inventario', function ($group) {
        // Productos
        $group->get('/productos', [ProductoController::class, 'listar']);
        $group->get('/productos/crear', [ProductoController::class, 'mostrarCrear']);
        $group->post('/productos/crear', [ProductoController::class, 'crear']);
        $group->get('/productos/{id}', [ProductoController::class, 'detalle']);
        $group->get('/productos/{id}/editar', [ProductoController::class, 'mostrarEditar']);
        $group->post('/productos/{id}/editar', [ProductoController::class, 'actualizar']);

        // Lotes — FEFO
        $group->get('/lotes', [InventarioController::class, 'listarLotes']);
        $group->get('/lotes/registrar', [InventarioController::class, 'mostrarRegistroLote']);
        $group->post('/lotes/registrar', [InventarioController::class, 'registrarLote']);

        // Alertas de inventario
        $group->get('/alertas', [InventarioController::class, 'alertas']);
        $group->post('/alertas/{id}/resolver', [InventarioController::class, 'resolverAlerta']);

        // Proveedores
        $group->get('/proveedores', [InventarioController::class, 'listarProveedores']);
        $group->get('/proveedores/crear', [InventarioController::class, 'mostrarCrearProveedor']);
        $group->post('/proveedores/crear', [InventarioController::class, 'crearProveedor']);
        $group->get('/proveedores/{id}/editar', [InventarioController::class, 'mostrarEditarProveedor']);
        $group->post('/proveedores/{id}/editar', [InventarioController::class, 'actualizarProveedor']);

    })->add($stockRoles)->add($authMiddleware);
};
