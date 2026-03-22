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
  <style>
    :root{--color-primary:#1A6B8A;--color-primary-dark:#1A3A4A;--color-primary-light:#4A9BB5;--color-secondary:#2A9D8F;--color-bg-main:#F4F9FC;--color-bg-card:#E9F5F8;--color-success:#27AE60;--color-error:#E74C3C;--color-success-soft:#EAFAF1;--color-error-soft:#FDEDEC;--color-text-primary:#2C3E50;--color-text-secondary:#7F8C8D;--color-border:#BDC3C7;--sidebar-width:240px;--topbar-height:64px;--radius-md:8px;--radius-lg:12px;--radius-full:9999px;--font-main:'Inter',sans-serif;--shadow-card:0 2px 8px rgba(0,0,0,0.08);}
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
    .topbar-icon-btn{width:36px;height:36px;border-radius:var(--radius-md);background:none;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--color-text-secondary);transition:background .15s,color .15s;}
    .topbar-icon-btn:hover{background:var(--color-bg-main);color:var(--color-primary);}
    .topbar-icon-btn svg{width:20px;height:20px;}
    .topbar-user{display:flex;align-items:center;gap:8px;padding:4px 8px 4px 4px;border-radius:var(--radius-md);cursor:pointer;transition:background .15s;border:none;background:none;}
    .topbar-user:hover{background:var(--color-bg-main);}
    .topbar-avatar{width:32px;height:32px;border-radius:50%;background:var(--color-primary);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;}
    .topbar-user-name{font-size:13px;font-weight:600;color:var(--color-text-primary);}
    .topbar-user-role{font-size:11px;color:var(--color-text-secondary);}
    .page-header{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:24px;}
    .page-title-block h1{font-size:22px;font-weight:700;color:var(--color-text-primary);display:flex;align-items:center;gap:10px;letter-spacing:-.3px;}
    .page-title-block p{font-size:13px;color:var(--color-text-secondary);margin-top:3px;}
    .btn-primary{height:42px;background:var(--color-primary);color:#fff;border:none;border-radius:var(--radius-md);font-family:var(--font-main);font-size:14px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;padding:0 18px;transition:background .2s;text-decoration:none;}
    .btn-primary:hover{background:var(--color-primary-light);}
    .btn-sm{height:34px;border:1.5px solid var(--color-border);border-radius:var(--radius-md);background:transparent;font-family:var(--font-main);font-size:13px;font-weight:500;color:var(--color-text-primary);cursor:pointer;display:inline-flex;align-items:center;gap:6px;padding:0 12px;text-decoration:none;transition:border-color .2s,color .2s;}
    .btn-sm:hover{border-color:var(--color-primary);color:var(--color-primary);}
    .btn-sm svg{width:14px;height:14px;}
    .filters-bar{background:#fff;border:1px solid var(--color-border);border-radius:var(--radius-lg);padding:14px 20px;display:flex;align-items:center;gap:12px;margin-bottom:16px;box-shadow:var(--shadow-card);}
    .search-wrap{position:relative;flex:1;}
    .search-wrap svg{position:absolute;left:12px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:var(--color-text-secondary);}
    .search-input{height:38px;border:1.5px solid var(--color-border);border-radius:var(--radius-md);padding:0 14px 0 38px;font-family:var(--font-main);font-size:14px;width:100%;outline:none;transition:border .15s;}
    .search-input:focus{border-color:var(--color-primary);}
    .table-card{background:#fff;border:1px solid var(--color-border);border-radius:var(--radius-lg);box-shadow:var(--shadow-card);overflow:hidden;}
    .table-header{padding:16px 20px;border-bottom:1px solid var(--color-border);display:flex;align-items:center;justify-content:space-between;background:var(--color-bg-main);}
    .table-header-title{font-size:15px;font-weight:600;color:var(--color-text-primary);display:flex;align-items:center;gap:8px;}
    .table-header-title svg{width:16px;height:16px;color:var(--color-primary);}
    .table-wrap{overflow-x:auto;}
    table{width:100%;border-collapse:collapse;font-size:14px;}
    thead tr{background:var(--color-bg-main);}
    th{padding:12px 16px;text-align:left;font-size:12px;font-weight:600;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:.8px;border-bottom:2px solid var(--color-border);}
    td{padding:13px 16px;border-bottom:1px solid var(--color-border);vertical-align:middle;}
    tbody tr:hover{background:var(--color-bg-main);}
    tbody tr:last-child td{border-bottom:none;}
    .badge-active{display:inline-flex;align-items:center;gap:4px;background:var(--color-success-soft);color:var(--color-success);padding:3px 10px;border-radius:var(--radius-full);font-size:11px;font-weight:600;}
    .empty-state{padding:60px 20px;text-align:center;color:var(--color-text-secondary);}
    .flash{padding:12px 16px;border-radius:var(--radius-md);margin-bottom:16px;font-size:14px;display:flex;align-items:center;gap:10px;}
    .flash svg{width:17px;height:17px;flex-shrink:0;}
    .flash.success{background:var(--color-success-soft);border-left:4px solid var(--color-success);color:#1a5c35;}
    .flash.success svg{color:var(--color-success);}
    .flash.error{background:var(--color-error-soft);border-left:4px solid var(--color-error);color:#7B241C;}
  </style>
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
