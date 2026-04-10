<?php
/**
 * Layout principal Administrativo: sidebar + topbar + área de contenido.
 * Implementa 100% responsividad (Mobile First) y arreglos de fuente Inter.
 */
$basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
$currentUri = $_SERVER['REQUEST_URI'] ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'Dashboard') ?> | FarmaPlus</title>
    <meta name="description" content="Sistema de gestión integral FarmaPlus Droguería">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <!-- Path Corregido Dinámico para que NO se pierda la compilación de estilos -->
    <link rel="stylesheet" href="<?= $basePath ?>/assets/css/app.min.css?v=<?= time() ?>">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>
</head>
<body class="bg-fp-bg-main relative w-full min-h-screen text-fp-text font-sans antialiased overflow-x-hidden">

<!-- Overlay Oscuro Móvil (se activa por JS) -->
<div id="sidebarOverlay" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 hidden lg:hidden transition-opacity opacity-0" onclick="toggleSidebar()"></div>

<!-- SIDEBAR (Menú Lateral Administrador) -->
<aside id="adminSidebar" class="bg-fp-primary-dark fixed top-0 left-0 h-screen flex flex-col z-50 w-[240px] transform -translate-x-full lg:translate-x-0 transition-transform duration-300 shadow-xl lg:shadow-none">
    
    <!-- Header Sidebar (Logo y Close) -->
    <div class="flex items-center justify-between px-4 pt-6 pb-5 border-b border-white/10 shrink-0">
        <div class="flex items-center gap-3">
            <div class="w-9 h-9 bg-fp-secondary rounded-lg flex items-center justify-center">
                <i data-lucide="pill" class="text-white w-5 h-5"></i>
            </div>
            <span class="text-[17px] font-bold text-white tracking-tight">Farma<span class="text-fp-secondary">Plus</span></span>
        </div>
        <!-- Botón Cerrar visible solo en móvil -->
        <button onclick="toggleSidebar()" class="lg:hidden text-white/70 hover:text-white p-1 rounded-md transition-colors">
            <i data-lucide="x" class="w-5 h-5"></i>
        </button>
    </div>

    <!-- Menú de Enlaces -->
    <nav class="flex-1 px-3 py-4 overflow-y-auto flex flex-col gap-1" style="scrollbar-width: none; -ms-overflow-style: none;">
        <style>
            #adminSidebar nav::-webkit-scrollbar { display: none; }
        </style>
        
        <span class="block text-[10px] font-semibold uppercase tracking-[1.5px] text-[#ecf0f159] px-2 mb-2 mt-2">Principal</span>
        <a href="<?= $basePath ?>/dashboard" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg cursor-pointer text-sm font-medium transition-colors <?= (strpos($currentUri, '/dashboard') !== false) ? 'bg-fp-secondary text-white shadow-sm' : 'text-white/85 hover:bg-fp-primary hover:text-white' ?>">
            <i data-lucide="layout-dashboard" class="w-[18px] h-[18px] shrink-0 opacity-85"></i> Dashboard
        </a>
        
        <div class="h-[1px] bg-white/5 mx-3 my-2"></div>
        <span class="block text-[10px] font-semibold uppercase tracking-[1.5px] text-[#ecf0f159] px-2 mb-2">Comercial</span>

        <a href="<?= $basePath ?>/ventas/pos" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg cursor-pointer text-sm font-medium transition-colors <?= (strpos($currentUri, '/ventas/pos') !== false) ? 'bg-fp-secondary text-white shadow-sm' : 'text-white/85 hover:bg-fp-primary hover:text-white' ?>">
            <i data-lucide="shopping-cart" class="w-[18px] h-[18px] shrink-0 opacity-85"></i> POS (Caja)
        </a>
        <a href="<?= $basePath ?>/pedidos" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg cursor-pointer text-sm font-medium transition-colors <?= (strpos($currentUri, '/pedidos') !== false) ? 'bg-fp-secondary text-white shadow-sm' : 'text-white/85 hover:bg-fp-primary hover:text-white' ?>">
            <i data-lucide="truck" class="w-[18px] h-[18px] shrink-0 opacity-85"></i> Pedidos
        </a>
        
        <div class="h-[1px] bg-white/5 mx-3 my-2"></div>
        <span class="block text-[10px] font-semibold uppercase tracking-[1.5px] text-[#ecf0f159] px-2 mb-2">Inventario</span>
        
        <a href="<?= $basePath ?>/inventario/productos" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg cursor-pointer text-sm font-medium transition-colors <?= (strpos($currentUri, '/inventario/productos') !== false) ? 'bg-fp-secondary text-white shadow-sm' : 'text-white/85 hover:bg-fp-primary hover:text-white' ?>">
            <i data-lucide="package" class="w-[18px] h-[18px] shrink-0 opacity-85"></i> Productos
        </a>
        <a href="<?= $basePath ?>/inventario/lotes" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg cursor-pointer text-sm font-medium transition-colors <?= (strpos($currentUri, '/inventario/lotes') !== false) ? 'bg-fp-secondary text-white shadow-sm' : 'text-white/85 hover:bg-fp-primary hover:text-white' ?>">
            <i data-lucide="layers" class="w-[18px] h-[18px] shrink-0 opacity-85"></i> Lotes
        </a>
        <a href="<?= $basePath ?>/inventario/alertas" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg cursor-pointer text-sm font-medium transition-colors <?= (strpos($currentUri, '/inventario/alertas') !== false) ? 'bg-fp-secondary text-white shadow-sm' : 'text-white/85 hover:bg-fp-primary hover:text-white' ?>">
            <i data-lucide="alert-triangle" class="w-[18px] h-[18px] shrink-0 opacity-85"></i> Alertas
        </a>
        <a href="<?= $basePath ?>/inventario/proveedores" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg cursor-pointer text-sm font-medium transition-colors <?= (strpos($currentUri, '/inventario/proveedores') !== false) ? 'bg-fp-secondary text-white shadow-sm' : 'text-white/85 hover:bg-fp-primary hover:text-white' ?>">
            <i data-lucide="building-2" class="w-[18px] h-[18px] shrink-0 opacity-85"></i> Proveedores
        </a>

        <div class="h-[1px] bg-white/5 mx-3 my-2"></div>
        <span class="block text-[10px] font-semibold uppercase tracking-[1.5px] text-[#ecf0f159] px-2 mb-2">Análisis</span>
        
        <a href="<?= $basePath ?>/gerente/reportes/ventas" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg cursor-pointer text-sm font-medium transition-colors <?= (strpos($currentUri, '/gerente/reportes') !== false) ? 'bg-fp-secondary text-white shadow-sm' : 'text-white/85 hover:bg-fp-primary hover:text-white' ?>">
            <i data-lucide="bar-chart-2" class="w-[18px] h-[18px] shrink-0 opacity-85"></i> Reportes
        </a>
        
        <div class="h-[1px] bg-white/5 mx-3 my-2"></div>
        <span class="block text-[10px] font-semibold uppercase tracking-[1.5px] text-[#ecf0f159] px-2 mb-2">Sistema</span>

        <a href="<?= $basePath ?>/admin/usuarios" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg cursor-pointer text-sm font-medium transition-colors <?= (strpos($currentUri, '/admin/usuarios') !== false) ? 'bg-fp-secondary text-white shadow-sm' : 'text-white/85 hover:bg-fp-primary hover:text-white' ?>">
            <i data-lucide="shield-check" class="w-[18px] h-[18px] shrink-0 opacity-85"></i> Usuarios
        </a>
        <a href="<?= $basePath ?>/admin/configuracion" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg cursor-pointer text-sm font-medium transition-colors <?= (strpos($currentUri, '/admin/config') !== false) ? 'bg-fp-secondary text-white shadow-sm' : 'text-white/85 hover:bg-fp-primary hover:text-white' ?>">
            <i data-lucide="settings" class="w-[18px] h-[18px] shrink-0 opacity-85"></i> Configuración
        </a>

    </nav>


    <!-- Footer Sidebar con Cerrar Sesión Minimalista -->
    <div class="px-3 py-4 border-t border-white/10 shrink-0">
        <div class="flex items-center gap-2.5">
            <div class="w-10 h-10 rounded-full bg-fp-secondary flex items-center justify-center text-white text-[13px] font-bold shadow-md shrink-0">
                <?= strtoupper(substr($_SESSION['nombres'] ?? 'U', 0, 1) . substr($_SESSION['apellidos'] ?? 'S', 0, 1)) ?>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-[13px] font-bold text-white truncate leading-tight">
                    <?= htmlspecialchars(explode(' ', trim($_SESSION['nombres'] ?? 'Usuario'))[0] . ' ' . explode(' ', trim($_SESSION['apellidos'] ?? ''))[0]) ?>
                </p>
                <p class="text-[11px] text-white/60 capitalize truncate">
                    <?= htmlspecialchars($_SESSION['rol'] ?? 'Staff') ?>
                </p>
            </div>
            <form method="POST" action="<?= $basePath ?>/logout" class="m-0 shrink-0">
                <button type="submit" class="p-2 text-white/60 hover:text-white hover:bg-white/10 rounded-md transition-colors" title="Cerrar sesión">
                    <i data-lucide="log-out" class="w-[18px] h-[18px]"></i>
                </button>
            </form>
        </div>
    </div>
</aside>

<!-- TOPBAR -->
<header class="fixed top-0 right-0 left-0 lg:left-[240px] h-[64px] bg-white border-b border-fp-border z-30 flex items-center justify-between px-4 sm:px-6 shadow-[0_2px_10px_-3px_rgba(0,0,0,0.05)] transition-all duration-300">
    
    <!-- Toggle Drawer (Mobile) + Title -->
    <div class="flex items-center gap-3 md:gap-4">
        <button onclick="toggleSidebar()" class="lg:hidden p-2 -ml-2 text-fp-text hover:bg-fp-bg-main rounded-md transition-colors">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
        
        <h2 class="font-bold text-fp-text text-[15px] sm:text-lg tracking-tight truncate max-w-[150px] sm:max-w-none">
            <?= htmlspecialchars($titulo ?? 'Panel') ?>
        </h2>
    </div>

    <!-- Right User Widget -->
    <div class="flex items-center gap-3">
        <div class="hidden sm:flex flex-col items-end mr-1">
            <span class="text-[13px] font-bold text-fp-text leading-tight">
                <?= htmlspecialchars(($_SESSION['nombres'] ?? '') . ' ' . ($_SESSION['apellidos'] ?? '')) ?>
            </span>
            <span class="text-[10px] text-fp-primary font-bold mt-0.5 uppercase tracking-wider bg-fp-primary/10 px-1.5 py-0.5 rounded">
                <?= htmlspecialchars($_SESSION['rol'] ?? 'Staff') ?>
            </span>
        </div>
        <div class="w-9 h-9 rounded-full bg-fp-secondary flex items-center justify-center text-white text-[13px] font-bold shadow-sm shrink-0">
            <?= strtoupper(substr($_SESSION['nombres'] ?? 'U', 0, 1) . substr($_SESSION['apellidos'] ?? 'S', 0, 1)) ?>
        </div>
    </div>
</header>

<!-- CONTENIDO PRINCIPAL (El Área Dinámica) -->
<!-- Se hizo overflow-y-auto propio para proteger el layout (Scroll Integrado) -->
<main class="ml-0 lg:ml-[240px] mt-[64px] p-4 sm:p-6 lg:p-8 h-[calc(100vh-64px)] w-full lg:w-[calc(100%-240px)] overflow-y-auto transition-all duration-300 bg-[#f8fafc]">
    <?php if (!empty($contenido)) echo $contenido; ?>
</main>

<script>
    // Iniciar íconos universales
    document.addEventListener("DOMContentLoaded", function() {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }

        // Cerrar sidebar automáticamente al hacer clic en un link de nav (móvil)
        const sidebar = document.getElementById('adminSidebar');
        if (sidebar) {
            sidebar.querySelectorAll('a[href]').forEach(function(link) {
                link.addEventListener('click', function() {
                    // Solo cerrar si el sidebar está abierto (visible) en móvil
                    if (window.innerWidth < 1024 && !sidebar.classList.contains('-translate-x-full')) {
                        closeSidebar();
                    }
                });
            });
        }
    });

    // Cerrar sidebar (con animación del overlay)
    function closeSidebar() {
        const sidebar  = document.getElementById('adminSidebar');
        const overlay  = document.getElementById('sidebarOverlay');
        if (!sidebar) return;
        sidebar.classList.add('-translate-x-full');
        if (overlay) {
            overlay.classList.add('opacity-0');
            setTimeout(() => overlay.classList.add('hidden'), 300);
        }
    }

    // Abrir/cerrar sidebar (toggle)
    function toggleSidebar() {
        const sidebar = document.getElementById('adminSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        if (!sidebar) return;

        const isOpen = !sidebar.classList.contains('-translate-x-full');

        if (isOpen) {
            // Cerrar
            closeSidebar();
        } else {
            // Abrir
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
