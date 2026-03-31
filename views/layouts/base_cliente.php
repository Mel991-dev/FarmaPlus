<?php
/**
 * Layout principal para Clientes: sidebar_cliente + topbar + área de contenido.
 *
 * Variables esperadas:
 *   $titulo   string — Título de la página
 *   $contenido string — HTML del contenido (provisto por ob_start)
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'Mi Cuenta | FarmaPlus') ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= rtrim($_ENV['APP_BASEPATH'] ?? '', '/') ?>/assets/css/app.min.css?v=<?= time() ?>">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>
</head>
<body class="bg-fp-bg-main relative w-full h-full min-h-screen text-fp-text font-sans antialiased overflow-x-hidden">

<!-- SIDEBAR EXCLUSIVO PARA CLIENTES -->
<?php include __DIR__ . '/sidebar_cliente.php'; ?>

<!-- TOPBAR -->
<header class="fixed top-0 right-0 left-[240px] h-[64px] bg-white border-b border-fp-border z-40 flex items-center justify-between px-6 shadow-sm transition-all duration-300">
    
    <!-- Left side: Breadcrumb/Title -->
    <div class="flex items-center gap-4">
        <h2 class="font-bold text-fp-text text-lg tracking-tight"><?= htmlspecialchars($titulo ?? 'Mi Perfil') ?></h2>
    </div>

    <!-- Right side: User Badge -->
    <div class="flex items-center gap-3">
        <div class="hidden md:flex flex-col items-end">
            <span class="text-[13px] font-semibold text-fp-text leading-tight">
                <?= htmlspecialchars(($_SESSION['nombres'] ?? '') . ' ' . ($_SESSION['apellidos'] ?? '')) ?>
            </span>
            <span class="text-[11px] text-fp-muted font-medium mt-0.5">Cliente</span>
        </div>
        <div class="w-9 h-9 rounded-full bg-fp-secondary flex items-center justify-center text-white text-[13px] font-bold shadow-sm">
            <?= strtoupper(substr($_SESSION['nombres'] ?? 'C', 0, 1) . substr($_SESSION['apellidos'] ?? 'L', 0, 1)) ?>
        </div>
    </div>
</header>

<!-- CONTENIDO PRINCIPAL -->
<main class="ml-[240px] mt-[64px] p-6 lg:p-8 min-h-[calc(100vh-64px)] w-[calc(100%-240px)] transition-all duration-300">
    <?php if (!empty($contenido)) echo $contenido; ?>
</main>

<script>
    // Inicializar Iconos Lucide
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    });
</script>
</body>
</html>
