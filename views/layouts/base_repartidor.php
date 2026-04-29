<?php
/**
 * Layout principal para Repartidor: sidebar_repartidor + topbar + área de contenido.
 *
 * Variables esperadas:
 *   $titulo   string — Título de la página
 *   $contenido string — HTML del contenido (provisto por ob_start)
 */
$basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
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

<!-- Overlay Oscuro Móvil -->
<div id="sidebarOverlayRepartidor" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity opacity-0" onclick="toggleSidebarRepartidor()"></div>

<!-- SIDEBAR EXCLUSIVO PARA REPARTIDOR -->
<?php include __DIR__ . '/sidebar_repartidor.php'; ?>

<!-- TOPBAR -->
<header class="fixed top-0 right-0 left-0 lg:left-[240px] h-[64px] bg-white border-b border-fp-border z-30 flex items-center justify-between px-4 sm:px-6 shadow-sm transition-all duration-300">
    
    <!-- Left side: Breadcrumb/Title + Hamburger -->
    <div class="flex items-center gap-3">
        <button onclick="toggleSidebarRepartidor()" class="lg:hidden p-2 -ml-2 text-fp-text hover:bg-fp-bg-main rounded-md transition-colors">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
        <h2 class="font-bold text-fp-text text-[15px] sm:text-lg tracking-tight"><?= htmlspecialchars($titulo ?? 'Mi Perfil') ?></h2>
    </div>

    <!-- Right side: User Badge -->
    <div class="flex items-center gap-3">
        <div class="hidden md:flex flex-col items-end">
            <span class="text-[13px] font-semibold text-fp-text leading-tight">
                <?= htmlspecialchars(($_SESSION['nombres'] ?? '') . ' ' . ($_SESSION['apellidos'] ?? '')) ?>
            </span>
            <span class="text-[11px] text-fp-muted font-medium mt-0.5">Repartidor</span>
        </div>
        <div class="w-9 h-9 rounded-full bg-fp-secondary flex items-center justify-center text-white text-[13px] font-bold shadow-sm">
            <?= strtoupper(substr($_SESSION['nombres'] ?? 'R', 0, 1) . substr($_SESSION['apellidos'] ?? 'P', 0, 1)) ?>
        </div>
    </div>
</header>

<!-- CONTENIDO PRINCIPAL -->
<main class="ml-0 lg:ml-[240px] mt-[64px] p-4 sm:p-6 lg:p-8 h-[calc(100vh-64px)] w-full lg:w-[calc(100%-240px)] overflow-y-auto transition-all duration-300">
    <?php if (!empty($contenido)) echo $contenido; ?>
</main>

<script>
    // Inicializar Iconos Lucide
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Cerrar sidebar automáticamente al hacer clic en un link de navegación (móvil)
        const sidebar = document.getElementById('sidebarRepartidor');
        if (sidebar) {
            sidebar.querySelectorAll('a[href]').forEach(function(link) {
                link.addEventListener('click', function() {
                    if (window.innerWidth < 1024 && !sidebar.classList.contains('-translate-x-full')) {
                        closeSidebarRepartidor();
                    }
                });
            });
        }
    });

    // Cerrar sidebar repartidor
    function closeSidebarRepartidor() {
        const sidebar = document.getElementById('sidebarRepartidor');
        const overlay = document.getElementById('sidebarOverlayRepartidor');
        if (!sidebar) return;
        sidebar.classList.add('-translate-x-full');
        if (overlay) {
            overlay.classList.add('opacity-0');
            setTimeout(() => overlay.classList.add('hidden'), 300);
        }
    }

    // Toggle Sidebar Móvil Repartidor
    function toggleSidebarRepartidor() {
        const sidebar = document.getElementById('sidebarRepartidor');
        const overlay = document.getElementById('sidebarOverlayRepartidor');
        if (!sidebar) return;

        const isOpen = !sidebar.classList.contains('-translate-x-full');

        if (isOpen) {
            closeSidebarRepartidor();
        } else {
            sidebar.classList.remove('-translate-x-full');
            if (overlay) {
                overlay.classList.remove('hidden');
                setTimeout(() => overlay.classList.remove('opacity-0'), 10);
            }
        }
    }
</script>
</body>
</html>
