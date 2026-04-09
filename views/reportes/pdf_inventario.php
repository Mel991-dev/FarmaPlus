<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<style>
    * { margin:0; padding:0; box-sizing:border-box; }
    body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #1a2535; }
    .header { background: #1A3A4A; color: white; padding: 20px 24px; margin-bottom: 24px; }
    .header h1 { font-size: 20px; font-weight: bold; }
    .header p  { font-size: 11px; opacity: 0.7; margin-top: 4px; }
    .section   { margin: 0 24px 20px 24px; }
    .section-title { font-size: 13px; font-weight: bold; color: #1A3A4A; border-bottom: 2px solid #17B89A; padding-bottom: 4px; margin-bottom: 12px; }
    .kpi-grid  { display: table; width: 100%; margin-bottom: 20px; }
    .kpi-box   { display: table-cell; width: 33%; padding: 12px 14px; border: 1px solid #e2e8f0; }
    .kpi-box.red { background: #fef2f2; }
    .kpi-box.amber { background: #fffbeb; }
    .kpi-label { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #64748b; }
    .kpi-val   { font-size: 20px; font-weight: bold; color: #1A3A4A; margin-top: 4px; }
    .kpi-val.red { color: #dc2626; }
    .kpi-val.amber { color: #d97706; }
    table { width: 100%; border-collapse: collapse; font-size: 11px; }
    th { background: #f1f5f9; text-align: left; padding: 8px 10px; font-size: 10px; font-weight: bold; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0; }
    td { padding: 7px 10px; border-bottom: 1px solid #f1f5f9; }
    tr:nth-child(even) td { background: #fafafa; }
    .text-right { text-align: right; }
    .badge-r { background: #fee2e2; color: #991b1b; padding: 2px 8px; border-radius: 99px; font-size: 10px; font-weight: bold; }
    .badge-a { background: #fef3c7; color: #92400e; padding: 2px 8px; border-radius: 99px; font-size: 10px; font-weight: bold; }
    .footer { margin: 24px; font-size: 10px; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 12px; }
</style>
</head>
<body>

<div class="header">
    <h1>FarmaPlus — Reporte de Inventario</h1>
    <p>Generado: <?= date('d/m/Y H:i') ?></p>
</div>

<!-- KPIs -->
<div class="section">
    <div class="kpi-grid">
        <div class="kpi-box red">
            <div class="kpi-label">Productos stock bajo</div>
            <div class="kpi-val red"><?= count($stockBajo ?? []) ?></div>
        </div>
        <div class="kpi-box amber">
            <div class="kpi-label">Lotes vencen en 30 días</div>
            <div class="kpi-val amber"><?= count($proxVencer ?? []) ?></div>
        </div>
        <div class="kpi-box">
            <div class="kpi-label">Categorías activas</div>
            <div class="kpi-val"><?= count($resumenCat ?? []) ?></div>
        </div>
    </div>
</div>

<!-- Stock bajo -->
<?php if (!empty($stockBajo)): ?>
<div class="section">
    <div class="section-title">Productos con stock bajo</div>
    <table>
        <thead><tr><th>Producto</th><th>Categoría</th><th class="text-right">Stock actual</th><th class="text-right">Mínimo</th><th class="text-right">Déficit</th></tr></thead>
        <tbody>
        <?php foreach ($stockBajo as $p): ?>
        <tr>
            <td><?= htmlspecialchars($p['nombre']) ?></td>
            <td><?= htmlspecialchars($p['categoria'] ?? '—') ?></td>
            <td class="text-right"><span class="badge-r"><?= (int)$p['stock_actual'] ?></span></td>
            <td class="text-right"><?= (int)$p['stock_minimo'] ?></td>
            <td class="text-right" style="color:#dc2626;font-weight:bold;">-<?= (int)$p['stock_minimo'] - (int)$p['stock_actual'] ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- Lotes próximos a vencer -->
<?php if (!empty($proxVencer)): ?>
<div class="section">
    <div class="section-title">Lotes próximos a vencer (≤ 30 días)</div>
    <table>
        <thead><tr><th>Producto</th><th>Lote</th><th class="text-right">Unidades</th><th class="text-right">Vence el</th><th class="text-right">Días rest.</th></tr></thead>
        <tbody>
        <?php foreach ($proxVencer as $l): ?>
        <tr>
            <td><?= htmlspecialchars($l['producto_nombre']) ?></td>
            <td style="font-family:monospace"><?= htmlspecialchars($l['numero_lote']) ?></td>
            <td class="text-right"><?= (int)$l['cantidad_actual'] ?></td>
            <td class="text-right"><?= (new DateTime($l['fecha_vencimiento']))->format('d/m/Y') ?></td>
            <td class="text-right">
                <span class="badge-<?= (int)$l['dias_restantes'] <= 7 ? 'r' : 'a' ?>"><?= (int)$l['dias_restantes'] ?> días</span>
            </td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<!-- Resumen por categoría -->
<div class="section">
    <div class="section-title">Inventario por categoría</div>
    <table>
        <thead><tr><th>Categoría</th><th class="text-right">Productos</th><th class="text-right">Unidades</th><th class="text-right">Valor inventario</th></tr></thead>
        <tbody>
        <?php foreach (($resumenCat ?? []) as $c): ?>
        <tr>
            <td><?= htmlspecialchars($c['categoria']) ?></td>
            <td class="text-right"><?= (int)$c['num_productos'] ?></td>
            <td class="text-right"><?= number_format((int)$c['stock_total']) ?></td>
            <td class="text-right" style="font-weight:bold">$<?= number_format((float)$c['valor_inventario'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="footer">FarmaPlus Droguería · NIT 900.123.456-7 · Documento confidencial generado automáticamente</div>
</body>
</html>
