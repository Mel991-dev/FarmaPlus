<?php
/**
 * views/usuarios/listar.php
 * Gestión de empleados (Admin). Tailwind puro.
 */
$basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');

$rolBadge = [
    'administrador' => 'bg-red-100 text-red-700',
    'gerente'       => 'bg-purple-100 text-purple-700',
    'farmaceutico'  => 'bg-blue-100 text-blue-700',
    'cajero'        => 'bg-cyan-100 text-cyan-700',
    'bodeguero'     => 'bg-orange-100 text-orange-700',
    'repartidor'    => 'bg-green-100 text-green-700',
];

$flashSuccess = $_SESSION['flash_success'] ?? null;
$flashError   = $_SESSION['flash_error']   ?? null;
unset($_SESSION['flash_success'], $_SESSION['flash_error']);
?>

<!-- CABECERA -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-7">
    <div>
        <h1 class="text-2xl font-bold text-fp-text tracking-tight">Gestión de Usuarios</h1>
        <p class="text-[13px] text-fp-muted mt-1"><?= count($empleados) ?> empleado(s) registrado(s)</p>
    </div>
    <a href="<?= $basePath ?>/admin/usuarios/crear"
       class="flex items-center gap-2 px-4 py-2.5 bg-fp-primary text-white text-[13px] font-bold rounded-lg hover:bg-fp-primary-light transition-colors shadow-sm">
        <i data-lucide="user-plus" class="w-4 h-4"></i> Nuevo empleado
    </a>
</div>

<!-- Flash messages -->
<?php if ($flashSuccess): ?>
<div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 mb-5 text-[13px] font-semibold">
    <i data-lucide="check-circle" class="w-4 h-4 text-green-500 shrink-0"></i> <?= htmlspecialchars($flashSuccess) ?>
</div>
<?php endif; ?>
<?php if ($flashError): ?>
<div class="flex items-center gap-3 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 mb-5 text-[13px] font-semibold">
    <i data-lucide="x-circle" class="w-4 h-4 text-red-500 shrink-0"></i> <?= htmlspecialchars($flashError) ?>
</div>
<?php endif; ?>

<!-- TABLA -->
<div class="bg-white rounded-xl border border-fp-border overflow-hidden shadow-sm">
    <?php if (empty($empleados)): ?>
    <div class="flex flex-col items-center py-16 text-fp-muted gap-3">
        <i data-lucide="users" class="w-12 h-12 text-fp-border"></i>
        <p class="text-[14px] font-medium">No hay empleados registrados aún.</p>
        <a href="<?= $basePath ?>/admin/usuarios/crear"
           class="text-[13px] text-fp-primary font-bold hover:underline">Crear primer empleado</a>
    </div>
    <?php else: ?>
    <div class="overflow-x-auto">
        <table class="w-full text-[13px]">
            <thead>
                <tr class="bg-fp-bg-main border-b border-fp-border">
                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Empleado</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted hidden md:table-cell">Documento</th>
                    <th class="text-left px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted hidden lg:table-cell">Correo</th>
                    <th class="text-center px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Rol</th>
                    <th class="text-center px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Estado</th>
                    <th class="text-right px-5 py-3 text-[11px] font-semibold uppercase tracking-wider text-fp-muted">Acciones</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-fp-border">
                <?php foreach ($empleados as $u): ?>
                <tr class="hover:bg-fp-bg-main/40 transition-colors">
                    <!-- Avatar + Nombre -->
                    <td class="px-5 py-3">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-full bg-fp-secondary/20 text-fp-secondary text-[12px] font-bold flex items-center justify-center shrink-0">
                                <?= strtoupper(substr($u['nombres'], 0, 1) . substr($u['apellidos'], 0, 1)) ?>
                            </div>
                            <div>
                                <p class="font-semibold text-fp-text"><?= htmlspecialchars($u['nombres'] . ' ' . $u['apellidos']) ?></p>
                                <p class="text-[11px] text-fp-muted"><?= htmlspecialchars($u['telefono'] ?: '—') ?></p>
                            </div>
                        </div>
                    </td>
                    <!-- Documento -->
                    <td class="px-5 py-3 text-fp-muted hidden md:table-cell font-mono text-[12px]">
                        <?= htmlspecialchars($u['tipo_documento'] . ' ' . $u['documento']) ?>
                    </td>
                    <!-- Correo -->
                    <td class="px-5 py-3 text-fp-muted hidden lg:table-cell truncate max-w-[180px]">
                        <?= htmlspecialchars($u['correo']) ?>
                    </td>
                    <!-- Rol -->
                    <td class="px-5 py-3 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold <?= $rolBadge[$u['rol_nombre']] ?? 'bg-gray-100 text-gray-600' ?>">
                            <?= ucfirst($u['rol_nombre']) ?>
                        </span>
                    </td>
                    <!-- Estado -->
                    <td class="px-5 py-3 text-center">
                        <?php if ($u['activo']): ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-green-100 text-green-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Activo
                        </span>
                        <?php else: ?>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-red-100 text-red-700">
                            <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Suspendido
                        </span>
                        <?php endif; ?>
                    </td>
                    <!-- Acciones -->
                    <td class="px-5 py-3 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="<?= $basePath ?>/admin/usuarios/<?= $u['usuario_id'] ?>/editar"
                               class="p-1.5 rounded-lg text-fp-muted hover:text-fp-primary hover:bg-fp-primary/10 transition-colors" title="Editar">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </a>
                            <form method="POST" action="<?= $basePath ?>/admin/usuarios/<?= $u['usuario_id'] ?>/suspender" class="m-0">
                                <button type="submit"
                                        class="p-1.5 rounded-lg text-fp-muted hover:text-amber-500 hover:bg-amber-50 transition-colors"
                                        title="<?= $u['activo'] ? 'Suspender' : 'Activar' ?>">
                                    <i data-lucide="<?= $u['activo'] ? 'user-x' : 'user-check' ?>" class="w-4 h-4"></i>
                                </button>
                            </form>
                            <?php if ((int)$u['usuario_id'] !== (int)($_SESSION['usuario_id'] ?? 0)): ?>
                            <form method="POST" action="<?= $basePath ?>/admin/usuarios/<?= $u['usuario_id'] ?>/eliminar" class="m-0"
                                  onsubmit="return confirm('¿Eliminar a <?= addslashes($u['nombres']) ?>? Esta acción no se puede deshacer.')">
                                <button type="submit"
                                        class="p-1.5 rounded-lg text-fp-muted hover:text-red-500 hover:bg-red-50 transition-colors" title="Eliminar">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i>
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
    <?php endif; ?>
</div>
