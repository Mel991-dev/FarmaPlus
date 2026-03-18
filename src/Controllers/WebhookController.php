<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Services\MercadoPagoService;
use App\Services\FEFOService;
use App\Services\EmailService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * WebhookController — Webhooks de MercadoPago.
 *
 * Módulo 8 E-commerce (RF-6.5, RF-6.6)
 * - Recibe notificación POST de MercadoPago al aprobar pago
 * - Verifica firma HMAC con MP_WEBHOOK_SECRET
 * - Actualiza estado del pedido a 'pagado'
 * - Descuenta stock por FEFO (RN-06)
 * - Envía confirmación de compra por correo (RF-6.5)
 * - No lleva AuthMiddleware — es llamado externamente por MercadoPago
 */
class WebhookController
{
    private MercadoPagoService $mpService;
    private FEFOService $fefoService;
    private EmailService $emailService;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $this->mpService    = new MercadoPagoService($db);
        $this->fefoService  = new FEFOService($db);
        $this->emailService = new EmailService();
    }

    /** POST /webhooks/mercadopago */
    public function mercadopago(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 4
        // 1. Verificar firma HMAC (MP_WEBHOOK_SECRET)
        // 2. Obtener payment_id de MercadoPago
        // 3. Consultar estado del pago vía SDK
        // 4. Si aprobado: actualizar pedido + descontar FEFO + enviar correo
        // 5. Retornar HTTP 200 a MercadoPago
        return $response->withStatus(200);
    }
}
