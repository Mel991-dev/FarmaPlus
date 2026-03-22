<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response as SlimResponse;

/**
 * RolMiddleware — Verifica que el usuario tenga uno de los roles permitidos.
 * Si el rol no está en la lista, redirige al dashboard (HTTP 302).
 *
 * Uso en rutas:
 *   $app->get('/admin/...', ...)->add(new RolMiddleware(['administrador']));
 */
class RolMiddleware
{
    public function __construct(private array $rolesPermitidos) {}

    public function __invoke(Request $request, Handler $handler): Response
    {
        $rolActual = $_SESSION['rol'] ?? '';

        if (!in_array($rolActual, $this->rolesPermitidos, true)) {
            $response = new SlimResponse();
            $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
            return $response
                ->withHeader('Location', $basePath . '/dashboard')
                ->withStatus(302);
        }

        return $handler->handle($request);
    }
}
