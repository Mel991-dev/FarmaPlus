<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * ReporteController — Reportes generales y exportación.
 *
 * Módulo 12: Reportes (RF-8.1 a RF-8.4)
 * - Ventas por período (diario, semanal, mensual)
 * - Productos más vendidos, categorías, rendimiento por vendedor
 * - Exportación PDF y Excel
 * - Reporte de vencimiento y stock bajo exportable en PDF
 */
class ReporteController
{
    public function __construct()
    {
        // Database::getInstance() se obtiene en cada método cuando se necesite
    }

    /** GET /gerente/reportes/ventas */
    public function ventas(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 5
        return $response;
    }

    /** GET /gerente/reportes/inventario */
    public function inventario(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 5
        return $response;
    }

    /** GET /gerente/reportes/exportar/{tipo}/{formato} */
    public function exportar(Request $request, Response $response, array $args): Response
    {
        // TODO: Implementar en Semana 5
        // tipo: ventas | inventario
        // formato: pdf | excel
        return $response;
    }
}
