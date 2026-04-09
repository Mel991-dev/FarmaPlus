<?php
/**
 * views/usuarios/form.php
 * Formulario unificado CREAR / EDITAR empleados (Admin).
 * $usuario === null → modo crear · $usuario = array → modo editar
 * Tailwind puro.
 */
$basePath  = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
$esEdicion = $usuario !== null;
$action    = $esEdicion
    ? $basePath . '/admin/usuarios/' . $usuario['usuario_id'] . '/editar'
    : $basePath . '/admin/usuarios/crear';

$tiposDoc = ['CC', 'CE', 'NIT', 'PEP', 'PPT'];
?>

<!-- CABECERA -->
<div class="flex items-center gap-3 mb-7">
    <a href="<?= $basePath ?>/admin/usuarios"
       class="p-2 rounded-lg border border-fp-border text-fp-muted hover:text-fp-primary hover:border-fp-primary/40 transition-colors">
        <i data-lucide="arrow-left" class="w-4 h-4"></i>
    </a>
    <div>
        <h1 class="text-2xl font-bold text-fp-text tracking-tight">
            <?= $esEdicion ? 'Editar empleado' : 'Nuevo empleado' ?>
        </h1>
        <?php if ($esEdicion): ?>
        <p class="text-[13px] text-fp-muted mt-0.5">
            <?= htmlspecialchars($usuario['nombres'] . ' ' . $usuario['apellidos']) ?>
            &mdash; ID <?= $usuario['usuario_id'] ?>
        </p>
        <?php endif; ?>
    </div>
</div>

<!-- FORMULARIO -->
<form method="POST" action="<?= $action ?>" class="max-w-2xl space-y-6">

    <!-- Información personal -->
    <div class="bg-white rounded-xl border border-fp-border p-6 space-y-4">
        <h2 class="text-[14px] font-bold text-fp-text flex items-center gap-2 pb-3 border-b border-fp-border">
            <i data-lucide="user" class="w-4 h-4 text-fp-primary"></i> Información personal
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <!-- Nombres -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[12px] font-semibold text-fp-text">Nombres <span class="text-fp-error">*</span></label>
                <input type="text" name="nombres" required
                       value="<?= htmlspecialchars($usuario['nombres'] ?? '') ?>"
                       placeholder="Ej: Carlos"
                       class="h-10 px-3 border border-fp-border rounded-lg text-[13px] text-fp-text bg-white focus:outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20 transition-all">
            </div>
            <!-- Apellidos -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[12px] font-semibold text-fp-text">Apellidos <span class="text-fp-error">*</span></label>
                <input type="text" name="apellidos" required
                       value="<?= htmlspecialchars($usuario['apellidos'] ?? '') ?>"
                       placeholder="Ej: Martínez"
                       class="h-10 px-3 border border-fp-border rounded-lg text-[13px] text-fp-text bg-white focus:outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20 transition-all">
            </div>
            <!-- Tipo documento -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[12px] font-semibold text-fp-text">Tipo de documento</label>
                <select name="tipo_documento"
                        class="h-10 px-3 border border-fp-border rounded-lg text-[13px] text-fp-text bg-white focus:outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20 transition-all">
                    <?php foreach ($tiposDoc as $td): ?>
                    <option value="<?= $td ?>" <?= ($usuario['tipo_documento'] ?? 'CC') === $td ? 'selected' : '' ?>>
                        <?= $td ?>
                    </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <!-- Documento -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[12px] font-semibold text-fp-text">Número de documento <span class="text-fp-error">*</span></label>
                <input type="text" name="documento" required
                       value="<?= htmlspecialchars($usuario['documento'] ?? '') ?>"
                       placeholder="Ej: 1023456789"
                       class="h-10 px-3 border border-fp-border rounded-lg text-[13px] text-fp-text bg-white focus:outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20 transition-all">
            </div>
            <!-- Correo -->
            <div class="flex flex-col gap-1.5 sm:col-span-2">
                <label class="text-[12px] font-semibold text-fp-text">Correo electrónico <span class="text-fp-error">*</span></label>
                <input type="email" name="correo" required
                       value="<?= htmlspecialchars($usuario['correo'] ?? '') ?>"
                       placeholder="correo@ejemplo.com"
                       class="h-10 px-3 border border-fp-border rounded-lg text-[13px] text-fp-text bg-white focus:outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20 transition-all">
            </div>
            <!-- Teléfono -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[12px] font-semibold text-fp-text">Teléfono</label>
                <input type="tel" name="telefono"
                       value="<?= htmlspecialchars($usuario['telefono'] ?? '') ?>"
                       placeholder="Ej: 3112345678"
                       class="h-10 px-3 border border-fp-border rounded-lg text-[13px] text-fp-text bg-white focus:outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20 transition-all">
            </div>
            <?php if ($esEdicion): ?>
            <!-- Estado -->
            <div class="flex flex-col gap-1.5">
                <label class="text-[12px] font-semibold text-fp-text">Estado de la cuenta</label>
                <select name="activo"
                        class="h-10 px-3 border border-fp-border rounded-lg text-[13px] text-fp-text bg-white focus:outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20 transition-all">
                    <option value="1" <?= ($usuario['activo'] ?? 1) ? 'selected' : '' ?>>Activo</option>
                    <option value="0" <?= !($usuario['activo'] ?? 1) ? 'selected' : '' ?>>Suspendido</option>
                </select>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Rol y acceso -->
    <div class="bg-white rounded-xl border border-fp-border p-6 space-y-4">
        <h2 class="text-[14px] font-bold text-fp-text flex items-center gap-2 pb-3 border-b border-fp-border">
            <i data-lucide="shield-check" class="w-4 h-4 text-fp-primary"></i> Rol y acceso
        </h2>
        <div class="flex flex-col gap-1.5">
            <label class="text-[12px] font-semibold text-fp-text">Rol del sistema <span class="text-fp-error">*</span></label>
            <select name="rol_id" required
                    class="h-10 px-3 border border-fp-border rounded-lg text-[13px] text-fp-text bg-white focus:outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20 transition-all">
                <option value="">— Seleccionar rol —</option>
                <?php foreach ($roles as $rol): ?>
                <option value="<?= $rol['rol_id'] ?>"
                    <?= ($usuario['rol_id'] ?? '') == $rol['rol_id'] ? 'selected' : '' ?>>
                    <?= ucfirst($rol['nombre']) ?> — <?= htmlspecialchars($rol['descripcion']) ?>
                </option>
                <?php endforeach; ?>
            </select>
            <?php if ($esEdicion): ?>
            <p class="text-[11px] text-fp-muted flex items-center gap-1 mt-0.5">
                <i data-lucide="info" class="w-3 h-3"></i>
                El cambio de rol tomará efecto en la próxima sesión del usuario.
            </p>
            <?php endif; ?>
        </div>
        <!-- Contraseña -->
        <div class="flex flex-col gap-1.5">
            <label class="text-[12px] font-semibold text-fp-text">
                Contraseña <?= $esEdicion ? '(dejar en blanco para no cambiar)' : '<span class="text-fp-error">*</span>' ?>
            </label>
            <input type="password" name="contrasena" <?= $esEdicion ? '' : 'required' ?>
                   minlength="6"
                   placeholder="<?= $esEdicion ? 'Nueva contraseña (opcional)' : 'Mínimo 6 caracteres' ?>"
                   class="h-10 px-3 border border-fp-border rounded-lg text-[13px] text-fp-text bg-white focus:outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20 transition-all">
        </div>
    </div>

    <!-- Acciones -->
    <div class="flex items-center justify-end gap-3">
        <a href="<?= $basePath ?>/admin/usuarios"
           class="px-5 py-2.5 border border-fp-border text-fp-text text-[13px] font-semibold rounded-lg hover:bg-fp-bg-main transition-colors">
            Cancelar
        </a>
        <button type="submit"
                class="flex items-center gap-2 px-5 py-2.5 bg-fp-primary text-white text-[13px] font-bold rounded-lg hover:bg-fp-primary-light transition-colors shadow-sm">
            <i data-lucide="save" class="w-4 h-4"></i>
            <?= $esEdicion ? 'Guardar cambios' : 'Crear empleado' ?>
        </button>
    </div>

</form>
