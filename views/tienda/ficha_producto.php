<?php
/**
 * views/tienda/ficha_producto.php — Detalle de un producto en la tienda.
 * Variables: $producto (array), $basePath (string), $totalItems (int), $enCarrito (int)
 */
$basePath   = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
$stockActual = (int)($producto['stock_actual'] ?? 0);
$precio      = (float)($producto['precio_venta'] ?? 0);
$hayStock    = $stockActual > 0;

ob_start();
?>

<div class="max-w-5xl mx-auto">

    <!-- Breadcrumb -->
    <nav class="flex items-center gap-2 text-[13px] text-slate-400 mb-6">
        <a href="<?= $basePath ?>/tienda" class="hover:text-fp-primary transition-colors flex items-center gap-1">
            <i data-lucide="store" class="w-3.5 h-3.5"></i> Tienda
        </a>
        <span>/</span>
        <span class="text-slate-600 font-medium"><?= htmlspecialchars($producto['nombre']) ?></span>
    </nav>

    <!-- Producto grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-8 bg-white rounded-2xl border border-slate-200 shadow-sm overflow-hidden">

        <!-- Imagen / Ícono -->
        <div class="relative bg-gradient-to-br from-slate-50 to-slate-100 flex items-center justify-center min-h-[280px] md:min-h-[360px]">
            <i data-lucide="pill" class="w-32 h-32 text-fp-primary/20"></i>
            <?php if (!$hayStock): ?>
            <div class="absolute inset-0 bg-white/60 backdrop-blur-sm flex items-center justify-center">
                <span class="bg-slate-700 text-white text-sm font-bold px-4 py-2 rounded-full">Sin stock</span>
            </div>
            <?php endif; ?>
            <!-- Categoría badge -->
            <?php if (!empty($producto['categoria_nombre'])): ?>
            <span class="absolute top-4 left-4 bg-white/90 text-fp-primary text-[11px] font-bold uppercase tracking-wider px-3 py-1 rounded-full border border-fp-primary/10 shadow-sm">
                <?= htmlspecialchars($producto['categoria_nombre']) ?>
            </span>
            <?php endif; ?>
        </div>

        <!-- Info del producto -->
        <div class="p-6 md:p-8 flex flex-col gap-5">

            <!-- Nombre -->
            <div>
                <h1 class="text-2xl font-black text-slate-800 leading-tight mb-2">
                    <?= htmlspecialchars($producto['nombre']) ?>
                </h1>
                <?php if (!empty($producto['principio_activo'])): ?>
                <p class="text-[13px] text-slate-500 font-medium">
                    Principio activo: <span class="font-mono text-fp-primary"><?= htmlspecialchars($producto['principio_activo']) ?></span>
                </p>
                <?php endif; ?>
            </div>

            <!-- Precio -->
            <div class="flex items-baseline gap-2">
                <span class="text-[36px] font-black text-fp-primary leading-none">
                    $<?= number_format($precio, 0, ',', '.') ?>
                </span>
                <span class="text-slate-400 text-[14px]">COP / unidad</span>
            </div>

            <!-- Stock indicator -->
            <div class="flex items-center gap-2">
                <?php if ($stockActual > 10): ?>
                    <div class="w-2 h-2 rounded-full bg-green-400"></div>
                    <span class="text-[13px] text-green-600 font-semibold">Disponible</span>
                <?php elseif ($stockActual > 0): ?>
                    <div class="w-2 h-2 rounded-full bg-amber-400 animate-pulse"></div>
                    <span class="text-[13px] text-amber-600 font-semibold">Pocas unidades (<?= $stockActual ?> disp.)</span>
                <?php else: ?>
                    <div class="w-2 h-2 rounded-full bg-red-400"></div>
                    <span class="text-[13px] text-red-500 font-semibold">Sin stock</span>
                <?php endif; ?>
            </div>

            <!-- Descripción -->
            <?php if (!empty($producto['descripcion'])): ?>
            <p class="text-[14px] text-slate-600 leading-relaxed border-t border-slate-100 pt-4">
                <?= nl2br(htmlspecialchars($producto['descripcion'])) ?>
            </p>
            <?php endif; ?>

            <!-- Info extra -->
            <div class="grid grid-cols-2 gap-3 text-[13px]">
                <?php if (!empty($producto['laboratorio'])): ?>
                <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                    <span class="text-slate-400 block text-[10px] uppercase tracking-wide mb-0.5">Laboratorio</span>
                    <span class="font-semibold text-slate-700"><?= htmlspecialchars($producto['laboratorio']) ?></span>
                </div>
                <?php endif; ?>
                <?php if (!empty($producto['registro_invima'])): ?>
                <div class="bg-slate-50 rounded-xl p-3 border border-slate-100">
                    <span class="text-slate-400 block text-[10px] uppercase tracking-wide mb-0.5">Reg. INVIMA</span>
                    <span class="font-mono font-semibold text-fp-primary text-[12px]"><?= htmlspecialchars($producto['registro_invima']) ?></span>
                </div>
                <?php endif; ?>
            </div>

            <!-- Control de cantidad + Botón -->
            <?php if ($hayStock): ?>
            <div class="flex items-center gap-3 mt-auto pt-4 border-t border-slate-100">
                <!-- Selector cantidad si ya está en carrito -->
                <div id="qtyControls" class="flex items-center gap-2 bg-slate-50 rounded-xl border border-slate-200 p-1">
                    <button type="button" id="btnMinus" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 hover:bg-white hover:text-fp-primary transition-all disabled:opacity-30" <?= $enCarrito <= 0 ? 'disabled' : '' ?>>
                        <i data-lucide="minus" class="w-4 h-4"></i>
                    </button>
                    <span id="qtyDisplay" class="w-8 text-center font-black text-slate-700 text-[15px]"><?= max(1, $enCarrito) ?></span>
                    <button type="button" id="btnPlus" class="w-8 h-8 rounded-lg flex items-center justify-center text-slate-500 hover:bg-white hover:text-fp-primary transition-all">
                        <i data-lucide="plus" class="w-4 h-4"></i>
                    </button>
                </div>

                <!-- Añadir al carrito -->
                <button id="btnAgregar"
                    class="flex-1 flex items-center justify-center gap-2 h-11 bg-fp-secondary hover:bg-fp-secondary/90 text-white font-bold rounded-xl transition-all shadow-sm active:scale-95 text-[14px]"
                    data-producto-id="<?= $producto['producto_id'] ?>"
                    data-nombre="<?= htmlspecialchars($producto['nombre']) ?>"
                    data-precio="<?= $precio ?>">
                    <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                    <?= $enCarrito > 0 ? 'Actualizar carrito' : 'Añadir al carrito' ?>
                </button>
            </div>

            <!-- Ir al carrito si ya hay algo -->
            <?php if ($enCarrito > 0): ?>
            <a href="<?= $basePath ?>/tienda/carrito" class="flex items-center justify-center gap-2 text-[13px] text-fp-primary hover:underline font-semibold">
                <i data-lucide="arrow-right" class="w-4 h-4"></i> Ver carrito (<?= $enCarrito ?> unidades)
            </a>
            <?php endif; ?>

            <?php else: ?>
            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200 text-center text-[13px] text-slate-500 mt-auto">
                <i data-lucide="bell" class="w-4 h-4 inline-block mr-1 text-slate-400"></i>
                Producto sin stock disponible actualmente.
            </div>
            <?php endif; ?>

        </div>
    </div>

    <!-- Volver al catálogo -->
    <div class="mt-6">
        <a href="<?= $basePath ?>/tienda" class="inline-flex items-center gap-2 text-[13px] text-slate-500 hover:text-fp-primary transition-colors font-medium">
            <i data-lucide="arrow-left" class="w-4 h-4"></i> Volver al catálogo
        </a>
    </div>
</div>

<script>
(function() {
    const basePath   = '<?= $basePath ?>';
    const productoId = <?= (int)$producto['producto_id'] ?>;
    let qty          = <?= max(1, $enCarrito) ?>;

    const display  = document.getElementById('qtyDisplay');
    const btnMinus = document.getElementById('btnMinus');
    const btnPlus  = document.getElementById('btnPlus');
    const btnAdd   = document.getElementById('btnAgregar');

    function updateUI() {
        display.textContent = qty;
        btnMinus.disabled   = qty <= 1;
    }

    btnMinus?.addEventListener('click', () => { if (qty > 1) { qty--; updateUI(); } });
    btnPlus?.addEventListener('click',  () => { qty++; updateUI(); });

    btnAdd?.addEventListener('click', async () => {
        btnAdd.disabled = true;
        btnAdd.innerHTML = '<svg class="animate-spin w-4 h-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Agregando...';

        try {
            const res  = await fetch(basePath + '/tienda/carrito/agregar', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json', 'X-Requested-With': 'XMLHttpRequest' },
                body: JSON.stringify({ producto_id: productoId, cantidad: qty })
            });
            const data = await res.json();
            if (data.success) {
                if (typeof actualizarBadge === 'function') actualizarBadge(data.totalItems ?? 0);
                btnAdd.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 11 4 18"></polyline></svg> ¡Añadido!';
                setTimeout(() => {
                    btnAdd.disabled = false;
                    btnAdd.innerHTML = '<svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"></path><line x1="3" y1="6" x2="21" y2="6"></line><path d="M16 10a4 4 0 01-8 0"></path></svg> Añadir al carrito';
                }, 2000);
            }
        } catch(e) {
            btnAdd.disabled = false;
            btnAdd.textContent = 'Error — Reintentar';
        }
    });

    updateUI();
})();
</script>

<?php
$contenido = ob_get_clean();
require __DIR__ . '/../layouts/tienda_layout.php';
?>
