<?php
/**
 * views/inventario/productos.php
 * Catálogo de Productos - Refactorizado para usar `base.php` y Tailwind Responsive
 */
$titulo = 'Catálogo de Productos';
ob_start(); 
?>

<!-- Page Header -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
  <div>
    <h1 class="text-2xl font-bold text-fp-text tracking-tight">Catálogo de Productos</h1>
    <p class="text-[13px] text-fp-muted mt-0.5">Gestión de inventario y precios del catálogo farmacéutico</p>
  </div>
  <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/productos/crear" class="bg-fp-primary hover:bg-fp-primary-dark text-white text-sm font-bold py-2.5 px-4 rounded-lg flex items-center justify-center gap-2 shadow-sm transition-colors shrink-0">
    <i data-lucide="plus" class="w-4 h-4"></i> Nuevo producto
  </a>
</div>

<!-- Stats Row -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  <div class="bg-white rounded-xl border border-fp-border p-4 flex items-center gap-4 shadow-sm relative overflow-hidden group">
    <div class="absolute left-0 top-0 bottom-0 w-1 bg-slate-300"></div>
    <div class="w-12 h-12 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center shrink-0">
      <i data-lucide="package" class="w-6 h-6"></i>
    </div>
    <div>
      <div class="text-2xl font-black text-fp-text font-mono tracking-tight leading-none mb-0.5"><?= $totalProductos ?? 0 ?></div>
      <div class="text-[11px] font-bold text-fp-muted uppercase tracking-wider">Total productos</div>
    </div>
  </div>

  <div class="bg-white rounded-xl border border-fp-border p-4 flex items-center gap-4 shadow-sm relative overflow-hidden group">
    <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#27AE60]"></div>
    <div class="w-12 h-12 rounded-xl bg-[#27AE60]/10 text-[#27AE60] flex items-center justify-center shrink-0">
      <i data-lucide="circle-check" class="w-6 h-6"></i>
    </div>
    <div>
      <div class="text-2xl font-black text-[#27AE60] font-mono tracking-tight leading-none mb-0.5"><?= $productosStockOk ?? 0 ?></div>
      <div class="text-[11px] font-bold text-[#27AE60] uppercase tracking-wider">Con stock OK</div>
    </div>
  </div>

  <div class="bg-white rounded-xl border border-fp-border p-4 flex items-center gap-4 shadow-sm relative overflow-hidden group">
    <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#F2C94C]"></div>
    <div class="w-12 h-12 rounded-xl bg-[#F2C94C]/10 text-[#E2B93B] flex items-center justify-center shrink-0">
      <i data-lucide="alert-triangle" class="w-6 h-6"></i>
    </div>
    <div>
      <div class="text-2xl font-black text-[#E2B93B] font-mono tracking-tight leading-none mb-0.5"><?= $productosStockBajo ?? 0 ?></div>
      <div class="text-[11px] font-bold text-[#E2B93B] uppercase tracking-wider">Con stock bajo</div>
    </div>
  </div>

  <div class="bg-white rounded-xl border border-fp-border p-4 flex items-center gap-4 shadow-sm relative overflow-hidden group">
    <div class="absolute left-0 top-0 bottom-0 w-1 bg-[#2D9CDB]"></div>
    <div class="w-12 h-12 rounded-xl bg-[#2D9CDB]/10 text-[#2D9CDB] flex items-center justify-center shrink-0">
      <i data-lucide="dollar-sign" class="w-6 h-6"></i>
    </div>
    <div>
      <div class="text-2xl font-black text-[#2D9CDB] font-mono tracking-tight leading-none mb-0.5">$<?= number_format($valorInventario ?? 0, 1) ?>M</div>
      <div class="text-[11px] font-bold text-[#2D9CDB] uppercase tracking-wider">Valor inventario COP</div>
    </div>
  </div>
</div>

<!-- Alert Banner -->
<?php if (($productosStockBajo ?? 0) > 0): ?>
<div class="bg-[#FEF9E7] border border-[#F1C40F]/30 rounded-xl p-4 mb-6 flex flex-col sm:flex-row sm:items-center gap-4">
  <div class="w-10 h-10 rounded-full bg-[#F1C40F]/20 text-[#D4AC0D] flex items-center justify-center shrink-0">
    <i data-lucide="alert-triangle" class="w-5 h-5"></i>
  </div>
  <div class="flex-1 min-w-0">
    <div class="text-sm text-[#7D6608] mb-1">
      <strong><?= $productosStockBajo ?> productos con stock bajo</strong> — 
      <?php
      $nombres = array_slice(array_column($productosAlerta ?? [], 'nombre'), 0, 3);
      echo htmlspecialchars(implode(', ', $nombres));
      if (count($productosAlerta ?? []) > 3) echo ' y ' . (count($productosAlerta) - 3) . ' más';
      ?>
      requieren reabastecimiento inmediato.
    </div>
  </div>
  <div class="flex items-center gap-3 shrink-0">
    <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/alertas" class="text-sm font-bold text-[#D4AC0D] hover:underline">Ver alertas</a>
    <button class="px-3 py-1.5 bg-white border border-[#D4AC0D]/30 text-[#D4AC0D] rounded-lg text-[13px] font-bold hover:bg-[#D4AC0D]/10 transition-colors" onclick="exportarAlertas()">Exportar</button>
  </div>
</div>
<?php endif; ?>

<!-- Toolbar (Responsive Stack) -->
<div class="flex flex-col md:flex-row flex-wrap gap-3 mb-6 bg-white p-3 rounded-xl border border-fp-border shadow-sm">
  <div class="relative flex-1 min-w-[200px]">
    <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-fp-muted"></i>
    <input type="text" id="searchInput" class="w-full h-10 pl-9 pr-3 bg-fp-bg-main border border-fp-border rounded-lg text-[13px] outline-none focus:border-fp-primary focus:ring-2 focus:ring-fp-primary/20" placeholder="Buscar por nombre, INVIMA o principio activo..." />
  </div>
  <select id="filterCategoria" class="h-10 px-3 bg-white border border-fp-border rounded-lg text-[13px] font-medium text-fp-text outline-none focus:border-fp-primary w-full md:w-auto">
    <option value="">Todas las categorías</option>
    <?php foreach ($categorias ?? [] as $cat): ?>
    <option value="<?= $cat['categoria_id'] ?>"><?= htmlspecialchars($cat['nombre']) ?></option>
    <?php endforeach; ?>
  </select>
  <select id="filterLaboratorio" class="h-10 px-3 bg-white border border-fp-border rounded-lg text-[13px] font-medium text-fp-text outline-none focus:border-fp-primary w-full md:w-auto">
    <option value="">Todos los laboratorios</option>
    <?php foreach ($proveedores ?? [] as $prov): ?>
    <option value="<?= $prov['proveedor_id'] ?>"><?= htmlspecialchars($prov['nombre']) ?></option>
    <?php endforeach; ?>
  </select>
  <select id="filterEstado" class="h-10 px-3 bg-white border border-fp-border rounded-lg text-[13px] font-medium text-fp-text outline-none focus:border-fp-primary w-full md:w-auto">
    <option value="">Todos los estados</option>
    <option value="activo">Activo</option>
    <option value="inactivo">Inactivo</option>
  </select>
  <label class="flex items-center gap-2 cursor-pointer h-10 px-3 border border-fp-border rounded-lg hover:bg-fp-bg-main transition-colors w-full md:w-auto">
    <input type="checkbox" id="filterSoloAlertas" class="w-4 h-4 text-fp-primary rounded" />
    <span class="text-[13px] font-medium text-fp-text">Solo alertas</span>
  </label>
  <button class="h-10 px-4 flex items-center justify-center gap-2 bg-white border border-fp-border hover:border-fp-primary hover:text-fp-primary rounded-lg text-[13px] font-bold transition-colors w-full md:w-auto" onclick="exportarCatalogo()">
    <i data-lucide="download" class="w-4 h-4"></i> Exportar
  </button>
</div>

<!-- Tabla de Productos Responsiva -->
<div class="bg-white rounded-xl border border-fp-border flex flex-col shadow-sm">
  <div class="p-4 border-b border-fp-border flex items-center justify-between bg-fp-bg-main/30">
    <div class="flex items-center gap-2 font-bold text-[15px] text-fp-text">
      <i data-lucide="package" class="w-5 h-5 text-fp-primary"></i> Catálogo
      <span class="text-[12px] font-bold text-fp-muted font-mono bg-white px-2 py-0.5 rounded border border-fp-border">(<?= count($productos ?? []) ?>)</span>
    </div>
    <div class="flex items-center border border-fp-border rounded-lg overflow-hidden shrink-0">
      <button id="btnVistaTabla" onclick="setView('tabla')" class="px-3 py-1.5 bg-fp-primary text-white text-[13px] font-bold flex items-center gap-1.5 transition-colors" title="Vista de tabla">
        <i data-lucide="list" class="w-4 h-4"></i><span class="hidden sm:inline">Tabla</span>
      </button>
      <button id="btnVistaGrid" onclick="setView('grid')" class="px-3 py-1.5 bg-fp-bg-main hover:bg-fp-bg-card text-fp-muted hover:text-fp-primary text-[13px] font-bold flex items-center gap-1.5 transition-colors border-l border-fp-border" title="Vista de cuadrícula">
        <i data-lucide="layout-grid" class="w-4 h-4"></i><span class="hidden sm:inline">Cuadrícula</span>
      </button>
    </div>
  </div>

  <div class="w-full overflow-x-auto" id="tableWrapper">
    <table id="productosTable" class="w-full text-left border-collapse min-w-[900px]">
      <thead>
        <tr class="bg-white border-b border-fp-border text-[11px] uppercase tracking-[1.5px] font-bold text-fp-muted">
          <th class="px-5 py-4 w-[250px]">Nombre Comercial</th>
          <th class="px-5 py-4">P. Activo</th>
          <th class="px-5 py-4">Laboratorio</th>
          <th class="px-5 py-4">Categoría</th>
          <th class="px-5 py-4 w-[140px]">Stock</th>
          <th class="px-5 py-4 w-[120px]">Precio V.</th>
          <th class="px-5 py-4 text-center">Estado</th>
          <th class="px-5 py-4 text-right">Acciones</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-fp-border/50 text-[13px] font-medium text-fp-text">
        <?php if (empty($productos)): ?>
        <tr>
          <td colspan="8" class="p-12 text-center text-fp-muted">
            <div class="w-16 h-16 rounded-full bg-fp-bg-main flex items-center justify-center mx-auto mb-3"><i data-lucide="package-x" class="w-8 h-8 opacity-50"></i></div>
            <div class="font-bold text-[15px] text-fp-text">No hay productos registrados</div>
            <div class="text-[13px]">Comienza registrando tu primer producto farmacéutico</div>
          </td>
        </tr>
        <?php else: ?>
          <?php foreach ($productos as $p): 
            $stockActual = (int)($p['stock_actual'] ?? 0);
            $stockMinimo = (int)($p['stock_minimo'] ?? 10);
            $tieneAlerta = $stockActual <= $stockMinimo;
            $porcentaje = $stockMinimo > 0 ? min(100, ($stockActual / $stockMinimo) * 100) : 100;
            $estadoStock = $stockActual === 0 ? 'bg-[#E74C3C]' : ($tieneAlerta ? 'bg-[#F2C94C]' : 'bg-[#27AE60]');
            $textStock = $stockActual === 0 ? 'text-[#E74C3C]' : ($tieneAlerta ? 'text-[#D4AC0D]' : 'text-[#27AE60]');
          ?>
        <tr class="<?= $tieneAlerta ? 'bg-[#FEF9E7]/30 row-alert' : 'hover:bg-fp-bg-main/50' ?> transition-colors">
          <td class="px-5 py-3">
            <div class="flex items-center gap-3">
              <div class="w-9 h-9 rounded-full <?= $tieneAlerta ? 'bg-[#F2C94C]/20 text-[#D4AC0D]' : 'bg-fp-primary/10 text-fp-primary' ?> flex items-center justify-center shrink-0">
                <i data-lucide="<?= $tieneAlerta ? 'alert-triangle' : 'pill' ?>" class="w-4 h-4"></i>
              </div>
              <div class="min-w-0">
                <div class="font-bold text-[13px] text-fp-text truncate" title="<?= htmlspecialchars($p['nombre']) ?>"><?= htmlspecialchars($p['nombre']) ?></div>
                <div class="text-[11px] text-fp-muted font-mono mt-0.5">INV: <?= htmlspecialchars($p['codigo_invima']) ?></div>
              </div>
            </div>
          </td>
          <td class="px-5 py-3 truncate max-w-[150px]" title="<?= htmlspecialchars($p['principio_activo'] ?: '—') ?>"><?= htmlspecialchars($p['principio_activo'] ?: '—') ?></td>
          <td class="px-5 py-3">
            <span class="inline-block px-2 py-0.5 border border-fp-border rounded bg-fp-bg-main text-[11px] font-semibold truncate max-w-[120px]" title="<?= htmlspecialchars($p['proveedor_nombre'] ?? 'General') ?>"><?= htmlspecialchars($p['proveedor_nombre'] ?? 'General') ?></span>
          </td>
          <td class="px-5 py-3">
            <span class="inline-block px-2 py-0.5 border border-[#8E44AD]/20 rounded bg-[#8E44AD]/10 text-[#8E44AD] text-[11px] font-bold uppercase tracking-wider truncate max-w-[100px]"><?= htmlspecialchars($p['categoria_nombre'] ?? 'S/C') ?></span>
          </td>
          <td class="px-5 py-3">
            <div class="flex items-center gap-2 mb-1">
              <span class="font-bold font-mono text-[14px] <?= $textStock ?>"><?= $stockActual ?></span>
              <span class="text-[10px] text-fp-muted">/ mín <?= $stockMinimo ?></span>
            </div>
            <div class="h-1.5 w-full bg-fp-border rounded-full overflow-hidden">
              <div class="h-full <?= $estadoStock ?>" style="width: <?= $porcentaje ?>%"></div>
            </div>
          </td>
          <td class="px-5 py-3">
            <div class="font-bold font-mono text-[#27AE60] text-[14px]">$<?= number_format($p['precio_venta'], 0, ',', '.') ?></div>
            <?php $margen = $p['precio_compra'] > 0 ? (($p['precio_venta'] - $p['precio_compra']) / $p['precio_compra']) * 100 : 0; ?>
            <div class="text-[10px] font-bold text-fp-muted mt-0.5">+<?= number_format($margen, 0) ?>% MG</div>
          </td>
          <td class="px-5 py-3 text-center">
            <?php if ($p['activo']): ?>
              <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-[#27AE60]/10 text-[#27AE60] text-[10px] font-bold uppercase tracking-wider"><i data-lucide="check-circle" class="w-3 h-3"></i> Activo</span>
            <?php else: ?>
              <span class="inline-flex items-center gap-1 px-2 py-1 rounded bg-fp-muted/10 text-fp-muted text-[10px] font-bold uppercase tracking-wider"><i data-lucide="circle-x" class="w-3 h-3"></i> Inactivo</span>
            <?php endif; ?>
          </td>
          <td class="px-5 py-3">
            <div class="flex items-center justify-end gap-1.5">
              <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/productos/<?= $p['producto_id'] ?>" class="w-8 h-8 rounded-lg flex items-center justify-center bg-fp-bg-main text-fp-text hover:bg-fp-primary hover:text-white transition-colors" title="Ver detalle"><i data-lucide="eye" class="w-4 h-4"></i></a>
              <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/productos/<?= $p['producto_id'] ?>/editar" class="w-8 h-8 rounded-lg flex items-center justify-center bg-fp-bg-main text-fp-text hover:bg-[#F2C94C] hover:text-[#9C8110] transition-colors" title="Editar"><i data-lucide="pencil" class="w-4 h-4"></i></a>
              <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/lotes/registrar?producto_id=<?= $p['producto_id'] ?>" class="w-8 h-8 rounded-lg flex items-center justify-center bg-fp-bg-main text-fp-text hover:bg-[#8E44AD] hover:text-white transition-colors" title="Registrar lote"><i data-lucide="package-plus" class="w-4 h-4"></i></a>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <!-- Vista Cuadrícula (Oculta por defecto) -->
  <div id="gridWrapper" class="hidden p-4 bg-fp-bg-main/20">
    <?php if (empty($productos)): ?>
      <div class="p-12 text-center text-fp-muted w-full">
        <div class="w-16 h-16 rounded-full bg-fp-bg-main flex items-center justify-center mx-auto mb-3"><i data-lucide="package-x" class="w-8 h-8 opacity-50"></i></div>
        <div class="font-bold text-[15px] text-fp-text">No hay productos registrados</div>
        <div class="text-[13px]">Comienza registrando tu primer producto farmacéutico</div>
      </div>
    <?php else: ?>
      <!-- Uso de Grid auto-fill para ocupar todo el espacio disponible equitativamente sin dejar huecos -->
      <div class="grid gap-4 items-start" style="grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));" id="gridContainer">
        <?php foreach ($productos as $p): 
          $stockActual = (int)($p['stock_actual'] ?? 0);
          $stockMinimo = (int)($p['stock_minimo'] ?? 10);
          $tieneAlerta = $stockActual <= $stockMinimo;
          $porcentaje = $stockMinimo > 0 ? min(100, ($stockActual / $stockMinimo) * 100) : 100;
          $estadoStock = $stockActual === 0 ? 'bg-[#E74C3C]' : ($tieneAlerta ? 'bg-[#F2C94C]' : 'bg-[#27AE60]');
          $textStock = $stockActual === 0 ? 'text-[#E74C3C]' : ($tieneAlerta ? 'text-[#D4AC0D]' : 'text-[#27AE60]');
          $margen = $p['precio_compra'] > 0 ? (($p['precio_venta'] - $p['precio_compra']) / $p['precio_compra']) * 100 : 0;
          
          // Color de categoría
          $catLower = strtolower($p['categoria_nombre'] ?? '');
          $catColorClass = 'bg-[#8E44AD]/10 text-[#8E44AD]'; // default
          if (str_contains($catLower, 'antibiótico')) $catColorClass = 'bg-[#FDEDEC] text-[#C0392B]';
          elseif (str_contains($catLower, 'analgésico')) $catColorClass = 'bg-[#EBF5FB] text-[#1A6B8A]';
          elseif (str_contains($catLower, 'antihipertensivo')) $catColorClass = 'bg-[#F4ECF7] text-[#8E44AD]';
          elseif (str_contains($catLower, 'antidiabético')) $catColorClass = 'bg-[#FEF9E7] text-[#D35400]';
          elseif (str_contains($catLower, 'gastroprotector')) $catColorClass = 'bg-[#EAFAF1] text-[#1E8449]';
          elseif (str_contains($catLower, 'antihistamínico')) $catColorClass = 'bg-[#E8F8F5] text-[#1ABC9C]';
        ?>
        <article class="bg-white border <?= $tieneAlerta ? 'border-fp-error/30' : 'border-fp-border' ?> rounded-2xl overflow-hidden flex flex-col hover:shadow-xl hover:border-fp-primary/50 hover:-translate-y-0.5 transition-all grid-item" data-name="<?= htmlspecialchars(strtolower($p['nombre'])) ?>">
          
          <!-- Header -->
          <div class="p-4 border-b border-fp-bg-main relative flex items-start gap-3">
            <div class="w-11 h-11 rounded-xl flex items-center justify-center shrink-0 <?= $tieneAlerta ? 'bg-fp-error/10 text-fp-error' : 'bg-fp-primary/10 text-fp-primary' ?>">
              <i data-lucide="<?= $tieneAlerta ? 'alert-triangle' : 'pill' ?>" class="w-5 h-5"></i>
            </div>
            <div class="flex-1 min-w-0 pr-2">
              <div class="font-bold text-[15px] text-fp-text leading-tight truncate" title="<?= htmlspecialchars($p['nombre']) ?>"><?= htmlspecialchars($p['nombre']) ?></div>
              <div class="font-mono text-[11px] text-fp-muted mt-1 tracking-wide">INV: <?= htmlspecialchars($p['codigo_invima']) ?></div>
              <div class="flex flex-wrap gap-1.5 mt-2">
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold <?= $catColorClass ?> truncate max-w-[120px]"><?= htmlspecialchars($p['categoria_nombre'] ?? 'S/C') ?></span>
                <?php if (!empty($p['control_especial']) || $p['es_medicamento']): ?>
                  <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-bold bg-[#3498DB]/10 text-[#3498DB] whitespace-nowrap"><i data-lucide="flask-conical" class="w-3 h-3"></i> F. Médica</span>
                <?php endif; ?>
              </div>
            </div>
            <span class="absolute top-4 right-4 w-2.5 h-2.5 rounded-full shadow-[0_0_0_2px] <?= $p['activo'] ? 'bg-fp-success shadow-fp-success/20' : 'bg-fp-error shadow-fp-error/20' ?>" title="<?= $p['activo'] ? 'Activo' : 'Inactivo' ?>"></span>
          </div>

          <!-- Body -->
          <div class="p-4 flex-1 flex flex-col gap-3">
            <div class="flex items-center justify-between gap-2">
              <span class="text-[11px] font-bold uppercase tracking-wider text-fp-muted">Principio</span>
              <span class="text-[13px] font-semibold text-fp-text text-right truncate" title="<?= htmlspecialchars($p['principio_activo']) ?>"><?= htmlspecialchars($p['principio_activo'] ?: '—') ?></span>
            </div>
            <div class="flex items-center justify-between gap-2">
              <span class="text-[11px] font-bold uppercase tracking-wider text-fp-muted">Laboratorio</span>
              <span class="text-[13px] font-semibold text-fp-text text-right truncate" title="<?= htmlspecialchars($p['proveedor_nombre']) ?>"><?= htmlspecialchars($p['proveedor_nombre'] ?? 'General') ?></span>
            </div>
            <div class="flex items-end justify-between gap-2 mt-1">
              <span class="text-[11px] font-bold uppercase tracking-wider text-fp-muted mb-1">Precio Venta</span>
              <div class="text-right">
                <div class="text-[20px] font-black leading-none text-fp-success font-mono">$<?= number_format($p['precio_venta'], 0, ',', '.') ?></div>
                <div class="text-[11px] font-bold text-fp-success mt-1.5">+<?= number_format($margen, 0) ?>% margen</div>
              </div>
            </div>

            <!-- Stock Section -->
            <div class="bg-fp-bg-main rounded-xl p-3 mt-1 border border-fp-border/50">
              <div class="flex items-baseline gap-1.5 mb-2">
                <span class="text-[20px] font-black leading-none font-mono <?= $textStock ?>"><?= $stockActual ?></span>
                <span class="text-[11px] font-medium text-fp-muted">uds. · mín. <?= $stockMinimo ?></span>
              </div>
              <div class="h-1.5 w-full bg-slate-200 rounded-full overflow-hidden mb-2">
                <div class="h-full <?= $estadoStock ?> transition-all duration-500" style="width: <?= $porcentaje ?>%"></div>
              </div>
              <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[11px] font-bold <?= $stockActual === 0 ? 'bg-fp-error/10 text-fp-error' : ($tieneAlerta ? 'bg-[#F2C94C]/10 text-[#D4AC0D]' : 'bg-fp-success/10 text-fp-success') ?>">
                <i data-lucide="<?= $tieneAlerta || $stockActual === 0 ? 'alert-triangle' : 'circle-check' ?>" class="w-3 h-3"></i>
                <?= $stockActual === 0 ? 'Sin stock' : ($tieneAlerta ? 'Stock bajo' : 'OK') ?>
              </span>
            </div>
          </div>

          <!-- Footer Actions -->
          <div class="p-3 border-t border-fp-bg-main flex gap-2 bg-slate-50/50">
            <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/productos/<?= $p['producto_id'] ?>" class="flex-1 h-9 rounded-lg bg-fp-primary text-white flex items-center justify-center gap-1.5 text-[12px] font-bold hover:bg-fp-primary-dark transition-colors shadow-sm">
              <i data-lucide="eye" class="w-4 h-4"></i> Ver
            </a>
            <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/productos/<?= $p['producto_id'] ?>/editar" class="flex-1 h-9 rounded-lg border border-fp-border bg-white text-fp-text flex items-center justify-center gap-1.5 text-[12px] font-bold hover:border-fp-primary hover:text-fp-primary transition-colors shadow-sm">
              <i data-lucide="pencil" class="w-4 h-4"></i> Editar
            </a>
            <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/inventario/lotes/registrar?producto_id=<?= $p['producto_id'] ?>" class="flex-1 h-9 rounded-lg border border-fp-border bg-white text-fp-text flex items-center justify-center gap-1.5 text-[12px] font-bold hover:border-[#8E44AD] hover:text-[#8E44AD] transition-colors shadow-sm">
              <i data-lucide="package" class="w-4 h-4"></i> Stock
            </a>
          </div>
        </article>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
// Filtros JS Modernos (Tabla y Cuadrícula)
document.getElementById('searchInput')?.addEventListener('input', function(e) {
  const termino = e.target.value.toLowerCase();
  
  // Filtrar Tabla
  const filas = document.querySelectorAll('#productosTable tbody tr');
  filas.forEach(fila => {
    const texto = fila.textContent.toLowerCase();
    fila.style.display = texto.includes(termino) ? '' : 'none';
  });

  // Filtrar Cuadrícula
  const gridItems = document.querySelectorAll('.grid-item');
  gridItems.forEach(item => {
    const texto = item.textContent.toLowerCase();
    item.style.display = texto.includes(termino) ? '' : 'none';
  });
});

['filterCategoria', 'filterLaboratorio', 'filterEstado', 'filterSoloAlertas'].forEach(id => {
  document.getElementById(id)?.addEventListener('change', aplicarFiltros);
});

function aplicarFiltros() {
  const soloAlertas = document.getElementById('filterSoloAlertas')?.checked;
  
  // Filtrar Tabla
  const filas = document.querySelectorAll('#productosTable tbody tr');
  filas.forEach(fila => {
    let mostrar = true;
    if (soloAlertas && !fila.classList.contains('row-alert')) mostrar = false;
    fila.style.display = mostrar ? '' : 'none';
  });

  // Filtrar Cuadrícula
  const gridItems = document.querySelectorAll('.grid-item');
  gridItems.forEach(item => {
    let mostrar = true;
    // La tarjeta del grid tiene la clase border-fp-error/30 cuando hay alerta
    if (soloAlertas && !item.classList.contains('border-fp-error/30')) mostrar = false;
    item.style.display = mostrar ? '' : 'none';
  });
}

function exportarCatalogo() { window.location.href = '/inventario/productos/exportar'; }
function exportarAlertas() { window.location.href = '/inventario/alertas/exportar'; }

// Toggle View (Tabla / Cuadrícula)
let currentView = localStorage.getItem('fp_productos_view') || 'tabla';

function setView(view) {
  currentView = view;
  localStorage.setItem('fp_productos_view', view);
  
  const tableWrap = document.getElementById('tableWrapper');
  const gridWrap  = document.getElementById('gridWrapper');
  const btnTabla  = document.getElementById('btnVistaTabla');
  const btnGrid   = document.getElementById('btnVistaGrid');

  if (view === 'grid') {
    tableWrap.classList.add('hidden');
    gridWrap.classList.remove('hidden');
    
    btnTabla.classList.replace('bg-fp-primary', 'bg-fp-bg-main');
    btnTabla.classList.replace('text-white', 'text-fp-muted');
    
    btnGrid.classList.replace('bg-fp-bg-main', 'bg-fp-primary');
    btnGrid.classList.replace('text-fp-muted', 'text-white');
  } else {
    tableWrap.classList.remove('hidden');
    gridWrap.classList.add('hidden');
    
    btnGrid.classList.replace('bg-fp-primary', 'bg-fp-bg-main');
    btnGrid.classList.replace('text-white', 'text-fp-muted');
    
    btnTabla.classList.replace('bg-fp-bg-main', 'bg-fp-primary');
    btnTabla.classList.replace('text-fp-muted', 'text-white');
  }
}

// Inicializar vista guardada
document.addEventListener('DOMContentLoaded', () => {
  setView(currentView);
});
</script>

<?php 
$contenido = ob_get_clean(); 
require __DIR__ . '/../layouts/base.php'; 
?>
