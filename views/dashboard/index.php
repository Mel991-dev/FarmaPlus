<?php
/**
 * views/dashboard/index.php
 * Refactorizado para usar `views/layouts/base.php` 
 * e implementa Tailwind puro para el layout (100% Responsive)
 */
$titulo = 'Dashboard';

$usuario    = $usuario    ?? ['nombres' => 'Usuario', 'apellidos' => '', 'rol_nombre' => 'usuario'];
$alertas    = $alertas    ?? [];
$ventas_hoy = $ventas_hoy ?? [];
$kpis       = $kpis       ?? ['ventas_dia' => 0, 'pedidos_pendientes' => 0, 'alertas_total' => 0, 'clientes_total' => 0];

$iniciales = strtoupper(mb_substr($usuario['nombres'], 0, 1) . mb_substr($usuario['apellidos'], 0, 1));
$nombre    = htmlspecialchars($usuario['nombres']);

function fmtCOP(float $v): string {
    return '$' . number_format($v, 0, ',', '.');
}

ob_start(); 
?>

<!-- SALUDO DINÁMICO -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
  <div class="flex items-center gap-4">
    <div class="w-14 h-14 rounded-full bg-fp-primary flex items-center justify-center text-white text-xl font-bold shadow-sm shrink-0">
      <?= htmlspecialchars($iniciales) ?>
    </div>
    <div class="flex flex-col">
      <h1 class="text-2xl font-bold text-fp-text tracking-tight" id="greeting">Bienvenido, <?= $nombre ?></h1>
      <p class="text-[13px] text-fp-muted mt-0.5" id="greetingDate">Cargando fecha…</p>
    </div>
  </div>
  <!-- Oculto en móbiles pequeños, visible en tablet/PC -->
  <div class="hidden sm:flex flex-col items-end">
    <div class="text-2xl font-bold text-fp-text font-mono tracking-tight" id="greetingTime">00:00</div>
    <div class="text-[11px] text-fp-muted font-medium uppercase tracking-wider" id="greetingDateSub">—</div>
  </div>
</div>

<!-- ACCESOS RÁPIDOS -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
  <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/ventas/pos" class="bg-white rounded-xl border border-fp-border p-4 flex items-center gap-4 hover:border-fp-primary/50 hover:shadow-md transition-all group">
    <div class="w-12 h-12 rounded-xl bg-[#2D9CDB]/10 text-[#2D9CDB] flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform"><i data-lucide="scan-line" class="w-6 h-6"></i></div>
    <div class="min-w-0">
      <div class="text-[15px] font-bold text-fp-text leading-tight group-hover:text-[#2D9CDB]">Nueva venta</div>
      <div class="text-[11px] text-fp-muted mt-0.5 whitespace-nowrap overflow-hidden text-ellipsis">Punto de venta POS</div>
    </div>
  </a>
  <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/lotes/registrar" class="bg-white rounded-xl border border-fp-border p-4 flex items-center gap-4 hover:border-fp-primary/50 hover:shadow-md transition-all group">
    <div class="w-12 h-12 rounded-xl bg-[#9B51E0]/10 text-[#9B51E0] flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform"><i data-lucide="layers" class="w-6 h-6"></i></div>
    <div class="min-w-0">
      <div class="text-[15px] font-bold text-fp-text leading-tight group-hover:text-[#9B51E0]">Registrar lote</div>
      <div class="text-[11px] text-fp-muted mt-0.5 whitespace-nowrap overflow-hidden text-ellipsis">Entrada de mercancía</div>
    </div>
  </a>
  <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/productos/crear" class="bg-white rounded-xl border border-fp-border p-4 flex items-center gap-4 hover:border-fp-primary/50 hover:shadow-md transition-all group">
    <div class="w-12 h-12 rounded-xl bg-[#F2994A]/10 text-[#F2994A] flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform"><i data-lucide="package-plus" class="w-6 h-6"></i></div>
    <div class="min-w-0">
      <div class="text-[15px] font-bold text-fp-text leading-tight group-hover:text-[#F2994A]">Nuevo producto</div>
      <div class="text-[11px] text-fp-muted mt-0.5 whitespace-nowrap overflow-hidden text-ellipsis">Agregar al catálogo</div>
    </div>
  </a>
  <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/admin/usuarios" class="bg-white rounded-xl border border-fp-border p-4 flex items-center gap-4 hover:border-fp-primary/50 hover:shadow-md transition-all group">
    <div class="w-12 h-12 rounded-xl bg-[#27AE60]/10 text-[#27AE60] flex items-center justify-center shrink-0 group-hover:scale-110 transition-transform"><i data-lucide="user-plus" class="w-6 h-6"></i></div>
    <div class="min-w-0">
      <div class="text-[15px] font-bold text-fp-text leading-tight group-hover:text-[#27AE60]">Nuevo usuario</div>
      <div class="text-[11px] text-fp-muted mt-0.5 whitespace-nowrap overflow-hidden text-ellipsis">Gestionar personal</div>
    </div>
  </a>
</div>

<!-- KPI CARDS -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
  <div class="bg-white rounded-xl border border-fp-border p-5 flex flex-col relative overflow-hidden group shadow-sm hover:border-[#27AE60]/50 transition-colors">
    <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#27AE60]"></div>
    <div class="flex items-center justify-between mb-2">
      <div class="w-10 h-10 rounded-lg bg-[#27AE60]/10 text-[#27AE60] flex items-center justify-center"><i data-lucide="receipt" class="w-5 h-5"></i></div>
      <i data-lucide="arrow-right" class="w-4 h-4 text-fp-muted opacity-0 group-hover:opacity-100 transition-opacity"></i>
    </div>
    <div class="text-3xl font-black text-fp-text tracking-tight font-mono <?= ((float)($kpis['ventas_dia'] ?? 0)) === 0.0 ? 'text-fp-muted' : '' ?>">
      <?= fmtCOP((float) ($kpis['ventas_dia'] ?? 0)) ?>
    </div>
    <div class="text-[13px] text-fp-muted font-medium mb-4">Ventas del día</div>
    <div class="pt-3 border-t border-fp-border border-dashed flex items-center gap-1.5 text-[11px] font-bold">
      <i data-lucide="trending-up" class="w-3.5 h-3.5 text-[#27AE60]"></i>
      <span class="text-[#27AE60]"><?= count($ventas_hoy) ?> transacciones</span>
      <span class="text-fp-muted">hoy</span>
    </div>
  </div>

  <div class="bg-white rounded-xl border border-fp-border p-5 flex flex-col relative overflow-hidden group shadow-sm hover:border-[#2D9CDB]/50 transition-colors">
    <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#2D9CDB]"></div>
    <div class="flex items-center justify-between mb-2">
      <div class="w-10 h-10 rounded-lg bg-[#2D9CDB]/10 text-[#2D9CDB] flex items-center justify-center"><i data-lucide="shopping-bag" class="w-5 h-5"></i></div>
      <i data-lucide="arrow-right" class="w-4 h-4 text-fp-muted opacity-0 group-hover:opacity-100 transition-opacity"></i>
    </div>
    <div class="text-3xl font-black text-fp-text tracking-tight font-mono <?= ((int)($kpis['pedidos_pendientes'] ?? 0)) === 0 ? 'text-fp-muted' : '' ?>">
      <?= (int) ($kpis['pedidos_pendientes'] ?? 0) ?>
    </div>
    <div class="text-[13px] text-fp-muted font-medium mb-4">Pedidos pendientes</div>
    <div class="pt-3 border-t border-fp-border border-dashed flex items-center gap-1.5 text-[11px] font-bold">
      <i data-lucide="truck" class="w-3.5 h-3.5 text-[#2D9CDB]"></i>
      <span class="text-[#2D9CDB]">En proceso</span>
      <span class="text-fp-muted">de atención</span>
    </div>
  </div>

  <div class="bg-white rounded-xl border border-fp-border p-5 flex flex-col relative overflow-hidden group shadow-sm hover:border-[#F2C94C]/50 transition-colors">
    <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#F2C94C]"></div>
    <div class="flex items-center justify-between mb-2">
      <div class="w-10 h-10 rounded-lg bg-[#F2C94C]/10 text-[#E2B93B] flex items-center justify-center"><i data-lucide="alert-triangle" class="w-5 h-5"></i></div>
      <i data-lucide="arrow-right" class="w-4 h-4 text-fp-muted opacity-0 group-hover:opacity-100 transition-opacity"></i>
    </div>
    <div class="text-3xl font-black text-fp-text tracking-tight font-mono <?= ((int)($kpis['alertas_total'] ?? 0)) === 0 ? 'text-fp-muted' : 'text-[#E2B93B]' ?>">
      <?= (int) ($kpis['alertas_total'] ?? 0) ?>
    </div>
    <div class="text-[13px] text-fp-muted font-medium mb-4">Alertas de inventario</div>
    <div class="pt-3 border-t border-fp-border border-dashed flex items-center gap-1.5 text-[11px] font-bold">
      <i data-lucide="trending-up" class="w-3.5 h-3.5 text-[#E74C3C]"></i>
      <span class="text-[#E74C3C]">Requieren</span>
      <span class="text-fp-muted">atención</span>
    </div>
  </div>

  <div class="bg-white rounded-xl border border-fp-border p-5 flex flex-col relative overflow-hidden group shadow-sm hover:border-[#1E4C6B]/50 transition-colors">
    <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#1E4C6B]"></div>
    <div class="flex items-center justify-between mb-2">
      <div class="w-10 h-10 rounded-lg bg-[#1E4C6B]/10 text-[#1E4C6B] flex items-center justify-center"><i data-lucide="users" class="w-5 h-5"></i></div>
      <i data-lucide="arrow-right" class="w-4 h-4 text-fp-muted opacity-0 group-hover:opacity-100 transition-opacity"></i>
    </div>
    <div class="text-3xl font-black text-fp-text tracking-tight font-mono <?= ((int)($kpis['clientes_total'] ?? 0)) === 0 ? 'text-fp-muted' : '' ?>">
      <?= (int) ($kpis['clientes_total'] ?? 0) ?>
    </div>
    <div class="text-[13px] text-fp-muted font-medium mb-4">Clientes registrados</div>
    <div class="pt-3 border-t border-fp-border border-dashed flex items-center gap-1.5 text-[11px] font-bold">
      <i data-lucide="trending-up" class="w-3.5 h-3.5 text-[#27AE60]"></i>
      <span class="text-[#27AE60]">Total</span>
      <span class="text-fp-muted">en el sistema</span>
    </div>
  </div>
</div>

<!-- SECCIÓN INFERIOR -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6 pb-10">
  <!-- Últimas ventas -->
  <div class="bg-white rounded-xl border border-fp-border flex flex-col shadow-sm">
    <div class="p-5 border-b border-fp-border flex items-center justify-between bg-fp-bg-main/30">
      <div class="flex items-center gap-2 font-bold text-[15px] text-fp-text"><i data-lucide="receipt" class="w-5 h-5 text-fp-primary"></i> Últimas ventas del día</div>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/ventas" class="text-[13px] font-bold text-fp-primary hover:underline flex items-center gap-1">Ver todas <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i></a>
    </div>
    
    <?php if (empty($ventas_hoy)): ?>
      <div class="flex flex-col items-center justify-center p-12 text-center">
        <div class="w-16 h-16 rounded-full bg-fp-bg-main flex items-center justify-center mb-4"><i data-lucide="shopping-cart" class="text-fp-muted w-8 h-8"></i></div>
        <div class="text-fp-text font-bold">Sin ventas registradas hoy.</div>
      </div>
    <?php else: ?>
      <div class="overflow-x-auto w-full">
        <table class="w-full text-left border-collapse min-w-[500px]">
          <thead>
            <tr class="bg-white border-b border-fp-border text-[11px] uppercase tracking-[1.5px] font-bold text-fp-muted">
              <th class="px-5 py-4">Comprobante</th>
              <th class="px-5 py-4">Canal</th>
              <th class="px-5 py-4 text-right">Total</th>
              <th class="px-5 py-4">Vendedor</th>
            </tr>
          </thead>
          <tbody class="divide-y divide-fp-border/50 text-[13px] font-medium text-fp-text">
            <?php foreach ($ventas_hoy as $v): ?>
              <tr class="hover:bg-fp-bg-main/50 transition-colors">
                <td class="px-5 py-3.5 font-mono text-fp-primary font-bold"><?= htmlspecialchars($v['numero_comprobante']) ?></td>
                <td class="px-5 py-3.5">
                  <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded bg-[#27AE60]/10 text-[#27AE60] text-[11px] font-bold"><i data-lucide="store" class="w-3 h-3"></i> Presencial</span>
                </td>
                <td class="px-5 py-3.5 text-right font-bold text-[#27AE60] font-mono"><?= fmtCOP((float)$v['total']) ?></td>
                <td class="px-5 py-3.5 text-fp-muted"><?= htmlspecialchars($v['vendedor_nombre'] ?? '—') ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    <?php endif; ?>
  </div>

  <!-- Alertas de inventario -->
  <div class="bg-white rounded-xl border border-fp-border flex flex-col shadow-sm">
    <div class="p-5 border-b border-fp-border flex items-center justify-between bg-fp-bg-main/30">
      <div class="flex items-center gap-2 font-bold text-[15px] text-fp-text"><i data-lucide="alert-triangle" class="w-5 h-5 text-fp-warning"></i> Alertas de inventario</div>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/alertas" class="text-[13px] font-bold text-fp-primary hover:underline flex items-center gap-1">Ver todas <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i></a>
    </div>

    <?php if (empty($alertas)): ?>
      <div class="flex flex-col items-center justify-center p-12 text-center">
        <div class="w-16 h-16 rounded-full bg-[#27AE60]/10 flex items-center justify-center mb-4"><i data-lucide="check-circle" class="text-[#27AE60] w-8 h-8"></i></div>
        <div class="text-fp-text font-bold">Sin alertas activas.</div>
        <p class="text-[13px] text-fp-muted mt-1">¡Todo bajo control!</p>
      </div>
    <?php else: ?>
      <div class="flex flex-col divide-y divide-fp-border/50">
        <?php foreach (array_slice($alertas, 0, 4) as $alerta): ?>
          <div class="p-4 flex flex-col sm:flex-row sm:items-center justify-between gap-4 hover:bg-fp-bg-main/50 transition-colors">
            <div class="flex-1 min-w-0">
              <div class="flex items-center gap-2 mb-1.5">
                <div class="font-bold text-[14px] text-fp-text leading-tight"><?= htmlspecialchars($alerta['producto_nombre'] ?? '—') ?></div>
                <?php if ($alerta['tipo'] === 'stock_minimo'): ?>
                  <span class="shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[#F2C94C]/10 text-[#D4AC0D]"><i data-lucide="alert-triangle" class="w-3 h-3"></i> Stock mínimo</span>
                <?php else: ?>
                  <span class="shrink-0 inline-flex items-center gap-1 px-1.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-[#E74C3C]/10 text-[#E74C3C]"><i data-lucide="clock" class="w-3 h-3"></i> Vence pronto</span>
                <?php endif; ?>
              </div>
              <div class="text-[12px] text-fp-muted mb-1"><?= htmlspecialchars($alerta['mensaje'] ?? '') ?></div>
              <div class="text-[11px] font-semibold text-fp-text/60">
                <?= htmlspecialchars($alerta['tipo'] === 'stock_minimo' ? 'Stock insuficiente para operar' : 'Lote: ' . ($alerta['numero_lote'] ?? '—')) ?>
              </div>
            </div>
            <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/alertas" class="shrink-0 self-start sm:self-center px-3 py-1.5 bg-white border border-fp-border hover:border-fp-primary hover:text-fp-primary rounded-lg text-[12px] font-bold text-fp-text transition-colors flex items-center gap-1.5">
              Ver detalle <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
  /* Saludo dinámico y reloj */
  function updateCustomTime() {
    const now  = new Date();
    const h    = now.getHours();
    const dias  = ['domingo','lunes','martes','miércoles','jueves','viernes','sábado'];
    const meses = ['enero','febrero','marzo','abril','mayo','junio','julio','agosto','septiembre','octubre','noviembre','diciembre'];
    const saludo = h < 12 ? 'Buenos días' : h < 18 ? 'Buenas tardes' : 'Buenas noches';
    const emoji  = h < 12 ? '☀️' : h < 18 ? '🌤️' : '🌙';
    
    document.getElementById('greeting').textContent = `${saludo}, <?= $nombre ?> ${emoji}`;
    document.getElementById('greetingDate').textContent = `${dias[now.getDay()]}, ${now.getDate()} de ${meses[now.getMonth()]} de ${now.getFullYear()}`;
    
    const timeEl = document.getElementById('greetingTime');
    const subEl = document.getElementById('greetingDateSub');
    if(timeEl) timeEl.textContent = `${String(h).padStart(2,'0')}:${String(now.getMinutes()).padStart(2,'0')}`;
    if(subEl) subEl.textContent = `${dias[now.getDay()]}, ${now.getDate()} ${meses[now.getMonth()].slice(0,3)}. ${now.getFullYear()}`;
  }
  updateCustomTime();
  setInterval(updateCustomTime, 1000);

  /* Flash message desde sesión PHP */
  <?php if (!empty($_SESSION['flash_msg'])): ?>
    // TODO: Require global toast in base.php
  <?php unset($_SESSION['flash_msg'], $_SESSION['flash_tipo']); ?>
  <?php endif; ?>
</script>

<?php 
$contenido = ob_get_clean(); 
require __DIR__ . '/../layouts/base.php'; 
?>
