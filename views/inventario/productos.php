<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Productos | FarmaPlus CRM</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="<?= $basePath ?>/assets/css/app.min.css" />
</head>
<body>

<div class="app-shell">
  
  <!-- SIDEBAR -->
  <?php include __DIR__ . '/../layouts/sidebar.php'; ?>

  <!-- TOPBAR -->
  <header class="topbar">
    <button class="topbar-toggle" onclick="toggleSidebar()">
      <i data-lucide="menu"></i>
    </button>
    <nav class="breadcrumb">
      <a href="<?= $basePath ?>/dashboard" class="breadcrumb-item">
        <i data-lucide="home" style="width:13px;height:13px;"></i> Inicio
      </a>
      <span class="breadcrumb-sep">/</span>
      <span class="breadcrumb-item current">Productos</span>
    </nav>
    <div class="topbar-actions">
      <button class="topbar-icon-btn">
        <i data-lucide="bell"></i>
        <?php if (($totalAlertas ?? 0) > 0): ?>
        <span class="notif-dot"></span>
        <?php endif; ?>
      </button>
      <button class="topbar-icon-btn">
        <i data-lucide="help-circle"></i>
      </button>
      <div class="topbar-user">
        <div class="topbar-avatar"><?= strtoupper(substr($_SESSION['nombres'] ?? 'U', 0, 1) . substr($_SESSION['apellidos'] ?? 'S', 0, 1)) ?></div>
        <div>
          <div class="topbar-user-name"><?= htmlspecialchars($_SESSION['nombres'] ?? '') ?> <?= htmlspecialchars($_SESSION['apellidos'] ?? '') ?></div>
          <div class="topbar-user-role"><?= htmlspecialchars($_SESSION['rol'] ?? '') ?></div>
        </div>
      </div>
    </div>
  </header>

  <!-- CONTENIDO PRINCIPAL -->
  <main class="main-content">
    
    <!-- Page Header -->
    <div class="page-header">
      <div class="page-header-left">
        <h1>Catálogo de Productos</h1>
        <p>Gestión de inventario y precios del catálogo farmacéutico</p>
      </div>
      <a href="<?= $basePath ?>/inventario/productos/crear" class="btn-primary">
        <i data-lucide="plus"></i> Nuevo producto
      </a>
    </div>

    <!-- Stats Row -->
    <div class="stats-row">
      <div class="stat-card">
        <div class="stat-icon">
          <i data-lucide="package"></i>
        </div>
        <div>
          <div class="stat-value"><?= $totalProductos ?? 0 ?></div>
          <div class="stat-label">Total productos</div>
        </div>
      </div>

      <div class="stat-card success">
        <div class="stat-icon">
          <i data-lucide="circle-check"></i>
        </div>
        <div>
          <div class="stat-value"><?= $productosStockOk ?? 0 ?></div>
          <div class="stat-label">Con stock OK</div>
        </div>
      </div>

      <div class="stat-card warning">
        <div class="stat-icon">
          <i data-lucide="alert-triangle"></i>
        </div>
        <div>
          <div class="stat-value"><?= $productosStockBajo ?? 0 ?></div>
          <div class="stat-label">Con stock bajo</div>
        </div>
      </div>

      <div class="stat-card info">
        <div class="stat-icon">
          <i data-lucide="dollar-sign"></i>
        </div>
        <div>
          <div class="stat-value">$<?= number_format($valorInventario ?? 0, 1) ?>M</div>
          <div class="stat-label">Valor inventario COP</div>
        </div>
      </div>
    </div>

    <!-- Alert Banner -->
    <?php if (($productosStockBajo ?? 0) > 0): ?>
    <div class="alert-banner">
      <i data-lucide="alert-triangle"></i>
      <div>
        <strong><?= $productosStockBajo ?> productos con stock bajo</strong> — 
        <?php
        $nombres = array_slice(array_column($productosAlerta ?? [], 'nombre'), 0, 3);
        echo htmlspecialchars(implode(', ', $nombres));
        if (count($productosAlerta ?? []) > 3) echo ' y ' . (count($productosAlerta) - 3) . ' más';
        ?>
        requieren reabastecimiento inmediato.
      </div>
      <div class="alert-banner-actions">
        <a href="<?= $basePath ?>/inventario/alertas" class="alert-link">Ver solo alertas</a>
        <button class="alert-link" onclick="exportarAlertas()">Exportar</button>
      </div>
    </div>
    <?php endif; ?>

    <!-- Toolbar -->
    <div class="toolbar">
      <div class="search-wrap">
        <i data-lucide="search" class="search-icon"></i>
        <input type="text" id="searchInput" class="search-input" placeholder="Buscar por nombre, INVIMA o principio activo..." />
      </div>
      <select id="filterCategoria" class="filter-select">
        <option value="">Todas las categorías</option>
        <?php foreach ($categorias ?? [] as $cat): ?>
        <option value="<?= $cat['categoria_id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
      <select id="filterLaboratorio" class="filter-select">
        <option value="">Todos los laboratorios</option>
        <?php foreach ($proveedores ?? [] as $prov): ?>
        <option value="<?= $prov['proveedor_id'] ?>"><?= htmlspecialchars($prov['nombre']) ?></option>
        <?php endforeach; ?>
      </select>
      <select id="filterEstado" class="filter-select">
        <option value="">Todos los estados</option>
        <option value="activo">Activo</option>
        <option value="inactivo">Inactivo</option>
      </select>
      <label class="filter-checkbox-wrap">
        <input type="checkbox" id="filterSoloAlertas" />
        Solo alertas activas
      </label>
      <button class="btn-secondary" onclick="exportarCatalogo()">
        <i data-lucide="download"></i> Exportar
      </button>
    </div>

    <!-- Tabla de Productos -->
    <div class="table-wrap">
      <div class="table-header-bar">
        <div class="table-title">
          <i data-lucide="package"></i>
          Catálogo de productos
          <span class="table-count">(<?= count($productos ?? []) ?> de <?= count($productos ?? []) ?>)</span>
        </div>
        <button class="btn-secondary btn-sm" onclick="toggleView()">
          <i data-lucide="grid-3x3"></i> Cuadrícula
        </button>
      </div>

      <div class="table-scroll-wrap">
        <table id="productosTable">
          <thead>
            <tr>
              <th class="th-sortable" data-sort="nombre">
                NOMBRE COMERCIAL
                <span class="th-sort-icon"><i data-lucide="chevron-down"></i></span>
              </th>
              <th>PRINCIPIO ACTIVO</th>
              <th>LABORATORIO</th>
              <th>CATEGORÍA</th>
              <th class="th-sortable" data-sort="stock">
                STOCK
                <span class="th-sort-icon"><i data-lucide="chevron-down"></i></span>
              </th>
              <th class="th-sortable" data-sort="precio">
                PRECIO VENTA
                <span class="th-sort-icon"><i data-lucide="chevron-down"></i></span>
              </th>
              <th>ESTADO</th>
              <th style="text-align:right;">ACCIONES</th>
            </tr>
          </thead>
          <tbody>
            <?php if (empty($productos)): ?>
            <tr>
              <td colspan="8" class="empty-state visible">
                <div class="empty-icon"><i data-lucide="package-x"></i></div>
                <div class="empty-title">No hay productos registrados</div>
                <div class="empty-desc">Comienza registrando tu primer producto farmacéutico</div>
              </td>
            </tr>
            <?php else: ?>
              <?php foreach ($productos as $p): 
                $stockActual = (int)($p['stock_actual'] ?? 0);
                $stockMinimo = (int)($p['stock_minimo'] ?? 10);
                $tieneAlerta = $stockActual <= $stockMinimo;
                $porcentaje = $stockMinimo > 0 ? min(100, ($stockActual / $stockMinimo) * 100) : 100;
                $estadoStock = $stockActual === 0 ? 'alert' : ($tieneAlerta ? 'warn' : 'ok');
              ?>
            <tr class="<?= $tieneAlerta ? 'row-alert' : '' ?>">
              <td>
                <div class="product-cell">
                  <div class="product-icon <?= $tieneAlerta ? 'alert' : 'ok' ?>">
                    <i data-lucide="<?= $tieneAlerta ? 'alert-triangle' : 'pill' ?>"></i>
                  </div>
                  <div>
                    <div class="product-name"><?= htmlspecialchars($p['nombre']) ?></div>
                    <div class="product-invima">INVIMA: <?= htmlspecialchars($p['codigo_invima']) ?></div>
                  </div>
                </div>
              </td>
              <td>
                <div class="principio"><?= htmlspecialchars($p['principio_activo'] ?: '—') ?></div>
              </td>
              <td>
                <span class="lab-pill"><?= htmlspecialchars($p['proveedor_nombre'] ?? 'Sin asignar') ?></span>
              </td>
              <td>
                <span class="cat-pill cat-<?= strtolower(str_replace(' ', '', $p['categoria_nombre'] ?? 'general')) ?>">
                  <?= htmlspecialchars($p['categoria_nombre'] ?? 'General') ?>
                </span>
              </td>
              <td>
                <div class="stock-cell">
                  <div class="stock-numbers">
                    <span class="stock-qty <?= $estadoStock ?>"><?= $stockActual ?></span>
                    <span class="stock-min-label">/ mín. <?= $stockMinimo ?></span>
                  </div>
                  <div class="stock-progress">
                    <div class="stock-progress-fill <?= $estadoStock ?>" style="width: <?= $porcentaje ?>%"></div>
                  </div>
                  <?php if ($tieneAlerta): ?>
                  <span class="badge badge-stock-low">
                    <i data-lucide="alert-triangle"></i> Stock bajo
                  </span>
                  <?php endif; ?>
                </div>
              </td>
              <td>
                <div class="price-cell">
                  <div class="price-main">$<?= number_format($p['precio_venta'], 0, ',', '.') ?></div>
                  <?php 
                  $margen = $p['precio_compra'] > 0 ? (($p['precio_venta'] - $p['precio_compra']) / $p['precio_compra']) * 100 : 0;
                  ?>
                  <div class="price-margin">+<?= number_format($margen, 0) ?>% margen</div>
                </div>
              </td>
              <td>
                <?php if ($p['activo']): ?>
                <span class="badge badge-activo">
                  <i data-lucide="circle-check"></i> Activo
                </span>
                <?php else: ?>
                <span class="badge badge-inactivo">
                  <i data-lucide="circle-x"></i> Inactivo
                </span>
                <?php endif; ?>
              </td>
              <td>
                <div class="actions-cell">
                  <a href="<?= $basePath ?>/inventario/productos/<?= $p['producto_id'] ?>" class="action-btn view" data-tooltip="Ver detalle">
                    <i data-lucide="eye"></i>
                  </a>
                  <a href="<?= $basePath ?>/inventario/productos/<?= $p['producto_id'] ?>/editar" class="action-btn edit" data-tooltip="Editar">
                    <i data-lucide="pencil"></i>
                  </a>
                  <a href="<?= $basePath ?>/inventario/lotes/registrar?producto_id=<?= $p['producto_id'] ?>" class="action-btn stock" data-tooltip="Registrar lote">
                    <i data-lucide="package-plus"></i>
                  </a>
                </div>
              </td>
            </tr>
            <?php endforeach; ?>
            <?php endif; ?>
          </tbody>
        </table>
      </div>

      <!-- Paginación -->
      <div class="pagination-bar">
        <div class="pagination-info">
          Mostrando <strong>1-<?= count($productos ?? []) ?></strong> de <strong><?= count($productos ?? []) ?></strong> productos
        </div>
        <div class="pagination-controls">
          <button class="page-btn" disabled>
            <i data-lucide="chevron-left"></i>
          </button>
          <button class="page-btn active">1</button>
          <button class="page-btn" disabled>
            <i data-lucide="chevron-right"></i>
          </button>
        </div>
      </div>
    </div>

  </main>
</div>

<script src="<?= $basePath ?>/assets/js/app.js"></script>
<script>
if (window.lucide) lucide.createIcons();

// Búsqueda en tiempo real
document.getElementById('searchInput')?.addEventListener('input', function(e) {
  const termino = e.target.value.toLowerCase();
  const filas = document.querySelectorAll('#productosTable tbody tr');
  
  filas.forEach(fila => {
    const texto = fila.textContent.toLowerCase();
    fila.style.display = texto.includes(termino) ? '' : 'none';
  });
});

// Filtros
['filterCategoria', 'filterLaboratorio', 'filterEstado', 'filterSoloAlertas'].forEach(id => {
  document.getElementById(id)?.addEventListener('change', aplicarFiltros);
});

function aplicarFiltros() {
  const categoria = document.getElementById('filterCategoria')?.value;
  const laboratorio = document.getElementById('filterLaboratorio')?.value;
  const estado = document.getElementById('filterEstado')?.value;
  const soloAlertas = document.getElementById('filterSoloAlertas')?.checked;
  
  const filas = document.querySelectorAll('#productosTable tbody tr');
  filas.forEach(fila => {
    let mostrar = true;
    
    if (soloAlertas && !fila.classList.contains('row-alert')) {
      mostrar = false;
    }
    
    fila.style.display = mostrar ? '' : 'none';
  });
}

function exportarCatalogo() {
  window.location.href = '/inventario/productos/exportar';
}

function exportarAlertas() {
  window.location.href = '/inventario/alertas/exportar';
}
</script>

</body>
</html>
