<?php
/**
 * views/tienda/pedido_confirmado.php — Pantalla post-pago.
 * Muestra el estado del pago y el resumen del pedido.
 */
$basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
$approved = in_array($status, ['approved', 'sandbox_skip']);
$rejected = $status === 'rejected';
ob_start();
?>

<div class="max-w-2xl mx-auto">

    <!-- Ícono de estado -->
    <div class="text-center mb-8">
        <?php if ($approved): ?>
        <div class="w-20 h-20 mx-auto rounded-full bg-fp-success/10 flex items-center justify-center mb-4 ring-4 ring-fp-success/20">
            <i data-lucide="check-circle" class="w-10 h-10 text-fp-success"></i>
        </div>
        <h1 class="text-3xl font-black text-slate-800 mb-2">¡Pedido confirmado!</h1>
        <p class="text-[15px] text-slate-500">Tu pago fue procesado exitosamente. Pronto recibirás un correo de confirmación.</p>
        <?php elseif ($rejected): ?>
        <div class="w-20 h-20 mx-auto rounded-full bg-fp-error/10 flex items-center justify-center mb-4 ring-4 ring-fp-error/20">
            <i data-lucide="x-circle" class="w-10 h-10 text-fp-error"></i>
        </div>
        <h1 class="text-3xl font-black text-slate-800 mb-2">Pago rechazado</h1>
        <p class="text-[15px] text-slate-500">Tu pago no pudo procesarse. Puedes intentar con otro método de pago.</p>
        <?php else: ?>
        <div class="w-20 h-20 mx-auto rounded-full bg-fp-warning/10 flex items-center justify-center mb-4 ring-4 ring-fp-warning/20">
            <i data-lucide="clock" class="w-10 h-10 text-fp-warning"></i>
        </div>
        <h1 class="text-3xl font-black text-slate-800 mb-2">Pago en proceso</h1>
        <p class="text-[15px] text-slate-500">Tu pago está siendo verificado. Te notificaremos por correo cuando sea confirmado.</p>
        <?php endif; ?>
    </div>

    <!-- Número de pedido -->
    <?php if ($pedido): ?>
    <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden mb-5">

        <!-- Header pedido -->
        <div class="flex items-center justify-between p-5 border-b border-slate-100 bg-slate-50/50">
            <div>
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Número de pedido</div>
                <div class="text-[22px] font-black text-fp-primary font-mono">#<?= str_pad((string)$pedido['pedido_id'], 6, '0', STR_PAD_LEFT) ?></div>
            </div>
            <div class="text-right">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-0.5">Estado</div>
                <?php
                $stateMap = [
                    'pendiente'       => ['Pendiente',    'bg-slate-100 text-slate-500'],
                    'pagado'          => ['Pagado ✓',     'bg-fp-success/10 text-fp-success'],
                    'en_preparacion'  => ['Preparando',   'bg-blue-50 text-blue-600'],
                    'en_camino'       => ['En camino 🚚', 'bg-fp-warning/10 text-fp-warning'],
                    'entregado'       => ['Entregado ✅', 'bg-fp-success/10 text-fp-success'],
                    'cancelado'       => ['Cancelado',    'bg-fp-error/10 text-fp-error'],
                ];
                $estadoPedido = $pedido['estado'] ?? 'pendiente';
                [$label, $cls] = $stateMap[$estadoPedido] ?? ['Desconocido', 'bg-slate-100 text-slate-500'];
                ?>
                <span class="px-3 py-1 rounded-full text-[12px] font-bold <?= $cls ?>"><?= $label ?></span>
            </div>
        </div>

        <!-- Detalle Items -->
        <div class="p-5">
            <h3 class="font-bold text-[14px] text-slate-700 mb-3">Productos</h3>
            <div class="flex flex-col gap-2.5">
                <?php foreach ($items as $item): ?>
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center shrink-0">
                            <i data-lucide="pill" class="w-4.5 h-4.5 text-fp-primary/40"></i>
                        </div>
                        <div>
                            <div class="text-[13px] font-semibold text-slate-700"><?= htmlspecialchars($item['producto_nombre']) ?></div>
                            <div class="text-[11px] text-slate-400">×<?= $item['cantidad'] ?> · $<?= number_format($item['precio_unitario'], 0, ',', '.') ?>/un.</div>
                        </div>
                    </div>
                    <span class="font-bold text-[14px] text-slate-700 shrink-0">$<?= number_format($item['precio_unitario'] * $item['cantidad'], 0, ',', '.') ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Totales -->
        <div class="px-5 pb-5 border-t border-slate-100 pt-4 flex flex-col gap-1.5">
            <div class="flex justify-between text-[13px]">
                <span class="text-slate-400">Subtotal</span>
                <span class="font-semibold">$<?= number_format($pedido['subtotal'], 0, ',', '.') ?></span>
            </div>
            <div class="flex justify-between text-[13px]">
                <span class="text-slate-400">Domicilio</span>
                <span class="font-semibold">$<?= number_format($pedido['costo_envio'], 0, ',', '.') ?></span>
            </div>
            <div class="flex justify-between pt-2 border-t border-slate-100 mt-1">
                <span class="font-black text-[15px]">Total pagado</span>
                <span class="font-black text-[17px] text-fp-primary">$<?= number_format($pedido['total'], 0, ',', '.') ?></span>
            </div>
        </div>
    </div>

    <!-- ¿Qué sigue? -->
    <?php if ($approved): ?>
    <div class="bg-fp-primary/5 border border-fp-primary/20 rounded-2xl p-5 mb-5">
        <h3 class="font-bold text-[14px] text-fp-primary mb-3 flex items-center gap-2">
            <i data-lucide="info" class="w-4 h-4"></i> ¿Qué sigue?
        </h3>
        <div class="flex flex-col gap-3">
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-full bg-fp-primary/20 text-fp-primary flex items-center justify-center text-[12px] font-bold shrink-0">1</div>
                <span class="text-[13px] text-slate-700">Recibirás un correo de confirmación con el resumen de tu pedido.</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-full bg-fp-primary/20 text-fp-primary flex items-center justify-center text-[12px] font-bold shrink-0">2</div>
                <span class="text-[13px] text-slate-700">Nuestro equipo preparará tu pedido y asignará un repartidor.</span>
            </div>
            <div class="flex items-center gap-3">
                <div class="w-7 h-7 rounded-full bg-fp-primary/20 text-fp-primary flex items-center justify-center text-[12px] font-bold shrink-0">3</div>
                <span class="text-[13px] text-slate-700">Recibirás notificaciones por correo cuando tu pedido esté en camino y al ser entregado.</span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php endif; ?>

    <!-- CTAs -->
    <div class="flex flex-col sm:flex-row gap-3">
        <a href="<?= $basePath ?>/mi-cuenta/pedidos" class="flex-1 h-11 flex items-center justify-center gap-2 bg-fp-primary text-white font-bold text-[14px] rounded-xl hover:bg-fp-primary-dark transition-colors shadow-sm">
            <i data-lucide="package" class="w-4 h-4"></i> Ver mis pedidos
        </a>
        <a href="<?= $basePath ?>/tienda" class="flex-1 h-11 flex items-center justify-center gap-2 bg-white border-2 border-slate-200 hover:border-fp-primary text-slate-700 hover:text-fp-primary font-bold text-[14px] rounded-xl transition-colors">
            <i data-lucide="store" class="w-4 h-4"></i> Seguir comprando
        </a>
        <?php if ($rejected): ?>
        <a href="<?= $basePath ?>/tienda/checkout" class="flex-1 h-11 flex items-center justify-center gap-2 bg-fp-secondary text-white font-bold text-[14px] rounded-xl hover:bg-fp-secondary/90 transition-colors shadow-sm">
            <i data-lucide="refresh-cw" class="w-4 h-4"></i> Reintentar pago
        </a>
        <?php endif; ?>
    </div>

</div>

<?php
$contenido = ob_get_clean();
require __DIR__ . '/../layouts/tienda_layout.php';
?>
