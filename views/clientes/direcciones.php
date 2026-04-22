<?php ob_start(); ?>

<div class="max-w-6xl mx-auto w-full flex flex-col gap-6">

    <div class="flex flex-col gap-1">
        <h2 class="text-2xl font-bold text-fp-text tracking-tight">Mis Direcciones</h2>
        <p class="text-sm text-fp-muted">Gestiona tus direcciones de entrega para farmacia a domicilio</p>
    </div>

    <!-- Alertas -->
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

    <!-- Contenido -->
    <div class="flex flex-col lg:flex-row gap-6 items-start">
        
        <!-- Formulario Nueva Dirección -->
        <div class="w-full lg:w-1/3 bg-white rounded-xl border border-fp-border shadow-sm flex flex-col">
            <div class="p-5 border-b border-fp-border bg-fp-bg-main/30">
                <h3 class="text-base font-bold text-fp-text flex items-center gap-2">
                    <i data-lucide="map-pin" class="w-4 h-4 text-fp-primary"></i> Añadir Dirección
                </h3>
            </div>
            
            <form action="<?= $basePath ?? '' ?>/mi-cuenta/direcciones/crear" method="POST" class="p-5 flex flex-col gap-4">
                
                <div class="flex flex-col gap-1.5">
                    <label for="alias" class="text-sm font-semibold text-fp-text">Alias (Casa, Oficina, etc.) <span class="text-fp-error">*</span></label>
                    <input type="text" id="alias" name="alias" class="w-full px-3.5 py-2 bg-fp-bg-main border border-fp-border rounded-lg text-sm text-fp-text focus:outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20 transition-all" placeholder="Ej: Casa principal" required>
                </div>
                
                <div class="flex flex-col gap-1.5">
                    <label for="direccion" class="text-sm font-semibold text-fp-text">Dirección exacta <span class="text-fp-error">*</span></label>
                    <input type="text" id="direccion" name="direccion" class="w-full px-3.5 py-2 bg-fp-bg-main border border-fp-border rounded-lg text-sm text-fp-text focus:outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20 transition-all" placeholder="Calle 123 #45-67" required>
                </div>
                
                <div class="flex gap-4">
                    <div class="flex flex-col gap-1.5 flex-1 w-1/2">
                        <label for="barrio" class="text-sm font-semibold text-fp-text">Barrio</label>
                        <input type="text" id="barrio" name="barrio" class="w-full px-3.5 py-2 bg-fp-bg-main border border-fp-border rounded-lg text-sm text-fp-text focus:outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20 transition-all">
                    </div>
                    <div class="flex flex-col gap-1.5 flex-1 w-1/2">
                        <label for="ciudad" class="text-sm font-semibold text-fp-text">Ciudad <span class="text-fp-error">*</span></label>
                        <input type="text" id="ciudad" name="ciudad" class="w-full px-3.5 py-2 bg-fp-bg-main border border-fp-border rounded-lg text-sm text-fp-text focus:outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20 transition-all" required value="Florencia">
                    </div>
                </div>
                
                <div class="flex flex-col gap-1.5">
                    <label for="referencia" class="text-sm font-semibold text-fp-text">Punto de referencia</label>
                    <input type="text" id="referencia" name="referencia" class="w-full px-3.5 py-2 bg-fp-bg-main border border-fp-border rounded-lg text-sm text-fp-text focus:outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20 transition-all" placeholder="Frente al parque...">
                </div>
                
                <label class="flex items-center gap-2.5 mt-2 mb-1 cursor-pointer group">
                    <input type="checkbox" name="predeterminada" value="1" class="w-4 h-4 text-fp-primary bg-fp-bg-main border-fp-border rounded focus:ring-fp-primary cursor-pointer">
                    <span class="text-sm font-medium text-fp-text group-hover:text-fp-primary transition-colors">Hacer mi dirección predeterminada</span>
                </label>
                
                <button type="submit" class="w-full bg-fp-primary text-white font-semibold px-4 py-2.5 rounded-lg hover:bg-fp-primary-dark transition-colors flex items-center justify-center gap-2 mt-2 shadow-sm">
                    <i data-lucide="plus" class="w-4 h-4"></i> Guardar Dirección
                </button>
            </form>
        </div>

        <!-- Lista de Direcciones -->
        <div class="w-full lg:w-2/3 bg-white rounded-xl border border-fp-border shadow-sm flex flex-col">
            <div class="p-5 border-b border-fp-border bg-fp-bg-main/30 flex items-center justify-between">
                <h3 class="text-base font-bold text-fp-text flex items-center gap-2">
                    <i data-lucide="list" class="w-4 h-4 text-fp-primary"></i> Direcciones Guardadas
                </h3>
            </div>
            
            <div class="flex flex-col bg-fp-border/20 gap-px">
                <?php if (empty($direcciones)): ?>
                    <div class="p-10 flex flex-col items-center justify-center text-center bg-white">
                        <div class="w-16 h-16 rounded-full bg-fp-bg-main flex items-center justify-center mb-4">
                            <i data-lucide="map-pin-off" class="w-8 h-8 text-fp-muted"></i>
                        </div>
                        <h4 class="text-base font-bold text-fp-text">No hay direcciones</h4>
                        <p class="text-sm text-fp-muted mt-1">Aún no has registrado ninguna dirección de entrega.</p>
                    </div>
                <?php else: ?>
                    <?php foreach($direcciones as $dir): ?>
                        <div class="p-5 bg-white flex flex-col md:flex-row items-start md:items-center justify-between gap-4 transition-colors hover:bg-fp-bg-main/50">
                            <div class="flex-1 min-w-0">
                                <h4 class="text-[15px] font-bold text-fp-text flex items-center gap-2 mb-1">
                                    <?= htmlspecialchars($dir['alias']) ?>
                                    <?php if($dir['predeterminada']): ?>
                                        <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-fp-success/10 text-fp-success uppercase tracking-wider">Predeterminada</span>
                                    <?php endif; ?>
                                </h4>
                                <p class="text-sm text-fp-text leading-relaxed">
                                    <?= htmlspecialchars($dir['direccion']) ?><br>
                                    <span class="text-fp-muted">Barrio:</span> <?= htmlspecialchars($dir['barrio'] ?: 'N/A') ?> — <span class="text-fp-muted">Ciudad:</span> <?= htmlspecialchars($dir['ciudad']) ?>
                                    <?php if (!empty($dir['referencia'])): ?>
                                        <br><span class="text-fp-muted text-[13px]"><i data-lucide="info" class="w-3 h-3 inline-block relative -top-px"></i> <?= htmlspecialchars($dir['referencia']) ?></span>
                                    <?php endif; ?>
                                </p>
                            </div>
                            
                            <form action="<?= $basePath ?? '' ?>/mi-cuenta/direcciones/<?= $dir['direccion_id'] ?>/eliminar" method="POST" onsubmit="return confirm('¿Seguro que deseas eliminar esta dirección?');" class="shrink-0 w-full md:w-auto mt-2 md:mt-0">
                                <button type="submit" class="w-full md:w-auto px-4 py-2 rounded-lg text-sm font-semibold text-fp-error bg-[#FDEDEC] hover:bg-[#FADBD8] border border-fp-error/20 transition-colors flex items-center justify-center gap-1.5 focus:ring-2 focus:ring-fp-error/20 outline-none">
                                    <i data-lucide="trash-2" class="w-4 h-4"></i> Eliminar
                                </button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

    </div>
</div>

<?php $contenido = ob_get_clean(); ?>
<?php include __DIR__ . '/../layouts/base_cliente.php'; ?>
