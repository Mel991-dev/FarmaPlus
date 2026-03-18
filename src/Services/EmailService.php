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
}
