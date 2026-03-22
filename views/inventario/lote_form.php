<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registrar Lote | FarmaPlus CRM</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>:root{--color-primary:#1A6B8A;--color-primary-dark:#1A3A4A;--color-primary-light:#4A9BB5;--color-secondary:#2A9D8F;--color-bg-main:#F4F9FC;--color-bg-card:#E9F5F8;--color-success:#27AE60;--color-error:#E74C3C;--color-warning:#F39C12;--color-info:#3498DB;--color-success-soft:#EAFAF1;--color-error-soft:#FDEDEC;--color-warning-soft:#FEF9E7;--color-info-soft:#EBF5FB;--color-text-primary:#2C3E50;--color-text-secondary:#7F8C8D;--color-border:#BDC3C7;--color-sidebar-bg:#1A3A4A;--color-sidebar-text:#ECF0F1;--color-sidebar-active:#2A9D8F;--color-sidebar-hover:#1A6B8A;--radius-sm:4px;--radius-md:8px;--radius-lg:12px;--radius-full:9999px;--font-main:'Inter',sans-serif;--font-mono:'JetBrains Mono',monospace;--sidebar-width:240px;--topbar-height:64px;--shadow-card:0 2px 8px rgba(0,0,0,0.08);--shadow-hover:0 8px 24px rgba(0,0,0,0.12);--shadow-focus:0 0 0 3px rgba(26,107,138,0.15);}*,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}html,body{height:100%;font-family:var(--font-main);font-size:16px;color:var(--color-text-primary);background:var(--color-bg-main);}.app-shell{display:flex;min-height:100vh;}.sidebar{width:var(--sidebar-width);background:var(--color-sidebar-bg);display:flex;flex-direction:column;position:fixed;top:0;left:0;height:100vh;z-index:100;transition:transform 0.3s ease;overflow:hidden;}.sidebar-logo{display:flex;align-items:center;gap:10px;padding:24px 16px 20px;border-bottom:1px solid rgba(255,255,255,0.1);flex-shrink:0;}.sidebar-logo-icon{width:36px;height:36px;background:var(--color-secondary);border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;}.sidebar-logo-icon svg{color:#fff;width:20px;height:20px;}.sidebar-logo-text{font-size:17px;font-weight:700;color:#fff;letter-spacing:-0.3px;}.sidebar-logo-text span{color:var(--color-secondary);}.sidebar-nav{flex:1;padding:16px 12px;overflow-y:auto;scrollbar-width:none;}.sidebar-nav::-webkit-scrollbar{display:none;}.nav-section-label{font-size:10px;font-weight:600;text-transform:uppercase;letter-spacing:1.5px;color:rgba(236,240,241,0.35);padding:0 8px;margin:16px 0 8px;display:block;}.nav-section-label:first-child{margin-top:0;}.nav-item{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:var(--radius-md);cursor:pointer;text-decoration:none;color:var(--color-sidebar-text);font-size:14px;font-weight:500;transition:background 0.15s,color 0.15s;}.nav-item:hover{background:var(--color-sidebar-hover);}.nav-item.active{background:var(--color-sidebar-active);color:#fff;}.nav-item svg{width:18px;height:18px;flex-shrink:0;opacity:0.85;}.nav-item.active svg{opacity:1;}.nav-badge{margin-left:auto;background:rgba(255,255,255,0.15);color:rgba(255,255,255,0.85);font-size:11px;font-weight:600;padding:2px 7px;border-radius:var(--radius-full);min-width:20px;text-align:center;}.nav-item.active .nav-badge{background:rgba(255,255,255,0.25);color:#fff;}.sidebar-divider{height:1px;background:rgba(255,255,255,0.08);margin:8px 12px;}.sidebar-footer{padding:12px 12px 16px;border-top:1px solid rgba(255,255,255,0.1);flex-shrink:0;}.sidebar-user{display:flex;align-items:center;gap:10px;padding:10px 12px;border-radius:var(--radius-md);cursor:pointer;transition:background 0.15s;}.sidebar-user:hover{background:rgba(255,255,255,0.06);}.sidebar-avatar{width:34px;height:34px;border-radius:50%;background:var(--color-primary);display:flex;align-items:center;justify-content:center;font-size:13px;font-weight:700;color:#fff;flex-shrink:0;}.sidebar-user-info{flex:1;min-width:0;}.sidebar-user-name{font-size:13px;font-weight:600;color:#fff;white-space:nowrap;overflow:hidden;text-overflow:ellipsis;}.sidebar-user-role{font-size:11px;color:rgba(236,240,241,0.5);margin-top:1px;}.sidebar-logout{background:none;border:none;cursor:pointer;color:rgba(236,240,241,0.4);display:flex;align-items:center;justify-content:center;padding:4px;border-radius:var(--radius-sm);transition:color 0.2s;}.sidebar-logout:hover{color:var(--color-error);}.sidebar-logout svg{width:16px;height:16px;}.sidebar-overlay{display:none;position:fixed;inset:0;background:rgba(0,0,0,0.4);z-index:90;}.sidebar-overlay.visible{display:block;}.topbar{height:var(--topbar-height);background:#fff;border-bottom:1px solid var(--color-border);box-shadow:0 1px 4px rgba(0,0,0,0.06);display:flex;align-items:center;padding:0 24px 0 calc(var(--sidebar-width) + 24px);position:fixed;top:0;left:0;right:0;z-index:50;gap:16px;}.topbar-toggle{display:none;background:none;border:none;cursor:pointer;color:var(--color-text-secondary);padding:6px;border-radius:var(--radius-md);}.topbar-toggle svg{width:22px;height:22px;}.breadcrumb{display:flex;align-items:center;gap:6px;flex:1;}.breadcrumb-item{font-size:13px;color:var(--color-text-secondary);text-decoration:none;transition:color 0.15s;display:flex;align-items:center;gap:4px;}.breadcrumb-item:hover{color:var(--color-primary);}.breadcrumb-item.current{font-weight:600;color:var(--color-text-primary);pointer-events:none;}.breadcrumb-sep{color:var(--color-border);font-size:13px;}.topbar-actions{display:flex;align-items:center;gap:8px;}.topbar-icon-btn{width:36px;height:36px;border-radius:var(--radius-md);background:none;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--color-text-secondary);transition:background 0.15s,color 0.15s;position:relative;}.topbar-icon-btn:hover{background:var(--color-bg-main);color:var(--color-primary);}.topbar-icon-btn svg{width:20px;height:20px;}.notif-dot{position:absolute;top:7px;right:7px;width:8px;height:8px;background:var(--color-error);border-radius:50%;border:2px solid #fff;}.topbar-user{display:flex;align-items:center;gap:8px;padding:4px 8px 4px 4px;border-radius:var(--radius-md);cursor:pointer;transition:background 0.15s;border:none;background:none;}.topbar-user:hover{background:var(--color-bg-main);}.topbar-avatar{width:32px;height:32px;border-radius:50%;background:var(--color-primary);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;}.topbar-user-name{font-size:13px;font-weight:600;color:var(--color-text-primary);line-height:1.2;}.topbar-user-role{font-size:11px;color:var(--color-text-secondary);}.main-content{margin-left:var(--sidebar-width);margin-top:var(--topbar-height);padding:28px 32px;flex:1;min-height:calc(100vh - var(--topbar-height));}.page-header{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:24px;}.page-header-left{display:flex;align-items:center;gap:14px;}.back-btn{width:38px;height:38px;border-radius:var(--radius-md);background:#fff;border:1.5px solid var(--color-border);cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--color-text-secondary);transition:all 0.15s;flex-shrink:0;text-decoration:none;}.back-btn:hover{border-color:var(--color-primary);color:var(--color-primary);background:var(--color-bg-card);}.back-btn svg{width:18px;height:18px;}.page-title-block h1{font-size:22px;font-weight:700;color:var(--color-text-primary);display:flex;align-items:center;gap:10px;letter-spacing:-0.3px;}.page-title-block p{font-size:13px;color:var(--color-text-secondary);margin-top:3px;}.btn-primary{height:42px;background:var(--color-primary);color:#fff;border:none;border-radius:var(--radius-md);font-family:var(--font-main);font-size:14px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;padding:0 18px;transition:background 0.2s,box-shadow 0.2s;white-space:nowrap;}.btn-primary:hover{background:var(--color-primary-light);box-shadow:0 4px 16px rgba(26,107,138,0.28);}.btn-primary svg{width:16px;height:16px;}.btn-secondary{height:42px;background:transparent;border:1.5px solid var(--color-border);border-radius:var(--radius-md);font-family:var(--font-main);font-size:14px;font-weight:500;color:var(--color-text-primary);cursor:pointer;display:inline-flex;align-items:center;gap:7px;padding:0 16px;transition:border-color 0.2s,background 0.2s;}.btn-secondary:hover{border-color:var(--color-primary);background:var(--color-bg-card);color:var(--color-primary);}.btn-secondary svg{width:15px;height:15px;}.btn-success{height:42px;background:var(--color-success);color:#fff;border:none;border-radius:var(--radius-md);font-family:var(--font-main);font-size:14px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;padding:0 18px;transition:background 0.2s;}.btn-success:hover{background:#1E8449;}.btn-success svg{width:16px;height:16px;}.card{background:#fff;border:1px solid var(--color-border);border-radius:var(--radius-lg);box-shadow:var(--shadow-card);overflow:hidden;margin-bottom:20px;}.card:last-child{margin-bottom:0;}.card-header{padding:16px 20px;border-bottom:1px solid var(--color-border);display:flex;align-items:center;justify-content:space-between;gap:12px;background:var(--color-bg-main);}.card-header-title{display:flex;align-items:center;gap:8px;font-size:15px;font-weight:600;color:var(--color-text-primary);}.card-header-title svg{width:16px;height:16px;color:var(--color-primary);}.card-header-desc{font-size:13px;color:var(--color-text-secondary);margin-top:2px;}.card-body{padding:20px;}.form-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:20px;}.col-span-2{grid-column:span 2;}.form-field{display:flex;flex-direction:column;gap:6px;}.form-label{font-size:14px;font-weight:500;color:var(--color-text-primary);display:flex;align-items:center;gap:4px;}.form-label .required{color:var(--color-error);}.form-label .hint{font-size:12px;color:var(--color-text-secondary);font-weight:400;}.form-input,.form-select,.form-textarea{height:48px;border:1.5px solid var(--color-border);border-radius:var(--radius-md);padding:0 16px;font-family:var(--font-main);font-size:15px;color:var(--color-text-primary);background:#fff;transition:border 0.15s,box-shadow 0.15s;outline:none;width:100%;}.form-input:focus,.form-select:focus,.form-textarea:focus{border-color:var(--color-primary);box-shadow:var(--shadow-focus);}.form-input.mono{font-family:var(--font-mono);font-size:14px;letter-spacing:0.5px;}.form-input.readonly{background:var(--color-bg-main);color:var(--color-text-secondary);cursor:default;}.form-textarea{height:auto;min-height:88px;padding:12px 16px;resize:vertical;line-height:1.5;}.form-select{appearance:none;cursor:pointer;background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='16' height='16' viewBox='0 0 24 24' fill='none' stroke='%237F8C8D' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E");background-repeat:no-repeat;background-position:right 14px center;padding-right:40px;}.form-help{font-size:12px;color:var(--color-text-secondary);}.input-wrap{position:relative;}.input-wrap .input-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);width:17px;height:17px;color:var(--color-text-secondary);pointer-events:none;}.input-wrap .form-input{padding-left:42px;}.product-preview{background:var(--color-bg-card);border:1px solid var(--color-primary-light);border-radius:var(--radius-md);padding:14px 16px;display:flex;align-items:center;gap:14px;margin-top:8px;}.product-preview-icon{width:42px;height:42px;background:var(--color-primary);border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;flex-shrink:0;}.product-preview-icon svg{width:22px;height:22px;color:#fff;}.product-preview-info{flex:1;}.product-preview-name{font-size:14px;font-weight:700;color:var(--color-text-primary);}.product-preview-sub{font-family:var(--font-mono);font-size:11px;color:var(--color-text-secondary);margin-top:2px;}.product-preview-chips{display:flex;align-items:center;gap:12px;margin-top:6px;flex-wrap:wrap;}.chip{display:inline-flex;align-items:center;gap:4px;font-size:12px;font-weight:500;}.chip svg{width:12px;height:12px;}.chip-warn{color:var(--color-warning);}.chip-muted{color:var(--color-text-secondary);}.badge-libre{display:inline-flex;align-items:center;gap:4px;background:var(--color-success-soft);color:var(--color-success);font-size:11px;font-weight:600;padding:3px 9px;border-radius:var(--radius-full);}.badge-libre svg{width:11px;height:11px;}.days-pill{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:var(--radius-full);font-size:12px;font-weight:600;margin-top:6px;}.days-pill svg{width:12px;height:12px;}.days-pill.ok{background:var(--color-success-soft);color:var(--color-success);}.days-pill.warn{background:var(--color-warning-soft);color:var(--color-warning);}.days-pill.crit{background:var(--color-error-soft);color:var(--color-error);}.alert-box{border-radius:var(--radius-md);padding:12px 16px;display:flex;align-items:flex-start;gap:10px;font-size:13px;line-height:1.5;margin-top:16px;}.alert-box svg{width:17px;height:17px;flex-shrink:0;margin-top:1px;}.alert-box strong{display:block;font-weight:700;margin-bottom:1px;}.alert-warn{background:var(--color-warning-soft);border:1px solid rgba(243,156,18,0.3);color:#7D5A00;}.alert-warn svg{color:var(--color-warning);}.alert-crit{background:var(--color-error-soft);border:1px solid rgba(231,76,60,0.3);color:#8B0000;}.alert-crit svg{color:var(--color-error);}.form-actions-bar{background:#fff;border:1px solid var(--color-border);border-radius:var(--radius-lg);padding:18px 20px;display:flex;align-items:center;gap:12px;box-shadow:var(--shadow-card);}.form-actions-note{margin-left:auto;font-size:12px;color:var(--color-text-secondary);display:flex;align-items:center;gap:4px;}.form-actions-note svg{width:13px;height:13px;color:var(--color-info);}.toast-container{position:fixed;top:80px;right:24px;z-index:999;display:flex;flex-direction:column;gap:8px;}.toast{display:flex;align-items:flex-start;gap:10px;padding:12px 16px;border-radius:var(--radius-md);box-shadow:0 4px 20px rgba(0,0,0,0.12);min-width:280px;max-width:360px;animation:toastIn 0.3s ease;}@keyframes toastIn{from{transform:translateX(120%);opacity:0;}to{transform:translateX(0);opacity:1;}}.toast.success{background:var(--color-success-soft);border-left:4px solid var(--color-success);}.toast.warning{background:var(--color-warning-soft);border-left:4px solid var(--color-warning);}.toast.info{background:var(--color-info-soft);border-left:4px solid var(--color-info);}.toast.error{background:var(--color-error-soft);border-left:4px solid var(--color-error);}.toast svg{width:16px;height:16px;flex-shrink:0;margin-top:1px;}.toast.success svg{color:var(--color-success);}.toast.warning svg{color:var(--color-warning);}.toast.info svg{color:var(--color-info);}.toast.error svg{color:var(--color-error);}.toast-text{font-size:14px;color:var(--color-text-primary);}.toast-text strong{display:block;font-weight:600;margin-bottom:1px;}@media (max-width:1023px){.sidebar{transform:translateX(-100%);}.sidebar.open{transform:translateX(0);}.topbar{padding:0 16px;}.topbar-toggle{display:flex;}.main-content{margin-left:0;padding:20px 16px;}.form-grid-2{grid-template-columns:1fr;}.col-span-2{grid-column:span 1;}}@media (max-width:767px){.page-header{flex-wrap:wrap;}.form-actions-bar{flex-direction:column;align-items:stretch;}.form-actions-note{margin-left:0;justify-content:center;}}</style>
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
      <a href="<?= $basePath ?>/inventario/lotes" class="breadcrumb-item">Lotes</a>
      <span class="breadcrumb-sep">/</span>
      <span class="breadcrumb-item current">Registrar entrada</span>
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
        <a href="<?= $basePath ?>/inventario/lotes" class="back-btn">
          <i data-lucide="arrow-left"></i>
        </a>
        <div class="page-title-block">
          <h1>Registrar entrada de lote</h1>
          <p>Complete los datos del nuevo lote recibido para actualizar el inventario.</p>
        </div>
      </div>
    </div>

    <?php if (!empty($_GET['error'])): ?>
    <div style="background:#FDEDEC;border:1px solid #E74C3C;border-left:4px solid #E74C3C;border-radius:8px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:flex-start;gap:10px;">
      <i data-lucide="alert-circle" style="width:18px;height:18px;color:#E74C3C;flex-shrink:0;margin-top:1px;"></i>
      <div>
        <strong style="font-size:13px;color:#922B21;display:block;margin-bottom:2px;">Error al registrar el lote</strong>
        <span style="font-size:13px;color:#922B21;"><?= htmlspecialchars($_GET['error']) ?></span>
      </div>
    </div>
    <?php endif; ?>
    <?php if (!empty($_GET['success'])): ?>
    <div style="background:#EAFAF1;border:1px solid #27AE60;border-left:4px solid #27AE60;border-radius:8px;padding:14px 18px;margin-bottom:20px;display:flex;align-items:center;gap:10px;">
      <i data-lucide="check-circle" style="width:18px;height:18px;color:#27AE60;flex-shrink:0;"></i>
      <span style="font-size:13px;color:#1E8449;"><?= htmlspecialchars($_GET['success']) ?></span>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= $basePath ?>/inventario/lotes/registrar" id="loteForm">

      
      <!-- SECCIÓN 1: Producto -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-header-title">
              <i data-lucide="pill"></i>Sección 1 — Producto
            </div>
            <div class="card-header-desc">Seleccione el medicamento al que pertenece este lote.</div>
          </div>
        </div>
        <div class="card-body">
          
          <div class="form-field" style="margin-bottom:16px;">
            <label class="form-label">
              Buscar producto <span class="required">*</span>
            </label>
            <div class="input-wrap">
              <i data-lucide="search" class="input-icon"></i>
              <input type="text" id="buscarProducto" class="form-input" 
                     placeholder="Nombre comercial, principio activo o código INVIMA..." 
                     autocomplete="off" />
              <input type="hidden" name="producto_id" id="productoIdHidden" required />
            </div>
            <span class="form-help">Busque por nombre comercial, principio activo o código INVIMA.</span>
          </div>

          <!-- Resultados de búsqueda -->
          <div id="searchResults" style="display:none; margin-top:12px;"></div>

          <!-- Preview del producto seleccionado -->
          <div class="product-preview" id="productPreview" style="display:none; margin-top:16px;">
            <div class="product-preview-icon">
              <i data-lucide="pill"></i>
            </div>
            <div class="product-preview-info">
              <div class="product-preview-name" id="previewNombre"></div>
              <div class="product-preview-sub" id="previewSub"></div>
              <div class="product-preview-chips" id="previewChips"></div>
            </div>
          </div>

        </div>
      </div>

      <!-- SECCIÓN 2: Datos del lote -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-header-title">
              <i data-lucide="layers"></i>Sección 2 — Datos del lote
            </div>
            <div class="card-header-desc">Información del lote recibido según el empaque del fabricante.</div>
          </div>
        </div>
        <div class="card-body">
          
          <div class="form-grid-2">
            
            <div class="form-field">
              <label class="form-label">
                Número de lote <span class="required">*</span>
              </label>
              <input type="text" name="numero_lote" class="form-input mono" 
                     placeholder="Ej: TQ-2026-089" required />
              <span class="form-help">Tal como aparece impreso en el empaque del fabricante.</span>
            </div>

            <div class="form-field">
              <label class="form-label">
                Cantidad recibida <span class="required">*</span>
                <span class="hint">(unidades)</span>
              </label>
              <input type="number" name="cantidad_inicial" class="form-input" min="1" 
                     placeholder="120" required />
            </div>

            <div class="form-field">
              <label class="form-label">
                Fecha de vencimiento <span class="required">*</span>
              </label>
              <input type="date" name="fecha_vencimiento" class="form-input" 
                     id="fechaVencimiento" required />
              <span class="form-help" id="diasRestantes"></span>
            </div>

            <div class="form-field">
              <label class="form-label">
                Proveedor
              </label>
              <select name="proveedor_id" class="form-select">
                <option value="">Sin asignar</option>
                <?php foreach ($proveedores ?? [] as $prov): ?>
                <option value="<?= $prov['proveedor_id'] ?>">
                  <?= htmlspecialchars($prov['nombre']) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>

          </div>

          <!-- Alerta de vencimiento -->
          <div class="alert-box alert-warn" id="alertVencimiento" style="display:none;">
            <i data-lucide="alert-triangle"></i>
            <div>
              <strong>Vencimiento próximo</strong>
              Este lote vence en menos de 30 días. Se generará una alerta automática al registrarlo.
            </div>
          </div>

          <div class="alert-box alert-crit" id="alertVencido" style="display:none;">
            <i data-lucide="x-circle"></i>
            <div>
              <strong>Fecha de vencimiento inválida</strong>
              La fecha de vencimiento debe ser posterior a la fecha actual. No se puede registrar un lote ya vencido.
            </div>
          </div>

        </div>
      </div>

      <!-- Barra de acciones -->
      <div class="form-actions-bar">
        <a href="<?= $basePath ?>/inventario/lotes" class="btn-secondary">
          <i data-lucide="x"></i> Cancelar
        </a>
        <div class="form-actions-note">
          <i data-lucide="info"></i>
          El stock se actualizará automáticamente al guardar
        </div>
        <button type="submit" class="btn-success" id="submitBtn" disabled>
          <i data-lucide="save"></i> Registrar lote
        </button>
      </div>

    </form>

  </main>
</div>

<script src="<?= $basePath ?>/assets/js/app.js"></script>
<script>
if (window.lucide) lucide.createIcons();

let productoSeleccionado = null;

// Búsqueda de productos con debounce
let searchTimeout;
document.getElementById('buscarProducto')?.addEventListener('input', function(e) {
  clearTimeout(searchTimeout);
  const termino = e.target.value.trim();
  
  if (termino.length < 2) {
    document.getElementById('searchResults').style.display = 'none';
    return;
  }
  
  searchTimeout = setTimeout(() => buscarProductos(termino), 300);
});

async function buscarProductos(termino) {
  try {
    const basePath = '<?= $basePath ?>';
    const response = await fetch(`${basePath}/api/productos/buscar?q=${encodeURIComponent(termino)}`);
    const productos = await response.json();
    
    const resultsDiv = document.getElementById('searchResults');
    if (productos.length === 0) {
      resultsDiv.innerHTML = '<div style="padding:12px;color:var(--color-text-secondary);font-size:13px;">No se encontraron productos</div>';
      resultsDiv.style.display = 'block';
      return;
    }
    
    resultsDiv.innerHTML = productos.map(p => `
      <div class="search-result-item" onclick='seleccionarProducto(${JSON.stringify(p)})'>
        <div><strong>${p.nombre}</strong></div>
        <div style="font-size:12px;color:var(--color-text-secondary);margin-top:2px;">
          INVIMA: ${p.codigo_invima} · Stock: ${p.stock_actual} unidades
        </div>
      </div>
    `).join('');
    resultsDiv.style.display = 'block';
  } catch (error) {
    console.error('Error buscando productos:', error);
  }
}

function seleccionarProducto(producto) {
  productoSeleccionado = producto;
  document.getElementById('productoIdHidden').value = producto.producto_id;
  document.getElementById('buscarProducto').value = producto.nombre;
  document.getElementById('searchResults').style.display = 'none';
  
  // Mostrar preview
  const preview = document.getElementById('productPreview');
  document.getElementById('previewNombre').textContent = producto.nombre;
  document.getElementById('previewSub').textContent = `INVIMA: ${producto.codigo_invima} · ${producto.proveedor_nombre || 'Sin laboratorio'}`;
  
  const chips = [];
  if (producto.stock_actual <= producto.stock_minimo) {
    chips.push(`<span class="chip chip-warn"><i data-lucide="alert-triangle"></i> Stock actual: <strong>${producto.stock_actual} unidades</strong></span>`);
  }
  chips.push(`<span class="chip chip-muted"><i data-lucide="layers"></i> Stock mínimo: ${producto.stock_minimo} unidades</span>`);
  
  if (!producto.control_especial) {
    chips.push(`<span class="badge-libre"><i data-lucide="circle-check"></i> Venta libre</span>`);
  }
  
  document.getElementById('previewChips').innerHTML = chips.join('');
  preview.style.display = 'flex';
  
  if (window.lucide) lucide.createIcons();
  validarFormulario();
}

// Validación de fecha de vencimiento
document.getElementById('fechaVencimiento')?.addEventListener('change', function() {
  const fechaVenc = new Date(this.value);
  const hoy = new Date();
  hoy.setHours(0, 0, 0, 0);
  
  const diffTime = fechaVenc - hoy;
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  
  const diasSpan = document.getElementById('diasRestantes');
  const alertVenc = document.getElementById('alertVencimiento');
  const alertVencido = document.getElementById('alertVencido');
  
  if (diffDays < 0) {
    diasSpan.textContent = 'La fecha de vencimiento ya pasó';
    diasSpan.style.color = 'var(--color-error)';
    alertVencido.style.display = 'flex';
    alertVenc.style.display = 'none';
    this.setCustomValidity('La fecha debe ser futura');
  } else if (diffDays <= 30) {
    diasSpan.textContent = `Vence en ${diffDays} días`;
    diasSpan.style.color = 'var(--color-warning)';
    alertVenc.style.display = 'flex';
    alertVencido.style.display = 'none';
    this.setCustomValidity('');
  } else {
    diasSpan.textContent = `Vence en ${diffDays} días`;
    diasSpan.style.color = 'var(--color-success)';
    alertVenc.style.display = 'none';
    alertVencido.style.display = 'none';
    this.setCustomValidity('');
  }
  
  validarFormulario();
});

function validarFormulario() {
  const productoId = document.getElementById('productoIdHidden')?.value;
  const numeroLote = document.querySelector('[name="numero_lote"]')?.value;
  const cantidad = document.querySelector('[name="cantidad_inicial"]')?.value;
  const fechaVenc = document.querySelector('[name="fecha_vencimiento"]')?.value;
  
  const valido = productoId && numeroLote && cantidad && fechaVenc;
  document.getElementById('submitBtn').disabled = !valido;
}

// Validar en cada cambio
document.querySelectorAll('#loteForm input, #loteForm select').forEach(campo => {
  campo.addEventListener('input', validarFormulario);
  campo.addEventListener('change', validarFormulario);
});
</script>

</body>
</html>
