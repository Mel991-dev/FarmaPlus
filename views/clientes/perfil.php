<?php ob_start(); ?>

<div class="max-w-6xl mx-auto w-full flex flex-col gap-6">

    <!-- Mensajes de Alerta -->
    <?php if (!empty($success)): ?>
        <div class="bg-fp-success/10 border-l-4 border-fp-success text-[#1D6E3A] p-4 rounded-lg flex items-center gap-3">
            <i data-lucide="check-circle" class="w-5 h-5 shrink-0 text-fp-success"></i>
            <span class="text-sm font-medium"><?= htmlspecialchars($success) ?></span>
        </div>
    <?php endif; ?>

    <?php if (!empty($error)): ?>
        <div class="bg-fp-error/10 border-l-4 border-fp-error text-[#922B21] p-4 rounded-lg flex items-center gap-3">
            <i data-lucide="alert-circle" class="w-5 h-5 shrink-0 text-fp-error"></i>
            <span class="text-sm font-medium"><?= htmlspecialchars($error) ?></span>
        </div>
    <?php endif; ?>

    <!-- Contenedor Principal Responsive -->
    <div class="flex flex-col lg:flex-row gap-6">

        <!-- Lado Izquierdo: Tarjeta de Identidad -->
        <div class="w-full lg:w-1/3 flex flex-col gap-5">
            <div class="bg-white rounded-xl border border-fp-border shadow-sm p-6 flex flex-col items-center text-center">
                
                <div class="w-24 h-24 rounded-full bg-fp-secondary flex items-center justify-center text-3xl font-bold text-white shadow-md mb-4">
                    <?= strtoupper(substr($cliente['nombres'] ?? 'C', 0, 1) . substr($cliente['apellidos'] ?? 'L', 0, 1)) ?>
                </div>

                <h3 class="text-xl font-bold text-fp-text leading-tight"><?= htmlspecialchars($cliente['nombres'] ?? '') ?> <?= htmlspecialchars($cliente['apellidos'] ?? '') ?></h3>
                <p class="text-fp-muted text-sm mt-1">Cliente Registrado</p>

                <div class="mt-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-fp-success/10 text-fp-success text-xs font-semibold rounded-full">
                        <i data-lucide="shield-check" class="w-3.5 h-3.5"></i> Cuenta Activa
                    </span>
                </div>

                <div class="w-full h-px bg-fp-border/50 my-6"></div>

                <!-- Datos Resumen -->
                <div class="w-full flex flex-col gap-4 text-left">
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-fp-bg-main flex items-center justify-center shrink-0">
                            <i data-lucide="id-card" class="w-4 h-4 text-fp-primary"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-fp-muted uppercase tracking-wider">Documento</p>
                            <p class="text-sm font-medium text-fp-text font-mono"><?= htmlspecialchars($cliente['documento'] ?? 'No registrado') ?></p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-fp-bg-main flex items-center justify-center shrink-0">
                            <i data-lucide="mail" class="w-4 h-4 text-fp-primary"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-fp-muted uppercase tracking-wider">Correo</p>
                            <p class="text-sm font-medium text-fp-text break-all"><?= htmlspecialchars($cliente['correo'] ?? '') ?></p>
                        </div>
                    </div>

                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-fp-bg-main flex items-center justify-center shrink-0">
                            <i data-lucide="phone" class="w-4 h-4 text-fp-primary"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-fp-muted uppercase tracking-wider">Teléfono</p>
                            <p class="text-sm font-medium text-fp-text"><?= htmlspecialchars($cliente['telefono'] ?? 'No registrado') ?></p>
                        </div>
                    </div>
                    
                    <div class="flex items-start gap-3">
                        <div class="w-8 h-8 rounded-lg bg-fp-bg-main flex items-center justify-center shrink-0">
                            <i data-lucide="calendar" class="w-4 h-4 text-fp-primary"></i>
                        </div>
                        <div>
                            <p class="text-[11px] font-bold text-fp-muted uppercase tracking-wider">Fecha Registro</p>
                            <p class="text-sm font-medium text-fp-text"><?= date('d M Y, H:i', strtotime($cliente['created_at'] ?? 'now')) ?></p>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        <!-- Lado Derecho: Formulario de Actualización -->
        <div class="w-full lg:w-2/3">
            <div class="bg-white rounded-xl border border-fp-border shadow-sm flex flex-col h-full overflow-hidden">
                
                <div class="p-6 border-b border-fp-border bg-fp-bg-main/30">
                    <h3 class="text-lg font-bold text-fp-text flex items-center gap-2">
                        <i data-lucide="user-cog" class="w-5 h-5 text-fp-primary"></i> Datos Personales
                    </h3>
                    <p class="text-sm text-fp-muted mt-1">Actualiza tu información de contacto para facturación y entregas.</p>
                </div>

                <form method="POST" action="<?= $basePath ?>/mi-cuenta/actualizar" class="p-6 flex flex-col gap-5 flex-1">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="flex flex-col gap-1.5">
                            <label for="nombres" class="text-sm font-semibold text-fp-text">Nombres <span class="text-fp-error">*</span></label>
                            <input type="text" id="nombres" name="nombres" value="<?= htmlspecialchars($cliente['nombres'] ?? '') ?>" required class="w-full px-4 py-2.5 bg-fp-bg-main border border-fp-border rounded-lg text-sm text-fp-text focus:outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20 transition-all">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label for="apellidos" class="text-sm font-semibold text-fp-text">Apellidos <span class="text-fp-error">*</span></label>
                            <input type="text" id="apellidos" name="apellidos" value="<?= htmlspecialchars($cliente['apellidos'] ?? '') ?>" required class="w-full px-4 py-2.5 bg-fp-bg-main border border-fp-border rounded-lg text-sm text-fp-text focus:outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20 transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="flex flex-col gap-1.5">
                            <label for="correo" class="text-sm font-semibold text-fp-text">Correo Electrónico <span class="text-fp-error">*</span></label>
                            <input type="email" id="correo" name="correo" value="<?= htmlspecialchars($cliente['correo'] ?? '') ?>" required class="w-full px-4 py-2.5 bg-fp-bg-main border border-fp-border rounded-lg text-sm text-fp-text focus:outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20 transition-all">
                        </div>
                        <div class="flex flex-col gap-1.5">
                            <label for="telefono" class="text-sm font-semibold text-fp-text">Teléfono Celular</label>
                            <input type="tel" id="telefono" name="telefono" value="<?= htmlspecialchars($cliente['telefono'] ?? '') ?>" class="w-full px-4 py-2.5 bg-fp-bg-main border border-fp-border rounded-lg text-sm text-fp-text focus:outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20 transition-all">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="flex flex-col gap-1.5">
                            <label for="documento" class="text-sm font-semibold text-fp-text text-fp-muted">Documento de Identidad</label>
                            <input type="text" id="documento" value="<?= htmlspecialchars($cliente['documento'] ?? '') ?>" disabled class="w-full px-4 py-2.5 bg-[#f1f5f9] border border-fp-border rounded-lg text-sm text-fp-muted cursor-not-allowed font-mono">
                            <span class="text-[11px] text-fp-muted mt-0.5"><i data-lucide="info" class="w-3 h-3 inline-block relative -top-[1px]"></i> Por seguridad este campo no se puede editar.</span>
                        </div>
                    </div>

                    <div class="mt-auto pt-6 flex items-center justify-end border-t border-fp-border/50">
                        <button type="submit" class="bg-fp-primary text-white font-semibold px-5 py-2.5 rounded-lg hover:bg-fp-primary-dark transition-colors inline-flex items-center gap-2 shadow-sm">
                            <i data-lucide="save" class="w-4 h-4"></i> Guardar Cambios
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>
</div>

<?php $contenido = ob_get_clean(); ?>
<?php include __DIR__ . '/../layouts/base_cliente.php'; ?>
