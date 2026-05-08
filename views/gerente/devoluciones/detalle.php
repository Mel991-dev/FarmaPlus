<?php
/**
 * views/gerente/devoluciones/detalle.php
 * Vista de detalle de una devolución con acciones de Aprobar / Rechazar.
 *
 * Variables esperadas:
 *   $devolucion  array   — resultado de DevolucionModel::obtenerPorId()
 *   $success     string  — mensaje de éxito de sesión
 *   $error       string  — mensaje de error de sesión
 */
$basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
$d = $devolucion;

$motivoLabel = [
    'producto_danado' => 'Producto dañado',
    'error_envio' => 'Error de envío',
    'cambio_opinion' => 'Cambio de opinión',
    'vencido' => 'Producto vencido',
    'otro' => 'Otro',
];

$estadoConfig = [
    'pendiente' => ['clases' => 'bg-amber-50 text-amber-700 border-amber-200', 'icono' => 'clock', 'label' => 'Pendiente de revisión'],
    'aprobada' => ['clases' => 'bg-green-50 text-green-700 border-green-200', 'icono' => 'check-circle', 'label' => 'Aprobada'],
    'rechazada' => ['clases' => 'bg-red-50   text-red-700   border-red-200', 'icono' => 'x-circle', 'label' => 'Rechazada'],
];
$ec = $estadoConfig[$d['estado']] ?? $estadoConfig['pendiente'];

$origenLabel = $d['tipo_origen'] === 'pedido'
    ? 'Pedido #' . ($d['ped_numero'] ?? '?')
    : 'Venta ' . ($d['venta_numero'] ?? '?');
$personaLabel = $d['tipo_origen'] === 'pedido'
    ? ($d['cliente_nombre'] ?? '—')
    : ($d['vendedor_nombre'] ?? '—');
?>

<!-- Cabecera -->
<div class="flex items-center gap-3 mb-6">
    <a href="<?= $basePath ?>/gerente/devoluciones"
        class="w-9 h-9 flex items-center justify-center rounded-xl bg-white border border-fp-border text-fp-muted hover:text-fp-primary hover:border-fp-primary transition-all">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
    </a>
    <div class="flex-1">
        <h1 class="text-xl font-bold text-fp-text">Devolución #<?= $d['devolucion_id'] ?></h1>
        <p class="text-[13px] text-fp-muted">
            Solicitada el <?= (new DateTime($d['created_at']))->format('d/m/Y \a \l\a\s H:i') ?>
        </p>
    </div>
    <!-- Badge de estado -->
    <span class="inline-flex items-center gap-2 px-4 py-2 rounded-xl border text-[13px] font-bold <?= $ec['clases'] ?>">
        <i data-lucide="<?= $ec['icono'] ?>" class="w-4 h-4"></i>
        <?= $ec['label'] ?>
    </span>
</div>

<!-- Alertas de sesión -->
<?php if ($success): ?>
    <div
        class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-[13px] font-semibold">
        <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
        <?= htmlspecialchars($success) ?>
    </div>
<?php endif; ?>
<?php if ($error): ?>
    <div
        class="mb-5 flex items-center gap-3 bg-red-50 border border-red-200 text-red-700 rounded-xl px-4 py-3 text-[13px] font-semibold">
        <i data-lucide="alert-circle" class="w-4 h-4 shrink-0"></i>
        <?= htmlspecialchars($error) ?>
    </div>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- Columna principal: detalles -->
    <div class="lg:col-span-2 flex flex-col gap-5">

        <!-- Tarjeta: Información del producto devuelto -->
        <div class="bg-white border border-fp-border rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-fp-border bg-fp-bg-main flex items-center gap-2">
                <i data-lucide="package" class="w-4 h-4 text-fp-primary"></i>
                <h2 class="text-[14px] font-bold text-fp-text">Producto devuelto</h2>
            </div>
            <div class="p-5 grid grid-cols-2 gap-x-6 gap-y-4 text-[13px]">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-fp-muted mb-0.5">Producto</p>
                    <p class="font-bold text-fp-text">
                        <?= htmlspecialchars($d['producto_nombre']) ?>
                        <?php if ($d['control_especial']): ?>
                            <span
                                class="ml-1 inline-block px-1.5 py-0.5 rounded text-[9px] font-bold bg-red-50 text-red-600 border border-red-100">CE</span>
                        <?php endif; ?>
                    </p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-fp-muted mb-0.5">Cantidad</p>
                    <p class="font-bold text-fp-text font-mono text-lg"><?= $d['cantidad'] ?> <span
                            class="text-[12px] text-fp-muted font-normal">unidades</span></p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-fp-muted mb-0.5">Motivo</p>
                    <p class="font-semibold text-fp-text"><?= $motivoLabel[$d['motivo']] ?? $d['motivo'] ?></p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-fp-muted mb-0.5">Tipo de origen
                    </p>
                    <span
                        class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-fp-primary/10 text-fp-primary text-[11px] font-bold uppercase tracking-wider">
                        <i data-lucide="<?= $d['tipo_origen'] === 'pedido' ? 'package' : 'shopping-bag' ?>"
                            class="w-3 h-3"></i>
                        <?= $d['tipo_origen'] === 'pedido' ? 'Pedido online' : 'Venta presencial' ?>
                    </span>
                </div>
                <?php if (!empty($d['observacion'])): ?>
                    <div class="col-span-2">
                        <p class="text-[11px] font-semibold uppercase tracking-wider text-fp-muted mb-0.5">Observación</p>
                        <p class="text-fp-text bg-fp-bg-main rounded-xl px-3 py-2 text-[13px]">
                            <?= nl2br(htmlspecialchars($d['observacion'])) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tarjeta: Origen de la transacción -->
        <div class="bg-white border border-fp-border rounded-2xl shadow-sm overflow-hidden">
            <div class="px-5 py-4 border-b border-fp-border bg-fp-bg-main flex items-center gap-2">
                <i data-lucide="link" class="w-4 h-4 text-fp-primary"></i>
                <h2 class="text-[14px] font-bold text-fp-text">Transacción de origen</h2>
            </div>
            <div class="p-5 grid grid-cols-2 gap-x-6 gap-y-4 text-[13px]">
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-fp-muted mb-0.5">Referencia</p>
                    <p class="font-bold text-fp-text"><?= htmlspecialchars($origenLabel) ?></p>
                </div>
                <div>
                    <p class="text-[11px] font-semibold uppercase tracking-wider text-fp-muted mb-0.5">
                        <?= $d['tipo_origen'] === 'pedido' ? 'Cliente' : 'Vendedor' ?>
                    </p>
                    <p class="font-semibold text-fp-text"><?= htmlspecialchars($personaLabel) ?></p>
                </div>
            </div>
        </div>

        <!-- Resultado de la gestión (solo si ya fue procesada) -->
        <?php if ($d['estado'] === 'aprobada'): ?>
            <div class="bg-green-50 border border-green-200 rounded-2xl p-5">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-green-100 rounded-xl flex items-center justify-center shrink-0">
                        <i data-lucide="check-circle" class="w-5 h-5 text-green-600"></i>
                    </div>
                    <div>
                        <h3 class="text-[14px] font-bold text-green-800 mb-1">Devolución aprobada</h3>
                        <p class="text-[13px] text-green-700">
                            El inventario ha sido actualizado: se sumaron <strong><?= $d['cantidad'] ?> unidades</strong> de
                            <strong><?= htmlspecialchars($d['producto_nombre']) ?></strong> al stock activo.
                        </p>
                        <?php if ($d['gerente_nombre']): ?>
                            <p class="text-[12px] text-green-600 mt-1.5">
                                Gestionado por: <strong><?= htmlspecialchars($d['gerente_nombre']) ?></strong> ·
                                <?= (new DateTime($d['updated_at']))->format('d/m/Y H:i') ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php elseif ($d['estado'] === 'rechazada'): ?>
            <div class="bg-red-50 border border-red-200 rounded-2xl p-5">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 bg-red-100 rounded-xl flex items-center justify-center shrink-0">
                        <i data-lucide="x-circle" class="w-5 h-5 text-red-500"></i>
                    </div>
                    <div>
                        <h3 class="text-[14px] font-bold text-red-800 mb-1">Devolución rechazada</h3>
                        <p class="text-[13px] text-red-700 bg-white/60 rounded-lg px-3 py-2 border border-red-100">
                            <?= nl2br(htmlspecialchars($d['razon_rechazo'] ?? 'Sin razón documentada.')) ?>
                        </p>
                        <?php if ($d['gerente_nombre']): ?>
                            <p class="text-[12px] text-red-500 mt-1.5">
                                Gestionado por: <strong><?= htmlspecialchars($d['gerente_nombre']) ?></strong> ·
                                <?= (new DateTime($d['updated_at']))->format('d/m/Y H:i') ?>
                            </p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>

    </div>

    <!-- Columna lateral: acciones del gerente -->
    <div class="flex flex-col gap-5">

        <!-- Panel de acción (solo si está pendiente) -->
        <?php if ($d['estado'] === 'pendiente'): ?>

            <!-- Aprobar -->
            <div class="bg-white border border-fp-border rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-fp-border bg-green-50 flex items-center gap-2">
                    <i data-lucide="check-circle" class="w-4 h-4 text-green-600"></i>
                    <h2 class="text-[14px] font-bold text-green-800">Aprobar devolución</h2>
                </div>
                <div class="p-5">
                    <p class="text-[13px] text-fp-muted mb-4">
                        Al aprobar, el inventario se actualiza sumando
                        <strong class="text-fp-text"><?= $d['cantidad'] ?> unidad(es)</strong> al stock de
                        <strong class="text-fp-text"><?= htmlspecialchars($d['producto_nombre']) ?></strong>.
                    </p>
                    <form method="POST" action="<?= $basePath ?>/gerente/devoluciones/<?= $d['devolucion_id'] ?>/aprobar"
                        onsubmit="return confirm('¿Confirmas que deseas APROBAR esta devolución?\n\nEl inventario será actualizado automáticamente.')">
                        <button type="submit" style="background-color: #16a34a;"
                            class="w-full h-11 flex items-center justify-center gap-2 text-white text-[14px] font-bold rounded-xl transition-colors shadow-sm hover:opacity-90">
                            <i data-lucide="check-circle" class="w-5 h-5"></i>
                            Aprobar y actualizar inventario
                        </button>
                    </form>
                </div>
            </div>

            <!-- Rechazar -->
            <div class="bg-white border border-fp-border rounded-2xl shadow-sm overflow-hidden">
                <div class="px-5 py-4 border-b border-fp-border bg-red-50 flex items-center gap-2">
                    <i data-lucide="x-circle" class="w-4 h-4 text-red-500"></i>
                    <h2 class="text-[14px] font-bold text-red-700">Rechazar devolución</h2>
                </div>
                <div class="p-5">
                    <form method="POST" action="<?= $basePath ?>/gerente/devoluciones/<?= $d['devolucion_id'] ?>/rechazar"
                        onsubmit="return validarRechazo(this)">
                        <div class="mb-4">
                            <label class="block text-[11px] font-bold uppercase tracking-wider text-fp-muted mb-1.5">
                                Razón del rechazo *
                            </label>
                            <textarea name="razon_rechazo" rows="4" required
                                placeholder="Ej: El empaque fue abierto. El plazo de 5 días hábiles venció. El producto es de control especial..."
                                class="w-full px-3 py-2.5 border border-fp-border rounded-xl text-[13px] text-fp-text focus:outline-none focus:border-red-400 focus:ring-1 focus:ring-red-200 resize-none"></textarea>
                            <p class="mt-1 text-[11px] text-fp-muted">Esta razón quedará documentada en el sistema.</p>
                        </div>
                        <button type="submit"
                            class="w-full h-11 flex items-center justify-center gap-2 bg-red-500 hover:bg-red-600 text-white text-[14px] font-bold rounded-xl transition-colors shadow-sm">
                            <i data-lucide="x-circle" class="w-5 h-5"></i>
                            Rechazar y documentar
                        </button>
                    </form>
                </div>
            </div>

        <?php else: ?>
            <!-- Ya procesada -->
            <div class="bg-fp-bg-main border border-fp-border rounded-2xl p-5 text-center">
                <i data-lucide="lock" class="w-8 h-8 text-fp-border mx-auto mb-2"></i>
                <p class="text-[13px] font-semibold text-fp-text">Devolución cerrada</p>
                <p class="text-[12px] text-fp-muted mt-1">Esta solicitud ya fue
                    <?= $d['estado'] === 'aprobada' ? 'aprobada' : 'rechazada' ?> y no puede modificarse.</p>
            </div>
        <?php endif; ?>

        <!-- Resumen rápido -->
        <div class="bg-white border border-fp-border rounded-2xl p-5 shadow-sm">
            <h3 class="text-[12px] font-bold uppercase tracking-wider text-fp-muted mb-3">Resumen</h3>
            <dl class="flex flex-col gap-2 text-[13px]">
                <div class="flex items-center justify-between">
                    <dt class="text-fp-muted">ID</dt>
                    <dd class="font-mono font-bold text-fp-text">#<?= $d['devolucion_id'] ?></dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-fp-muted">Tipo</dt>
                    <dd class="font-semibold text-fp-text capitalize"><?= $d['tipo_origen'] ?></dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-fp-muted">Cantidad</dt>
                    <dd class="font-bold text-fp-text"><?= $d['cantidad'] ?> uds.</dd>
                </div>
                <div class="flex items-center justify-between">
                    <dt class="text-fp-muted">Creada</dt>
                    <dd class="text-fp-text"><?= (new DateTime($d['created_at']))->format('d/m/Y') ?></dd>
                </div>
                <?php if ($d['updated_at'] !== $d['created_at']): ?>
                    <div class="flex items-center justify-between">
                        <dt class="text-fp-muted">Actualizada</dt>
                        <dd class="text-fp-text"><?= (new DateTime($d['updated_at']))->format('d/m/Y H:i') ?></dd>
                    </div>
                <?php endif; ?>
            </dl>
        </div>
    </div>
</div>

<script>
    function validarRechazo(form) {
        const razon = form.razon_rechazo.value.trim();
        if (!razon) {
            alert('Debes escribir la razón del rechazo para documentarla correctamente.');
            return false;
        }
        return confirm('¿Confirmas que deseas RECHAZAR esta devolución?\n\nLa razón quedará documentada en el sistema.');
    }
    if (typeof lucide !== 'undefined') lucide.createIcons();
</script>