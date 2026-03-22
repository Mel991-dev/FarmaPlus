<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= isset($producto) ? 'Editar' : 'Nuevo' ?> Producto | FarmaPlus CRM</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="<?= $basePath ?>/assets/css/app.min.css">
  <style>:root{--color-primary:#1A6B8A;--color-primary-dark:#1A3A4A;--color-primary-light:#4A9BB5;--color-secondary:#2A9D8F;--color-bg-main:#F4F9FC;--color-bg-card:#E9F5F8;--color-success:#27AE60;--color-error:#E74C3C;--color-warning:#F39C12;--color-info:#3498DB;--color-success-soft:#EAFAF1;--color-error-soft:#FDEDEC;--color-warning-soft:#FEF9E7;--color-info-soft:#EBF5FB;--color-text-primary:#2C3E50;--color-text-secondary:#7F8C8D;--color-border:#BDC3C7;--color-sidebar-bg:#1A3A4A;--color-sidebar-text:#ECF0F1;--color-sidebar-active:#2A9D8F;--color-sidebar-hover:#1A6B8A;--radius-sm:4px;--radius-md:8px;--radius-lg:12px;--radius-full:9999px;--font-main:'Inter',sans-serif;--font-mono:'JetBrains Mono',monospace;--sidebar-width:240px;--topbar-height:64px;--shadow-card:0 2px 8px rgba(0,0,0,0.08);--shadow-hover:0 8px 24px rgba(0,0,0,0.12);--shadow-focus:0 0 0 3px rgba(26,107,138,0.15);}*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}html,body{height:100%;font-family:var(--font-main);font-size:16px;color:var(--color-text-primary);background:var(--color-bg-main);overflow-x:hidden;}.app-shell{display:flex;min-height:100vh;overflow-x:hidden;}.sidebar{width:var(--sidebar-width);background:var(--color-sidebar-bg);display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;z-index:100;transition:transform 0.3s ease;overflow:hidden;}.sidebar-logo{display:flex;align-items:center;gap:10px;padding:24px 16px 20px;border-bottom:1px solid rgba(255,255,255,0.1);}.sidebar-logo-icon{width:36px;height:36px;background:var(--color-secondary);border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;}.sidebar-logo-icon svg{color:#fff;width:20px;height:20px;}.sidebar-logo-text{font-size:17px;font-weight:700;color:#fff;letter-spacing:-0.3px;}.sidebar-logo-text span{color:var(--color-secondary);}.sidebar-nav{flex:1;padding:16px 12px;overflow-y:auto;scrollbar-width:none;}.sidebar-nav::-webkit-scrollbar{display:none;}.nav-section-label{font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:1.5px;color:rgba(236,240,241,0.35);padding:0 8px;margin:16px 0 8px;}.nav-section-label:first-child{margin-top:0;}.nav-item{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:var(--radius-md);cursor:pointer;text-decoration:none;color:var(--color-sidebar-text);font-size:14px;font-weight:500;transition:background 0.15s,color 0.15s;}.nav-item:hover{background:var(--color-sidebar-hover);}.nav-item.active{background:var(--color-sidebar-active);color:#fff;}.nav-item svg{width:18px;height:18px;flex-shrink:0;opacity:0.85;}.nav-item.active svg{opacity:1;}.nav-badge{margin-left:auto;background:rgba(255,255,255,0.15);color:rgba(255,255,255,0.85);font-size:11px;font-weight:600;padding:2px 7px;border-radius:var(--radius-full);}.sidebar-divider{height:1px;background:rgba(255,255,255,0.08);margin:8px 12px;}.sidebar-footer{padding:12px 12px 16px;border-top:1px solid rgba(255,255,255,0.1);}.sidebar-user{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:var(--radius-md);cursor:pointer;transition:background 0.15s;}.sidebar-user:hover{background:rgba(255,255,255,0.06);}.sidebar-avatar{width:34px;height:34px;border-radius:50%;background:var(--color-primary);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0;}.sidebar-user-info{flex:1;min-width:0;}.sidebar-user-name{font-size:13px;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}.sidebar-user-role{font-size:11px;color:rgba(236,240,241,0.5);margin-top:1px;}.sidebar-logout{background:none;border:none;cursor:pointer;color:rgba(236,240,241,0.4);display:flex;align-items:center;justify-content:center;padding:4px;border-radius:var(--radius-sm);transition:color 0.2s;}.sidebar-logout:hover{color:var(--color-error);}.sidebar-logout svg{width:16px;height:16px;}.topbar{height:var(--topbar-height);background:#fff;border-bottom:1px solid var(--color-border);box-shadow:0 1px 4px rgba(0,0,0,0.06);display:flex;align-items:center;padding:0 24px 0 calc(var(--sidebar-width) + 20px);position:fixed;top:0;left:0;right:0;z-index:50;gap:16px;}.topbar-toggle{display:none;background:none;border:none;cursor:pointer;color:var(--color-text-secondary);padding:6px;border-radius:var(--radius-md);}.topbar-toggle svg{width:22px;height:22px;}.breadcrumb{display:flex;align-items:center;gap:6px;flex:1;}.breadcrumb-item{font-size:13px;color:var(--color-text-secondary);text-decoration:none;transition:color 0.15s;display:flex;align-items:center;gap:4px;}.breadcrumb-item:hover{color:var(--color-primary);}.breadcrumb-item.current{font-weight:600;color:var(--color-text-primary);pointer-events:none;}.breadcrumb-sep{color:var(--color-border);font-size:13px;}.topbar-actions{display:flex;align-items:center;gap:8px;}.topbar-icon-btn{width:36px;height:36px;border-radius:var(--radius-md);background:none;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--color-text-secondary);transition:background 0.15s,color 0.15s;position:relative;}.topbar-icon-btn:hover{background:var(--color-bg-main);color:var(--color-primary);}.topbar-icon-btn svg{width:20px;height:20px;}.notif-dot{position:absolute;top:7px;right:7px;width:8px;height:8px;background:var(--color-error);border-radius:50%;border:2px solid #fff;}.topbar-user{display:flex;align-items:center;gap:8px;padding:4px 8px 4px 4px;border-radius:var(--radius-md);cursor:pointer;transition:background 0.15s;border:none;background:none;}.topbar-user:hover{background:var(--color-bg-main);}.topbar-avatar{width:32px;height:32px;border-radius:50%;background:var(--color-primary);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;}.topbar-user-name{font-size:13px;font-weight:600;color:var(--color-text-primary);}.topbar-user-role{font-size:11px;color:var(--color-text-secondary);}.main-content{margin-left:var(--sidebar-width);margin-top:var(--topbar-height);padding:24px 24px 100px 20px !important;flex:1;min-width:0;max-width:calc(100vw - var(--sidebar-width));overflow-x:hidden;}.page-header{display:flex;align-items:flex-start;justify-content:space-between;gap:16px;margin-bottom:24px;flex-wrap:wrap;max-width:1200px;}.page-header-left h1{font-size:22px;font-weight:700;color:var(--color-text-primary);letter-spacing:-0.3px;display:flex;align-items:center;gap:10px;}.page-header-left h1 svg{width:22px;height:22px;color:var(--color-warning);}.page-header-left p{font-size:14px;color:var(--color-text-secondary);margin-top:4px;}.form-layout{display:grid;grid-template-columns:1fr 320px;gap:24px;align-items:start;max-width:1200px;}.form-card{background:#fff;border:1px solid var(--color-border);border-radius:var(--radius-lg);box-shadow:var(--shadow-card);overflow:hidden;transition:transform 0.2s,box-shadow 0.2s;}.form-card:hover{box-shadow:var(--shadow-hover);}.form-section{padding:24px 28px;}.form-section + .form-section{border-top:1px solid var(--color-border);}.section-heading{display:flex;align-items:center;gap:10px;margin-bottom:20px;}.section-icon{width:34px;height:34px;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;flex-shrink:0;}.section-icon svg{width:17px;height:17px;}.section-icon.blue{background:var(--color-info-soft);}.section-icon.blue svg{color:var(--color-primary);}.section-icon.amber{background:var(--color-warning-soft);}.section-icon.amber svg{color:var(--color-warning);}.section-icon.green{background:var(--color-success-soft);}.section-icon.green svg{color:var(--color-success);}.section-heading-text{flex:1;}.section-title{font-size:14px;font-weight:700;color:var(--color-text-primary);}.section-subtitle{font-size:12px;color:var(--color-text-secondary);margin-top:1px;}.form-stack{display:flex;flex-direction:column;gap:16px;}.form-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}.form-grid-3{display:grid;grid-template-columns:1fr 1fr 1fr;gap:16px;}.form-grid-invima{display:grid;grid-template-columns:1fr 180px;gap:16px;}.form-group{display:flex;flex-direction:column;gap:6px;}.form-label{font-size:12px;font-weight:600;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.5px;display:flex;align-items:center;gap:4px;}.req{color:var(--color-error);font-size:13px;line-height:1;}.form-input,.form-select{font-family:var(--font-main);font-size:14px;color:var(--color-text-primary);background:#fff;width:100%;border:1px solid var(--color-border);border-radius:var(--radius-md);height:42px;padding:0 14px;transition:border-color 0.15s,box-shadow 0.15s;}.form-input.mono{font-family:var(--font-mono);font-size:13px;letter-spacing:0.5px;color:var(--color-primary);}.form-input:focus,.form-select:focus{outline:none;border-color:var(--color-primary);box-shadow:var(--shadow-focus);}.form-hint{font-size:12px;color:var(--color-text-secondary);display:flex;align-items:flex-start;gap:5px;line-height:1.4;}.form-hint svg{width:13px;height:13px;flex-shrink:0;margin-top:1px;}.price-input-wrap{position:relative;}.price-prefix{position:absolute;left:0;top:0;bottom:0;width:48px;display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:var(--color-text-secondary);background:var(--color-bg-main);border-right:1px solid var(--color-border);border-radius:var(--radius-md) 0 0 var(--radius-md);pointer-events:none;user-select:none;}.price-input-wrap .form-input{padding-left:58px;font-family:var(--font-mono);font-size:14px;}.toggle-field{display:flex;align-items:center;justify-content:space-between;padding:12px 16px;background:var(--color-bg-main);border:1px solid var(--color-border);border-radius:var(--radius-md);gap:12px;}.toggle-field-info{flex:1;}.toggle-field-label{font-size:13px;font-weight:600;color:var(--color-text-primary);}.toggle-field-desc{font-size:12px;color:var(--color-text-secondary);margin-top:1px;}.toggle-switch{position:relative;width:46px;height:26px;flex-shrink:0;}.toggle-switch input{opacity:0;width:0;height:0;}.toggle-slider{position:absolute;inset:0;background:#BDC3C7;border-radius:var(--radius-full);cursor:pointer;transition:background 0.2s;}.toggle-slider::before{content:'';position:absolute;width:20px;height:20px;border-radius:50%;background:#fff;top:3px;left:3px;transition:transform 0.2s;box-shadow:0 1px 4px rgba(0,0,0,0.18);}.toggle-switch input:checked + .toggle-slider{background:var(--color-primary);}.toggle-switch input:checked + .toggle-slider::before{transform:translateX(20px);}.stock-alert-card{display:none;background:var(--color-warning-soft);border:1px solid rgba(243,156,18,0.35);border-left:4px solid var(--color-warning);border-radius:var(--radius-md);padding:13px 16px;align-items:flex-start;gap:12px;animation:fadeDown 0.22s ease;}.stock-alert-card.visible{display:flex;}@keyframes fadeDown{from{opacity:0;transform:translateY(-6px);}to{opacity:1;transform:translateY(0);}}.stock-alert-icon{width:30px;height:30px;flex-shrink:0;background:rgba(243,156,18,0.2);border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;}.stock-alert-icon svg{width:15px;height:15px;color:var(--color-warning);}.stock-alert-text{flex:1;}.stock-alert-title{font-size:12px;font-weight:700;color:#7D5A00;margin-bottom:2px;}.stock-alert-desc{font-size:12px;color:#8C6800;line-height:1.5;}.margen-display{display:flex;align-items:center;gap:10px;padding:11px 16px;background:var(--color-bg-main);border:1px solid var(--color-border);border-radius:var(--radius-md);min-height:42px;}.margen-label{font-size:12px;font-weight:600;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:0.5px;flex:1;}.margen-value{font-size:18px;font-weight:700;font-family:var(--font-mono);transition:color 0.3s;}.margen-value.positive{color:var(--color-success);}.margen-value.negative{color:var(--color-error);}.margen-value.zero{color:var(--color-text-secondary);}.margen-badge{font-size:11px;font-weight:600;padding:3px 8px;border-radius:var(--radius-full);white-space:nowrap;}.margen-badge.positive{background:var(--color-success-soft);color:var(--color-success);}.margen-badge.negative{background:var(--color-error-soft);color:var(--color-error);}.margen-badge.zero{background:#F4F9FC;color:var(--color-text-secondary);}.side-panel{display:flex;flex-direction:column;gap:14px;}.side-card{background:#fff;border:1px solid var(--color-border);border-radius:var(--radius-lg);box-shadow:var(--shadow-card);overflow:hidden;}.side-card-header{padding:14px 18px;border-bottom:1px solid var(--color-border);background:var(--color-bg-main);font-size:12px;font-weight:700;text-transform:uppercase;letter-spacing:0.6px;color:var(--color-text-secondary);display:flex;align-items:center;gap:6px;}.side-card-header svg{width:14px;height:14px;}.side-card-body{padding:16px 18px;display:flex;flex-direction:column;gap:10px;}.progress-item{display:flex;align-items:center;gap:10px;font-size:13px;}.progress-dot{width:20px;height:20px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:2px solid var(--color-border);background:#fff;transition:border-color 0.2s,background 0.2s;}.progress-dot svg{width:11px;height:11px;}.progress-dot.done{border-color:var(--color-success);background:var(--color-success-soft);}.progress-dot.done svg{color:var(--color-success);}.progress-label{color:var(--color-text-secondary);transition:color 0.2s,font-weight 0.2s;}.progress-label.done{color:var(--color-text-primary);font-weight:600;}.required-note{font-size:12px;color:var(--color-text-secondary);padding:12px 18px;border-top:1px solid var(--color-border);display:flex;align-items:center;gap:6px;}.required-note .req{font-size:14px;}.preview-pill-row{display:flex;align-items:center;gap:10px;padding:12px 14px;background:var(--color-bg-main);border-radius:var(--radius-md);border:1px dashed var(--color-border);}.preview-pill-icon{width:36px;height:36px;background:var(--color-warning-soft);border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;flex-shrink:0;}.preview-pill-icon svg{width:18px;height:18px;color:var(--color-warning);}.preview-name{font-size:13px;font-weight:700;color:var(--color-text-primary);line-height:1.3;}.preview-sub{font-size:11px;color:var(--color-text-secondary);margin-top:2px;}.preview-empty{font-size:12px;color:var(--color-text-secondary);font-style:italic;text-align:center;width:100%;}.tip-item{display:flex;gap:8px;align-items:flex-start;}.tip-icon{width:20px;height:20px;border-radius:4px;display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:1px;}.tip-icon svg{width:12px;height:12px;}.tip-text{font-size:12px;color:var(--color-text-secondary);line-height:1.5;}.form-footer{background:#fff;border:1px solid var(--color-border);border-radius:var(--radius-lg);box-shadow:var(--shadow-card);padding:16px 22px;display:flex;align-items:center;justify-content:space-between;gap:12px;flex-wrap:wrap;}.form-footer-note{font-size:12px;color:var(--color-text-secondary);display:flex;align-items:center;gap:6px;}.form-footer-note svg{width:14px;height:14px;}.form-footer-actions{display:flex;gap:10px;}.btn{display:inline-flex;align-items:center;gap:7px;padding:10px 20px;border-radius:var(--radius-md);font-family:var(--font-main);font-size:14px;font-weight:600;cursor:pointer;border:none;transition:all 0.15s;text-decoration:none;white-space:nowrap;}.btn svg{width:16px;height:16px;flex-shrink:0;}.btn-primary{background:var(--color-primary);color:#fff;}.btn-primary:hover{box-shadow:0 4px 14px rgba(26,107,138,0.28);}.btn-secondary{background:#fff;color:var(--color-text-primary);border:1px solid var(--color-border);}.btn-secondary:hover{background:var(--color-bg-main);border-color:#aaa;}.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.45);z-index:99;}@media (max-width:1023px){.sidebar{transform:translateX(-100%);}.sidebar.open{transform:translateX(0);}.sidebar-overlay.visible{display:block;}.topbar{padding-left:16px;}.topbar-toggle{display:flex;}.main-content{margin-left:0;padding:20px 20px 48px;max-width:100vw;}.form-layout{grid-template-columns:1fr;}.side-panel{order:-1;}.form-grid-3{grid-template-columns:1fr 1fr;}}@media (max-width:767px){.main-content{padding:16px 16px 48px;}.form-grid-2{grid-template-columns:1fr;}.form-grid-3{grid-template-columns:1fr;}.form-grid-invima{grid-template-columns:1fr;}.form-section{padding:18px;}.topbar-user > div:last-child{display:none;}.form-footer{flex-direction:column;align-items:stretch;}.form-footer-actions{flex-direction:column-reverse;}.btn{justify-content:center;}}</style>
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
      <a href="<?= $basePath ?>/inventario/productos" class="breadcrumb-item">Productos</a>
      <span class="breadcrumb-sep">/</span>
      <span class="breadcrumb-item current"><?= isset($producto) ? 'Editar' : 'Nuevo' ?> producto</span>
    </nav>
    <div class="topbar-actions">
      <button class="topbar-icon-btn">
        <i data-lucide="bell"></i>
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
        <h1>
          <i data-lucide="package-plus"></i>
          <?= isset($producto) ? 'Editar' : 'Nuevo' ?> producto
        </h1>
        <p>Registra un nuevo producto farmacéutico en el catálogo de la droguería.</p>
      </div>
    </div>

    <?php if (!empty($_GET['error'])): ?>
    <div style="background:#FDEDEC;border:1px solid #E74C3C;border-left:4px solid #E74C3C;border-radius:8px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:flex-start;gap:10px;max-width:1200px;">
      <i data-lucide="alert-circle" style="width:18px;height:18px;color:#E74C3C;flex-shrink:0;margin-top:1px;"></i>
      <div>
        <strong style="font-size:13px;color:#922B21;display:block;margin-bottom:2px;">Error al guardar el producto</strong>
        <span style="font-size:13px;color:#922B21;"><?= htmlspecialchars($_GET['error']) ?></span>
      </div>
    </div>
    <?php endif; ?>
    <?php if (!empty($_GET['success'])): ?>
    <div style="background:#EAFAF1;border:1px solid #27AE60;border-left:4px solid #27AE60;border-radius:8px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;max-width:1200px;">
      <i data-lucide="check-circle" style="width:18px;height:18px;color:#27AE60;flex-shrink:0;"></i>
      <span style="font-size:13px;color:#1E8449;"><?= htmlspecialchars($_GET['success']) ?></span>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= isset($producto) ? $basePath . '/inventario/productos/' . $producto['producto_id'] . '/editar' : $basePath . '/inventario/productos/crear' ?>" id="productoForm">

      
      <div class="form-layout">
        
        <!-- Columna Principal -->
        <div class="form-stack">
          
          <!-- SECCIÓN 1: Identificación -->
          <div class="form-card">
            <div class="form-section">
              <div class="section-heading">
                <div class="section-icon blue">
                  <i data-lucide="pill"></i>
                </div>
                <div class="section-heading-text">
                  <div class="section-title">Identificación del producto</div>
                  <div class="section-subtitle">Nombre, principio activo, forma farmacéutica y código INVIMA</div>
                </div>
              </div>

              <div class="form-stack">
                <div class="form-grid-2">
                  <div class="form-group">
                    <label class="form-label">
                      Nombre comercial <span class="req">*</span>
                    </label>
                    <input type="text" name="nombre" class="form-input" 
                           value="<?= htmlspecialchars($producto['nombre'] ?? '') ?>"
                           placeholder="Ej: Amoxicilina Mk 500mg x 10 cápsulas" required />
                  </div>

                  <div class="form-group">
                    <label class="form-label">
                      Principio activo <span class="req">*</span>
                    </label>
                    <input type="text" name="principio_activo" class="form-input"
                           value="<?= htmlspecialchars($producto['principio_activo'] ?? '') ?>"
                           placeholder="Ej: Amoxicilina trihidrato" required />
                  </div>
                </div>

                <div class="form-grid-2">
                  <div class="form-group">
                    <label class="form-label">
                      Concentración <span class="req">*</span>
                    </label>
                    <input type="text" name="concentracion" class="form-input"
                           value="<?= htmlspecialchars($producto['concentracion'] ?? '') ?>"
                           placeholder="Ej: 500mg" required />
                  </div>

                  <div class="form-group">
                    <label class="form-label">
                      Forma farmacéutica <span class="req">*</span>
                    </label>
                    <select name="forma_farmaceutica" class="form-select" required>
                      <option value="">Seleccionar...</option>
                      <option value="Cápsulas" <?= ($producto['forma_farmaceutica'] ?? '') === 'Cápsulas' ? 'selected' : '' ?>>Cápsulas</option>
                      <option value="Tabletas" <?= ($producto['forma_farmaceutica'] ?? '') === 'Tabletas' ? 'selected' : '' ?>>Tabletas</option>
                      <option value="Jarabe" <?= ($producto['forma_farmaceutica'] ?? '') === 'Jarabe' ? 'selected' : '' ?>>Jarabe</option>
                      <option value="Suspensión" <?= ($producto['forma_farmaceutica'] ?? '') === 'Suspensión' ? 'selected' : '' ?>>Suspensión</option>
                      <option value="Inyectable" <?= ($producto['forma_farmaceutica'] ?? '') === 'Inyectable' ? 'selected' : '' ?>>Inyectable</option>
                      <option value="Crema" <?= ($producto['forma_farmaceutica'] ?? '') === 'Crema' ? 'selected' : '' ?>>Crema</option>
                      <option value="Gel" <?= ($producto['forma_farmaceutica'] ?? '') === 'Gel' ? 'selected' : '' ?>>Gel</option>
                      <option value="Gotas" <?= ($producto['forma_farmaceutica'] ?? '') === 'Gotas' ? 'selected' : '' ?>>Gotas</option>
                    </select>
                  </div>
                </div>

                <div class="form-grid-invima">
                  <div class="form-group">
                    <label class="form-label">
                      Código INVIMA <span class="req">*</span>
                    </label>
                    <input type="text" name="codigo_invima" class="form-input mono"
                           value="<?= htmlspecialchars($producto['codigo_invima'] ?? '') ?>"
                           placeholder="M-XXXXAR-RX" required />
                    <div class="form-hint">
                      <i data-lucide="info"></i>
                      Consulta el código en el registro sanitario del INVIMA Colombia.
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="form-label">
                      Requiere fórmula médica
                    </label>
                    <div class="toggle-field">
                      <div class="toggle-field-info">
                        <div class="toggle-field-label">Control especial</div>
                      </div>
                      <label class="toggle-switch">
                        <input type="checkbox" name="control_especial" value="1" 
                               <?= ($producto['control_especial'] ?? 0) ? 'checked' : '' ?> />
                        <span class="toggle-slider"></span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- SECCIÓN 2: Clasificación -->
          <div class="form-card">
            <div class="form-section">
              <div class="section-heading">
                <div class="section-icon amber">
                  <i data-lucide="tag"></i>
                </div>
                <div class="section-heading-text">
                  <div class="section-title">Clasificación</div>
                  <div class="section-subtitle">Laboratorio, categoría, presentación y fecha de vencimiento del lote</div>
                </div>
              </div>

              <div class="form-stack">
                <div class="form-grid-2">
                  <div class="form-group">
                    <label class="form-label">
                      Laboratorio <span class="req">*</span>
                    </label>
                    <select name="proveedor_id" class="form-select" required>
                      <option value="">Seleccionar...</option>
                      <?php foreach ($proveedores ?? [] as $prov): ?>
                      <option value="<?= $prov['proveedor_id'] ?>" 
                              <?= ($producto['proveedor_id'] ?? '') == $prov['proveedor_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($prov['nombre']) ?>
                      </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <div class="form-group">
                    <label class="form-label">
                      Categoría <span class="req">*</span>
                    </label>
                    <select name="categoria_id" class="form-select" required>
                      <option value="">Seleccionar...</option>
                      <?php foreach ($categorias ?? [] as $cat): ?>
                      <option value="<?= $cat['categoria_id'] ?>"
                              <?= ($producto['categoria_id'] ?? '') == $cat['categoria_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nombre']) ?>
                      </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- SECCIÓN 3: Inventario y precios -->
          <div class="form-card">
            <div class="form-section">
              <div class="section-heading">
                <div class="section-icon green">
                  <i data-lucide="dollar-sign"></i>
                </div>
                <div class="section-heading-text">
                  <div class="section-title">Inventario y precios</div>
                  <div class="section-subtitle">Precios de compra, venta y stock mínimo</div>
                </div>
              </div>

              <div class="form-stack">
                <div class="form-grid-2">
                  <div class="form-group">
                    <label class="form-label">
                      Stock inicial <span class="req">*</span>
                    </label>
                    <input type="number" name="stock_inicial" class="form-input" min="0"
                           value="<?= $producto['stock_actual'] ?? 0 ?>"
                           placeholder="0" required />
                    <div class="form-hint">
                      <i data-lucide="info"></i>
                      Unidades disponibles al momento del registro.
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="form-label">
                      Stock mínimo / Umbral de alerta <span class="req">*</span>
                    </label>
                    <input type="number" name="stock_minimo" class="form-input" min="0"
                           value="<?= $producto['stock_minimo'] ?? 10 ?>"
                           placeholder="10" required />
                    <div class="form-hint">
                      <i data-lucide="info"></i>
                      Se generará una alerta cuando el stock baje de este valor.
                    </div>
                  </div>
                </div>

                <div class="form-grid-2">
                  <div class="form-group">
                    <label class="form-label">
                      Precio de compra <span class="req">*</span>
                    </label>
                    <div class="price-input-wrap">
                      <div class="price-prefix">COP</div>
                      <input type="number" name="precio_compra" class="form-input" step="0.01" min="0"
                             value="<?= $producto['precio_compra'] ?? '' ?>"
                             placeholder="0" required />
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="form-label">
                      Precio de venta <span class="req">*</span>
                    </label>
                    <div class="price-input-wrap">
                      <div class="price-prefix">COP</div>
                      <input type="number" name="precio_venta" class="form-input" step="0.01" min="0"
                             value="<?= $producto['precio_venta'] ?? '' ?>"
                             placeholder="0" required id="precioVenta" />
                    </div>
                  </div>
                </div>


                <!-- Margen calculado -->
                <div class="form-group">
                  <label class="form-label">Margen de ganancia — Calculado automáticamente</label>
                  <div class="margen-display" id="margenDisplay">
                    <span class="margen-label" id="margenLabel">Margen estimado</span>
                    <span class="margen-value zero" id="margenValue">-</span>
                    <span class="margen-badge zero" id="margenBadge">Sin datos</span>
                  </div>
                  <div class="form-hint">
                    <i data-lucide="info"></i>
                    Fórmula: (Precio venta – Precio compra) / Precio compra × 100
                  </div>
                </div>

                <!-- Alerta de stock -->
                <div class="stock-alert-card" id="stockAlert">
                  <div class="stock-alert-icon">
                    <i data-lucide="alert-triangle"></i>
                  </div>
                  <div class="stock-alert-text">
                    <div class="stock-alert-title">Stock mínimo configurado</div>
                    <div class="stock-alert-desc">
                      El sistema generará una alerta automática cuando el stock disponible sea igual o menor a 
                      <strong id="stockMinimoDisplay">10</strong> unidades.
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Columna Lateral -->
        <div class="side-panel">
          
          <!-- Vista Previa -->
          <div class="side-card">
            <div class="side-card-header">
              <i data-lucide="eye"></i>
              Vista previa
            </div>
            <div class="side-card-body">
              <div class="preview-pill-row" id="previewCard">
                <div class="preview-pill-icon">
                  <i data-lucide="package"></i>
                </div>
                <div>
                  <div class="preview-name" id="previewNombre">Completa el nombre para ver la vista previa...</div>
                  <div class="preview-sub" id="previewSub"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Progreso -->
          <div class="side-card">
            <div class="side-card-header">
              <i data-lucide="list-checks"></i>
              Progreso
            </div>
            <div class="side-card-body">
              <div class="progress-item">
                <div class="progress-dot" id="progressId">
                  <i data-lucide="check"></i>
                </div>
                <span class="progress-label" id="labelId">Identificación</span>
              </div>
              <div class="progress-item">
                <div class="progress-dot" id="progressClasif">
                  <i data-lucide="check"></i>
                </div>
                <span class="progress-label" id="labelClasif">Clasificación</span>
              </div>
              <div class="progress-item">
                <div class="progress-dot" id="progressPrecios">
                  <i data-lucide="check"></i>
                </div>
                <span class="progress-label" id="labelPrecios">Inventario y precios</span>
              </div>
            </div>
            <div class="required-note">
              <span class="req">*</span> Campos marcados son obligatorios
            </div>
          </div>

          <!-- Guía Regulatoria -->
          <div class="side-card">
            <div class="side-card-header">
              <i data-lucide="book-open"></i>
              Guía regulatoria
            </div>
            <div class="side-card-body">
              <div class="tip-item">
                <div class="tip-icon" style="background:var(--color-info-soft);">
                  <i data-lucide="shield-check" style="color:var(--color-info);"></i>
                </div>
                <div class="tip-text">
                  Consulta el <strong>Registro INVIMA</strong> en 
                  <a href="https://registros.invima.gov.co" target="_blank" style="color:var(--color-primary);text-decoration:underline;">registros.invima.gov.co</a> 
                  para verificar el código.
                </div>
              </div>
            </div>
          </div>

        </div>

      </div>

      <!-- Form Footer -->
      <div class="form-card" style="margin-top:24px; max-width:1200px;">
        <div class="form-section" style="padding:16px 24px;">
          <div class="form-footer" style="box-shadow:none; border:none; padding:0; background:transparent;">
            <div class="form-footer-note">
              <i data-lucide="info"></i>
              <span class="req">*</span> Campos obligatorios. Completa todos antes de guardar.
            </div>
            <div class="form-footer-actions">
              <a href="<?= $basePath ?>/inventario/productos" class="btn btn-secondary">
                <i data-lucide="x"></i> Cancelar
              </a>
              <button type="submit" class="btn btn-primary">
                <i data-lucide="save"></i> <?= isset($producto) ? 'Guardar producto →' : 'Guardar producto →' ?>
              </button>
            </div>
          </div>
        </div>
      </div>

    </form>

  </main>
</div>

<script src="<?= $basePath ?>/assets/js/app.js"></script>
<script src="<?= $basePath ?>/assets/js/validaciones.js"></script>
<script>
if (window.lucide) lucide.createIcons();

// Vista previa en tiempo real
const campos = {
  nombre: document.querySelector('[name="nombre"]'),
  principio_activo: document.querySelector('[name="principio_activo"]'),
  codigo_invima: document.querySelector('[name="codigo_invima"]'),
  precio_compra: document.querySelector('[name="precio_compra"]'),
  precio_venta: document.querySelector('[name="precio_venta"]'),
  stock_minimo: document.querySelector('[name="stock_minimo"]')
};

function actualizarPreview() {
  const nombre = campos.nombre?.value || '';
  const principio = campos.principio_activo?.value || '';
  const invima = campos.codigo_invima?.value || '';
  
  document.getElementById('previewNombre').textContent = nombre || 'Completa el nombre para ver la vista previa...';
  document.getElementById('previewSub').textContent = invima ? `INVIMA: ${invima}` : '';
  
  // Progreso
  const seccion1 = nombre && principio && invima;
  const seccion2 = document.querySelector('[name="proveedor_id"]')?.value && document.querySelector('[name="categoria_id"]')?.value;
  const seccion3 = campos.precio_compra?.value && campos.precio_venta?.value && campos.stock_minimo?.value;
  
  actualizarProgreso('progressId', 'labelId', seccion1);
  actualizarProgreso('progressClasif', 'labelClasif', seccion2);
  actualizarProgreso('progressPrecios', 'labelPrecios', seccion3);
}

function actualizarProgreso(dotId, labelId, completado) {
  const dot = document.getElementById(dotId);
  const label = document.getElementById(labelId);
  if (completado) {
    dot?.classList.add('done');
    label?.classList.add('done');
  } else {
    dot?.classList.remove('done');
    label?.classList.remove('done');
  }
}

// Cálculo de margen
function calcularMargen() {
  const compra = parseFloat(campos.precio_compra?.value) || 0;
  const venta = parseFloat(campos.precio_venta?.value) || 0;
  
  if (compra > 0 && venta > 0) {
    const margen = ((venta - compra) / compra) * 100;
    const margenDisplay = document.getElementById('margenDisplay');
    const margenValue = document.getElementById('margenValue');
    const margenBadge = document.getElementById('margenBadge');
    
    margenDisplay.style.display = 'flex';
    margenValue.textContent = margen.toFixed(1) + '%';
    
    if (margen > 0) {
      margenValue.className = 'margen-value positive';
      margenBadge.className = 'margen-badge positive';
      margenBadge.textContent = 'Rentable';
    } else if (margen < 0) {
      margenValue.className = 'margen-value negative';
      margenBadge.className = 'margen-badge negative';
      margenBadge.textContent = 'Pérdida';
    } else {
      margenValue.className = 'margen-value zero';
      margenBadge.className = 'margen-badge zero';
      margenBadge.textContent = 'Sin margen';
    }
  }
}

// Actualizar display de stock mínimo
campos.stock_minimo?.addEventListener('input', function() {
  document.getElementById('stockMinimoDisplay').textContent = this.value || '10';
  document.getElementById('stockAlert').classList.toggle('visible', this.value > 0);
});

// Event listeners
Object.values(campos).forEach(campo => {
  campo?.addEventListener('input', actualizarPreview);
});

campos.precio_compra?.addEventListener('input', calcularMargen);
campos.precio_venta?.addEventListener('input', calcularMargen);

// Inicializar
actualizarPreview();
calcularMargen();
if (campos.stock_minimo?.value > 0) {
  document.getElementById('stockAlert').classList.add('visible');
}
</script>

</body>
</html>
