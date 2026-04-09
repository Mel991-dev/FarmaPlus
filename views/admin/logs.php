<?php
/**
 * views/admin/logs.php
 * Logs de auditoría — filtrable por usuario, acción y fecha.
 * Tailwind puro.
 */
$basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
?>

<!-- CABECERA -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-7">
    <div>
        <h1 class="text-2xl font-bold text-fp-text tracking-tight">Logs de Auditoría</h1>
        <p class="text-[13px] text-fp-muted mt-1">
            <?= count($logs) ?> registro(s) · Máximo 200 más recientes
        </p>
    </div>
</div>

<!-- FILTROS -->
<form method="GET" action="<?= $basePath ?>/admin/logs"
      class="flex flex-wrap items-end gap-3 bg-white border border-fp-border rounded-xl p-4 mb-6 shadow-sm">
    <div class="flex flex-col gap-1">
        <label class="text-[11px] font-semibold text-fp-muted uppercase tracking-wide">Usuario</label>
        <input type="text" name="usuario" value="<?= htmlspecialchars($filtroUsuario ?? '') ?>"
               placeholder="Nombre o documento"
               class="h-9 px-3 border border-fp-border rounded-lg text-[13px] text-fp-text focus:outline-none focus:border-fp-primary focus:ring-1 focus:ring-fp-primary/30 w-44">
    </div>
    <div class="flex flex-col gap-1">
        <label class="text-[11px] font-semibold text-fp-muted uppercase tracking-wide">Acción</label>
        <input type="text" name="accion" value="<?= htmlspecialchars($filtroAccion ?? '') ?>"
               placeholder="Ej: usuario_creado"
               class="h-9 px-3 border border-fp-border rounded-lg text-[13px] text-fp-text focus:outline-none focus:border-fp-primary focus:ring-1 focus:ring-fp-primary/30 w-44">
    </div>
    <div class="flex flex-col gap-1">
        <label class="text-[11px] font-semibold text-fp-muted uppercase tracking-wide">Fecha</label>
        <input type="date" name="fecha" value="<?= htmlspecialchars($filtroFecha ?? '') ?>"
               class="h-9 px-3 border border-fp-border rounded-lg text-[13px] text-fp-text focus:outline-none focus:border-fp-primary focus:ring-1 focus:ring-fp-primary/30">
    </div>
    <button type="submit"
            class="h-9 px-4 bg-fp-primary text-white text-[13px] font-semibold rounded-lg hover:bg-fp-primary-light transition-colors">
        Filtrar
    </button>
    <a href="<?= $basePath ?>/admin/logs"
       class="h-9 px-4 flex items-center border border-fp-border text-fp-muted text-[13px] font-semibold rounded-lg hover:bg-fp-bg-main transition-colors">
        Limpiar
    </a>
</form>

<!-- TABLA -->
<div class="bg-white rounded-xl border border-fp-border overflow-hidden shadow-sm">
    <?php if (empty($logs)): ?>
    <div class="flex flex-col items-center py-16 text-fp-muted gap-3">
        <i data-lucide="file-text" class="w-12 h-12 text-fp-border"></i>
        <p class="text-[14px] font-medium">No hay logs que coincidan con los filtros</p>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full text-[13px]">
            <thead>
                <tr class="bg-fp-bg-main border-b border-fp-border">
                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Fecha / Hora</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Usuario</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted hidden md:table-cell">Rol</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Acción</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted hidden lg:table-cell">Detalle</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted hidden xl:table-cell">IP</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-fp-border">
                <?php foreach ($logs as $log):
                    $accion = $log['accion'] ?? '';
                    $accionColor = match(true) {
                        str_contains($accion, 'eliminado') || str_contains($accion, 'error') => 'bg-red-100 text-red-700',
                        str_contains($accion, 'creado')    || str_contains($accion, 'login')  => 'bg-green-100 text-green-700',
                        str_contains($accion, 'editado')   || str_contains($accion, 'actualiz') => 'bg-blue-100 text-blue-700',
                        str_contains($accion, 'suspendido') => 'bg-amber-100 text-amber-700',
                        default => 'bg-gray-100 text-gray-600',
                    };
                ?>
                <tr class="hover:bg-fp-bg-main/40 transition-colors">
                    <td class="px-5 py-3 text-fp-muted font-mono text-[11px] whitespace-nowrap">
                        <?= (new DateTime($log['created_at']))->format('d/m/Y H:i:s') ?>
                    </td>
                    <td class="px-5 py-3 font-semibold text-fp-text whitespace-nowrap">
                        <?= htmlspecialchars($log['nombre_usuario']) ?>
                    </td>
                    <td class="px-5 py-3 text-fp-muted hidden md:table-cell text-[12px]">
                        <?= ucfirst(htmlspecialchars($log['rol'])) ?>
                    </td>
                    <td class="px-5 py-3">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold <?= $accionColor ?>">
                            <?= htmlspecialchars(str_replace('_', ' ', $accion)) ?>
                        </span>
                    </td>
                    <td class="px-5 py-3 text-fp-muted hidden lg:table-cell max-w-[300px] truncate" title="<?= htmlspecialchars($log['detalle']) ?>">
                        <?= htmlspecialchars($log['detalle']) ?>
                    </td>
                    <td class="px-5 py-3 text-fp-muted hidden xl:table-cell font-mono text-[11px]">
                        <?= htmlspecialchars($log['ip'] ?: '—') ?>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
