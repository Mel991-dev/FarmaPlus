<?php

declare(strict_types=1);

use Slim\App;
use App\Controllers\ClienteController;
use App\Middleware\AuthMiddleware;

return function (App $app): void {
    // Registro de cliente (público)
    $app->get('/registro', [ClienteController::class, 'mostrarRegistro']);
    $app->post('/registro', [ClienteController::class, 'registrar']);

    // Tienda — catálogo (público)
    $app->get('/tienda', [\App\Controllers\TiendaController::class, 'catalogo']);
    $app->get('/tienda/producto/{id}', [\App\Controllers\TiendaController::class, 'fichaProducto']);

    // Carrito y checkout (requiere sesión cliente)
    $app->get('/tienda/carrito', [\App\Controllers\TiendaController::class, 'carrito'])
        ->add(new AuthMiddleware());
    $app->post('/tienda/carrito/agregar', [\App\Controllers\TiendaController::class, 'agregarAlCarrito'])
        ->add(new AuthMiddleware());
    $app->post('/tienda/carrito/actualizar', [\App\Controllers\TiendaController::class, 'actualizarCarrito'])
        ->add(new AuthMiddleware());
    $app->post('/tienda/carrito/vaciar', [\App\Controllers\TiendaController::class, 'vaciarCarrito'])
        ->add(new AuthMiddleware());

    // Checkout en 3 pasos (requiere sesión cliente)
    $app->get('/tienda/checkout', [\App\Controllers\TiendaController::class, 'checkout'])
        ->add(new AuthMiddleware());
    $app->post('/tienda/checkout/procesar', [\App\Controllers\TiendaController::class, 'procesarCheckout'])
        ->add(new AuthMiddleware());
    $app->get('/tienda/confirmacion/{id}', [\App\Controllers\TiendaController::class, 'confirmacion'])
        ->add(new AuthMiddleware());

    // Perfil del cliente
    $app->get('/mi-cuenta', [ClienteController::class, 'perfil'])
        ->add(new AuthMiddleware());
    $app->post('/mi-cuenta/actualizar', [ClienteController::class, 'actualizar'])
        ->add(new AuthMiddleware());
    $app->get('/mi-cuenta/pedidos', [ClienteController::class, 'misPedidos'])
        ->add(new AuthMiddleware());

    // Gestión de direcciones (cliente autenticado)
    $app->get('/mi-cuenta/direcciones', [ClienteController::class, 'misDirectiones'])
        ->add(new AuthMiddleware());
    $app->post('/mi-cuenta/direcciones/crear', [ClienteController::class, 'crearDireccion'])
        ->add(new AuthMiddleware());
    $app->post('/mi-cuenta/direcciones/{id}/eliminar', [ClienteController::class, 'eliminarDireccion'])
        ->add(new AuthMiddleware());
};
