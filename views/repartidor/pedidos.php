<?php
/**
 * views/repartidor/pedidos.php — Panel del repartidor.
 * RF-7.2, RF-7.3: Ver y actualizar estado de sus pedidos asignados.
 */
$basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
ob_start();

$estadoColors = [
    'en_preparacion'   => 'bg-blue-50 text-blue-600 border-blue-200',
    'en_camino'        => 'bg-fp-warning/10 text-fp-warning border-fp-warning/20',
    'entregado'        => 'bg-fp-success/10 text-fp-success border-fp-success/20',
    'devuelto_fallido' => 'bg-fp-error/10 text-fp-error border-fp-error/20',
    'pendiente'        => 'bg-slate-100 text-slate-500 border-slate-200',
];

$estadoLabels = [
    'en_preparacion'   => 'Preparando',
    'en_camino'        => 'En camino 🚚',
    'entregado'        => 'Entregado ✅',
    'devuelto_fallido' => 'Dev. fallido',
    'pendiente'        => 'Pendiente',
];

$transiciones = [
    'en_preparacion' => [['value' => 'en_camino', 'label' => '🚚 Salir a entregar', 'color' => 'bg-fp-warning text-white']],
    'en_camino'      => [
        ['value' => 'entregado',        'label' => '✅ Marcar como entregado', 'color' => 'bg-fp-success text-white'],
        ['value' => 'devuelto_fallido', 'label' => '❌ No se pudo entregar',   'color' => 'bg-fp-error text-white'],
    ],
];
?>

<!-- Header Repartidor -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-fp-text tracking-tight flex items-center gap-2">
            <i data-lucide="truck" class="w-6 h-6 text-fp-primary"></i> Mis entregas
        </h1>
        <p class="text-[13px] text-fp-muted mt-0.5">
            Hola <?= htmlspecialchars($_SESSION['nombres'] ?? '') ?> — tienes <strong class="text-fp-primary"><?= count($pedidos) ?></strong> entrega<?= count($pedidos) !== 1 ? 's' : '' ?> activa<?= count($pedidos) !== 1 ? 's' : '' ?>
        </p>
    </div>
</div>

<?php if (empty($pedidos)): ?>
<!-- Sin pedidos -->
<div class="flex flex-col items-center justify-center py-20 bg-white rounded-2xl border border-fp-border">
    <div class="w-20 h-20 rounded-full bg-fp-success/10 flex items-center justify-center mb-4">
        <i data-lucide="check-circle" class="w-10 h-10 text-fp-success opacity-80"></i>
    </div>
    <h2 class="text-[18px] font-bold text-fp-text mb-2">¡Todo al día!</h2>
    <p class="text-[14px] text-fp-muted max-w-xs text-center">No tienes entregas pendientes en este momento. El coordinador te asignará nuevos pedidos.</p>
</div>

<?php else: ?>
<!-- Grid de Tarjetas de Pedido -->
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <?php foreach ($pedidos as $p):
        $estadoCls   = $estadoColors[$p['estado']] ?? 'bg-slate-100 text-slate-500';
        $estadoLabel = $estadoLabels[$p['estado']] ?? $p['estado'];
        $acciones    = $transiciones[$p['estado']] ?? [];
        $direccion   = implode(', ', array_filter([$p['direccion'] ?? '', $p['barrio'] ?? '', $p['ciudad'] ?? '']));
    ?>
    <div class="bg-white rounded-2xl border border-fp-border shadow-sm overflow-hidden flex flex-col"
         id="card-<?= $p['pedido_id'] ?>">

        <!-- Header de la tarjeta -->
        <div class="p-4 border-b border-fp-border/50 flex items-start justify-between gap-2 bg-slate-50/50">
            <div>
                <span class="font-mono font-black text-[15px] text-fp-primary">#<?= str_pad((string)$p['pedido_id'], 6, '0', STR_PAD_LEFT) ?></span>
                <div class="text-[11px] text-fp-muted mt-0.5"><?= date('d/m/Y H:i', strtotime($p['created_at'])) ?></div>
            </div>
            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border text-[11px] font-bold <?= $estadoCls ?>">
                <?= $estadoLabel ?>
            </span>
        </div>

        <!-- Cuerpo -->
        <div class="p-4 flex-1 flex flex-col gap-3">

            <!-- Cliente -->
            <div class="flex items-start gap-2">
                <i data-lucide="user" class="w-4 h-4 text-fp-muted mt-0.5 shrink-0"></i>
                <div>
                    <div class="font-bold text-[13px] text-fp-text"><?= htmlspecialchars($p['cliente_nombre'] ?? '—') ?></div>
                    <?php if (!empty($p['cliente_telefono'])): ?>
                    <a href="tel:<?= htmlspecialchars($p['cliente_telefono']) ?>" class="text-[12px] text-fp-primary hover:underline font-semibold flex items-center gap-1 mt-0.5">
                        <i data-lucide="phone" class="w-3 h-3"></i> <?= htmlspecialchars($p['cliente_telefono']) ?>
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Dirección -->
            <div class="flex items-start gap-2">
                <i data-lucide="map-pin" class="w-4 h-4 text-fp-error mt-0.5 shrink-0"></i>
                <div>
                    <div class="text-[13px] font-semibold text-fp-text leading-snug"><?= htmlspecialchars($direccion ?: 'Sin dirección') ?></div>
                    <?php if (!empty($p['referencia'])): ?>
                    <div class="text-[11px] text-fp-muted italic mt-0.5"><?= htmlspecialchars($p['referencia']) ?></div>
                    <?php endif; ?>
                    <!-- Link Maps -->
                    <?php if (!empty($direccion)): ?>
                    <a href="https://maps.google.com/?q=<?= urlencode($direccion) ?>" target="_blank"
                       class="text-[11px] text-fp-primary hover:underline flex items-center gap-1 mt-1 font-semibold">
                        <i data-lucide="navigation" class="w-3 h-3"></i> Abrir en Google Maps
                    </a>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Total -->
            <div class="flex items-center justify-between bg-fp-bg-main rounded-lg px-3 py-2">
                <span class="text-[12px] text-fp-muted font-semibold">Total pedido</span>
                <span class="font-black text-[14px] text-fp-primary">$<?= number_format((float)$p['total'], 0, ',', '.') ?></span>
            </div>

            <!-- Acciones de transición de estado -->
            <?php if (!empty($acciones)): ?>
            <div class="flex flex-col gap-2 mt-2">
                <?php foreach ($acciones as $accion): ?>
                <button
                    type="button"
                    onclick="actualizarEstado(<?= $p['pedido_id'] ?>, '<?= $accion['value'] ?>')"
                    class="w-full h-10 flex items-center justify-center gap-2 <?= $accion['color'] ?> text-[13px] font-bold rounded-xl transition-all active:scale-95 shadow-sm"
                    data-pedido="<?= $p['pedido_id'] ?>"
                    data-estado="<?= $accion['value'] ?>"
                >
                    <?= $accion['label'] ?>
                </button>
                <?php endforeach; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-2 text-[12px] text-fp-muted italic">Sin acciones disponibles</div>
            <?php endif; ?>
        </div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<!-- Modal Observación Devolución -->
<div id="modalDevolucion" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="cerrarDevolucion()"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md z-10">
        <div class="p-6 border-b border-slate-100">
            <h3 class="font-bold text-[17px] text-slate-800 flex items-center gap-2">
                <i data-lucide="alert-triangle" class="w-5 h-5 text-fp-error"></i> Registrar devolución
            </h3>
            <p class="text-[13px] text-slate-500 mt-1">Ingresa el motivo por el cual no se pudo entregar el pedido.</p>
        </div>
        <div class="p-6">
            <textarea id="observacionDevolucion" rows="4" placeholder="Ej: El cliente no estaba en casa, dirección incorrecta..."
                      class="w-full px-3 py-2.5 border border-slate-200 rounded-xl text-[13px] outline-none focus:border-fp-error focus:ring-2 focus:ring-fp-error/20 resize-none mb-4"
                      required></textarea>
            <div class="flex gap-3">
                <button onclick="cerrarDevolucion()" class="flex-1 h-10 border-2 border-slate-200 text-slate-600 font-bold text-[13px] rounded-xl">Cancelar</button>
                <button onclick="confirmarDevolucion()" class="flex-1 h-10 bg-fp-error text-white font-bold text-[13px] rounded-xl hover:bg-fp-error/90 shadow-sm">
                    <span id="btnDevText">Confirmar devolución</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const BP = '<?= $basePath ?>';
let pendingPedidoId = null;
let pendingEstado   = null;

async function actualizarEstado(pedidoId, estado) {
    pendingPedidoId = pedidoId;
    pendingEstado   = estado;

    if (estado === 'devuelto_fallido') {
        document.getElementById('observacionDevolucion').value = '';
        document.getElementById('modalDevolucion').classList.remove('hidden');
        return;
    }

    await enviarActualizacion(pedidoId, estado, '');
}

function cerrarDevolucion() {
    document.getElementById('modalDevolucion').classList.add('hidden');
    pendingPedidoId = null;
    pendingEstado   = null;
}

async function confirmarDevolucion() {
    const obs = document.getElementById('observacionDevolucion').value.trim();
    if (!obs) { alert('Ingresa el motivo de la devolución.'); return; }
    document.getElementById('btnDevText').textContent = 'Procesando...';
    await enviarActualizacion(pendingPedidoId, 'devuelto_fallido', obs);
    cerrarDevolucion();
}

async function enviarActualizacion(pedidoId, estado, observacion) {
    try {
        const body = `estado=${encodeURIComponent(estado)}&observacion=${encodeURIComponent(observacion)}`;
        const res  = await fetch(`${BP}/repartidor/pedidos/${pedidoId}/actualizar-estado`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body
        });
        const data = await res.json();

        if (data.success) {
            // Animación de éxito y recarga
            const card = document.getElementById('card-' + pedidoId);
            if (card) {
                card.style.opacity = '0';
                card.style.transform = 'translateY(-8px)';
                card.style.transition = 'all .3s';
            }
            setTimeout(() => window.location.reload(), 400);
        } else {
            alert(data.error || 'Error al actualizar el estado.');
        }
    } catch(e) {
        alert('Error de conexión. Inténtalo de nuevo.');
    }
}
</script>

<?php
$contenido = ob_get_clean();

// Layout especial repartidor — hereda base.php
$titulo = 'Mis Entregas';
require __DIR__ . '/../layouts/base.php';
?>
