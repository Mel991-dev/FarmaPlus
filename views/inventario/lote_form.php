<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registrar Lote | FarmaPlus CRM</title>
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
      <a href="<?= $basePath ?>/inventario/lotes" class="breadcrumb-item">Lotes</a>
      <span class="breadcrumb-sep">/</span>
      <span class="breadcrumb-item current">Registrar entrada</span>
    </nav>
    <div class="topbar-actions">
      <button class="topbar-icon-btn">
        <i data-lucide="bell"></i>
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
        <a href="<?= $basePath ?>/inventario/lotes" class="back-btn">
          <i data-lucide="arrow-left"></i>
        </a>
        <div class="page-title-block">
          <h1>Registrar entrada de lote</h1>
          <p>Complete los datos del nuevo lote recibido para actualizar el inventario.</p>
        </div>
      </div>
    </div>

    <?php if (!empty($_GET['error'])): ?>
    <div class="error-banner">
      <i data-lucide="alert-circle"></i>
      <div>
        <strong>Error al registrar el lote</strong><br>
        <span><?= htmlspecialchars($_GET['error']) ?></span>
      </div>
    </div>
    <?php endif; ?>
    <?php if (!empty($_GET['success'])): ?>
    <div class="success-banner">
      <i data-lucide="check-circle"></i>
      <span><?= htmlspecialchars($_GET['success']) ?></span>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= $basePath ?>/inventario/lotes/registrar" id="loteForm">

      
      <!-- SECCIÓN 1: Producto -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-header-title">
              <i data-lucide="pill"></i>Sección 1 — Producto
            </div>
            <div class="card-header-desc">Seleccione el medicamento al que pertenece este lote.</div>
          </div>
        </div>
        <div class="card-body">
          
          <div class="form-field" style="margin-bottom:16px;">
            <label class="form-label">
              Buscar producto <span class="required">*</span>
            </label>
            <div class="input-wrap">
              <i data-lucide="search" class="input-icon"></i>
              <input type="text" id="buscarProducto" class="form-input" 
                     placeholder="Nombre comercial, principio activo o código INVIMA..." 
                     autocomplete="off" />
              <input type="hidden" name="producto_id" id="productoIdHidden" required />
            </div>
            <span class="form-help">Busque por nombre comercial, principio activo o código INVIMA.</span>
          </div>

          <!-- Resultados de búsqueda -->
          <div id="searchResults" style="display:none; margin-top:12px;"></div>

          <!-- Preview del producto seleccionado -->
          <div class="product-preview" id="productPreview" style="display:none; margin-top:16px;">
            <div class="product-preview-icon">
              <i data-lucide="pill"></i>
            </div>
            <div class="product-preview-info">
              <div class="product-preview-name" id="previewNombre"></div>
              <div class="product-preview-sub" id="previewSub"></div>
              <div class="product-preview-chips" id="previewChips"></div>
            </div>
          </div>

        </div>
      </div>

      <!-- SECCIÓN 2: Datos del lote -->
      <div class="card">
        <div class="card-header">
          <div>
            <div class="card-header-title">
              <i data-lucide="layers"></i>Sección 2 — Datos del lote
            </div>
            <div class="card-header-desc">Información del lote recibido según el empaque del fabricante.</div>
          </div>
        </div>
        <div class="card-body">
          
          <div class="form-grid-2">
            
            <div class="form-field">
              <label class="form-label">
                Número de lote <span class="required">*</span>
              </label>
              <input type="text" name="numero_lote" class="form-input mono" 
                     placeholder="Ej: TQ-2026-089" required />
              <span class="form-help">Tal como aparece impreso en el empaque del fabricante.</span>
            </div>

            <div class="form-field">
              <label class="form-label">
                Cantidad recibida <span class="required">*</span>
                <span class="hint">(unidades)</span>
              </label>
              <input type="number" name="cantidad_inicial" class="form-input" min="1" 
                     placeholder="120" required />
            </div>

            <div class="form-field">
              <label class="form-label">
                Fecha de vencimiento <span class="required">*</span>
              </label>
              <input type="date" name="fecha_vencimiento" class="form-input" 
                     id="fechaVencimiento" required />
              <span class="form-help" id="diasRestantes"></span>
            </div>

            <div class="form-field">
              <label class="form-label">
                Proveedor
              </label>
              <select name="proveedor_id" class="form-select">
                <option value="">Sin asignar</option>
                <?php foreach ($proveedores ?? [] as $prov): ?>
                <option value="<?= $prov['proveedor_id'] ?>">
                  <?= htmlspecialchars($prov['nombre']) ?>
                </option>
                <?php endforeach; ?>
              </select>
            </div>

          </div>

          <!-- Alerta de vencimiento -->
          <div class="alert-box alert-warn" id="alertVencimiento" style="display:none;">
            <i data-lucide="alert-triangle"></i>
            <div>
              <strong>Vencimiento próximo</strong>
              Este lote vence en menos de 30 días. Se generará una alerta automática al registrarlo.
            </div>
          </div>

          <div class="alert-box alert-crit" id="alertVencido" style="display:none;">
            <i data-lucide="x-circle"></i>
            <div>
              <strong>Fecha de vencimiento inválida</strong>
              La fecha de vencimiento debe ser posterior a la fecha actual. No se puede registrar un lote ya vencido.
            </div>
          </div>

        </div>
      </div>

      <!-- Barra de acciones -->
      <div class="form-actions-bar">
        <a href="<?= $basePath ?>/inventario/lotes" class="btn-secondary">
          <i data-lucide="x"></i> Cancelar
        </a>
        <div class="form-actions-note">
          <i data-lucide="info"></i>
          El stock se actualizará automáticamente al guardar
        </div>
        <button type="submit" class="btn-success" id="submitBtn" disabled>
          <i data-lucide="save"></i> Registrar lote
        </button>
      </div>

    </form>

  </main>
</div>

<script src="<?= $basePath ?>/assets/js/app.js"></script>
<script>
if (window.lucide) lucide.createIcons();

let productoSeleccionado = null;

// Búsqueda de productos con debounce
let searchTimeout;
document.getElementById('buscarProducto')?.addEventListener('input', function(e) {
  clearTimeout(searchTimeout);
  const termino = e.target.value.trim();
  
  if (termino.length < 2) {
    document.getElementById('searchResults').style.display = 'none';
    return;
  }
  
  searchTimeout = setTimeout(() => buscarProductos(termino), 300);
});

async function buscarProductos(termino) {
  try {
    const basePath = '<?= $basePath ?>';
    const response = await fetch(`${basePath}/api/productos/buscar?q=${encodeURIComponent(termino)}`);
    const productos = await response.json();
    
    const resultsDiv = document.getElementById('searchResults');
    if (productos.length === 0) {
      resultsDiv.innerHTML = '<div style="padding:12px;color:var(--color-text-secondary);font-size:13px;">No se encontraron productos</div>';
      resultsDiv.style.display = 'block';
      return;
    }
    
    resultsDiv.innerHTML = productos.map(p => `
      <div class="search-result-item" onclick='seleccionarProducto(${JSON.stringify(p)})'>
        <div><strong>${p.nombre}</strong></div>
        <div style="font-size:12px;color:var(--color-text-secondary);margin-top:2px;">
          INVIMA: ${p.codigo_invima} · Stock: ${p.stock_actual} unidades
        </div>
      </div>
    `).join('');
    resultsDiv.style.display = 'block';
  } catch (error) {
    console.error('Error buscando productos:', error);
  }
}

function seleccionarProducto(producto) {
  productoSeleccionado = producto;
  document.getElementById('productoIdHidden').value = producto.producto_id;
  document.getElementById('buscarProducto').value = producto.nombre;
  document.getElementById('searchResults').style.display = 'none';
  
  // Mostrar preview
  const preview = document.getElementById('productPreview');
  document.getElementById('previewNombre').textContent = producto.nombre;
  document.getElementById('previewSub').textContent = `INVIMA: ${producto.codigo_invima} · ${producto.proveedor_nombre || 'Sin laboratorio'}`;
  
  const chips = [];
  if (producto.stock_actual <= producto.stock_minimo) {
    chips.push(`<span class="chip chip-warn"><i data-lucide="alert-triangle"></i> Stock actual: <strong>${producto.stock_actual} unidades</strong></span>`);
  }
  chips.push(`<span class="chip chip-muted"><i data-lucide="layers"></i> Stock mínimo: ${producto.stock_minimo} unidades</span>`);
  
  if (!producto.control_especial) {
    chips.push(`<span class="badge-libre"><i data-lucide="circle-check"></i> Venta libre</span>`);
  }
  
  document.getElementById('previewChips').innerHTML = chips.join('');
  preview.style.display = 'flex';
  
  if (window.lucide) lucide.createIcons();
  validarFormulario();
}

// Validación de fecha de vencimiento
document.getElementById('fechaVencimiento')?.addEventListener('change', function() {
  const fechaVenc = new Date(this.value);
  const hoy = new Date();
  hoy.setHours(0, 0, 0, 0);
  
  const diffTime = fechaVenc - hoy;
  const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24));
  
  const diasSpan = document.getElementById('diasRestantes');
  const alertVenc = document.getElementById('alertVencimiento');
  const alertVencido = document.getElementById('alertVencido');
  
  if (diffDays < 0) {
    diasSpan.textContent = 'La fecha de vencimiento ya pasó';
    diasSpan.style.color = 'var(--color-error)';
    alertVencido.style.display = 'flex';
    alertVenc.style.display = 'none';
    this.setCustomValidity('La fecha debe ser futura');
  } else if (diffDays <= 30) {
    diasSpan.textContent = `Vence en ${diffDays} días`;
    diasSpan.style.color = 'var(--color-warning)';
    alertVenc.style.display = 'flex';
    alertVencido.style.display = 'none';
    this.setCustomValidity('');
  } else {
    diasSpan.textContent = `Vence en ${diffDays} días`;
    diasSpan.style.color = 'var(--color-success)';
    alertVenc.style.display = 'none';
    alertVencido.style.display = 'none';
    this.setCustomValidity('');
  }
  
  validarFormulario();
});

function validarFormulario() {
  const productoId = document.getElementById('productoIdHidden')?.value;
  const numeroLote = document.querySelector('[name="numero_lote"]')?.value;
  const cantidad = document.querySelector('[name="cantidad_inicial"]')?.value;
  const fechaVenc = document.querySelector('[name="fecha_vencimiento"]')?.value;
  
  const valido = productoId && numeroLote && cantidad && fechaVenc;
  document.getElementById('submitBtn').disabled = !valido;
}

// Validar en cada cambio
document.querySelectorAll('#loteForm input, #loteForm select').forEach(campo => {
  campo.addEventListener('input', validarFormulario);
  campo.addEventListener('change', validarFormulario);
});
</script>

</body>
</html>
