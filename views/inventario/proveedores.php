<?php
// views/inventario/proveedores.php
// Variables esperadas: $proveedores, $basePath, $iniciales, $nombre, $rol
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Proveedores — FarmaPlus CRM</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet" />
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
      <a href="<?= $basePath ?>/inventario/productos" class="breadcrumb-item">Inventario</a>
      <span class="breadcrumb-sep">/</span>
      <span class="breadcrumb-item current">Proveedores</span>
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

    <?php if (!empty($_GET['success'])): ?>
    <div class="flash success"><i data-lucide="circle-check"></i><?= htmlspecialchars($_GET['success']) ?></div>
    <?php elseif (!empty($_GET['error'])): ?>
    <div class="flash error"><i data-lucide="alert-circle"></i><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <div class="page-header">
      <div class="page-title-block">
        <h1><i data-lucide="building-2"></i> Proveedores</h1>
        <p>Laboratorios y distribuidores registrados en el sistema</p>
      </div>
      <a href="<?= $basePath ?>/inventario/proveedores/crear" class="btn-primary">
        <i data-lucide="plus"></i> Nuevo proveedor
      </a>
    </div>

    <div class="filters-bar">
      <div class="search-wrap">
        <i data-lucide="search"></i>
        <input type="text" class="search-input" id="buscarProv" placeholder="Buscar por nombre o NIT..." />
      </div>
    </div>

    <div class="table-card">
      <div class="table-header">
        <div class="table-header-title"><i data-lucide="building-2"></i> Listado de proveedores</div>
        <span style="font-size:13px;color:var(--color-text-secondary);"><?= count($proveedores) ?> registros</span>
      </div>

      <?php if (empty($proveedores)): ?>
      <div class="empty-state">
        <i data-lucide="building-2" style="width:48px;height:48px;color:var(--color-border);display:block;margin:0 auto 12px;"></i>
        <p>No hay proveedores registrados</p>
        <span>Añade el primer proveedor o laboratorio distribuidor.</span>
      </div>
      <?php else: ?>
      <div class="table-wrap">
        <table id="tablaProv">
          <thead>
            <tr>
              <th>Nombre del proveedor</th>
              <th>NIT</th>
              <th>País</th>
              <th>Teléfono</th>
              <th>Correo</th>
              <th>Estado</th>
              <th style="text-align:center;">Acciones</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($proveedores as $prov): ?>
            <tr data-nombre="<?= strtolower(htmlspecialchars($prov['nombre'])) ?>" data-nit="<?= htmlspecialchars($prov['nit']) ?>">
              <td>
                <div style="font-weight:600;"><?= htmlspecialchars($prov['nombre']) ?></div>
                <?php if ($prov['sitio_web']): ?>
                <div style="font-size:12px;"><a href="<?= htmlspecialchars($prov['sitio_web']) ?>" target="_blank" style="color:var(--color-primary);text-decoration:none;"><?= htmlspecialchars($prov['sitio_web']) ?></a></div>
                <?php endif; ?>
              </td>
              <td><span style="font-family:monospace;font-size:13px;"><?= htmlspecialchars($prov['nit']) ?></span></td>
              <td><?= htmlspecialchars($prov['pais_origen'] ?? 'Colombia') ?></td>
              <td><?= htmlspecialchars($prov['telefono'] ?: '—') ?></td>
              <td><?= htmlspecialchars($prov['correo'] ?: '—') ?></td>
              <td>
                <span class="badge-active"><i data-lucide="circle-check" style="width:11px;height:11px;"></i> Activo</span>
              </td>
              <td style="text-align:center;">
                <a href="<?= $basePath ?>/inventario/proveedores/<?= $prov['proveedor_id'] ?>/editar" class="btn-sm">
                  <i data-lucide="pencil"></i> Editar
                </a>
              </td>
            </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
      <?php endif; ?>
    </div>
  </main>
</div>
<script src="<?= $basePath ?>/assets/js/app.js"></script>
<script>
  if (window.lucide) lucide.createIcons();
  document.getElementById('buscarProv')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#tablaProv tbody tr').forEach(row => {
      row.style.display = (!q || row.dataset.nombre.includes(q) || row.dataset.nit.includes(q)) ? '' : 'none';
    });
  });
</script>
</body>
</html>
