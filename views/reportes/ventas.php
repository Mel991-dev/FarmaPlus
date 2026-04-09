<?php
/**
 * views/reportes/ventas.php
 * Reporte de ventas por período — filtros, KPIs, tabla y exportar PDF.
 * Tailwind puro.
 */
$basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
function fmtCop2(float $v): string { return '$' . number_format($v, 0, ',', '.'); }

$metodoPagoLabel = [
    'efectivo'          => 'Efectivo',
    'tarjeta_debito'    => 'Tarjeta débito',
    'tarjeta_credito'   => 'Tarjeta crédito',
    'transferencia'     => 'Transferencia',
];
?>

<!-- CABECERA + FILTROS -->
<div class="flex flex-col md:flex-row md:items-start md:justify-between gap-5 mb-7">
    <div>
        <h1 class="text-2xl font-bold text-fp-text tracking-tight">Reporte de Ventas</h1>
        <p class="text-[13px] text-fp-muted mt-1">Período: <strong class="text-fp-text"><?= htmlspecialchars($desde) ?></strong> al <strong class="text-fp-text"><?= htmlspecialchars($hasta) ?></strong></p>
    </div>
    <!-- Formulario filtros -->
    <form method="GET" action="<?= $basePath ?>/gerente/reportes/ventas"
          class="flex flex-wrap items-end gap-2 bg-white border border-fp-border rounded-xl p-4 shadow-sm">
        <div class="flex flex-col gap-1">
            <label class="text-[11px] font-semibold text-fp-muted uppercase tracking-wide">Desde</label>
            <input type="date" name="desde" value="<?= htmlspecialchars($desde) ?>"
                   class="h-9 px-3 border border-fp-border rounded-lg text-[13px] text-fp-text focus:outline-none focus:border-fp-primary focus:ring-1 focus:ring-fp-primary/30">
        </div>
        <div class="flex flex-col gap-1">
            <label class="text-[11px] font-semibold text-fp-muted uppercase tracking-wide">Hasta</label>
            <input type="date" name="hasta" value="<?= htmlspecialchars($hasta) ?>"
                   class="h-9 px-3 border border-fp-border rounded-lg text-[13px] text-fp-text focus:outline-none focus:border-fp-primary focus:ring-1 focus:ring-fp-primary/30">
        </div>
        <button type="submit"
                class="h-9 px-4 bg-fp-primary text-white text-[13px] font-semibold rounded-lg hover:bg-fp-primary-light transition-colors">
            Aplicar
        </button>
        <a href="<?= $basePath ?>/gerente/reportes/exportar/ventas/pdf?desde=<?= urlencode($desde) ?>&hasta=<?= urlencode($hasta) ?>"
           class="h-9 px-4 flex items-center gap-2 bg-red-50 text-red-600 border border-red-200 text-[13px] font-semibold rounded-lg hover:bg-red-100 transition-colors">
            <i data-lucide="file-down" class="w-4 h-4"></i> PDF
        </a>
    </form>
</div>

<!-- KPIs -->
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
    <div class="bg-white rounded-xl border border-fp-border p-4">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-fp-muted mb-1">Total vendido</p>
        <p class="text-[22px] font-bold text-fp-text"><?= fmtCop2((float)($kpis['total_ventas'] ?? 0)) ?></p>
    </div>
    <div class="bg-white rounded-xl border border-fp-border p-4">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-fp-muted mb-1">N° de ventas</p>
        <p class="text-[22px] font-bold text-fp-text"><?= number_format((int)($kpis['num_ventas'] ?? 0)) ?></p>
    </div>
    <div class="bg-white rounded-xl border border-fp-border p-4">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-fp-muted mb-1">Ticket promedio</p>
        <p class="text-[22px] font-bold text-fp-text"><?= fmtCop2((float)($kpis['ticket_promedio'] ?? 0)) ?></p>
    </div>
    <div class="bg-white rounded-xl border border-fp-border p-4">
        <p class="text-[11px] font-semibold uppercase tracking-wider text-fp-muted mb-1">Venta máxima</p>
        <p class="text-[22px] font-bold text-fp-text"><?= fmtCop2((float)($kpis['venta_maxima'] ?? 0)) ?></p>
    </div>
</div>

<!-- SEGUNDA FILA: tendencia diaria + método de pago -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
    <!-- Tabla ventas por día -->
    <div class="lg:col-span-2 bg-white rounded-xl border border-fp-border overflow-hidden">
        <div class="px-5 py-4 border-b border-fp-border">
            <h3 class="text-[14px] font-bold text-fp-text">Ventas por día</h3>
        </div>
        <?php if (empty($ventasPorDia)): ?>
        <div class="flex flex-col items-center justify-center py-12 text-fp-muted">
            <i data-lucide="bar-chart-2" class="w-10 h-10 mb-3 text-fp-border"></i>
            <p class="text-[13px]">Sin ventas en el período seleccionado</p>
        </div>
        <?php else: ?>
        <div class="overflow-x-auto">
        <table class="w-full text-[13px]">
            <thead>
                <tr class="bg-fp-bg-main border-b border-fp-border">
                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Fecha</th>
                    <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Ventas</th>
                    <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Total</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-fp-border">
                <?php foreach ($ventasPorDia as $fila): ?>
                <tr class="hover:bg-fp-bg-main/50 transition-colors">
                    <td class="px-5 py-3 font-medium text-fp-text">
                        <?= (new DateTime($fila['fecha']))->format('d/m/Y') ?>
                    </td>
                    <td class="px-5 py-3 text-right text-fp-muted"><?= $fila['num_ventas'] ?></td>
                    <td class="px-5 py-3 text-right font-bold text-fp-text"><?= fmtCop2((float)$fila['total_dia']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Método de pago -->
    <div class="bg-white rounded-xl border border-fp-border p-5">
        <h3 class="text-[14px] font-bold text-fp-text mb-4">Método de pago</h3>
        <?php if (empty($porMetodoPago)): ?>
            <p class="text-[13px] text-fp-muted text-center mt-6">Sin datos</p>
        <?php else: ?>
        <div class="flex flex-col gap-3">
            <?php
            $totalMet = array_sum(array_column($porMetodoPago, 'total'));
            foreach ($porMetodoPago as $mp):
                $pctMet = $totalMet > 0 ? round((float)$mp['total'] / $totalMet * 100) : 0;
            ?>
            <div>
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[12px] font-semibold text-fp-text">
                        <?= $metodoPagoLabel[$mp['metodo_pago']] ?? $mp['metodo_pago'] ?>
                    </span>
                    <span class="text-[11px] text-fp-muted"><?= $pctMet ?>%</span>
                </div>
                <div class="w-full h-2 bg-fp-bg-main rounded-full overflow-hidden">
                    <div class="h-2 bg-fp-primary rounded-full" style="width:<?= $pctMet ?>%"></div>
                </div>
                <span class="text-[11px] text-fp-muted"><?= fmtCop2((float)$mp['total']) ?> · <?= $mp['num_ventas'] ?> ventas</span>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- TERCERA FILA: top productos + rendimiento vendedores -->
<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

    <!-- Top productos -->
    <div class="bg-white rounded-xl border border-fp-border overflow-hidden">
        <div class="px-5 py-4 border-b border-fp-border flex items-center justify-between">
            <h3 class="text-[14px] font-bold text-fp-text">Top 10 productos vendidos</h3>
            <span class="text-[11px] text-fp-muted">por unidades</span>
        </div>
        <?php if (empty($topProductos)): ?>
            <p class="px-5 py-8 text-center text-[13px] text-fp-muted">Sin datos</p>
        <?php else: ?>
        <div class="overflow-x-auto">
        <table class="w-full text-[13px]">
            <thead>
                <tr class="bg-fp-bg-main border-b border-fp-border">
                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">#</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Producto</th>
                    <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Unid.</th>
                    <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Ingreso</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-fp-border">
                <?php foreach ($topProductos as $i => $prod): ?>
                <tr class="hover:bg-fp-bg-main/50 transition-colors">
                    <td class="px-5 py-3 text-fp-muted font-mono"><?= $i + 1 ?></td>
                    <td class="px-5 py-3 font-semibold text-fp-text"><?= htmlspecialchars($prod['nombre']) ?></td>
                    <td class="px-5 py-3 text-right text-fp-muted"><?= number_format((int)$prod['unidades_vendidas']) ?></td>
                    <td class="px-5 py-3 text-right font-bold text-fp-text"><?= fmtCop2((float)$prod['ingreso_total']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>

    <!-- Rendimiento vendedores -->
    <div class="bg-white rounded-xl border border-fp-border overflow-hidden">
        <div class="px-5 py-4 border-b border-fp-border">
            <h3 class="text-[14px] font-bold text-fp-text">Rendimiento por vendedor</h3>
        </div>
        <?php if (empty($porVendedor)): ?>
            <p class="px-5 py-8 text-center text-[13px] text-fp-muted">Sin datos de vendedores</p>
        <?php else: ?>
        <div class="overflow-x-auto">
        <table class="w-full text-[13px]">
            <thead>
                <tr class="bg-fp-bg-main border-b border-fp-border">
                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Vendedor</th>
                    <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Ventas</th>
                    <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Total</th>
                    <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Prom.</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-fp-border">
                <?php foreach ($porVendedor as $v): ?>
                <tr class="hover:bg-fp-bg-main/50 transition-colors">
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-full bg-fp-secondary/20 text-fp-secondary font-bold text-[11px] flex items-center justify-center shrink-0">
                                <?= strtoupper(substr($v['vendedor'], 0, 2)) ?>
                            </div>
                            <span class="font-semibold text-fp-text"><?= htmlspecialchars($v['vendedor']) ?></span>
                        </div>
                    </td>
                    <td class="px-5 py-3 text-right text-fp-muted"><?= $v['num_ventas'] ?></td>
                    <td class="px-5 py-3 text-right font-bold text-fp-success"><?= fmtCop2((float)$v['total_vendido']) ?></td>
                    <td class="px-5 py-3 text-right text-fp-muted"><?= fmtCop2((float)$v['ticket_promedio']) ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        </div>
        <?php endif; ?>
    </div>
</div>
