<?php
/**
 * views/tienda/catalogo.php — Catálogo de productos de la tienda en línea.
 * RF-6.1, HU-CLI-03: Solo productos sin control especial y con stock.
 */
$basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
ob_start();
?>

<!-- Hero Tienda -->
<div class="relative overflow-hidden rounded-2xl bg-gradient-to-br from-fp-primary via-[#0f4c65] to-[#1a6b8a] p-6 sm:p-8 mb-8">
    <div class="relative z-10">
        <div class="inline-flex items-center gap-2 bg-white/10 border border-white/20 text-white text-xs font-bold px-3 py-1 rounded-full mb-4">
            <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> Productos certificados INVIMA
        </div>
        <h1 class="text-2xl sm:text-3xl font-black text-white leading-tight mb-2">
            Tu farmacia de confianza<br><span class="text-fp-secondary">en línea</span>
        </h1>
        <p class="text-white/80 text-[14px] max-w-md">Medicamentos de venta libre, vitaminas y más. Entrega a domicilio rápida y segura.</p>
    </div>
    <!-- Decoración -->
    <div class="absolute right-0 top-0 h-full w-1/3 opacity-10 flex items-center justify-end pr-8">
        <i data-lucide="package" class="w-40 h-40 text-white"></i>
    </div>
</div>

<!-- Filtros de Categoría -->
<?php if (!empty($categorias)): ?>
<div class="flex items-center gap-2 mb-6 overflow-x-auto no-scrollbar pb-1 scroll-smooth snap-x">
    <a href="<?= $basePath ?>/tienda" class="snap-start shrink-0 px-3 py-1.5 rounded-lg text-[13px] font-semibold transition-colors <?= empty($_GET['categoria']) ? 'bg-fp-primary text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:border-fp-primary hover:text-fp-primary' ?>">
        Todos
    </a>
    <?php foreach ($categorias as $cat): ?>
    <a href="<?= $basePath ?>/tienda?categoria=<?= $cat['categoria_id'] ?><?= !empty($_GET['q']) ? '&q=' . urlencode($_GET['q']) : '' ?>"
       class="snap-start shrink-0 px-3 py-1.5 rounded-lg text-[13px] font-semibold transition-colors <?= (($_GET['categoria'] ?? '') == $cat['categoria_id']) ? 'bg-fp-primary text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-600 hover:border-fp-primary hover:text-fp-primary' ?>">
        <?= htmlspecialchars($cat['nombre']) ?>
    </a>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Resultados -->
<div class="flex items-center justify-between mb-4">
    <p class="text-[13px] text-slate-500 font-medium">
        <?= count($productos) ?> producto<?= count($productos) !== 1 ? 's' : '' ?> encontrado<?= count($productos) !== 1 ? 's' : '' ?>
    </p>
    <?php if (!empty($_GET['q'])): ?>
    <div class="flex items-center gap-2">
        <span class="text-[12px] text-slate-500">Buscando: <strong><?= htmlspecialchars($_GET['q']) ?></strong></span>
        <a href="<?= $basePath ?>/tienda" class="text-[12px] text-fp-error hover:underline font-semibold flex items-center gap-1">
            <i data-lucide="x" class="w-3 h-3"></i> Limpiar
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Grid de Productos -->
<?php if (empty($productos)): ?>
<div class="flex flex-col items-center justify-center py-20 text-center">
    <div class="w-20 h-20 rounded-full bg-slate-100 flex items-center justify-center mb-4">
        <i data-lucide="search-x" class="w-10 h-10 text-slate-400"></i>
    </div>
    <h3 class="text-[16px] font-bold text-slate-700 mb-1">Ningún producto encontrado</h3>
    <p class="text-[13px] text-slate-500 max-w-xs mb-4">Intenta con términos diferentes o explora todas las categorías.</p>
    <a href="<?= $basePath ?>/tienda" class="px-4 py-2 bg-fp-primary text-white text-[13px] font-bold rounded-xl hover:bg-fp-primary-dark transition-colors">
        Ver todos los productos
    </a>
</div>
<?php else: ?>
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-4 sm:gap-5">
    <?php foreach ($productos as $p): ?>
    <?php
        $stock     = (int)($p['stock_actual'] ?? 0);
        $precio    = number_format((float)$p['precio_venta'], 0, ',', '.');
        $stockLow  = $stock > 0 && $stock <= 5;
    ?>
    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden flex flex-col shadow-sm hover:shadow-md hover:border-fp-primary/30 transition-all group"
         data-producto-id="<?= $p['producto_id'] ?>"
         data-nombre="<?= strtolower(htmlspecialchars($p['nombre'])) ?>"
         data-categoria="<?= (int)$p['categoria_id'] ?>">

        <!-- Imagen / placeholder — clickeable hacia la ficha del producto -->
        <a href="<?= $basePath ?>/tienda/producto/<?= $p['producto_id'] ?>" class="block relative bg-gradient-to-br from-slate-50 to-slate-100 h-[160px] flex items-center justify-center overflow-hidden">
            <?php if (!empty($p['imagen_principal'])): ?>
                <img src="<?= $basePath ?>/assets/uploads/productos/<?= $p['producto_id'] ?>/<?= htmlspecialchars($p['imagen_principal']) ?>"
                     alt="<?= htmlspecialchars($p['nombre']) ?>"
                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300" />
            <?php else: ?>
                <?php $iconoPlaceholder = (($p['es_medicamento'] ?? 1) == 1) ? 'pill' : 'package'; ?>
                <i data-lucide="<?= $iconoPlaceholder ?>" class="w-16 h-16 text-fp-primary/20 group-hover:scale-110 group-hover:text-fp-primary/30 transition-all duration-300"></i>
            <?php endif; ?>
            <!-- Categoria badge -->
            <span class="absolute top-2.5 left-2.5 bg-white/90 backdrop-blur-sm text-[10px] font-bold uppercase tracking-wider text-fp-primary px-2 py-0.5 rounded-full border border-fp-primary/10">
                <?= htmlspecialchars($p['categoria_nombre'] ?? '') ?>
            </span>
            <!-- Stock low badge -->
            <?php if ($stockLow): ?>
            <span class="absolute top-2.5 right-2.5 bg-fp-warning/10 text-fp-warning text-[10px] font-bold px-2 py-0.5 rounded-full border border-fp-warning/20">
                Últimas unidades
            </span>
            <?php endif; ?>
        </a>

        <!-- Info -->
        <div class="p-4 flex flex-col flex-1">
            <h3 class="font-bold text-[14px] text-slate-800 leading-snug mb-1 line-clamp-2" title="<?= htmlspecialchars($p['nombre']) ?>">
                <?= htmlspecialchars($p['nombre']) ?>
            </h3>
            <?php if (!empty($p['principio_activo'])): ?>
            <p class="text-[11px] text-slate-500 font-medium mb-3 line-clamp-1"><?= htmlspecialchars($p['principio_activo']) ?></p>
            <?php endif; ?>

            <div class="flex items-center justify-between mt-auto">
                <div>
                    <div class="text-[18px] font-black text-fp-primary leading-none">$<?= $precio ?></div>
                    <div class="text-[10px] text-slate-400 mt-0.5">COP · unidad</div>
                </div>
                <div class="flex items-center gap-1 text-[11px] text-slate-500 font-medium bg-slate-50 px-2 py-1 rounded-lg border border-slate-200">
                    <i data-lucide="box" class="w-3 h-3"></i> <?= $stock ?>
                </div>
            </div>

            <!-- Botón Añadir al Carrito -->
            <button
                type="button"
                onclick="agregarAlCarrito(<?= $p['producto_id'] ?>, '<?= htmlspecialchars(addslashes($p['nombre']), ENT_QUOTES) ?>')"
                class="mt-3 w-full h-10 flex items-center justify-center gap-2 bg-fp-secondary/10 hover:bg-fp-secondary text-fp-secondary hover:text-white border-2 border-fp-secondary/30 hover:border-fp-secondary text-[13px] font-bold rounded-xl transition-all duration-200 active:scale-95"
                id="btn-add-<?= $p['producto_id'] ?>"
            >
                <i data-lucide="shopping-cart" class="w-4 h-4"></i>
                <span>Añadir al carrito</span>
            </button>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Modal Confirmación (Toast flotante) -->
<script>
const BP = '<?= $basePath ?>';

async function agregarAlCarrito(productoId, nombre) {
    const btn = document.getElementById('btn-add-' + productoId);
    if (btn) {
        btn.disabled = true;
        btn.innerHTML = `<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> <span>Añadiendo...</span>`;
        if (typeof lucide !== 'undefined') lucide.createIcons();
    }

    try {
        const res = await fetch(BP + '/tienda/carrito/agregar', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `producto_id=${productoId}&cantidad=1`
        });
        const data = await res.json();

        if (data.success) {
            actualizarBadge(data.totalItems);
            showToastTienda(`«${nombre}» añadido al carrito ✓`, 'success');
            if (btn) {
                btn.innerHTML = `<i data-lucide="check" class="w-4 h-4"></i> <span>Añadido</span>`;
                btn.classList.add('bg-fp-success', 'text-white', 'border-fp-success');
                if (typeof lucide !== 'undefined') lucide.createIcons();
                setTimeout(() => {
                    btn.disabled = false;
                    btn.classList.remove('bg-fp-success', 'text-white', 'border-fp-success');
                    btn.innerHTML = `<i data-lucide="shopping-cart" class="w-4 h-4"></i> <span>Añadir al carrito</span>`;
                    if (typeof lucide !== 'undefined') lucide.createIcons();
                }, 2000);
            }
        } else {
            showToastTienda(data.error || 'No se pudo añadir.', 'error');
            if (btn) { btn.disabled = false; btn.innerHTML = `<i data-lucide="shopping-cart" class="w-4 h-4"></i> <span>Añadir al carrito</span>`; if (typeof lucide !== 'undefined') lucide.createIcons(); }
        }
    } catch (e) {
        showToastTienda('Error de conexión.', 'error');
        if (btn) { btn.disabled = false; btn.innerHTML = `<i data-lucide="shopping-cart" class="w-4 h-4"></i> <span>Añadir al carrito</span>`; if (typeof lucide !== 'undefined') lucide.createIcons(); }
    }
}
</script>

<?php
$contenido = ob_get_clean();
require __DIR__ . '/../layouts/tienda_layout.php';
?>
