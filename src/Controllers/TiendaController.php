<?php declare(strict_types=1);
namespace App\Controllers;

use App\Database\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * TiendaController — Catálogo, carrito y checkout del e-commerce.
 * Módulo 8: E-commerce (RF-6.1 a RF-6.6)
 */
class TiendaController
{
    public function catalogo(Request $request, Response $response): Response
    {
        // TODO: Semana 4 — Listar productos sin control especial (RF-6.1, HU-CLI-03)
        return $response;
    }

    public function fichaProducto(Request $request, Response $response, array $args): Response
    {
        // TODO: Semana 4
        return $response;
    }

    public function carrito(Request $request, Response $response): Response
    {
        // TODO: Semana 4 — Ver carrito (HU-CLI-04)
        return $response;
    }

    public function agregarAlCarrito(Request $request, Response $response): Response
    {
        // TODO: Semana 4
        return $response;
    }

    public function actualizarCarrito(Request $request, Response $response): Response
    {
        // TODO: Semana 4
        return $response;
    }

    public function vaciarCarrito(Request $request, Response $response): Response
    {
        // TODO: Semana 4
        return $response;
    }

    public function checkout(Request $request, Response $response): Response
    {
        // TODO: Semana 4 — Checkout 3 pasos con cálculo de domicilio (RF-6.3, HU-CLI-05)
        return $response;
    }

    public function procesarCheckout(Request $request, Response $response): Response
    {
        // TODO: Semana 4 — Crear pedido + preferencia MercadoPago (RN-05)
        return $response;
    }

    public function confirmacion(Request $request, Response $response, array $args): Response
    {
        // TODO: Semana 4 — Pantalla post-pago aprobado
        return $response;
    }
}
