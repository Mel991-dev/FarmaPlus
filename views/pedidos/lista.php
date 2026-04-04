<?php
/**
 * views/pedidos/lista.php — Lista de pedidos en línea para Admin/Gerente.
 * RF-7.1: Gestión completa de pedidos con asignación de repartidor.
 */
$titulo = 'Pedidos en Línea';
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
    'pendiente'        => 'Pendiente',
    'pagado'           => 'Pagado',
    'en_preparacion'   => 'En preparación',
    'en_camino'        => 'En camino',
    'entregado'        => 'Entregado',
    'devuelto_fallido' => 'Devuelto/Fallido',
    'cancelado'        => 'Cancelado',
];
?>

<?php if (!empty($_GET['success'])): ?>
<div class="mb-6 bg-fp-success/10 border-l-4 border-fp-success p-3 rounded-lg flex items-center gap-2 text-sm font-semibold text-fp-success shadow-sm">
    <i data-lucide="circle-check" class="w-5 h-5"></i> <?= htmlspecialchars($_GET['success']) ?>
</div>
<?php elseif (!empty($_GET['error'])): ?>
<div class="mb-6 bg-fp-error/10 border-l-4 border-fp-error p-3 rounded-lg flex items-center gap-2 text-sm font-semibold text-fp-error shadow-sm">
    <i data-lucide="alert-circle" class="w-5 h-5"></i> <?= htmlspecialchars($_GET['error']) ?>
</div>
<?php endif; ?>

<!-- Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-fp-text tracking-tight flex items-center gap-2">
            <i data-lucide="package" class="w-6 h-6 text-fp-primary"></i> Pedidos en Línea
        </h1>
        <p class="text-[13px] text-fp-muted mt-0.5">Gestión de pedidos del canal e-commerce</p>
    </div>
</div>

<!-- Stats rápidas -->
<?php
$stats = ['total' => 0, 'pendiente' => 0, 'pagado' => 0, 'en_preparacion' => 0, 'en_camino' => 0];
foreach ($pedidos as $p) {
    $stats['total']++;
    if (isset($stats[$p['estado']])) $stats[$p['estado']]++;
}
?>
<div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
    <?php $statDef = [
        ['label' => 'Pendientes pago', 'key' => 'pendiente',   'color' => 'border-l-slate-400',   'icon' => 'clock', 'textColor' => 'text-slate-600'],
        ['label' => 'Pago confirmado', 'key' => 'pagado',      'color' => 'border-l-fp-success',  'icon' => 'check-circle', 'textColor' => 'text-fp-success'],
        ['label' => 'En preparación',  'key' => 'en_preparacion','color' => 'border-l-blue-400', 'icon' => 'box',  'textColor' => 'text-blue-600'],
        ['label' => 'En camino',       'key' => 'en_camino',   'color' => 'border-l-fp-warning',  'icon' => 'truck', 'textColor' => 'text-fp-warning'],
    ];
    foreach ($statDef as $s): ?>
    <div class="bg-white rounded-xl border border-fp-border p-4 flex items-center gap-3 shadow-sm relative overflow-hidden">
        <div class="absolute left-0 top-0 bottom-0 w-1 <?= $s['color'] ?>"></div>
        <div class="w-10 h-10 rounded-xl bg-slate-50 flex items-center justify-center shrink-0">
            <i data-lucide="<?= $s['icon'] ?>" class="w-5 h-5 <?= $s['textColor'] ?>"></i>
        </div>
        <div>
            <div class="text-xl font-black <?= $s['textColor'] ?> font-mono leading-none"><?= $stats[$s['key']] ?></div>
            <div class="text-[10px] font-bold text-fp-muted uppercase tracking-wide mt-0.5"><?= $s['label'] ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Filtros -->
<div class="flex flex-wrap items-center gap-3 mb-5 bg-white p-3 rounded-xl border border-fp-border shadow-sm">
    <form method="GET" action="" class="flex flex-wrap items-center gap-3 w-full">
        <select name="estado" onchange="this.form.submit()"
                class="h-9 px-3 pr-8 bg-fp-bg-main border border-fp-border rounded-lg text-[13px] font-medium text-fp-text outline-none focus:border-fp-primary cursor-pointer appearance-none">
            <option value="">Todos los estados</option>
            <?php foreach ($estadoLabels as $val => $lbl): ?>
            <option value="<?= $val ?>" <?= ($_GET['estado'] ?? '') === $val ? 'selected' : '' ?>><?= $lbl ?></option>
            <?php endforeach; ?>
        </select>
        <?php if (!empty($_GET['estado'])): ?>
        <a href="<?= rtrim($_ENV['APP_BASEPATH'] ?? '', '/') ?>/pedidos" class="text-[12px] text-fp-error font-semibold flex items-center gap-1 hover:underline">
            <i data-lucide="x" class="w-3 h-3"></i> Limpiar
        </a>
        <?php endif; ?>
        <span class="ml-auto text-[12px] text-fp-muted font-medium"><?= $total ?> pedido<?= $total !== 1 ? 's' : '' ?></span>
    </form>
</div>

<!-- Tabla -->
<div class="bg-white rounded-xl border border-fp-border shadow-sm overflow-hidden">
    <?php if (empty($pedidos)): ?>
    <div class="flex flex-col items-center justify-center p-12">
        <div class="w-16 h-16 rounded-full bg-fp-bg-main flex items-center justify-center mb-3">
            <i data-lucide="package" class="w-8 h-8 text-fp-muted opacity-80"></i>
        </div>
        <p class="text-[15px] font-bold text-fp-text mb-1">Sin pedidos</p>
        <span class="text-[13px] text-fp-muted">Aún no hay pedidos para mostrar con estos filtros.</span>
    </div>
    <?php else: ?>
    <div class="w-full overflow-x-auto">
        <table class="w-full text-left border-collapse min-w-[900px]">
            <thead>
                <tr class="bg-white border-b border-fp-border text-[11px] uppercase tracking-[1.5px] font-bold text-fp-muted">
                    <th class="px-5 py-4">Pedido</th>
                    <th class="px-5 py-4">Cliente</th>
                    <th class="px-5 py-4">Fecha</th>
                    <th class="px-5 py-4">Estado</th>
                    <th class="px-5 py-4">Repartidor</th>
                    <th class="px-5 py-4 text-right">Total</th>
                    <th class="px-5 py-4 text-center">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-fp-border/50">
                <?php foreach ($pedidos as $p): ?>
                <?php $estadoCls = $estadoColors[$p['estado']] ?? 'bg-slate-100 text-slate-500'; ?>
                <tr class="hover:bg-fp-bg-main/50 transition-colors group">
                    <td class="px-5 py-4">
                        <span class="font-mono font-bold text-[14px] text-fp-primary">#<?= str_pad((string)$p['pedido_id'], 6, '0', STR_PAD_LEFT) ?></span>
                    </td>
                    <td class="px-5 py-4">
                        <div class="font-semibold text-[13px] text-fp-text"><?= htmlspecialchars($p['cliente_nombre'] ?? '—') ?></div>
                        <div class="text-[11px] text-fp-muted"><?= htmlspecialchars($p['cliente_correo'] ?? '') ?></div>
                    </td>
                    <td class="px-5 py-4 text-[12px] text-fp-muted font-medium">
                        <?= date('d/m/Y', strtotime($p['created_at'])) ?><br>
                        <span class="text-[10px]"><?= date('H:i', strtotime($p['created_at'])) ?></span>
                    </td>
                    <td class="px-5 py-4">
                        <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border text-[11px] font-bold <?= $estadoCls ?>">
                            <?= $estadoLabels[$p['estado']] ?? $p['estado'] ?>
                        </span>
                    </td>
                    <td class="px-5 py-4">
                        <?php if ($p['repartidor_nombre']): ?>
                        <div class="flex items-center gap-1.5 text-[12px] text-fp-text font-medium">
                            <i data-lucide="user" class="w-3.5 h-3.5 text-fp-muted"></i>
                            <?= htmlspecialchars($p['repartidor_nombre']) ?>
                        </div>
                        <?php elseif (in_array($p['estado'], ['pagado', 'pendiente'])): ?>
                        <button
                            onclick="abrirModalAsignar(<?= $p['pedido_id'] ?>)"
                            class="text-[11px] font-bold text-fp-primary hover:underline flex items-center gap-1"
                        >
                            <i data-lucide="user-plus" class="w-3.5 h-3.5"></i> Asignar
                        </button>
                        <?php else: ?>
                        <span class="text-[12px] text-fp-muted">—</span>
                        <?php endif; ?>
                    </td>
                    <td class="px-5 py-4 text-right font-bold font-mono text-[14px] text-fp-text">
                        $<?= number_format((float)$p['total'], 0, ',', '.') ?>
                    </td>
                    <td class="px-5 py-4">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="<?= rtrim($_ENV['APP_BASEPATH'] ?? '', '/') ?>/pedidos/<?= $p['pedido_id'] ?>"
                               class="w-8 h-8 rounded-lg bg-fp-bg-main border border-fp-border text-fp-text hover:bg-fp-primary hover:text-white transition-colors flex items-center justify-center"
                               title="Ver detalle">
                                <i data-lucide="eye" class="w-4 h-4"></i>
                            </a>
                            <?php if (in_array($p['estado'], ['pendiente', 'pagado'])): ?>
                            <form method="POST" action="<?= rtrim($_ENV['APP_BASEPATH'] ?? '', '/') ?>/pedidos/<?= $p['pedido_id'] ?>/cancelar" class="m-0">
                                <button type="submit"
                                        onclick="return confirm('¿Cancelar este pedido?')"
                                        class="w-8 h-8 rounded-lg bg-fp-bg-main border border-fp-border text-fp-error hover:bg-fp-error hover:text-white transition-colors flex items-center justify-center"
                                        title="Cancelar pedido">
                                    <i data-lucide="x" class="w-4 h-4"></i>
                                </button>
                            </form>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Paginación -->
    <?php if ($paginas > 1): ?>
    <div class="px-5 py-4 border-t border-fp-border flex items-center justify-between gap-4 flex-wrap">
        <span class="text-[12px] text-fp-muted font-medium">Página <?= $pagina ?> de <?= $paginas ?></span>
        <div class="flex items-center gap-2">
            <?php if ($pagina > 1): ?>
            <a href="?pagina=<?= $pagina - 1 ?><?= !empty($_GET['estado']) ? '&estado=' . $_GET['estado'] : '' ?>"
               class="h-8 px-3 flex items-center text-[12px] font-bold text-fp-text border border-fp-border rounded-lg hover:border-fp-primary hover:text-fp-primary transition-colors">
                <i data-lucide="chevron-left" class="w-4 h-4"></i>
            </a>
            <?php endif; ?>
            <?php for ($i = max(1,$pagina-2); $i <= min($paginas,$pagina+2); $i++): ?>
            <a href="?pagina=<?= $i ?><?= !empty($_GET['estado']) ? '&estado=' . $_GET['estado'] : '' ?>"
               class="w-8 h-8 flex items-center justify-center text-[12px] font-bold rounded-lg transition-colors <?= $i === $pagina ? 'bg-fp-primary text-white' : 'border border-fp-border text-fp-text hover:border-fp-primary hover:text-fp-primary' ?>">
                <?= $i ?>
            </a>
            <?php endfor; ?>
            <?php if ($pagina < $paginas): ?>
            <a href="?pagina=<?= $pagina + 1 ?><?= !empty($_GET['estado']) ? '&estado=' . $_GET['estado'] : '' ?>"
               class="h-8 px-3 flex items-center text-[12px] font-bold text-fp-text border border-fp-border rounded-lg hover:border-fp-primary hover:text-fp-primary transition-colors">
                <i data-lucide="chevron-right" class="w-4 h-4"></i>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php endif; ?>
</div>

<!-- Modal Asignar Repartidor -->
<div id="modalAsignar" class="fixed inset-0 z-50 flex items-center justify-center p-4 hidden">
    <div class="absolute inset-0 bg-black/50 backdrop-blur-sm" onclick="cerrarModal()"></div>
    <div class="relative bg-white rounded-2xl shadow-xl w-full max-w-md z-10 overflow-hidden">
        <div class="p-6 border-b border-slate-100">
            <h3 class="font-bold text-[17px] text-slate-800 flex items-center gap-2">
                <i data-lucide="user-plus" class="w-5 h-5 text-fp-primary"></i> Asignar repartidor
            </h3>
            <p class="text-[13px] text-slate-500 mt-1">Selecciona el repartidor para el <strong id="modalPedidoLabel">Pedido</strong></p>
        </div>
        <div class="p-6">
            <select id="modalRepartidorSelect"
                    class="w-full h-10 px-3 border border-slate-200 rounded-xl text-[14px] outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20 mb-4">
                <option value="">— Selecciona un repartidor —</option>
                <?php foreach ($repartidores as $r): ?>
                <option value="<?= $r['usuario_id'] ?>"><?= htmlspecialchars($r['nombre']) ?></option>
                <?php endforeach; ?>
            </select>
            <div class="flex gap-3">
                <button onclick="cerrarModal()"
                        class="flex-1 h-10 border-2 border-slate-200 hover:border-slate-300 text-slate-600 font-bold text-[13px] rounded-xl transition-colors">
                    Cancelar
                </button>
                <button onclick="confirmarAsignacion()"
                        class="flex-1 h-10 bg-fp-primary hover:bg-fp-primary-dark text-white font-bold text-[13px] rounded-xl transition-colors shadow-sm">
                    <span id="btnAsignarText">Asignar</span>
                </button>
            </div>
        </div>
    </div>
</div>

<script>
const BP = '<?= rtrim($_ENV['APP_BASEPATH'] ?? '', '/') ?>';
let pedidoActivo = null;

function abrirModalAsignar(pedidoId) {
    pedidoActivo = pedidoId;
    document.getElementById('modalPedidoLabel').textContent = '#' + String(pedidoId).padStart(6,'0');
    document.getElementById('modalRepartidorSelect').value = '';
    document.getElementById('modalAsignar').classList.remove('hidden');
}

function cerrarModal() {
    document.getElementById('modalAsignar').classList.add('hidden');
    pedidoActivo = null;
}

async function confirmarAsignacion() {
    const repartidorId = document.getElementById('modalRepartidorSelect').value;
    if (!repartidorId) { alert('Selecciona un repartidor.'); return; }

    const btn = document.getElementById('btnAsignarText');
    btn.textContent = 'Asignando...';

    try {
        const res = await fetch(`${BP}/pedidos/${pedidoActivo}/asignar-repartidor`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `repartidor_id=${repartidorId}`
        });
        const data = await res.json();
        if (data.success) {
            cerrarModal();
            window.location.reload();
        } else {
            alert(data.error || 'Error al asignar.');
            btn.textContent = 'Asignar';
        }
    } catch(e) {
        alert('Error de conexión.');
        btn.textContent = 'Asignar';
    }
}
</script>

<?php
$contenido = ob_get_clean();
require __DIR__ . '/../layouts/base.php';
?>
