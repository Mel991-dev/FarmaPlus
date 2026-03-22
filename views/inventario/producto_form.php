<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= isset($producto) ? 'Editar' : 'Nuevo' ?> Producto | FarmaPlus CRM</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="<?= $basePath ?>/assets/css/app.min.css">
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
      <a href="<?= $basePath ?>/inventario/productos" class="breadcrumb-item">Productos</a>
      <span class="breadcrumb-sep">/</span>
      <span class="breadcrumb-item current"><?= isset($producto) ? 'Editar' : 'Nuevo' ?> producto</span>
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
        <h1>
          <i data-lucide="package-plus"></i>
          <?= isset($producto) ? 'Editar' : 'Nuevo' ?> producto
        </h1>
        <p>Registra un nuevo producto farmacéutico en el catálogo de la droguería.</p>
      </div>
    </div>

    <?php if (!empty($_GET['error'])): ?>
    <div class="error-banner">
      <i data-lucide="alert-circle"></i>
      <div>
        <strong>Error al guardar el producto</strong><br>
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

    <form method="POST" action="<?= isset($producto) ? $basePath . '/inventario/productos/' . $producto['producto_id'] . '/editar' : $basePath . '/inventario/productos/crear' ?>" id="productoForm">

      
      <div class="form-layout">
        
        <!-- Columna Principal -->
        <div class="form-stack">
          
          <!-- SECCIÓN 1: Identificación -->
          <div class="form-card">
            <div class="form-section">
              <div class="section-heading">
                <div class="section-icon blue">
                  <i data-lucide="pill"></i>
                </div>
                <div class="section-heading-text">
                  <div class="section-title">Identificación del producto</div>
                  <div class="section-subtitle">Nombre, principio activo, forma farmacéutica y código INVIMA</div>
                </div>
              </div>

              <div class="form-stack">
                <div class="form-grid-2">
                  <div class="form-group">
                    <label class="form-label">
                      Nombre comercial <span class="req">*</span>
                    </label>
                    <input type="text" name="nombre" class="form-input" 
                           value="<?= htmlspecialchars($producto['nombre'] ?? '') ?>"
                           placeholder="Ej: Amoxicilina Mk 500mg x 10 cápsulas" required />
                  </div>

                  <div class="form-group">
                    <label class="form-label">
                      Principio activo <span class="req">*</span>
                    </label>
                    <input type="text" name="principio_activo" class="form-input"
                           value="<?= htmlspecialchars($producto['principio_activo'] ?? '') ?>"
                           placeholder="Ej: Amoxicilina trihidrato" required />
                  </div>
                </div>

                <div class="form-grid-2">
                  <div class="form-group">
                    <label class="form-label">
                      Concentración <span class="req">*</span>
                    </label>
                    <input type="text" name="concentracion" class="form-input"
                           value="<?= htmlspecialchars($producto['concentracion'] ?? '') ?>"
                           placeholder="Ej: 500mg" required />
                  </div>

                  <div class="form-group">
                    <label class="form-label">
                      Forma farmacéutica <span class="req">*</span>
                    </label>
                    <select name="forma_farmaceutica" class="form-select" required>
                      <option value="">Seleccionar...</option>
                      <option value="Cápsulas" <?= ($producto['forma_farmaceutica'] ?? '') === 'Cápsulas' ? 'selected' : '' ?>>Cápsulas</option>
                      <option value="Tabletas" <?= ($producto['forma_farmaceutica'] ?? '') === 'Tabletas' ? 'selected' : '' ?>>Tabletas</option>
                      <option value="Jarabe" <?= ($producto['forma_farmaceutica'] ?? '') === 'Jarabe' ? 'selected' : '' ?>>Jarabe</option>
                      <option value="Suspensión" <?= ($producto['forma_farmaceutica'] ?? '') === 'Suspensión' ? 'selected' : '' ?>>Suspensión</option>
                      <option value="Inyectable" <?= ($producto['forma_farmaceutica'] ?? '') === 'Inyectable' ? 'selected' : '' ?>>Inyectable</option>
                      <option value="Crema" <?= ($producto['forma_farmaceutica'] ?? '') === 'Crema' ? 'selected' : '' ?>>Crema</option>
                      <option value="Gel" <?= ($producto['forma_farmaceutica'] ?? '') === 'Gel' ? 'selected' : '' ?>>Gel</option>
                      <option value="Gotas" <?= ($producto['forma_farmaceutica'] ?? '') === 'Gotas' ? 'selected' : '' ?>>Gotas</option>
                    </select>
                  </div>
                </div>

                <div class="form-grid-invima">
                  <div class="form-group">
                    <label class="form-label">
                      Código INVIMA <span class="req">*</span>
                    </label>
                    <input type="text" name="codigo_invima" class="form-input mono"
                           value="<?= htmlspecialchars($producto['codigo_invima'] ?? '') ?>"
                           placeholder="M-XXXXAR-RX" required />
                    <div class="form-hint">
                      <i data-lucide="info"></i>
                      Consulta el código en el registro sanitario del INVIMA Colombia.
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="form-label">
                      Requiere fórmula médica
                    </label>
                    <div class="toggle-field">
                      <div class="toggle-field-info">
                        <div class="toggle-field-label">Control especial</div>
                      </div>
                      <label class="toggle-switch">
                        <input type="checkbox" name="control_especial" value="1" 
                               <?= ($producto['control_especial'] ?? 0) ? 'checked' : '' ?> />
                        <span class="toggle-slider"></span>
                      </label>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- SECCIÓN 2: Clasificación -->
          <div class="form-card">
            <div class="form-section">
              <div class="section-heading">
                <div class="section-icon amber">
                  <i data-lucide="tag"></i>
                </div>
                <div class="section-heading-text">
                  <div class="section-title">Clasificación</div>
                  <div class="section-subtitle">Laboratorio, categoría, presentación y fecha de vencimiento del lote</div>
                </div>
              </div>

              <div class="form-stack">
                <div class="form-grid-2">
                  <div class="form-group">
                    <label class="form-label">
                      Laboratorio <span class="req">*</span>
                    </label>
                    <select name="proveedor_id" class="form-select" required>
                      <option value="">Seleccionar...</option>
                      <?php foreach ($proveedores ?? [] as $prov): ?>
                      <option value="<?= $prov['proveedor_id'] ?>" 
                              <?= ($producto['proveedor_id'] ?? '') == $prov['proveedor_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($prov['nombre']) ?>
                      </option>
                      <?php endforeach; ?>
                    </select>
                  </div>

                  <div class="form-group">
                    <label class="form-label">
                      Categoría <span class="req">*</span>
                    </label>
                    <select name="categoria_id" class="form-select" required>
                      <option value="">Seleccionar...</option>
                      <?php foreach ($categorias ?? [] as $cat): ?>
                      <option value="<?= $cat['categoria_id'] ?>"
                              <?= ($producto['categoria_id'] ?? '') == $cat['categoria_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['nombre']) ?>
                      </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- SECCIÓN 3: Inventario y precios -->
          <div class="form-card">
            <div class="form-section">
              <div class="section-heading">
                <div class="section-icon green">
                  <i data-lucide="dollar-sign"></i>
                </div>
                <div class="section-heading-text">
                  <div class="section-title">Inventario y precios</div>
                  <div class="section-subtitle">Precios de compra, venta y stock mínimo</div>
                </div>
              </div>

              <div class="form-stack">
                <div class="form-grid-2">
                  <div class="form-group">
                    <label class="form-label">
                      Stock inicial <span class="req">*</span>
                    </label>
                    <input type="number" name="stock_inicial" class="form-input" min="0"
                           value="<?= $producto['stock_actual'] ?? 0 ?>"
                           placeholder="0" required />
                    <div class="form-hint">
                      <i data-lucide="info"></i>
                      Unidades disponibles al momento del registro.
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="form-label">
                      Stock mínimo / Umbral de alerta <span class="req">*</span>
                    </label>
                    <input type="number" name="stock_minimo" class="form-input" min="0"
                           value="<?= $producto['stock_minimo'] ?? 10 ?>"
                           placeholder="10" required />
                    <div class="form-hint">
                      <i data-lucide="info"></i>
                      Se generará una alerta cuando el stock baje de este valor.
                    </div>
                  </div>
                </div>

                <div class="form-grid-2">
                  <div class="form-group">
                    <label class="form-label">
                      Precio de compra <span class="req">*</span>
                    </label>
                    <div class="price-input-wrap">
                      <div class="price-prefix">COP</div>
                      <input type="number" name="precio_compra" class="form-input" step="0.01" min="0"
                             value="<?= $producto['precio_compra'] ?? '' ?>"
                             placeholder="0" required />
                    </div>
                  </div>

                  <div class="form-group">
                    <label class="form-label">
                      Precio de venta <span class="req">*</span>
                    </label>
                    <div class="price-input-wrap">
                      <div class="price-prefix">COP</div>
                      <input type="number" name="precio_venta" class="form-input" step="0.01" min="0"
                             value="<?= $producto['precio_venta'] ?? '' ?>"
                             placeholder="0" required id="precioVenta" />
                    </div>
                  </div>
                </div>


                <!-- Margen calculado -->
                <div class="form-group">
                  <label class="form-label">Margen de ganancia — Calculado automáticamente</label>
                  <div class="margen-display" id="margenDisplay">
                    <span class="margen-label" id="margenLabel">Margen estimado</span>
                    <span class="margen-value zero" id="margenValue">-</span>
                    <span class="margen-badge zero" id="margenBadge">Sin datos</span>
                  </div>
                  <div class="form-hint">
                    <i data-lucide="info"></i>
                    Fórmula: (Precio venta – Precio compra) / Precio compra × 100
                  </div>
                </div>

                <!-- Alerta de stock -->
                <div class="stock-alert-card" id="stockAlert">
                  <div class="stock-alert-icon">
                    <i data-lucide="alert-triangle"></i>
                  </div>
                  <div class="stock-alert-text">
                    <div class="stock-alert-title">Stock mínimo configurado</div>
                    <div class="stock-alert-desc">
                      El sistema generará una alerta automática cuando el stock disponible sea igual o menor a 
                      <strong id="stockMinimoDisplay">10</strong> unidades.
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        </div>

        <!-- Columna Lateral -->
        <div class="side-panel">
          
          <!-- Vista Previa -->
          <div class="side-card">
            <div class="side-card-header">
              <i data-lucide="eye"></i>
              Vista previa
            </div>
            <div class="side-card-body">
              <div class="preview-pill-row" id="previewCard">
                <div class="preview-pill-icon">
                  <i data-lucide="package"></i>
                </div>
                <div>
                  <div class="preview-name" id="previewNombre">Completa el nombre para ver la vista previa...</div>
                  <div class="preview-sub" id="previewSub"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Progreso -->
          <div class="side-card">
            <div class="side-card-header">
              <i data-lucide="list-checks"></i>
              Progreso
            </div>
            <div class="side-card-body">
              <div class="progress-item">
                <div class="progress-dot" id="progressId">
                  <i data-lucide="check"></i>
                </div>
                <span class="progress-label" id="labelId">Identificación</span>
              </div>
              <div class="progress-item">
                <div class="progress-dot" id="progressClasif">
                  <i data-lucide="check"></i>
                </div>
                <span class="progress-label" id="labelClasif">Clasificación</span>
              </div>
              <div class="progress-item">
                <div class="progress-dot" id="progressPrecios">
                  <i data-lucide="check"></i>
                </div>
                <span class="progress-label" id="labelPrecios">Inventario y precios</span>
              </div>
            </div>
            <div class="required-note">
              <span class="req">*</span> Campos marcados son obligatorios
            </div>
          </div>

          <!-- Guía Regulatoria -->
          <div class="side-card">
            <div class="side-card-header">
              <i data-lucide="book-open"></i>
              Guía regulatoria
            </div>
            <div class="side-card-body">
              <div class="tip-item">
                <div class="tip-icon" style="background:var(--color-info-soft);">
                  <i data-lucide="shield-check" style="color:var(--color-info);"></i>
                </div>
                <div class="tip-text">
                  Consulta el <strong>Registro INVIMA</strong> en 
                  <a href="https://registros.invima.gov.co" target="_blank" style="color:var(--color-primary);text-decoration:underline;">registros.invima.gov.co</a> 
                  para verificar el código.
                </div>
              </div>
            </div>
          </div>

        </div>

      </div>

      <!-- Form Footer -->
      <div class="form-card" style="margin-top:24px; max-width:1200px;">
        <div class="form-section" style="padding:16px 24px;">
          <div class="form-footer" style="box-shadow:none; border:none; padding:0; background:transparent;">
            <div class="form-footer-note">
              <i data-lucide="info"></i>
              <span class="req">*</span> Campos obligatorios. Completa todos antes de guardar.
            </div>
            <div class="form-footer-actions">
              <a href="<?= $basePath ?>/inventario/productos" class="btn btn-secondary">
                <i data-lucide="x"></i> Cancelar
              </a>
              <button type="submit" class="btn btn-primary">
                <i data-lucide="save"></i> <?= isset($producto) ? 'Guardar producto →' : 'Guardar producto →' ?>
              </button>
            </div>
          </div>
        </div>
      </div>

    </form>

  </main>
</div>

<script src="<?= $basePath ?>/assets/js/app.js"></script>
<script src="<?= $basePath ?>/assets/js/validaciones.js"></script>
<script>
if (window.lucide) lucide.createIcons();

// Vista previa en tiempo real
const campos = {
  nombre: document.querySelector('[name="nombre"]'),
  principio_activo: document.querySelector('[name="principio_activo"]'),
  codigo_invima: document.querySelector('[name="codigo_invima"]'),
  precio_compra: document.querySelector('[name="precio_compra"]'),
  precio_venta: document.querySelector('[name="precio_venta"]'),
  stock_minimo: document.querySelector('[name="stock_minimo"]')
};

function actualizarPreview() {
  const nombre = campos.nombre?.value || '';
  const principio = campos.principio_activo?.value || '';
  const invima = campos.codigo_invima?.value || '';
  
  document.getElementById('previewNombre').textContent = nombre || 'Completa el nombre para ver la vista previa...';
  document.getElementById('previewSub').textContent = invima ? `INVIMA: ${invima}` : '';
  
  // Progreso
  const seccion1 = nombre && principio && invima;
  const seccion2 = document.querySelector('[name="proveedor_id"]')?.value && document.querySelector('[name="categoria_id"]')?.value;
  const seccion3 = campos.precio_compra?.value && campos.precio_venta?.value && campos.stock_minimo?.value;
  
  actualizarProgreso('progressId', 'labelId', seccion1);
  actualizarProgreso('progressClasif', 'labelClasif', seccion2);
  actualizarProgreso('progressPrecios', 'labelPrecios', seccion3);
}

function actualizarProgreso(dotId, labelId, completado) {
  const dot = document.getElementById(dotId);
  const label = document.getElementById(labelId);
  if (completado) {
    dot?.classList.add('done');
    label?.classList.add('done');
  } else {
    dot?.classList.remove('done');
    label?.classList.remove('done');
  }
}

// Cálculo de margen
function calcularMargen() {
  const compra = parseFloat(campos.precio_compra?.value) || 0;
  const venta = parseFloat(campos.precio_venta?.value) || 0;
  
  if (compra > 0 && venta > 0) {
    const margen = ((venta - compra) / compra) * 100;
    const margenDisplay = document.getElementById('margenDisplay');
    const margenValue = document.getElementById('margenValue');
    const margenBadge = document.getElementById('margenBadge');
    
    margenDisplay.style.display = 'flex';
    margenValue.textContent = margen.toFixed(1) + '%';
    
    if (margen > 0) {
      margenValue.className = 'margen-value positive';
      margenBadge.className = 'margen-badge positive';
      margenBadge.textContent = 'Rentable';
    } else if (margen < 0) {
      margenValue.className = 'margen-value negative';
      margenBadge.className = 'margen-badge negative';
      margenBadge.textContent = 'Pérdida';
    } else {
      margenValue.className = 'margen-value zero';
      margenBadge.className = 'margen-badge zero';
      margenBadge.textContent = 'Sin margen';
    }
  }
}

// Actualizar display de stock mínimo
campos.stock_minimo?.addEventListener('input', function() {
  document.getElementById('stockMinimoDisplay').textContent = this.value || '10';
  document.getElementById('stockAlert').classList.toggle('visible', this.value > 0);
});

// Event listeners
Object.values(campos).forEach(campo => {
  campo?.addEventListener('input', actualizarPreview);
});

campos.precio_compra?.addEventListener('input', calcularMargen);
campos.precio_venta?.addEventListener('input', calcularMargen);

// Inicializar
actualizarPreview();
calcularMargen();
if (campos.stock_minimo?.value > 0) {
  document.getElementById('stockAlert').classList.add('visible');
}
</script>

</body>
</html>
