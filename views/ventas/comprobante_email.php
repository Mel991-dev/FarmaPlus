<?php
$titulo = 'Comprobante FP-' . htmlspecialchars(substr($venta['numero_comprobante'], 3));
function dCOPe($n) { return '$' . number_format((float)$n, 0, ',', '.'); }
$subtotal = array_reduce($detalle, function($c, $i) { return $c + (float)$i['subtotal']; }, 0);
$totalQty = array_reduce($detalle, function($c, $i) { return $c + (int)$i['cantidad']; }, 0);
$descuento = $subtotal - (float)$venta['total'];
$fechaDate = date('d M. Y', strtotime($venta['created_at']));
$horaCorta = date('H:i \h', strtotime($venta['created_at']));

$itemsHtml = '';
foreach ($detalle as $item) {
    $invima = htmlspecialchars($item['codigo_invima'] ?? 'S/N');
    $itemsHtml .= "
    <tr>
        <td style='padding:10px 8px;border-bottom:1px solid #f1f5f9;'>" . htmlspecialchars($item['producto_nombre'] ?? '') . "<br><small style='color:#7F8C8D;font-size:10px;'>INVIMA $invima</small></td>
        <td style='padding:10px 8px;border-bottom:1px solid #f1f5f9;text-align:center;'>" . $item['cantidad'] . "</td>
        <td style='padding:10px 8px;border-bottom:1px solid #f1f5f9;text-align:right;'>" . dCOPe($item['precio_unitario']) . "</td>
        <td style='padding:10px 8px;border-bottom:1px solid #f1f5f9;text-align:right;font-weight:600;'>" . dCOPe($item['subtotal']) . "</td>
    </tr>";
}
?>
<div style='font-family:Inter,sans-serif;max-width:600px;margin:0 auto;padding:24px;background:#f8fafc;'>
    <div style='background:linear-gradient(135deg,#1A6B8A,#0f4c65);padding:28px 24px;border-radius:12px 12px 0 0;text-align:center;'>
        <div style='width:56px;height:56px;background:rgba(255,255,255,0.2);border-radius:50%;margin:0 auto 12px;display:flex;align-items:center;justify-content:center;'>
            <span style='font-size:28px;'>🧾</span>
        </div>
        <h1 style='color:#fff;margin:0;font-size:22px;'>FarmaPlus Droguería</h1>
        <p style='color:rgba(255,255,255,0.8);margin:8px 0 0;'>Comprobante: <strong><?= htmlspecialchars($venta['numero_comprobante']) ?></strong></p>
    </div>
    <div style='background:#fff;padding:24px;border:1px solid #e2e8f0;'>
        <p>Hola,</p>
        <p>Adjunto encontrarás el detalle de tu compra realizada en nuestra sede principal el <strong><?= $fechaDate ?> a las <?= $horaCorta ?></strong>.</p>
        <h3 style='color:#1A6B8A;border-bottom:2px solid #f1f5f9;padding-bottom:8px;'>Detalles de la compra</h3>
        <table style='width:100%;border-collapse:collapse;'>
            <thead>
                <tr style='background:#f8fafc;'>
                    <th style='padding:10px 8px;text-align:left;color:#64748b;font-size:12px;text-transform:uppercase;'>Producto</th>
                    <th style='padding:10px 8px;text-align:center;color:#64748b;font-size:12px;text-transform:uppercase;'>Cant.</th>
                    <th style='padding:10px 8px;text-align:right;color:#64748b;font-size:12px;text-transform:uppercase;'>Precio unit.</th>
                    <th style='padding:10px 8px;text-align:right;color:#64748b;font-size:12px;text-transform:uppercase;'>Subtotal</th>
                </tr>
            </thead>
            <tbody><?= $itemsHtml ?></tbody>
        </table>
        <div style='border-top:2px solid #f1f5f9;margin-top:16px;padding-top:16px;'>
            <table style='width:100%;'>
                <tr><td style='padding:4px;color:#64748b;'>Subtotal:</td><td style='padding:4px;text-align:right;'><?= dCOPe($subtotal) ?></td></tr>
                <tr><td style='padding:4px;color:#64748b;'>Descuento aplicado:</td><td style='padding:4px;text-align:right;'><?= $descuento > 0 ? "− " . dCOPe($descuento) : "$0" ?></td></tr>
                <tr><td style='padding:4px;font-weight:700;font-size:16px;color:#27AE60;'>Total pagado:</td><td style='padding:4px;text-align:right;font-weight:700;font-size:16px;color:#27AE60;'><?= dCOPe($venta['total']) ?></td></tr>
            </table>
        </div>
    </div>
    <div style='background:#f8fafc;padding:16px 24px;border-radius:0 0 12px 12px;text-align:center;'>
        <p style='color:#64748b;font-size:13px;margin:0;'>Conserve este documento. Tiene 5 días hábiles para devoluciones de productos OTC.</p>
    </div>
</div>
