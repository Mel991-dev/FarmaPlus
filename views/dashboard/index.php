<?php
/**
 * views/dashboard/index.php
 * Dashboard principal — fiel al mockup fp-07-dashboard.html
 * Muestra datos reales de sesión y alertas de inventario desde la BD.
 *
 * Variables inyectadas por DashboardController:
 *   $usuario      array — Datos del usuario en sesión
 *   $alertas      array — Alertas activas de inventario
 *   $ventas_hoy   array — Últimas 5 ventas del día
 *   $kpis         array — Totales: ventas_dia, pedidos_pendientes, alertas_total, clientes_total
 */
$usuario    = $usuario    ?? ['nombres' => 'Usuario', 'apellidos' => '', 'rol_nombre' => 'usuario'];
$alertas    = $alertas    ?? [];
$ventas_hoy = $ventas_hoy ?? [];
$kpis       = $kpis       ?? ['ventas_dia' => 0, 'pedidos_pendientes' => 0, 'alertas_total' => 0, 'clientes_total' => 0];

$iniciales = strtoupper(mb_substr($usuario['nombres'], 0, 1) . mb_substr($usuario['apellidos'], 0, 1));
$nombre    = htmlspecialchars($usuario['nombres']);
$rol       = htmlspecialchars(ucfirst($usuario['rol_nombre']));

function fmtCOP(float $v): string {
    return '$' . number_format($v, 0, ',', '.');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Dashboard | FarmaPlus CRM</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/assets/css/app.min.css?v=<?= time() ?>" />
</head>
<body>

<div class="toast-container" id="toastContainer"></div>
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<div class="app-shell">

  <!-- SIDEBAR -->
  <aside class="sidebar" id="sidebar" role="navigation" aria-label="Navegación principal">
    <div class="sidebar-logo">
      <div class="sidebar-logo-icon"><i data-lucide="pill"></i></div>
      <span class="sidebar-logo-text">Farma<span>Plus</span></span>
    </div>
    <nav class="sidebar-nav">
      <span class="nav-section-label">Principal</span>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/dashboard" class="nav-item active" aria-current="page">
        <i data-lucide="layout-dashboard"></i>Dashboard
      </a>
      <div class="sidebar-divider"></div>
      <span class="nav-section-label">Comercial</span>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/ventas/pos" class="nav-item"><i data-lucide="receipt"></i>Ventas POS</a>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/pedidos" class="nav-item"><i data-lucide="shopping-bag"></i>Pedidos</a>
      <div class="sidebar-divider"></div>
      <span class="nav-section-label">Inventario</span>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/productos" class="nav-item"><i data-lucide="pill"></i>Productos</a>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/lotes" class="nav-item"><i data-lucide="layers"></i>Lotes</a>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/alertas" class="nav-item">
        <i data-lucide="alert-triangle"></i>Alertas
        <?php if (($kpis['alertas_total'] ?? 0) > 0): ?>
        <span class="nav-badge" style="background:rgba(231,76,60,0.25);color:#E74C3C;"><?= $kpis['alertas_total'] ?></span>
        <?php endif; ?>
      </a>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/proveedores" class="nav-item"><i data-lucide="building-2"></i>Proveedores</a>
      <div class="sidebar-divider"></div>
      <span class="nav-section-label">Análisis</span>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/gerente/reportes/ventas" class="nav-item"><i data-lucide="bar-chart-2"></i>Reportes</a>
      <div class="sidebar-divider"></div>
      <span class="nav-section-label">Sistema</span>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/admin/usuarios" class="nav-item"><i data-lucide="shield-check"></i>Usuarios</a>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/admin/configuracion" class="nav-item"><i data-lucide="settings"></i>Configuración</a>
    </nav>
    <div class="sidebar-footer">
      <div class="sidebar-user">
        <div class="sidebar-avatar"><?= htmlspecialchars($iniciales) ?></div>
        <div class="sidebar-user-info">
          <div class="sidebar-user-name"><?= $nombre ?> <?= htmlspecialchars($usuario['apellidos'] ?? '') ?></div>
          <div class="sidebar-user-role"><?= $rol ?></div>
        </div>
        <form method="POST" action="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/logout">
          <button type="submit" class="sidebar-logout" aria-label="Cerrar sesión">
            <i data-lucide="log-out"></i>
          </button>
        </form>
      </div>
    </div>
  </aside>

  <!-- TOPBAR -->
  <header class="topbar" role="banner">
    <button class="topbar-toggle" onclick="openSidebar()" aria-label="Abrir menú">
      <i data-lucide="menu"></i>
    </button>
    <nav class="breadcrumb" aria-label="Ruta de navegación">
      <a href="#" class="breadcrumb-item"><i data-lucide="home" style="width:13px;height:13px;"></i> Inicio</a>
      <span class="breadcrumb-sep">/</span>
      <span class="breadcrumb-item current">Dashboard</span>
    </nav>
    <div class="topbar-actions">
      <button class="topbar-icon-btn" aria-label="Notificaciones">
        <i data-lucide="bell"></i>
        <?php if (($kpis['alertas_total'] ?? 0) > 0): ?>
        <span class="notif-dot"></span>
        <?php endif; ?>
      </button>
      <button class="topbar-user">
        <div class="topbar-avatar"><?= htmlspecialchars($iniciales) ?></div>
        <div>
          <div class="topbar-user-name"><?= $nombre ?></div>
          <div class="topbar-user-role"><?= $rol ?></div>
        </div>
      </button>
    </div>
  </header>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="main-content" role="main">

    <!-- SALUDO DINÁMICO -->
    <div class="greeting-row">
      <div class="greeting-left">
        <div class="greeting-avatar"><?= htmlspecialchars($iniciales) ?></div>
        <div class="greeting-text">
          <h1 id="greeting">Bienvenido, <?= $nombre ?></h1>
          <p id="greetingDate">Cargando fecha…</p>
        </div>
      </div>
      <div class="greeting-datetime">
        <div class="greeting-time" id="greetingTime">00:00</div>
        <div class="greeting-date" id="greetingDateSub">—</div>
      </div>
    </div>

    <!-- ACCESOS RÁPIDOS -->
    <div class="quick-actions" role="region" aria-label="Accesos rápidos">
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/ventas/pos" class="quick-action-btn">
        <div class="qa-icon blue"><i data-lucide="scan-line"></i></div>
        <div><div class="qa-label">Nueva venta</div><div class="qa-sub">Punto de venta POS</div></div>
      </a>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/lotes/registrar" class="quick-action-btn">
        <div class="qa-icon purple"><i data-lucide="layers"></i></div>
        <div><div class="qa-label">Registrar lote</div><div class="qa-sub">Entrada de mercancía</div></div>
      </a>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/productos/crear" class="quick-action-btn">
        <div class="qa-icon amber"><i data-lucide="package-plus"></i></div>
        <div><div class="qa-label">Nuevo producto</div><div class="qa-sub">Agregar al catálogo</div></div>
      </a>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/admin/usuarios" class="quick-action-btn">
        <div class="qa-icon green"><i data-lucide="user-plus"></i></div>
        <div><div class="qa-label">Nuevo usuario</div><div class="qa-sub">Gestionar personal</div></div>
      </a>
    </div>

    <!-- KPI CARDS -->
    <div class="kpi-row" role="region" aria-label="Indicadores clave">
      <!-- Ventas del día -->
      <a href="/ventas" class="kpi-card success">
        <div class="kpi-card-top">
          <div class="kpi-icon"><i data-lucide="receipt"></i></div>
          <div class="kpi-arrow"><i data-lucide="arrow-right"></i></div>
        </div>
        <div class="kpi-value <?= ((float)($kpis['ventas_dia'] ?? 0)) === 0.0 ? 'is-zero' : '' ?>"><?= fmtCOP((float) ($kpis['ventas_dia'] ?? 0)) ?></div>
        <div class="kpi-label">Ventas del día</div>
        <div class="kpi-divider"></div>
        <div class="kpi-trend kpi-trend-up">
          <div class="kpi-trend-icon"><i data-lucide="trending-up"></i></div>
          <span><?= count($ventas_hoy) ?> transacciones</span>
          <span class="kpi-trend-text">hoy</span>
        </div>
      </a>
      <!-- Pedidos pendientes -->
      <a href="/pedidos" class="kpi-card info">
        <div class="kpi-card-top">
          <div class="kpi-icon"><i data-lucide="shopping-bag"></i></div>
          <div class="kpi-arrow"><i data-lucide="arrow-right"></i></div>
        </div>
        <div class="kpi-value <?= ((int)($kpis['pedidos_pendientes'] ?? 0)) === 0 ? 'is-zero' : '' ?>"><?= (int) ($kpis['pedidos_pendientes'] ?? 0) ?></div>
        <div class="kpi-label">Pedidos pendientes</div>
        <div class="kpi-divider"></div>
        <div class="kpi-trend kpi-trend-up">
          <div class="kpi-trend-icon"><i data-lucide="truck"></i></div>
          <span>En proceso</span>
          <span class="kpi-trend-text">de atención</span>
        </div>
      </a>
      <!-- Alertas inventario -->
      <a href="inventario/alertas" class="kpi-card warning">
        <div class="kpi-card-top">
          <div class="kpi-icon"><i data-lucide="alert-triangle"></i></div>
          <div class="kpi-arrow"><i data-lucide="arrow-right"></i></div>
        </div>
        <div class="kpi-value <?= ((int)($kpis['alertas_total'] ?? 0)) === 0 ? 'is-zero' : '' ?>"><?= (int) ($kpis['alertas_total'] ?? 0) ?></div>
        <div class="kpi-label">Alertas de inventario</div>
        <div class="kpi-divider"></div>
        <div class="kpi-trend kpi-trend-down">
          <div class="kpi-trend-icon"><i data-lucide="trending-up"></i></div>
          <span>Requieren</span>
          <span class="kpi-trend-text">atención</span>
        </div>
      </a>
      <!-- Clientes -->
      <a href="#" class="kpi-card">
        <div class="kpi-card-top">
          <div class="kpi-icon"><i data-lucide="users"></i></div>
          <div class="kpi-arrow"><i data-lucide="arrow-right"></i></div>
        </div>
        <div class="kpi-value <?= ((int)($kpis['clientes_total'] ?? 0)) === 0 ? 'is-zero' : '' ?>"><?= (int) ($kpis['clientes_total'] ?? 0) ?></div>
        <div class="kpi-label">Clientes registrados</div>
        <div class="kpi-divider"></div>
        <div class="kpi-trend kpi-trend-up">
          <div class="kpi-trend-icon"><i data-lucide="trending-up"></i></div>
          <span>Total</span>
          <span class="kpi-trend-text">en el sistema</span>
        </div>
      </a>
    </div>

    <!-- SECCIÓN INFERIOR -->
    <div class="bottom-grid">
      <!-- Últimas ventas -->
      <div class="section-card">
        <div class="section-header">
          <div class="section-title"><i data-lucide="receipt"></i>Últimas ventas del día</div>
          <a href="/ventas" class="link-btn">Ver todas <i data-lucide="arrow-right"></i></a>
        </div>
        <?php if (empty($ventas_hoy)): ?>
          <div class="empty-state empty-ventas visible">
            <i data-lucide="shopping-cart"></i>
            Sin ventas registradas hoy.
          </div>
        <?php else: ?>
        <table class="interactions-table" aria-label="Últimas ventas registradas">
          <thead><tr><th>Comprobante</th><th>Canal</th><th>Total</th><th>Vendedor</th></tr></thead>
          <tbody>
          <?php foreach ($ventas_hoy as $v): ?>
            <tr>
              <td><span style="font-family:var(--font-mono);font-size:12px;color:var(--color-primary);"><?= htmlspecialchars($v['numero_comprobante']) ?></span></td>
              <td><span class="int-badge int-presencial"><i data-lucide="store"></i>Presencial</span></td>
              <td><strong style="color:var(--color-success);"><?= fmtCOP((float)$v['total']) ?></strong></td>
              <td style="font-size:12px;color:var(--color-text-secondary);"><?= htmlspecialchars($v['vendedor_nombre'] ?? '—') ?></td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
        <?php endif; ?>
      </div>

      <!-- Alertas de inventario -->
      <div class="section-card">
        <div class="section-header">
          <div class="section-title"><i data-lucide="alert-triangle"></i>Alertas de inventario</div>
          <a href="inventario/alertas" class="link-btn">Ver todas <i data-lucide="arrow-right"></i></a>
        </div>
        <?php if (empty($alertas)): ?>
          <div class="empty-state empty-alertas visible">
            <i data-lucide="check-circle"></i>
            Sin alertas activas. ¡Todo bajo control!
          </div>
        <?php else: ?>
        <div class="stock-list">
        <?php foreach (array_slice($alertas, 0, 4) as $alerta): ?>
          <div class="stock-item">
            <div class="stock-item-top">
              <div>
                <div class="stock-product-name"><?= htmlspecialchars($alerta['producto_nombre'] ?? '—') ?></div>
                <div class="stock-product-lab"><?= htmlspecialchars($alerta['mensaje'] ?? '') ?></div>
              </div>
              <?php if ($alerta['tipo'] === 'stock_minimo'): ?>
              <span class="stock-badge-low"><i data-lucide="alert-triangle"></i>Stock mínimo</span>
              <?php else: ?>
              <span class="stock-badge-low stock-badge-vence"><i data-lucide="clock"></i>Vence pronto</span>
              <?php endif; ?>
            </div>
            <div class="stock-item-actions">
              <span style="font-size:11px;color:var(--color-text-secondary);"><?= htmlspecialchars($alerta['tipo'] === 'stock_minimo' ? 'Stock insuficiente' : 'Lote: ' . ($alerta['numero_lote'] ?? '—')) ?></span>
              <a href="/inventario/alertas" class="stock-action-btn">Ver detalle <i data-lucide="arrow-right"></i></a>
            </div>
          </div>
        <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>

  </main>
</div>

<script>
  lucide.createIcons();

  /* Saludo dinámico y reloj */
  function updateDateTime() {
    const now  = new Date();
    const h    = now.getHours();
    const dias  = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
    const meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    const saludo = h < 12 ? 'Buenos días' : h < 18 ? 'Buenas tardes' : 'Buenas noches';
    const emoji  = h < 12 ? '☀️' : h < 18 ? '🌤️' : '🌙';
    document.getElementById('greeting').textContent = `${saludo}, <?= $nombre ?> ${emoji}`;
    document.getElementById('greetingDate').textContent = `${dias[now.getDay()]}, ${now.getDate()} de ${meses[now.getMonth()]} de ${now.getFullYear()}`;
    document.getElementById('greetingTime').textContent = `${String(h).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
    document.getElementById('greetingDateSub').textContent = `${dias[now.getDay()]}, ${now.getDate()} ${meses[now.getMonth()].slice(0,3)}. ${now.getFullYear()}`;
  }
  updateDateTime();
  setInterval(updateDateTime, 1000);

  /* Toast */
  function showToast(msg, type = 'success') {
    const container = document.getElementById('toastContainer');
    const icons = { success:'circle-check', warning:'alert-triangle', info:'info', error:'circle-x' };
    const toast = document.createElement('div');
    toast.className = `toast ${type}`;
    toast.innerHTML = `<i data-lucide="${icons[type]}"></i><div class="toast-text"><strong>${msg}</strong></div>`;
    container.appendChild(toast);
    lucide.createIcons();
    setTimeout(() => { toast.style.transition='opacity 0.3s,transform 0.3s'; toast.style.opacity='0'; toast.style.transform='translateX(120%)'; setTimeout(()=>toast.remove(),300); }, 4000);
  }

  /* Sidebar mobile */
  function openSidebar()  { document.getElementById('sidebar').classList.add('open'); document.getElementById('sidebarOverlay').classList.add('visible'); }
  function closeSidebar() { document.getElementById('sidebar').classList.remove('open'); document.getElementById('sidebarOverlay').classList.remove('visible'); }

  /* Flash message desde sesión PHP */
  <?php if (!empty($_SESSION['flash_msg'])): ?>
  setTimeout(() => showToast('<?= addslashes($_SESSION['flash_msg']) ?>', '<?= addslashes($_SESSION['flash_tipo'] ?? 'success') ?>'), 500);
  <?php unset($_SESSION['flash_msg'], $_SESSION['flash_tipo']); ?>
  <?php endif; ?>

  /* Alertas de inventario al cargar */
  <?php if (($kpis['alertas_total'] ?? 0) > 0): ?>
  setTimeout(() => showToast('<?= (int)$kpis['alertas_total'] ?> alertas de inventario requieren atención', 'warning'), 1200);
  <?php endif; ?>
</script>
</body>
</html>
