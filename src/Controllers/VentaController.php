<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Models\VentaModel;
use App\Models\ProductoModel;
use App\Services\FEFOService;
use App\Services\EmailService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * VentaController — Punto de venta presencial (POS).
 *
 * Módulo 7: Ventas Presenciales (RF-5.1 a RF-5.5)
 * - Control especial requiere fórmula médica (RF-5.2, RN-02)
 * - Descuento FEFO automático al confirmar venta (RF-4.4, RN-06)
 * - Comprobante con número único formato FP-{AÑO}-{SEQ} (RN-08)
 */
class VentaController
{
    private VentaModel $ventaModel;
    private ProductoModel $productoModel;
    private FEFOService $fefoService;
    private EmailService $emailService;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $this->ventaModel    = new VentaModel($db);
        $this->productoModel = new ProductoModel($db);
        $this->fefoService   = new FEFOService($db);
        $this->emailService  = new EmailService();
    }

    /** GET /ventas/pos — Pantalla POS */
    public function pos(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 3
        return $response;
    }

    /** POST /ventas/pos/procesar — Confirmar venta */
    public function procesarVenta(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 3
        // 1. Verificar control especial → fórmula médica (RF-5.2, RN-02)
        // 2. Verificar stock > 0 por producto (RN-07)
        // 3. Llamar FEFOService.descontarStock() (RN-06)
        // 4. Generar número comprobante: FP-{AÑO}-{SEQ}
        // 5. Insertar en ventas_presenciales y detalle_venta
        return $response;
    }

    /** GET /ventas/comprobante/{id} */
    public function comprobante(Request $request, Response $response, array $args): Response
    {
        // TODO: Implementar en Semana 3
        return $response;
    }

    /** POST /ventas/comprobante/{id}/enviar-correo */
    public function enviarComprobante(Request $request, Response $response, array $args): Response
    {
        // TODO: Implementar en Semana 3
        // Enviar comprobante por correo vía PHPMailer (RF-5.5)
        return $response;
    }

    /** GET /ventas/mis-ventas */
    public function misVentas(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 3
        // Solo ventas del usuario autenticado (HU-VEND-04)
        return $response;
    }

    /** GET /ventas/buscar-producto — AJAX autocompletado POS */
    public function buscarProducto(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 3
        // Retornar JSON con nombre, stock calculado, precio, control_especial
        return $response;
    }
}
