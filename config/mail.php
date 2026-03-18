<?php

declare(strict_types=1);

/**
 * config/mail.php — Configuración SMTP para PHPMailer.
 * Ver EmailService.php para el uso.
 */

return [
    'host'      => $_ENV['MAIL_HOST']      ?? 'smtp.mailtrap.io',
    'port'      => (int) ($_ENV['MAIL_PORT'] ?? 2525),
    'user'      => $_ENV['MAIL_USER']      ?? '',
    'pass'      => $_ENV['MAIL_PASS']      ?? '',
    'from'      => $_ENV['MAIL_FROM']      ?? 'noreply@farmaplus.co',
    'from_name' => $_ENV['MAIL_FROM_NAME'] ?? 'FarmaPlus CRM',
    'encryption'=> 'tls', // STARTTLS en puerto 2525/587
];
