<?php

declare(strict_types=1);

/**
 * config/database.php — Configuración de la base de datos.
 * Las credenciales se cargan desde las variables de entorno (.env).
 *
 * Este archivo NO contiene credenciales en texto plano.
 * Ver: .env.example para la lista de variables requeridas.
 */

return [
    'host'    => $_ENV['DB_HOST']  ?? '127.0.0.1',
    'port'    => $_ENV['DB_PORT']  ?? '3306',
    'dbname'  => $_ENV['DB_NAME']  ?? 'farmaplus_db',
    'user'    => $_ENV['DB_USER']  ?? 'root',
    'pass'    => $_ENV['DB_PASS']  ?? '',
    'charset' => 'utf8mb4',
];
