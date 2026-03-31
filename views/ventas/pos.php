<?php
$titulo = 'Punto de Venta';
// POS usa su propia estructura HTML.
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>FarmaPlus - Punto de Venta</title>
<link rel="stylesheet" href="<?= rtrim($_ENV['APP_BASEPATH'] ?? '', '/') ?>/assets/css/app.min.css?v=<?= time() ?>">
<script src="https://unpkg.com/lucide@latest"></script>
<link rel="icon" type="image/svg+xml" href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/public/assets/img/pill.svg">
</head>
<body class="bg-[#f8fafc] font-sans text-fp-text h-screen overflow-hidden flex flex-col">

<!-- Modal Venta Exitosa (Tailwind overlay modal) -->
<div id="modalSuccess" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-[100] hidden items-center justify-center p-4 transition-opacity">
  <div class="bg-white rounded-2xl shadow-xl w-full max-w-sm overflow-hidden flex flex-col transform scale-95 transition-transform" id="modalSuccessBox">
    
    <div class="p-6 pb-4 flex flex-col items-center border-b border-fp-border/50 text-center">
      <div class="w-16 h-16 bg-fp-success/10 rounded-full flex items-center justify-center mb-3">
        <i data-lucide="check" class="text-fp-success w-8 h-8"></i>
      </div>
      <h2 class="text-xl font-bold text-fp-text leading-tight">¡Venta Exitosa!</h2>
      <p class="text-sm text-fp-muted mt-1">Transacción registrada sin problemas.</p>
    </div>
    
    <div class="p-6 bg-fp-bg-main/30 flex flex-col gap-3">
      <div class="flex justify-between items-center text-sm border-b border-fp-border/50 pb-2">
        <span class="text-fp-muted font-medium">N.º comprobante</span>
        <span class="comprobante-num font-mono font-bold text-fp-text">FP-XXXX</span>
      </div>
      <div class="flex justify-between items-center text-sm border-b border-fp-border/50 pb-2">
        <span class="text-fp-muted font-medium">Vendedor</span>
        <span class="modal-seller-name font-semibold text-fp-text text-right truncate pl-4">Cargando...</span>
      </div>
      <div class="flex justify-between items-center text-sm border-b border-fp-border/50 pb-2">
        <span class="text-fp-muted font-medium">Método Pago</span>
        <span class="modal-pay-method font-semibold capitalize text-fp-text">Efectivo</span>
      </div>
      <div class="flex justify-between items-center pt-1">
        <span class="font-bold text-fp-text">Total Cobrado</span>
        <span class="text-xl font-bold text-fp-success modal-total-amount">$0</span>
      </div>
    </div>
    
    <div class="p-4 grid grid-cols-2 gap-3 border-t border-fp-border">
      <button class="flex items-center justify-center gap-2 px-4 py-2 bg-fp-bg-main text-fp-text font-semibold rounded-lg hover:bg-fp-border/50 transition-colors border border-fp-border/50" onclick="printComprobante()">
        <i data-lucide="printer" class="w-4 h-4"></i> Recibo
      </button>
      <button class="flex items-center justify-center gap-2 px-4 py-2 text-white bg-fp-primary hover:bg-fp-primary-dark font-semibold rounded-lg transition-colors" onclick="newSale()">
        <i data-lucide="plus" class="w-4 h-4"></i> Listo
      </button>
    </div>
    
  </div>
</div>

<!-- TOASTS -->
<div id="toastContainer" class="fixed top-20 right-6 z-[120] flex flex-col gap-2"></div>

<!-- ================= TOPBAR ================= -->
<header class="h-[60px] bg-white border-b border-fp-border flex items-center justify-between px-4 shrink-0 shadow-sm z-50">
  
  <div class="flex items-center h-full">
    <!-- Logo -->
    <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/dashboard" class="flex items-center gap-2.5 mr-6 border-r border-fp-border/50 pr-6 h-full text-fp-text hover:opacity-80 transition-opacity">
      <div class="w-8 h-8 bg-fp-secondary rounded-lg flex items-center justify-center">
        <i data-lucide="pill" class="text-white w-5 h-5"></i>
      </div>
      <span class="text-base font-bold tracking-tight">Farma<span class="text-fp-secondary">Plus</span></span>
    </a>
    
    <!-- Title -->
    <div class="hidden md:flex items-center gap-2 text-fp-text font-semibold text-[15px]">
      <i data-lucide="monitor" class="w-4 h-4 text-fp-primary"></i> Punto de Venta
    </div>
  </div>

  <div class="flex items-center h-full">
    <!-- Reloj -->
    <div class="flex items-center gap-2 text-sm text-fp-muted font-medium border-l border-fp-border/50 pl-6 mr-6 h-full">
      <i data-lucide="clock" class="w-4 h-4"></i> <span id="posTime">00:00</span>
    </div>

    <!-- Vendedor -->
    <div class="flex items-center gap-3">
      <div class="hidden sm:flex flex-col items-end">
        <span class="text-[13px] font-bold text-fp-text leading-none"><?= htmlspecialchars($_SESSION['nombres'] ?? '') ?></span>
        <span class="text-[11px] text-fp-muted mt-1 uppercase tracking-wider font-semibold">Vendedor</span>
      </div>
      <div class="w-8 h-8 rounded-full bg-fp-primary flex items-center justify-center text-white text-xs font-bold shadow-sm">
        <?= strtoupper(substr($_SESSION['nombres'] ?? 'U', 0, 1) . substr($_SESSION['apellidos'] ?? '', 0, 1)) ?>
      </div>
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/dashboard" class="ml-4 px-3 py-1.5 bg-fp-bg-main border border-fp-border/80 text-fp-text text-sm font-semibold rounded-lg hover:bg-fp-border/50 transition-colors flex items-center gap-2 shrink-0">
        <i data-lucide="log-out" class="w-4 h-4"></i> Salir
      </a>
    </div>
  </div>
</header>


<!-- ================= POS SHELL (Dos Paneles) ================= -->
<main class="flex-1 flex overflow-hidden">
  
  <!-- PANEL IZQUIERDO: CATÁLOGO -->
  <section class="flex-1 flex flex-col min-w-0 bg-white border-r border-fp-border shadow-[4px_0_24px_-10px_rgba(0,0,0,0.05)] z-10">
    
    <!-- Barra de Búsqueda Minimalista -->
    <div class="p-4 border-b border-fp-border/60 bg-white shrink-0 flex flex-col gap-3">
      <div class="flex items-center gap-3">
        <!-- Input de Texto -->
        <div class="relative flex-1">
          <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-5 h-5 text-fp-muted pointer-events-none"></i>
          <input type="text" id="posSearchInput" placeholder="Buscar medicamento o Cód. INVIMA..." class="w-full h-[46px] pl-10 pr-4 bg-[#f8fafc] border border-fp-border outline-none rounded-xl text-[15px] font-medium text-fp-text placeholder:text-slate-400 focus:border-fp-primary focus:bg-white focus:ring-4 focus:ring-fp-primary/10 transition-all shadow-sm" autofocus oninput="filterProducts()">
        </div>
        
        <!-- Select Categoría -->
        <div class="relative w-[180px] shrink-0">
          <i data-lucide="filter" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-fp-muted pointer-events-none"></i>
          <select id="posCatSelect" onchange="filterCat(this.value)" class="w-full h-[46px] pl-9 pr-8 bg-white border border-fp-border outline-none rounded-xl text-sm font-semibold text-fp-text appearance-none cursor-pointer focus:border-fp-primary focus:ring-4 focus:ring-fp-primary/10 transition-all shadow-sm">
            <option value="">Todas</option>
            <option value="Antibiótico">Antibiótico</option>
            <option value="Analgésico">Analgésico</option>
            <option value="Antihistamínico">Antihistamínico</option>
            <option value="Antidiabético">Antidiabético</option>
            <option value="Antiácido">Antiácido</option>
            <option value="Vitamina">Vitamina</option>
            <option value="Antihipertensivo">Antihipertensivo</option>
          </select>
          <div class="absolute right-3 top-1/2 -translate-y-1/2 pointer-events-none">
            <i data-lucide="chevron-down" class="w-4 h-4 text-fp-muted"></i>
          </div>
        </div>
      </div>
      <!-- Hint Minimalista -->
      <div class="flex items-start gap-1.5 text-[11px] text-fp-muted px-1 mt-0.5">
        <i data-lucide="info" class="w-3.5 h-3.5 shrink-0"></i>
        <span>Los medicamentos de control especial <strong class="text-fp-error font-semibold">siempre</strong> requerirán fórmula médica para finalizar el cobro.</span>
      </div>
    </div>

    <!-- Resultados en Grilla -->
    <div class="flex-1 overflow-y-auto p-4 bg-[#f8fafc]">
      <div id="posResults" class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 auto-rows-max gap-4 pb-20">
        <!-- Renderizados por JS -->
      </div>
    </div>

  </section>

  <!-- PANEL DERECHO: CARRITO -->
  <section class="w-[360px] lg:w-[400px] flex flex-col bg-white shrink-0 z-20">
    
    <!-- Titulo del Carrito -->
    <div class="h-[60px] px-5 border-b border-fp-border/60 flex items-center justify-between shrink-0 bg-white">
      <div class="flex items-center gap-2.5 font-bold text-fp-text text-lg">
        <i data-lucide="shopping-cart" class="w-5 h-5 text-fp-primary"></i> Caja
        <span id="cartCount" class="bg-fp-primary text-white text-[11px] px-2 py-0.5 rounded-full relative -top-[1px]">0</span>
      </div>
      <button onclick="clearCart()" class="text-[13px] font-semibold text-fp-muted hover:text-fp-error flex items-center gap-1.5 transition-colors" title="Vaciar Carrito">
        <i data-lucide="trash-2" class="w-4 h-4"></i> Vaciar
      </button>
    </div>

    <!-- Alerta Fórmula -->
    <div id="formulaAlert" class="hidden flex-col gap-2 p-4 mx-4 mt-4 bg-fp-error/5 border border-fp-error/20 rounded-xl relative overflow-hidden transition-all duration-300">
      <!-- Indicador rojo borde izquierdo decorativo -->
      <div class="absolute left-0 top-0 bottom-0 w-1 bg-fp-error"></div>
      
      <div class="flex items-center gap-2 text-fp-error font-bold text-sm">
        <i data-lucide="flask-conical" class="w-4 h-4"></i> Requiere Fórmula
      </div>
      <p class="text-[12px] text-fp-text/80 leading-relaxed font-medium">Hay productos en el carrito que exigen receta médica obligatoria.</p>
      <input type="text" id="formulaInput" placeholder="Nº o Cód. de Fórmula" class="mt-1 w-full h-[38px] px-3 bg-white border border-fp-error/30 rounded-lg text-sm text-fp-text outline-none focus:border-fp-error focus:ring-2 focus:ring-fp-error/20 placeholder:text-slate-400 font-mono shadow-sm">
    </div>

    <!-- Lista de Items -->
    <div id="cartItems" class="flex-1 overflow-y-auto w-full flex flex-col px-1 pt-2 pb-4">
      <!-- Items via JS -->
    </div>

    <!-- Footer Checkout (Fijo abajo) -->
    <div class="border-t border-fp-border/60 bg-white p-5 flex flex-col gap-4 shrink-0 shadow-[0_-4px_24px_rgba(0,0,0,0.03)] z-10">
      
      <!-- Cálculos -->
      <div class="flex flex-col gap-2.5">
        <div class="flex justify-between items-center text-[15px] text-fp-muted font-medium">
          <span>Subtotal</span>
          <span id="summarySubtotal" class="font-mono text-fp-text">$0</span>
        </div>
        
        <div class="flex justify-between items-center text-[15px] font-medium group">
          <span class="text-fp-muted">Descuento</span>
          <div class="flex items-center gap-2">
             <span id="discountChip" class="hidden text-xs font-bold text-fp-primary bg-fp-bg-card px-2 py-0.5 rounded-full border border-fp-primary/20">- $0</span>
             <div class="relative w-[110px]">
               <span class="absolute left-2.5 top-1/2 -translate-y-1/2 text-fp-muted font-mono text-sm">$</span>
               <input type="number" id="discountInput" value="0" min="0" oninput="recalcTotal()" placeholder="0" class="w-full h-8 pl-6 pr-2 bg-[#f8fafc] border border-fp-border rounded-lg text-sm font-mono text-right text-fp-text outline-none focus:border-fp-primary focus:bg-white transition-colors hover:border-fp-border hover:bg-white group-hover:border-slate-300">
             </div>
          </div>
        </div>

        <div class="w-full border-t border-dashed border-fp-border/80 my-1"></div>
        
        <div class="flex justify-between items-baseline mb-1">
          <span class="text-lg font-bold text-fp-text">Total a Pagar</span>
          <span id="grandTotal" class="text-3xl font-black text-fp-primary tracking-tight font-mono">$0</span>
        </div>
      </div>

      <!-- Radios de Método de pago -->
      <div class="flex flex-col gap-2">
        <span class="text-[11px] font-bold text-fp-muted uppercase tracking-wider">Modo de Pago</span>
        <div class="grid grid-cols-4 gap-2">
          
          <button id="pay-efectivo" onclick="selectPay('efectivo')" class="pay-btn active h-[46px] border-2 border-fp-primary bg-fp-bg-main text-fp-primary rounded-xl flex flex-col items-center justify-center gap-0.5 transition-all text-center">
            <i data-lucide="banknote" class="w-4 h-4"></i>
            <span class="text-[10px] font-bold leading-none">Cash</span>
          </button>
          
          <button id="pay-debito" onclick="selectPay('debito')" class="pay-btn h-[46px] border border-fp-border bg-white text-fp-muted rounded-xl flex flex-col items-center justify-center gap-0.5 transition-all hover:border-slate-300 hover:bg-slate-50">
            <i data-lucide="credit-card" class="w-4 h-4"></i>
            <span class="text-[10px] font-bold leading-none">Débito</span>
          </button>
          
          <button id="pay-credito" onclick="selectPay('credito')" class="pay-btn h-[46px] border border-fp-border bg-white text-fp-muted rounded-xl flex flex-col items-center justify-center gap-0.5 transition-all hover:border-slate-300 hover:bg-slate-50">
            <i data-lucide="credit-card" class="w-4 h-4 text-[#8e44ad]"></i>
            <span class="text-[10px] font-bold leading-none">Crédit</span>
          </button>
          
          <button id="pay-transferencia" onclick="selectPay('transferencia')" class="pay-btn h-[46px] border border-fp-border bg-white text-fp-muted rounded-xl flex flex-col items-center justify-center gap-0.5 transition-all hover:border-slate-300 hover:bg-slate-50">
            <i data-lucide="smartphone" class="w-4 h-4 text-fp-success"></i>
            <span class="text-[10px] font-bold leading-none">Trans.</span>
          </button>

        </div>
      </div>

      <!-- Main Action -->
      <button id="btnConfirmSale" onclick="confirmSale()" class="w-full h-[52px] bg-fp-success hover:bg-[#219653] text-white rounded-xl font-bold text-[17px] flex items-center justify-center gap-2.5 transition-transform active:scale-[0.98] shadow-[0_4px_12px_rgba(39,174,96,0.25)] border-b-4 border-black/10 hover:border-black/20 mt-1">
        <i data-lucide="check-circle" class="w-5 h-5"></i> Confirmar Venta
      </button>

    </div>

  </section>

</main>


<!-- ================= JAVASCRIPT ================= -->
<script>
lucide.createIcons();

/* ── Reloj ─────────────────────────────────────────────────── */
function updateClock() {
  const now = new Date();
  const h = String(now.getHours()).padStart(2,'0');
  const m = String(now.getMinutes()).padStart(2,'0');
  document.getElementById('posTime').textContent = `${h}:${m}`;
}
updateClock();
setInterval(updateClock, 30000);

/* ── Base URL para APIs ────────────────────────────────────── */
const BASE_PATH = '<?= $_ENV['APP_BASEPATH'] ?? '' ?>';

/* ── Estado del carrito ────────────────────────────────────── */
let catalogo = [];
let cart = [];
let selectedPayment = 'efectivo';
let searchQ   = '';

/* ── Formato COP ───────────────────────────────────────────── */
const COP = (n) => '$' + Number(n).toLocaleString('es-CO');

/* ── Fetch al Backend para buscar productos ────────────────── */
async function fetchProducts() {
  if (searchQ.length > 0 && searchQ.length < 2) return;
  
  const url = `${BASE_PATH}/ventas/buscar-producto?q=` + encodeURIComponent(searchQ);
  try {
    const res = await fetch(url);
    const data = await res.json();
    
    catalogo = data.map(p => ({
      id: p.producto_id,
      nombre: p.nombre,
      invima: p.codigo_invima,
      laboratorio: p.proveedor_nombre || 'Genérico',
      precio: parseFloat(p.precio_venta),
      stock: parseInt(p.stock_actual),
      ctrl: parseInt(p.control_especial) === 1,
      stockMin: parseInt(p.stock_minimo)
    }));
    
    renderProducts();
  } catch (err) {
    showToast('Error de red al cargar el catálogo.', 'error');
  }
}

/* ── Render Catálogo (Tailwind Cards) ──────────────────────── */
function renderProducts() {
  const container = document.getElementById('posResults');
  
  if (catalogo.length === 0) {
    container.innerHTML = `
      <div class="col-span-full flex flex-col items-center justify-center p-12 text-center h-full">
        <div class="w-16 h-16 bg-white border border-fp-border rounded-full flex items-center justify-center shadow-sm mb-4">
          <i data-lucide="search-x" class="text-fp-muted w-7 h-7"></i>
        </div>
        <h3 class="text-lg font-bold text-fp-text">Sin Resultados</h3>
        <p class="text-sm text-fp-muted mt-1">Intenta buscar con otra palabra clave o código.</p>
      </div>`;
    lucide.createIcons();
    return;
  }

  container.innerHTML = catalogo.map(p => {
    const isOutOfStock = p.stock === 0;
    const isLowStock = p.stock > 0 && p.stock < p.stockMin;
    
    // Status Styles
    let stockBadge = `<span class="inline-flex items-center gap-1 text-[11px] font-bold text-fp-success bg-fp-success/10 px-2 py-0.5 rounded border border-fp-success/20"><i data-lucide="package" class="w-3 h-3"></i> ${p.stock}</span>`;
    if(isOutOfStock) stockBadge = `<span class="inline-flex items-center gap-1 text-[11px] font-bold text-fp-error bg-fp-error/10 px-2 py-0.5 rounded border border-fp-error/20"><i data-lucide="ban" class="w-3 h-3"></i> 0</span>`;
    else if(isLowStock) stockBadge = `<span class="inline-flex items-center gap-1 text-[11px] font-bold text-fp-warning bg-fp-warning/10 px-2 py-0.5 rounded border border-fp-warning/20"><i data-lucide="alert-triangle" class="w-3 h-3"></i> ${p.stock}</span>`;

    return `
      <div class="bg-white border ${isOutOfStock ? 'border-fp-error/30 opacity-70 cursor-not-allowed' : 'border-fp-border/70 hover:border-fp-primary/50 hover:shadow-md cursor-pointer'} rounded-xl p-4 flex flex-col h-full transition-all group relative overflow-hidden" onclick="${isOutOfStock ? '' : `addToCart(${p.id})`}">
        
        ${p.ctrl ? `<div class="absolute top-0 right-0 bg-fp-error text-white text-[9px] font-bold px-2 py-0.5 rounded-bl-lg uppercase tracking-wider flex items-center gap-1 shadow-sm"><i data-lucide="flask-conical" class="w-2.5 h-2.5"></i> Control</div>` : ''}

        <div class="flex justify-between items-start mb-2">
          <div class="w-10 h-10 rounded-lg ${isOutOfStock ? 'bg-slate-100 text-slate-400' : 'bg-fp-bg-main text-fp-primary'} flex items-center justify-center shrink-0">
            <i data-lucide="pill" class="w-5 h-5"></i>
          </div>
          ${stockBadge}
        </div>
        
        <h4 class="text-[14px] font-bold text-fp-text leading-snug break-words mb-1">${p.nombre}</h4>
        <p class="text-[11px] text-fp-muted font-medium mb-auto">${p.laboratorio} · ${p.invima}</p>
        
        <div class="mt-4 pt-3 border-t border-dashed border-fp-border/50 flex items-center justify-between">
          <span class="text-base font-black text-fp-text font-mono">${COP(p.precio)}</span>
          <button class="w-7 h-7 rounded-md flex items-center justify-center transition-colors ${isOutOfStock ? 'bg-slate-100 text-slate-400' : 'bg-fp-primary/10 text-fp-primary group-hover:bg-fp-primary group-hover:text-white'}" ${isOutOfStock ? 'disabled' : ''}>
            <i data-lucide="${isOutOfStock ? 'ban' : 'plus'}" class="w-4 h-4"></i>
          </button>
        </div>
      </div>
    `;
  }).join('');

  lucide.createIcons();
}

/* ── Render Carrito (Tailwind Items) ───────────────────────── */
function renderCart() {
  const container = document.getElementById('cartItems');
  const countEl   = document.getElementById('cartCount');
  const hasCtrl   = cart.some(i => i.ctrl);
  const formulaEl = document.getElementById('formulaAlert');

  countEl.textContent = cart.length;
  
  if(hasCtrl) {
      formulaEl.classList.remove('hidden');
      formulaEl.classList.add('flex');
  } else {
      formulaEl.classList.add('hidden');
      formulaEl.classList.remove('flex');
      document.getElementById('formulaInput').value = ''; // Limpiar si lo quitaron
  }

  if (cart.length === 0) {
    container.innerHTML = `
      <div class="flex flex-col items-center justify-center text-center p-8 mt-10 opacity-70">
        <div class="w-16 h-16 bg-fp-bg-main rounded-full flex items-center justify-center mb-3 border border-dashed border-fp-border">
          <i data-lucide="shopping-cart" class="w-7 h-7 text-fp-muted"></i>
        </div>
        <p class="text-[15px] font-bold text-fp-text">Caja Vacía</p>
        <p class="text-xs text-fp-muted mt-1 leading-relaxed">Haz clic en los productos para<br>agregarlos al recibo.</p>
      </div>`;
    lucide.createIcons();
    recalcTotal();
    return;
  }

  container.innerHTML = cart.map((item, idx) => `
    <div class="bg-white border border-fp-border rounded-xl p-3 mx-4 my-2 flex flex-col gap-2 shadow-sm hover:border-fp-primary/30 transition-colors relative group">
      
      <div class="flex items-start justify-between gap-2">
        <div class="flex-1 min-w-0">
          <h5 class="text-[13px] font-bold text-fp-text leading-tight pr-6">${item.nombre}</h5>
          <div class="flex items-center gap-2 mt-1">
            <span class="text-[11px] text-fp-muted">${COP(item.precio)} c/u</span>
            ${item.ctrl ? `<span class="inline-flex items-center gap-0.5 px-1.5 py-0.5 rounded text-[9px] font-bold uppercase tracking-wider bg-fp-error/10 text-fp-error"><i data-lucide="flask-conical" class="w-2.5 h-2.5"></i> Rx</span>` : ''}
          </div>
        </div>
        <button class="absolute top-2 right-2 p-1.5 text-fp-border hover:text-fp-error hover:bg-fp-error/10 rounded-md transition-colors opacity-0 group-hover:opacity-100" onclick="removeFromCart(${idx})" title="Quitar">
          <i data-lucide="x" class="w-4 h-4"></i>
        </button>
      </div>

      <div class="flex items-center justify-between mt-1 pt-2 border-t border-fp-border border-dashed">
        
        <!-- Controles Cantidad Modernos -->
        <div class="flex items-center bg-[#f8fafc] border border-fp-border rounded-lg overflow-hidden h-8">
          <button class="w-8 h-full flex items-center justify-center text-fp-muted hover:text-fp-text hover:bg-slate-200 transition-colors" onclick="changeQty(${idx}, -1)">
            <i data-lucide="minus" class="w-3.5 h-3.5"></i>
          </button>
          <div class="w-8 h-full flex items-center justify-center font-bold text-[13px] text-fp-text bg-white border-l border-r border-fp-border font-mono pointer-events-none">${item.qty}</div>
          <button class="w-8 h-full flex items-center justify-center text-fp-primary hover:text-fp-primary-dark hover:bg-fp-primary/10 transition-colors" onclick="changeQty(${idx}, +1)">
            <i data-lucide="plus" class="w-3.5 h-3.5"></i>
          </button>
        </div>
        
        <!-- Línea Total -->
        <span class="text-[15px] font-black text-fp-text font-mono">${COP(item.precio * item.qty)}</span>
      </div>
      
    </div>
  `).join('');

  lucide.createIcons();
  recalcTotal();
}

/* ── Lógica Financiera ─────────────────────────────────────── */
function recalcTotal() {
  const subtotal  = cart.reduce((sum, i) => sum + i.precio * i.qty, 0);
  const discount  = parseInt(document.getElementById('discountInput').value) || 0;
  const total     = Math.max(0, subtotal - discount);
  const chipEl    = document.getElementById('discountChip');

  document.getElementById('summarySubtotal').textContent = COP(subtotal);
  document.getElementById('grandTotal').textContent      = COP(total);
  
  const btnIcon = `<i data-lucide="check-circle" class="w-5 h-5"></i>`;
  document.getElementById('btnConfirmSale').innerHTML = `${btnIcon} Cobrar · ${COP(total)}`;
  lucide.createIcons();

  if (discount > 0) {
    chipEl.textContent = `-${COP(discount)}`;
    chipEl.classList.remove('hidden');
  } else {
    chipEl.classList.add('hidden');
  }
}

/* ── Acciones CRUD Carrito ─────────────────────────────────── */
function addToCart(id) {
  const prod = catalogo.find(p => p.id === id);
  if (!prod || prod.stock === 0) return;

  const existing = cart.find(i => i.id === id);
  if (existing) {
    if (existing.qty < prod.stock) {
      existing.qty++;
    } else {
      showToast('Stock máximo alcanzado', 'warning');
      return;
    }
  } else {
    cart.push({ ...prod, qty: 1 });
  }
  
  renderCart();
}

function removeFromCart(idx) {
  cart.splice(idx, 1);
  renderCart();
}

function changeQty(idx, delta) {
  const item = cart[idx];
  const prod = catalogo.find(p => p.id === item.id);
  const newQty = item.qty + delta;
  if (newQty < 1) { removeFromCart(idx); return; }
  if (newQty > prod.stock) { showToast('Cuentas solo con ' + prod.stock + ' en Stock', 'warning'); return; }
  cart[idx].qty = newQty;
  renderCart();
}

function clearCart() {
  if (cart.length === 0) return;
  cart = [];
  renderCart();
  showToast('Caja vaciada', 'info');
}

/* ── Filtros Optimizado ────────────────────────────────────── */
let timeoutSearch;
function filterProducts() {
  clearTimeout(timeoutSearch);
  timeoutSearch = setTimeout(() => {
    searchQ = document.getElementById('posSearchInput').value.toLowerCase().trim();
    if(searchQ.length >= 2 || searchQ.length === 0) fetchProducts();
  }, 350);
}

function filterCat(cat) {
  searchQ = cat;
  document.getElementById('posSearchInput').value = cat;
  fetchProducts();
}

/* ── Pago & Checkout ───────────────────────────────────────── */
function selectPay(method) {
  selectedPayment = method;
  
  // Limpiar estilos activos de todos
  document.querySelectorAll('.pay-btn').forEach(btn => {
    btn.classList.remove('active', 'border-2', 'border-fp-primary', 'bg-fp-bg-main', 'text-fp-primary');
    btn.classList.add('border', 'border-fp-border', 'bg-white', 'text-fp-muted', 'hover:border-slate-300', 'hover:bg-slate-50');
  });
  
  // Asignar al seleccionado
  const selectedBtn = document.getElementById(`pay-${method}`);
  selectedBtn.classList.remove('border', 'border-fp-border', 'bg-white', 'text-fp-muted', 'hover:border-slate-300', 'hover:bg-slate-50');
  selectedBtn.classList.add('active', 'border-2', 'border-fp-primary', 'bg-fp-bg-main', 'text-fp-primary');
}

let processURL = "";

async function confirmSale() {
  if (cart.length === 0) {
    showToast('Agrega productos para continuar.', 'warning'); return;
  }
  
  const hasCtrl   = cart.some(i => i.ctrl);
  const formulaV  = document.getElementById('formulaInput').value.trim();
  
  if (hasCtrl && !formulaV) {
    showToast('Falta número de fórmula médica obligatoria.', 'error');
    document.getElementById('formulaInput').focus(); 
    document.getElementById('formulaAlert').classList.add('ring-2', 'ring-fp-error', 'ring-offset-2');
    setTimeout(() => document.getElementById('formulaAlert').classList.remove('ring-2', 'ring-fp-error', 'ring-offset-2'), 1000);
    return;
  }
  
  const btn = document.getElementById('btnConfirmSale');
  const oldText = btn.innerHTML;
  btn.disabled = true;
  btn.innerHTML = `<i data-lucide="loader-2" class="w-5 h-5 animate-spin"></i> Registrando...`;
  lucide.createIcons();

  const payload = {
    metodo_pago: selectedPayment,
    formula_medica: formulaV,
    items: cart.map(i => ({ producto_id: i.id, cantidad: i.qty }))
  };

  try {
    const res = await fetch(`${BASE_PATH}/ventas/pos/procesar`, {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify(payload)
    });
    
    const data = await res.json();
    if (data.success) {
      processURL = data.redirect;
      
      // Inyectar data en el modal (buscando spans)
      document.querySelector('.comprobante-num').textContent = data.comprobante;
      document.querySelector('.modal-seller-name').textContent = "<?= htmlspecialchars($_SESSION['nombres'] ?? '') ?>";
      document.querySelector('.modal-pay-method').textContent = selectedPayment;
      document.querySelector('.modal-total-amount').textContent = COP(Math.max(0, cart.reduce((sum, i) => sum + i.precio * i.qty, 0) - (parseInt(document.getElementById('discountInput').value)||0)));
      
      // Mostrar y animar Modal Tailwind
      const modal = document.getElementById('modalSuccess');
      const box = document.getElementById('modalSuccessBox');
      modal.classList.remove('hidden');
      modal.classList.add('flex');
      setTimeout(() => {
         box.classList.remove('scale-95');
         box.classList.add('scale-100');
      }, 10);
      
    } else {
      showToast(data.error || 'Operación declinada.', 'error');
      btn.disabled = false;
      btn.innerHTML = oldText;
      lucide.createIcons();
    }
  } catch (err) {
    showToast('Fallo en la conexión. Intenta de nuevo.', 'error');
    btn.disabled = false;
    btn.innerHTML = oldText;
    lucide.createIcons();
  }
}

/* ── Modal & Util ──────────────────────────────────────────── */
function printComprobante() {
  if(processURL) window.open(processURL, '_blank');
}

function newSale() {
  cart = [];
  document.getElementById('discountInput').value = '0';
  document.getElementById('formulaInput').value  = '';
  document.getElementById('posSearchInput').value = '';
  document.getElementById('posCatSelect').value = '';
  searchQ = '';
  
  // Cerrar Modal
  const modal = document.getElementById('modalSuccess');
  const box = document.getElementById('modalSuccessBox');
  box.classList.add('scale-95');
  box.classList.remove('scale-100');
  setTimeout(() => {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
  }, 200);

  // Restaurar Pay a Efectivo
  selectPay('efectivo');

  // Limpiar State
  renderCart();
  fetchProducts(); 
  document.getElementById('posSearchInput').focus();
  
  const btn = document.getElementById('btnConfirmSale');
  btn.disabled = false;
}

/* ── Modern Tailwind Toast ─────────────────────────────────── */
function showToast(msg, type = 'success') {
  const icons  = { success:'check-circle', warning:'alert-triangle', info:'info', error:'x-circle' };
  const bgs    = { success:'bg-fp-success', warning:'bg-fp-warning', info:'bg-fp-info', error:'bg-fp-error' };
  
  const container = document.getElementById('toastContainer');
  const toast = document.createElement('div');
  toast.className = `flex items-center gap-3 w-[320px] bg-white border-l-4 border-${type === 'error' ? 'fp-error' : (type==='warning' ? 'fp-warning' : 'fp-success')} p-3 rounded-lg shadow-lg transform translate-x-full opacity-0 transition-all duration-300`;
  
  toast.innerHTML = `
    <div class="w-8 h-8 rounded-full ${bgs[type]}/10 flex items-center justify-center shrink-0">
       <i data-lucide="${icons[type]}" class="w-4 h-4 text-${type === 'error' ? 'fp-error' : (type==='warning' ? 'fp-warning' : 'fp-success')}"></i>
    </div>
    <p class="text-[13px] font-semibold text-fp-text leading-snug break-words pr-2">${msg}</p>
  `;
  
  container.appendChild(toast);
  lucide.createIcons();
  
  // Animate In
  requestAnimationFrame(() => {
    toast.classList.remove('translate-x-full', 'opacity-0');
    toast.classList.add('translate-x-0', 'opacity-100');
  });
  
  // Animate Out
  setTimeout(() => {
    toast.classList.remove('translate-x-0', 'opacity-100');
    toast.classList.add('translate-x-full', 'opacity-0');
    setTimeout(() => toast.remove(), 300);
  }, 3500);
}

/* ── Init ──────────────────────────────────────────────────── */
fetchProducts(); 
renderCart();
</script>

</body>
</html>
