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
    .kpi-box   { display: table-cell; width: 25%; padding: 12px 14px; background: #f8fafc; border: 1px solid #e2e8f0; }
    .kpi-label { font-size: 10px; font-weight: bold; text-transform: uppercase; color: #64748b; }
    .kpi-val   { font-size: 18px; font-weight: bold; color: #1A3A4A; margin-top: 4px; }
    table { width: 100%; border-collapse: collapse; font-size: 11px; }
    th { background: #f1f5f9; text-align: left; padding: 8px 10px; font-size: 10px; font-weight: bold; text-transform: uppercase; color: #64748b; border-bottom: 1px solid #e2e8f0; }
    td { padding: 7px 10px; border-bottom: 1px solid #f1f5f9; }
    tr:nth-child(even) td { background: #fafafa; }
    .text-right { text-align: right; }
    .footer { margin: 24px; font-size: 10px; color: #94a3b8; text-align: center; border-top: 1px solid #e2e8f0; padding-top: 12px; }
    .badge-green { background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 99px; font-size: 10px; font-weight: bold; }
</style>
</head>
<body>

<div class="header">
    <h1>FarmaPlus — Reporte de Ventas</h1>
    <p>Período: <?= htmlspecialchars($desde) ?> al <?= htmlspecialchars($hasta) ?> · Generado: <?= date('d/m/Y H:i') ?></p>
</div>

<!-- KPIs -->
<div class="section">
    <div class="kpi-grid">
        <div class="kpi-box">
            <div class="kpi-label">Total vendido</div>
            <div class="kpi-val">$<?= number_format((float)($kpis['total_ventas'] ?? 0), 0, ',', '.') ?></div>
        </div>
        <div class="kpi-box">
            <div class="kpi-label">N° de ventas</div>
            <div class="kpi-val"><?= number_format((int)($kpis['num_ventas'] ?? 0)) ?></div>
        </div>
        <div class="kpi-box">
            <div class="kpi-label">Ticket promedio</div>
            <div class="kpi-val">$<?= number_format((float)($kpis['ticket_promedio'] ?? 0), 0, ',', '.') ?></div>
        </div>
        <div class="kpi-box">
            <div class="kpi-label">Venta máxima</div>
            <div class="kpi-val">$<?= number_format((float)($kpis['venta_maxima'] ?? 0), 0, ',', '.') ?></div>
        </div>
    </div>
</div>

<!-- Ventas por día -->
<div class="section">
    <div class="section-title">Ventas por día</div>
    <table>
        <thead><tr><th>Fecha</th><th class="text-right">Ventas</th><th class="text-right">Total</th></tr></thead>
        <tbody>
        <?php foreach (($ventasPorDia ?? []) as $f): ?>
        <tr>
            <td><?= (new DateTime($f['fecha']))->format('d/m/Y') ?></td>
            <td class="text-right"><?= $f['num_ventas'] ?></td>
            <td class="text-right">$<?= number_format((float)$f['total_dia'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Top productos -->
<div class="section">
    <div class="section-title">Top 10 productos más vendidos</div>
    <table>
        <thead><tr><th>#</th><th>Producto</th><th class="text-right">Unidades</th><th class="text-right">Ingreso</th></tr></thead>
        <tbody>
        <?php foreach (($topProductos ?? []) as $i => $p): ?>
        <tr>
            <td><?= $i + 1 ?></td>
            <td><?= htmlspecialchars($p['nombre']) ?></td>
            <td class="text-right"><?= number_format((int)$p['unidades_vendidas']) ?></td>
            <td class="text-right">$<?= number_format((float)$p['ingreso_total'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<!-- Rendimiento vendedores -->
<div class="section">
    <div class="section-title">Rendimiento por vendedor</div>
    <table>
        <thead><tr><th>Vendedor</th><th class="text-right">Ventas</th><th class="text-right">Total vendido</th><th class="text-right">Ticket prom.</th></tr></thead>
        <tbody>
        <?php foreach (($porVendedor ?? []) as $v): ?>
        <tr>
            <td><?= htmlspecialchars($v['vendedor']) ?></td>
            <td class="text-right"><?= $v['num_ventas'] ?></td>
            <td class="text-right">$<?= number_format((float)$v['total_vendido'], 0, ',', '.') ?></td>
            <td class="text-right">$<?= number_format((float)$v['ticket_promedio'], 0, ',', '.') ?></td>
        </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<div class="footer">FarmaPlus Droguería · NIT 900.123.456-7 · Documento confidencial generado automáticamente</div>
</body>
</html>
