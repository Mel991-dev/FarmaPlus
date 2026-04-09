<?php
/**
 * views/dashboard/gerente.php
 * Dashboard gerencial — KPIs, comparativa mensual, top productos, pedidos por estado.
 * Tailwind puro — sin estilos CSS inline.
 */
$basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');

function fmtM(float $v): string { return '$' . number_format($v, 0, ',', '.'); }
function pct(float $v): string  { return ($v >= 0 ? '+' : '') . number_format($v, 1) . '%'; }

// Colores de estado de pedidos
$estadoColor = [
    'pendiente'       => 'bg-amber-100 text-amber-700',
    'pagado'          => 'bg-blue-100 text-blue-700',
    'en_preparacion'  => 'bg-indigo-100 text-indigo-700',
    'en_camino'       => 'bg-cyan-100 text-cyan-700',
    'entregado'       => 'bg-green-100 text-green-700',
    'cancelado'       => 'bg-red-100 text-red-700',
    'devuelto'        => 'bg-gray-100 text-gray-600',
];

// Datos de la mini-gráfica (últimos 7 días)
$labelsGraf  = array_column($ventas7dias ?? [], 'fecha');
$totalesGraf = array_column($ventas7dias ?? [], 'total');
$maxGraf     = max(array_merge([1], $totalesGraf));
?>

<!-- CABECERA -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
    <div>
        <h1 class="text-2xl font-bold text-fp-text tracking-tight">
            Dashboard Gerencial
        </h1>
        <p class="text-[13px] text-fp-muted mt-1">
            <?= date('l, d \d\e F \d\e Y') ?> &mdash; Mes en curso: <strong class="text-fp-text"><?= date('F Y') ?></strong>
        </p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <a href="<?= $basePath ?>/gerente/reportes/ventas"
           class="flex items-center gap-2 px-4 py-2 bg-fp-primary text-white text-[13px] font-semibold rounded-lg hover:bg-fp-primary-light transition-colors shadow-sm">
            <i data-lucide="bar-chart-2" class="w-4 h-4"></i> Reporte de Ventas
        </a>
        <a href="<?= $basePath ?>/gerente/reportes/inventario"
           class="flex items-center gap-2 px-4 py-2 bg-white border border-fp-border text-fp-text text-[13px] font-semibold rounded-lg hover:bg-fp-bg-main transition-colors">
            <i data-lucide="package" class="w-4 h-4"></i> Inventario
        </a>
    </div>
</div>

<!-- KPIs PRINCIPALES — fila 4 tarjetas -->
<div class="grid grid-cols-1 sm:grid-cols-2 xl:grid-cols-4 gap-4 mb-8">

    <!-- Ventas mes actual -->
    <div class="bg-white rounded-xl border border-fp-border p-5 flex flex-col gap-3">
        <div class="flex items-center justify-between">
            <span class="text-[12px] font-semibold uppercase tracking-wider text-fp-muted">Ventas del mes</span>
            <div class="w-9 h-9 rounded-xl bg-fp-primary/10 flex items-center justify-center">
                <i data-lucide="trending-up" class="w-5 h-5 text-fp-primary"></i>
            </div>
        </div>
        <div class="text-[26px] font-bold text-fp-text tracking-tight leading-none">
            <?= fmtM($mesActual) ?>
        </div>
        <div class="flex items-center gap-1.5 text-[12px] font-medium
            <?= $variacionPct >= 0 ? 'text-fp-success' : 'text-fp-error' ?>">
            <i data-lucide="<?= $variacionPct >= 0 ? 'arrow-up' : 'arrow-down' ?>" class="w-3 h-3"></i>
            <?= pct($variacionPct) ?> vs mes anterior (<?= fmtM($mesAnterior) ?>)
        </div>
    </div>

    <!-- # Ventas y ticket promedio -->
    <div class="bg-white rounded-xl border border-fp-border p-5 flex flex-col gap-3">
        <div class="flex items-center justify-between">
            <span class="text-[12px] font-semibold uppercase tracking-wider text-fp-muted">Ventas realizadas</span>
            <div class="w-9 h-9 rounded-xl bg-[#9B51E0]/10 flex items-center justify-center">
                <i data-lucide="receipt" class="w-5 h-5 text-[#9B51E0]"></i>
            </div>
        </div>
        <div class="text-[26px] font-bold text-fp-text tracking-tight leading-none">
            <?= number_format((int)($kpisMes['num_ventas'] ?? 0), 0, ',', '.') ?>
        </div>
        <div class="text-[12px] text-fp-muted">
            Ticket prom. <strong class="text-fp-text"><?= fmtM((float)($kpisMes['ticket_promedio'] ?? 0)) ?></strong>
        </div>
    </div>

    <!-- Pedidos online -->
    <?php
    $pedEntregados = 0; $pedCancelados = 0; $pedPendientes = 0;
    foreach (($pedidosEstado ?? []) as $row) {
        if ($row['estado'] === 'entregado')  $pedEntregados = (int)$row['num_pedidos'];
        if ($row['estado'] === 'cancelado')  $pedCancelados = (int)$row['num_pedidos'];
        if (in_array($row['estado'], ['pendiente','pagado','en_preparacion','en_camino']))
            $pedPendientes += (int)$row['num_pedidos'];
    }
    ?>
    <div class="bg-white rounded-xl border border-fp-border p-5 flex flex-col gap-3">
        <div class="flex items-center justify-between">
            <span class="text-[12px] font-semibold uppercase tracking-wider text-fp-muted">Pedidos online</span>
            <div class="w-9 h-9 rounded-xl bg-fp-secondary/10 flex items-center justify-center">
                <i data-lucide="shopping-bag" class="w-5 h-5 text-fp-secondary"></i>
            </div>
        </div>
        <div class="text-[26px] font-bold text-fp-text tracking-tight leading-none"><?= $pedEntregados ?> entregados</div>
        <div class="flex items-center gap-3 text-[12px] text-fp-muted">
            <span class="text-amber-600 font-medium"><?= $pedPendientes ?> en curso</span>
            <span class="text-fp-error font-medium"><?= $pedCancelados ?> cancelados</span>
        </div>
    </div>

    <!-- Alertas / Top vendedor -->
    <div class="bg-white rounded-xl border border-fp-border p-5 flex flex-col gap-3">
        <div class="flex items-center justify-between">
            <span class="text-[12px] font-semibold uppercase tracking-wider text-fp-muted">Alertas activas</span>
            <div class="w-9 h-9 rounded-xl bg-fp-error/10 flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-fp-error"></i>
            </div>
        </div>
        <div class="text-[26px] font-bold <?= $alertasTotal > 0 ? 'text-fp-error' : 'text-fp-success' ?> tracking-tight leading-none">
            <?= $alertasTotal ?>
        </div>
        <?php if ($alertasTotal > 0): ?>
        <a href="<?= $basePath ?>/inventario/alertas"
           class="text-[12px] text-fp-error font-semibold hover:underline flex items-center gap-1">
            <i data-lucide="arrow-right" class="w-3 h-3"></i> Ver alertas
        </a>
        <?php else: ?>
        <span class="text-[12px] text-fp-success font-medium flex items-center gap-1">
            <i data-lucide="check-circle" class="w-3 h-3"></i> Sin alertas críticas
        </span>
        <?php endif; ?>
    </div>

</div>

<!-- SEGUNDA FILA: mini-gráfica + top productos + pedidos estado -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">

    <!-- Mini-gráfica 7 días (barras CSS) -->
    <div class="lg:col-span-2 bg-white rounded-xl border border-fp-border p-5">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-[14px] font-bold text-fp-text">Ventas — últimos 7 días</h3>
            <a href="<?= $basePath ?>/gerente/reportes/ventas"
               class="text-[12px] text-fp-primary font-semibold hover:underline flex items-center gap-1">
                Ver reporte completo <i data-lucide="external-link" class="w-3 h-3"></i>
            </a>
        </div>
        <?php if (empty($ventas7dias)): ?>
            <div class="flex items-center justify-center h-32 text-fp-muted text-[13px]">
                <i data-lucide="inbox" class="w-5 h-5 mr-2"></i> Sin ventas en los últimos 7 días
            </div>
        <?php else: ?>
        <div class="flex items-end gap-2 h-36">
            <?php foreach ($ventas7dias as $dia): ?>
            <?php
                $pct = $maxGraf > 0 ? ((float)$dia['total'] / $maxGraf * 100) : 0;
                $pct = max($pct, 2);
                $label = (new DateTime($dia['fecha']))->format('d/m');
            ?>
            <div class="flex-1 flex flex-col items-center gap-1 group">
                <span class="text-[10px] text-fp-muted opacity-0 group-hover:opacity-100 transition-opacity font-mono">
                    <?= fmtM((float)$dia['total']) ?>
                </span>
                <div class="w-full bg-fp-primary/20 rounded-t-md relative overflow-hidden"
                     style="height: <?= round($pct) ?>%">
                    <div class="absolute inset-0 bg-fp-primary rounded-t-md group-hover:bg-fp-secondary transition-colors"></div>
                </div>
                <span class="text-[10px] text-fp-muted font-medium"><?= $label ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Pedidos por estado (donut textual) -->
    <div class="bg-white rounded-xl border border-fp-border p-5">
        <h3 class="text-[14px] font-bold text-fp-text mb-4">Pedidos por estado (mes)</h3>
        <?php if (empty($pedidosEstado)): ?>
            <p class="text-[13px] text-fp-muted mt-6 text-center">Sin pedidos este mes</p>
        <?php else: ?>
        <div class="flex flex-col gap-2.5">
            <?php foreach ($pedidosEstado as $row): ?>
            <div class="flex items-center justify-between">
                <span class="inline-flex items-center gap-1.5 text-[12px] font-semibold px-2.5 py-1 rounded-full <?= $estadoColor[$row['estado']] ?? 'bg-gray-100 text-gray-600' ?>">
                    <?= ucfirst(str_replace('_', ' ', $row['estado'])) ?>
                </span>
                <span class="text-[13px] font-bold text-fp-text"><?= $row['num_pedidos'] ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

</div>

<!-- TERCERA FILA: top productos semana + top vendedor -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- Top 5 productos semana -->
    <div class="bg-white rounded-xl border border-fp-border p-5">
        <h3 class="text-[14px] font-bold text-fp-text mb-4 flex items-center gap-2">
            <i data-lucide="star" class="w-4 h-4 text-amber-400"></i> Top 5 productos (semana)
        </h3>
        <?php if (empty($topSemana)): ?>
            <p class="text-[13px] text-fp-muted text-center mt-4">Sin ventas esta semana</p>
        <?php else: ?>
        <div class="flex flex-col gap-3">
            <?php foreach ($topSemana as $i => $prod): ?>
            <div class="flex items-center gap-3">
                <span class="w-6 h-6 rounded-full bg-fp-primary/10 text-fp-primary text-[11px] font-bold flex items-center justify-center shrink-0">
                    <?= $i + 1 ?>
                </span>
                <div class="flex-1 min-w-0">
                    <p class="text-[13px] font-semibold text-fp-text truncate"><?= htmlspecialchars($prod['nombre']) ?></p>
                    <p class="text-[11px] text-fp-muted"><?= number_format((int)$prod['unidades_vendidas']) ?> unidades</p>
                </div>
                <span class="text-[13px] font-bold text-fp-text shrink-0"><?= fmtM((float)$prod['ingreso_total']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>

    <!-- Top vendedor del mes -->
    <div class="bg-white rounded-xl border border-fp-border p-5">
        <h3 class="text-[14px] font-bold text-fp-text mb-4 flex items-center gap-2">
            <i data-lucide="trophy" class="w-4 h-4 text-amber-400"></i> Rendimiento vendedores (mes)
        </h3>
        <?php if (empty($vendedores)): ?>
            <p class="text-[13px] text-fp-muted text-center mt-4">Sin datos de ventas este mes</p>
        <?php else: ?>
        <div class="flex flex-col gap-3">
            <?php foreach (array_slice($vendedores, 0, 5) as $i => $v): ?>
            <div class="flex items-center gap-3">
                <div class="w-8 h-8 rounded-full bg-fp-secondary flex items-center justify-center text-white text-[12px] font-bold shrink-0">
                    <?= strtoupper(substr($v['vendedor'], 0, 2)) ?>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-[13px] font-semibold text-fp-text truncate"><?= htmlspecialchars($v['vendedor']) ?></p>
                    <p class="text-[11px] text-fp-muted"><?= $v['num_ventas'] ?> ventas · Prom <?= fmtM((float)$v['ticket_promedio']) ?></p>
                </div>
                <span class="text-[13px] font-bold text-fp-success shrink-0"><?= fmtM((float)$v['total_vendido']) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
        <a href="<?= $basePath ?>/gerente/reportes/ventas"
           class="mt-4 text-[12px] text-fp-primary font-semibold hover:underline flex items-center gap-1 justify-end">
            Ver reporte completo <i data-lucide="arrow-right" class="w-3 h-3"></i>
        </a>
        <?php endif; ?>
    </div>

</div>
