<?php
// views/inventario/proveedor_form.php
// Variables: $proveedor (array|null), $basePath
$esEdicion = !empty($proveedor);
$pageTitle = $esEdicion ? 'Editar Proveedor' : 'Nuevo Proveedor';
$actionUrl = $esEdicion
    ? $basePath . '/inventario/proveedores/' . $proveedor['proveedor_id'] . '/editar'
    : $basePath . '/inventario/proveedores/crear';
$iniciales = strtoupper(substr($_SESSION['nombres'] ?? 'U', 0, 1) . substr($_SESSION['apellidos'] ?? 'S', 0, 1));
$nombre = htmlspecialchars($_SESSION['nombres'] ?? '');
$rol    = htmlspecialchars($_SESSION['rol'] ?? '');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= $pageTitle ?> | FarmaPlus CRM</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= $basePath ?>/assets/css/app.min.css" />
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    :root{--color-primary:#1A6B8A;--color-primary-dark:#1A3A4A;--color-primary-light:#4A9BB5;--color-secondary:#2A9D8F;--color-bg-main:#F4F9FC;--color-bg-card:#E9F5F8;--color-success:#27AE60;--color-error:#E74C3C;--color-text-primary:#2C3E50;--color-text-secondary:#7F8C8D;--color-border:#BDC3C7;--sidebar-width:240px;--topbar-height:64px;--radius-md:8px;--radius-lg:12px;--radius-full:9999px;--font-main:'Inter',sans-serif;--font-mono:'JetBrains Mono',monospace;--shadow-card:0 2px 8px rgba(0,0,0,0.08);--shadow-focus:0 0 0 3px rgba(26,107,138,0.15);}
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    html,body{height:100%;font-family:var(--font-main);font-size:16px;color:var(--color-text-primary);background:var(--color-bg-main);}
    .app-shell{display:flex;min-height:100vh;}
    .main-content{margin-left:var(--sidebar-width);margin-top:var(--topbar-height);padding:28px 32px 28px 20px;flex:1;}
    .topbar{height:var(--topbar-height);background:#fff;border-bottom:1px solid var(--color-border);box-shadow:0 1px 4px rgba(0,0,0,0.06);display:flex;align-items:center;padding:0 24px 0 calc(var(--sidebar-width) + 20px);position:fixed;top:0;left:0;right:0;z-index:50;gap:16px;}
    .breadcrumb{display:flex;align-items:center;gap:6px;flex:1;}
    .breadcrumb-item{font-size:13px;color:var(--color-text-secondary);text-decoration:none;transition:color .15s;display:flex;align-items:center;gap:4px;}
    .breadcrumb-item:hover{color:var(--color-primary);}
    .breadcrumb-item.current{font-weight:600;color:var(--color-text-primary);pointer-events:none;}
    .breadcrumb-sep{color:var(--color-border);}
    .topbar-actions{display:flex;align-items:center;gap:8px;}
    .topbar-icon-btn{width:36px;height:36px;border-radius:var(--radius-md);background:none;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--color-text-secondary);}
    .topbar-icon-btn svg{width:20px;height:20px;}
    .topbar-user{display:flex;align-items:center;gap:8px;padding:4px 8px 4px 4px;border-radius:var(--radius-md);cursor:pointer;border:none;background:none;}
    .topbar-avatar{width:32px;height:32px;border-radius:50%;background:var(--color-primary);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;}
    .topbar-user-name{font-size:13px;font-weight:600;color:var(--color-text-primary);}
    .topbar-user-role{font-size:11px;color:var(--color-text-secondary);}
    .page-header{display:flex;align-items:center;gap:16px;margin-bottom:24px;}
    .back-btn{width:38px;height:38px;border-radius:var(--radius-md);background:#fff;border:1.5px solid var(--color-border);cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--color-text-secondary);transition:all .15s;text-decoration:none;}
    .back-btn:hover{border-color:var(--color-primary);color:var(--color-primary);}
    .back-btn svg{width:18px;height:18px;}
    .page-title-block h1{font-size:22px;font-weight:700;color:var(--color-text-primary);display:flex;align-items:center;gap:10px;}
    .page-title-block p{font-size:13px;color:var(--color-text-secondary);margin-top:3px;}
    .card{background:#fff;border:1px solid var(--color-border);border-radius:var(--radius-lg);box-shadow:var(--shadow-card);overflow:hidden;margin-bottom:20px;}
    .card:last-child{margin-bottom:0;}
    .card-header{padding:16px 20px;border-bottom:1px solid var(--color-border);background:var(--color-bg-main);}
    .card-header-title{display:flex;align-items:center;gap:8px;font-size:15px;font-weight:600;color:var(--color-text-primary);}
    .card-header-title svg{width:16px;height:16px;color:var(--color-primary);}
    .card-header-desc{font-size:13px;color:var(--color-text-secondary);margin-top:2px;}
    .card-body{padding:20px;}
    .form-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:20px;}
    .form-field{display:flex;flex-direction:column;gap:6px;}
    .form-label{font-size:14px;font-weight:500;color:var(--color-text-primary);}
    .form-label .required{color:var(--color-error);}
    .form-input,.form-select{height:48px;border:1.5px solid var(--color-border);border-radius:var(--radius-md);padding:0 16px;font-family:var(--font-main);font-size:15px;color:var(--color-text-primary);background:#fff;transition:border .15s,box-shadow .15s;outline:none;width:100%;}
    .form-input:focus,.form-select:focus{border-color:var(--color-primary);box-shadow:var(--shadow-focus);}
    .form-help{font-size:12px;color:var(--color-text-secondary);}
    .form-actions-bar{background:#fff;border:1px solid var(--color-border);border-radius:var(--radius-lg);padding:18px 20px;display:flex;align-items:center;gap:12px;box-shadow:var(--shadow-card);}
    .btn-primary{height:42px;background:var(--color-primary);color:#fff;border:none;border-radius:var(--radius-md);font-family:var(--font-main);font-size:14px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;padding:0 18px;transition:background .2s;}
    .btn-primary:hover{background:var(--color-primary-light);}
    .btn-primary svg{width:16px;height:16px;}
    .btn-secondary-cancel{height:42px;background:transparent;border:1.5px solid var(--color-border);border-radius:var(--radius-md);font-family:var(--font-main);font-size:14px;font-weight:500;color:var(--color-text-primary);cursor:pointer;display:inline-flex;align-items:center;gap:7px;padding:0 16px;transition:border-color .2s;text-decoration:none;}
    .btn-secondary-cancel:hover{border-color:var(--color-primary);}
    .btn-secondary-cancel svg{width:15px;height:15px;}
    .flash{padding:12px 16px;border-radius:var(--radius-md);margin-bottom:16px;font-size:14px;display:flex;align-items:center;gap:10px;}
    .flash svg{width:17px;height:17px;flex-shrink:0;}
    .flash.error{background:#FDEDEC;border-left:4px solid var(--color-error);color:#7B241C;}
    .flash.error svg{color:var(--color-error);}
  </style>
</head>
<body>
<div class="app-shell">

  <?php include __DIR__ . '/../layouts/sidebar.php'; ?>

  <header class="topbar">
    <nav class="breadcrumb">
      <a href="<?= $basePath ?>/dashboard" class="breadcrumb-item"><i data-lucide="home" style="width:13px;height:13px;"></i> Inicio</a>
      <span class="breadcrumb-sep">/</span>
      <a href="<?= $basePath ?>/inventario/proveedores" class="breadcrumb-item">Proveedores</a>
      <span class="breadcrumb-sep">/</span>
      <span class="breadcrumb-item current"><?= $esEdicion ? 'Editar' : 'Nuevo' ?></span>
    </nav>
    <div class="topbar-actions">
      <button class="topbar-icon-btn"><i data-lucide="bell"></i></button>
      <div class="topbar-user">
        <div class="topbar-avatar"><?= $iniciales ?></div>
        <div>
          <div class="topbar-user-name"><?= $nombre ?></div>
          <div class="topbar-user-role"><?= $rol ?></div>
        </div>
      </div>
    </div>
  </header>

  <main class="main-content">

    <?php if (!empty($_GET['error'])): ?>
    <div class="flash error"><i data-lucide="alert-circle"></i><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <div class="page-header">
      <a href="<?= $basePath ?>/inventario/proveedores" class="back-btn"><i data-lucide="arrow-left"></i></a>
      <div class="page-title-block">
        <h1><i data-lucide="building-2"></i> <?= $pageTitle ?></h1>
        <p><?= $esEdicion ? 'Modifica los datos del proveedor registrado.' : 'Añade un nuevo laboratorio o distribuidor al sistema.' ?></p>
      </div>
    </div>

    <form method="POST" action="<?= $actionUrl ?>">

      <div class="card">
        <div class="card-header">
          <div class="card-header-title"><i data-lucide="building-2"></i> Datos del proveedor</div>
          <div class="card-header-desc">Información fiscal y de contacto del proveedor.</div>
        </div>
        <div class="card-body">
          <div class="form-grid-2">

            <div class="form-field">
              <label class="form-label">Nombre del proveedor <span class="required">*</span></label>
              <input type="text" name="nombre" class="form-input"
                     value="<?= htmlspecialchars($proveedor['nombre'] ?? '') ?>"
                     placeholder="Ej: Laboratorios MK" required />
            </div>

            <div class="form-field">
              <label class="form-label">NIT <span class="required">*</span></label>
              <input type="text" name="nit" class="form-input"
                     value="<?= htmlspecialchars($proveedor['nit'] ?? '') ?>"
                     placeholder="Ej: 800123456-7"
                     <?= $esEdicion ? 'readonly style="background:var(--color-bg-main);color:var(--color-text-secondary);"' : '' ?>
                     required />
              <?php if ($esEdicion): ?>
              <span class="form-help">El NIT no puede modificarse después del registro.</span>
              <?php else: ?>
              <span class="form-help">Número único del proveedor (HU-AUX-03). No puede repetirse.</span>
              <?php endif; ?>
            </div>

            <div class="form-field">
              <label class="form-label">País de origen</label>
              <input type="text" name="pais_origen" class="form-input"
                     value="<?= htmlspecialchars($proveedor['pais_origen'] ?? 'Colombia') ?>"
                     placeholder="Colombia" />
            </div>

            <div class="form-field">
              <label class="form-label">Teléfono</label>
              <input type="text" name="telefono" class="form-input"
                     value="<?= htmlspecialchars($proveedor['telefono'] ?? '') ?>"
                     placeholder="+57 300 000 0000" />
            </div>

            <div class="form-field">
              <label class="form-label">Correo electrónico</label>
              <input type="email" name="correo" class="form-input"
                     value="<?= htmlspecialchars($proveedor['correo'] ?? '') ?>"
                     placeholder="ventas@proveedor.com" />
            </div>

            <div class="form-field">
              <label class="form-label">Sitio web</label>
              <input type="url" name="sitio_web" class="form-input"
                     value="<?= htmlspecialchars($proveedor['sitio_web'] ?? '') ?>"
                     placeholder="https://www.proveedor.com" />
            </div>

          </div>
        </div>
      </div>

      <div class="form-actions-bar">
        <a href="<?= $basePath ?>/inventario/proveedores" class="btn-secondary-cancel">
          <i data-lucide="x"></i> Cancelar
        </a>
        <button type="submit" class="btn-primary">
          <i data-lucide="save"></i> <?= $esEdicion ? 'Guardar cambios' : 'Crear proveedor' ?>
        </button>
      </div>

    </form>
  </main>
</div>
<script src="<?= $basePath ?>/assets/js/app.js"></script>
<script>if (window.lucide) lucide.createIcons();</script>
</body>
</html>
