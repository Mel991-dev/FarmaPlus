<?php
/**
 * views/reportes/inventario.php
 * Reporte de inventario: stock bajo, lotes próximos a vencer, resumen por categoría.
 * Tailwind puro.
 */
$basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
function fmtCop3(float $v): string { return '$' . number_format($v, 0, ',', '.'); }
?>

<!-- CABECERA -->
<div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-7">
    <div>
        <h1 class="text-2xl font-bold text-fp-text tracking-tight">Reporte de Inventario</h1>
        <p class="text-[13px] text-fp-muted mt-1">Generado el <?= date('d/m/Y H:i') ?></p>
    </div>
    <div class="flex items-center gap-2 flex-wrap">
        <a href="<?= $basePath ?>/gerente/reportes/exportar/inventario/pdf"
           class="flex items-center gap-2 px-4 py-2 bg-red-50 text-red-600 border border-red-200 text-[13px] font-semibold rounded-lg hover:bg-red-100 transition-colors">
            <i data-lucide="file-down" class="w-4 h-4"></i> Exportar PDF
        </a>
        <a href="<?= $basePath ?>/gerente/reportes/ventas"
           class="flex items-center gap-2 px-4 py-2 bg-white border border-fp-border text-fp-text text-[13px] font-semibold rounded-lg hover:bg-fp-bg-main transition-colors">
            <i data-lucide="bar-chart-2" class="w-4 h-4"></i> Ventas
        </a>
    </div>
</div>

<!-- ALERTAS RESUMEN KPIs -->
<div class="grid grid-cols-1 sm:grid-cols-3 gap-4 mb-8">
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-red-100 flex items-center justify-center shrink-0">
            <i data-lucide="alert-triangle" class="w-5 h-5 text-red-500"></i>
        </div>
        <div>
            <p class="text-[11px] font-semibold uppercase text-red-500 tracking-wider">Stock bajo</p>
            <p class="text-[22px] font-bold text-red-700"><?= count($stockBajo) ?> productos</p>
        </div>
    </div>
    <div class="bg-amber-50 border border-amber-200 rounded-xl p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-amber-100 flex items-center justify-center shrink-0">
            <i data-lucide="calendar-x" class="w-5 h-5 text-amber-500"></i>
        </div>
        <div>
            <p class="text-[11px] font-semibold uppercase text-amber-500 tracking-wider">Vencen en 30 días</p>
            <p class="text-[22px] font-bold text-amber-700"><?= count($proxVencer) ?> lotes</p>
        </div>
    </div>
    <div class="bg-orange-50 border border-orange-200 rounded-xl p-4 flex items-center gap-4">
        <div class="w-10 h-10 rounded-xl bg-orange-100 flex items-center justify-center shrink-0">
            <i data-lucide="clock" class="w-5 h-5 text-orange-500"></i>
        </div>
        <div>
            <p class="text-[11px] font-semibold uppercase text-orange-500 tracking-wider">Vencen en 60 días</p>
            <p class="text-[22px] font-bold text-orange-700"><?= count($proxVencer60) ?> lotes</p>
        </div>
    </div>
</div>

<!-- STOCK BAJO -->
<div class="bg-white rounded-xl border border-fp-border overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-fp-border flex items-center gap-2">
        <i data-lucide="alert-triangle" class="w-4 h-4 text-red-500"></i>
        <h3 class="text-[14px] font-bold text-fp-text">Productos con stock bajo</h3>
    </div>
    <?php if (empty($stockBajo)): ?>
    <div class="flex flex-col items-center py-10 text-fp-muted">
        <i data-lucide="check-circle" class="w-10 h-10 mb-3 text-fp-success"></i>
        <p class="text-[13px]">Todos los productos tienen stock suficiente.</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
    <table class="w-full text-[13px]">
        <thead>
            <tr class="bg-fp-bg-main border-b border-fp-border">
                <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Producto</th>
                <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Categoría</th>
                <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Stock actual</th>
                <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Stock mínimo</th>
                <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Déficit</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-fp-border">
            <?php foreach ($stockBajo as $p): ?>
            <?php $deficit = (int)$p['stock_minimo'] - (int)$p['stock_actual']; ?>
            <tr class="hover:bg-red-50/30 transition-colors">
                <td class="px-5 py-3 font-semibold text-fp-text"><?= htmlspecialchars($p['nombre']) ?></td>
                <td class="px-5 py-3 text-fp-muted"><?= htmlspecialchars($p['categoria'] ?? '—') ?></td>
                <td class="px-5 py-3 text-right">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold
                        <?= (int)$p['stock_actual'] === 0 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' ?>">
                        <?= number_format((int)$p['stock_actual']) ?>
                    </span>
                </td>
                <td class="px-5 py-3 text-right text-fp-muted"><?= number_format((int)$p['stock_minimo']) ?></td>
                <td class="px-5 py-3 text-right font-bold text-red-600">-<?= number_format($deficit) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>

<!-- LOTES PRÓXIMOS A VENCER (30 días) -->
<div class="bg-white rounded-xl border border-fp-border overflow-hidden mb-6">
    <div class="px-5 py-4 border-b border-fp-border flex items-center gap-2">
        <i data-lucide="calendar-x" class="w-4 h-4 text-amber-500"></i>
        <h3 class="text-[14px] font-bold text-fp-text">Lotes próximos a vencer (≤ 30 días)</h3>
    </div>
    <?php if (empty($proxVencer)): ?>
    <div class="flex flex-col items-center py-10 text-fp-muted">
        <i data-lucide="check-circle" class="w-10 h-10 mb-3 text-fp-success"></i>
        <p class="text-[13px]">Sin lotes próximos a vencer en los próximos 30 días.</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
    <table class="w-full text-[13px]">
        <thead>
            <tr class="bg-fp-bg-main border-b border-fp-border">
                <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Producto</th>
                <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Lote</th>
                <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Unidades</th>
                <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Vence el</th>
                <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Días rest.</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-fp-border">
            <?php foreach ($proxVencer as $lote): ?>
            <?php $dias = (int)$lote['dias_restantes']; ?>
            <tr class="hover:bg-amber-50/30 transition-colors">
                <td class="px-5 py-3 font-semibold text-fp-text"><?= htmlspecialchars($lote['producto_nombre']) ?></td>
                <td class="px-5 py-3 font-mono text-fp-muted text-[12px]"><?= htmlspecialchars($lote['numero_lote']) ?></td>
                <td class="px-5 py-3 text-right text-fp-muted"><?= number_format((int)$lote['cantidad_actual']) ?></td>
                <td class="px-5 py-3 text-right text-fp-text"><?= (new DateTime($lote['fecha_vencimiento']))->format('d/m/Y') ?></td>
                <td class="px-5 py-3 text-right">
                    <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[11px] font-bold
                        <?= $dias <= 7 ? 'bg-red-100 text-red-700' : 'bg-amber-100 text-amber-700' ?>">
                        <?= $dias ?> días
                    </span>
                </td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>

<!-- RESUMEN POR CATEGORÍA -->
<div class="bg-white rounded-xl border border-fp-border overflow-hidden">
    <div class="px-5 py-4 border-b border-fp-border flex items-center gap-2">
        <i data-lucide="layers" class="w-4 h-4 text-fp-primary"></i>
        <h3 class="text-[14px] font-bold text-fp-text">Inventario por categoría</h3>
    </div>
    <?php if (empty($resumenCat)): ?>
        <p class="px-5 py-8 text-center text-[13px] text-fp-muted">Sin datos de categorías</p>
    <?php else: ?>
    <div class="overflow-x-auto">
    <table class="w-full text-[13px]">
        <thead>
            <tr class="bg-fp-bg-main border-b border-fp-border">
                <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Categoría</th>
                <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Productos</th>
                <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Unidades totales</th>
                <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Valor inventario</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-fp-border">
            <?php foreach ($resumenCat as $cat): ?>
            <tr class="hover:bg-fp-bg-main/50 transition-colors">
                <td class="px-5 py-3 font-semibold text-fp-text"><?= htmlspecialchars($cat['categoria']) ?></td>
                <td class="px-5 py-3 text-right text-fp-muted"><?= number_format((int)$cat['num_productos']) ?></td>
                <td class="px-5 py-3 text-right text-fp-muted"><?= number_format((int)$cat['stock_total']) ?></td>
                <td class="px-5 py-3 text-right font-bold text-fp-text"><?= fmtCop3((float)$cat['valor_inventario']) ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    </div>
    <?php endif; ?>
</div>
