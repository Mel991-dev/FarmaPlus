<?php declare(strict_types=1);
namespace App\Controllers;

use App\Database\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * ConfiguracionController — Módulo 14: Configuración del Sistema.
 * Solo Administrador. Gestiona variables de negocio en la tabla configuracion.
 */
class ConfiguracionController
{
    public function mostrar(Request $request, Response $response): Response
    {
        // TODO: Semana 1 — Leer tabla configuracion y mostrar formulario
        return $response;
    }

    public function actualizar(Request $request, Response $response): Response
    {
        // TODO: Semana 1 — UPDATE a la tabla configuracion
        return $response;
    }
}
