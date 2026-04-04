<aside class="sidebar bg-fp-primary-dark fixed top-0 left-0 h-screen flex flex-col z-50 transition-transform duration-300 w-[240px] -translate-x-full lg:translate-x-0" id="sidebarCliente" role="navigation" aria-label="Navegación principal">
  
  <!-- Logo -->
  <div class="flex items-center justify-between px-4 pt-6 pb-5 border-b border-white/10 shrink-0">
    <div class="flex items-center gap-3">
        <div class="w-9 h-9 bg-fp-secondary rounded-lg flex items-center justify-center">
        <i data-lucide="pill" class="text-white w-5 h-5"></i>
        </div>
        <span class="text-[17px] font-bold text-white tracking-tight">Farma<span class="text-fp-secondary">Plus</span></span>
    </div>
    <button onclick="toggleSidebarCliente()" class="lg:hidden text-white/70 hover:text-white p-1 rounded-md transition-colors"><i data-lucide="x" class="w-5 h-5"></i></button>
  </div>

  <!-- Menú de Navegación -->
  <nav class="flex-1 px-3 py-4 overflow-y-auto no-scrollbar">
    
    <span class="block text-[10px] font-semibold uppercase tracking-[1.5px] text-[#ecf0f159] px-2 mb-2">Mi Cuenta</span>
    
    <a href="<?= $basePath ?>/mi-cuenta" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg cursor-pointer text-white/85 text-sm font-medium transition-colors hover:bg-fp-primary hover:text-white <?= (strpos($_SERVER['REQUEST_URI'], '/mi-cuenta') !== false && strpos($_SERVER['REQUEST_URI'], '/pedidos') === false && strpos($_SERVER['REQUEST_URI'], '/direcciones') === false) ? 'bg-fp-secondary text-white' : '' ?>">
      <i data-lucide="user" class="w-[18px] h-[18px] shrink-0 opacity-85"></i> Mi Perfil
    </a>

    <div class="h-[1px] bg-white/5 mx-3 my-2"></div>

    <span class="block text-[10px] font-semibold uppercase tracking-[1.5px] text-[#ecf0f159] px-2 mt-4 mb-2">Tienda</span>

    <a href="<?= $basePath ?>/tienda" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg cursor-pointer text-white/85 text-sm font-medium transition-colors hover:bg-fp-primary hover:text-white">
      <i data-lucide="store" class="w-[18px] h-[18px] shrink-0 opacity-85"></i> Catálogo de Productos
    </a>

    <div class="h-[1px] bg-white/5 mx-3 my-2"></div>

    <span class="block text-[10px] font-semibold uppercase tracking-[1.5px] text-[#ecf0f159] px-2 mt-4 mb-2">Mis Compras</span>

    <a href="<?= $basePath ?>/mi-cuenta/pedidos" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg cursor-pointer text-white/85 text-sm font-medium transition-colors hover:bg-fp-primary hover:text-white <?= (strpos($_SERVER['REQUEST_URI'], '/pedidos') !== false) ? 'bg-fp-secondary text-white' : '' ?>">
      <i data-lucide="shopping-bag" class="w-[18px] h-[18px] shrink-0 opacity-85"></i> Historial de Pedidos
    </a>
    
    <a href="<?= $basePath ?>/mi-cuenta/direcciones" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg cursor-pointer text-white/85 text-sm font-medium transition-colors hover:bg-fp-primary hover:text-white <?= (strpos($_SERVER['REQUEST_URI'], '/direcciones') !== false) ? 'bg-fp-secondary text-white' : '' ?>">
      <i data-lucide="map-pin" class="w-[18px] h-[18px] shrink-0 opacity-85"></i> Mis Direcciones
    </a>

  </nav>

  <!-- Footer con Usuario -->
  <div class="p-3 border-t border-white/10 shrink-0">
    <div class="flex items-center gap-2.5 p-2.5 rounded-lg transition-colors hover:bg-white/5">
      
      <div class="w-[34px] h-[34px] rounded-full bg-fp-primary flex items-center justify-center text-[13px] font-bold text-white shrink-0">
        <?= strtoupper(substr($_SESSION['nombres'] ?? 'C', 0, 1) . substr($_SESSION['apellidos'] ?? 'L', 0, 1)) ?>
      </div>
      
      <div class="flex-1 min-w-0">
        <div class="text-[13px] font-semibold text-white whitespace-nowrap overflow-hidden text-ellipsis">
          <?= htmlspecialchars($_SESSION['nombres'] ?? '') ?> <?= htmlspecialchars($_SESSION['apellidos'] ?? '') ?>
        </div>
        <div class="text-[11px] text-[#ecf0f180] mt-px">Cliente</div>
      </div>
      
      <form method="POST" action="<?= $basePath ?>/logout" class="m-0">
        <button type="submit" class="bg-transparent border-none cursor-pointer text-[#ecf0f166] flex items-center justify-center p-1 rounded transition-colors hover:text-fp-error" title="Cerrar sesión">
          <i data-lucide="log-out" class="w-4 h-4"></i>
        </button>
      </form>
      
    </div>
  </div>
</aside>
