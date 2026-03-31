<?php
// views/inventario/alertas.php
$titulo = 'Alertas de Inventario';
ob_start();
?>

<?php if (!empty($_GET['success'])): ?>
<div class="mb-6 bg-fp-success/10 border-l-4 border-fp-success p-3 rounded-lg flex items-center gap-2 text-sm font-semibold text-fp-success shadow-sm">
  <i data-lucide="circle-check" class="w-5 h-5"></i> <?= htmlspecialchars($_GET['success']) ?>
</div>
<?php elseif (!empty($_GET['error'])): ?>
<div class="mb-6 bg-fp-error/10 border-l-4 border-fp-error p-3 rounded-lg flex items-center gap-2 text-sm font-semibold text-fp-error shadow-sm">
  <i data-lucide="alert-circle" class="w-5 h-5"></i> <?= htmlspecialchars($_GET['error']) ?>
</div>
<?php endif; ?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div>
    <h1 class="text-2xl font-bold text-fp-text tracking-tight flex items-center gap-2">
      <i data-lucide="bell-ring" class="w-6 h-6 text-fp-error/80"></i> Alertas de Inventario
    </h1>
    <p class="text-[13px] text-fp-muted mt-0.5">Alertas automáticas de stock mínimo y vencimiento de lotes</p>
  </div>
</div>

<!-- Stats Row -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-6">
  <!-- Alertas Activas -->
  <div class="bg-white rounded-xl border border-fp-border p-4 flex items-center gap-4 shadow-sm relative overflow-hidden">
    <div class="absolute left-0 top-0 bottom-0 w-1 bg-fp-primary"></div>
    <div class="w-12 h-12 rounded-xl bg-fp-primary/10 text-fp-primary flex items-center justify-center shrink-0">
        <i data-lucide="bell" class="w-6 h-6"></i>
    </div>
    <div>
        <div class="text-2xl font-black text-fp-text font-mono tracking-tight leading-none mb-0.5"><?= $totalAlertas ?? 0 ?></div>
        <div class="text-[11px] font-bold text-fp-muted uppercase tracking-wider">Alertas activas</div>
    </div>
  </div>

  <!-- Stock Mínimo Crítico -->
  <div class="bg-white rounded-xl border border-fp-border p-4 flex items-center gap-4 shadow-sm relative overflow-hidden">
    <div class="absolute left-0 top-0 bottom-0 w-1 bg-fp-error"></div>
    <div class="w-12 h-12 rounded-xl bg-fp-error/10 text-fp-error flex items-center justify-center shrink-0">
        <i data-lucide="package-minus" class="w-6 h-6"></i>
    </div>
    <div>
        <div class="text-2xl font-black text-fp-error font-mono tracking-tight leading-none mb-0.5"><?= count($alertasStock ?? []) ?></div>
        <div class="text-[11px] font-bold text-fp-error uppercase tracking-wider">Stock mínimo crítico</div>
    </div>
  </div>

  <!-- Lotes por Vencer -->
  <div class="bg-white rounded-xl border border-fp-border p-4 flex items-center gap-4 shadow-sm relative overflow-hidden">
    <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#F1C40F]"></div>
    <div class="w-12 h-12 rounded-xl bg-[#F1C40F]/10 text-[#D4AC0D] flex items-center justify-center shrink-0">
        <i data-lucide="calendar-x" class="w-6 h-6"></i>
    </div>
    <div>
        <div class="text-2xl font-black text-[#D4AC0D] font-mono tracking-tight leading-none mb-0.5"><?= count($alertasVencimiento ?? []) ?></div>
        <div class="text-[11px] font-bold text-[#D4AC0D] uppercase tracking-wider">Lotes por vencer</div>
    </div>
  </div>
</div>

<!-- Tabs -->
<div class="flex items-center gap-2 mb-6 border-b border-fp-border w-full overflow-x-auto no-scrollbar scroll-smooth snap-x pb-0">
  <button class="snap-start whitespace-nowrap px-4 py-2.5 text-sm font-bold border-b-2 border-fp-primary text-fp-primary flex items-center gap-2 tab-btn" data-target="panel-todas">
    <i data-lucide="list" class="w-4 h-4"></i> Todas
    <?php if (($totalAlertas ?? 0) > 0): ?><span class="bg-fp-primary text-white text-[10px] px-1.5 py-0.5 rounded-full ml-1"><?= $totalAlertas ?></span><?php endif; ?>
  </button>
  <button class="snap-start whitespace-nowrap px-4 py-2.5 text-sm font-semibold border-b-2 border-transparent text-fp-muted hover:text-fp-text transition-colors flex items-center gap-2 tab-btn" data-target="panel-stock">
    <i data-lucide="package-minus" class="w-4 h-4"></i> Stock mínimo
    <?php if (count($alertasStock ?? []) > 0): ?><span class="bg-[#E74C3C] text-white text-[10px] px-1.5 py-0.5 rounded-full ml-1"><?= count($alertasStock) ?></span><?php endif; ?>
  </button>
  <button class="snap-start whitespace-nowrap px-4 py-2.5 text-sm font-semibold border-b-2 border-transparent text-fp-muted hover:text-fp-text transition-colors flex items-center gap-2 tab-btn" data-target="panel-vencimiento">
    <i data-lucide="calendar-x" class="w-4 h-4"></i> Vencimiento
    <?php if (count($alertasVencimiento ?? []) > 0): ?><span class="bg-[#F39C12] text-white text-[10px] px-1.5 py-0.5 rounded-full ml-1"><?= count($alertasVencimiento) ?></span><?php endif; ?>
  </button>
</div>

<!-- Panel: Todas -->
<div class="tab-panel block" id="panel-todas">
  <?php if (empty($alertas)): ?>
    <div class="flex flex-col items-center justify-center p-12 bg-white rounded-xl border border-fp-border border-dashed">
      <div class="w-16 h-16 rounded-full bg-fp-success/10 flex items-center justify-center mb-3">
        <i data-lucide="check-circle" class="w-8 h-8 text-fp-success opacity-80"></i>
      </div>
      <p class="text-[16px] font-bold text-fp-text mb-1">¡Sin alertas activas!</p>
      <span class="text-[13px] text-fp-muted">El inventario está en óptimas condiciones.</span>
    </div>
  <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <?php foreach ($alertas as $a): 
      $tipo = $a['tipo'];
      $esStock = $tipo === 'stock_minimo';
      $colorStripe = $esStock ? 'bg-[#E74C3C]' : 'bg-[#F39C12]';
      $colorIconBg = $esStock ? 'bg-[#E74C3C]/10 text-[#E74C3C]' : 'bg-[#F39C12]/10 text-[#F39C12]';
      $iconName = $esStock ? 'package-minus' : 'calendar-x';
    ?>
    <div class="bg-white rounded-xl border border-fp-border p-4 flex flex-col sm:flex-row sm:items-start gap-4 shadow-sm relative overflow-hidden group hover:border-[#E74C3C]/30 transition-colors">
      <div class="absolute left-0 top-0 bottom-0 w-1 <?= $colorStripe ?>"></div>
      
      <div class="w-10 h-10 rounded-full <?= $colorIconBg ?> flex items-center justify-center shrink-0">
        <i data-lucide="<?= $iconName ?>" class="w-5 h-5"></i>
      </div>
      
      <div class="flex-1 min-w-0">
        <div class="font-bold text-[14px] text-fp-text truncate" title="<?= htmlspecialchars($a['producto_nombre'] ?? '—') ?>"><?= htmlspecialchars($a['producto_nombre'] ?? '—') ?></div>
        <div class="text-[13px] text-fp-muted mt-1 leading-snug drop-shadow-sm"><?= htmlspecialchars($a['mensaje'] ?? '') ?></div>
        
        <div class="flex flex-wrap items-center gap-2 mt-3">
          <?php if ($esStock): ?>
            <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-[#E74C3C]/10 text-[#E74C3C] text-[11px] font-bold shadow-sm"><i data-lucide="package" class="w-3 h-3"></i> Actual: <?= (int)($a['stock_actual'] ?? 0) ?></span>
            <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-fp-bg-main border border-fp-border text-fp-muted text-[11px] font-semibold"><i data-lucide="alert-triangle" class="w-3 h-3"></i> Mín: <?= (int)($a['stock_minimo'] ?? 0) ?></span>
          <?php else: ?>
            <?php if (!empty($a['numero_lote'])): ?>
            <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-fp-bg-main border border-fp-border text-fp-text text-[11px] font-semibold"><i data-lucide="layers" class="w-3 h-3 text-fp-muted"></i> Lote: <?= htmlspecialchars($a['numero_lote']) ?></span>
            <?php endif; ?>
            <?php if (!empty($a['fecha_vencimiento'])): 
              $diasRestantes = (int)(new \DateTime())->diff(new \DateTime($a['fecha_vencimiento']))->days;
            ?>
            <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-[#F39C12]/10 text-[#F39C12] text-[11px] font-bold"><i data-lucide="calendar" class="w-3 h-3"></i> Vence en <?= $diasRestantes ?> días</span>
            <?php endif; ?>
          <?php endif; ?>
          <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-fp-muted text-[10px] font-medium ml-auto"><i data-lucide="clock" class="w-3 h-3"></i> <?= date('d/m/Y H:i', strtotime($a['created_at'] ?? 'now')) ?></span>
        </div>
      </div>
      
      <div class="mt-4 sm:mt-0 sm:ml-2 sm:self-center shrink-0">
        <form method="POST" action="<?= $basePath ?>/inventario/alertas/<?= (int)$a['alerta_id'] ?>/resolver" class="m-0">
          <button type="submit" class="w-full sm:w-auto px-3 py-1.5 bg-white border-2 border-fp-success/30 text-fp-success text-[12px] font-bold rounded-lg hover:bg-fp-success hover:border-fp-success hover:text-white transition-all shadow-sm flex items-center justify-center gap-1.5" onclick="return confirm('¿Marcar esta alerta como resuelta?')">
            <i data-lucide="check" class="w-4 h-4"></i> Resolver
          </button>
        </form>
      </div>
    </div>
    <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- Panel: Stock -->
<div class="tab-panel hidden" id="panel-stock">
  <?php if (empty($alertasStock)): ?>
    <div class="flex flex-col items-center justify-center p-12 bg-white rounded-xl border border-fp-border border-dashed">
      <div class="w-16 h-16 rounded-full bg-fp-success/10 flex items-center justify-center mb-3">
         <i data-lucide="check-circle" class="w-8 h-8 text-fp-success opacity-80"></i>
      </div>
      <p class="text-[16px] font-bold text-fp-text mb-1">Sin alertas de stock</p>
      <span class="text-[13px] text-fp-muted">Todos los productos están por encima del nivel mínimo.</span>
    </div>
  <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <?php foreach ($alertasStock as $a): ?>
    <!-- Tarjeta reducida para Stock -->
    <div class="bg-white rounded-xl border border-fp-border p-4 flex flex-col sm:flex-row gap-4 shadow-sm relative overflow-hidden group hover:border-[#E74C3C]/30">
      <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#E74C3C]"></div>
      <div class="w-10 h-10 rounded-full bg-[#E74C3C]/10 text-[#E74C3C] flex items-center justify-center shrink-0"><i data-lucide="package-minus" class="w-5 h-5"></i></div>
      <div class="flex-1 min-w-0">
        <div class="font-bold text-[14px] text-fp-text truncate"><?= htmlspecialchars($a['producto_nombre'] ?? '—') ?></div>
        <div class="text-[13px] text-fp-muted mt-1 leading-snug"><?= htmlspecialchars($a['mensaje'] ?? '') ?></div>
        <div class="flex flex-wrap items-center gap-2 mt-3">
          <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-[#E74C3C]/10 text-[#E74C3C] text-[11px] font-bold shadow-sm"><i data-lucide="package" class="w-3 h-3"></i> Actual: <?= (int)($a['stock_actual'] ?? 0) ?></span>
          <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-fp-bg-main border border-fp-border text-fp-muted text-[11px] font-semibold"><i data-lucide="alert-triangle" class="w-3 h-3"></i> Mín: <?= (int)($a['stock_minimo'] ?? 0) ?></span>
        </div>
      </div>
      <div class="mt-4 sm:mt-0 self-center shrink-0">
        <form method="POST" action="<?= $basePath ?>/inventario/alertas/<?= (int)$a['alerta_id'] ?>/resolver" class="m-0">
          <button type="submit" class="w-full sm:w-auto px-3 py-1.5 bg-white border-2 border-fp-success/30 text-fp-success text-[12px] font-bold rounded-lg hover:bg-fp-success hover:border-fp-success hover:text-white transition-all shadow-sm flex items-center justify-center gap-1.5" onclick="return confirm('¿Marcar como resuelta?')">
            <i data-lucide="check" class="w-4 h-4"></i> Resolver
          </button>
        </form>
      </div>
    </div>
    <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<!-- Panel: Vencimiento -->
<div class="tab-panel hidden" id="panel-vencimiento">
  <?php if (empty($alertasVencimiento)): ?>
    <div class="flex flex-col items-center justify-center p-12 bg-white rounded-xl border border-fp-border border-dashed">
      <div class="w-16 h-16 rounded-full bg-fp-success/10 flex items-center justify-center mb-3">
         <i data-lucide="check-circle" class="w-8 h-8 text-fp-success opacity-80"></i>
      </div>
      <p class="text-[16px] font-bold text-fp-text mb-1">Sin alertas de vencimiento</p>
      <span class="text-[13px] text-fp-muted">Ningún lote está próximo a vencer.</span>
    </div>
  <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <?php foreach ($alertasVencimiento as $a): 
      $diasRestantes = !empty($a['fecha_vencimiento']) ? (int)(new \DateTime())->diff(new \DateTime($a['fecha_vencimiento']))->days : 0;
    ?>
    <div class="bg-white rounded-xl border border-fp-border p-4 flex flex-col sm:flex-row gap-4 shadow-sm relative overflow-hidden group hover:border-[#F39C12]/30">
      <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#F39C12]"></div>
      <div class="w-10 h-10 rounded-full bg-[#F39C12]/10 text-[#F39C12] flex items-center justify-center shrink-0"><i data-lucide="calendar-x" class="w-5 h-5"></i></div>
      <div class="flex-1 min-w-0">
        <div class="font-bold text-[14px] text-fp-text truncate"><?= htmlspecialchars($a['producto_nombre'] ?? '—') ?></div>
        <div class="text-[13px] text-fp-muted mt-1 leading-snug"><?= htmlspecialchars($a['mensaje'] ?? '') ?></div>
        <div class="flex flex-wrap items-center gap-2 mt-3">
          <?php if (!empty($a['numero_lote'])): ?>
          <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-fp-bg-main border border-fp-border text-fp-text text-[11px] font-semibold"><i data-lucide="layers" class="w-3 h-3 text-fp-muted"></i> Lote: <?= htmlspecialchars($a['numero_lote']) ?></span>
          <?php endif; ?>
          <?php if (!empty($a['fecha_vencimiento'])): ?>
          <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md bg-[#F39C12]/10 text-[#F39C12] text-[11px] font-bold"><i data-lucide="calendar" class="w-3 h-3"></i> Vence en <?= $diasRestantes ?> días</span>
          <?php endif; ?>
        </div>
      </div>
      <div class="mt-4 sm:mt-0 self-center shrink-0">
        <form method="POST" action="<?= $basePath ?>/inventario/alertas/<?= (int)$a['alerta_id'] ?>/resolver" class="m-0">
          <button type="submit" class="w-full sm:w-auto px-3 py-1.5 bg-white border-2 border-fp-success/30 text-fp-success text-[12px] font-bold rounded-lg hover:bg-fp-success hover:border-fp-success hover:text-white transition-all shadow-sm flex items-center justify-center gap-1.5" onclick="return confirm('¿Marcar como resuelta?')">
            <i data-lucide="check" class="w-4 h-4"></i> Resolver
          </button>
        </form>
      </div>
    </div>
    <?php endforeach; ?>
    </div>
  <?php endif; ?>
</div>

<script>
// Sistema de Tabs Ligera JS
document.addEventListener('DOMContentLoaded', () => {
    const tabs = document.querySelectorAll('.tab-btn');
    const panels = document.querySelectorAll('.tab-panel');

    tabs.forEach(tab => {
        tab.addEventListener('click', () => {
            // Remove active classes
            tabs.forEach(t => {
                t.classList.remove('border-fp-primary', 'text-fp-primary', 'font-bold');
                t.classList.add('border-transparent', 'text-fp-muted', 'font-semibold');
            });
            panels.forEach(p => {
                p.classList.remove('block');
                p.classList.add('hidden');
            });

            // Add active classes
            tab.classList.remove('border-transparent', 'text-fp-muted', 'font-semibold');
            tab.classList.add('border-fp-primary', 'text-fp-primary', 'font-bold');
            
            const targetId = tab.getAttribute('data-target');
            document.getElementById(targetId).classList.remove('hidden');
            document.getElementById(targetId).classList.add('block');
        });
    });
});
</script>

<?php
$contenido = ob_get_clean();
require __DIR__ . '/../layouts/base.php';
?>
