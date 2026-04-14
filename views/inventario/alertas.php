<?php
// views/inventario/alertas.php
$titulo = 'Alertas de Inventario';
ob_start();

$meses = ['Ene','Feb','Mar','Abr','May','Jun','Jul','Ago','Sep','Oct','Nov','Dic'];
$fechaActualStr = date('d') . ' ' . strtolower($meses[date('n') - 1]) . '. ' . date('Y - H:i') . ' h';
?>

<?php if (!empty($_GET['success'])): ?>
<div class="mb-6 bg-fp-success/10 border-l-4 border-fp-success p-3 rounded-lg flex items-center gap-2 text-sm font-semibold text-fp-success shadow-sm">
  <i data-lucide="circle-check" class="w-5 h-5"></i> <?= htmlspecialchars($_GET['success']) ?>
</div>
<?php elseif (!empty($_GET['error'])): ?>
<div class="mb-6 bg-[#FDEDEC] border-l-4 border-[#E74C3C] p-3 rounded-lg flex items-center gap-2 text-sm font-semibold text-[#E74C3C] shadow-sm">
  <i data-lucide="alert-circle" class="w-5 h-5"></i> <?= htmlspecialchars($_GET['error']) ?>
</div>
<?php endif; ?>

<!-- Encabezado como en el Mockup -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div>
    <h1 class="text-2xl font-bold text-fp-text tracking-tight">Panel de alertas de inventario</h1>
    <p class="text-[13px] text-fp-muted mt-1">Revisión de productos con stock crítico y lotes próximos a vencer. Actualizado: <?= $fechaActualStr ?></p>
  </div>
  <button class="bg-white border border-fp-border/70 text-fp-text hover:bg-fp-bg-main font-semibold text-[13px] px-4 py-2 rounded-lg shadow-sm flex items-center gap-2 transition-colors">
    <i data-lucide="download" class="w-4 h-4"></i> Exportar reporte
  </button>
</div>

<!-- Tarjetas Resumen Superiores -->
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
  <!-- Alertas Totales -->
  <div class="bg-white rounded-xl border border-fp-border/70 p-5 flex items-center gap-4 shadow-sm relative overflow-hidden">
    <div class="w-12 h-12 rounded-xl bg-[#FDEDEC] text-[#E74C3C] flex items-center justify-center shrink-0">
        <i data-lucide="alert-triangle" class="w-6 h-6"></i>
    </div>
    <div>
        <div class="text-[26px] font-black text-[#E74C3C] font-mono tracking-tight leading-none mb-1"><?= $totalAlertas ?? 0 ?></div>
        <div class="text-[12px] font-medium text-fp-muted">Alertas activas en total</div>
    </div>
  </div>

  <!-- Productos bajo stock -->
  <div class="bg-white rounded-xl border border-fp-border/70 p-5 flex items-center gap-4 shadow-sm relative overflow-hidden">
    <div class="w-12 h-12 rounded-xl bg-[#F39C12]/10 text-[#F39C12] flex items-center justify-center shrink-0">
        <i data-lucide="package-minus" class="w-6 h-6"></i>
    </div>
    <div>
        <div class="text-[26px] font-black text-[#F39C12] font-mono tracking-tight leading-none mb-1"><?= count($alertasStock ?? []) ?></div>
        <div class="text-[12px] font-medium text-fp-muted">Productos bajo stock mínimo</div>
    </div>
  </div>

  <!-- Lotes próximos a vencer -->
  <div class="bg-white rounded-xl border border-fp-border/70 p-5 flex items-center gap-4 shadow-sm relative overflow-hidden">
    <div class="w-12 h-12 rounded-xl bg-[#E67E22]/10 text-[#E67E22] flex items-center justify-center shrink-0">
        <i data-lucide="clock" class="w-6 h-6"></i>
    </div>
    <div>
        <div class="text-[26px] font-black text-[#E67E22] font-mono tracking-tight leading-none mb-1"><?= count($alertasVencimiento ?? []) ?></div>
        <div class="text-[12px] font-medium text-fp-muted">Lotes con vencimiento < 30 días</div>
    </div>
  </div>
</div>

<!-- ==============================================
     SECCIÓN: STOCK MÍNIMO
     ============================================== -->
<div class="mb-4 flex flex-col sm:flex-row sm:items-center justify-between border-b border-fp-border/50 pb-3 gap-3">
  <div class="flex items-center gap-3">
      <span class="bg-[#F39C12]/10 text-[#F39C12] border border-[#F39C12]/20 font-bold text-[11px] px-2.5 py-1 rounded-full flex items-center gap-1.5"><i data-lucide="alert-triangle" class="w-3.5 h-3.5"></i> Stock mínimo</span>
      <div>
          <h2 class="text-[16px] font-bold text-fp-text leading-tight">Productos bajo stock mínimo</h2>
          <p class="text-[11.5px] text-fp-muted mt-0.5">Ordenados del más crítico al menos crítico</p>
      </div>
  </div>
  <span class="text-fp-muted text-[12px] font-semibold border border-fp-border/70 rounded-full px-3 py-1 bg-white shadow-sm"><?= count($alertasStock ?? []) ?> alertas</span>
</div>

<div class="flex flex-col gap-4 mb-8">
  <?php if (empty($alertasStock)): ?>
    <div class="flex flex-col items-center justify-center py-10 bg-white rounded-xl border border-fp-border border-dashed">
      <div class="w-14 h-14 rounded-full bg-fp-success/10 flex items-center justify-center mb-3">
         <i data-lucide="check-circle" class="w-7 h-7 text-fp-success opacity-80"></i>
      </div>
      <p class="text-[15px] font-bold text-fp-text mb-0.5">Sin alertas de stock</p>
      <span class="text-[12px] text-fp-muted">El nivel de los productos es óptimo.</span>
    </div>
  <?php else: ?>
    <?php foreach ($alertasStock as $a): 
      $stockActual = (int)($a['stock_actual'] ?? 0);
      $stockMinimo = (int)($a['stock_minimo'] ?? 0);
      // Calcular porcentaje respecto al mínimo para la barra de progreso
      // Si el actual es 0, es 0%. Si el minimo es 0 (no debería pero se protege), es 100%.
      $porcentaje = ($stockMinimo > 0) ? min(100, round(($stockActual / $stockMinimo) * 100)) : 100;
      $barraColor = ($porcentaje <= 25) ? 'bg-[#E74C3C]' : 'bg-[#E67E22]'; 
    ?>
    <div class="bg-white rounded-xl border border-fp-border p-4 shadow-sm relative overflow-hidden group flex flex-col md:flex-row gap-4">
      <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#E74C3C]"></div>
      
      <!-- Icono Izquierdo -->
      <div class="w-12 h-12 rounded-xl bg-[#FDEDEC] text-[#E74C3C] flex items-center justify-center shrink-0 self-start md:self-center">
        <i data-lucide="alert-triangle" class="w-6 h-6"></i>
      </div>
      
      <!-- Contenido Principal -->
      <div class="flex-1 min-w-0 flex flex-col justify-center">
        <div class="font-bold text-[15px] text-fp-text mb-0.5"><?= htmlspecialchars($a['producto_nombre'] ?? '—') ?></div>
        
        <div class="text-[12px] text-fp-muted mt-1 flex flex-wrap gap-2 items-center">
            <span><?= htmlspecialchars($a['laboratorio'] ?? 'Sin Laboratorio') ?></span>
            <span class="text-fp-border/80">•</span>
            <span><?= htmlspecialchars($a['categoria_nombre'] ?? 'Sin Categoría') ?></span>
            <span class="text-fp-border/80">•</span>
            <span>Venta libre</span>
        </div>
        
        <?php if(!empty($a['numero_lote'])): ?>
        <div class="text-[12px] text-fp-muted mt-1">
            Lote activo: <span class="font-semibold text-fp-text/80"><?= htmlspecialchars($a['numero_lote']) ?></span>
        </div>
        <?php endif; ?>

        <!-- Progress Bar (Cantidades) -->
        <div class="mt-4 flex items-center gap-4">
            <div class="text-[12px] font-semibold text-fp-text whitespace-nowrap"><span class="font-bold text-[13px]"><?= $stockActual ?></span> / <?= $stockMinimo ?> unidades</div>
            <div class="flex-1 h-1.5 bg-gray-100 rounded-full overflow-hidden">
                <div class="h-full <?= $barraColor ?> rounded-full transition-all duration-500" style="width: <?= $porcentaje ?>%"></div>
            </div>
            <div class="text-[12px] font-bold text-[#E74C3C] whitespace-nowrap"><?= $porcentaje ?>%</div>
        </div>
      </div>
      
      <!-- Botones Acción -->
      <div class="mt-4 md:mt-0 flex flex-row md:flex-col gap-2 shrink-0 md:self-center md:pl-4 md:border-l md:border-fp-border/50">
          <a href="<?= $basePath ?>/inventario/lotes/registrar?producto_id=<?= (int)$a['producto_id'] ?>" class="flex-1 md:flex-none px-4 py-2 bg-fp-primary hover:bg-fp-primary-dark text-white text-[12.5px] font-bold rounded-lg transition-colors flex items-center justify-center gap-2 shadow-sm">
            <i data-lucide="plus" class="w-4 h-4"></i> Registrar lote
          </a>
          <form method="POST" action="<?= $basePath ?>/inventario/alertas/<?= (int)$a['alerta_id'] ?>/resolver" class="m-0 flex-1 md:flex-none flex">
            <button type="submit" class="w-full px-4 py-2 bg-white border border-fp-border hover:bg-fp-bg-main text-fp-text text-[12.5px] font-semibold rounded-lg transition-colors flex items-center justify-center gap-2" onclick="return confirm('¿Confirma marcar como resuelta?')">
              <i data-lucide="check-circle" class="w-4 h-4 text-fp-muted"></i> Marcar resuelta
            </button>
          </form>
      </div>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<!-- ==============================================
     SECCIÓN: VENCIMIENTO
     ============================================== -->
<div class="mb-4 mt-6 flex flex-col sm:flex-row sm:items-center justify-between border-b border-fp-border/50 pb-3 gap-3">
  <div class="flex items-center gap-3">
      <span class="bg-[#E67E22]/10 text-[#E67E22] border border-[#E67E22]/20 font-bold text-[11px] px-2.5 py-1 rounded-full flex items-center gap-1.5"><i data-lucide="clock" class="w-3.5 h-3.5"></i> Vencimiento</span>
      <div>
          <h2 class="text-[16px] font-bold text-fp-text leading-tight">Lotes próximos a vencer</h2>
          <p class="text-[11.5px] text-fp-muted mt-0.5">Ordenados por proximidad de caducidad</p>
      </div>
  </div>
  <span class="text-fp-muted text-[12px] font-semibold border border-fp-border/70 rounded-full px-3 py-1 bg-white shadow-sm"><?= count($alertasVencimiento ?? []) ?> alertas</span>
</div>

<div class="flex flex-col gap-4 mb-4">
  <?php if (empty($alertasVencimiento)): ?>
    <div class="flex flex-col items-center justify-center py-10 bg-white rounded-xl border border-fp-border border-dashed">
      <div class="w-14 h-14 rounded-full bg-fp-success/10 flex items-center justify-center mb-3">
         <i data-lucide="check-circle" class="w-7 h-7 text-fp-success opacity-80"></i>
      </div>
      <p class="text-[15px] font-bold text-fp-text mb-0.5">Sin alertas de vencimiento</p>
      <span class="text-[12px] text-fp-muted">Los lotes están en condiciones vigentes.</span>
    </div>
  <?php else: ?>
    <?php foreach ($alertasVencimiento as $a): 
      try {
        $diasRestantes = (!empty($a['fecha_vencimiento']) && $a['fecha_vencimiento'] !== '0000-00-00') 
            ? (int)(new \DateTime())->diff(new \DateTime($a['fecha_vencimiento']))->days 
            : 0;
      } catch (\Exception $e) {
        $diasRestantes = 0;
      }
      $caducado = $diasRestantes <= 0;
    ?>
    <div class="bg-white rounded-xl border border-fp-border p-4 shadow-sm relative overflow-hidden group flex flex-col md:flex-row gap-4">
      <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#E67E22]"></div>
      
      <!-- Icono Izquierdo -->
      <div class="w-12 h-12 rounded-xl bg-[#E67E22]/10 text-[#E67E22] flex items-center justify-center shrink-0 self-start md:self-center">
        <i data-lucide="calendar-x" class="w-6 h-6"></i>
      </div>
      
      <!-- Contenido Principal -->
      <div class="flex-1 min-w-0 flex flex-col justify-center">
        <div class="font-bold text-[15px] text-fp-text mb-0.5"><?= htmlspecialchars($a['producto_nombre'] ?? '—') ?></div>
        
        <div class="text-[12px] text-fp-muted mt-1 flex flex-wrap gap-2 items-center">
            <span><?= htmlspecialchars($a['laboratorio'] ?? 'Sin Laboratorio') ?></span>
            <span class="text-fp-border/80">•</span>
            <span><?= htmlspecialchars($a['categoria_nombre'] ?? 'Sin Categoría') ?></span>
        </div>
        
        <div class="text-[12px] text-fp-text mt-2 font-medium">
            Lote: <span class="font-mono bg-fp-bg-main px-1.5 py-0.5 rounded border border-fp-border/50"><?= htmlspecialchars($a['numero_lote'] ?? 'N/A') ?></span> 
            <span class="mx-2 text-fp-border">|</span>
            Cantidad afectada: <span class="font-bold"><?= (int)($a['cantidad_actual'] ?? 0) ?> unidades</span>
        </div>

        <div class="mt-3 flex items-center gap-2">
            <?php if ($caducado): ?>
                <span class="bg-[#E74C3C]/10 text-[#E74C3C] border border-[#E74C3C]/20 text-[11px] font-bold px-2.5 py-1 rounded-md flex items-center gap-1.5 shadow-sm"><i data-lucide="alert-circle" class="w-3.5 h-3.5"></i> Lote caducado</span>
            <?php else: ?>
                <span class="bg-[#E67E22]/10 text-[#E67E22] border border-[#E67E22]/20 text-[11px] font-bold px-2.5 py-1 rounded-md flex items-center gap-1.5 shadow-sm"><i data-lucide="calendar" class="w-3.5 h-3.5"></i> Vence en <?= $diasRestantes ?> días</span>
            <?php endif; ?>
            <span class="text-[11px] text-fp-muted ml-1">Fecha oficial: <?= !empty($a['fecha_vencimiento']) && $a['fecha_vencimiento'] !== '0000-00-00' ? date('d/m/Y', strtotime($a['fecha_vencimiento'])) : 'No definida' ?></span>
        </div>
      </div>
      
      <!-- Botones Acción -->
      <div class="mt-4 md:mt-0 flex flex-row md:flex-col gap-2 shrink-0 md:self-center md:pl-4 md:border-l md:border-fp-border/50">
          <form method="POST" action="<?= $basePath ?>/inventario/alertas/<?= (int)$a['alerta_id'] ?>/resolver" class="m-0 flex-1 md:flex-none flex">
            <button type="submit" class="w-full h-[40px] px-4 bg-white border border-fp-border hover:bg-fp-bg-main hover:border-fp-primary hover:text-fp-primary transition-all text-fp-text text-[12.5px] font-semibold rounded-lg flex items-center justify-center gap-2" onclick="return confirm('¿Confirma marcar la alerta de vencimiento como resuelta?')">
              <i data-lucide="check-circle" class="w-4 h-4"></i> Resolver
            </button>
          </form>
      </div>
    </div>
    <?php endforeach; ?>
  <?php endif; ?>
</div>

<script>
// Inicializa íconos de la vista particular
document.addEventListener('DOMContentLoaded', () => {
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});
</script>

<?php
$contenido = ob_get_clean();
require __DIR__ . '/../layouts/base.php';
?>
