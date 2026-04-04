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
        </div>
        <?php endforeach; ?>
    </div>
    <?php endif; ?>
</div>

<?php
$contenido = ob_get_clean();
require __DIR__ . '/../layouts/tienda_layout.php';
?>
