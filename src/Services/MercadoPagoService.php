<?php

declare(strict_types=1);

namespace App\Services;

use PDO;
use MercadoPago\MercadoPagoConfig;
use MercadoPago\Client\Preference\PreferenceClient;
use MercadoPago\Client\Payment\PaymentClient;

/**
 * MercadoPagoService — Wrapper del SDK oficial de MercadoPago PHP.
 *
 * Módulo 8: E-commerce (RF-6.4, RF-6.5, RN-18)
 * - Crear preferencias de pago (redirige al checkout de MercadoPago)
 * - Verificar estado de un pago vía API
 * - No se almacenan datos bancarios en nuestra BD (RN-18)
 */
class MercadoPagoService
{
    private bool $isSandbox;

    public function __construct(private PDO $db)
    {
        $token = $_ENV['MP_ACCESS_TOKEN'] ?? '';
        MercadoPagoConfig::setAccessToken($token);
        // Si el access token empieza con TEST-, o si MP_SANDBOX es true, estamos en sandbox
        $this->isSandbox = filter_var($_ENV['MP_SANDBOX'] ?? 'false', FILTER_VALIDATE_BOOLEAN) 
                           || str_starts_with($token, 'TEST-');
    }

    /**
     * Crear preferencia de pago en MercadoPago.
     * Retorna la URL de checkout para redirigir al cliente.
     *
     * @param array $items       Lista de [{title, quantity, unit_price}]
     * @param int   $pedidoId    ID del pedido en nuestra BD (para back_urls)
     * @param float $envio       Costo del domicilio
     * @return string            URL de checkout de MercadoPago (sandbox o producción)
     */
    public function crearPreferencia(array $items, int $pedidoId, float $envio): string
    {
        $basePath  = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $appUrl    = rtrim($_ENV['APP_URL']      ?? 'http://localhost', '/');

        // Construir items con formato MercadoPago
        $mpItems = [];
        foreach ($items as $item) {
            $mpItems[] = [
                'id'          => (string)($item['producto_id'] ?? $item['id'] ?? 'prod'),
                'title'       => (string)($item['nombre'] ?? $item['title'] ?? 'Producto'),
                'quantity'    => (int)($item['cantidad'] ?? $item['quantity'] ?? 1),
                'unit_price'  => (float)($item['precio_unitario'] ?? $item['unit_price'] ?? 0),
                'currency_id' => 'COP',
            ];
        }

        // Agregar costo de envío como ítem separado si aplica
        if ($envio > 0) {
            $mpItems[] = [
                'id'         => 'envio',
                'title'      => 'Costo de domicilio',
                'quantity'   => 1,
                'unit_price' => (float)$envio,
                'currency_id' => 'COP',
            ];
        }

        $client = new PreferenceClient();

        $preferenceData = [
            'items' => $mpItems,

            // URLs de retorno al finalizar el proceso de pago
            'back_urls' => [
                'success' => "{$appUrl}{$basePath}/tienda/confirmacion/{$pedidoId}?status=approved",
                'failure' => "{$appUrl}{$basePath}/tienda/confirmacion/{$pedidoId}?status=rejected",
                'pending' => "{$appUrl}{$basePath}/tienda/confirmacion/{$pedidoId}?status=pending",
            ],

            // Redirigir automáticamente al completar
            'auto_return' => 'approved',

            // Referencia externa para identificar el pedido en el webhook
            'external_reference' => (string)$pedidoId,

            // Webhooks (MercadoPago notificará aquí)
            'notification_url' => "{$appUrl}{$basePath}/webhooks/mercadopago",

            // Límite de tiempo para completar el pago (30 min)
            'expires'         => true,
            'expiration_date_from' => date('c'),
            'expiration_date_to'   => date('c', strtotime('+30 minutes')),
        ];

        try {
            $preference = $client->create($preferenceData);

            // Sandbox usa sandbox_init_point, producción usa init_point
            return $this->isSandbox
                ? ($preference->sandbox_init_point ?? '')
                : ($preference->init_point ?? '');
        } catch (\Throwable $e) {
            // Log el error pero no detener el flujo
            error_log('[MercadoPago] Error al crear preferencia: ' . $e->getMessage());
            throw new \RuntimeException('No se pudo conectar con MercadoPago. Verifique las credenciales en .env');
        }
    }

    /**
     * Consultar el estado de un pago por su ID.
     * Usado por el WebhookController para validar el pago antes de confirmar el pedido.
     *
     * @return array ['id', 'status', 'external_reference', 'transaction_amount']
     */
    public function obtenerEstadoPago(string $paymentId): array
    {
        try {
            $client  = new PaymentClient();
            $payment = $client->get((int)$paymentId);

            return [
                'id'                  => $payment->id,
                'status'              => $payment->status,
                'external_reference'  => $payment->external_reference,
                'transaction_amount'  => $payment->transaction_amount,
                'currency_id'         => $payment->currency_id,
                'payer_email'         => $payment->payer->email ?? null,
            ];
        } catch (\Throwable $e) {
            error_log('[MercadoPago] Error al obtener pago: ' . $e->getMessage());
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    /**
     * Verificar la firma HMAC del webhook de MercadoPago.
     */
    public function verificarFirmaWebhook(string $payload, string $signatureHeader): bool
    {
        $secret    = $_ENV['MP_WEBHOOK_SECRET'] ?? '';
        // Si no hay secret configurado, se permite todo (entorno dev)
        if (empty($secret) || $secret === 'tu_webhook_secret') {
            return true;
        }
        $calculada = hash_hmac('sha256', $payload, $secret);
        return hash_equals($calculada, $signatureHeader);
    }
}
