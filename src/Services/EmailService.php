<?php

declare(strict_types=1);

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * EmailService — Wrapper de PHPMailer para FarmaPlus CRM.
 *
 * Todos los correos del sistema pasan por este servicio:
 * - Recuperación de contraseña (RF-1.4)
 * - Comprobante de venta POS (RF-5.5)
 * - Confirmación de compra en línea (RF-6.5)
 * - Notificaciones de cambio de estado de pedido (RF-7.4)
 */
class EmailService
{
    private PHPMailer $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->mailer->isSMTP();
        $this->mailer->Host       = $_ENV['MAIL_HOST']      ?? 'smtp.mailtrap.io';
        $this->mailer->SMTPAuth   = true;
        $this->mailer->Username   = $_ENV['MAIL_USER']      ?? '';
        $this->mailer->Password   = $_ENV['MAIL_PASS']      ?? '';
        $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mailer->Port       = (int) ($_ENV['MAIL_PORT'] ?? 2525);
        $this->mailer->CharSet    = 'UTF-8';
        $this->mailer->setFrom(
            $_ENV['MAIL_FROM']      ?? 'noreply@farmaplus.co',
            $_ENV['MAIL_FROM_NAME'] ?? 'FarmaPlus CRM'
        );
    }

    /**
     * Enviar correo de recuperación de contraseña.
     */
    public function enviarRecuperacion(string $destinatario, string $nombre, string $enlace): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($destinatario, $nombre);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Recuperación de contraseña — FarmaPlus CRM';
            $this->mailer->Body    = $this->plantillaRecuperacion($nombre, $enlace);
            $this->mailer->AltBody = "Hola {$nombre}, haz clic en este enlace para restablecer tu contraseña: {$enlace}";
            $this->mailer->send();
            return true;
        } catch (Exception) {
            return false;
        }
    }

    /**
     * Enviar comprobante de venta POS por correo.
     */
    public function enviarComprobantePOS(string $destinatario, string $nombre, string $htmlComprobante): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($destinatario, $nombre);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = 'Comprobante de compra — FarmaPlus Droguería';
            $this->mailer->Body    = $htmlComprobante;
            $this->mailer->send();
            return true;
        } catch (Exception) {
            return false;
        }
    }

    /**
     * Notificar cambio de estado de pedido al cliente.
     */
    public function notificarEstadoPedido(string $destinatario, string $nombre, string $numeroPedido, string $estado): bool
    {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($destinatario, $nombre);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = "Tu pedido #{$numeroPedido} — Estado actualizado";
            $this->mailer->Body    = "<p>Hola <strong>{$nombre}</strong>,</p><p>Tu pedido <strong>#{$numeroPedido}</strong> ahora está en estado: <strong>{$estado}</strong>.</p>";
            $this->mailer->send();
            return true;
        } catch (Exception) {
            return false;
        }
    }

    private function plantillaRecuperacion(string $nombre, string $enlace): string
    {
        return "
        <div style='font-family:Inter,sans-serif;max-width:560px;margin:0 auto;padding:24px;'>
            <h2 style='color:#1A6B8A;'>FarmaPlus CRM</h2>
            <p>Hola <strong>{$nombre}</strong>,</p>
            <p>Recibimos una solicitud para restablecer tu contraseña. El enlace expirará en <strong>30 minutos</strong>.</p>
            <a href='{$enlace}' style='display:inline-block;background:#1A6B8A;color:#fff;padding:12px 24px;border-radius:8px;text-decoration:none;font-weight:600;'>
                Restablecer contraseña
            </a>
            <p style='color:#7F8C8D;font-size:13px;margin-top:20px;'>Si no solicitaste esto, ignora este correo.</p>
        </div>";
    }

    /**
     * Enviar confirmación de compra en línea al cliente.
     *
     * @param string $destinatario  Email del cliente
     * @param string $nombre        Nombre del cliente
     * @param int    $pedidoId      ID del pedido
     * @param array  $items         Lista de items [{nombre, cantidad, precio_unitario}]
     * @param float  $subtotal      Subtotal sin envío
     * @param float  $costoEnvio    Costo de domicilio
     * @param float  $total         Total del pedido
     */
    public function enviarConfirmacionPedido(
        string $destinatario,
        string $nombre,
        int $pedidoId,
        array $items,
        float $subtotal,
        float $costoEnvio,
        float $total
    ): bool {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($destinatario, $nombre);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = "✅ Pedido #{$pedidoId} confirmado — FarmaPlus";
            $this->mailer->Body    = $this->plantillaConfirmacionPedido($nombre, $pedidoId, $items, $subtotal, $costoEnvio, $total);
            $this->mailer->AltBody = "Hola {$nombre}, tu pedido #{$pedidoId} ha sido confirmado. Total: $" . number_format($total, 0, ',', '.');
            $this->mailer->send();
            return true;
        } catch (Exception) {
            return false;
        }
    }

    /**
     * Notificar al repartidor cuando se le asigna un pedido.
     */
    public function notificarRepartidorAsignado(
        string $destinatario,
        string $nombreRepartidor,
        int $pedidoId,
        string $direccion,
        string $cliente
    ): bool {
        try {
            $this->mailer->clearAddresses();
            $this->mailer->addAddress($destinatario, $nombreRepartidor);
            $this->mailer->isHTML(true);
            $this->mailer->Subject = "📦 Nuevo pedido asignado #{$pedidoId} — FarmaPlus";
            $this->mailer->Body    = "
            <div style='font-family:Inter,sans-serif;max-width:560px;margin:0 auto;padding:24px;background:#f8fafc;border-radius:12px;'>
                <div style='background:#1A6B8A;padding:20px 24px;border-radius:8px 8px 0 0;'>
                    <h2 style='color:#fff;margin:0;'>FarmaPlus — Nuevo pedido</h2>
                </div>
                <div style='background:#fff;padding:24px;border-radius:0 0 8px 8px;border:1px solid #e2e8f0;'>
                    <p>Hola <strong>{$nombreRepartidor}</strong>,</p>
                    <p>Se te ha asignado el pedido <strong>#{$pedidoId}</strong>.</p>
                    <table style='width:100%;border-collapse:collapse;margin:16px 0;'>
                        <tr><td style='padding:8px;color:#64748b;'>Cliente:</td><td style='padding:8px;font-weight:600;'>{$cliente}</td></tr>
                        <tr><td style='padding:8px;color:#64748b;'>Dirección:</td><td style='padding:8px;font-weight:600;'>{$direccion}</td></tr>
                    </table>
                    <p style='color:#64748b;font-size:13px;'>Ingresa al panel de repartidor para ver el detalle completo.</p>
                </div>
            </div>";
            $this->mailer->send();
            return true;
        } catch (Exception) {
            return false;
        }
    }

    private function plantillaConfirmacionPedido(
        string $nombre,
        int $pedidoId,
        array $items,
        float $subtotal,
        float $costoEnvio,
        float $total
    ): string {
        $itemsHtml = '';
        foreach ($items as $item) {
            $precioUnit   = (float)($item['precio_unitario'] ?? 0);
            $cant         = (int)($item['cantidad'] ?? 1);
            $subtotalItem = $precioUnit * $cant;
            $itemsHtml .= "
            <tr>
                <td style='padding:10px 8px;border-bottom:1px solid #f1f5f9;'>" . htmlspecialchars($item['nombre'] ?? '') . "</td>
                <td style='padding:10px 8px;border-bottom:1px solid #f1f5f9;text-align:center;'>" . $cant . "</td>
                <td style='padding:10px 8px;border-bottom:1px solid #f1f5f9;text-align:right;'>$" . number_format($precioUnit, 0, ',', '.') . "</td>
                <td style='padding:10px 8px;border-bottom:1px solid #f1f5f9;text-align:right;font-weight:600;'>$" . number_format($subtotalItem, 0, ',', '.') . "</td>
            </tr>";
        }


        return "
        <div style='font-family:Inter,sans-serif;max-width:600px;margin:0 auto;padding:24px;background:#f8fafc;'>
            <div style='background:linear-gradient(135deg,#1A6B8A,#0f4c65);padding:28px 24px;border-radius:12px 12px 0 0;text-align:center;'>
                <div style='width:56px;height:56px;background:rgba(255,255,255,0.2);border-radius:50%;margin:0 auto 12px;display:flex;align-items:center;justify-content:center;'>
                    <span style='font-size:28px;'>✅</span>
                </div>
                <h1 style='color:#fff;margin:0;font-size:22px;'>¡Pedido confirmado!</h1>
                <p style='color:rgba(255,255,255,0.8);margin:8px 0 0;'>Número de pedido: <strong>#" . $pedidoId . "</strong></p>
            </div>
            <div style='background:#fff;padding:24px;border:1px solid #e2e8f0;'>
                <p>Hola <strong>{$nombre}</strong>,</p>
                <p>Tu pedido ha sido confirmado y ya está siendo preparado. Te notificaremos cuando esté en camino.</p>
                <h3 style='color:#1A6B8A;border-bottom:2px solid #f1f5f9;padding-bottom:8px;'>Resumen del pedido</h3>
                <table style='width:100%;border-collapse:collapse;'>
                    <thead>
                        <tr style='background:#f8fafc;'>
                            <th style='padding:10px 8px;text-align:left;color:#64748b;font-size:12px;text-transform:uppercase;'>Producto</th>
                            <th style='padding:10px 8px;text-align:center;color:#64748b;font-size:12px;text-transform:uppercase;'>Cant.</th>
                            <th style='padding:10px 8px;text-align:right;color:#64748b;font-size:12px;text-transform:uppercase;'>Precio unit.</th>
                            <th style='padding:10px 8px;text-align:right;color:#64748b;font-size:12px;text-transform:uppercase;'>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>{$itemsHtml}</tbody>
                </table>
                <div style='border-top:2px solid #f1f5f9;margin-top:16px;padding-top:16px;'>
                    <table style='width:100%;'>
                        <tr><td style='padding:4px;color:#64748b;'>Subtotal:</td><td style='padding:4px;text-align:right;'>$" . number_format($subtotal, 0, ',', '.') . "</td></tr>
                        <tr><td style='padding:4px;color:#64748b;'>Domicilio:</td><td style='padding:4px;text-align:right;'>$" . number_format($costoEnvio, 0, ',', '.') . "</td></tr>
                        <tr><td style='padding:4px;font-weight:700;font-size:16px;color:#1A6B8A;'>Total:</td><td style='padding:4px;text-align:right;font-weight:700;font-size:16px;color:#1A6B8A;'>$" . number_format($total, 0, ',', '.') . "</td></tr>
                    </table>
                </div>
            </div>
            <div style='background:#f8fafc;padding:16px 24px;border-radius:0 0 12px 12px;text-align:center;'>
                <p style='color:#64748b;font-size:13px;margin:0;'>¿Preguntas? Contáctanos en <a href='mailto:contacto@farmaplus.co' style='color:#1A6B8A;'>contacto@farmaplus.co</a></p>
            </div>
        </div>";
    }
}

