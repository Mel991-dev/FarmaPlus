<?php

declare(strict_types=1);

use Slim\App;
use App\Controllers\WebhookController;

return function (App $app): void {
    // Webhook de MercadoPago — confirmación de pago
    // NOTA: Esta ruta no lleva AuthMiddleware (es llamada por MercadoPago externamente)
    $app->post('/webhooks/mercadopago', [WebhookController::class, 'mercadopago']);
};
