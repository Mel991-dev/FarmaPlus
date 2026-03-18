<?php
/**
 * Layout de autenticación: login y recuperación de contraseña.
 * Sin sidebar. Diseño centrado con branding FarmaPlus.
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'Acceso') ?> | FarmaPlus CRM</title>
    <meta name="description" content="Acceso al sistema FarmaPlus CRM">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.min.css">
</head>
<body class="min-h-screen bg-fp-primary-dark flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <!-- Header branding -->
        <div class="text-center mb-8">
            <h1 class="text-white text-2xl font-bold tracking-tight">FarmaPlus CRM</h1>
            <p class="text-white/60 text-sm mt-1">Sistema de gestión farmacéutica</p>
        </div>

        <!-- Tarjeta de formulario -->
        <div class="bg-white rounded-fp shadow-xl p-8">
            <?php if (!empty($contenido)) echo $contenido; ?>
        </div>

        <p class="text-center text-white/30 text-xs mt-6">
            © <?= date('Y') ?> FarmaPlus Droguería · SENA ADS 2026
        </p>
    </div>
    <script src="/assets/js/app.js"></script>
</body>
</html>
