<?php
// views/inventario/lotes.php
// Variables esperadas: $lotes (array), $basePath (string), $iniciales, $nombre, $rol
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Lotes — FarmaPlus CRM</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="<?= $basePath ?>/assets/css/app.min.css" />
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    :root {
      --color-primary:#1A6B8A;--color-primary-dark:#1A3A4A;--color-primary-light:#4A9BB5;
      --color-secondary:#2A9D8F;--color-bg-main:#F4F9FC;--color-bg-card:#E9F5F8;
      --color-success:#27AE60;--color-error:#E74C3C;--color-warning:#F39C12;
      --color-success-soft:#EAFAF1;--color-error-soft:#FDEDEC;--color-warning-soft:#FEF9E7;
      --color-text-primary:#2C3E50;--color-text-secondary:#7F8C8D;--color-border:#BDC3C7;
      --color-sidebar-bg:#1A3A4A;--sidebar-width:240px;--topbar-height:64px;
      --radius-md:8px;--radius-lg:12px;--radius-full:9999px;
      --font-main:'Inter',sans-serif;--font-mono:'JetBrains Mono',monospace;
      --shadow-card:0 2px 8px rgba(0,0,0,0.08);
    }
    *,*::before,*::after{box-sizing:border-box;margin:0;padding:0;}
    html,body{height:100%;font-family:var(--font-main);font-size:16px;color:var(--color-text-primary);background:var(--color-bg-main);}
    .app-shell{display:flex;min-height:100vh;}
    .main-content{margin-left:var(--sidebar-width);margin-top:var(--topbar-height);padding:28px 32px 28px 20px;flex:1;}
    .topbar{height:var(--topbar-height);background:#fff;border-bottom:1px solid var(--color-border);box-shadow:0 1px 4px rgba(0,0,0,0.06);display:flex;align-items:center;padding:0 24px 0 calc(var(--sidebar-width) + 20px);position:fixed;top:0;left:0;right:0;z-index:50;gap:16px;}
    .breadcrumb{display:flex;align-items:center;gap:6px;flex:1;}
    .breadcrumb-item{font-size:13px;color:var(--color-text-secondary);text-decoration:none;transition:color .15s;display:flex;align-items:center;gap:4px;}
    .breadcrumb-item:hover{color:var(--color-primary);}
    .breadcrumb-item.current{font-weight:600;color:var(--color-text-primary);pointer-events:none;}
    .breadcrumb-sep{color:var(--color-border);font-size:13px;}
    .topbar-actions{display:flex;align-items:center;gap:8px;}
    .topbar-icon-btn{width:36px;height:36px;border-radius:var(--radius-md);background:none;border:none;cursor:pointer;display:flex;align-items:center;justify-content:center;color:var(--color-text-secondary);transition:background .15s,color .15s;}
    .topbar-icon-btn:hover{background:var(--color-bg-main);color:var(--color-primary);}
    .topbar-icon-btn svg{width:20px;height:20px;}
    .topbar-user{display:flex;align-items:center;gap:8px;padding:4px 8px 4px 4px;border-radius:var(--radius-md);cursor:pointer;transition:background .15s;border:none;background:none;}
    .topbar-user:hover{background:var(--color-bg-main);}
    .topbar-avatar{width:32px;height:32px;border-radius:50%;background:var(--color-primary);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;}
    .topbar-user-name{font-size:13px;font-weight:600;color:var(--color-text-primary);}
    .topbar-user-role{font-size:11px;color:var(--color-text-secondary);}

    /* Page header */
    .page-header{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:24px;}
    .page-header-left{display:flex;align-items:center;gap:14px;}
    .page-title-block h1{font-size:22px;font-weight:700;color:var(--color-text-primary);display:flex;align-items:center;gap:10px;letter-spacing:-.3px;}
    .page-title-block p{font-size:13px;color:var(--color-text-secondary);margin-top:3px;}
    .btn-primary{height:42px;background:var(--color-primary);color:#fff;border:none;border-radius:var(--radius-md);font-family:var(--font-main);font-size:14px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:8px;padding:0 18px;transition:background .2s,box-shadow .2s;text-decoration:none;}
    .btn-primary:hover{background:var(--color-primary-light);box-shadow:0 4px 16px rgba(26,107,138,.28);}
    .btn-primary svg{width:16px;height:16px;}
    .btn-secondary{height:36px;border:1.5px solid var(--color-border);border-radius:var(--radius-md);font-family:var(--font-main);font-size:13px;font-weight:500;color:var(--color-text-primary);cursor:pointer;display:inline-flex;align-items:center;gap:7px;padding:0 14px;transition:border-color .2s,background .2s;text-decoration:none;background:transparent;}
    .btn-secondary:hover{border-color:var(--color-primary);background:var(--color-bg-card);color:var(--color-primary);}

    /* Estadísticas rápidas */
    .stats-row{display:grid;grid-template-columns:repeat(4,1fr);gap:16px;margin-bottom:24px;}
    .stat-card{background:#fff;border:1px solid var(--color-border);border-radius:var(--radius-lg);padding:16px 20px;display:flex;align-items:center;gap:14px;box-shadow:var(--shadow-card);}
    .stat-icon{width:42px;height:42px;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
    .stat-icon svg{width:20px;height:20px;}
    .stat-icon.blue{background:#EBF5FB;color:var(--color-primary);}
    .stat-icon.green{background:var(--color-success-soft);color:var(--color-success);}
    .stat-icon.warn{background:var(--color-warning-soft);color:var(--color-warning);}
    .stat-icon.red{background:var(--color-error-soft);color:var(--color-error);}
    .stat-val{font-size:24px;font-weight:700;color:var(--color-text-primary);line-height:1;}
    .stat-label{font-size:12px;color:var(--color-text-secondary);margin-top:3px;}

    /* Filtros */
    .filters-bar{background:#fff;border:1px solid var(--color-border);border-radius:var(--radius-lg);padding:14px 20px;display:flex;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:16px;box-shadow:var(--shadow-card);}
    .search-wrap{position:relative;flex:1;min-width:220px;}
    .search-wrap svg{position:absolute;left:12px;top:50%;transform:translateY(-50%);width:16px;height:16px;color:var(--color-text-secondary);}
    .search-input{height:38px;border:1.5px solid var(--color-border);border-radius:var(--radius-md);padding:0 14px 0 38px;font-family:var(--font-main);font-size:14px;color:var(--color-text-primary);background:#fff;outline:none;width:100%;transition:border .15s;}
    .search-input:focus{border-color:var(--color-primary);}
    .filter-select{height:38px;border:1.5px solid var(--color-border);border-radius:var(--radius-md);padding:0 36px 0 12px;font-family:var(--font-main);font-size:13px;color:var(--color-text-primary);background:#fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='14' height='14' viewBox='0 0 24 24' fill='none' stroke='%237F8C8D' stroke-width='2'%3E%3Cpolyline points='6 9 12 15 18 9'%3E%3C/polyline%3E%3C/svg%3E") no-repeat right 12px center;appearance:none;outline:none;transition:border .15s;cursor:pointer;}
    .filter-select:focus{border-color:var(--color-primary);}

    /* Tabla */
    .table-card{background:#fff;border:1px solid var(--color-border);border-radius:var(--radius-lg);box-shadow:var(--shadow-card);overflow:hidden;}
    .table-header{padding:16px 20px;border-bottom:1px solid var(--color-border);display:flex;align-items:center;justify-content:space-between;background:var(--color-bg-main);}
    .table-header-title{font-size:15px;font-weight:600;color:var(--color-text-primary);display:flex;align-items:center;gap:8px;}
    .table-header-title svg{width:16px;height:16px;color:var(--color-primary);}
    .table-wrap{overflow-x:auto;}
    table{width:100%;border-collapse:collapse;font-size:14px;}
    thead tr{background:var(--color-bg-main);}
    th{padding:12px 16px;text-align:left;font-size:12px;font-weight:600;color:var(--color-text-secondary);text-transform:uppercase;letter-spacing:.8px;white-space:nowrap;border-bottom:2px solid var(--color-border);}
    td{padding:13px 16px;border-bottom:1px solid var(--color-border);vertical-align:middle;}
    tbody tr:hover{background:var(--color-bg-main);}
    tbody tr:last-child td{border-bottom:none;}
    .mono{font-family:var(--font-mono);font-size:13px;}

    /* Badges de estado */
    .badge{display:inline-flex;align-items:center;gap:4px;padding:3px 10px;border-radius:var(--radius-full);font-size:11px;font-weight:600;white-space:nowrap;}
    .badge svg{width:11px;height:11px;}
    .badge-ok{background:var(--color-success-soft);color:var(--color-success);}
    .badge-warn{background:var(--color-warning-soft);color:var(--color-warning);}
    .badge-crit{background:var(--color-error-soft);color:var(--color-error);}
    .badge-muted{background:#F0F3F4;color:var(--color-text-secondary);}

    /* Semáforo de días */
    .days-pill{display:inline-flex;align-items:center;gap:5px;padding:3px 10px;border-radius:var(--radius-full);font-size:12px;font-weight:600;}
    .days-pill svg{width:12px;height:12px;}
    .days-pill.ok{background:var(--color-success-soft);color:var(--color-success);}
    .days-pill.warn{background:var(--color-warning-soft);color:var(--color-warning);}
    .days-pill.crit{background:var(--color-error-soft);color:var(--color-error);}

    /* Progress bar stock */
    .stock-bar-wrap{display:flex;align-items:center;gap:8px;}
    .stock-bar-bg{flex:1;height:6px;background:#EEF2F3;border-radius:3px;overflow:hidden;}
    .stock-bar-fill{height:100%;border-radius:3px;transition:width .4s;}
    .stock-val{font-size:13px;font-weight:600;min-width:40px;text-align:right;}

    .empty-state{padding:60px 20px;text-align:center;color:var(--color-text-secondary);}
    .empty-state svg{width:48px;height:48px;color:var(--color-border);margin-bottom:12px;}
    .empty-state p{font-size:15px;margin-bottom:4px;color:var(--color-text-primary);}
    .empty-state span{font-size:13px;}

    /* Success/Error flash */
    .flash{padding:12px 16px;border-radius:var(--radius-md);margin-bottom:16px;font-size:14px;display:flex;align-items:center;gap:10px;}
    .flash svg{width:17px;height:17px;flex-shrink:0;}
    .flash.success{background:var(--color-success-soft);border-left:4px solid var(--color-success);color:#1a5c35;}
    .flash.success svg{color:var(--color-success);}
    .flash.error{background:var(--color-error-soft);border-left:4px solid var(--color-error);color:#7B241C;}
    .flash.error svg{color:var(--color-error);}
  </style>
</head>
<body>
<div class="app-shell">

  <?php include __DIR__ . '/../layouts/sidebar.php'; ?>

  <header class="topbar">
    <nav class="breadcrumb">
      <a href="<?= $basePath ?>/dashboard" class="breadcrumb-item">
        <i data-lucide="home" style="width:13px;height:13px;"></i> Inicio
      </a>
      <span class="breadcrumb-sep">/</span>
      <a href="<?= $basePath ?>/inventario/productos" class="breadcrumb-item">Inventario</a>
      <span class="breadcrumb-sep">/</span>
      <span class="breadcrumb-item current">Lotes</span>
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

    <?php
    $hoy = new \DateTime();
    $lotesOk = 0; $lotesWarn = 0; $lotesCrit = 0; $totalUnidades = 0;
    foreach ($lotes as $l) {
      $totalUnidades += (int)($l['cantidad_actual'] ?? 0);
      $fv = new \DateTime($l['fecha_vencimiento'] ?? 'today');
      $dias = (int)$hoy->diff($fv)->days * ($fv >= $hoy ? 1 : -1);
      if ($dias < 0 || $l['cantidad_actual'] == 0) { $lotesCrit++; }
      elseif ($dias <= 30) { $lotesWarn++; }
      else { $lotesOk++; }
    }
    $totalLotes = count($lotes);
    ?>

    <?php if (!empty($_GET['success'])): ?>
    <div class="flash success"><i data-lucide="circle-check"></i><?= htmlspecialchars($_GET['success']) ?></div>
    <?php elseif (!empty($_GET['error'])): ?>
    <div class="flash error"><i data-lucide="alert-circle"></i><?= htmlspecialchars($_GET['error']) ?></div>
    <?php endif; ?>

    <div class="page-header">
      <div class="page-header-left">
        <div class="page-title-block">
          <h1><i data-lucide="layers"></i> Control de Lotes</h1>
          <p>Gestión de lotes por método FEFO — First Expired, First Out</p>
        </div>
      </div>
      <a href="<?= $basePath ?>/inventario/lotes/registrar" class="btn-primary">
        <i data-lucide="plus"></i> Registrar lote
      </a>
    </div>

    <!-- Stats -->
    <div class="stats-row">
      <div class="stat-card">
        <div class="stat-icon blue"><i data-lucide="layers"></i></div>
        <div><div class="stat-val"><?= $totalLotes ?></div><div class="stat-label">Total de lotes</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon green"><i data-lucide="package"></i></div>
        <div><div class="stat-val"><?= number_format($totalUnidades) ?></div><div class="stat-label">Unidades en stock</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon warn"><i data-lucide="alert-triangle"></i></div>
        <div><div class="stat-val"><?= $lotesWarn ?></div><div class="stat-label">Vencen en < 30 días</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon red"><i data-lucide="x-circle"></i></div>
        <div><div class="stat-val"><?= $lotesCrit ?></div><div class="stat-label">Agotados / Vencidos</div></div>
      </div>
    </div>

    <!-- Filtros -->
    <div class="filters-bar">
      <div class="search-wrap">
        <i data-lucide="search"></i>
        <input type="text" class="search-input" id="buscarLote" placeholder="Buscar por producto o número de lote..." />
      </div>
      <select class="filter-select" id="filtroEstado">
        <option value="">Todos los estados</option>
        <option value="ok">Vigentes (> 30 días)</option>
        <option value="warn">Por vencer (≤ 30 días)</option>
        <option value="crit">Agotados / Vencidos</option>
      </select>
      <a href="<?= $basePath ?>/inventario/lotes?export=1" class="btn-secondary">
        <i data-lucide="download"></i> Exportar
      </a>
    </div>

    <!-- Tabla de lotes -->
    <div class="table-card">
      <div class="table-header">
        <div class="table-header-title"><i data-lucide="layers"></i> Lotes registrados</div>
        <span style="font-size:13px;color:var(--color-text-secondary);"><?= $totalLotes ?> lotes</span>
      </div>

      <?php if (empty($lotes)): ?>
      <div class="empty-state">
        <i data-lucide="inbox"></i>
        <p>No hay lotes registrados aún</p>
        <span>Registra el primer lote de medicamentos para iniciar el control FEFO.</span>
        <br><br>
        <a href="<?= $basePath ?>/inventario/lotes/registrar" class="btn-primary" style="display:inline-flex;">
          <i data-lucide="plus"></i> Registrar primer lote
        </a>
      </div>
      <?php else: ?>
      <div class="table-wrap">
        <table id="tablaLotes">
          <thead>
            <tr>
              <th>Nº Lote</th>
              <th>Producto</th>
              <th>Vencimiento</th>
              <th>Días restantes</th>
              <th>Stock inicial</th>
              <th>Stock actual</th>
              <th>Proveedor</th>
              <th>Estado</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($lotes as $lote):
              $fv = new \DateTime($lote['fecha_vencimiento']);
              $diffInterval = $hoy->diff($fv);
              $dias = (int)$diffInterval->days * ($fv >= $hoy ? 1 : -1);
              $stockActual = (int)($lote['cantidad_actual'] ?? 0);
              $stockInicial = (int)($lote['cantidad_inicial'] ?? 1);
              $pct = $stockInicial > 0 ? min(100, round(($stockActual / $stockInicial) * 100)) : 0;

              // Clase y etiqueta del semáforo
              if ($dias < 0) {
                $pillClass = 'crit'; $pillLabel = 'Vencido';
                $barColor = 'var(--color-error)';
                $badgeClass = 'badge-crit'; $badgeLabel = 'Vencido';
              } elseif ($dias <= 30) {
                $pillClass = 'warn'; $pillLabel = "{$dias} días";
                $barColor = 'var(--color-warning)';
                $badgeClass = 'badge-warn'; $badgeLabel = 'Por vencer';
              } else {
                $pillClass = 'ok'; $pillLabel = "{$dias} días";
                $barColor = 'var(--color-success)';
                $badgeClass = 'badge-ok'; $badgeLabel = 'Vigente';
              }
              if ($stockActual === 0) {
                $badgeClass = 'badge-muted'; $badgeLabel = 'Agotado';
              }

              // Data-filter para JS
              $dataFilter = ($stockActual === 0 || $dias < 0) ? 'crit' : ($dias <= 30 ? 'warn' : 'ok');
            ?>
            <tr data-estado="<?= $dataFilter ?>"
                data-nombre="<?= strtolower(htmlspecialchars($lote['producto_nombre'] ?? '')) ?>"
                data-lote="<?= strtolower(htmlspecialchars($lote['numero_lote'] ?? '')) ?>">
              <td><span class="mono"><?= htmlspecialchars($lote['numero_lote']) ?></span></td>
              <td>
                <div style="font-weight:600;font-size:14px;"><?= htmlspecialchars($lote['producto_nombre'] ?? '—') ?></div>
              </td>
              <td>
                <span class="mono"><?= date('d/m/Y', strtotime($lote['fecha_vencimiento'])) ?></span>
              </td>
              <td>
                <span class="days-pill <?= $pillClass ?>">
                  <i data-lucide="<?= $dias < 0 ? 'x-circle' : ($dias <= 30 ? 'alert-triangle' : 'check-circle') ?>"></i>
                  <?= $pillLabel ?>
                </span>
              </td>
              <td><?= number_format((int)($lote['cantidad_inicial'] ?? 0)) ?> un.</td>
              <td>
                <div class="stock-bar-wrap">
                  <div class="stock-bar-bg">
                    <div class="stock-bar-fill" style="width:<?= $pct ?>%;background:<?= $barColor ?>;"></div>
                  </div>
                  <span class="stock-val" style="color:<?= $barColor ?>;"><?= number_format($stockActual) ?></span>
                </div>
              </td>
              <td><?= htmlspecialchars($lote['proveedor_nombre'] ?? '—') ?></td>
              <td><span class="badge <?= $badgeClass ?>"><i data-lucide="<?= $badgeClass === 'badge-ok' ? 'circle-check' : ($badgeClass === 'badge-muted' ? 'minus-circle' : 'alert-circle') ?>"></i><?= $badgeLabel ?></span></td>
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

// Filtro por texto
document.getElementById('buscarLote')?.addEventListener('input', function() {
  filtrar();
});
// Filtro por estado
document.getElementById('filtroEstado')?.addEventListener('change', function() {
  filtrar();
});

function filtrar() {
  const q     = document.getElementById('buscarLote').value.toLowerCase();
  const est   = document.getElementById('filtroEstado').value;
  document.querySelectorAll('#tablaLotes tbody tr').forEach(row => {
    const nombre = row.dataset.nombre || '';
    const lote   = row.dataset.lote   || '';
    const estado = row.dataset.estado || '';
    const matchQ   = !q   || nombre.includes(q) || lote.includes(q);
    const matchEst = !est || estado === est;
    row.style.display = matchQ && matchEst ? '' : 'none';
  });
}
</script>
</body>
</html>
