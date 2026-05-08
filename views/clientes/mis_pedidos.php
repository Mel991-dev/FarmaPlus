<?php
/**
 * views/clientes/mis_pedidos.php — Historial de pedidos del cliente.
 */
$basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');

$estadoConfig = [
    'pendiente'        => ['label' => 'Pendiente pago',  'cls' => 'bg-slate-100 text-slate-500 border-slate-200',    'icon' => 'clock'],
    'pagado'           => ['label' => 'Pagado',           'cls' => 'bg-green-50 text-green-600 border-green-200',     'icon' => 'check-circle'],
    'en_preparacion'   => ['label' => 'En preparación',  'cls' => 'bg-blue-50 text-blue-600 border-blue-200',        'icon' => 'box'],
    'en_camino'        => ['label' => 'En camino 🚚',    'cls' => 'bg-amber-50 text-amber-600 border-amber-200',     'icon' => 'truck'],
    'entregado'        => ['label' => 'Entregado ✅',    'cls' => 'bg-green-50 text-green-600 border-green-200',     'icon' => 'package-check'],
    'devuelto_fallido' => ['label' => 'No entregado',    'cls' => 'bg-red-50 text-red-500 border-red-200',           'icon' => 'package-x'],
    'cancelado'        => ['label' => 'Cancelado',        'cls' => 'bg-slate-100 text-slate-400 border-slate-200',   'icon' => 'x-circle'],
];

$motivoLabel = [
    'producto_danado' => 'Producto dañado',
    'error_envio'     => 'Error de envío',
    'cambio_opinion'  => 'Cambio de opinión',
    'vencido'         => 'Producto vencido',
    'otro'            => 'Otro',
];

$estadoDevolucionConfig = [
    'pendiente' => ['cls' => 'bg-amber-50 text-amber-700 border-amber-200', 'label' => 'Pendiente'],
    'aprobada'  => ['cls' => 'bg-green-50 text-green-700 border-green-200', 'label' => 'Aprobada'],
    'rechazada' => ['cls' => 'bg-red-50 text-red-700 border-red-200', 'label' => 'Rechazada'],
];

ob_start();
?>

<div class="max-w-3xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-black text-slate-800 flex items-center gap-2">
                <i data-lucide="package" class="w-6 h-6 text-fp-secondary"></i> Mis pedidos
            </h1>
            <p class="text-[13px] text-slate-500 mt-1">Historial de compras en línea</p>
        </div>
        <a href="<?= $basePath ?>/tienda" class="flex items-center gap-2 px-4 py-2 bg-fp-secondary text-white text-[13px] font-bold rounded-xl hover:bg-fp-secondary/90 transition-colors shadow-sm">
            <i data-lucide="store" class="w-4 h-4"></i> Ir a la tienda
        </a>
    </div>

    <?php if (!empty($success)): ?>
    <div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-[13px] font-semibold">
        <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
        <?= htmlspecialchars($success) ?>
    </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
    <div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-[13px] font-semibold">
        <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
        <?= htmlspecialchars($error) ?>
    </div>
    <?php endif; ?>

    <?php if (empty($pedidos)): ?>
    <!-- Sin pedidos -->
    <div class="flex flex-col items-center justify-center py-20 bg-white rounded-2xl border border-slate-200 text-center">
        <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mb-4">
            <i data-lucide="package" class="w-10 h-10 text-slate-300"></i>
        </div>
        <h2 class="text-[17px] font-bold text-slate-700 mb-2">Aún no tienes pedidos</h2>
        <p class="text-[13px] text-slate-500 max-w-xs mb-5">Cuando realices una compra en nuestra tienda, aparecerá aquí el historial completo.</p>
        <a href="<?= $basePath ?>/tienda" class="px-5 py-2.5 bg-fp-secondary text-white text-[13px] font-bold rounded-xl hover:bg-fp-secondary/90 transition-colors shadow-sm flex items-center gap-2">
            <i data-lucide="shopping-bag" class="w-4 h-4"></i> Ver catálogo
        </a>
    </div>

    <?php else: ?>
    <!-- Lista de pedidos -->
    <div class="flex flex-col gap-4">
        <?php foreach ($pedidos as $p):
            $cfg   = $estadoConfig[$p['estado']] ?? $estadoConfig['pendiente'];
            $fecha = date('d/m/Y H:i', strtotime($p['created_at']));
            $num   = str_pad((string)$p['pedido_id'], 6, '0', STR_PAD_LEFT);
            $detallesPedido = $detallesPorPedido[(int)$p['pedido_id']] ?? [];
            $devolucionesPedido = $devolucionesPorPedido[(int)$p['pedido_id']] ?? [];
        ?>
        <div class="bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden hover:shadow-md hover:border-fp-primary/30 transition-all">

            <!-- Header tarjeta -->
            <div class="flex items-center justify-between p-4 border-b border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-fp-primary/10 flex items-center justify-center">
                        <i data-lucide="<?= $cfg['icon'] ?>" class="w-5 h-5 text-fp-primary"></i>
                    </div>
                    <div>
                        <div class="font-mono font-black text-[15px] text-fp-primary">#<?= $num ?></div>
                        <div class="text-[11px] text-slate-400"><?= $fecha ?></div>
                    </div>
                </div>
                <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border text-[11px] font-bold <?= $cfg['cls'] ?>">
                    <?= $cfg['label'] ?>
                </span>
            </div>

            <!-- Cuerpo tarjeta -->
            <div class="p-4 flex items-center justify-between gap-4 flex-wrap">
                <div class="flex flex-wrap gap-4 text-[13px]">
                    <div>
                        <span class="text-slate-400 font-medium block text-[10px] uppercase tracking-wide mb-0.5">Total</span>
                        <span class="font-black text-fp-primary text-[15px]">$<?= number_format((float)($p['total'] ?? 0), 0, ',', '.') ?></span>
                    </div>
                    <?php if (!empty($p['costo_envio'])): ?>
                    <div>
                        <span class="text-slate-400 font-medium block text-[10px] uppercase tracking-wide mb-0.5">Domicilio</span>
                        <span class="font-semibold text-slate-600">$<?= number_format((float)$p['costo_envio'], 0, ',', '.') ?></span>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($p['ciudad'])): ?>
                    <div>
                        <span class="text-slate-400 font-medium block text-[10px] uppercase tracking-wide mb-0.5">Ciudad</span>
                        <span class="font-semibold text-slate-600"><?= htmlspecialchars($p['ciudad']) ?></span>
                    </div>
                    <?php endif; ?>
                </div>

                <!-- Tracking visual -->
                <?php if (in_array($p['estado'], ['pagado','en_preparacion','en_camino','entregado'])): ?>
                <div class="flex items-center gap-1 shrink-0">
                    <?php
                    $pasos = ['pagado','en_preparacion','en_camino','entregado'];
                    $idx   = array_search($p['estado'], $pasos);
                    foreach ($pasos as $i => $paso):
                        $activo   = $i <= $idx;
                        $esActual = $i === $idx;
                    ?>
                    <div class="flex items-center gap-1">
                        <div class="w-2 h-2 rounded-full <?= $esActual ? 'bg-fp-secondary ring-2 ring-fp-secondary/30' : ($activo ? 'bg-fp-success' : 'bg-slate-200') ?>"></div>
                        <?php if ($i < count($pasos)-1): ?>
                        <div class="w-4 h-0.5 <?= $i < $idx ? 'bg-fp-success' : 'bg-slate-200' ?>"></div>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <?php if ($p['estado'] === 'entregado'): ?>
            <details class="border-t border-slate-100 group">
                <summary class="list-none cursor-pointer px-4 py-3 bg-fp-bg-main/50 hover:bg-fp-bg-main transition-colors">
                    <div class="flex items-center justify-between gap-3">
                        <div class="flex items-center gap-2 text-[13px] font-bold text-fp-text">
                            <i data-lucide="rotate-ccw" class="w-4 h-4 text-fp-primary"></i>
                            Solicitar devolución
                        </div>
                        <i data-lucide="chevron-down" class="w-4 h-4 text-fp-muted transition-transform group-open:rotate-180"></i>
                    </div>
                </summary>

                <div class="px-4 pb-4 pt-2">
                    <div class="mb-3 text-[12px] text-fp-muted bg-amber-50 border border-amber-200 rounded-xl px-3 py-2.5">
                        Solo aplica para productos en buen estado de empaque y dentro del plazo legal de devolución.
                    </div>

                    <?php if (!empty($devolucionesPedido)): ?>
                    <div class="mb-4 p-3 rounded-xl border border-fp-border bg-white">
                        <p class="text-[11px] font-bold uppercase tracking-wide text-fp-muted mb-2">Solicitudes registradas</p>
                        <div class="flex flex-col gap-2">
                            <?php foreach ($devolucionesPedido as $dev):
                                $devCfg = $estadoDevolucionConfig[$dev['estado']] ?? $estadoDevolucionConfig['pendiente'];
                            ?>
                            <div class="flex items-center justify-between gap-3 text-[12px]">
                                <div class="min-w-0">
                                    <p class="font-semibold text-fp-text truncate"><?= htmlspecialchars($dev['producto_nombre']) ?></p>
                                    <p class="text-fp-muted">Cant. <?= (int)$dev['cantidad'] ?> · <?= htmlspecialchars($motivoLabel[$dev['motivo']] ?? $dev['motivo']) ?></p>
                                </div>
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full border text-[10px] font-bold <?= $devCfg['cls'] ?>">
                                    <?= $devCfg['label'] ?>
                                </span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php endif; ?>

                    <?php if (empty($detallesPedido)): ?>
                    <p class="text-[12px] text-fp-muted">No se encontraron productos para este pedido.</p>
                    <?php else: ?>
                    <form method="POST" action="<?= $basePath ?>/mi-cuenta/pedidos/<?= (int)$p['pedido_id'] ?>/devoluciones" class="grid grid-cols-1 md:grid-cols-2 gap-3">
                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-semibold uppercase tracking-wide text-fp-muted mb-1">Producto *</label>
                            <select name="producto_id" required class="w-full h-10 px-3 border border-fp-border rounded-xl text-[13px] text-fp-text focus:outline-none focus:border-fp-primary focus:ring-1 focus:ring-fp-primary/30 js-producto-dev">
                                <option value="">Selecciona un producto</option>
                                <?php foreach ($detallesPedido as $d): ?>
                                <option value="<?= (int)$d['producto_id'] ?>"
                                        data-max="<?= (int)$d['cantidad'] ?>"
                                        data-control="<?= (int)($d['control_especial'] ?? 0) ?>">
                                    <?= htmlspecialchars($d['producto_nombre']) ?> — Comprado: <?= (int)$d['cantidad'] ?>
                                    <?= ((int)($d['control_especial'] ?? 0) === 1) ? ' (Control especial - no aplica)' : '' ?>
                                </option>
                                <?php endforeach; ?>
                            </select>
                            <p class="mt-1 text-[11px] text-red-600 hidden js-aviso-control">Este producto es de control especial y no aplica para devolución.</p>
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold uppercase tracking-wide text-fp-muted mb-1">Cantidad *</label>
                            <input type="number" name="cantidad" min="1" value="1" required class="w-full h-10 px-3 border border-fp-border rounded-xl text-[13px] text-fp-text focus:outline-none focus:border-fp-primary focus:ring-1 focus:ring-fp-primary/30 js-cantidad-dev">
                        </div>

                        <div>
                            <label class="block text-[11px] font-semibold uppercase tracking-wide text-fp-muted mb-1">Motivo *</label>
                            <select name="motivo" required class="w-full h-10 px-3 border border-fp-border rounded-xl text-[13px] text-fp-text focus:outline-none focus:border-fp-primary focus:ring-1 focus:ring-fp-primary/30">
                                <option value="producto_danado">Producto dañado</option>
                                <option value="error_envio">Error de envío</option>
                                <option value="cambio_opinion">Cambio de opinión</option>
                                <option value="vencido">Producto vencido</option>
                                <option value="otro">Otro</option>
                            </select>
                        </div>

                        <div class="md:col-span-2">
                            <label class="block text-[11px] font-semibold uppercase tracking-wide text-fp-muted mb-1">Observación</label>
                            <textarea name="observacion" rows="3" class="w-full px-3 py-2.5 border border-fp-border rounded-xl text-[13px] text-fp-text focus:outline-none focus:border-fp-primary focus:ring-1 focus:ring-fp-primary/30 resize-none" placeholder="Cuéntanos qué sucedió con el producto..."></textarea>
                        </div>

                        <div class="md:col-span-2 flex justify-end">
                            <button type="submit" class="h-10 px-5 bg-fp-primary text-white text-[13px] font-bold rounded-xl hover:bg-fp-primary-light transition-colors shadow-sm">
                                Enviar solicitud
                            </button>
                        </div>
                    </form>
                    <?php endif; ?>
                </div>
            </details>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('details form').forEach(function (form) {
        const selectProducto = form.querySelector('.js-producto-dev');
        const inputCantidad = form.querySelector('.js-cantidad-dev');
        const avisoControl = form.querySelector('.js-aviso-control');
        const submit = form.querySelector('button[type="submit"]');

        if (!selectProducto || !inputCantidad) return;

        const sync = function () {
            const opt = selectProducto.options[selectProducto.selectedIndex];
            const max = parseInt(opt?.dataset?.max || '1', 10);
            const esControl = (opt?.dataset?.control || '0') === '1';
            inputCantidad.max = String(Math.max(1, max));
            if (parseInt(inputCantidad.value || '1', 10) > max) inputCantidad.value = '1';
            if (avisoControl) avisoControl.classList.toggle('hidden', !esControl);
            if (submit) submit.disabled = esControl;
            if (submit) submit.classList.toggle('opacity-60', esControl);
            if (submit) submit.classList.toggle('cursor-not-allowed', esControl);
        };

        selectProducto.addEventListener('change', sync);
        sync();
    });
});
</script>

</div>

<?php
$contenido = ob_get_clean();
require __DIR__ . '/../layouts/tienda_layout.php';
?>
