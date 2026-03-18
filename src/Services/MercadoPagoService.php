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
    public function __construct(private PDO $db)
    {
        MercadoPagoConfig::setAccessToken($_ENV['MP_ACCESS_TOKEN'] ?? '');
    }

    /**
     * Crear preferencia de pago en MercadoPago.
     * Retorna la URL de checkout para redirigir al cliente.
     *
     * @param array $items       Lista de [{title, quantity, unit_price}]
     * @param int   $pedidoId    ID del pedido en nuestra BD (para back_urls)
     * @param float $envio       Costo del domicilio
     * @return string            URL de checkout de MercadoPago
     */
    public function crearPreferencia(array $items, int $pedidoId, float $envio): string
    {
        // TODO: Implementar en Semana 4
        // 1. Construir array de items con formato MercadoPago
        // 2. Agregar el costo de envío como ítem separado si $envio > 0
        // 3. Configurar back_urls y auto_return
        // 4. Retornar $preference->init_point (producción) o sandbox_init_point (desarrollo)
        return '';
    }

    /**
     * Consultar el estado de un pago por su ID.
     * Usado por el WebhookController para validar el pago antes de confirmar el pedido.
     */
    public function obtenerEstadoPago(string $paymentId): array
    {
        // TODO: Implementar en Semana 4
        return [];
    }

    /**
     * Verificar la firma HMAC del webhook de MercadoPago.
     */
    public function verificarFirmaWebhook(string $payload, string $signatureHeader): bool
    {
        // TODO: Implementar en Semana 4
        $secret    = $_ENV['MP_WEBHOOK_SECRET'] ?? '';
        $calculada = hash_hmac('sha256', $payload, $secret);
        return hash_equals($calculada, $signatureHeader);
    }
}
