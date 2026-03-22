<?php
// views/inventario/alertas.php
// Variables: $alertas, $alertasStock, $alertasVencimiento, $totalAlertas, $basePath, $iniciales, $nombre, $rol
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Alertas de Inventario — FarmaPlus CRM</title>
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
      <a href="<?= $basePath ?>/inventario/productos" class="breadcrumb-item">Inventario</a>
      <span class="breadcrumb-sep">/</span>
      <span class="breadcrumb-item current">Alertas</span>
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
        <h1><i data-lucide="bell-ring"></i> Alertas de Inventario</h1>
        <p>Alertas automáticas de stock mínimo y vencimiento de lotes</p>
      </div>
    </div>

    <!-- Stats -->
    <div class="stats-row">
      <div class="stat-card">
        <div class="stat-icon red"><i data-lucide="bell-ring"></i></div>
        <div><div class="stat-val"><?= $totalAlertas ?></div><div class="stat-label">Alertas activas</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon red"><i data-lucide="package-minus"></i></div>
        <div><div class="stat-val"><?= count($alertasStock) ?></div><div class="stat-label">Stock mínimo crítico</div></div>
      </div>
      <div class="stat-card">
        <div class="stat-icon warn"><i data-lucide="calendar-x"></i></div>
        <div><div class="stat-val"><?= count($alertasVencimiento) ?></div><div class="stat-label">Lotes por vencer</div></div>
      </div>
    </div>

    <!-- Tabs -->
    <div class="tabs">
      <button class="tab-btn active" onclick="setTab('todas')">
        <i data-lucide="list"></i> Todas
        <?php if ($totalAlertas > 0): ?><span class="tab-count"><?= $totalAlertas ?></span><?php endif; ?>
      </button>
      <button class="tab-btn" onclick="setTab('stock')">
        <i data-lucide="package-minus"></i> Stock mínimo
        <?php if (count($alertasStock) > 0): ?><span class="tab-count"><?= count($alertasStock) ?></span><?php endif; ?>
      </button>
      <button class="tab-btn" onclick="setTab('vencimiento')">
        <i data-lucide="calendar-x"></i> Vencimiento
        <?php if (count($alertasVencimiento) > 0): ?><span class="tab-count warn-bg"><?= count($alertasVencimiento) ?></span><?php endif; ?>
      </button>
    </div>

    <!-- Panel: Todas -->
    <div class="tab-panel active" id="panel-todas">
      <?php if (empty($alertas)): ?>
        <div class="empty-state">
          <div class="empty-icon"><i data-lucide="check-circle"></i></div>
          <p style="font-size:16px;font-weight:600;color:var(--color-text-primary);">¡Sin alertas activas!</p>
          <span>El inventario está en óptimas condiciones.</span>
        </div>
      <?php else: ?>
        <?php foreach ($alertas as $a): 
          $tipo = $a['tipo'];
          $esStock = $tipo === 'stock_minimo';
        ?>
        <div class="alert-card">
          <div class="alert-stripe <?= $esStock ? 'stock' : 'vencimiento' ?>"></div>
          <div class="alert-card-body">
            <div class="alert-icon <?= $esStock ? 'stock' : 'vencimiento' ?>">
              <i data-lucide="<?= $esStock ? 'package-minus' : 'calendar-x' ?>"></i>
            </div>
            <div class="alert-info">
              <div class="alert-title"><?= htmlspecialchars($a['producto_nombre'] ?? '—') ?></div>
              <div class="alert-desc"><?= htmlspecialchars($a['mensaje'] ?? '') ?></div>
              <div class="alert-meta">
                <?php if ($esStock): ?>
                  <span class="meta-chip crit"><i data-lucide="package"></i> Stock actual: <?= (int)($a['stock_actual'] ?? 0) ?> un.</span>
                  <span class="meta-chip"><i data-lucide="alert-triangle"></i> Mínimo: <?= (int)($a['stock_minimo'] ?? 0) ?> un.</span>
                <?php else: ?>
                  <?php if (!empty($a['numero_lote'])): ?>
                  <span class="meta-chip"><i data-lucide="layers"></i> Lote: <?= htmlspecialchars($a['numero_lote']) ?></span>
                  <?php endif; ?>
                  <?php if (!empty($a['fecha_vencimiento'])): 
                    $diasRestantes = (int)(new \DateTime())->diff(new \DateTime($a['fecha_vencimiento']))->days;
                  ?>
                  <span class="meta-chip warn"><i data-lucide="calendar"></i> Vence: <?= date('d/m/Y', strtotime($a['fecha_vencimiento'])) ?> (<?= $diasRestantes ?> días)</span>
                  <?php endif; ?>
                <?php endif; ?>
                <span class="meta-chip"><i data-lucide="clock"></i> <?= date('d/m/Y H:i', strtotime($a['created_at'] ?? 'now')) ?></span>
              </div>
            </div>
            <div class="alert-actions">
              <form method="POST" action="<?= $basePath ?>/inventario/alertas/<?= (int)$a['alerta_id'] ?>/resolver">
                <button type="submit" class="btn-resolver" onclick="return confirm('¿Marcar esta alerta como resuelta?')">
                  <i data-lucide="check"></i> Resolver
                </button>
              </form>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Panel: Stock -->
    <div class="tab-panel" id="panel-stock">
      <?php if (empty($alertasStock)): ?>
        <div class="empty-state">
          <div class="empty-icon"><i data-lucide="check-circle"></i></div>
          <p style="font-size:16px;font-weight:600;color:var(--color-text-primary);">Sin alertas de stock</p>
          <span>Todos los productos están por encima del nivel mínimo.</span>
        </div>
      <?php else: ?>
        <?php foreach ($alertasStock as $a): ?>
        <div class="alert-card">
          <div class="alert-stripe stock"></div>
          <div class="alert-card-body">
            <div class="alert-icon stock"><i data-lucide="package-minus"></i></div>
            <div class="alert-info">
              <div class="alert-title"><?= htmlspecialchars($a['producto_nombre'] ?? '—') ?></div>
              <div class="alert-desc"><?= htmlspecialchars($a['mensaje'] ?? '') ?></div>
              <div class="alert-meta">
                <span class="meta-chip crit"><i data-lucide="package"></i> Stock actual: <?= (int)($a['stock_actual'] ?? 0) ?> un.</span>
                <span class="meta-chip"><i data-lucide="alert-triangle"></i> Mínimo: <?= (int)($a['stock_minimo'] ?? 0) ?> un.</span>
              </div>
            </div>
            <div class="alert-actions">
              <form method="POST" action="<?= $basePath ?>/inventario/alertas/<?= (int)$a['alerta_id'] ?>/resolver">
                <button type="submit" class="btn-resolver" onclick="return confirm('¿Marcar como resuelta?')">
                  <i data-lucide="check"></i> Resolver
                </button>
              </form>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Panel: Vencimiento -->
    <div class="tab-panel" id="panel-vencimiento">
      <?php if (empty($alertasVencimiento)): ?>
        <div class="empty-state">
          <div class="empty-icon"><i data-lucide="check-circle"></i></div>
          <p style="font-size:16px;font-weight:600;color:var(--color-text-primary);">Sin alertas de vencimiento</p>
          <span>Ningún lote está próximo a vencer.</span>
        </div>
      <?php else: ?>
        <?php foreach ($alertasVencimiento as $a): 
          $diasRestantes = !empty($a['fecha_vencimiento']) 
            ? (int)(new \DateTime())->diff(new \DateTime($a['fecha_vencimiento']))->days 
            : 0;
        ?>
        <div class="alert-card">
          <div class="alert-stripe vencimiento"></div>
          <div class="alert-card-body">
            <div class="alert-icon vencimiento"><i data-lucide="calendar-x"></i></div>
            <div class="alert-info">
              <div class="alert-title"><?= htmlspecialchars($a['producto_nombre'] ?? '—') ?></div>
              <div class="alert-desc"><?= htmlspecialchars($a['mensaje'] ?? '') ?></div>
              <div class="alert-meta">
                <?php if (!empty($a['numero_lote'])): ?>
                <span class="meta-chip"><i data-lucide="layers"></i> Lote: <?= htmlspecialchars($a['numero_lote']) ?></span>
                <?php endif; ?>
                <?php if (!empty($a['fecha_vencimiento'])): ?>
                <span class="meta-chip warn"><i data-lucide="calendar"></i> Vence: <?= date('d/m/Y', strtotime($a['fecha_vencimiento'])) ?> (<?= $diasRestantes ?> días)</span>
                <?php endif; ?>
              </div>
            </div>
            <div class="alert-actions">
              <form method="POST" action="<?= $basePath ?>/inventario/alertas/<?= (int)$a['alerta_id'] ?>/resolver">
                <button type="submit" class="btn-resolver" onclick="return confirm('¿Marcar como resuelta?')">
                  <i data-lucide="check"></i> Resolver
                </button>
              </form>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

  </main>
</div>
<script src="<?= $basePath ?>/assets/js/app.js"></script>
<script>
if (window.lucide) lucide.createIcons();

function setTab(tab) {
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
  
  event.currentTarget.classList.add('active');
  document.getElementById('panel-' + tab).classList.add('active');
}
</script>
</body>
</html>
