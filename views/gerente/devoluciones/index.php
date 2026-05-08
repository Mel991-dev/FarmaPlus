<?php
/**
 * views/gerente/devoluciones/index.php
 * Lista de devoluciones con tabs por estado.
 *
 * Variables esperadas:
 *   $devoluciones  array   — resultados de DevolucionModel::listar()
 *   $contadores    array   — ['pendiente'=>N, 'aprobada'=>N, 'rechazada'=>N]
 *   $estadoActual  string  — estado del tab activo
 */
$basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
$success  = $_GET['success'] ?? null;

$motivoLabel = [
    'producto_danado' => 'Producto dañado',
    'error_envio'     => 'Error de envío',
    'cambio_opinion'  => 'Cambio de opinión',
    'vencido'         => 'Producto vencido',
    'otro'            => 'Otro',
];

$estadoClase = [
    'pendiente' => 'bg-amber-50 text-amber-700 border-amber-200',
    'aprobada'  => 'bg-green-50 text-green-700 border-green-200',
    'rechazada' => 'bg-red-50 text-red-700 border-red-200',
];
$estadoIcono = [
    'pendiente' => 'clock',
    'aprobada'  => 'check-circle',
    'rechazada' => 'x-circle',
];
?>

<?php if ($success): ?>
<div class="mb-5 flex items-center gap-3 bg-green-50 border border-green-200 text-green-700 rounded-xl px-4 py-3 text-[13px] font-semibold">
    <i data-lucide="check-circle" class="w-4 h-4 shrink-0"></i>
    <?= htmlspecialchars(urldecode($success)) ?>
</div>
<?php endif; ?>

<!-- Cabecera -->
<div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
    <div>
        <h1 class="text-2xl font-bold text-fp-text tracking-tight flex items-center gap-2">
            <i data-lucide="rotate-ccw" class="w-6 h-6 text-fp-primary"></i>
            Gestión de Devoluciones
        </h1>
        <p class="text-[13px] text-fp-muted mt-0.5">Revisa, aprueba o rechaza las solicitudes de devolución de los clientes.</p>
    </div>
    <a href="<?= $basePath ?>/gerente/devoluciones/crear"
       class="inline-flex items-center gap-2 h-10 px-5 bg-fp-primary hover:bg-fp-primary-light text-white text-[13px] font-semibold rounded-xl transition-all shadow-sm shrink-0">
        <i data-lucide="plus" class="w-4 h-4"></i> Nueva solicitud
    </a>
</div>

<!-- Tabs por estado -->
<div class="flex items-center gap-1 mb-6 bg-white border border-fp-border rounded-xl p-1 w-fit">
    <?php
    $tabs = [
        'pendiente' => ['label' => 'Pendientes',  'icon' => 'clock',        'color' => 'amber'],
        'aprobada'  => ['label' => 'Aprobadas',   'icon' => 'check-circle', 'color' => 'green'],
        'rechazada' => ['label' => 'Rechazadas',  'icon' => 'x-circle',     'color' => 'red'],
    ];
    foreach ($tabs as $tabKey => $tab):
        $isActive = $estadoActual === $tabKey;
        $count    = $contadores[$tabKey] ?? 0;
        $activeClass = $isActive
            ? 'bg-fp-primary text-white shadow-sm'
            : 'text-fp-muted hover:bg-fp-bg-main hover:text-fp-text';
    ?>
    <a href="<?= $basePath ?>/gerente/devoluciones?estado=<?= $tabKey ?>"
       class="inline-flex items-center gap-2 px-4 h-9 rounded-lg text-[13px] font-semibold transition-all <?= $activeClass ?>">
        <i data-lucide="<?= $tab['icon'] ?>" class="w-3.5 h-3.5"></i>
        <?= $tab['label'] ?>
        <span class="inline-flex items-center justify-center min-w-[20px] h-5 px-1.5 rounded-full text-[10px] font-bold
                     <?= $isActive ? 'bg-white/20 text-white' : 'bg-fp-border text-fp-muted' ?>">
            <?= $count ?>
        </span>
    </a>
    <?php endforeach; ?>
</div>

<!-- Tabla de devoluciones -->
<div class="bg-white border border-fp-border rounded-2xl overflow-hidden shadow-sm">
    <?php if (empty($devoluciones)): ?>
        <div class="flex flex-col items-center justify-center py-16 text-fp-muted">
            <div class="w-16 h-16 rounded-full bg-fp-bg-main flex items-center justify-center mb-4">
                <i data-lucide="package-search" class="w-8 h-8 opacity-40"></i>
            </div>
            <p class="text-[15px] font-semibold text-fp-text">Sin devoluciones <?= $estadoActual ?>s</p>
            <p class="text-[13px] mt-1">No hay solicitudes con este estado en el sistema.</p>
        </div>
    <?php else: ?>
        <div class="overflow-x-auto">
            <table class="w-full text-[13px]">
                <thead>
                    <tr class="bg-fp-bg-main border-b border-fp-border">
                        <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">#</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Origen</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Producto</th>
                        <th class="text-center px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Cant.</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Motivo</th>
                        <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Fecha</th>
                        <th class="text-center px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Estado</th>
                        <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Acción</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-fp-border">
                    <?php foreach ($devoluciones as $d):
                        $origenLabel = $d['tipo_origen'] === 'pedido'
                            ? 'Pedido #' . ($d['ped_numero'] ?? '?')
                            : 'Venta ' . ($d['venta_numero'] ?? '?');
                        $personaLabel = $d['tipo_origen'] === 'pedido'
                            ? ($d['cliente_nombre'] ?? '—')
                            : ($d['vendedor_nombre'] ?? '—');
                    ?>
                    <tr class="hover:bg-fp-bg-main/40 transition-colors">
                        <td class="px-5 py-3 font-mono text-fp-muted"><?= $d['devolucion_id'] ?></td>
                        <td class="px-5 py-3">
                            <div class="font-semibold text-fp-text text-[12px]"><?= htmlspecialchars($origenLabel) ?></div>
                            <div class="text-[11px] text-fp-muted truncate max-w-[140px]"><?= htmlspecialchars($personaLabel) ?></div>
                        </td>
                        <td class="px-5 py-3 font-semibold text-fp-text max-w-[180px] truncate">
                            <?= htmlspecialchars($d['producto_nombre']) ?>
                            <?php if ($d['control_especial']): ?>
                                <span class="ml-1 inline-block px-1.5 py-0.5 rounded text-[9px] font-bold bg-red-50 text-red-600 border border-red-100">CE</span>
                            <?php endif; ?>
                        </td>
                        <td class="px-5 py-3 text-center font-mono font-bold text-fp-text"><?= $d['cantidad'] ?></td>
                        <td class="px-5 py-3 text-fp-muted"><?= $motivoLabel[$d['motivo']] ?? $d['motivo'] ?></td>
                        <td class="px-5 py-3 text-fp-muted whitespace-nowrap">
                            <?= (new DateTime($d['created_at']))->format('d/m/Y H:i') ?>
                        </td>
                        <td class="px-5 py-3 text-center">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full border text-[11px] font-bold uppercase tracking-wider <?= $estadoClase[$d['estado']] ?? '' ?>">
                                <i data-lucide="<?= $estadoIcono[$d['estado']] ?? 'circle' ?>" class="w-3 h-3"></i>
                                <?= ucfirst($d['estado']) ?>
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right">
                            <a href="<?= $basePath ?>/gerente/devoluciones/<?= $d['devolucion_id'] ?>"
                               class="inline-flex items-center gap-1.5 h-8 px-3 bg-fp-bg-main border border-fp-border rounded-lg text-[12px] font-semibold text-fp-text hover:bg-fp-primary hover:text-white hover:border-fp-primary transition-all">
                                <i data-lucide="eye" class="w-3.5 h-3.5"></i> Ver
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</div>
