<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Models\ReporteModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * ReporteController — Módulo 12: Reportes gerenciales.
 * RF-8.1 Ventas por período · RF-8.2 Productos más vendidos
 * RF-8.3 Rendimiento por vendedor · RF-8.4 Exportar PDF
 *
 * Rutas (protegidas por RolMiddleware gerente|administrador):
 *   GET /gerente/reportes/ventas
 *   GET /gerente/reportes/inventario
 *   GET /gerente/reportes/exportar/{tipo}/{formato}
 */
class ReporteController
{
    private ReporteModel $model;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $this->model = new ReporteModel($db);
    }

    // ─────────────────────────────────────────────────────────
    // GET /gerente/reportes/ventas
    // ─────────────────────────────────────────────────────────
    public function ventas(Request $request, Response $response): Response
    {
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');

        // Filtros desde query string
        $params = $request->getQueryParams();
        $hasta  = $params['hasta']  ?? date('Y-m-d');
        $desde  = $params['desde']  ?? date('Y-m-d', strtotime('-29 days'));

        // Datos
        $kpis           = $this->model->kpisVentasPeriodo($desde, $hasta);
        $ventasPorDia   = $this->model->ventasPorDia($desde, $hasta);
        $topProductos   = $this->model->productosMasVendidos($desde, $hasta, 10);
        $porMetodoPago  = $this->model->ventasPorMetodoPago($desde, $hasta);
        $porVendedor    = $this->model->rendimientoPorVendedor($desde, $hasta);
        $estadoPedidos  = $this->model->pedidosPorEstado($desde, $hasta);

        $titulo = 'Reporte de Ventas';
        ob_start();
        include __DIR__ . '/../../views/reportes/ventas.php';
        $contenido = ob_get_clean();

        ob_start();
        include __DIR__ . '/../../views/layouts/base.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    // ─────────────────────────────────────────────────────────
    // GET /gerente/reportes/inventario
    // ─────────────────────────────────────────────────────────
    public function inventario(Request $request, Response $response): Response
    {
        $stockBajo      = $this->model->productosStockBajo();
        $proxVencer     = $this->model->lotesProximosVencer(30);
        $proxVencer60   = $this->model->lotesProximosVencer(60);
        $resumenCat     = $this->model->resumenInventarioPorCategoria();

        $titulo = 'Reporte de Inventario';
        ob_start();
        include __DIR__ . '/../../views/reportes/inventario.php';
        $contenido = ob_get_clean();

        ob_start();
        include __DIR__ . '/../../views/layouts/base.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    // ─────────────────────────────────────────────────────────
    // GET /gerente/reportes/exportar/{tipo}/{formato}
    // tipo:    ventas | inventario
    // formato: pdf
    // ─────────────────────────────────────────────────────────
    public function exportar(Request $request, Response $response, array $args): Response
    {
        $tipo    = $args['tipo']    ?? 'ventas';
        $formato = $args['formato'] ?? 'pdf';

        $params = $request->getQueryParams();
        $hasta  = $params['hasta'] ?? date('Y-m-d');
        $desde  = $params['desde'] ?? date('Y-m-d', strtotime('-29 days'));

        if ($formato !== 'pdf') {
            $response->getBody()->write('Formato no soportado.');
            return $response->withStatus(400);
        }

        // Cargar DOMPDF
        $options = new \Dompdf\Options();
        $options->set('isRemoteEnabled', false);
        $options->set('defaultFont', 'Helvetica');
        $dompdf = new \Dompdf\Dompdf($options);

        if ($tipo === 'ventas') {
            $kpis          = $this->model->kpisVentasPeriodo($desde, $hasta);
            $ventasPorDia  = $this->model->ventasPorDia($desde, $hasta);
            $topProductos  = $this->model->productosMasVendidos($desde, $hasta, 10);
            $porVendedor   = $this->model->rendimientoPorVendedor($desde, $hasta);
            ob_start();
            include __DIR__ . '/../../views/reportes/pdf_ventas.php';
            $html = ob_get_clean();
            $filename = "reporte_ventas_{$desde}_{$hasta}.pdf";
        } else {
            $stockBajo  = $this->model->productosStockBajo();
            $proxVencer = $this->model->lotesProximosVencer(30);
            $resumenCat = $this->model->resumenInventarioPorCategoria();
            ob_start();
            include __DIR__ . '/../../views/reportes/pdf_inventario.php';
            $html = ob_get_clean();
            $filename = "reporte_inventario_" . date('Y-m-d') . ".pdf";
        }

        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();

        $pdfContent = $dompdf->output();

        return $response
            ->withHeader('Content-Type', 'application/pdf')
            ->withHeader('Content-Disposition', "attachment; filename=\"{$filename}\"")
            ->withHeader('Content-Length', (string) strlen($pdfContent))
            ->withBody((function () use ($pdfContent) {
                $stream = fopen('php://temp', 'r+');
                fwrite($stream, $pdfContent);
                rewind($stream);
                return new \Slim\Psr7\Stream($stream);
            })());
    }
}
