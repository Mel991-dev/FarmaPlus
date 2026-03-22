<aside class="sidebar" id="sidebar" role="navigation" aria-label="Navegación principal">
  <div class="sidebar-logo">
    <div class="sidebar-logo-icon"><i data-lucide="pill"></i></div>
    <span class="sidebar-logo-text">Farma<span>Plus</span></span>
  </div>
  <nav class="sidebar-nav">
    <span class="nav-section-label">Principal</span>
    <a href="<?= $basePath ?>/dashboard" class="nav-item">
      <i data-lucide="layout-dashboard"></i>Dashboard
    </a>
    <div class="sidebar-divider"></div>
    <span class="nav-section-label">Comercial</span>
    <a href="<?= $basePath ?>/ventas/pos" class="nav-item"><i data-lucide="receipt"></i>Ventas POS</a>
    <a href="<?= $basePath ?>/pedidos" class="nav-item"><i data-lucide="shopping-bag"></i>Pedidos</a>
    <div class="sidebar-divider"></div>
    <span class="nav-section-label">Inventario</span>
    <a href="<?= $basePath ?>/inventario/productos" class="nav-item"><i data-lucide="pill"></i>Productos</a>
    <a href="<?= $basePath ?>/inventario/lotes" class="nav-item"><i data-lucide="layers"></i>Lotes</a>
    <a href="<?= $basePath ?>/inventario/alertas" class="nav-item">
      <i data-lucide="alert-triangle"></i>Alertas
      <?php if (($totalAlertas ?? 0) > 0): ?>
      <span class="nav-badge" style="background:rgba(231,76,60,0.25);color:#E74C3C;"><?= $totalAlertas ?></span>
      <?php endif; ?>
    </a>
    <a href="<?= $basePath ?>/inventario/proveedores" class="nav-item"><i data-lucide="building-2"></i>Proveedores</a>
    <div class="sidebar-divider"></div>
    <span class="nav-section-label">Análisis</span>
    <a href="<?= $basePath ?>/gerente/reportes/ventas" class="nav-item"><i data-lucide="bar-chart-2"></i>Reportes</a>
    <div class="sidebar-divider"></div>
    <span class="nav-section-label">Sistema</span>
    <a href="<?= $basePath ?>/admin/usuarios" class="nav-item"><i data-lucide="shield-check"></i>Usuarios</a>
    <a href="<?= $basePath ?>/admin/configuracion" class="nav-item"><i data-lucide="settings"></i>Configuración</a>
  </nav>
  <div class="sidebar-footer">
    <div class="sidebar-user">
      <div class="sidebar-avatar"><?= strtoupper(substr($_SESSION['nombres'] ?? 'U', 0, 1) . substr($_SESSION['apellidos'] ?? 'S', 0, 1)) ?></div>
      <div class="sidebar-user-info">
        <div class="sidebar-user-name"><?= htmlspecialchars($_SESSION['nombres'] ?? '') ?> <?= htmlspecialchars($_SESSION['apellidos'] ?? '') ?></div>
        <div class="sidebar-user-role"><?= htmlspecialchars($_SESSION['rol'] ?? '') ?></div>
      </div>
      <form method="POST" action="<?= $basePath ?>/logout">
        <button type="submit" class="sidebar-logout" aria-label="Cerrar sesión">
          <i data-lucide="log-out"></i>
        </button>
      </form>
    </div>
  </div>
</aside>
