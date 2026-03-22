<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Productos | FarmaPlus CRM</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    :root{--color-primary:#1A6B8A;--color-primary-dark:#1A3A4A;--color-primary-light:#4A9BB5;--color-secondary:#2A9D8F;--color-bg-main:#F4F9FC;--color-bg-card:#E9F5F8;--color-success:#27AE60;--color-error:#E74C3C;--color-warning:#F39C12;--color-info:#3498DB;--color-success-soft:#EAFAF1;--color-error-soft:#FDEDEC;--color-warning-soft:#FEF9E7;--color-info-soft:#EBF5FB;--color-text-primary:#2C3E50;--color-text-secondary:#7F8C8D;--color-border:#BDC3C7;--color-sidebar-bg:#1A3A4A;--color-sidebar-text:#ECF0F1;--color-sidebar-active:#2A9D8F;--color-sidebar-hover:#1A6B8A;--radius-sm:4px;--radius-md:8px;--radius-lg:12px;--radius-full:9999px;--font-main:'Inter',sans-serif;--font-mono:'JetBrains Mono',monospace;--sidebar-width:240px;--topbar-height:64px;--shadow-card:0 2px 8px rgba(0,0,0,0.08);--shadow-focus:0 0 0 3px rgba(26,107,138,0.15);}
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    html,body{height:100%;font-family:var(--font-main);font-size:16px;color:var(--color-text-primary);background:var(--color-bg-main);overflow-x:hidden;}
    .app-shell{display:flex;min-height:100vh;overflow-x:hidden;}
    .sidebar{width:var(--sidebar-width);background:var(--color-sidebar-bg);display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;z-index:100;transition:transform 0.3s ease;overflow:hidden;}
    .sidebar-logo{display:flex;align-items:center;gap:10px;padding:24px 16px 20px;border-bottom:1px solid rgba(255,255,255,0.1);}
    .sidebar-logo-icon{width:36px;height:36px;background:var(--color-secondary);border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;}
    .sidebar-logo-icon svg{color:#fff;width:20px;height:20px;}
    .sidebar-logo-text{font-size:17px;font-weight:700;color:#fff;letter-spacing:-0.3px;}
    .sidebar-logo-text span{color:var(--color-secondary);}
    .sidebar-nav{flex:1;padding:16px 12px;overflow-y:auto;scrollbar-width:none;}
    .sidebar-nav::-webkit-scrollbar{display:none;}
    .nav-section-label{font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:1.5px;color:rgba(236,240,241,0.35);padding:0 8px;margin:16px 0 8px;}
    .nav-section-label:first-child{margin-top:0;}
    .nav-item{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:var(--radius-md);cursor:pointer;text-decoration:none;color:var(--color-sidebar-text);font-size:14px;font-weight:500;transition:background 0.15s,color 0.15s;}
    .nav-item:hover{background:var(--color-sidebar-hover);}
    .nav-item.active{background:var(--color-sidebar-active);color:#fff;}
    .nav-item svg{width:18px;height:18px;flex-shrink:0;opacity:0.85;}
    .nav-item.active svg{opacity:1;}
    .nav-badge{margin-left:auto;background:rgba(255,255,255,0.15);color:rgba(255,255,255,0.85);font-size:11px;font-weight:600;padding:2px 7px;border-radius:var(--radius-full);}
    .sidebar-divider{height:1px;background:rgba(255,255,255,0.08);margin:8px 12px;}
    .sidebar-footer{padding:12px 12px 16px;border-top:1px solid rgba(255,255,255,0.1);}
    .sidebar-user{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:var(--radius-md);cursor:pointer;transition:background 0.15s;}
    .sidebar-user:hover{background:rgba(255,255,255,0.06);}
    .sidebar-avatar{width:34px;height:34px;border-radius:50%;background:var(--color-primary);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0;}
    .sidebar-user-info{flex:1;min-width:0;}
    .sidebar-user-name{font-size:13px;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}
    .sidebar-user-role{font-size:11px;color:rgba(236,240,241,0.5);margin-top:1px;}
    .sidebar-logout{background:none;border:none;cursor:pointer;color:rgba(236,240,241,0.4);display:flex;align-items:center;justify-content:center;padding:4px;border-radius:var(--radius-sm);transition:color 0.2s;}
    .sidebar-logout:hover{color:var(--color-error);}
    .sidebar-logout svg{width:16px;height:16px;}
    .topbar{height:var(--topbar-height);background:#fff;border-bottom:1px solid var(--color-border);box-shadow:0 1px 4px rgba(0,0,0,0.06);display:flex;align-items:center;padding:0 24px 0 calc(var(--sidebar-width) + 24px);position:fixed;top:0;left:0;right:0;z-index:50;gap:16px;}
    .topbar-toggle{display:none;background:none;border:none;cursor:pointer;color:var(--color-text-secondary);padding:6px;border-radius:var(--radius-md);}
    .topbar-toggle svg{width:22px;height:22px;}
    .breadcrumb{display:flex;align-items:center;gap:6px;flex:1;}
    .breadcrumb-item{font-size:13px;color:var(--color-text-secondary);text-decoration:none;transition:color 0.15s;display:flex;align-items:center;gap:4px;}
    .breadcrumb-item:hover{color:var(--color-primary);}
    .breadcrumb-item.current{font-weight:600;color:var(--color-text-primary);pointer-events:none;}
    .breadcrumb-sep{color:var(--color-border);font-size:13px;}
    .topbar-actions{display:flex;align-items:center;gap:8px;}
    .topbar-icon-btn{width:36px;height:36px;border-radius:var(--radius-md);background:none;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--color-text-secondary);transition:background 0.15s,color 0.15s;position:relative;}
    .topbar-icon-btn:hover{background:var(--color-bg-main);color:var(--color-primary);}
    .topbar-icon-btn svg{width:20px;height:20px;}
    .notif-dot{position:absolute;top:7px;right:7px;width:8px;height:8px;background:var(--color-error);border-radius:50%;border:2px solid #fff;}
    .topbar-user{display:flex;align-items:center;gap:8px;padding:4px 8px 4px 4px;border-radius:var(--radius-md);cursor:pointer;transition:background 0.15s;border:none;background:none;}
    .topbar-user:hover{background:var(--color-bg-main);}
    .topbar-avatar{width:32px;height:32px;border-radius:50%;background:var(--color-primary);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;}
    .topbar-user-name{font-size:13px;font-weight:600;color:var(--color-text-primary);}
    .topbar-user-role{font-size:11px;color:var(--color-text-secondary);}
    .main-content{margin-left:var(--sidebar-width);margin-top:var(--topbar-height);padding:28px 32px;flex:1;min-width:0;max-width:calc(100vw - var(--sidebar-width));overflow-x:hidden;}
    .page-header{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap;}
    .page-header-left h1{font-size:22px;font-weight:700;color:var(--color-text-primary);letter-spacing:-0.4px;}
    .page-header-left p{font-size:14px;color:var(--color-text-secondary);margin-top:4px;}
    .btn-primary{height:48px;background:var(--color-primary);color:#fff;border:none;border-radius:var(--radius-md);font-family:var(--font-main);font-size:15px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;padding:0 20px;transition:background 0.2s,box-shadow 0.2s,transform 0.1s;white-space:nowrap;}
    .btn-primary:hover{background:var(--color-primary-light);box-shadow:0 4px 16px rgba(26,107,138,0.28);}
    .btn-primary:active{transform:scale(0.99);}
    .btn-primary svg{width:18px;height:18px;}
    .btn-secondary{height:40px;background:transparent;border:1.5px solid var(--color-border);border-radius:var(--radius-md);font-family:var(--font-main);font-size:14px;font-weight:500;color:var(--color-text-primary);cursor:pointer;display:inline-flex;align-items:center;gap:7px;padding:0 14px;transition:border-color 0.2s,background 0.2s,color 0.2s;white-space:nowrap;}
    .btn-secondary:hover{border-color:var(--color-primary);background:var(--color-bg-card);color:var(--color-primary);}
    .btn-secondary svg{width:15px;height:15px;}
    .btn-sm{height:34px;font-size:13px;padding:0 14px;}
    .stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;width:100%;}
    .stat-card{background:#fff;border-radius:var(--radius-lg);padding:18px 20px;border-left:4px solid var(--color-primary);box-shadow:var(--shadow-card);display:flex;align-items:center;gap:14px;}
    .stat-card.warning{border-left-color:var(--color-warning);}
    .stat-card.success{border-left-color:var(--color-success);}
    .stat-card.info{border-left-color:var(--color-info);}
    .stat-icon{width:42px;height:42px;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
    .stat-card .stat-icon{background:#EBF5FB;}
    .stat-card.warning .stat-icon{background:var(--color-warning-soft);}
    .stat-card.success .stat-icon{background:var(--color-success-soft);}
    .stat-card.info .stat-icon{background:var(--color-info-soft);}
    .stat-icon svg{width:20px;height:20px;}
    .stat-card .stat-icon svg{color:var(--color-primary);}
    .stat-card.warning .stat-icon svg{color:var(--color-warning);}
    .stat-card.success .stat-icon svg{color:var(--color-success);}
    .stat-card.info .stat-icon svg{color:var(--color-info);}
    .stat-value{font-size:26px;font-weight:700;color:var(--color-text-primary);line-height:1;}
    .stat-label{font-size:12px;font-weight:500;color:var(--color-text-secondary);margin-top:4px;}
    .alert-banner{display:flex;align-items:flex-start;gap:12px;background:var(--color-warning-soft);border:1px solid rgba(243,156,18,0.3);border-left:4px solid var(--color-warning);border-radius:var(--radius-md);padding:12px 16px;margin-bottom:20px;font-size:13px;color:#7D6608;flex-wrap:wrap;width:100%;}
    .alert-banner svg{width:18px;height:18px;color:var(--color-warning);flex-shrink:0;margin-top:1px;}
    .alert-banner>div{flex:1;min-width:0;}
    .alert-banner strong{font-weight:700;}
    .alert-banner-actions{margin-left:auto;display:flex;gap:8px;}
    .alert-link{font-size:12px;font-weight:600;color:var(--color-warning);background:none;border:none;cursor:pointer;padding:4px 10px;border-radius:var(--radius-sm);transition:background 0.15s;white-space:nowrap;text-decoration:none;}
    .alert-link:hover{background:rgba(243,156,18,0.15);}
    .toolbar{background:#fff;border:1px solid var(--color-border);border-radius:var(--radius-lg);padding:14px 16px;display:flex;align-items:center;gap:10px;margin-bottom:20px;box-shadow:var(--shadow-card);flex-wrap:wrap;width:100%;}
    .search-wrap{flex:1;min-width:200px;position:relative;}
    .search-icon{position:absolute;left:13px;top:50%;transform:translateY(-50%);color:var(--color-text-secondary);width:17px;height:17px;pointer-events:none;transition:color 0.2s;}
    .search-input{width:100%;height:40px;padding:0 12px 0 40px;border:1.5px solid var(--color-border);border-radius:var(--radius-md);font-family:var(--font-main);font-size:14px;color:var(--color-text-primary);background:var(--color-bg-main);outline:none;transition:border-color 0.2s,box-shadow 0.2s,background 0.2s;}
    .search-input::placeholder{color:#B0B8C1;}
    .search-input:focus{border-color:var(--color-primary);box-shadow:var(--shadow-focus);background:#fff;}
    .search-wrap:focus-within .search-icon{color:var(--color-primary);}
    .filter-select{height:40px;padding:0 10px;border:1.5px solid var(--color-border);border-radius:var(--radius-md);font-family:var(--font-main);font-size:13px;color:var(--color-text-primary);background:var(--color-bg-main);outline:none;cursor:pointer;transition:border-color 0.2s;min-width:130px;max-width:170px;flex:1;}
    .filter-select:focus{border-color:var(--color-primary);}
    .filter-checkbox-wrap{display:flex;align-items:center;gap:7px;cursor:pointer;user-select:none;font-size:13px;font-weight:500;color:var(--color-text-primary);white-space:nowrap;padding:0 4px;}
    .filter-checkbox-wrap input[type="checkbox"]{width:16px;height:16px;cursor:pointer;accent-color:var(--color-warning);}
    .table-wrap{background:#fff;border:1px solid var(--color-border);border-radius:var(--radius-lg);box-shadow:var(--shadow-card);overflow:hidden;width:100%;}
    .table-scroll-wrap{overflow-x:auto;-webkit-overflow-scrolling:touch;}
    .table-header-bar{padding:16px 20px;border-bottom:1px solid var(--color-border);display:flex;align-items:center;justify-content:space-between;gap:12px;background:var(--color-bg-main);}
    .table-title{font-size:15px;font-weight:600;color:var(--color-text-primary);display:flex;align-items:center;gap:8px;}
    .table-title svg{width:17px;height:17px;color:var(--color-primary);}
    .table-count{font-size:13px;font-weight:400;color:var(--color-text-secondary);}
    table{width:100%;border-collapse:collapse;min-width:860px;}
    thead th{background:var(--color-bg-main);padding:12px 14px;text-align:left;font-size:11px;font-weight:600;text-transform:uppercase;letter-spacing:0.8px;color:var(--color-text-secondary);border-bottom:1px solid var(--color-border);white-space:nowrap;}
    thead th:first-child{padding-left:20px;}
    thead th:last-child{padding-right:20px;text-align:right;}
    .th-sortable{cursor:pointer;user-select:none;}
    .th-sortable:hover{color:var(--color-primary);}
    .th-sort-icon{display:inline-flex;vertical-align:middle;margin-left:4px;opacity:0.4;}
    .th-sort-icon.active{opacity:1;color:var(--color-primary);}
    .th-sort-icon svg{width:13px;height:13px;}
    tbody tr{border-bottom:1px solid #F4F9FC;transition:background 0.12s;}
    tbody tr:last-child{border-bottom:none;}
    tbody tr:hover{background:var(--color-bg-card);}
    tbody tr.row-alert{background:rgba(243,156,18,0.03);}
    tbody tr.row-alert:hover{background:rgba(243,156,18,0.07);}
    tbody td{padding:14px 14px;font-size:14px;vertical-align:middle;}
    tbody td:first-child{padding-left:20px;}
    tbody td:last-child{padding-right:20px;}
    .product-cell{display:flex;align-items:center;gap:12px;}
    .product-icon{width:38px;height:38px;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
    .product-icon.ok{background:var(--color-success-soft);}
    .product-icon.alert{background:var(--color-warning-soft);}
    .product-icon.ok svg{color:var(--color-success);width:18px;height:18px;}
    .product-icon.alert svg{color:var(--color-warning);width:18px;height:18px;}
    .product-name{font-size:14px;font-weight:600;color:var(--color-text-primary);line-height:1.3;}
    .product-invima{font-family:var(--font-mono);font-size:11px;color:var(--color-text-secondary);margin-top:3px;letter-spacing:0.3px;}
    .principio{font-size:13px;color:var(--color-text-secondary);max-width:160px;line-height:1.4;}
    .lab-pill{display:inline-flex;align-items:center;gap:4px;background:var(--color-bg-main);border:1px solid var(--color-border);border-radius:var(--radius-full);padding:3px 9px;font-size:12px;font-weight:500;color:var(--color-text-primary);white-space:nowrap;}
    .cat-pill{display:inline-flex;align-items:center;border-radius:var(--radius-full);padding:3px 9px;font-size:11px;font-weight:600;white-space:nowrap;}
    .cat-antibiotico{background:#F3E5F5;color:#6A1B9A;}
    .cat-analgesico{background:var(--color-info-soft);color:#1A5276;}
    .cat-antihipert{background:var(--color-success-soft);color:#1D6E3A;}
    .cat-antidiabetico{background:#FFF3E0;color:#E65100;}
    .cat-gastroprot{background:var(--color-bg-card);color:var(--color-primary-dark);}
    .cat-antihistaminico{background:#F9FBE7;color:#558B2F;}
    .stock-cell{display:flex;flex-direction:column;gap:5px;min-width:110px;}
    .stock-numbers{display:flex;align-items:center;gap:6px;}
    .stock-qty{font-size:16px;font-weight:700;font-family:var(--font-mono);}
    .stock-qty.ok{color:var(--color-success);}
    .stock-qty.alert{color:var(--color-error);}
    .stock-qty.warn{color:var(--color-warning);}
    .stock-min-label{font-size:11px;color:var(--color-text-secondary);}
    .stock-progress{height:4px;background:#F0F3F4;border-radius:var(--radius-full);overflow:hidden;width:100%;}
    .stock-progress-fill{height:100%;border-radius:var(--radius-full);}
    .stock-progress-fill.ok{background:var(--color-success);}
    .stock-progress-fill.alert{background:var(--color-error);}
    .stock-progress-fill.warn{background:var(--color-warning);}
    .badge{display:inline-flex;align-items:center;gap:4px;border-radius:var(--radius-full);padding:3px 9px;font-size:11px;font-weight:600;white-space:nowrap;}
    .badge svg{width:11px;height:11px;flex-shrink:0;}
    .badge-stock-ok{background:var(--color-success-soft);color:#1D6E3A;}
    .badge-stock-low{background:var(--color-warning-soft);border:1px solid rgba(243,156,18,0.3);color:#7D6608;}
    .badge-activo{background:var(--color-success-soft);color:#1D6E3A;}
    .badge-inactivo{background:var(--color-error-soft);color:#922B21;}
    .price-cell{display:flex;flex-direction:column;gap:2px;}
    .price-main{font-size:14px;font-weight:700;color:var(--color-text-primary);font-family:var(--font-mono);}
    .price-margin{font-size:11px;font-weight:600;color:var(--color-success);}
    .actions-cell{display:flex;align-items:center;justify-content:flex-end;gap:4px;}
    .action-btn{width:34px;height:34px;border-radius:var(--radius-md);background:none;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--color-text-secondary);transition:background 0.15s,color 0.15s;position:relative;}
    .action-btn svg{width:16px;height:16px;}
    .action-btn:hover{background:var(--color-bg-main);}
    .action-btn.view:hover{color:var(--color-primary);background:var(--color-bg-card);}
    .action-btn.edit:hover{color:var(--color-warning);background:var(--color-warning-soft);}
    .action-btn.stock:hover{color:var(--color-secondary);background:var(--color-success-soft);}
    .action-btn.deact:hover{color:var(--color-error);background:var(--color-error-soft);}
    .action-btn::after{content:attr(data-tooltip);position:absolute;bottom:calc(100% + 6px);left:50%;transform:translateX(-50%);background:var(--color-text-primary);color:#fff;font-size:11px;font-weight:500;padding:4px 8px;border-radius:var(--radius-sm);white-space:nowrap;opacity:0;pointer-events:none;transition:opacity 0.15s;}
    .action-btn:hover::after{opacity:1;}
    .pagination-bar{padding:14px 20px;border-top:1px solid var(--color-border);display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;background:#fff;}
    .pagination-info{font-size:13px;color:var(--color-text-secondary);}
    .pagination-info strong{color:var(--color-text-primary);}
    .pagination-controls{display:flex;align-items:center;gap:4px;}
    .page-btn{min-width:34px;height:34px;padding:0 8px;border:1.5px solid var(--color-border);border-radius:var(--radius-md);background:#fff;font-family:var(--font-main);font-size:13px;font-weight:500;color:var(--color-text-primary);cursor:pointer;display:flex;align-items:center;justify-content:center;transition:background 0.15s,border-color 0.15s,color 0.15s;}
    .page-btn:hover:not(:disabled){border-color:var(--color-primary);color:var(--color-primary);background:var(--color-bg-card);}
    .page-btn.active{background:var(--color-primary);border-color:var(--color-primary);color:#fff;}
    .page-btn:disabled{opacity:0.4;cursor:not-allowed;}
    .page-btn svg{width:14px;height:14px;}
    .empty-state{padding:64px 24px;text-align:center;display:none;}
    .empty-state.visible{display:block;}
    .empty-icon{width:64px;height:64px;background:var(--color-bg-card);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;}
    .empty-icon svg{width:28px;height:28px;color:var(--color-text-secondary);}
    .empty-title{font-size:16px;font-weight:600;color:var(--color-text-primary);margin-bottom:6px;}
    .empty-desc{font-size:14px;color:var(--color-text-secondary);}
    .sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:99;}
    @media (max-width:1023px){.sidebar{transform:translateX(-100%);}.sidebar.open{transform:translateX(0);}.sidebar-overlay.visible{display:block;}.topbar{padding-left:16px;}.topbar-toggle{display:flex;}.main-content{margin-left:0;padding:20px;max-width:100vw;}.stats-row{grid-template-columns:repeat(2,1fr);}.filter-select{min-width:120px;max-width:none;}}
    @media (max-width:767px){.main-content{padding:12px;max-width:100vw;}.stats-row{grid-template-columns:1fr 1fr;gap:10px;}.stat-value{font-size:20px;}.page-header{flex-direction:column;align-items:flex-start;}.btn-primary{width:100%;justify-content:center;}.toolbar{flex-direction:column;align-items:stretch;}.filter-select{min-width:0;max-width:none;flex:none;width:100%;}.filter-checkbox-wrap{width:100%;}.btn-secondary{width:100%;justify-content:center;}.topbar-user>div:last-child{display:none;}}
  </style>
</head>
<body>

<div class="app-shell">
  
  <!-- SIDEBAR -->
  <?php include __DIR__ . '/../layouts/sidebar.php'; ?>

  <!-- TOPBAR -->
  <header class="topbar">
    <button class="topbar-toggle" onclick="toggleSidebar()">
      <i data-lucide="menu"></i>
    </button>
    <nav class="breadcrumb">
      <a href="<?= $basePath ?>/dashboard" class="breadcrumb-item">
        <i data-lucide="home" style="width:13px;height:13px;"></i> Inicio
      </a>
      <span class="breadcrumb-sep">/</span>
      <span class="breadcrumb-item current">Productos</span>
    </nav>
    <div class="topbar-actions">
      <button class="topbar-icon-btn">
        <i data-lucide="bell"></i>
        <?php if (($totalAlertas ?? 0) > 0): ?>
        <span class="notif-dot"></span>
        <?php endif; ?>
      </button>
      <button class="topbar-icon-btn">
        <i data-lucide="help-circle"></i>
      </button>
      <div class="topbar-user">
        <div class="topbar-avatar"><?= strtoupper(substr($_SESSION['nombres'] ?? 'U', 0, 1) . substr($_SESSION['apellidos'] ?? 'S', 0, 1)) ?></div>
        <div>
          <div class="topbar-user-name"><?= htmlspecialchars($_SESSION['nombres'] ?? '') ?> <?= htmlspecialchars($_SESSION['apellidos'] ?? '') ?></div>
          <div class="topbar-user-role"><?= htmlspecialchars($_SESSION['rol'] ?? '') ?></div>
        </div>
      </div>
    </div>
  </header>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="main-content">
    
    <!-- Page Header -->
    <div class="page-header">
      <div class="page-header-left">
        <h1>Catálogo de Productos</h1>
        <p>Gestión de inventario y precios del catálogo farmacéutico</p>
      </div>
      <a href="<?= $basePath ?>/inventario/productos/crear" class="btn-primary">
        <i data-lucide="plus"></i> Nuevo producto
      </a>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
      <div class="stat-card">
        <div class="stat-icon">
          <i data-lucide="package"></i>
        </div>
        <div>
          <div class="stat-value"><?= $totalProductos ?? 0 ?></div>
          <div class="stat-label">Total productos</div>
        </div>
      </div>

      <div class="stat-card success">
        <div class="stat-icon">
          <i data-lucide="circle-check"></i>
        </div>
        <div>
          <div class="stat-value"><?= $productosStockOk ?? 0 ?></div>
          <div class="stat-label">Con stock OK</div>
        </div>
      </div>

      <div class="stat-card warning">
        <div class="stat-icon">
          <i data-lucide="alert-triangle"></i>
        </div>
        <div>
          <div class="stat-value"><?= $productosStockBajo ?? 0 ?></div>
          <div class="stat-label">Con stock bajo</div>
        </div>
      </div>

      <div class="stat-card info">
        <div class="stat-icon">
          <i data-lucide="dollar-sign"></i>
        </div>
        <div>
          <div class="stat-value">$<?= number_format($valorInventario ?? 0, 1) ?>M</div>
          <div class="stat-label">Valor inventario COP</div>
        </div>
      </div>
    </div>

    <!-- Alert Banner -->
    <?php if (($productosStockBajo ?? 0) > 0): ?>
    <div class="alert-banner">
      <i data-lucide="alert-triangle"></i>
      <div>
        <strong><?= $productosStockBajo ?> productos con stock bajo</strong> — 
        <?php
        $nombres = array_slice(array_column($productosAlerta ?? [], 'nombre'), 0, 3);
        echo htmlspecialchars(implode(', ', $nombres));
        if (count($productosAlerta ?? []) > 3) echo ' y ' . (count($productosAlerta) - 3) . ' más';
        ?>
        requieren reabastecimiento inmediato.
      </div>
      <div class="alert-banner-actions">
        <a href="<?= $basePath ?>/inventario/alertas" class="alert-link">Ver solo alertas</a>
        <button class="alert-link" onclick="exportarAlertas()">Exportar</button>
      </div>
    </div>
    <?php endif; ?>

    <!-- Toolbar -->
    <div class="toolbar">
      <div class="search-wrap">
        <i data-lucide="search" class="search-icon"></i>
        <input type="text" id="searchInput" class="search-input" placeholder="Buscar por nombre, INVIMA o principio activo..." />
      </div>
      <select id="filterCategoria" class="filter-select">
        <option value="">Todas las categorías</option>
        <?php foreach ($categorias ?? [] as $cat): ?>
        <option value="<?= $cat['categoria_id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
      <select id="filterLaboratorio" class="filter-select">
        <option value="">Todos los laboratorios</option>
        <?php foreach ($proveedores ?? [] as $prov): ?>
        <option value="<?= $prov['proveedor_id'] ?>"><?= htmlspecialchars($prov['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
      <select id="filterEstado" class="filter-select">
        <option value="">Todos los estados</option>
        <option value="activo">Activo</option>
        <option value="inactivo">Inactivo</option>
      </select>
      <label class="filter-checkbox-wrap">
        <input type="checkbox" id="filterSoloAlertas" />
        Solo alertas activas
      </label>
      <button class="btn-secondary" onclick="exportarCatalogo()">
        <i data-lucide="download"></i> Exportar
      </button>
    </div>

    <!-- Tabla de Productos -->
    <div class="table-wrap">
      <div class="table-header-bar">
        <div class="table-title">
          <i data-lucide="package"></i>
          Catálogo de productos
          <span class="table-count">(<?= count($productos ?? []) ?> de <?= count($productos ?? []) ?>)</span>
        </div>
        <button class="btn-secondary btn-sm" onclick="toggleView()">
          <i data-lucide="grid-3x3"></i> Cuadrícula
        </button>
      </div>

      <div class="table-scroll-wrap">
        <table id="productosTable">
          <thead>
            <tr>
              <th class="th-sortable" data-sort="nombre">
                NOMBRE COMERCIAL
                <span class="th-sort-icon"><i data-lucide="chevron-down"></i></span>
              </th>
              <th>PRINCIPIO ACTIVO</th>
              <th>LABORATORIO</th>
              <th>CATEGORÍA</th>
              <th class="th-sortable" data-sort="stock">
                STOCK
                <span class="th-sort-icon"><i data-lucide="chevron-down"></i></span>
              </th>
              <th class="th-sortable" data-sort="precio">
                PRECIO VENTA
                <span class="th-sort-icon"><i data-lucide="chevron-down"></i></span>
              </th>
              <th>ESTADO</th>
              <th style="text-align:right;">ACCIONES</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($productos)): ?>
            <tr>
              <td colspan="8" class="empty-state visible">
                <div class="empty-icon"><i data-lucide="package-x"></i></div>
                <div class="empty-title">No hay productos registrados</div>
                <div class="empty-desc">Comienza registrando tu primer producto farmacéutico</div>
              </td>
            </tr>
            <?php else: ?>
              <?php foreach ($productos as $p): 
                $stockActual = (int)($p['stock_actual'] ?? 0);
                $stockMinimo = (int)($p['stock_minimo'] ?? 10);
                $tieneAlerta = $stockActual <= $stockMinimo;
                $porcentaje = $stockMinimo > 0 ? min(100, ($stockActual / $stockMinimo) * 100) : 100;
                $estadoStock = $stockActual === 0 ? 'alert' : ($tieneAlerta ? 'warn' : 'ok');
              ?>
            <tr class="<?= $tieneAlerta ? 'row-alert' : '' ?>">
              <td>
                <div class="product-cell">
                  <div class="product-icon <?= $tieneAlerta ? 'alert' : 'ok' ?>">
                    <i data-lucide="<?= $tieneAlerta ? 'alert-triangle' : 'pill' ?>"></i>
                  </div>
                  <div>
                    <div class="product-name"><?= htmlspecialchars($p['nombre']) ?></div>
                    <div class="product-invima">INVIMA: <?= htmlspecialchars($p['codigo_invima']) ?></div>
                  </div>
                </div>
              </td>
              <td>
                <div class="principio"><?= htmlspecialchars($p['principio_activo'] ?: '—') ?></div>
              </td>
              <td>
                <span class="lab-pill"><?= htmlspecialchars($p['proveedor_nombre'] ?? 'Sin asignar') ?></span>
              </td>
              <td>
                <span class="cat-pill cat-<?= strtolower(str_replace(' ', '', $p['categoria_nombre'] ?? 'general')) ?>">
                  <?= htmlspecialchars($p['categoria_nombre'] ?? 'General') ?>
                </span>
              </td>
              <td>
                <div class="stock-cell">
                  <div class="stock-numbers">
                    <span class="stock-qty <?= $estadoStock ?>"><?= $stockActual ?></span>
                    <span class="stock-min-label">/ mín. <?= $stockMinimo ?></span>
                  </div>
                  <div class="stock-progress">
                    <div class="stock-progress-fill <?= $estadoStock ?>" style="width: <?= $porcentaje ?>%"></div>
                  </div>
                  <?php if ($tieneAlerta): ?>
                  <span class="badge badge-stock-low">
                    <i data-lucide="alert-triangle"></i> Stock bajo
                  </span>
                  <?php endif; ?>
                </div>
              </td>
              <td>
                <div class="price-cell">
                  <div class="price-main">$<?= number_format($p['precio_venta'], 0, ',', '.') ?></div>
                  <?php 
                  $margen = $p['precio_compra'] > 0 ? (($p['precio_venta'] - $p['precio_compra']) / $p['precio_compra']) * 100 : 0;
                  ?>
                  <div class="price-margin">+<?= number_format($margen, 0) ?>% margen</div>
                </div>
              </td>
              <td>
                <?php if ($p['activo']): ?>
                <span class="badge badge-activo">
                  <i data-lucide="circle-check"></i> Activo
                </span>
                <?php else: ?>
                <span class="badge badge-inactivo">
                  <i data-lucide="circle-x"></i> Inactivo
                </span>
                <?php endif; ?>
              </td>
              <td>
                <div class="actions-cell">
                  <a href="<?= $basePath ?>/inventario/productos/<?= $p['producto_id'] ?>" class="action-btn view" data-tooltip="Ver detalle">
                    <i data-lucide="eye"></i>
                  </a>
                  <a href="<?= $basePath ?>/inventario/productos/<?= $p['producto_id'] ?>/editar" class="action-btn edit" data-tooltip="Editar">
                    <i data-lucide="pencil"></i>
                  </a>
                  <a href="<?= $basePath ?>/inventario/lotes/registrar?producto_id=<?= $p['producto_id'] ?>" class="action-btn stock" data-tooltip="Registrar lote">
                    <i data-lucide="package-plus"></i>
                  </a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Paginación -->
      <div class="pagination-bar">
        <div class="pagination-info">
          Mostrando <strong>1-<?= count($productos ?? []) ?></strong> de <strong><?= count($productos ?? []) ?></strong> productos
        </div>
        <div class="pagination-controls">
          <button class="page-btn" disabled>
            <i data-lucide="chevron-left"></i>
          </button>
          <button class="page-btn active">1</button>
          <button class="page-btn" disabled>
            <i data-lucide="chevron-right"></i>
          </button>
        </div>
      </div>
    </div>

  </main>
</div>

<script src="<?= $basePath ?>/assets/js/app.js"></script>
<script>
if (window.lucide) lucide.createIcons();

// Búsqueda en tiempo real
document.getElementById('searchInput')?.addEventListener('input', function(e) {
  const termino = e.target.value.toLowerCase();
  const filas = document.querySelectorAll('#productosTable tbody tr');
  
  filas.forEach(fila => {
    const texto = fila.textContent.toLowerCase();
    fila.style.display = texto.includes(termino) ? '' : 'none';
  });
});

// Filtros
['filterCategoria', 'filterLaboratorio', 'filterEstado', 'filterSoloAlertas'].forEach(id => {
  document.getElementById(id)?.addEventListener('change', aplicarFiltros);
});

function aplicarFiltros() {
  const categoria = document.getElementById('filterCategoria')?.value;
  const laboratorio = document.getElementById('filterLaboratorio')?.value;
  const estado = document.getElementById('filterEstado')?.value;
  const soloAlertas = document.getElementById('filterSoloAlertas')?.checked;
  
  const filas = document.querySelectorAll('#productosTable tbody tr');
  filas.forEach(fila => {
    let mostrar = true;
    
    if (soloAlertas && !fila.classList.contains('row-alert')) {
      mostrar = false;
    }
    
    fila.style.display = mostrar ? '' : 'none';
  });
}

function exportarCatalogo() {
  window.location.href = '/inventario/productos/exportar';
}

function exportarAlertas() {
  window.location.href = '/inventario/alertas/exportar';
}
</script>

</body>
</html>
