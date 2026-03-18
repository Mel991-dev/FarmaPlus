<?php

declare(strict_types=1);

namespace App\Middleware;

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Server\MiddlewareInterface;
use Psr\Http\Server\RequestHandlerInterface as Handler;
use Slim\Psr7\Response as SlimResponse;

/**
 * AuthMiddleware — Verifica que el usuario tenga una sesión PHP activa.
 * Si no hay sesión, redirige al login (HTTP 302).
 */
class AuthMiddleware implements MiddlewareInterface
{
    public function process(Request $request, Handler $handler): Response
    {
        if (empty($_SESSION['usuario_id'])) {
            $response = new SlimResponse();
            $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
            return $response
                ->withHeader('Location', $basePath . '/login')
                ->withStatus(302);
        }

        return $handler->handle($request);
    }
}
