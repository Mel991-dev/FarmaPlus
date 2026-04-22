<?php
/**
 * views/tienda/carrito.php — Carrito de compras.
 * HU-CLI-04: Ver productos, ajustar cantidades, proceder a pago.
 */
$basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
ob_start();
?>

<div class="max-w-5xl mx-auto">

    <!-- Header -->
    <div class="flex items-center justify-between mb-8">
        <div>
            <h1 class="text-2xl font-black text-slate-800 flex items-center gap-2">
                <i data-lucide="shopping-cart" class="w-6 h-6 text-fp-secondary"></i> Mi carrito
            </h1>
            <p class="text-[13px] text-slate-500 mt-1"><?= $totalItems ?> producto<?= $totalItems !== 1 ? 's' : '' ?> en tu carrito</p>
        </div>
        <a href="<?= $basePath ?>/tienda" class="flex items-center gap-1.5 text-[13px] font-semibold text-fp-primary hover:underline">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Seguir comprando
        </a>
    </div>

    <?php if (empty($carrito)): ?>
    <!-- Carrito vacío -->
    <div class="flex flex-col items-center justify-center py-20 text-center bg-white rounded-2xl border border-slate-200">
        <div class="w-24 h-24 rounded-full bg-slate-100 flex items-center justify-center mb-5">
            <i data-lucide="shopping-cart" class="w-12 h-12 text-slate-300"></i>
        </div>
        <h2 class="text-[18px] font-bold text-slate-700 mb-2">Tu carrito está vacío</h2>
        <p class="text-[14px] text-slate-500 max-w-xs mb-6">Agrega medicamentos y productos farmacéuticos desde nuestro catálogo.</p>
        <a href="<?= $basePath ?>/tienda" class="px-6 py-2.5 bg-fp-secondary text-white text-[14px] font-bold rounded-xl hover:bg-fp-secondary/90 transition-colors shadow-sm flex items-center gap-2">
            <i data-lucide="store" class="w-4 h-4"></i> Ir al catálogo
        </a>
    </div>

    <?php else: ?>
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Lista de Items -->
        <div class="lg:col-span-2 flex flex-col gap-3">
            <?php foreach ($carrito as $productoId => $item): ?>
            <div class="bg-white rounded-xl border border-slate-200 p-4 flex flex-col sm:flex-row sm:items-center gap-4 cart-row"
                 id="row-<?= $productoId ?>">

                <!-- Imagen / Ícono Producto -->
                <div class="w-16 h-16 rounded-xl bg-slate-50 border border-slate-200 overflow-hidden shrink-0 flex items-center justify-center">
                    <?php if (!empty($item['imagen'])): ?>
                        <img src="<?= $basePath ?>/assets/uploads/productos/<?= $productoId ?>/<?= htmlspecialchars($item['imagen']) ?>"
                             alt="<?= htmlspecialchars($item['nombre']) ?>"
                             class="w-full h-full object-cover" />
                    <?php else: ?>
                        <?php $icono = (($item['es_medicamento'] ?? 1) == 1) ? 'pill' : 'package'; ?>
                        <i data-lucide="<?= $icono ?>" class="w-8 h-8 text-fp-primary/30"></i>
                    <?php endif; ?>
                </div>

                <!-- Info -->
                <div class="flex-1 min-w-0">
                    <div class="font-bold text-[14px] text-slate-800 leading-tight truncate"><?= htmlspecialchars($item['nombre']) ?></div>
                    <div class="text-[13px] text-slate-500 mt-0.5">$<?= number_format($item['precio_unitario'], 0, ',', '.') ?> / unidad</div>
                </div>

                <!-- Control Cantidad -->
                <div class="flex items-center gap-2 shrink-0">
                    <button
                        onclick="cambiarCantidad(<?= $productoId ?>, -1)"
                        class="w-8 h-8 rounded-lg bg-slate-100 border border-slate-200 hover:bg-slate-200 flex items-center justify-center transition-colors text-slate-600"
                    >
                        <i data-lucide="minus" class="w-3.5 h-3.5"></i>
                    </button>
                    <span class="w-8 text-center font-bold text-[14px] text-slate-800 qty-label" id="qty-<?= $productoId ?>"><?= $item['cantidad'] ?></span>
                    <button
                        onclick="cambiarCantidad(<?= $productoId ?>, 1)"
                        class="w-8 h-8 rounded-lg bg-slate-100 border border-slate-200 hover:bg-slate-200 flex items-center justify-center transition-colors text-slate-600"
                    >
                        <i data-lucide="plus" class="w-3.5 h-3.5"></i>
                    </button>
                </div>

                <!-- Subtotal -->
                <div class="text-right shrink-0 sm:ml-auto">
                    <div class="font-black text-[15px] text-fp-primary" id="sub-<?= $productoId ?>">
                        $<?= number_format($item['subtotal'], 0, ',', '.') ?>
                    </div>
                    <button
                        onclick="eliminarItem(<?= $productoId ?>)"
                        class="text-[11px] text-fp-error hover:underline font-semibold mt-1 flex items-center gap-0.5 ml-auto"
                    >
                        <i data-lucide="trash-2" class="w-3 h-3"></i> Eliminar
                    </button>
                </div>
            </div>
            <?php endforeach; ?>

            <!-- Botón Vaciar -->
            <button onclick="vaciarCarrito()" class="self-start text-[12px] text-slate-400 hover:text-fp-error transition-colors font-semibold flex items-center gap-1 mt-1">
                <i data-lucide="trash" class="w-3.5 h-3.5"></i> Vaciar carrito
            </button>
        </div>

        <!-- Resumen del Pedido -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 sticky top-24 shadow-sm">
                <h2 class="font-bold text-[16px] text-slate-800 mb-4 pb-3 border-b border-slate-100">Resumen del pedido</h2>

                <div class="flex items-center justify-between mb-2">
                    <span class="text-[13px] text-slate-500">Subtotal (<span id="totalItemsLabel"><?= $totalItems ?></span> items)</span>
                    <span class="font-semibold text-[14px] text-slate-800" id="subtotalLabel">$<?= number_format($subtotal, 0, ',', '.') ?></span>
                </div>
                <div class="flex items-center justify-between mb-4">
                    <span class="text-[13px] text-slate-500">Domicilio</span>
                    <span class="text-[13px] text-slate-500 italic">Se calcula al seleccionar dirección</span>
                </div>

                <div class="border-t border-slate-100 pt-4 mb-5">
                    <div class="flex items-center justify-between">
                        <span class="font-black text-[15px] text-slate-800">Total estimado</span>
                        <span class="font-black text-[17px] text-fp-primary" id="totalLabel">$<?= number_format($subtotal, 0, ',', '.') ?></span>
                    </div>
                    <p class="text-[11px] text-slate-400 mt-1">* El total final incluye el costo de domicilio</p>
                </div>

                <a href="<?= $basePath ?>/tienda/checkout" id="btnCheckout"
                   class="w-full h-12 flex items-center justify-center gap-2 bg-fp-secondary hover:bg-fp-secondary/90 text-white text-[14px] font-bold rounded-xl shadow-sm transition-all active:scale-95">
                    <i data-lucide="lock" class="w-4 h-4"></i> Proceder al pago
                </a>

                <!-- Seguridad -->
                <div class="flex items-center justify-center gap-3 mt-4 text-[11px] text-slate-400">
                    <span class="flex items-center gap-1"><i data-lucide="shield-check" class="w-3.5 h-3.5 text-green-500"></i> Pago seguro</span>
                    <span class="flex items-center gap-1"><i data-lucide="lock" class="w-3.5 h-3.5 text-blue-500"></i> SSL</span>
                </div>
            </div>
        </div>

    </div>
    <?php endif; ?>
</div>

<script>
const BP = '<?= $basePath ?>';
const preciosUnitarios = {
    <?php foreach ($carrito as $id => $item): ?>
    "<?= $id ?>": <?= $item['precio_unitario'] ?>,
    <?php endforeach; ?>
};

async function cambiarCantidad(productoId, delta) {
    const qtyEl  = document.getElementById('qty-' + productoId);
    const actual = parseInt(qtyEl?.textContent || '1');
    const nueva  = actual + delta;

    if (nueva <= 0) { return eliminarItem(productoId); }

    try {
        const res  = await fetch(BP + '/tienda/carrito/actualizar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `producto_id=${productoId}&cantidad=${nueva}`
        });
        const data = await res.json();
        if (data.success) {
            if (qtyEl) qtyEl.textContent = nueva;
            const subEl = document.getElementById('sub-' + productoId);
            if (subEl) subEl.textContent = '$' + formatNum(preciosUnitarios[productoId] * nueva);
            actualizarResumen(data.subtotal, data.totalItems);
        }
    } catch(e) {}
}

async function eliminarItem(productoId) {
    try {
        const res = await fetch(BP + '/tienda/carrito/actualizar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `producto_id=${productoId}&cantidad=0`
        });
        const data = await res.json();
        if (data.success) {
            const row = document.getElementById('row-' + productoId);
            if (row) { row.style.opacity = '0'; setTimeout(() => { row.remove(); }, 300); }
            actualizarResumen(data.subtotal, data.totalItems);
        }
    } catch(e) {}
}

async function vaciarCarrito() {
    if (!confirm('¿Vaciar todo el carrito?')) return;
    await fetch(BP + '/tienda/carrito/vaciar', { method: 'POST' });
    window.location.reload();
}

function actualizarResumen(subtotal, totalItems) {
    const s = document.getElementById('subtotalLabel');
    const t = document.getElementById('totalLabel');
    const n = document.getElementById('totalItemsLabel');
    if (s) s.textContent = '$' + formatNum(subtotal);
    if (t) t.textContent = '$' + formatNum(subtotal);
    if (n) n.textContent = totalItems;
    actualizarBadge(totalItems);
    if (totalItems === 0) setTimeout(() => window.location.reload(), 400);
}

function formatNum(n) {
    return Math.round(n).toLocaleString('es-CO');
}
</script>

<?php
$contenido = ob_get_clean();
require __DIR__ . '/../layouts/tienda_layout.php';
?>
