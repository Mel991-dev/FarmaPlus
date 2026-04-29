<?php

declare(strict_types=1);

use Slim\Factory\AppFactory;

require __DIR__ . '/../vendor/autoload.php';

// Cargar variables de entorno
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
$dotenv->load();

// Iniciar sesión PHP
session_name($_ENV['SESSION_NAME'] ?? 'farmaplus_session');
session_start();

// Crear app Slim
$app = AppFactory::create();

// Base path para instalaciones en subdirectorio (ej: /farmaplus/public)
// Dejar vacío si se usa VirtualHost apuntando a /public directamente
$basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
if ($basePath !== '') {
    $app->setBasePath($basePath);
}

// Middleware de errores
$app->addErrorMiddleware(
    (bool) ($_ENV['APP_DEBUG'] ?? false),
    true,
    true
);

// Middleware para parsear body de requests
$app->addBodyParsingMiddleware();

// Registrar rutas
(require __DIR__ . '/../src/Routes/auth.php')($app);
(require __DIR__ . '/../src/Routes/admin.php')($app);
(require __DIR__ . '/../src/Routes/gerente.php')($app);
(require __DIR__ . '/../src/Routes/inventario.php')($app);
(require __DIR__ . '/../src/Routes/ventas.php')($app);
(require __DIR__ . '/../src/Routes/pedidos.php')($app);
(require __DIR__ . '/../src/Routes/tienda.php')($app);
(require __DIR__ . '/../src/Routes/webhooks.php')($app);

// ── Ruta raíz: redirigir al login (que gestiona internamente la redirección por rol si ya hay sesión) ───
$app->get('/', function ($request, $response) {
    $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
    return $response->withHeader('Location', $basePath . '/login')->withStatus(302);
});

// ── Dashboard principal (protegido para empleados de tienda/admin) ────────
$app->get('/dashboard', [\App\Controllers\DashboardController::class, 'index'])
    ->add(new \App\Middleware\RolMiddleware(['administrador', 'auxiliar', 'vendedor']))
    ->add(\App\Middleware\AuthMiddleware::class);

$app->run();
