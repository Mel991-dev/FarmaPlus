<?php
// views/inventario/proveedor_form.php
// Variables: $proveedor (array|null), $basePath
$esEdicion = !empty($proveedor);
$pageTitle = $esEdicion ? 'Editar Proveedor' : 'Nuevo Proveedor';
$actionUrl = $esEdicion
    ? $basePath . '/inventario/proveedores/' . $proveedor['proveedor_id'] . '/editar'
    : $basePath . '/inventario/proveedores/crear';

$titulo = $pageTitle;
ob_start(); 
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div>
    <h1 class="text-2xl font-bold text-fp-text tracking-tight flex items-center gap-2">
      <a href="<?= $basePath ?>/inventario/proveedores" class="text-fp-muted hover:text-fp-primary transition-colors">
        <i data-lucide="arrow-left" class="w-6 h-6"></i>
      </a>
      <?= $pageTitle ?>
    </h1>
    <p class="text-[13px] text-fp-muted mt-0.5 ml-8"><?= $esEdicion ? 'Modifica los datos del proveedor registrado.' : 'Añade un nuevo laboratorio o distribuidor al sistema.' ?></p>
  </div>
</div>

      <div class="card">
        <div class="card-header">
          <div class="card-header-title"><i data-lucide="building-2"></i> Datos del proveedor</div>
          <div class="card-header-desc">Información fiscal y de contacto del proveedor.</div>
        </div>
        <div class="card-body">
          <div class="form-grid-2">

            <div class="form-field">
              <label class="form-label">Nombre del proveedor <span class="required">*</span></label>
              <input type="text" name="nombre" class="form-input"
                     value="<?= htmlspecialchars($proveedor['nombre'] ?? '') ?>"
                     placeholder="Ej: Laboratorios MK" required />
            </div>

            <div class="form-field">
              <label class="form-label">NIT <span class="required">*</span></label>
              <input type="text" name="nit" class="form-input"
                     value="<?= htmlspecialchars($proveedor['nit'] ?? '') ?>"
                     placeholder="Ej: 800123456-7"
                     <?= $esEdicion ? 'readonly style="background:var(--color-bg-main);color:var(--color-text-secondary);"' : '' ?>
                     required />
              <?php if ($esEdicion): ?>
              <span class="form-help">El NIT no puede modificarse después del registro.</span>
              <?php else: ?>
              <span class="form-help">Número único del proveedor (HU-AUX-03). No puede repetirse.</span>
              <?php endif; ?>
            </div>

            <div class="form-field">
              <label class="form-label">País de origen</label>
              <input type="text" name="pais_origen" class="form-input"
                     value="<?= htmlspecialchars($proveedor['pais_origen'] ?? 'Colombia') ?>"
                     placeholder="Colombia" />
            </div>

            <div class="form-field">
              <label class="form-label">Teléfono</label>
              <input type="text" name="telefono" class="form-input"
                     value="<?= htmlspecialchars($proveedor['telefono'] ?? '') ?>"
                     placeholder="+57 300 000 0000" />
            </div>

            <div class="form-field">
              <label class="form-label">Correo electrónico</label>
              <input type="email" name="correo" class="form-input"
                     value="<?= htmlspecialchars($proveedor['correo'] ?? '') ?>"
                     placeholder="ventas@proveedor.com" />
            </div>

            <div class="form-field">
              <label class="form-label">Sitio web</label>
              <input type="url" name="sitio_web" class="form-input"
                     value="<?= htmlspecialchars($proveedor['sitio_web'] ?? '') ?>"
                     placeholder="https://www.proveedor.com" />
            </div>

          </div>
        </div>
      </div>

      <div class="form-actions-bar">
        <a href="<?= $basePath ?>/inventario/proveedores" class="btn-secondary-cancel">
          <i data-lucide="x"></i> Cancelar
        </a>
        <button type="submit" class="btn-primary">
          <i data-lucide="save"></i> <?= $esEdicion ? 'Guardar cambios' : 'Crear proveedor' ?>
        </button>
      </div>

    </form>
<script>if (window.lucide) lucide.createIcons();</script>
<?php 
$contenido = ob_get_clean(); 
require __DIR__ . '/../layouts/base.php'; 
?>
