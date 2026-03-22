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
