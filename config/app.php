<?php

declare(strict_types=1);

/**
 * config/app.php — Variables globales de la aplicación.
 */

return [
    'name'     => $_ENV['APP_NAME']    ?? 'FarmaPlus CRM',
    'url'      => $_ENV['APP_URL']     ?? 'http://localhost',
    'env'      => $_ENV['APP_ENV']     ?? 'production',
    'version'  => $_ENV['APP_VERSION'] ?? '1.0.0',
    'debug'    => (bool) ($_ENV['APP_DEBUG'] ?? false),
    'timezone' => 'America/Bogota',
    'locale'   => 'es_CO',
];
