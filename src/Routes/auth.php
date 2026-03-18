<?php

use App\Controllers\AuthController;
use App\Middleware\AuthMiddleware;
use Slim\App;

return function (App $app) {

    // GET  /login
    $app->get('/login', [AuthController::class, 'mostrarLogin']);

    // POST /login
    $app->post('/login', [AuthController::class, 'procesarLogin']);

    // POST /logout
    $app->post('/logout', [AuthController::class, 'logout']);

    // GET  /recuperar-contrasena
    $app->get('/recuperar-contrasena', [AuthController::class, 'mostrarRecuperar']);

    // POST /recuperar-contrasena
    $app->post('/recuperar-contrasena', [AuthController::class, 'procesarRecuperar']);

    // GET  /nueva-contrasena?token=xxx
    $app->get('/nueva-contrasena', [AuthController::class, 'mostrarNuevaContrasena']);

    // POST /nueva-contrasena
    $app->post('/nueva-contrasena', [AuthController::class, 'procesarNuevaContrasena']);
};
