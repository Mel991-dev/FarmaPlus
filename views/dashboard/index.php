<?php
/**
 * views/dashboard/index.php
 * Dashboard principal — fiel al mockup fp-07-dashboard.html
 * Muestra datos reales de sesión y alertas de inventario desde la BD.
 *
 * Variables inyectadas por DashboardController:
 *   $usuario      array — Datos del usuario en sesión
 *   $alertas      array — Alertas activas de inventario
 *   $ventas_hoy   array — Últimas 5 ventas del día
 *   $kpis         array — Totales: ventas_dia, pedidos_pendientes, alertas_total, clientes_total
 */
$usuario    = $usuario    ?? ['nombres' => 'Usuario', 'apellidos' => '', 'rol_nombre' => 'usuario'];
$alertas    = $alertas    ?? [];
$ventas_hoy = $ventas_hoy ?? [];
$kpis       = $kpis       ?? ['ventas_dia' => 0, 'pedidos_pendientes' => 0, 'alertas_total' => 0, 'clientes_total' => 0];

$iniciales = strtoupper(mb_substr($usuario['nombres'], 0, 1) . mb_substr($usuario['apellidos'], 0, 1));
$nombre    = htmlspecialchars($usuario['nombres']);
$rol       = htmlspecialchars(ucfirst($usuario['rol_nombre']));

function fmtCOP(float $v): string {
    return '$' . number_format($v, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard | FarmaPlus CRM</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    :root {
      --color-primary:#1A6B8A; --color-primary-dark:#1A3A4A; --color-primary-light:#4A9BB5;
      --color-secondary:#2A9D8F; --color-bg-main:#F4F9FC; --color-bg-card:#E9F5F8;
      --color-success:#27AE60; --color-error:#E74C3C; --color-warning:#F39C12; --color-info:#3498DB;
      --color-success-soft:#EAFAF1; --color-error-soft:#FDEDEC; --color-warning-soft:#FEF9E7; --color-info-soft:#EBF5FB;
      --color-text-primary:#2C3E50; --color-text-secondary:#7F8C8D; --color-border:#BDC3C7;
      --color-sidebar-bg:#1A3A4A; --color-sidebar-active:#2A9D8F; --color-sidebar-hover:#1A6B8A;
      --radius-sm:4px; --radius-md:8px; --radius-lg:12px; --radius-full:9999px;
      --font-main:'Inter',sans-serif; --font-mono:'JetBrains Mono',monospace;
      --sidebar-width:240px; --topbar-height:64px;
      --shadow-card:0 2px 8px rgba(0,0,0,0.08); --shadow-hover:0 8px 24px rgba(0,0,0,0.12);
    }
    *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
    html, body { height:100%; font-family:var(--font-main); font-size:16px; color:var(--color-text-primary); background:var(--color-bg-main); }
    .app-shell { display:flex; min-height:100vh; }

    /* SIDEBAR */
    .sidebar { width:var(--sidebar-width); background:var(--color-sidebar-bg); display:flex; flex-direction:column; position:fixed; top:0; left:0; height:100vh; z-index:100; transition:transform 0.3s ease; overflow:hidden; }
    .sidebar-logo { display:flex; align-items:center; gap:10px; padding:24px 16px 20px; border-bottom:1px solid rgba(255,255,255,0.1); }
    .sidebar-logo-icon { width:36px; height:36px; background:var(--color-secondary); border-radius:var(--radius-md); display:flex; align-items:center; justify-content:center; }
    .sidebar-logo-icon svg { color:#fff; width:20px; height:20px; }
    .sidebar-logo-text { font-size:17px; font-weight:700; color:#fff; letter-spacing:-0.3px; }
    .sidebar-logo-text span { color:var(--color-secondary); }
    .sidebar-nav { flex:1; padding:16px 12px; overflow-y:auto; scrollbar-width:none; }
    .sidebar-nav::-webkit-scrollbar { display:none; }
    .nav-section-label { font-size:10px; font-weight:600; text-transform:uppercase; letter-spacing:1.5px; color:rgba(236,240,241,0.35); padding:0 8px; margin:16px 0 8px; display:block; }
    .nav-section-label:first-child { margin-top:0; }
    .nav-item { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:var(--radius-md); cursor:pointer; text-decoration:none; color:rgba(236,240,241,0.8); font-size:14px; font-weight:500; transition:background 0.15s,color 0.15s; }
    .nav-item:hover  { background:var(--color-sidebar-hover); color:#fff; }
    .nav-item.active { background:var(--color-sidebar-active); color:#fff; }
    .nav-item svg    { width:18px; height:18px; flex-shrink:0; opacity:0.85; }
    .nav-item.active svg { opacity:1; }
    .nav-badge { margin-left:auto; background:rgba(255,255,255,0.15); color:rgba(255,255,255,0.85); font-size:11px; font-weight:600; padding:2px 7px; border-radius:var(--radius-full); }
    .sidebar-divider { height:1px; background:rgba(255,255,255,0.08); margin:8px 12px; }
    .sidebar-footer { padding:12px 12px 16px; border-top:1px solid rgba(255,255,255,0.1); }
    .sidebar-user { display:flex; align-items:center; gap:10px; padding:10px 12px; border-radius:var(--radius-md); cursor:pointer; transition:background 0.15s; }
    .sidebar-user:hover { background:rgba(255,255,255,0.06); }
    .sidebar-avatar { width:34px; height:34px; border-radius:50%; background:var(--color-primary); display:flex; align-items:center; justify-content:center; font-size:13px; font-weight:700; color:#fff; flex-shrink:0; }
    .sidebar-user-info { flex:1; min-width:0; }
    .sidebar-user-name { font-size:13px; font-weight:600; color:#fff; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; }
    .sidebar-user-role { font-size:11px; color:rgba(236,240,241,0.5); margin-top:1px; }
    .sidebar-logout { background:none; border:none; cursor:pointer; color:rgba(236,240,241,0.4); display:flex; align-items:center; justify-content:center; padding:4px; border-radius:var(--radius-sm); transition:color 0.2s; }
    .sidebar-logout:hover { color:var(--color-error); }
    .sidebar-logout svg { width:16px; height:16px; }

    /* TOPBAR */
    .topbar { height:var(--topbar-height); background:#fff; border-bottom:1px solid var(--color-border); box-shadow:0 1px 4px rgba(0,0,0,0.06); display:flex; align-items:center; padding:0 24px 0 calc(var(--sidebar-width) + 24px); position:fixed; top:0; left:0; right:0; z-index:50; gap:16px; }
    .topbar-toggle { display:none; background:none; border:none; cursor:pointer; color:var(--color-text-secondary); padding:6px; border-radius:var(--radius-md); }
    .topbar-toggle svg { width:22px; height:22px; }
    .breadcrumb { display:flex; align-items:center; gap:6px; flex:1; }
    .breadcrumb-item { font-size:13px; color:var(--color-text-secondary); text-decoration:none; transition:color 0.15s; display:flex; align-items:center; gap:4px; }
    .breadcrumb-item:hover { color:var(--color-primary); }
    .breadcrumb-item.current { font-weight:600; color:var(--color-text-primary); pointer-events:none; }
    .breadcrumb-sep { color:var(--color-border); }
    .topbar-actions { display:flex; align-items:center; gap:8px; }
    .topbar-icon-btn { width:36px; height:36px; border-radius:var(--radius-md); background:none; border:none; cursor:pointer; display:flex; align-items:center; justify-content:center; color:var(--color-text-secondary); transition:background 0.15s,color 0.15s; position:relative; }
    .topbar-icon-btn:hover { background:var(--color-bg-main); color:var(--color-primary); }
    .topbar-icon-btn svg { width:20px; height:20px; }
    .notif-dot { position:absolute; top:7px; right:7px; width:8px; height:8px; background:var(--color-error); border-radius:50%; border:2px solid #fff; }
    .topbar-user { display:flex; align-items:center; gap:8px; padding:4px 8px 4px 4px; border-radius:var(--radius-md); cursor:pointer; transition:background 0.15s; border:none; background:none; }
    .topbar-user:hover { background:var(--color-bg-main); }
    .topbar-avatar { width:32px; height:32px; border-radius:50%; background:var(--color-primary); display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; color:#fff; }
    .topbar-user-name { font-size:13px; font-weight:600; color:var(--color-text-primary); }
    .topbar-user-role { font-size:11px; color:var(--color-text-secondary); }

    /* MAIN */
    .main-content { margin-left:var(--sidebar-width); margin-top:var(--topbar-height); padding:28px 32px; flex:1; }

    /* GREETING */
    .greeting-row { display:flex; align-items:flex-start; justify-content:space-between; gap:16px; margin-bottom:28px; flex-wrap:wrap; }
    .greeting-left { display:flex; align-items:center; gap:14px; }
    .greeting-avatar { width:52px; height:52px; border-radius:50%; background:var(--color-primary); display:flex; align-items:center; justify-content:center; font-size:18px; font-weight:700; color:#fff; border:3px solid var(--color-bg-card); box-shadow:0 2px 10px rgba(26,107,138,0.2); flex-shrink:0; }
    .greeting-text h1 { font-size:22px; font-weight:700; letter-spacing:-0.3px; }
    .greeting-text p  { font-size:14px; color:var(--color-text-secondary); margin-top:2px; }
    .greeting-datetime { text-align:right; }
    .greeting-time { font-size:26px; font-weight:700; font-family:var(--font-mono); letter-spacing:1px; line-height:1; }
    .greeting-date { font-size:13px; color:var(--color-text-secondary); margin-top:2px; }

    /* ACCESOS RÁPIDOS */
    .quick-actions { display:grid; grid-template-columns:repeat(4,1fr); gap:12px; margin-bottom:28px; }
    .quick-action-btn { background:#fff; border:1px solid var(--color-border); border-radius:var(--radius-md); padding:14px 16px; display:flex; align-items:center; gap:10px; cursor:pointer; text-decoration:none; transition:border-color 0.15s,box-shadow 0.15s,background 0.15s; box-shadow:var(--shadow-card); }
    .quick-action-btn:hover { border-color:var(--color-primary); background:var(--color-bg-card); box-shadow:0 4px 16px rgba(26,107,138,0.12); }
    .qa-icon { width:36px; height:36px; border-radius:var(--radius-md); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .qa-icon svg { width:18px; height:18px; }
    .qa-icon.blue   { background:var(--color-info-soft); } .qa-icon.blue svg   { color:var(--color-primary); }
    .qa-icon.green  { background:var(--color-success-soft); } .qa-icon.green svg  { color:var(--color-success); }
    .qa-icon.amber  { background:var(--color-warning-soft); } .qa-icon.amber svg  { color:var(--color-warning); }
    .qa-icon.purple { background:#F3EEFF; } .qa-icon.purple svg { color:#7B61FF; }
    .qa-label { font-size:13px; font-weight:600; color:var(--color-text-primary); }
    .qa-sub   { font-size:11px; color:var(--color-text-secondary); margin-top:1px; }

    /* KPI CARDS */
    .kpi-row { display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:28px; }
    .kpi-card { background:#fff; border-radius:var(--radius-lg); padding:22px 24px; border-left:4px solid var(--color-primary); box-shadow:var(--shadow-card); cursor:pointer; transition:box-shadow 0.2s,transform 0.15s; text-decoration:none; display:block; }
    .kpi-card:hover { box-shadow:var(--shadow-hover); transform:translateY(-2px); }
    .kpi-card.warning { border-left-color:var(--color-warning); }
    .kpi-card.success { border-left-color:var(--color-success); }
    .kpi-card.info    { border-left-color:var(--color-info); }
    .kpi-card-top { display:flex; align-items:flex-start; justify-content:space-between; gap:12px; margin-bottom:16px; }
    .kpi-icon { width:46px; height:46px; border-radius:var(--radius-md); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .kpi-card         .kpi-icon { background:#EBF5FB; } .kpi-card         .kpi-icon svg { color:var(--color-primary); }
    .kpi-card.warning .kpi-icon { background:var(--color-warning-soft); } .kpi-card.warning .kpi-icon svg { color:var(--color-warning); }
    .kpi-card.success .kpi-icon { background:var(--color-success-soft); } .kpi-card.success .kpi-icon svg { color:var(--color-success); }
    .kpi-card.info    .kpi-icon { background:var(--color-info-soft); }    .kpi-card.info    .kpi-icon svg { color:var(--color-info); }
    .kpi-icon svg { width:22px; height:22px; }
    .kpi-arrow { width:28px; height:28px; border-radius:var(--radius-sm); background:var(--color-bg-main); display:flex; align-items:center; justify-content:center; color:var(--color-text-secondary); flex-shrink:0; transition:background 0.15s,color 0.15s; }
    .kpi-card:hover .kpi-arrow { background:var(--color-bg-card); color:var(--color-primary); }
    .kpi-arrow svg { width:14px; height:14px; }
    .kpi-value { font-size:36px; font-weight:700; letter-spacing:-1px; line-height:1; margin-bottom:4px; }
    .kpi-label { font-size:14px; font-weight:500; color:var(--color-text-secondary); margin-bottom:14px; }
    .kpi-divider { height:1px; background:#F4F9FC; margin-bottom:12px; }
    .kpi-trend { display:flex; align-items:center; gap:6px; font-size:12px; font-weight:600; }
    .kpi-trend-up   { color:var(--color-success); }
    .kpi-trend-down { color:var(--color-error); }
    .kpi-trend-icon { width:20px; height:20px; border-radius:50%; display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .kpi-trend-up   .kpi-trend-icon { background:var(--color-success-soft); }
    .kpi-trend-down .kpi-trend-icon { background:var(--color-error-soft); }
    .kpi-trend-icon svg { width:12px; height:12px; }
    .kpi-trend-up   .kpi-trend-icon svg { color:var(--color-success); }
    .kpi-trend-down .kpi-trend-icon svg { color:var(--color-error); }
    .kpi-trend-text { color:var(--color-text-secondary); font-weight:400; }

    /* BOTTOM GRID */
    .bottom-grid { display:grid; grid-template-columns:60% 1fr; gap:20px; }
    .section-card { background:#fff; border:1px solid var(--color-border); border-radius:var(--radius-lg); box-shadow:var(--shadow-card); overflow:hidden; }
    .section-header { padding:16px 20px; border-bottom:1px solid var(--color-border); display:flex; align-items:center; justify-content:space-between; gap:12px; background:var(--color-bg-main); }
    .section-title { font-size:15px; font-weight:600; color:var(--color-text-primary); display:flex; align-items:center; gap:8px; }
    .section-title svg { width:17px; height:17px; color:var(--color-primary); }
    .link-btn { display:inline-flex; align-items:center; gap:4px; font-size:12px; font-weight:600; color:var(--color-primary); background:none; border:none; cursor:pointer; padding:4px 8px; border-radius:var(--radius-sm); transition:background 0.15s; text-decoration:none; }
    .link-btn:hover { background:var(--color-bg-card); }
    .link-btn svg { width:13px; height:13px; }

    /* TABLA VENTAS */
    .interactions-table { width:100%; border-collapse:collapse; }
    .interactions-table thead th { background:var(--color-bg-main); padding:10px 16px; text-align:left; font-size:11px; font-weight:600; text-transform:uppercase; letter-spacing:0.8px; color:var(--color-text-secondary); border-bottom:1px solid var(--color-border); }
    .interactions-table thead th:first-child { padding-left:20px; }
    .interactions-table thead th:last-child  { padding-right:20px; }
    .interactions-table tbody tr { border-bottom:1px solid #F4F9FC; transition:background 0.12s; }
    .interactions-table tbody tr:last-child { border-bottom:none; }
    .interactions-table tbody tr:hover { background:var(--color-bg-card); }
    .interactions-table tbody td { padding:12px 16px; font-size:13px; vertical-align:middle; }
    .interactions-table tbody td:first-child { padding-left:20px; }
    .interactions-table tbody td:last-child  { padding-right:20px; }
    .client-cell { display:flex; align-items:center; gap:10px; }
    .client-mini-av { width:30px; height:30px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:11px; font-weight:700; color:#fff; flex-shrink:0; background:var(--color-primary); }
    .client-name { font-weight:600; font-size:13px; }
    .client-type { font-size:11px; color:var(--color-text-secondary); margin-top:1px; }
    .int-badge { display:inline-flex; align-items:center; gap:4px; border-radius:var(--radius-full); padding:3px 9px; font-size:11px; font-weight:600; white-space:nowrap; }
    .int-badge svg { width:11px; height:11px; }
    .int-presencial { background:var(--color-success-soft); color:#1D6E3A; }
    .int-online     { background:var(--color-info-soft); color:var(--color-primary); }
    .empty-state { text-align:center; padding:32px 20px; color:var(--color-text-secondary); font-size:14px; }

    /* ALERTAS STOCK */
    .stock-list { padding:8px 0; }
    .stock-item { padding:14px 20px; border-bottom:1px solid #F4F9FC; display:flex; flex-direction:column; gap:8px; transition:background 0.12s; }
    .stock-item:last-child { border-bottom:none; }
    .stock-item:hover { background:var(--color-bg-card); }
    .stock-item-top { display:flex; align-items:flex-start; justify-content:space-between; gap:8px; }
    .stock-product-name { font-size:13px; font-weight:700; line-height:1.3; }
    .stock-product-lab  { font-size:11px; color:var(--color-text-secondary); margin-top:2px; }
    .stock-badge-low { display:inline-flex; align-items:center; gap:4px; background:var(--color-warning-soft); border:1px solid rgba(243,156,18,0.3); color:#7D6608; font-size:11px; font-weight:600; padding:3px 8px; border-radius:var(--radius-full); white-space:nowrap; flex-shrink:0; }
    .stock-badge-low svg { width:11px; height:11px; }
    .stock-badge-vence { background:#FEF9E7; color:var(--color-warning); border-color:rgba(243,156,18,0.3); }
    .stock-numbers { display:flex; align-items:center; gap:8px; font-size:12px; }
    .stock-current { font-weight:700; color:var(--color-error); font-family:var(--font-mono); }
    .stock-sep { color:var(--color-border); }
    .stock-min { color:var(--color-text-secondary); font-family:var(--font-mono); }
    .stock-bar-wrap { height:4px; background:#F0F3F4; border-radius:var(--radius-full); overflow:hidden; }
    .stock-bar-fill { height:100%; border-radius:var(--radius-full); background:var(--color-error); transition:width 0.4s; }
    .stock-item-actions { display:flex; align-items:center; justify-content:space-between; }
    .stock-action-btn { font-size:12px; font-weight:600; color:var(--color-primary); background:none; border:none; cursor:pointer; display:inline-flex; align-items:center; gap:4px; padding:4px 8px; border-radius:var(--radius-sm); transition:background 0.15s; text-decoration:none; }
    .stock-action-btn:hover { background:var(--color-bg-card); }
    .stock-action-btn svg { width:12px; height:12px; }

    /* TOAST */
    .toast-container { position:fixed; top:80px; right:24px; z-index:999; display:flex; flex-direction:column; gap:8px; }
    .toast { display:flex; align-items:flex-start; gap:10px; background:#fff; border-radius:var(--radius-md); padding:12px 16px; box-shadow:0 4px 16px rgba(0,0,0,0.12); min-width:280px; max-width:360px; animation:slideIn 0.25s ease; border-left:4px solid var(--color-success); }
    .toast.warning { border-left-color:var(--color-warning); background:var(--color-warning-soft); }
    .toast.info    { border-left-color:var(--color-info);    background:var(--color-info-soft); }
    .toast.success { border-left-color:var(--color-success); background:var(--color-success-soft); }
    @keyframes slideIn { from { transform:translateX(120%); opacity:0; } to { transform:translateX(0); opacity:1; } }
    .toast svg { width:18px; height:18px; flex-shrink:0; margin-top:1px; }
    .toast.warning svg { color:var(--color-warning); } .toast.info svg { color:var(--color-info); } .toast.success svg { color:var(--color-success); }
    .toast-text { font-size:13px; line-height:1.5; }
    .toast-text strong { font-weight:600; display:block; }

    /* SIDEBAR OVERLAY MOBILE */
    .sidebar-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,0.45); z-index:99; }

    /* RESPONSIVE */
    @media (max-width:1023px) { .sidebar { transform:translateX(-100%); } .sidebar.open { transform:translateX(0); } .sidebar-overlay.visible { display:block; } .topbar { padding-left:16px; } .topbar-toggle { display:flex; } .main-content { margin-left:0; padding:20px; } .kpi-row { grid-template-columns:repeat(2,1fr); } .quick-actions { grid-template-columns:repeat(2,1fr); } .bottom-grid { grid-template-columns:1fr; } }
    @media (max-width:767px) { .main-content { padding:16px; } .kpi-row { grid-template-columns:1fr 1fr; gap:10px; } .kpi-value { font-size:26px; } .quick-actions { grid-template-columns:1fr 1fr; } .greeting-datetime { display:none; } }
  </style>
</head>
<body>

<div class="toast-container" id="toastContainer"></div>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<div class="app-shell">

  <!-- SIDEBAR -->
  <aside class="sidebar" id="sidebar" role="navigation" aria-label="Navegación principal">
    <div class="sidebar-logo">
      <div class="sidebar-logo-icon"><i data-lucide="pill"></i></div>
      <span class="sidebar-logo-text">Farma<span>Plus</span></span>
    </div>
    <nav class="sidebar-nav">
      <span class="nav-section-label">Principal</span>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/dashboard" class="nav-item active" aria-current="page">
        <i data-lucide="layout-dashboard"></i>Dashboard
      </a>
      <div class="sidebar-divider"></div>
      <span class="nav-section-label">Comercial</span>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/ventas/pos" class="nav-item"><i data-lucide="receipt"></i>Ventas POS</a>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/pedidos" class="nav-item"><i data-lucide="shopping-bag"></i>Pedidos</a>
      <div class="sidebar-divider"></div>
      <span class="nav-section-label">Inventario</span>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/productos" class="nav-item"><i data-lucide="pill"></i>Productos</a>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/lotes" class="nav-item"><i data-lucide="layers"></i>Lotes</a>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/alertas" class="nav-item">
        <i data-lucide="alert-triangle"></i>Alertas
        <?php if (($kpis['alertas_total'] ?? 0) > 0): ?>
        <span class="nav-badge" style="background:rgba(231,76,60,0.25);color:#E74C3C;"><?= $kpis['alertas_total'] ?></span>
        <?php endif; ?>
      </a>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/proveedores" class="nav-item"><i data-lucide="building-2"></i>Proveedores</a>
      <div class="sidebar-divider"></div>
      <span class="nav-section-label">Análisis</span>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/gerente/reportes/ventas" class="nav-item"><i data-lucide="bar-chart-2"></i>Reportes</a>
      <div class="sidebar-divider"></div>
      <span class="nav-section-label">Sistema</span>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/admin/usuarios" class="nav-item"><i data-lucide="shield-check"></i>Usuarios</a>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/admin/configuracion" class="nav-item"><i data-lucide="settings"></i>Configuración</a>
    </nav>
    <div class="sidebar-footer">
      <div class="sidebar-user">
        <div class="sidebar-avatar"><?= htmlspecialchars($iniciales) ?></div>
        <div class="sidebar-user-info">
          <div class="sidebar-user-name"><?= $nombre ?> <?= htmlspecialchars($usuario['apellidos'] ?? '') ?></div>
          <div class="sidebar-user-role"><?= $rol ?></div>
        </div>
        <form method="POST" action="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/logout">
          <button type="submit" class="sidebar-logout" aria-label="Cerrar sesión">
            <i data-lucide="log-out"></i>
          </button>
        </form>
      </div>
    </div>
  </aside>

  <!-- TOPBAR -->
  <header class="topbar" role="banner">
    <button class="topbar-toggle" onclick="openSidebar()" aria-label="Abrir menú">
      <i data-lucide="menu"></i>
    </button>
    <nav class="breadcrumb" aria-label="Ruta de navegación">
      <a href="#" class="breadcrumb-item"><i data-lucide="home" style="width:13px;height:13px;"></i> Inicio</a>
      <span class="breadcrumb-sep">/</span>
      <span class="breadcrumb-item current">Dashboard</span>
    </nav>
    <div class="topbar-actions">
      <button class="topbar-icon-btn" aria-label="Notificaciones">
        <i data-lucide="bell"></i>
        <?php if (($kpis['alertas_total'] ?? 0) > 0): ?>
        <span class="notif-dot"></span>
        <?php endif; ?>
      </button>
      <button class="topbar-user">
        <div class="topbar-avatar"><?= htmlspecialchars($iniciales) ?></div>
        <div>
          <div class="topbar-user-name"><?= $nombre ?></div>
          <div class="topbar-user-role"><?= $rol ?></div>
        </div>
      </button>
    </div>
  </header>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="main-content" role="main">

    <!-- SALUDO DINÁMICO -->
    <div class="greeting-row">
      <div class="greeting-left">
        <div class="greeting-avatar"><?= htmlspecialchars($iniciales) ?></div>
        <div class="greeting-text">
          <h1 id="greeting">Bienvenido, <?= $nombre ?></h1>
          <p id="greetingDate">Cargando fecha…</p>
        </div>
      </div>
      <div class="greeting-datetime">
        <div class="greeting-time" id="greetingTime">00:00</div>
        <div class="greeting-date" id="greetingDateSub">—</div>
      </div>
    </div>

    <!-- ACCESOS RÁPIDOS -->
    <div class="quick-actions" role="region" aria-label="Accesos rápidos">
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/ventas/pos" class="quick-action-btn">
        <div class="qa-icon blue"><i data-lucide="scan-line"></i></div>
        <div><div class="qa-label">Nueva venta</div><div class="qa-sub">Punto de venta POS</div></div>
      </a>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/lotes/crear" class="quick-action-btn">
        <div class="qa-icon purple"><i data-lucide="layers"></i></div>
        <div><div class="qa-label">Registrar lote</div><div class="qa-sub">Entrada de mercancía</div></div>
      </a>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/productos/crear" class="quick-action-btn">
        <div class="qa-icon amber"><i data-lucide="package-plus"></i></div>
        <div><div class="qa-label">Nuevo producto</div><div class="qa-sub">Agregar al catálogo</div></div>
      </a>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/admin/usuarios" class="quick-action-btn">
        <div class="qa-icon green"><i data-lucide="user-plus"></i></div>
        <div><div class="qa-label">Nuevo usuario</div><div class="qa-sub">Gestionar personal</div></div>
      </a>
    </div>

    <!-- KPI CARDS -->
    <div class="kpi-row" role="region" aria-label="Indicadores clave">
      <!-- Ventas del día -->
      <a href="/ventas" class="kpi-card success">
        <div class="kpi-card-top">
          <div class="kpi-icon"><i data-lucide="receipt"></i></div>
          <div class="kpi-arrow"><i data-lucide="arrow-right"></i></div>
        </div>
        <div class="kpi-value"><?= fmtCOP((float) ($kpis['ventas_dia'] ?? 0)) ?></div>
        <div class="kpi-label">Ventas del día</div>
        <div class="kpi-divider"></div>
        <div class="kpi-trend kpi-trend-up">
          <div class="kpi-trend-icon"><i data-lucide="trending-up"></i></div>
          <span><?= count($ventas_hoy) ?> transacciones</span>
          <span class="kpi-trend-text">hoy</span>
        </div>
      </a>
      <!-- Pedidos pendientes -->
      <a href="/pedidos" class="kpi-card info">
        <div class="kpi-card-top">
          <div class="kpi-icon"><i data-lucide="shopping-bag"></i></div>
          <div class="kpi-arrow"><i data-lucide="arrow-right"></i></div>
        </div>
        <div class="kpi-value"><?= (int) ($kpis['pedidos_pendientes'] ?? 0) ?></div>
        <div class="kpi-label">Pedidos pendientes</div>
        <div class="kpi-divider"></div>
        <div class="kpi-trend kpi-trend-up">
          <div class="kpi-trend-icon"><i data-lucide="truck"></i></div>
          <span>En proceso</span>
          <span class="kpi-trend-text">de atención</span>
        </div>
      </a>
      <!-- Alertas inventario -->
      <a href="/inventario/alertas" class="kpi-card warning">
        <div class="kpi-card-top">
          <div class="kpi-icon"><i data-lucide="alert-triangle"></i></div>
          <div class="kpi-arrow"><i data-lucide="arrow-right"></i></div>
        </div>
        <div class="kpi-value" style="color:var(--color-warning);"><?= (int) ($kpis['alertas_total'] ?? 0) ?></div>
        <div class="kpi-label">Alertas de inventario</div>
        <div class="kpi-divider"></div>
        <div class="kpi-trend kpi-trend-down">
          <div class="kpi-trend-icon"><i data-lucide="trending-up"></i></div>
          <span>Requieren</span>
          <span class="kpi-trend-text">atención</span>
        </div>
      </a>
      <!-- Clientes -->
      <a href="#" class="kpi-card">
        <div class="kpi-card-top">
          <div class="kpi-icon"><i data-lucide="users"></i></div>
          <div class="kpi-arrow"><i data-lucide="arrow-right"></i></div>
        </div>
        <div class="kpi-value"><?= (int) ($kpis['clientes_total'] ?? 0) ?></div>
        <div class="kpi-label">Clientes registrados</div>
        <div class="kpi-divider"></div>
        <div class="kpi-trend kpi-trend-up">
          <div class="kpi-trend-icon"><i data-lucide="trending-up"></i></div>
          <span>Total</span>
          <span class="kpi-trend-text">en el sistema</span>
        </div>
      </a>
    </div>

    <!-- SECCIÓN INFERIOR -->
    <div class="bottom-grid">
      <!-- Últimas ventas -->
      <div class="section-card">
        <div class="section-header">
          <div class="section-title"><i data-lucide="receipt"></i>Últimas ventas del día</div>
          <a href="/ventas" class="link-btn">Ver todas <i data-lucide="arrow-right"></i></a>
        </div>
        <?php if (empty($ventas_hoy)): ?>
          <div class="empty-state">
            <i data-lucide="shopping-cart" style="width:32px;height:32px;margin-bottom:8px;color:var(--color-border);display:block;margin:0 auto 8px;"></i>
            Sin ventas registradas hoy.
          </div>
        <?php else: ?>
        <table class="interactions-table" aria-label="Últimas ventas registradas">
          <thead><tr><th>Comprobante</th><th>Canal</th><th>Total</th><th>Vendedor</th></tr></thead>
          <tbody>
          <?php foreach ($ventas_hoy as $v): ?>
            <tr>
              <td><span style="font-family:var(--font-mono);font-size:12px;color:var(--color-primary);"><?= htmlspecialchars($v['numero_comprobante']) ?></span></td>
              <td><span class="int-badge int-presencial"><i data-lucide="store"></i>Presencial</span></td>
              <td><strong style="color:var(--color-success);"><?= fmtCOP((float)$v['total']) ?></strong></td>
              <td style="font-size:12px;color:var(--color-text-secondary);"><?= htmlspecialchars($v['vendedor_nombre'] ?? '—') ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <?php endif; ?>
      </div>

      <!-- Alertas de inventario -->
      <div class="section-card">
        <div class="section-header">
          <div class="section-title"><i data-lucide="alert-triangle"></i>Alertas de inventario</div>
          <a href="/inventario/alertas" class="link-btn">Ver todas <i data-lucide="arrow-right"></i></a>
        </div>
        <?php if (empty($alertas)): ?>
          <div class="empty-state">
            <i data-lucide="check-circle" style="width:32px;height:32px;color:var(--color-success);display:block;margin:0 auto 8px;"></i>
            Sin alertas activas. ¡Todo bajo control!
          </div>
        <?php else: ?>
        <div class="stock-list">
        <?php foreach (array_slice($alertas, 0, 4) as $alerta): ?>
          <div class="stock-item">
            <div class="stock-item-top">
              <div>
                <div class="stock-product-name"><?= htmlspecialchars($alerta['producto_nombre'] ?? '—') ?></div>
                <div class="stock-product-lab"><?= htmlspecialchars($alerta['mensaje'] ?? '') ?></div>
              </div>
              <?php if ($alerta['tipo'] === 'stock_minimo'): ?>
              <span class="stock-badge-low"><i data-lucide="alert-triangle"></i>Stock mínimo</span>
              <?php else: ?>
              <span class="stock-badge-low stock-badge-vence"><i data-lucide="clock"></i>Vence pronto</span>
              <?php endif; ?>
            </div>
            <div class="stock-item-actions">
              <span style="font-size:11px;color:var(--color-text-secondary);"><?= htmlspecialchars($alerta['tipo'] === 'stock_minimo' ? 'Stock insuficiente' : 'Lote: ' . ($alerta['numero_lote'] ?? '—')) ?></span>
              <a href="/inventario/alertas" class="stock-action-btn">Ver detalle <i data-lucide="arrow-right"></i></a>
            </div>
          </div>
        <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>

  </main>
</div>

<script>
  lucide.createIcons();

  /* Saludo dinámico y reloj */
  function updateDateTime() {
    const now  = new Date();
    const h    = now.getHours();
    const dias  = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
    const meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    const saludo = h < 12 ? 'Buenos días' : h < 18 ? 'Buenas tardes' : 'Buenas noches';
    const emoji  = h < 12 ? '☀️' : h < 18 ? '🌤️' : '🌙';
    document.getElementById('greeting').textContent = `${saludo}, <?= $nombre ?> ${emoji}`;
    document.getElementById('greetingDate').textContent = `${dias[now.getDay()]}, ${now.getDate()} de ${meses[now.getMonth()]} de ${now.getFullYear()}`;
    document.getElementById('greetingTime').textContent = `${String(h).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
    document.getElementById('greetingDateSub').textContent = `${dias[now.getDay()]}, ${now.getDate()} ${meses[now.getMonth()].slice(0,3)}. ${now.getFullYear()}`;
  }
  updateDateTime();
  setInterval(updateDateTime, 1000);

  /* Toast */
  function showToast(msg, type = 'success') {
    const container = document.getElementById('toastContainer');
    const icons = { success:'circle-check', warning:'alert-triangle', info:'info', error:'circle-x' };
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `<i data-lucide="${icons[type]}"></i><div class="toast-text"><strong>${msg}</strong></div>`;
    container.appendChild(toast);
    lucide.createIcons();
    setTimeout(() => { toast.style.transition='opacity 0.3s,transform 0.3s'; toast.style.opacity='0'; toast.style.transform='translateX(120%)'; setTimeout(()=>toast.remove(),300); }, 4000);
  }

  /* Sidebar mobile */
  function openSidebar()  { document.getElementById('sidebar').classList.add('open'); document.getElementById('sidebarOverlay').classList.add('visible'); }
  function closeSidebar() { document.getElementById('sidebar').classList.remove('open'); document.getElementById('sidebarOverlay').classList.remove('visible'); }

  /* Flash message desde sesión PHP */
  <?php if (!empty($_SESSION['flash_msg'])): ?>
  setTimeout(() => showToast('<?= addslashes($_SESSION['flash_msg']) ?>', '<?= addslashes($_SESSION['flash_tipo'] ?? 'success') ?>'), 500);
  <?php unset($_SESSION['flash_msg'], $_SESSION['flash_tipo']); ?>
  <?php endif; ?>

  /* Alertas de inventario al cargar */
  <?php if (($kpis['alertas_total'] ?? 0) > 0): ?>
  setTimeout(() => showToast('<?= (int)$kpis['alertas_total'] ?> alertas de inventario requieren atención', 'warning'), 1200);
  <?php endif; ?>
</script>
</body>
</html>
