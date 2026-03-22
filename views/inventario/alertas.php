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
  <style>
    :root{--color-primary:#1A6B8A;--color-primary-dark:#1A3A4A;--color-primary-light:#4A9BB5;--color-secondary:#2A9D8F;--color-bg-main:#F4F9FC;--color-bg-card:#E9F5F8;--color-success:#27AE60;--color-error:#E74C3C;--color-warning:#F39C12;--color-info:#3498DB;--color-info-soft:#EBF5FB;--color-success-soft:#EAFAF1;--color-error-soft:#FDEDEC;--color-warning-soft:#FEF9E7;--color-text-primary:#2C3E50;--color-text-secondary:#7F8C8D;--color-border:#BDC3C7;--sidebar-width:240px;--topbar-height:64px;--radius-md:8px;--radius-lg:12px;--radius-full:9999px;--font-main:'Inter',sans-serif;--font-mono:'JetBrains Mono',monospace;--shadow-card:0 2px 8px rgba(0,0,0,0.08);}
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
    .topbar-user{display:flex;align-items:center;gap:8px;padding:4px 8px 4px 4px;border-radius:var(--radius-md);cursor:pointer;border:none;background:none;}
    .topbar-avatar{width:32px;height:32px;border-radius:50%;background:var(--color-primary);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:700;color:#fff;}
    .topbar-user-name{font-size:13px;font-weight:600;color:var(--color-text-primary);}
    .topbar-user-role{font-size:11px;color:var(--color-text-secondary);}

    /* KPI cards */
    .stats-row{display:grid;grid-template-columns:repeat(3,1fr);gap:16px;margin-bottom:24px;}
    .stat-card{background:#fff;border:1px solid var(--color-border);border-radius:var(--radius-lg);padding:16px 20px;display:flex;align-items:center;gap:14px;box-shadow:var(--shadow-card);}
    .stat-icon{width:44px;height:44px;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
    .stat-icon svg{width:22px;height:22px;}
    .stat-icon.red{background:var(--color-error-soft);color:var(--color-error);}
    .stat-icon.warn{background:var(--color-warning-soft);color:var(--color-warning);}
    .stat-icon.blue{background:var(--color-info-soft);color:var(--color-info);}
    .stat-val{font-size:26px;font-weight:700;color:var(--color-text-primary);line-height:1;}
    .stat-label{font-size:12px;color:var(--color-text-secondary);margin-top:2px;}

    /* Tab layout */
    .tabs{display:flex;gap:4px;margin-bottom:16px;border-bottom:2px solid var(--color-border);padding-bottom:0;}
    .tab-btn{padding:10px 20px;border:none;background:none;font-family:var(--font-main);font-size:14px;font-weight:500;color:var(--color-text-secondary);cursor:pointer;border-bottom:2px solid transparent;margin-bottom:-2px;transition:color .15s,border-color .15s;display:flex;align-items:center;gap:7px;border-radius:var(--radius-md) var(--radius-md) 0 0;}
    .tab-btn:hover{color:var(--color-text-primary);background:var(--color-bg-card);}
    .tab-btn.active{color:var(--color-primary);border-bottom-color:var(--color-primary);font-weight:600;}
    .tab-count{background:var(--color-error-soft);color:var(--color-error);font-size:11px;font-weight:700;padding:2px 7px;border-radius:var(--radius-full);}
    .tab-count.warn-bg{background:var(--color-warning-soft);color:var(--color-warning);}
    .tab-panel{display:none;}
    .tab-panel.active{display:block;}

    /* Alerta cards */
    .alert-card{background:#fff;border:1px solid var(--color-border);border-radius:var(--radius-lg);box-shadow:var(--shadow-card);overflow:hidden;margin-bottom:12px;}
    .alert-card:last-child{margin-bottom:0;}
    .alert-card-body{padding:16px 20px;display:flex;align-items:flex-start;gap:16px;}
    .alert-icon{width:44px;height:44px;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px;}
    .alert-icon svg{width:22px;height:22px;}
    .alert-icon.stock{background:var(--color-error-soft);color:var(--color-error);}
    .alert-icon.vencimiento{background:var(--color-warning-soft);color:var(--color-warning);}
    .alert-info{flex:1;}
    .alert-title{font-size:15px;font-weight:700;color:var(--color-text-primary);margin-bottom:3px;}
    .alert-desc{font-size:13px;color:var(--color-text-secondary);line-height:1.5;}
    .alert-meta{display:flex;align-items:center;gap:10px;margin-top:8px;flex-wrap:wrap;}
    .meta-chip{display:inline-flex;align-items:center;gap:4px;background:var(--color-bg-card);color:var(--color-text-secondary);font-size:12px;font-weight:500;padding:3px 10px;border-radius:var(--radius-full);}
    .meta-chip svg{width:12px;height:12px;}
    .meta-chip.crit{background:var(--color-error-soft);color:var(--color-error);}
    .meta-chip.warn{background:var(--color-warning-soft);color:var(--color-warning);}
    .alert-actions{display:flex;align-items:flex-start;padding-top:4px;}
    .btn-resolver{height:36px;background:var(--color-success);color:#fff;border:none;border-radius:var(--radius-md);font-family:var(--font-main);font-size:13px;font-weight:600;cursor:pointer;display:inline-flex;align-items:center;gap:7px;padding:0 14px;transition:background .2s;white-space:nowrap;}
    .btn-resolver:hover{background:#1E8449;}
    .btn-resolver svg{width:14px;height:14px;}
    .alert-stripe{height:4px;}
    .alert-stripe.stock{background:var(--color-error);}
    .alert-stripe.vencimiento{background:var(--color-warning);}

    /* Empty state */
    .empty-state{padding:60px 20px;text-align:center;color:var(--color-text-secondary);}
    .empty-icon{width:56px;height:56px;background:var(--color-success-soft);border-radius:50%;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;}
    .empty-icon svg{width:28px;height:28px;color:var(--color-success);}

    /* Flash messages */
    .flash{padding:12px 16px;border-radius:var(--radius-md);margin-bottom:16px;font-size:14px;display:flex;align-items:center;gap:10px;}
    .flash svg{width:17px;height:17px;flex-shrink:0;}
    .flash.success{background:var(--color-success-soft);border-left:4px solid var(--color-success);color:#1a5c35;}
    .flash.success svg{color:var(--color-success);}
    .flash.error{background:var(--color-error-soft);border-left:4px solid var(--color-error);color:#7B241C;}

    /* Page */
    .page-header{display:flex;align-items:center;justify-content:space-between;gap:16px;margin-bottom:24px;}
    .page-title-block h1{font-size:22px;font-weight:700;color:var(--color-text-primary);display:flex;align-items:center;gap:10px;letter-spacing:-.3px;}
    .page-title-block p{font-size:13px;color:var(--color-text-secondary);margin-top:3px;}
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
