<?php
/**
 * Layout principal: sidebar + topbar + área de contenido.
 * Todas las vistas del panel administrativo heredan este layout.
 *
 * Variables esperadas:
 *   $titulo   string — Título de la página
 *   $contenido string — HTML del contenido (incluido desde la vista correspondiente)
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'FarmaPlus CRM') ?> | FarmaPlus CRM</title>
    <meta name="description" content="Sistema de gestión integral FarmaPlus Droguería">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/assets/css/app.min.css">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>
</head>
<body class="bg-fp-bg-main">

<!-- SIDEBAR -->
<nav class="sidebar flex flex-col">
    <div class="p-4 border-b border-white/10">
        <h1 class="text-white font-bold text-lg tracking-tight">FarmaPlus CRM</h1>
        <p class="text-white/50 text-xs mt-0.5">v1.0.0</p>
    </div>
    <div class="flex-1 py-4 overflow-y-auto">
        <!-- Menú dinámico según rol — TODO: Implementar en Semana 1 -->
        <a href="/dashboard" class="sidebar-item active">
            <i data-lucide="layout-dashboard" class="w-4 h-4"></i> Dashboard
        </a>
        <a href="/inventario/productos" class="sidebar-item">
            <i data-lucide="package" class="w-4 h-4"></i> Productos
        </a>
        <a href="/inventario/alertas" class="sidebar-item">
            <i data-lucide="alert-triangle" class="w-4 h-4"></i> Alertas
        </a>
        <a href="/ventas/pos" class="sidebar-item">
            <i data-lucide="shopping-cart" class="w-4 h-4"></i> Punto de Venta
        </a>
        <a href="/pedidos" class="sidebar-item">
            <i data-lucide="truck" class="w-4 h-4"></i> Pedidos
        </a>
        <a href="/gerente/reportes/ventas" class="sidebar-item">
            <i data-lucide="bar-chart-2" class="w-4 h-4"></i> Reportes
        </a>
        <a href="/admin/usuarios" class="sidebar-item">
            <i data-lucide="users" class="w-4 h-4"></i> Usuarios
        </a>
        <a href="/admin/configuracion" class="sidebar-item">
            <i data-lucide="settings" class="w-4 h-4"></i> Configuración
        </a>
    </div>
    <div class="p-4 border-t border-white/10">
        <form method="POST" action="/logout">
            <button type="submit" class="sidebar-item w-full text-red-300 hover:text-red-100 hover:bg-red-900/30">
                <i data-lucide="log-out" class="w-4 h-4"></i> Cerrar sesión
            </button>
        </form>
    </div>
</nav>

<!-- TOPBAR -->
<header class="topbar">
    <h2 class="font-semibold text-fp-text text-base"><?= htmlspecialchars($titulo ?? '') ?></h2>
    <div class="flex items-center gap-3">
        <span class="text-sm text-fp-muted">
            <?= htmlspecialchars(($_SESSION['nombres'] ?? '') . ' ' . ($_SESSION['apellidos'] ?? '')) ?>
        </span>
        <span class="badge badge-info capitalize"><?= htmlspecialchars($_SESSION['rol'] ?? '') ?></span>
    </div>
</header>

<!-- CONTENIDO PRINCIPAL -->
<main class="main-content p-8">
    <?php if (!empty($contenido)) echo $contenido; ?>
</main>

<script src="/assets/js/app.js"></script>
<script>if (window.lucide) lucide.createIcons();</script>
</body>
</html>
