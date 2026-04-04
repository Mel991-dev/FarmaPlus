<?php
/**
 * views/tienda/checkout.php — Checkout con dirección y resumen antes de pagar.
 * RF-6.3, HU-CLI-05: Selección dirección + cálculo domicilio + redirección a MP.
 */
$basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
ob_start();
?>

<div class="max-w-5xl mx-auto">

    <!-- Stepper visual -->
    <div class="flex items-center gap-2 sm:gap-4 mb-8 overflow-x-auto no-scrollbar pb-1">
        <div class="flex items-center gap-2 shrink-0">
            <div class="w-7 h-7 rounded-full bg-fp-success text-white flex items-center justify-center text-[12px] font-bold shrink-0">
                <i data-lucide="check" class="w-3.5 h-3.5"></i>
            </div>
            <span class="text-[13px] font-bold text-fp-success">Carrito</span>
        </div>
        <div class="h-[2px] w-8 sm:w-12 bg-fp-primary rounded shrink-0"></div>
        <div class="flex items-center gap-2 shrink-0">
            <div class="w-7 h-7 rounded-full bg-fp-primary text-white flex items-center justify-center text-[12px] font-bold shrink-0">2</div>
            <span class="text-[13px] font-bold text-fp-primary">Dirección y pago</span>
        </div>
        <div class="h-[2px] w-8 sm:w-12 bg-slate-200 rounded shrink-0"></div>
        <div class="flex items-center gap-2 shrink-0">
            <div class="w-7 h-7 rounded-full bg-slate-200 text-slate-400 flex items-center justify-center text-[12px] font-bold shrink-0">3</div>
            <span class="text-[13px] font-medium text-slate-400">Confirmación</span>
        </div>
    </div>

    <?php if (!empty($_GET['error'])): ?>
    <div class="mb-6 bg-fp-error/10 border-l-4 border-fp-error p-3 rounded-lg flex items-center gap-2 text-sm font-semibold text-fp-error">
        <i data-lucide="alert-circle" class="w-5 h-5"></i> <?= htmlspecialchars($_GET['error']) ?>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= $basePath ?>/tienda/checkout/procesar" id="checkoutForm">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Panel izquierdo: Dirección -->
        <div class="lg:col-span-2 flex flex-col gap-5">

            <!-- Selección de dirección de entrega -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <h2 class="font-bold text-[16px] text-slate-800 mb-4 flex items-center gap-2">
                    <i data-lucide="map-pin" class="w-5 h-5 text-fp-secondary"></i> Dirección de entrega
                </h2>

                <?php if (empty($direcciones)): ?>
                <div class="flex flex-col items-center p-6 bg-slate-50 rounded-xl border border-dashed border-slate-300 text-center">
                    <i data-lucide="map-pin-off" class="w-10 h-10 text-slate-400 mb-2"></i>
                    <p class="text-[14px] font-bold text-slate-600 mb-1">No tienes direcciones guardadas</p>
                    <p class="text-[13px] text-slate-500 mb-4">Agrega una dirección para continuar con el pedido.</p>
                    <a href="<?= $basePath ?>/mi-cuenta/direcciones" class="px-4 py-2 bg-fp-primary text-white text-[13px] font-bold rounded-xl hover:bg-fp-primary-dark transition-colors flex items-center gap-2">
                        <i data-lucide="plus" class="w-4 h-4"></i> Agregar dirección
                    </a>
                </div>
                <?php else: ?>
                <div class="flex flex-col gap-3">
                    <?php foreach ($direcciones as $idx => $dir): ?>
                    <label class="flex items-start gap-3 p-4 rounded-xl border-2 cursor-pointer transition-all hover:border-fp-primary/50 has-[:checked]:border-fp-primary has-[:checked]:bg-fp-primary/5 group"
                           for="dir-<?= $dir['direccion_id'] ?>">
                        <input
                            type="radio"
                            name="direccion_entrega_id"
                            id="dir-<?= $dir['direccion_id'] ?>"
                            value="<?= $dir['direccion_id'] ?>"
                            <?= $idx === 0 ? 'checked' : '' ?>
                            class="mt-0.5 accent-fp-primary w-4 h-4 shrink-0"
                            required
                        >
                        <div class="flex-1 min-w-0">
                            <div class="font-bold text-[14px] text-slate-800"><?= htmlspecialchars($dir['direccion']) ?></div>
                            <div class="text-[12px] text-slate-500 mt-0.5">
                                <?php if (!empty($dir['barrio'])): ?><?= htmlspecialchars($dir['barrio']) ?>, <?php endif; ?>
                                <?= htmlspecialchars($dir['ciudad'] ?? '') ?>
                            </div>
                            <?php if (!empty($dir['referencia'])): ?>
                            <div class="text-[11px] text-slate-400 mt-1 italic"><?= htmlspecialchars($dir['referencia']) ?></div>
                            <?php endif; ?>
                        </div>
                        <?php if ($dir['principal'] ?? false): ?>
                        <span class="shrink-0 text-[10px] font-bold text-fp-primary bg-fp-primary/10 px-2 py-0.5 rounded-full">Principal</span>
                        <?php endif; ?>
                    </label>
                    <?php endforeach; ?>
                </div>
                <a href="<?= $basePath ?>/mi-cuenta/direcciones" class="mt-3 text-[12px] text-fp-primary hover:underline font-semibold flex items-center gap-1">
                    <i data-lucide="plus" class="w-3 h-3"></i> Gestionar direcciones
                </a>
                <?php endif; ?>
            </div>

            <!-- Método de pago -->
            <div class="bg-white rounded-2xl border border-slate-200 p-5 shadow-sm">
                <h2 class="font-bold text-[16px] text-slate-800 mb-4 flex items-center gap-2">
                    <i data-lucide="credit-card" class="w-5 h-5 text-fp-secondary"></i> Método de pago
                </h2>
                <div class="flex flex-col sm:flex-row gap-3">
                    <!-- MercadoPago -->
                    <label class="flex items-center gap-4 p-4 flex-1 rounded-xl border-2 border-fp-primary bg-fp-primary/5 cursor-pointer">
                        <input type="radio" name="metodo_pago" value="mercadopago" checked class="accent-fp-primary w-4 h-4 shrink-0">
                        <div class="flex-1">
                            <div class="font-bold text-[14px] text-slate-800 flex items-center gap-2">
                                <span class="text-[11px] px-2 py-0.5 bg-[#009EE3] text-white rounded font-bold">MP</span>
                                MercadoPago
                            </div>
                            <div class="text-[11px] text-slate-500 mt-0.5">Tarjeta débito/crédito, PSE, Efecty</div>
                        </div>
                    </label>
                </div>
                <p class="text-[11px] text-slate-400 mt-3 flex items-center gap-1">
                    <i data-lucide="shield-check" class="w-3.5 h-3.5 text-green-500"></i>
                    Tus datos bancarios son procesados de forma segura por MercadoPago. FarmaPlus no los almacena.
                </p>
            </div>
        </div>

        <!-- Panel derecho: Resumen -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-2xl border border-slate-200 p-5 sticky top-24 shadow-sm">
                <h2 class="font-bold text-[16px] text-slate-800 mb-4 pb-3 border-b border-slate-100">Resumen</h2>

                <!-- Items del carrito -->
                <div class="flex flex-col gap-2 mb-4">
                    <?php foreach ($carrito as $item): ?>
                    <div class="flex items-start justify-between gap-2">
                        <div class="flex-1 min-w-0">
                            <div class="text-[12px] font-semibold text-slate-700 leading-tight truncate"><?= htmlspecialchars($item['nombre']) ?></div>
                            <div class="text-[11px] text-slate-400">x<?= $item['cantidad'] ?></div>
                        </div>
                        <span class="text-[13px] font-bold text-slate-700 shrink-0">$<?= number_format($item['precio_unitario'] * $item['cantidad'], 0, ',', '.') ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>

                <div class="border-t border-slate-100 pt-3 flex flex-col gap-1.5 mb-4">
                    <div class="flex justify-between text-[13px]">
                        <span class="text-slate-500">Subtotal</span>
                        <span class="font-semibold">$<?= number_format($subtotal, 0, ',', '.') ?></span>
                    </div>
                    <div class="flex justify-between text-[13px]">
                        <span class="text-slate-500">Domicilio estimado</span>
                        <span class="font-semibold text-fp-success">$<?= number_format($costoEnvio, 0, ',', '.') ?></span>
                    </div>
                    <div class="border-t border-slate-100 mt-2 pt-2 flex justify-between">
                        <span class="font-black text-[15px]">Total</span>
                        <span class="font-black text-[17px] text-fp-primary">$<?= number_format($total, 0, ',', '.') ?></span>
                    </div>
                </div>

                <?php if (!empty($direcciones)): ?>
                <button type="submit"
                        class="w-full h-12 flex items-center justify-center gap-2 bg-[#009EE3] hover:bg-[#0080c0] text-white text-[14px] font-bold rounded-xl shadow-sm transition-all active:scale-95">
                    <span class="text-[11px] px-1.5 py-0.5 bg-white/20 rounded font-bold">MP</span>
                    Pagar con MercadoPago
                </button>
                <p class="text-[11px] text-slate-400 text-center mt-2">Serás redirigido al checkout seguro de MercadoPago</p>
                <?php else: ?>
                <div class="w-full h-12 flex items-center justify-center bg-slate-100 text-slate-400 text-[13px] font-semibold rounded-xl cursor-not-allowed">
                    Agrega una dirección para continuar
                </div>
                <?php endif; ?>

            </div>
        </div>

    </div>
    </form>
</div>

<script>
    document.getElementById('checkoutForm')?.addEventListener('submit', function(e) {
        const btn = this.querySelector('button[type="submit"]');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i data-lucide="loader-2" class="w-4 h-4 animate-spin"></i> Redirigiendo a MercadoPago...';
            if (typeof lucide !== 'undefined') lucide.createIcons();
        }
    });
</script>

<?php
$contenido = ob_get_clean();
require __DIR__ . '/../layouts/tienda_layout.php';
?>
