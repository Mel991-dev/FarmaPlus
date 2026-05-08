<?php
/**
 * views/gerente/devoluciones/crear.php
 * Formulario de nueva solicitud de devolución (creada por el gerente).
 *
 * Variables esperadas:
 *   $pedidos    array  — pedidos entregados disponibles
 *   $ventas     array  — ventas presenciales recientes
 *   $productos  array  — productos del pedido/venta seleccionado (si aplica)
 *   $error      string — mensaje de error de sesión
 */
$basePath   = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
$params     = $_GET;
$tipoOrigen = $params['tipo'] ?? 'pedido';
$selPedido  = (int)($params['pedido_id'] ?? 0);
$selVenta   = (int)($params['venta_id']  ?? 0);
?>

<!-- Cabecera -->
<div class="flex items-center gap-3 mb-6">
    <a href="<?= $basePath ?>/gerente/devoluciones"
       class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-fp-border text-fp-muted hover:text-fp-primary hover:border-fp-primary transition-all">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
    </a>
    <div>
        <h1 class="text-xl font-bold text-fp-text">Nueva Solicitud de Devolución</h1>
        <p class="text-[13px] text-fp-muted mt-0.5">Registra una devolución desde un pedido online o una venta presencial.</p>
    </div>
</div>

<?php if ($error): ?>
<div class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-[13px] font-semibold">
    <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
    <?= htmlspecialchars($error) ?>
</div>
<?php endif; ?>

<!-- Nota legal -->
<div class="mb-6 flex items-start gap-3 bg-amber-50 border border-amber-200 text-amber-800 rounded-xl px-4 py-3 text-[13px]">
    <i data-lucide="info" class="w-4 h-4 shrink-0 mt-0.5 text-amber-600"></i>
    <div>
        <strong class="font-bold">Política de devoluciones (Ley 2300/2023):</strong>
        Solo aplica para medicamentos de <strong>venta libre</strong>, en empaque sin abrir y dentro de los <strong>5 días hábiles</strong> posteriores a la compra.
        Los productos de <span class="font-bold text-red-700">control especial (CE)</span> <strong>no tienen devolución</strong>.
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Formulario principal -->
    <div class="lg:col-span-2">
        <form method="POST" action="<?= $basePath ?>/gerente/devoluciones/crear" id="formDevolucion"
              class="bg-white border border-fp-border rounded-2xl shadow-sm overflow-hidden">

            <div class="px-6 py-4 border-b border-fp-border bg-fp-bg-main">
                <h2 class="text-[14px] font-bold text-fp-text flex items-center gap-2">
                    <i data-lucide="file-plus" class="w-4 h-4 text-fp-primary"></i>
                    Datos de la devolución
                </h2>
            </div>

            <div class="p-6 flex flex-col gap-5">

                <!-- PASO 1: Tipo de origen -->
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-fp-muted mb-2">
                        Tipo de origen *
                    </label>
                    <div class="flex gap-3">
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="tipo_origen" value="pedido" class="sr-only peer"
                                   <?= $tipoOrigen === 'pedido' ? 'checked' : '' ?>>
                            <div class="flex items-center gap-2 h-10 px-4 border-2 rounded-xl text-[13px] font-semibold transition-all
                                        border-fp-border text-fp-muted
                                        peer-checked:border-fp-primary peer-checked:bg-fp-primary/5 peer-checked:text-fp-primary">
                                <i data-lucide="package" class="w-4 h-4"></i> Pedido online
                            </div>
                        </label>
                        <label class="flex-1 cursor-pointer">
                            <input type="radio" name="tipo_origen" value="venta" class="sr-only peer"
                                   <?= $tipoOrigen === 'venta' ? 'checked' : '' ?>>
                            <div class="flex items-center gap-2 h-10 px-4 border-2 rounded-xl text-[13px] font-semibold transition-all
                                        border-fp-border text-fp-muted
                                        peer-checked:border-fp-primary peer-checked:bg-fp-primary/5 peer-checked:text-fp-primary">
                                <i data-lucide="shopping-bag" class="w-4 h-4"></i> Venta presencial
                            </div>
                        </label>
                    </div>
                </div>

                <!-- PASO 2: Seleccionar pedido/venta -->
                <div id="seccionPedido" class="<?= $tipoOrigen !== 'pedido' ? 'hidden' : '' ?>">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-fp-muted mb-1.5">
                        Pedido entregado *
                    </label>
                    <select name="pedido_id" id="selectPedido"
                            class="w-full h-10 px-3 border border-fp-border rounded-xl text-[13px] text-fp-text focus:outline-none focus:border-fp-primary focus:ring-1 focus:ring-fp-primary/30">
                        <option value="">— Selecciona un pedido —</option>
                        <?php foreach ($pedidos as $ped): ?>
                            <option value="<?= $ped['pedido_id'] ?>" <?= $selPedido === (int)$ped['pedido_id'] ? 'selected' : '' ?>>
                                Pedido #<?= $ped['pedido_id'] ?> — <?= htmlspecialchars($ped['cliente_nombre']) ?>
                                (<?= (new DateTime($ped['created_at']))->format('d/m/Y') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <p class="mt-1 text-[11px] text-fp-muted">Solo se muestran pedidos con estado "Entregado".</p>
                </div>

                <div id="seccionVenta" class="<?= $tipoOrigen !== 'venta' ? 'hidden' : '' ?>">
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-fp-muted mb-1.5">
                        Venta presencial *
                    </label>
                    <select name="venta_id" id="selectVenta"
                            class="w-full h-10 px-3 border border-fp-border rounded-xl text-[13px] text-fp-text focus:outline-none focus:border-fp-primary focus:ring-1 focus:ring-fp-primary/30">
                        <option value="">— Selecciona una venta —</option>
                        <?php foreach ($ventas as $v): ?>
                            <option value="<?= $v['venta_id'] ?>" <?= $selVenta === (int)$v['venta_id'] ? 'selected' : '' ?>>
                                <?= htmlspecialchars($v['numero_comprobante']) ?> — <?= htmlspecialchars($v['vendedor_nombre']) ?>
                                (<?= (new DateTime($v['created_at']))->format('d/m/Y') ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Botón buscar productos (recarga la página con el ID seleccionado) -->
                <?php if (empty($productos) && ($selPedido || $selVenta)): ?>
                    <div class="text-[13px] text-amber-600 flex items-center gap-2">
                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                        No se encontraron productos para el origen seleccionado.
                    </div>
                <?php elseif (empty($productos)): ?>
                    <button type="button" id="btnBuscar"
                            class="self-start h-10 px-5 bg-fp-bg-main border border-fp-border rounded-xl text-[13px] font-semibold text-fp-text hover:bg-fp-primary hover:text-white hover:border-fp-primary transition-all">
                        <i data-lucide="search" class="w-4 h-4 inline-block mr-1"></i> Cargar productos
                    </button>
                <?php endif; ?>

                <!-- PASO 3: Seleccionar producto (solo si hay productos cargados) -->
                <?php if (!empty($productos)): ?>
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-fp-muted mb-1.5">
                        Producto a devolver *
                    </label>
                    <select name="producto_id" required
                            class="w-full h-10 px-3 border border-fp-border rounded-xl text-[13px] text-fp-text focus:outline-none focus:border-fp-primary focus:ring-1 focus:ring-fp-primary/30">
                        <option value="">— Selecciona el producto —</option>
                        <?php foreach ($productos as $prod): ?>
                            <option value="<?= $prod['producto_id'] ?>"
                                    data-control="<?= $prod['control_especial'] ? '1' : '0' ?>"
                                    data-max="<?= $prod['cantidad'] ?>">
                                <?= htmlspecialchars($prod['producto_nombre']) ?>
                                <?= $prod['control_especial'] ? ' ⚠ Control Especial (NO aplica devolución)' : '' ?>
                                — Comprado: <?= $prod['cantidad'] ?> uds.
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div id="avisoControlEspecial"
                         class="hidden mt-2 flex items-center gap-2 text-[12px] text-red-600 font-semibold bg-red-50 border border-red-100 rounded-lg px-3 py-2">
                        <i data-lucide="ban" class="w-4 h-4"></i>
                        Los medicamentos de control especial no tienen devolución (Ley 2300/2023).
                    </div>
                </div>

                <!-- Cantidad -->
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-fp-muted mb-1.5">
                        Cantidad a devolver *
                    </label>
                    <input type="number" name="cantidad" id="inputCantidad"
                           min="1" value="1" required
                           class="w-full h-10 px-3 border border-fp-border rounded-xl text-[13px] text-fp-text focus:outline-none focus:border-fp-primary focus:ring-1 focus:ring-fp-primary/30">
                </div>
                <?php endif; ?>

                <!-- Motivo -->
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-fp-muted mb-1.5">
                        Motivo de la devolución *
                    </label>
                    <select name="motivo" required
                            class="w-full h-10 px-3 border border-fp-border rounded-xl text-[13px] text-fp-text focus:outline-none focus:border-fp-primary focus:ring-1 focus:ring-fp-primary/30">
                        <option value="producto_danado">Producto dañado</option>
                        <option value="error_envio">Error en el envío (producto equivocado)</option>
                        <option value="cambio_opinion">Cambio de opinión del cliente</option>
                        <option value="vencido">Producto próximo a vencer / vencido</option>
                        <option value="otro">Otro</option>
                    </select>
                </div>

                <!-- Observación -->
                <div>
                    <label class="block text-[11px] font-bold uppercase tracking-wider text-fp-muted mb-1.5">
                        Observación adicional
                    </label>
                    <textarea name="observacion" rows="3" placeholder="Describe el estado del producto, la solicitud del cliente, etc."
                              class="w-full px-3 py-2.5 border border-fp-border rounded-xl text-[13px] text-fp-text focus:outline-none focus:border-fp-primary focus:ring-1 focus:ring-fp-primary/30 resize-none"></textarea>
                </div>

            </div>

            <!-- Botones -->
            <div class="px-6 py-4 border-t border-fp-border bg-fp-bg-main flex items-center justify-end gap-3">
                <a href="<?= $basePath ?>/gerente/devoluciones"
                   class="h-10 px-5 flex items-center justify-center border border-fp-border rounded-xl text-[13px] font-semibold text-fp-text hover:bg-fp-bg-card transition-colors">
                    Cancelar
                </a>
                <button type="submit"
                        class="h-10 px-6 bg-fp-primary hover:bg-fp-primary-light text-white text-[13px] font-bold rounded-xl transition-colors shadow-sm flex items-center justify-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i> Registrar solicitud
                </button>
            </div>
        </form>
    </div>

    <!-- Panel lateral de ayuda -->
    <div class="flex flex-col gap-4">
        <div class="bg-white border border-fp-border rounded-2xl p-5 shadow-sm">
            <h3 class="text-[13px] font-bold text-fp-text mb-3 flex items-center gap-2">
                <i data-lucide="help-circle" class="w-4 h-4 text-fp-primary"></i>
                ¿Cómo funciona?
            </h3>
            <ol class="flex flex-col gap-2.5 text-[12px] text-fp-muted">
                <li class="flex items-start gap-2">
                    <span class="w-5 h-5 bg-fp-primary/10 text-fp-primary text-[10px] font-bold rounded-full flex items-center justify-center shrink-0 mt-0.5">1</span>
                    Selecciona si la devolución viene de un <strong>pedido online</strong> o una <strong>venta presencial</strong>.
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-5 h-5 bg-fp-primary/10 text-fp-primary text-[10px] font-bold rounded-full flex items-center justify-center shrink-0 mt-0.5">2</span>
                    Carga los productos del origen seleccionado y escoge el que se devuelve.
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-5 h-5 bg-fp-primary/10 text-fp-primary text-[10px] font-bold rounded-full flex items-center justify-center shrink-0 mt-0.5">3</span>
                    La solicitud queda en estado <strong>Pendiente</strong> hasta que la apruebes o rechaces.
                </li>
                <li class="flex items-start gap-2">
                    <span class="w-5 h-5 bg-green-100 text-green-600 text-[10px] font-bold rounded-full flex items-center justify-center shrink-0 mt-0.5">✓</span>
                    Al <strong>aprobar</strong>, el inventario se actualiza automáticamente.
                </li>
            </ol>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-2xl p-5">
            <h3 class="text-[13px] font-bold text-amber-800 mb-2 flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-4 h-4"></i> Recordatorio legal
            </h3>
            <ul class="text-[12px] text-amber-700 flex flex-col gap-1.5">
                <li>• Máximo <strong>5 días hábiles</strong> desde la compra.</li>
                <li>• Empaque <strong>sin abrir</strong> y en buen estado.</li>
                <li>• Medicamentos <strong>CE</strong> no aplican para devolución.</li>
                <li>• Verificar comprobante de compra original.</li>
            </ul>
        </div>
    </div>
</div>

<script>
(function () {
    // Cambiar entre tipo de origen
    document.querySelectorAll('input[name="tipo_origen"]').forEach(radio => {
        radio.addEventListener('change', function () {
            document.getElementById('seccionPedido').classList.toggle('hidden', this.value !== 'pedido');
            document.getElementById('seccionVenta').classList.toggle('hidden', this.value !== 'venta');
        });
    });

    // Botón "Cargar productos" — redirige con el ID seleccionado
    const btnBuscar = document.getElementById('btnBuscar');
    if (btnBuscar) {
        btnBuscar.addEventListener('click', function () {
            const tipo     = document.querySelector('input[name="tipo_origen"]:checked')?.value ?? 'pedido';
            const pedidoId = document.getElementById('selectPedido')?.value ?? '';
            const ventaId  = document.getElementById('selectVenta')?.value  ?? '';
            const param    = tipo === 'pedido' ? `pedido_id=${pedidoId}` : `venta_id=${ventaId}`;
            window.location.href = `<?= $basePath ?>/gerente/devoluciones/crear?tipo=${tipo}&${param}`;
        });
    }

    // Advertencia de control especial
    const selectProducto = document.querySelector('select[name="producto_id"]');
    const aviso = document.getElementById('avisoControlEspecial');
    if (selectProducto && aviso) {
        selectProducto.addEventListener('change', function () {
            const opt = this.options[this.selectedIndex];
            const esControl = opt?.dataset?.control === '1';
            aviso.classList.toggle('hidden', !esControl);
            document.querySelector('button[type="submit"]').disabled = esControl;
        });

        // Limitar cantidad al máximo comprado
        selectProducto.addEventListener('change', function () {
            const max = parseInt(this.options[this.selectedIndex]?.dataset?.max ?? '999', 10);
            const input = document.getElementById('inputCantidad');
            if (input) { input.max = max; if (parseInt(input.value) > max) input.value = 1; }
        });
    }

    if (typeof lucide !== 'undefined') lucide.createIcons();
})();
</script>
