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
