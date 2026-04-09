<?php
/**
 * views/configuracion/index.php
 * Configuración del sistema (solo Admin).
 * Tailwind puro.
 */
$basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
?>

<!-- CABECERA -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-7">
    <div>
        <h1 class="text-2xl font-bold text-fp-text tracking-tight">Configuración del Sistema</h1>
        <p class="text-[13px] text-fp-muted mt-1">Variables de negocio · Solo administrador</p>
    </div>
</div>

<!-- Flash -->
<?php if ($flash_success ?? null): ?>
<div class="flex items-center gap-3 bg-green-50 border border-green-200 text-green-800 rounded-lg px-4 py-3 mb-6 text-[13px] font-semibold">
    <i data-lucide="check-circle" class="w-4 h-4 text-green-500 shrink-0"></i>
    <?= htmlspecialchars($flash_success) ?>
</div>
<?php endif; ?>

<form method="POST" action="<?= $basePath ?>/admin/configuracion" class="max-w-2xl space-y-6">

    <!-- Negocio -->
    <div class="bg-white rounded-xl border border-fp-border p-6 space-y-4">
        <h2 class="text-[14px] font-bold text-fp-text flex items-center gap-2 pb-3 border-b border-fp-border">
            <i data-lucide="building-2" class="w-4 h-4 text-fp-primary"></i> Datos del negocio
        </h2>

        <div class="flex flex-col gap-1.5">
            <label class="text-[12px] font-semibold text-fp-text">Nombre de la farmacia</label>
            <p class="text-[11px] text-fp-muted -mt-0.5"><?= htmlspecialchars($config['nombre_farmacia']['descripcion'] ?? '') ?></p>
            <input type="text" name="nombre_farmacia"
                   value="<?= htmlspecialchars($config['nombre_farmacia']['valor'] ?? '') ?>"
                   class="h-10 px-3 border border-fp-border rounded-lg text-[13px] text-fp-text bg-white focus:outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20 transition-all">
        </div>

        <div class="flex flex-col gap-1.5">
            <label class="text-[12px] font-semibold text-fp-text">Ciudad de cobertura</label>
            <p class="text-[11px] text-fp-muted -mt-0.5"><?= htmlspecialchars($config['ciudad_cobertura']['descripcion'] ?? '') ?></p>
            <input type="text" name="ciudad_cobertura"
                   value="<?= htmlspecialchars($config['ciudad_cobertura']['valor'] ?? '') ?>"
                   class="h-10 px-3 border border-fp-border rounded-lg text-[13px] text-fp-text bg-white focus:outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20 transition-all">
        </div>

        <div class="flex flex-col gap-1.5">
            <label class="text-[12px] font-semibold text-fp-text">Correo de notificaciones</label>
            <p class="text-[11px] text-fp-muted -mt-0.5"><?= htmlspecialchars($config['correo_notificaciones']['descripcion'] ?? '') ?></p>
            <input type="email" name="correo_notificaciones"
                   value="<?= htmlspecialchars($config['correo_notificaciones']['valor'] ?? '') ?>"
                   placeholder="admin@farmaplus.com.co"
                   class="h-10 px-3 border border-fp-border rounded-lg text-[13px] text-fp-text bg-white focus:outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20 transition-all">
        </div>
    </div>

    <!-- Domicilios -->
    <div class="bg-white rounded-xl border border-fp-border p-6 space-y-4">
        <h2 class="text-[14px] font-bold text-fp-text flex items-center gap-2 pb-3 border-b border-fp-border">
            <i data-lucide="truck" class="w-4 h-4 text-fp-primary"></i> Domicilios y e-commerce
        </h2>

        <div class="flex flex-col gap-1.5">
            <label class="text-[12px] font-semibold text-fp-text">Costo de envío base (COP)</label>
            <p class="text-[11px] text-fp-muted -mt-0.5"><?= htmlspecialchars($config['costo_envio_base']['descripcion'] ?? '') ?></p>
            <div class="relative">
                <span class="absolute left-3 top-1/2 -translate-y-1/2 text-fp-muted text-[13px] font-bold">$</span>
                <input type="number" name="costo_envio_base" min="0" step="500"
                       value="<?= htmlspecialchars($config['costo_envio_base']['valor'] ?? '3000') ?>"
                       class="w-full h-10 pl-7 pr-3 border border-fp-border rounded-lg text-[13px] text-fp-text bg-white focus:outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20 transition-all">
            </div>
        </div>
    </div>

    <!-- Inventario -->
    <div class="bg-white rounded-xl border border-fp-border p-6 space-y-4">
        <h2 class="text-[14px] font-bold text-fp-text flex items-center gap-2 pb-3 border-b border-fp-border">
            <i data-lucide="package" class="w-4 h-4 text-fp-primary"></i> Alertas de inventario
        </h2>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="flex flex-col gap-1.5">
                <label class="text-[12px] font-semibold text-fp-text">Stock mínimo global</label>
                <p class="text-[11px] text-fp-muted -mt-0.5"><?= htmlspecialchars($config['stock_minimo_global']['descripcion'] ?? '') ?></p>
                <input type="number" name="stock_minimo_global" min="1"
                       value="<?= htmlspecialchars($config['stock_minimo_global']['valor'] ?? '10') ?>"
                       class="h-10 px-3 border border-fp-border rounded-lg text-[13px] text-fp-text bg-white focus:outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20 transition-all">
            </div>
            <div class="flex flex-col gap-1.5">
                <label class="text-[12px] font-semibold text-fp-text">Días alerta vencimiento</label>
                <p class="text-[11px] text-fp-muted -mt-0.5"><?= htmlspecialchars($config['dias_alerta_vencim']['descripcion'] ?? '') ?></p>
                <input type="number" name="dias_alerta_vencim" min="1" max="180"
                       value="<?= htmlspecialchars($config['dias_alerta_vencim']['valor'] ?? '30') ?>"
                       class="h-10 px-3 border border-fp-border rounded-lg text-[13px] text-fp-text bg-white focus:outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20 transition-all">
            </div>
        </div>

        <div class="bg-amber-50 border border-amber-200 rounded-lg px-4 py-3 flex items-start gap-2">
            <i data-lucide="info" class="w-4 h-4 text-amber-500 shrink-0 mt-0.5"></i>
            <p class="text-[12px] text-amber-700">
                Ces valores se usan como referencia global. Cada producto puede tener su propio stock mínimo configurado individualmente.
            </p>
        </div>
    </div>

    <!-- Guardar -->
    <div class="flex justify-end">
        <button type="submit"
                class="flex items-center gap-2 px-6 py-2.5 bg-fp-primary text-white text-[13px] font-bold rounded-lg hover:bg-fp-primary-light transition-colors shadow-sm">
            <i data-lucide="save" class="w-4 h-4"></i> Guardar configuración
        </button>
    </div>
</form>
