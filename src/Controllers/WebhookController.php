<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Models\PedidoModel;
use App\Models\DetallePedidoModel;
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
    private FEFOService        $fefoService;
    private EmailService       $emailService;
    private PedidoModel        $pedidoModel;
    private DetallePedidoModel $detallePedidoModel;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $this->mpService          = new MercadoPagoService($db);
        $this->fefoService        = new FEFOService($db);
        $this->emailService       = new EmailService();
        $this->pedidoModel        = new PedidoModel($db);
        $this->detallePedidoModel = new DetallePedidoModel($db);
    }

    /**
     * POST /webhooks/mercadopago
     *
     * MercadoPago envía un JSON con la notificación.
     * Formato típico (IPN v2):
     * {
     *   "action": "payment.created",
     *   "data": { "id": "12345678" }
     * }
     */
    public function mercadopago(Request $request, Response $response): Response
    {
        // Leer el cuerpo crudo del request
        $payload   = (string)$request->getBody();
        $signature = $request->getHeaderLine('x-signature');
        $dataId    = $request->getHeaderLine('x-request-id');

        // 1. Verificar firma HMAC (en desarrollo con secret vacío, se omite)
        if (!$this->mpService->verificarFirmaWebhook($payload, $signature)) {
            error_log('[Webhook MP] Firma inválida. Payload: ' . substr($payload, 0, 200));
            return $response->withStatus(401);
        }

        // 2. Parsear el body
        $body = json_decode($payload, true);
        if (!$body) {
            return $response->withStatus(400);
        }

        // Soportar tanto IPN clásico como notificaciones v2
        $paymentId = null;
        if (isset($body['data']['id'])) {
            $paymentId = (string)$body['data']['id'];
        } elseif (isset($body['id'])) {
            $paymentId = (string)$body['id'];
        }

        // Si no hay payment_id o es un topic diferente a 'payment', responder OK igualmente
        $topic = $body['type'] ?? $body['topic'] ?? '';
        if (!in_array($topic, ['payment', 'merchant_order']) || !$paymentId) {
            return $response->withStatus(200);
        }

        // 3. Consultar el estado del pago vía SDK
        $pagoInfo = $this->mpService->obtenerEstadoPago($paymentId);

        if (empty($pagoInfo['status'])) {
            error_log('[Webhook MP] No se pudo obtener info del pago: ' . $paymentId);
            return $response->withStatus(200); // Responder 200 de todas formas
        }

        // 4. Solo procesar si el pago fue aprobado
        if ($pagoInfo['status'] !== 'approved') {
            error_log("[Webhook MP] Pago {$paymentId} con status: {$pagoInfo['status']}. No se procesa.");
            return $response->withStatus(200);
        }

        // 5. Buscar el pedido por la referencia externa (= pedido_id en nuestra BD)
        $externalRef = $pagoInfo['external_reference'] ?? '';
        if (empty($externalRef)) {
            return $response->withStatus(200);
        }

        $pedido = $this->pedidoModel->obtenerPorMpReferencia($externalRef);
        if (!$pedido) {
            error_log("[Webhook MP] Pedido no encontrado con referencia: {$externalRef}");
            return $response->withStatus(200);
        }

        // Evitar doble procesamiento
        if ($pedido['estado'] !== 'pendiente') {
            return $response->withStatus(200);
        }

        // 6. Actualizar estado del pedido a 'pagado'
        $this->pedidoModel->actualizarMpPago(
            (int)$pedido['pedido_id'],
            $paymentId,
            $pagoInfo['status']
        );

        // 7. Descontar stock por FEFO (RN-06)
        $items = $this->detallePedidoModel->obtenerPorPedido((int)$pedido['pedido_id']);
        foreach ($items as $item) {
            try {
                $this->fefoService->descontarStock((int)$item['producto_id'], (int)$item['cantidad']);
            } catch (\RuntimeException $e) {
                // Loguear pero no bloquear el pago — el administrador gestionará el stock
                error_log('[Webhook MP] Stock insuficiente: ' . $e->getMessage());
            }
        }

        // 8. Enviar email de confirmación al cliente
        if (!empty($pedido['cliente_correo'])) {
            $emailItems = array_map(fn($i) => [
                'nombre'          => $i['producto_nombre'],
                'cantidad'        => $i['cantidad'],
                'precio_unitario' => $i['precio_unitario'],
            ], $items);

            $this->emailService->enviarConfirmacionPedido(
                $pedido['cliente_correo'],
                $pedido['cliente_nombre'] ?? 'Cliente',
                (int)$pedido['pedido_id'],
                $emailItems,
                (float)$pedido['subtotal'],
                (float)$pedido['costo_envio'],
                (float)$pedido['total']
            );
        }

        error_log("[Webhook MP] Pedido #{$pedido['pedido_id']} procesado correctamente. Pago: {$paymentId}");

        // 9. MercadoPago espera HTTP 200 para no reintentar
        return $response->withStatus(200);
    }
}
