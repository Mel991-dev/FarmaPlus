<?php
// views/inventario/proveedores.php
$titulo = 'Proveedores';
ob_start();
?>

<?php if (!empty($_GET['success'])): ?>
<div class="mb-6 bg-fp-success/10 border-l-4 border-fp-success p-3 rounded-lg flex items-center gap-2 text-sm font-semibold text-fp-success shadow-sm">
  <i data-lucide="circle-check" class="w-5 h-5"></i><?= htmlspecialchars($_GET['success']) ?>
</div>
<?php elseif (!empty($_GET['error'])): ?>
<div class="mb-6 bg-fp-error/10 border-l-4 border-fp-error p-3 rounded-lg flex items-center gap-2 text-sm font-semibold text-fp-error shadow-sm">
  <i data-lucide="alert-circle" class="w-5 h-5"></i><?= htmlspecialchars($_GET['error']) ?>
</div>
<?php endif; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div>
    <h1 class="text-2xl font-bold text-fp-text tracking-tight flex items-center gap-2">
      <i data-lucide="building-2" class="w-6 h-6 text-fp-primary"></i> Proveedores
    </h1>
    <p class="text-[13px] text-fp-muted mt-0.5">Laboratorios y distribuidores registrados en el sistema</p>
  </div>
  <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/proveedores/crear" class="bg-fp-primary hover:bg-fp-primary-dark text-white text-[13px] font-bold py-2.5 px-4 rounded-lg flex items-center justify-center gap-2 shadow-sm transition-colors shrink-0">
    <i data-lucide="plus" class="w-4 h-4"></i> Nuevo proveedor
  </a>
</div>

<!-- Toolbar -->
<div class="flex flex-col md:flex-row flex-wrap gap-3 mb-6 bg-white p-3 rounded-xl border border-fp-border shadow-sm">
  <div class="relative flex-1 min-w-[200px]">
    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-fp-muted"></i>
    <input type="text" id="buscarProv" class="w-full h-10 pl-9 pr-3 bg-fp-bg-main border border-fp-border rounded-lg text-[13px] outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20" placeholder="Buscar por nombre o NIT..." />
  </div>
</div>

<!-- Tabla Proveedores -->
<div class="bg-white rounded-xl border border-fp-border flex flex-col shadow-sm overflow-hidden">
  <div class="p-4 border-b border-fp-border flex items-center justify-between bg-fp-bg-main/30">
    <div class="flex items-center gap-2 font-bold text-[15px] text-fp-text">
       <i data-lucide="building-2" class="w-5 h-5 text-fp-primary"></i> Listado de proveedores
    </div>
    <span class="text-[12px] font-bold text-fp-primary/80 bg-fp-primary/10 px-2.5 py-1 rounded-md tracking-wider uppercase"><?= count($proveedores ?? []) ?> registros</span>
  </div>

  <?php if (empty($proveedores)): ?>
    <div class="flex flex-col items-center justify-center p-12">
      <div class="w-16 h-16 rounded-full bg-fp-bg-main flex items-center justify-center mb-3">
        <i data-lucide="building-2" class="w-8 h-8 text-fp-muted opacity-80"></i>
      </div>
      <p class="text-[15px] font-bold text-fp-text mb-1">No hay proveedores registrados</p>
      <span class="text-[13px] text-fp-muted">Añade el primer proveedor o laboratorio distribuidor.</span>
    </div>
  <?php else: ?>
    <div class="w-full overflow-x-auto">
      <table id="tablaProv" class="w-full text-left border-collapse min-w-[800px]">
        <thead>
          <tr class="bg-white border-b border-fp-border text-[11px] uppercase tracking-[1.5px] font-bold text-fp-muted">
            <th class="px-5 py-4 w-[250px]">Nombre del proveedor</th>
            <th class="px-5 py-4">NIT</th>
            <th class="px-5 py-4">País</th>
            <th class="px-5 py-4">Contacto</th>
            <th class="px-5 py-4 text-center">Estado</th>
            <th class="px-5 py-4 text-center">Acciones</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-fp-border/50 text-[13px] font-medium text-fp-text">
          <?php foreach ($proveedores as $prov): ?>
          <tr data-nombre="<?= strtolower(htmlspecialchars($prov['nombre'])) ?>" data-nit="<?= htmlspecialchars($prov['nit']) ?>" class="hover:bg-fp-bg-main/50 transition-colors group">
            <td class="px-5 py-4">
              <div class="font-bold text-[14px] text-fp-text mb-0.5"><?= htmlspecialchars($prov['nombre']) ?></div>
              <?php if ($prov['sitio_web']): ?>
                <a href="<?= htmlspecialchars($prov['sitio_web']) ?>" target="_blank" class="text-[11px] font-semibold text-fp-primary hover:underline flex items-center gap-1"><i data-lucide="globe" class="w-3 h-3"></i> Sitio web</a>
              <?php endif; ?>
            </td>
            <td class="px-5 py-4">
              <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded bg-fp-bg-main border border-fp-border font-mono text-[12px] font-semibold text-fp-text">
                 <?= htmlspecialchars($prov['nit']) ?>
              </span>
            </td>
            <td class="px-5 py-4 text-[13px] font-medium text-fp-muted flex items-center gap-1.5 mt-2">
              <i data-lucide="map-pin" class="w-4 h-4"></i> <?= htmlspecialchars($prov['pais_origen'] ?? 'Colombia') ?>
            </td>
            <td class="px-5 py-4">
              <div class="flex flex-col gap-1">
                <div class="flex items-center gap-1.5 text-fp-text"><i data-lucide="phone" class="w-3.5 h-3.5 text-fp-muted"></i> <?= htmlspecialchars($prov['telefono'] ?: '—') ?></div>
                <div class="flex items-center gap-1.5 text-fp-text"><i data-lucide="mail" class="w-3.5 h-3.5 text-fp-muted"></i> <?= htmlspecialchars($prov['correo'] ?: '—') ?></div>
              </div>
            </td>
            <td class="px-5 py-4 text-center">
              <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-fp-success/10 text-fp-success text-[10px] font-bold uppercase tracking-wider"><i data-lucide="check-circle" class="w-3 h-3"></i> Activo</span>
            </td>
            <td class="px-5 py-4">
              <div class="flex items-center justify-center gap-1.5">
                <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/proveedores/<?= $prov['proveedor_id'] ?>/editar" class="w-8 h-8 rounded-lg flex items-center justify-center bg-fp-bg-main text-fp-text hover:bg-fp-primary hover:text-white transition-colors" title="Editar">
                  <i data-lucide="pencil" class="w-4 h-4"></i>
                </a>
              </div>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<script>
  document.getElementById('buscarProv')?.addEventListener('input', function() {
    const q = this.value.toLowerCase();
    document.querySelectorAll('#tablaProv tbody tr').forEach(row => {
      row.style.display = (!q || row.dataset.nombre.includes(q) || row.dataset.nit.includes(q)) ? '' : 'none';
    });
  });
</script>

<?php 
$contenido = ob_get_clean();
require __DIR__ . '/../layouts/base.php'; 
?>
