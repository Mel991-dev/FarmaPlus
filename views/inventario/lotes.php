<?php
// views/inventario/lotes.php
$titulo = 'Control de Lotes';
ob_start();

$hoy = new \DateTime();
$lotesOk = 0; $lotesWarn = 0; $lotesCrit = 0; $totalUnidades = 0;
foreach ($lotes as $l) {
  $totalUnidades += (int)($l['cantidad_actual'] ?? 0);
  $fv = new \DateTime($l['fecha_vencimiento'] ?? 'today');
  $diff = $hoy->diff($fv);
  $dias = (int)$diff->days * ($fv >= $hoy && !$diff->invert ? 1 : -1);
  if ($dias < 0 || $l['cantidad_actual'] == 0) { $lotesCrit++; }
  elseif ($dias <= 30) { $lotesWarn++; }
  else { $lotesOk++; }
}
$totalLotes = count($lotes);
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
      <i data-lucide="layers" class="w-6 h-6 text-fp-primary"></i> Control de Lotes
    </h1>
    <p class="text-[13px] text-fp-muted mt-0.5">Gestión de lotes por método FEFO — First Expired, First Out</p>
  </div>
  <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/lotes/registrar" class="bg-fp-primary hover:bg-fp-primary-dark text-white text-[13px] font-bold py-2.5 px-4 rounded-lg flex items-center justify-center gap-2 shadow-sm transition-colors shrink-0">
    <i data-lucide="plus" class="w-4 h-4"></i> Registrar lote
  </a>
</div>

<!-- Stats Row -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  <div class="bg-white rounded-xl border border-fp-border p-4 flex items-center gap-4 shadow-sm relative overflow-hidden group">
    <div class="absolute left-0 top-0 bottom-0 w-1 bg-fp-primary"></div>
    <div class="w-12 h-12 rounded-xl bg-fp-primary/10 text-fp-primary flex items-center justify-center shrink-0">
      <i data-lucide="layers" class="w-6 h-6"></i>
    </div>
    <div>
      <div class="text-2xl font-black text-fp-text font-mono tracking-tight leading-none mb-0.5"><?= $totalLotes ?></div>
      <div class="text-[11px] font-bold text-fp-muted uppercase tracking-wider">Total de lotes</div>
    </div>
  </div>

  <div class="bg-white rounded-xl border border-fp-border p-4 flex items-center gap-4 shadow-sm relative overflow-hidden group">
    <div class="absolute left-0 top-0 bottom-0 w-1 bg-fp-success"></div>
    <div class="w-12 h-12 rounded-xl bg-fp-success/10 text-fp-success flex items-center justify-center shrink-0">
      <i data-lucide="package" class="w-6 h-6"></i>
    </div>
    <div>
      <div class="text-2xl font-black text-fp-success font-mono tracking-tight leading-none mb-0.5"><?= number_format($totalUnidades) ?></div>
      <div class="text-[11px] font-bold text-fp-success uppercase tracking-wider">Unidades en stock</div>
    </div>
  </div>

  <div class="bg-white rounded-xl border border-fp-border p-4 flex items-center gap-4 shadow-sm relative overflow-hidden group">
    <div class="absolute left-0 top-0 bottom-0 w-1 bg-fp-warning"></div>
    <div class="w-12 h-12 rounded-xl bg-fp-warning/10 text-fp-warning flex items-center justify-center shrink-0">
      <i data-lucide="alert-triangle" class="w-6 h-6"></i>
    </div>
    <div>
      <div class="text-2xl font-black text-fp-warning font-mono tracking-tight leading-none mb-0.5"><?= $lotesWarn ?></div>
      <div class="text-[11px] font-bold text-fp-warning uppercase tracking-wider">Vencen en &le; 30 días</div>
    </div>
  </div>

  <div class="bg-white rounded-xl border border-fp-border p-4 flex items-center gap-4 shadow-sm relative overflow-hidden group">
    <div class="absolute left-0 top-0 bottom-0 w-1 bg-fp-error"></div>
    <div class="w-12 h-12 rounded-xl bg-fp-error/10 text-fp-error flex items-center justify-center shrink-0">
      <i data-lucide="x-circle" class="w-6 h-6"></i>
    </div>
    <div>
      <div class="text-2xl font-black text-fp-error font-mono tracking-tight leading-none mb-0.5"><?= $lotesCrit ?></div>
      <div class="text-[11px] font-bold text-fp-error uppercase tracking-wider">Agotados / Vencidos</div>
    </div>
  </div>
</div>

<!-- Toolbar -->
<div class="flex flex-col md:flex-row flex-wrap gap-3 mb-6 bg-white p-3 rounded-xl border border-fp-border shadow-sm">
  <div class="relative flex-1 min-w-[200px]">
    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-fp-muted"></i>
    <input type="text" id="buscarLote" class="w-full h-10 pl-9 pr-3 bg-fp-bg-main border border-fp-border rounded-lg text-[13px] outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20" placeholder="Buscar por producto o número de lote..." />
  </div>
  <select id="filtroEstado" class="h-10 px-3 bg-white border border-fp-border rounded-lg text-[13px] font-medium text-fp-text outline-none focus:border-fp-primary w-full md:w-auto">
    <option value="">Todos los estados</option>
    <option value="ok">Vigentes (> 30 días)</option>
    <option value="warn">Por vencer (≤ 30 días)</option>
    <option value="crit">Agotados / Vencidos</option>
  </select>
  <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/lotes?export=1" class="h-10 px-4 flex items-center justify-center gap-2 bg-white border border-fp-border hover:border-fp-primary hover:text-fp-primary rounded-lg text-[13px] font-bold transition-colors w-full md:w-auto text-fp-text whitespace-nowrap">
    <i data-lucide="download" class="w-4 h-4"></i> Exportar
  </a>
</div>

<!-- Tabla de Lotes -->
<div class="bg-white rounded-xl border border-fp-border flex flex-col shadow-sm overflow-hidden mb-6">
  <div class="p-4 border-b border-fp-border flex items-center justify-between bg-fp-bg-main/30">
    <div class="flex items-center gap-2 font-bold text-[15px] text-fp-text">
       <i data-lucide="layers" class="w-5 h-5 text-fp-primary"></i> Lotes registrados
    </div>
    <span class="text-[12px] font-bold text-fp-primary/80 bg-fp-primary/10 px-2.5 py-1 rounded-md tracking-wider uppercase"><?= $totalLotes ?> lotes</span>
  </div>

  <?php if (empty($lotes)): ?>
    <div class="flex flex-col items-center justify-center p-12">
      <div class="w-16 h-16 rounded-full bg-fp-bg-main flex items-center justify-center mb-3">
        <i data-lucide="inbox" class="w-8 h-8 text-fp-muted opacity-80"></i>
      </div>
      <p class="text-[15px] font-bold text-fp-text mb-1">No hay lotes registrados aún</p>
      <span class="text-[13px] text-fp-muted text-center max-w-sm mb-4">Registra el primer lote de medicamentos para iniciar el control FEFO.</span>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/lotes/registrar" class="bg-fp-primary text-white text-[13px] font-bold py-2 px-4 rounded-lg flex items-center gap-2 hover:bg-fp-primary-dark transition-colors">
        <i data-lucide="plus" class="w-4 h-4"></i> Registrar primer lote
      </a>
    </div>
  <?php else: ?>
    <div class="w-full overflow-x-auto">
      <table id="tablaLotes" class="w-full text-left border-collapse min-w-[1000px]">
        <thead>
          <tr class="bg-white border-b border-fp-border text-[11px] uppercase tracking-[1.5px] font-bold text-fp-muted">
            <th class="px-5 py-4">Nº Lote</th>
            <th class="px-5 py-4 w-[250px]">Producto</th>
            <th class="px-5 py-4">Vencimiento</th>
            <th class="px-5 py-4">Días restantes</th>
            <th class="px-5 py-4">Stock inicial</th>
            <th class="px-5 py-4">Stock actual</th>
            <th class="px-5 py-4">Estado</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-fp-border/50 text-[13px] font-medium text-fp-text">
          <?php foreach ($lotes as $lote):
            $fv = new \DateTime($lote['fecha_vencimiento']);
            $diffInterval = $hoy->diff($fv);
            $dias = (int)$diffInterval->days * ($fv >= $hoy && !$diffInterval->invert ? 1 : -1);
            $stockActual = (int)($lote['cantidad_actual'] ?? 0);
            $stockInicial = (int)($lote['cantidad_inicial'] ?? 1);
            $pct = $stockInicial > 0 ? min(100, round(($stockActual / $stockInicial) * 100)) : 0;

            if ($dias < 0) {
              $pillClass = 'bg-[#E74C3C]/10 text-[#E74C3C] border-[#E74C3C]/20'; $pillLabel = 'Vencido'; $pillIcon = 'x-circle';
              $barColor = 'bg-[#E74C3C]'; $textColor = 'text-[#E74C3C]';
              $badgeClass = 'bg-[#E74C3C]/10 text-[#E74C3C] border-[#E74C3C]/20'; $badgeLabel = 'Vencido'; $badgeIcon = 'alert-circle';
            } elseif ($dias <= 30) {
              $pillClass = 'bg-[#F1C40F]/20 text-[#D4AC0D] border-[#F1C40F]/30'; $pillLabel = "{$dias} días"; $pillIcon = 'alert-triangle';
              $barColor = 'bg-[#F1C40F]';  $textColor = 'text-[#D4AC0D]';
              $badgeClass = 'bg-[#F1C40F]/20 text-[#D4AC0D] border-[#F1C40F]/30'; $badgeLabel = 'Por vencer'; $badgeIcon = 'alert-circle';
            } else {
              $pillClass = 'bg-[#2ECC71]/10 text-[#27AE60] border-[#2ECC71]/20'; $pillLabel = "{$dias} días"; $pillIcon = 'check-circle';
              $barColor = 'bg-[#2ECC71]';  $textColor = 'text-[#27AE60]';
              $badgeClass = 'bg-[#2ECC71]/10 text-[#27AE60] border-[#2ECC71]/20'; $badgeLabel = 'Vigente'; $badgeIcon = 'circle-check';
            }
            if ($stockActual === 0) {
              $badgeClass = 'bg-fp-bg-main text-fp-muted border-fp-border'; $badgeLabel = 'Agotado'; $badgeIcon = 'minus-circle';
              $pillClass = 'bg-fp-bg-main text-fp-muted border-fp-border';
              $barColor = 'bg-fp-border';  $textColor = 'text-fp-muted';
            }

            $dataFilter = ($stockActual === 0 || $dias < 0) ? 'crit' : ($dias <= 30 ? 'warn' : 'ok');
          ?>
          <tr data-estado="<?= $dataFilter ?>"
              data-nombre="<?= strtolower(htmlspecialchars($lote['producto_nombre'] ?? '')) ?>"
              data-lote="<?= strtolower(htmlspecialchars($lote['numero_lote'] ?? '')) ?>"
              class="hover:bg-fp-bg-main/50 transition-colors">
            
            <td class="px-5 py-4">
              <span class="inline-flex items-center px-2 py-1 rounded bg-fp-bg-main border border-fp-border font-mono text-[12px] font-semibold text-fp-text">
                <?= htmlspecialchars($lote['numero_lote']) ?>
              </span>
            </td>
            
            <td class="px-5 py-4">
              <div class="font-bold text-[14px] text-fp-text truncate" title="<?= htmlspecialchars($lote['producto_nombre'] ?? '—') ?>"><?= htmlspecialchars($lote['producto_nombre'] ?? '—') ?></div>
              <div class="text-[11px] text-fp-muted mt-0.5 truncate" title="<?= htmlspecialchars($lote['proveedor_nombre'] ?? '—') ?>"><i data-lucide="building-2" class="w-3 h-3 inline"></i> <?= htmlspecialchars($lote['proveedor_nombre'] ?? '—') ?></div>
            </td>
            
            <td class="px-5 py-4 font-mono text-[13px] text-fp-text">
              <?= date('d/m/Y', strtotime($lote['fecha_vencimiento'])) ?>
            </td>
            
            <td class="px-5 py-4">
              <span class="inline-flex items-center gap-1 px-2 py-1 border rounded-md text-[11px] font-bold tracking-wide <?= $pillClass ?>">
                <i data-lucide="<?= $pillIcon ?>" class="w-3 h-3"></i> <?= $pillLabel ?>
              </span>
            </td>
            
            <td class="px-5 py-4 text-[13px] font-bold text-fp-muted">
              <?= number_format((int)($lote['cantidad_inicial'] ?? 0)) ?> <span class="text-[10px] font-normal">un.</span>
            </td>
            
            <td class="px-5 py-4 w-[160px]">
              <div class="flex items-center gap-2 mb-1.5">
                <span class="font-bold font-mono text-[13px] <?= $textColor ?> w-8 text-right"><?= number_format($stockActual) ?></span>
              </div>
              <div class="h-1.5 w-full bg-fp-border/80 rounded-full overflow-hidden">
                <div class="h-full <?= $barColor ?>" style="width: <?= $pct ?>%"></div>
              </div>
            </td>
            
            <td class="px-5 py-4">
              <span class="inline-flex items-center gap-1.5 px-2 py-1 border rounded-md text-[10px] font-bold uppercase tracking-wider <?= $badgeClass ?>">
                <i data-lucide="<?= $badgeIcon ?>" class="w-3 h-3"></i> <?= $badgeLabel ?>
              </span>
            </td>
            
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  <?php endif; ?>
</div>

<script>
document.getElementById('buscarLote')?.addEventListener('input', filtrar);
document.getElementById('filtroEstado')?.addEventListener('change', filtrar);

function filtrar() {
  const q     = document.getElementById('buscarLote').value.toLowerCase();
  const est   = document.getElementById('filtroEstado').value;
  document.querySelectorAll('#tablaLotes tbody tr').forEach(row => {
    const nombre = row.dataset.nombre || '';
    const lote   = row.dataset.lote   || '';
    const estado = row.dataset.estado || '';
    const matchQ   = !q   || nombre.includes(q) || lote.includes(q);
    const matchEst = !est || estado === est;
    row.style.display = matchQ && matchEst ? '' : 'none';
  });
}
</script>

<?php 
$contenido = ob_get_clean();
require __DIR__ . '/../layouts/base.php'; 
?>
