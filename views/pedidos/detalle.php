<?php
/**
 * views/pedidos/detalle.php — Detalle de un pedido para Admin/Gerente.
 */
$titulo = "Pedido #{$pedido['pedido_id']}";
ob_start();

$estadoColors = [
    'pendiente'        => 'bg-slate-100 text-slate-500 border-slate-200',
    'pagado'           => 'bg-fp-success/10 text-fp-success border-fp-success/20',
    'en_preparacion'   => 'bg-blue-50 text-blue-600 border-blue-200',
    'en_camino'        => 'bg-fp-warning/10 text-fp-warning border-fp-warning/20',
    'entregado'        => 'bg-fp-success/10 text-fp-success border-fp-success/20',
    'devuelto_fallido' => 'bg-fp-error/10 text-fp-error border-fp-error/20',
    'cancelado'        => 'bg-slate-100 text-slate-400 border-slate-200',
];
$estadoLabels = [
    'pendiente'        => 'Pendiente pago',
    'pagado'           => 'Pagado',
    'en_preparacion'   => 'En preparación',
    'en_camino'        => 'En camino',
    'entregado'        => 'Entregado',
    'devuelto_fallido' => 'Devuelto/Fallido',
    'cancelado'        => 'Cancelado',
];
$basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
?>

<div class="max-w-4xl">

    <!-- Breadcrumb -->
    <div class="flex items-center gap-2 text-[12px] text-fp-muted mb-6">
        <a href="<?= $basePath ?>/pedidos" class="hover:text-fp-primary flex items-center gap-1 font-semibold">
            <i data-lucide="arrow-left" class="w-3.5 h-3.5"></i> Pedidos
        </a>
        <span>/</span>
        <span class="font-mono font-bold text-fp-primary">#<?= str_pad((string)$pedido['pedido_id'], 6, '0', STR_PAD_LEFT) ?></span>
    </div>

    <!-- Encabezado -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h1 class="text-2xl font-bold text-fp-text flex items-center gap-2">
                <i data-lucide="package" class="w-6 h-6 text-fp-primary"></i>
                Pedido <span class="font-mono text-fp-primary">#<?= str_pad((string)$pedido['pedido_id'], 6, '0', STR_PAD_LEFT) ?></span>
            </h1>
            <p class="text-[13px] text-fp-muted mt-0.5"><?= date('d/m/Y H:i', strtotime($pedido['created_at'])) ?></p>
        </div>
        <div class="flex items-center gap-3">
            <?php $estadoCls = $estadoColors[$pedido['estado']] ?? 'bg-slate-100 text-slate-500'; ?>
            <span class="px-3 py-1.5 rounded-full border text-[12px] font-bold <?= $estadoCls ?>">
                <?= $estadoLabels[$pedido['estado']] ?? $pedido['estado'] ?>
            </span>
            <?php if (in_array($pedido['estado'], ['pendiente', 'pagado'])): ?>
            <form method="POST" action="<?= $basePath ?>/pedidos/<?= $pedido['pedido_id'] ?>/cancelar" class="m-0">
                <button type="submit" onclick="return confirm('¿Cancelar este pedido?')"
                        class="px-3 py-1.5 bg-fp-error/10 text-fp-error border border-fp-error/30 hover:bg-fp-error hover:text-white text-[12px] font-bold rounded-lg transition-colors">
                    Cancelar
                </button>
            </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">

        <!-- Items del pedido -->
        <div class="md:col-span-2 flex flex-col gap-5">
            <div class="bg-white rounded-xl border border-fp-border shadow-sm overflow-hidden">
                <div class="p-4 border-b border-fp-border bg-fp-bg-main/30">
                    <h2 class="font-bold text-[14px] text-fp-text flex items-center gap-2">
                        <i data-lucide="shopping-bag" class="w-4.5 h-4.5 text-fp-primary"></i> Productos del pedido
                    </h2>
                </div>
                <div class="divide-y divide-fp-border/50">
                    <?php foreach ($items as $item): ?>
                    <div class="p-4 flex items-center gap-4">
                        <div class="w-10 h-10 rounded-xl bg-fp-bg-main flex items-center justify-center shrink-0">
                            <i data-lucide="pill" class="w-5 h-5 text-fp-primary/40"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <div class="font-semibold text-[13px] text-fp-text"><?= htmlspecialchars($item['producto_nombre']) ?></div>
                            <div class="text-[11px] text-fp-muted">×<?= $item['cantidad'] ?> · $<?= number_format($item['precio_unitario'], 0, ',', '.') ?>/un.</div>
                        </div>
                        <span class="font-bold text-[14px] text-fp-text shrink-0">$<?= number_format($item['subtotal'], 0, ',', '.') ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="p-4 border-t border-fp-border bg-fp-bg-main/30 flex flex-col gap-1.5">
                    <div class="flex justify-between text-[13px]"><span class="text-fp-muted">Subtotal</span><span class="font-semibold">$<?= number_format($pedido['subtotal'], 0, ',', '.') ?></span></div>
                    <div class="flex justify-between text-[13px]"><span class="text-fp-muted">Domicilio</span><span class="font-semibold">$<?= number_format($pedido['costo_envio'], 0, ',', '.') ?></span></div>
                    <div class="flex justify-between pt-2 border-t border-fp-border mt-1"><span class="font-black text-[15px]">Total</span><span class="font-black text-[16px] text-fp-primary">$<?= number_format($pedido['total'], 0, ',', '.') ?></span></div>
                </div>
            </div>
        </div>

        <!-- Panel derecho: info cliente y repartidor -->
        <div class="md:col-span-1 flex flex-col gap-5">

            <!-- Cliente -->
            <div class="bg-white rounded-xl border border-fp-border shadow-sm p-4">
                <h2 class="font-bold text-[13px] text-fp-text mb-3 flex items-center gap-2"><i data-lucide="user" class="w-4 h-4 text-fp-primary"></i> Cliente</h2>
                <div class="font-semibold text-[13px]"><?= htmlspecialchars($pedido['cliente_nombre'] ?? '—') ?></div>
                <div class="text-[12px] text-fp-muted"><?= htmlspecialchars($pedido['cliente_correo'] ?? '') ?></div>
                <?php if (!empty($pedido['direccion'])): ?>
                <div class="mt-3 flex items-start gap-1.5">
                    <i data-lucide="map-pin" class="w-3.5 h-3.5 text-fp-error mt-0.5 shrink-0"></i>
                    <div class="text-[12px] text-fp-text leading-snug"><?= htmlspecialchars($pedido['direccion'] ?? '') ?><?= !empty($pedido['barrio']) ? ', ' . htmlspecialchars($pedido['barrio']) : '' ?><?= !empty($pedido['ciudad']) ? ', ' . htmlspecialchars($pedido['ciudad']) : '' ?></div>
                </div>
                <?php endif; ?>
            </div>

            <!-- Repartidor / Asignación -->
            <div class="bg-white rounded-xl border border-fp-border shadow-sm p-4">
                <h2 class="font-bold text-[13px] text-fp-text mb-3 flex items-center gap-2"><i data-lucide="truck" class="w-4 h-4 text-fp-primary"></i> Repartidor</h2>
                <?php if (!empty($pedido['repartidor_id'])): ?>
                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 rounded-full bg-fp-secondary flex items-center justify-center text-white text-xs font-bold">
                        <?= strtoupper(substr($pedido['repartidor_nombre'] ?? 'R', 0, 1)) ?>
                    </div>
                    <div class="font-semibold text-[13px]"><?= htmlspecialchars($pedido['repartidor_nombre'] ?? '—') ?></div>
                </div>
                <?php elseif (in_array($pedido['estado'], ['pagado', 'pendiente'])): ?>
                <p class="text-[12px] text-fp-muted mb-3">Sin repartidor asignado.</p>
                <select id="repartidorSelect" class="w-full h-9 px-3 border border-fp-border rounded-lg text-[13px] outline-none focus:border-fp-primary mb-2">
                    <option value="">— Seleccionar —</option>
                    <?php foreach ($repartidores as $r): ?>
                    <option value="<?= $r['usuario_id'] ?>"><?= htmlspecialchars($r['nombre']) ?></option>
                    <?php endforeach; ?>
                </select>
                <button onclick="asignar(<?= $pedido['pedido_id'] ?>)"
                        class="w-full h-9 bg-fp-primary text-white text-[12px] font-bold rounded-lg hover:bg-fp-primary-dark transition-colors flex items-center justify-center gap-1.5">
                    <i data-lucide="user-plus" class="w-3.5 h-3.5"></i> Asignar repartidor
                </button>
                <?php else: ?>
                <p class="text-[12px] text-fp-muted italic">No aplica en este estado.</p>
                <?php endif; ?>
            </div>

            <!-- Datos MP -->
            <?php if (!empty($pedido['mp_payment_id'])): ?>
            <div class="bg-white rounded-xl border border-fp-border shadow-sm p-4">
                <h2 class="font-bold text-[13px] text-fp-text mb-3 flex items-center gap-2">
                    <span class="text-[10px] px-2 py-0.5 bg-[#009EE3] text-white rounded font-bold">MP</span> MercadoPago
                </h2>
                <div class="flex flex-col gap-1 text-[12px]">
                    <div class="flex justify-between"><span class="text-fp-muted">Payment ID</span><span class="font-mono font-semibold"><?= htmlspecialchars($pedido['mp_payment_id']) ?></span></div>
                    <div class="flex justify-between"><span class="text-fp-muted">Estado MP</span><span class="font-semibold capitalize"><?= htmlspecialchars($pedido['mp_status'] ?? '—') ?></span></div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
const BP = '<?= $basePath ?>';
async function asignar(pedidoId) {
    const r = document.getElementById('repartidorSelect').value;
    if (!r) { alert('Selecciona un repartidor.'); return; }
    const res = await fetch(`${BP}/pedidos/${pedidoId}/asignar-repartidor`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `repartidor_id=${r}`
    });
    const data = await res.json();
    if (data.success) window.location.reload();
    else alert(data.error || 'Error al asignar.');
}
</script>

<?php
$contenido = ob_get_clean();
require __DIR__ . '/../layouts/base.php';
?>
