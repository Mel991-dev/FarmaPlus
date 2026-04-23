<?php
$titulo = isset($producto) ? 'Editar Producto' : 'Nuevo Producto';
ob_start(); 
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div>
    <h1 class="text-2xl font-bold text-fp-text tracking-tight flex items-center gap-2">
      <i data-lucide="package-plus" class="w-6 h-6"></i>
      <?= isset($producto) ? 'Editar' : 'Nuevo' ?> producto
    </h1>
    <p class="text-[13px] text-fp-muted mt-0.5">Registra un nuevo producto farmacéutico en el catálogo de la droguería.</p>
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

          <!-- TOGGLE TIPO DE PRODUCTO -->
          <div class="form-card">
            <div class="form-section">
              <div class="section-heading">
                <div class="section-icon blue"><i data-lucide="layers"></i></div>
                <div class="section-heading-text">
                  <div class="section-title">Tipo de producto</div>
                  <div class="section-subtitle">¿Es un medicamento o un producto de miscelánea/consumo?</div>
                </div>
              </div>
              <div class="toggle-field" style="margin-top:12px;">
                <div class="toggle-field-info">
                  <div class="toggle-field-label" id="tipoLabel">
                    <?= (($producto['es_medicamento'] ?? 1) == 1) ? '💊 Medicamento' : '🛒 Miscelánea / Consumo' ?>
                  </div>
                  <div class="toggle-field-desc" style="font-size:12px;color:var(--color-muted);">Activa para medicamentos — requiere INVIMA y principio activo</div>
                </div>
                <label class="toggle-switch">
                  <input type="checkbox" name="es_medicamento" id="esMedicamentoToggle" value="1"
                         <?= (($producto['es_medicamento'] ?? 1) == 1) ? 'checked' : '' ?> />
                  <span class="toggle-slider"></span>
                </label>
              </div>
            </div>
          </div>

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
                   <div class="form-stack">
                <div class="form-group">
                  <label class="form-label">Nombre comercial <span class="req">*</span></label>
                  <input type="text" name="nombre" class="form-input"
                         value="<?= htmlspecialchars($producto['nombre'] ?? '') ?>"
                         placeholder="Ej: Amoxicilina 500mg / Jabón Protex / Crema Ponds" required />
                </div>

                <!-- Campos exclusivos de medicamentos -->
                <div id="campos-farmaceuticos" style="<?= (($producto['es_medicamento'] ?? 1) == 0) ? 'display:none;' : '' ?>">
                  <div class="form-grid-2">
                    <div class="form-group">
                      <label class="form-label">Principio activo <span class="req med-req">*</span></label>
                      <input type="text" name="principio_activo" class="form-input"
                             value="<?= htmlspecialchars($producto['principio_activo'] ?? '') ?>"
                             placeholder="Ej: Amoxicilina trihidrato" />
                    </div>
                    <div class="form-group">
                      <label class="form-label">Concentración <span class="req med-req">*</span></label>
                      <input type="text" name="concentracion" class="form-input"
                             value="<?= htmlspecialchars($producto['concentracion'] ?? '') ?>"
                             placeholder="Ej: 500mg" />
                    </div>
                  </div>
                  <div class="form-grid-2">
                    <div class="form-group">
                      <label class="form-label">Forma farmacéutica <span class="req med-req">*</span></label>
                      <select name="forma_farmaceutica" class="form-select">
                        <option value="">Seleccionar...</option>
                        <?php foreach(['Cápsulas','Tabletas','Jarabe','Suspensión','Inyectable','Crema','Gel','Gotas'] as $ff): ?>
                        <option value="<?= $ff ?>" <?= ($producto['forma_farmaceutica'] ?? '') === $ff ? 'selected' : '' ?>><?= $ff ?></option>
                        <?php endforeach; ?>
                      </select>
                    </div>
                    <div class="form-group">
                      <label class="form-label">Código INVIMA <span class="req med-req">*</span></label>
                      <input type="text" name="codigo_invima" class="form-input mono"
                             value="<?= htmlspecialchars($producto['codigo_invima'] ?? '') ?>"
                             placeholder="M-XXXXAR-RX" />
                      <div class="form-hint"><i data-lucide="info"></i> Consulta en registros.invima.gov.co</div>
                    </div>
                  </div>
                  <div class="form-group">
                    <label class="form-label">Requiere fórmula médica</label>
                    <div class="toggle-field">
                      <div class="toggle-field-info"><div class="toggle-field-label">Control especial (Ley 2300/2023)</div></div>
                      <label class="toggle-switch">
                        <input type="checkbox" name="control_especial" value="1"
                               <?= ($producto['control_especial'] ?? 0) ? 'checked' : '' ?> />
                        <span class="toggle-slider"></span>
                      </label>
                    </div>
                  </div>
                </div><!-- /campos-farmaceuticos -->
              </div>
            </div>
          </div>
        </div>      </div>
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

          <!-- IMÁGENES DEL PRODUCTO -->
          <?php if (isset($producto)): ?>
          <div class="side-card">
            <div class="side-card-header"><i data-lucide="image"></i> Imágenes del producto</div>
            <div class="side-card-body">
              <p style="font-size:12px;color:var(--color-muted);margin-bottom:12px;">Hasta 4 imágenes · JPG/PNG/WEBP · máx. 2 MB · La primera es la imagen principal.</p>
              <div id="imageSlots" style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                <?php
                $imgMap = [];
                foreach ($imagenes ?? [] as $img) { $imgMap[$img['orden']] = $img; }
                for ($slot = 1; $slot <= 4; $slot++):
                  $img = $imgMap[$slot] ?? null;
                ?>
                <div class="img-slot" id="slot-<?= $slot ?>" style="border:2px dashed var(--color-border);border-radius:10px;padding:8px;text-align:center;position:relative;min-height:90px;display:flex;align-items:center;justify-content:center;flex-direction:column;gap:6px;">
                  <?php if ($img): ?>
                    <img src="<?= $basePath ?>/assets/uploads/productos/<?= $producto['producto_id'] ?>/<?= htmlspecialchars($img['nombre_archivo']) ?>"
                         style="width:100%;max-height:70px;object-fit:cover;border-radius:6px;" />
                    <span style="font-size:10px;color:var(--color-muted);"><?= $slot == 1 ? '⭐ Principal' : 'Imagen '.$slot ?></span>
                    <button type="button" onclick="eliminarImagen(<?= $img['imagen_id'] ?>, <?= $slot ?>)"
                            style="position:absolute;top:4px;right:4px;background:#ef4444;color:#fff;border:none;border-radius:50%;width:20px;height:20px;font-size:11px;cursor:pointer;line-height:1;">×</button>
                  <?php else: ?>
                    <label style="cursor:pointer;width:100%;height:100%;display:flex;flex-direction:column;align-items:center;justify-content:center;gap:4px;">
                      <i data-lucide="plus-circle" style="width:22px;height:22px;color:var(--color-muted);"></i>
                      <span style="font-size:10px;color:var(--color-muted);"><?= $slot == 1 ? 'Principal' : 'Imagen '.$slot ?></span>
                      <input type="file" accept="image/jpeg,image/png,image/webp" style="display:none;"
                             onchange="subirImagen(this, <?= $slot ?>)" />
                    </label>
                  <?php endif; ?>
                </div>
                <?php endfor; ?>
              </div>
              <div id="uploadMsg" style="font-size:12px;margin-top:8px;min-height:18px;"></div>
            </div>
          </div>
          <?php else: ?>
          <div class="side-card">
            <div class="side-card-header"><i data-lucide="image"></i> Imágenes del producto</div>
            <div class="side-card-body">
              <p style="font-size:12px;color:var(--color-muted);">Guarda el producto primero. Luego podrás añadir hasta 4 imágenes desde la edición.</p>
            </div>
          </div>
          <?php endif; ?>

          <!-- Vista Previa -->
          <div class="side-card">
            <div class="side-card-header"><i data-lucide="eye"></i> Vista previa</div>
            <div class="side-card-body">
              <div class="preview-pill-row" id="previewCard">
                <div class="preview-pill-icon"><i data-lucide="package" id="previewIcon"></i></div>
                <div>
                  <div class="preview-name" id="previewNombre">Completa el nombre para ver la vista previa...</div>
                  <div class="preview-sub" id="previewSub"></div>
                </div>
              </div>
            </div>
          </div>

          <!-- Progreso -->
          <div class="side-card">
            <div class="side-card-header"><i data-lucide="list-checks"></i> Progreso</div>
            <div class="side-card-body">
              <div class="progress-item"><div class="progress-dot" id="progressId"><i data-lucide="check"></i></div><span class="progress-label" id="labelId">Identificación</span></div>
              <div class="progress-item"><div class="progress-dot" id="progressClasif"><i data-lucide="check"></i></div><span class="progress-label" id="labelClasif">Clasificación</span></div>
              <div class="progress-item"><div class="progress-dot" id="progressPrecios"><i data-lucide="check"></i></div><span class="progress-label" id="labelPrecios">Inventario y precios</span></div>
            </div>
            <div class="required-note"><span class="req">*</span> Campos marcados son obligatorios</div>
          </div>

          <!-- Guía -->
          <div class="side-card" id="guiaRegulatoria">
            <div class="side-card-header"><i data-lucide="book-open"></i> Guía regulatoria</div>
            <div class="side-card-body">
              <div class="tip-item">
                <div class="tip-icon" style="background:var(--color-info-soft);"><i data-lucide="shield-check" style="color:var(--color-info);"></i></div>
                <div class="tip-text">Consulta el <strong>Registro INVIMA</strong> en <a href="https://registros.invima.gov.co" target="_blank" style="color:var(--color-primary);text-decoration:underline;">registros.invima.gov.co</a></div>
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
<script>
if (window.lucide) lucide.createIcons();

// ── Toggle tipo de producto ──────────────────────────────────────────────────
const toggleMed = document.getElementById('esMedicamentoToggle');
const camposFarma = document.getElementById('campos-farmaceuticos');
const tipoLabel = document.getElementById('tipoLabel');
const guiaEl = document.getElementById('guiaRegulatoria');

function aplicarTipo(esMed) {
  if (camposFarma) camposFarma.style.display = esMed ? '' : 'none';
  if (tipoLabel) tipoLabel.textContent = esMed ? '💊 Medicamento' : '🛒 Miscelánea / Consumo';
  if (guiaEl) guiaEl.style.display = esMed ? '' : 'none';
  // Quitar/añadir required a campos farmacéuticos
  camposFarma?.querySelectorAll('input,select').forEach(el => {
    if (esMed) el.removeAttribute('data-notrequired');
    else el.removeAttribute('required');
  });
}

toggleMed?.addEventListener('change', () => aplicarTipo(toggleMed.checked));
aplicarTipo(toggleMed?.checked ?? true);

// ── Subida de imágenes AJAX ──────────────────────────────────────────────────
const productoId = <?= isset($producto) ? (int)$producto['producto_id'] : 'null' ?>;
const basePath   = '<?= $basePath ?>';

async function subirImagen(input, slot) {
  if (!input.files[0] || !productoId) return;
  const msg = document.getElementById('uploadMsg');
  msg.textContent = '⏳ Subiendo imagen...';
  msg.style.color = 'var(--color-muted)';

  const fd = new FormData();
  fd.append('imagen', input.files[0]);

  try {
    const res = await fetch(`${basePath}/inventario/productos/${productoId}/imagenes`, {
      method: 'POST', body: fd
    });
    const data = await res.json();
    if (data.success) {
      msg.textContent = '✅ Imagen guardada';
      msg.style.color = 'var(--color-success)';
      setTimeout(() => location.reload(), 800);
    } else {
      msg.textContent = '❌ ' + (data.error || 'Error al subir');
      msg.style.color = '#ef4444';
    }
  } catch { msg.textContent = '❌ Fallo de conexión'; msg.style.color = '#ef4444'; }
}

async function eliminarImagen(imagenId, slot) {
  if (!confirm('¿Eliminar esta imagen?')) return;
  const msg = document.getElementById('uploadMsg');
  try {
    const res = await fetch(`${basePath}/inventario/productos/${productoId}/imagenes/${imagenId}`, {
      method: 'DELETE'
    });
    const data = await res.json();
    if (data.success) { msg.textContent = '🗑️ Imagen eliminada'; setTimeout(() => location.reload(), 500); }
    else { msg.textContent = '❌ ' + (data.error || 'Error'); }
  } catch { msg.textContent = '❌ Fallo de conexión'; }
}

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
  
  const margenValue = document.getElementById('margenValue');
  const margenBadge = document.getElementById('margenBadge');

  if (compra > 0 && venta > 0) {
    const margen = ((venta - compra) / compra) * 100;
    margenValue.textContent = (margen > 0 ? '+' : '') + margen.toFixed(1) + '%';
    
    if (margen > 0.01) {
      margenValue.className = 'margen-value positive';
      margenBadge.className = 'margen-badge positive';
      margenBadge.textContent = 'Rentable';
    } else if (margen < -0.01) {
      margenValue.className = 'margen-value negative';
      margenBadge.className = 'margen-badge negative';
      margenBadge.textContent = 'Pérdida';
    } else {
      margenValue.className = 'margen-value zero';
      margenBadge.className = 'margen-badge zero';
      margenBadge.textContent = 'Sin margen';
    }
  } else {
    margenValue.textContent = '-';
    margenValue.className = 'margen-value zero';
    margenBadge.className = 'margen-badge zero';
    margenBadge.textContent = 'Sin datos';
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

<?php 
$contenido = ob_get_clean(); 
require __DIR__ . '/../layouts/base.php'; 
?>
