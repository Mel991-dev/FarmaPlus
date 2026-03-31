<?php
$titulo = 'Comprobante de Venta';
// Layout mínimo para el comprobante
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comprobante <?= htmlspecialchars($venta['numero_comprobante']) ?></title>
    <link rel="stylesheet" href="<?= rtrim($_ENV['APP_BASEPATH'] ?? '', '/') ?>/assets/css/app.min.css?v=<?= time() ?>">
    <style>
        body { background: #e0e0e0; display: flex; justify-content: center; padding: 40px 20px; font-family: 'JetBrains Mono', monospace; }
        .receipt { background: #fff; width: 330px; padding: 24px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 8px; }
        .r-header { text-align: center; border-bottom: 2px dashed #ccc; padding-bottom: 15px; margin-bottom: 15px; }
        .r-header h2 { margin: 0; font-size: 20px; font-weight: 700; color: #1A3A4A; font-family: 'Inter', sans-serif; }
        .r-header p { margin: 4px 0 0; font-size: 11px; color: #666; }
        .r-info { font-size: 12px; margin-bottom: 15px; line-height: 1.5; color: #333; }
        .r-info div { display: flex; justify-content: space-between; }
        .r-table { width: 100%; font-size: 11px; margin-bottom: 15px; border-collapse: collapse; }
        .r-table th { border-bottom: 1px solid #ccc; padding: 4px 0; text-align: left; }
        .r-table td { padding: 6px 0; border-bottom: 1px dotted #eee; }
        .r-table td.qty { width: 30px; }
        .r-table td.price { text-align: right; width: 60px; }
        .r-table td.total { text-align: right; font-weight: bold; width: 60px; }
        .r-totals { font-size: 13px; font-weight: 600; text-align: right; border-top: 2px dashed #ccc; padding-top: 15px; line-height: 1.8; }
        .r-grand { font-size: 18px; font-weight: 700; }
        .r-footer { text-align: center; font-size: 11px; color: #666; margin-top: 25px; line-height: 1.4; border-top: 1px solid #eee; padding-top: 15px; }
        .controls { position: fixed; top: 10px; right: 10px; display: flex; gap: 10px; }
        @media print {
            body { background: #fff; padding: 0; align-items: flex-start; }
            .receipt { box-shadow: none; width: 100%; max-width: 80mm; padding: 0; border-radius: 0; margin: 0 auto; }
            .controls { display: none; }
        }
    </style>
</head>
<body>

<div class="controls">
    <button onclick="window.print()" class="btn-primary btn-sm"><svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg> Imprimir</button>
    <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/ventas/pos" class="btn-secondary btn-sm">Volver al POS</a>
</div>

<div class="receipt">
    <div class="r-header">
        <h2>FarmaPlus</h2>
        <p>NIT: 900.123.456-7</p>
        <p>Calle Principal #123, Ciudad</p>
    </div>
    
    <div class="r-info">
        <div><span>Comprobante:</span> <strong><?= htmlspecialchars($venta['numero_comprobante']) ?></strong></div>
        <div><span>Fecha:</span> <span><?= date('d/m/Y H:i', strtotime($venta['created_at'])) ?></span></div>
        <div><span>Vendedor:</span> <span><?= htmlspecialchars($venta['vendedor_nombre']) ?></span></div>
        <div><span>Método Pago:</span> <span style="text-transform: capitalize;"><?= htmlspecialchars($venta['metodo_pago']) ?></span></div>
        <?php if (!empty($venta['formula_medica'])): ?>
        <div style="margin-top: 5px; color: #E74C3C;"><span>Fórmula Médica:</span> <strong><?= htmlspecialchars($venta['formula_medica']) ?></strong></div>
        <?php endif; ?>
    </div>

    <table class="r-table">
        <thead>
            <tr>
                <th>CANT</th>
                <th>PRODUCTO</th>
                <th style="text-align: right;">SUBTOTAL</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($detalle as $item): ?>
            <tr>
                <td class="qty"><?= $item['cantidad'] ?></td>
                <td>
                    <?= htmlspecialchars($item['producto_nombre']) ?>
                    <div style="font-size: 9px; color: #888;">Lote: <?= $item['lote_id'] ?> | <?= '$' . number_format((float)$item['precio_unitario'], 0, ',', '.') ?> c/u</div>
                </td>
                <td class="total"><?= '$' . number_format((float)$item['subtotal'], 0, ',', '.') ?></td>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="r-totals">
        <div>TOTAL: <span class="r-grand"><?= '$' . number_format((float)$venta['total'], 0, ',', '.') ?></span></div>
    </div>

    <div class="r-footer">
        <p>¡Gracias por tu compra!</p>
        <p>Conservar este recibo para reclamos médicos.</p>
    </div>
</div>

</body>
</html>
