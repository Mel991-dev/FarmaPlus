<?php
/**
 * Layout público de la Tienda FarmaPlus.
 * Sin sidebar — accesible sin iniciar sesión.
 *
 * Variables esperadas:
 *   $titulo     string  — Título de la página
 *   $contenido  string  — HTML generado por ob_start()
 *   $totalItems int     — Cantidad de productos en el carrito (para badge)
 */
$basePath   = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
$totalItems = $totalItems ?? 0;
$loggedIn   = isset($_SESSION['usuario_id']);
$nombre     = $loggedIn ? ($_SESSION['nombres'] ?? 'Mi cuenta') : '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($titulo ?? 'Tienda | FarmaPlus') ?></title>
    <meta name="description" content="Compra medicamentos y productos farmacéuticos en línea con FarmaPlus. Entrega a domicilio en tu ciudad.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= $basePath ?>/assets/css/app.min.css?v=<?= time() ?>">
    <script src="https://unpkg.com/lucide@latest/dist/umd/lucide.min.js" defer></script>
    <!-- Override: auth.css aplica html,body{height:100%} que bloquea scroll en la tienda -->
    <style>
        html, body { height: auto !important; min-height: 100vh; overflow-y: auto !important; }
        /* Animación apertura de modales */
        @keyframes modalIn {
            from { transform: translateY(14px) scale(0.97); opacity: 0; }
            to   { transform: translateY(0)    scale(1);    opacity: 1; }
        }
        .modal-open { display: flex !important; }
        .modal-box  { animation: modalIn 0.22s ease; }
    </style>
</head>
<body class="bg-fp-bg-main font-sans antialiased text-fp-text min-h-screen">

<!-- ============================================================
     HEADER / TOPBAR
============================================================ -->
<header class="sticky top-0 z-50 bg-fp-primary-dark shadow-lg">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 h-[64px] flex items-center justify-between gap-4">

        <!-- Logo -->
        <a href="<?= $basePath ?>/tienda" class="flex items-center gap-2.5 shrink-0 group">
            <div class="w-9 h-9 bg-fp-secondary rounded-xl flex items-center justify-center shadow-sm group-hover:scale-105 transition-transform">
                <i data-lucide="pill" class="w-5 h-5 text-white"></i>
            </div>
            <span class="text-[18px] font-bold tracking-tight text-white">Farma<span class="text-fp-secondary">Plus</span></span>
        </a>

        <!-- Barra de búsqueda central (md+) -->
        <form method="GET" action="<?= $basePath ?>/tienda" class="hidden md:flex flex-1 max-w-lg relative">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-white/40"></i>
            <input
                type="text"
                name="q"
                value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                placeholder="Buscar medicamentos, vitaminas..."
                class="w-full h-10 pl-9 pr-4 bg-white/10 border border-white/20 rounded-xl text-[14px] text-white placeholder-white/40 outline-none focus:border-fp-secondary focus:ring-2 focus:ring-fp-secondary/30 focus:bg-white/15 transition-all"
            >
        </form>

        <!-- Acciones derechas -->
        <div class="flex items-center gap-2 sm:gap-3 shrink-0">

            <!-- Carrito -->
            <a href="<?= $basePath ?>/tienda/carrito" class="relative p-2 hover:bg-white/10 rounded-xl transition-colors group" title="Carrito de compras">
                <i data-lucide="shopping-cart" class="w-5 h-5 text-white/80 group-hover:text-white transition-colors"></i>
                <?php if ($totalItems > 0): ?>
                <span id="cartBadge" class="absolute -top-1 -right-1 min-w-[18px] h-[18px] bg-fp-secondary text-white text-[10px] font-bold rounded-full flex items-center justify-center px-1"><?= $totalItems ?></span>
                <?php else: ?>
                <span id="cartBadge" class="absolute -top-1 -right-1 min-w-[18px] h-[18px] bg-fp-secondary text-white text-[10px] font-bold rounded-full hidden items-center justify-center px-1">0</span>
                <?php endif; ?>
            </a>

            <?php if ($loggedIn): ?>
            <!-- Usuario autenticado -->
            <div class="flex items-center gap-2 pl-3 border-l border-white/15">
                <div class="flex-col items-end hidden sm:flex">
                    <span class="text-[13px] font-semibold text-white leading-tight"><?= htmlspecialchars($nombre) ?></span>
                    <a href="<?= $basePath ?>/mi-cuenta" class="text-[11px] text-fp-secondary hover:underline font-medium">Mi cuenta</a>
                </div>
                <div class="w-9 h-9 rounded-full bg-fp-secondary flex items-center justify-center text-white text-xs font-bold shadow-sm">
                    <?= strtoupper(substr($_SESSION['nombres'] ?? 'C', 0, 1) . substr($_SESSION['apellidos'] ?? '', 0, 1)) ?>
                </div>
            </div>
            <?php else: ?>
            <a href="<?= $basePath ?>/login" class="hidden sm:flex items-center gap-2 px-3 py-2 bg-white/10 hover:bg-white/20 text-white rounded-xl text-[13px] font-semibold transition-all border border-white/15">
                <i data-lucide="log-in" class="w-4 h-4"></i> Iniciar sesión
            </a>
            <a href="<?= $basePath ?>/registro" class="px-3 py-2 bg-fp-secondary hover:bg-fp-secondary/90 text-white rounded-xl text-[13px] font-bold transition-all shadow-sm">
                Regístrate
            </a>
            <?php endif; ?>

        </div>
    </div>

    <!-- Búsqueda móvil (sm-) -->
    <div class="md:hidden px-4 pb-3">
        <form method="GET" action="<?= $basePath ?>/tienda" class="relative">
            <i data-lucide="search" class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-white/40"></i>
            <input
                type="text"
                name="q"
                value="<?= htmlspecialchars($_GET['q'] ?? '') ?>"
                placeholder="Buscar medicamentos..."
                class="w-full h-9 pl-9 pr-4 bg-white/10 border border-white/20 rounded-lg text-[13px] text-white placeholder-white/40 outline-none focus:border-fp-secondary focus:bg-white/15"
            >
        </form>
    </div>
</header>

<!-- CONTENIDO -->
<main class="max-w-7xl mx-auto px-4 sm:px-6 py-6 lg:py-8 min-h-[calc(100vh-64px)]">
    <?php if (!empty($contenido)) echo $contenido; ?>
</main>

<!-- ============================================================
     FOOTER MEJORADO
============================================================ -->
<footer class="bg-fp-primary-dark text-white/70 mt-12" role="contentinfo">

    <!-- Cuerpo principal: 3 columnas -->
    <div class="border-b border-white/10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 py-12">
            <div class="grid grid-cols-1 md:grid-cols-[260px_1fr_1fr] gap-10 md:gap-12 items-start">

                <!-- COL 1: Marca + contacto -->
                <div>
                    <!-- Logo -->
                    <div class="flex items-center gap-2.5 mb-4">
                        <div class="w-9 h-9 bg-fp-secondary rounded-xl flex items-center justify-center shrink-0">
                            <i data-lucide="pill" class="w-5 h-5 text-white"></i>
                        </div>
                        <span class="text-[19px] font-bold text-white tracking-tight">Farma<span class="text-fp-secondary">Plus</span></span>
                    </div>

                    <p class="text-[13px] leading-relaxed mb-6 text-white/60">
                        Droguería colombiana comprometida con tu salud.<br>
                        Medicamentos de calidad y domicilios en Neiva y el Huila.
                    </p>

                    <!-- Contacto -->
                    <div class="flex flex-col gap-3">
                        <div class="flex items-start gap-2.5 text-[13px] text-white/60">
                            <i data-lucide="map-pin" class="w-4 h-4 text-fp-secondary shrink-0 mt-0.5"></i>
                            <span><strong class="text-white/80">Cra. 5 #12-80, Centro</strong><br>Neiva, Huila · CP 410001</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-[13px] text-white/60">
                            <i data-lucide="phone" class="w-4 h-4 text-fp-secondary shrink-0"></i>
                            <a href="tel:+5788712345" class="hover:text-fp-secondary transition-colors"><strong class="text-white/80">(8) 871 2345</strong></a>
                        </div>
                        <div class="flex items-center gap-2.5 text-[13px] text-white/60">
                            <i data-lucide="message-circle" class="w-4 h-4 text-fp-secondary shrink-0"></i>
                            <a href="tel:+573112345678" class="hover:text-fp-secondary transition-colors">+57 311 234 5678</a>
                            <span class="text-white/30 text-[11px]">(WhatsApp)</span>
                        </div>
                        <div class="flex items-center gap-2.5 text-[13px] text-white/60">
                            <i data-lucide="mail" class="w-4 h-4 text-fp-secondary shrink-0"></i>
                            <a href="mailto:info@farmaplus.com.co" class="hover:text-fp-secondary transition-colors">info@farmaplus.com.co</a>
                        </div>
                    </div>
                </div>

                <!-- COL 2: Soporte -->
                <div>
                    <h3 class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-white mb-4 pb-3 border-b border-white/10">
                        <i data-lucide="headphones" class="w-3.5 h-3.5 text-fp-secondary"></i>
                        Soporte y ayuda
                    </h3>
                    <div class="flex flex-col">
                        <button onclick="fpModal('m-soporte')" class="flex items-center gap-2.5 py-2.5 text-[13px] text-white/60 hover:text-fp-secondary hover:pl-1 transition-all text-left border-b border-white/5 group">
                            <i data-lucide="phone" class="w-3.5 h-3.5 text-fp-secondary/70 group-hover:text-fp-secondary shrink-0"></i> Contactar soporte
                        </button>
                        <a href="<?= $basePath ?>/tienda" class="flex items-center gap-2.5 py-2.5 text-[13px] text-white/60 hover:text-fp-secondary hover:pl-1 transition-all border-b border-white/5 group">
                            <i data-lucide="store" class="w-3.5 h-3.5 text-fp-secondary/70 group-hover:text-fp-secondary shrink-0"></i> Catálogo de productos
                        </a>
                        <a href="<?= $basePath ?>/mi-cuenta/pedidos" class="flex items-center gap-2.5 py-2.5 text-[13px] text-white/60 hover:text-fp-secondary hover:pl-1 transition-all border-b border-white/5 group">
                            <i data-lucide="package" class="w-3.5 h-3.5 text-fp-secondary/70 group-hover:text-fp-secondary shrink-0"></i> Mis pedidos
                        </a>
                        <button onclick="fpModal('m-soporte')" class="flex items-center gap-2.5 py-2.5 text-[13px] text-white/60 hover:text-fp-secondary hover:pl-1 transition-all text-left border-b border-white/5 group">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5 text-fp-secondary/70 group-hover:text-fp-secondary shrink-0"></i> Política de devoluciones
                        </button>
                        <button onclick="fpModal('m-soporte')" class="flex items-center gap-2.5 py-2.5 text-[13px] text-white/60 hover:text-fp-secondary hover:pl-1 transition-all text-left group">
                            <i data-lucide="map" class="w-3.5 h-3.5 text-fp-secondary/70 group-hover:text-fp-secondary shrink-0"></i> Zonas de cobertura — Huila
                        </button>
                    </div>
                </div>

                <!-- COL 3: Legal -->
                <div>
                    <h3 class="flex items-center gap-2 text-[11px] font-bold uppercase tracking-widest text-white mb-4 pb-3 border-b border-white/10">
                        <i data-lucide="scale" class="w-3.5 h-3.5 text-fp-secondary"></i>
                        Legal y normativa
                    </h3>

                    <!-- Badges legales -->
                    <div class="flex flex-wrap gap-1.5 mb-5">
                        <span class="inline-flex items-center gap-1 bg-white/5 border border-white/10 rounded px-2 py-1 text-[11px] font-semibold text-white/50">
                            <i data-lucide="shield-check" class="w-3 h-3 text-fp-secondary"></i> INVIMA
                        </span>
                        <span class="inline-flex items-center gap-1 bg-white/5 border border-white/10 rounded px-2 py-1 text-[11px] font-semibold text-white/50">
                            <i data-lucide="file-check" class="w-3 h-3 text-fp-secondary"></i> Reg. Sanitario
                        </span>
                        <span class="inline-flex items-center gap-1 bg-white/5 border border-white/10 rounded px-2 py-1 text-[11px] font-semibold text-white/50">
                            <i data-lucide="lock" class="w-3 h-3 text-fp-secondary"></i> Habeas Data
                        </span>
                        <span class="inline-flex items-center gap-1 bg-white/5 border border-white/10 rounded px-2 py-1 text-[11px] font-semibold text-white/50">
                            <i data-lucide="check-circle" class="w-3 h-3 text-fp-secondary"></i> SSL
                        </span>
                    </div>

                    <div class="flex flex-col">
                        <button onclick="fpModal('m-privacidad')" class="flex items-center gap-2.5 py-2.5 text-[13px] text-white/60 hover:text-fp-secondary hover:pl-1 transition-all text-left border-b border-white/5 group">
                            <i data-lucide="shield-check" class="w-3.5 h-3.5 text-fp-secondary/70 group-hover:text-fp-secondary shrink-0"></i> Política de privacidad
                        </button>
                        <button onclick="fpModal('m-terminos')" class="flex items-center gap-2.5 py-2.5 text-[13px] text-white/60 hover:text-fp-secondary hover:pl-1 transition-all text-left border-b border-white/5 group">
                            <i data-lucide="file-text" class="w-3.5 h-3.5 text-fp-secondary/70 group-hover:text-fp-secondary shrink-0"></i> Términos de uso
                        </button>
                        <button onclick="fpModal('m-aviso')" class="flex items-center gap-2.5 py-2.5 text-[13px] text-white/60 hover:text-fp-secondary hover:pl-1 transition-all text-left group">
                            <i data-lucide="info" class="w-3.5 h-3.5 text-fp-secondary/70 group-hover:text-fp-secondary shrink-0"></i> Aviso legal
                        </button>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Barra inferior copyright -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 py-5 flex flex-col sm:flex-row items-center justify-between gap-3 flex-wrap">
        <div>
            <p class="text-[12px] text-white/30">
                © <?= date('Y') ?> <strong class="text-white/50">FarmaPlus</strong>. Todos los derechos reservados.
                Droguería registrada ante la Secretaría de Salud del Huila.
            </p>
            <p class="text-[11px] font-mono text-white/20 mt-0.5">NIT 900.123.456-7</p>
        </div>
        <div class="flex items-center flex-wrap">
            <button onclick="fpModal('m-privacidad')" class="text-[12px] text-white/35 hover:text-fp-secondary px-3 py-1 border-r border-white/10 transition-colors last:border-r-0">Privacidad</button>
            <button onclick="fpModal('m-terminos')"   class="text-[12px] text-white/35 hover:text-fp-secondary px-3 py-1 border-r border-white/10 transition-colors last:border-r-0">Términos</button>
            <button onclick="fpModal('m-aviso')"      class="text-[12px] text-white/35 hover:text-fp-secondary px-3 py-1 border-r border-white/10 transition-colors last:border-r-0">Aviso legal</button>
            <button onclick="fpModal('m-soporte')"    class="text-[12px] text-white/35 hover:text-fp-secondary px-3 py-1 transition-colors">Soporte</button>
        </div>
    </div>
</footer>

<!-- ============================================================
     MODALES LEGALES
============================================================ -->

<!-- Overlay genérico -->
<div id="fpModalOverlay" class="hidden fixed inset-0 bg-black/50 z-[9000] items-center justify-center p-5" onclick="if(event.target===this)fpModalClose()">

    <!-- Modal: Privacidad -->
    <div id="m-privacidad" class="modal-box hidden w-full max-w-lg bg-white rounded-2xl shadow-2xl flex flex-col max-h-[84vh] overflow-hidden">
        <div class="flex items-center justify-between gap-3 px-6 py-4 border-b border-fp-border bg-fp-bg-main shrink-0">
            <div class="flex items-center gap-2 text-[16px] font-bold text-fp-text">
                <i data-lucide="shield-check" class="w-5 h-5 text-fp-primary"></i> Política de Privacidad
            </div>
            <button onclick="fpModalClose()" class="p-1.5 rounded-lg hover:bg-fp-bg-card text-fp-muted hover:text-fp-primary transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="overflow-y-auto flex-1 px-6 py-5 text-[14px] text-fp-text leading-7 space-y-4">
            <div><h4 class="font-bold text-fp-primary-dark text-[13px] mb-1 flex items-center gap-1.5"><i data-lucide="info" class="w-3.5 h-3.5 text-fp-secondary"></i>1. Responsable del tratamiento</h4>
            <p class="text-fp-muted"><strong class="text-fp-text">FarmaPlus</strong> (NIT 900.123.456-7), Cra. 5 #12-80, Neiva, Huila, es responsable del tratamiento de sus datos conforme a la <span class="bg-fp-bg-card text-fp-primary text-[11px] font-semibold px-2 py-0.5 rounded-full border border-fp-border/50">Ley 1581/2012</span> y el <span class="bg-fp-bg-card text-fp-primary text-[11px] font-semibold px-2 py-0.5 rounded-full border border-fp-border/50">Decreto 1377/2013</span>.</p></div>
            <div><h4 class="font-bold text-fp-primary-dark text-[13px] mb-1 flex items-center gap-1.5"><i data-lucide="database" class="w-3.5 h-3.5 text-fp-secondary"></i>2. Datos que recopilamos</h4>
            <p class="text-fp-muted">Nombre, identificación, dirección de entrega, teléfono, correo e historial de compras. Para medicamentos de <strong class="text-fp-text">control especial</strong> se almacena el número de fórmula médica.</p></div>
            <div><h4 class="font-bold text-fp-primary-dark text-[13px] mb-1 flex items-center gap-1.5"><i data-lucide="target" class="w-3.5 h-3.5 text-fp-secondary"></i>3. Finalidad</h4>
            <p class="text-fp-muted">Sus datos se usan para: procesar pedidos, cumplir obligaciones ante INVIMA y Ministerio de Salud, enviar notificaciones de estado de pedido, y mejorar el servicio.</p></div>
            <div><h4 class="font-bold text-fp-primary-dark text-[13px] mb-1 flex items-center gap-1.5"><i data-lucide="lock" class="w-3.5 h-3.5 text-fp-secondary"></i>4. Derechos — Habeas Data</h4>
            <p class="text-fp-muted">Tiene derecho a <strong class="text-fp-text">conocer, actualizar, rectificar y suprimir</strong> sus datos. Escríbanos a <strong class="text-fp-text">privacidad@farmaplus.com.co</strong>.</p></div>
            <div><h4 class="font-bold text-fp-primary-dark text-[13px] mb-1 flex items-center gap-1.5"><i data-lucide="share-2" class="w-3.5 h-3.5 text-fp-secondary"></i>5. Transferencia</h4>
            <p class="text-fp-muted">No vendemos datos a terceros. Solo se comparten con <strong class="text-fp-text">MercadoPago</strong> para procesar pagos y con autoridades sanitarias cuando la normativa lo exija.</p></div>
        </div>
        <div class="px-6 py-4 border-t border-fp-border bg-fp-bg-main flex justify-end gap-3 shrink-0">
            <button onclick="fpModalClose()" class="h-10 px-5 rounded-fp border border-fp-border text-[13px] font-semibold text-fp-text hover:bg-fp-bg-card transition-colors">Cerrar</button>
            <button onclick="fpModalClose()" class="h-10 px-5 rounded-fp bg-fp-primary text-white text-[13px] font-semibold hover:bg-fp-primary-light transition-colors">Entendido</button>
        </div>
    </div>

    <!-- Modal: Términos -->
    <div id="m-terminos" class="modal-box hidden w-full max-w-lg bg-white rounded-2xl shadow-2xl flex flex-col max-h-[84vh] overflow-hidden">
        <div class="flex items-center justify-between gap-3 px-6 py-4 border-b border-fp-border bg-fp-bg-main shrink-0">
            <div class="flex items-center gap-2 text-[16px] font-bold text-fp-text">
                <i data-lucide="file-text" class="w-5 h-5 text-fp-primary"></i> Términos de Uso
            </div>
            <button onclick="fpModalClose()" class="p-1.5 rounded-lg hover:bg-fp-bg-card text-fp-muted hover:text-fp-primary transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="overflow-y-auto flex-1 px-6 py-5 text-[14px] text-fp-text leading-7 space-y-4">
            <div><h4 class="font-bold text-fp-primary-dark text-[13px] mb-1 flex items-center gap-1.5"><i data-lucide="store" class="w-3.5 h-3.5 text-fp-secondary"></i>1. Sobre el servicio</h4>
            <p class="text-fp-muted">FarmaPlus dispensa exclusivamente <strong class="text-fp-text">medicamentos de venta libre</strong> en línea. Los de control especial solo se entregan presencialmente con fórmula médica válida, según la <span class="bg-fp-bg-card text-fp-primary text-[11px] font-semibold px-2 py-0.5 rounded-full border border-fp-border/50">Ley 2300/2023</span>.</p></div>
            <div><h4 class="font-bold text-fp-primary-dark text-[13px] mb-1 flex items-center gap-1.5"><i data-lucide="rotate-ccw" class="w-3.5 h-3.5 text-fp-secondary"></i>2. Devoluciones</h4>
            <p class="text-fp-muted">Medicamentos de venta libre: hasta <strong class="text-fp-text">5 días hábiles</strong> tras la compra, empaque sin abrir y con comprobante. Los de control especial <strong class="text-fp-text">no tienen devolución</strong>.</p></div>
            <div><h4 class="font-bold text-fp-primary-dark text-[13px] mb-1 flex items-center gap-1.5"><i data-lucide="credit-card" class="w-3.5 h-3.5 text-fp-secondary"></i>3. Pagos y domicilios</h4>
            <p class="text-fp-muted">Pagos procesados por <strong class="text-fp-text">MercadoPago</strong> (PSE, Nequi o tarjeta). El costo del domicilio varía según distancia. Tiempo estimado: 25–90 minutos.</p></div>
            <div><h4 class="font-bold text-fp-primary-dark text-[13px] mb-1 flex items-center gap-1.5"><i data-lucide="alert-triangle" class="w-3.5 h-3.5 text-fp-secondary"></i>4. Limitación de responsabilidad</h4>
            <p class="text-fp-muted">FarmaPlus no es responsable por el uso incorrecto de medicamentos ni por demoras por fuerza mayor. La información no reemplaza la consulta médica profesional.</p></div>
        </div>
        <div class="px-6 py-4 border-t border-fp-border bg-fp-bg-main flex justify-end gap-3 shrink-0">
            <button onclick="fpModalClose()" class="h-10 px-5 rounded-fp border border-fp-border text-[13px] font-semibold text-fp-text hover:bg-fp-bg-card transition-colors">Cerrar</button>
            <button onclick="fpModalClose()" class="h-10 px-5 rounded-fp bg-fp-primary text-white text-[13px] font-semibold hover:bg-fp-primary-light transition-colors">Entendido</button>
        </div>
    </div>

    <!-- Modal: Aviso Legal -->
    <div id="m-aviso" class="modal-box hidden w-full max-w-lg bg-white rounded-2xl shadow-2xl flex flex-col max-h-[84vh] overflow-hidden">
        <div class="flex items-center justify-between gap-3 px-6 py-4 border-b border-fp-border bg-fp-bg-main shrink-0">
            <div class="flex items-center gap-2 text-[16px] font-bold text-fp-text">
                <i data-lucide="info" class="w-5 h-5 text-fp-primary"></i> Aviso Legal
            </div>
            <button onclick="fpModalClose()" class="p-1.5 rounded-lg hover:bg-fp-bg-card text-fp-muted hover:text-fp-primary transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="overflow-y-auto flex-1 px-6 py-5 text-[14px] text-fp-text leading-7 space-y-4">
            <div><h4 class="font-bold text-fp-primary-dark text-[13px] mb-1 flex items-center gap-1.5"><i data-lucide="building-2" class="w-3.5 h-3.5 text-fp-secondary"></i>Datos del establecimiento</h4>
            <p class="text-fp-muted"><strong class="text-fp-text">Razón social:</strong> FarmaPlus S.A.S.<br><strong class="text-fp-text">NIT:</strong> 900.123.456-7<br><strong class="text-fp-text">Dirección:</strong> Cra. 5 #12-80, Centro, Neiva, Huila<br><strong class="text-fp-text">Registro INVIMA:</strong> vigente · Secretaría de Salud del Huila: autorizado</p></div>
            <div><h4 class="font-bold text-fp-primary-dark text-[13px] mb-1 flex items-center gap-1.5"><i data-lucide="shield" class="w-3.5 h-3.5 text-fp-secondary"></i>Normativa aplicable</h4>
            <p class="text-fp-muted">Este sitio se rige por la <span class="bg-fp-bg-card text-fp-primary text-[11px] font-semibold px-2 py-0.5 rounded-full border border-fp-border/50">Resolución 1403/2007</span>, el <span class="bg-fp-bg-card text-fp-primary text-[11px] font-semibold px-2 py-0.5 rounded-full border border-fp-border/50">Decreto 677/1995</span>, la <span class="bg-fp-bg-card text-fp-primary text-[11px] font-semibold px-2 py-0.5 rounded-full border border-fp-border/50">Ley 2300/2023</span> y la <span class="bg-fp-bg-card text-fp-primary text-[11px] font-semibold px-2 py-0.5 rounded-full border border-fp-border/50">Ley 1581/2012</span>.</p></div>
        </div>
        <div class="px-6 py-4 border-t border-fp-border bg-fp-bg-main flex justify-end shrink-0">
            <button onclick="fpModalClose()" class="h-10 px-5 rounded-fp bg-fp-primary text-white text-[13px] font-semibold hover:bg-fp-primary-light transition-colors">Cerrar</button>
        </div>
    </div>

    <!-- Modal: Soporte -->
    <div id="m-soporte" class="modal-box hidden w-full max-w-lg bg-white rounded-2xl shadow-2xl flex flex-col max-h-[84vh] overflow-hidden">
        <div class="flex items-center justify-between gap-3 px-6 py-4 border-b border-fp-border bg-fp-bg-main shrink-0">
            <div class="flex items-center gap-2 text-[16px] font-bold text-fp-text">
                <i data-lucide="headphones" class="w-5 h-5 text-fp-primary"></i> Soporte — FarmaPlus
            </div>
            <button onclick="fpModalClose()" class="p-1.5 rounded-lg hover:bg-fp-bg-card text-fp-muted hover:text-fp-primary transition-colors">
                <i data-lucide="x" class="w-5 h-5"></i>
            </button>
        </div>
        <div class="overflow-y-auto flex-1 px-6 py-5 space-y-3">
            <p class="text-[14px] text-fp-muted mb-2">Nuestro equipo está disponible para ayudarte con pedidos, domicilios o cualquier consulta.</p>
            <div class="flex items-center gap-3 bg-fp-bg-main border border-fp-border rounded-xl p-4">
                <div class="w-10 h-10 bg-fp-bg-card rounded-xl flex items-center justify-center shrink-0">
                    <i data-lucide="phone" class="w-5 h-5 text-fp-primary"></i>
                </div>
                <div><p class="font-bold text-[14px] text-fp-text">Línea directa</p><p class="text-[12px] text-fp-muted">(8) 871 2345 · Lun–Sáb 7am–9pm · Dom 8am–6pm</p></div>
            </div>
            <div class="flex items-center gap-3 bg-fp-bg-main border border-fp-border rounded-xl p-4">
                <div class="w-10 h-10 bg-green-50 rounded-xl flex items-center justify-center shrink-0">
                    <i data-lucide="message-circle" class="w-5 h-5 text-fp-success"></i>
                </div>
                <div><p class="font-bold text-[14px] text-fp-text">WhatsApp</p><p class="text-[12px] text-fp-muted">+57 311 234 5678 · Respuesta en menos de 30 min</p></div>
            </div>
            <div class="flex items-center gap-3 bg-fp-bg-main border border-fp-border rounded-xl p-4">
                <div class="w-10 h-10 bg-amber-50 rounded-xl flex items-center justify-center shrink-0">
                    <i data-lucide="mail" class="w-5 h-5 text-fp-warning"></i>
                </div>
                <div><p class="font-bold text-[14px] text-fp-text">Correo electrónico</p><p class="text-[12px] text-fp-muted">soporte@farmaplus.com.co · Respuesta en 24 h hábiles</p></div>
            </div>
        </div>
        <div class="px-6 py-4 border-t border-fp-border bg-fp-bg-main flex justify-end shrink-0">
            <button onclick="fpModalClose()" class="h-10 px-5 rounded-fp bg-fp-primary text-white text-[13px] font-semibold hover:bg-fp-primary-light transition-colors">Cerrar</button>
        </div>
    </div>

</div><!-- #fpModalOverlay -->

<!-- Toast global -->
<div id="toastContainerTienda" class="fixed bottom-5 right-4 flex flex-col gap-2 z-[9999] pointer-events-none"></div>

<script>
    document.addEventListener('DOMContentLoaded', () => {
        if (typeof lucide !== 'undefined') lucide.createIcons();
    });

    /* ---- Modales del footer ---- */
    let _modalActivo = null;
    function fpModal(id) {
        const overlay = document.getElementById('fpModalOverlay');
        // Ocultar todos los modales
        overlay.querySelectorAll('.modal-box').forEach(m => m.classList.add('hidden'));
        // Mostrar el solicitado
        const box = document.getElementById(id);
        if (box) { box.classList.remove('hidden'); _modalActivo = id; }
        overlay.classList.remove('hidden');
        overlay.classList.add('flex');
        if (typeof lucide !== 'undefined') lucide.createIcons();
        document.body.style.overflow = 'hidden';
    }
    function fpModalClose() {
        const overlay = document.getElementById('fpModalOverlay');
        overlay.classList.add('hidden');
        overlay.classList.remove('flex');
        overlay.querySelectorAll('.modal-box').forEach(m => m.classList.add('hidden'));
        document.body.style.overflow = '';
        _modalActivo = null;
    }
    document.addEventListener('keydown', e => { if (e.key === 'Escape') fpModalClose(); });

    /* ---- Toasts ---- */
    function showToastTienda(msg, type = 'success') {
        const icons   = { success: 'check-circle', error: 'x-circle', info: 'info', warning: 'alert-triangle' };
        const colors  = { success: 'border-green-500', error: 'border-red-500', info: 'border-blue-500', warning: 'border-yellow-500' };
        const container = document.getElementById('toastContainerTienda');
        const toast   = document.createElement('div');
        toast.className = `flex items-center gap-3 bg-white border-l-4 ${colors[type]} p-3 rounded-lg shadow-lg w-[300px] sm:w-[340px] translate-x-full opacity-0 transition-all duration-300 pointer-events-auto`;
        toast.innerHTML = `<i data-lucide="${icons[type]}" class="w-5 h-5 shrink-0"></i><p class="text-[13px] font-semibold text-slate-700">${msg}</p>`;
        container.appendChild(toast);
        if (typeof lucide !== 'undefined') lucide.createIcons();
        requestAnimationFrame(() => { toast.classList.remove('translate-x-full', 'opacity-0'); });
        setTimeout(() => {
            toast.classList.add('translate-x-full', 'opacity-0');
            setTimeout(() => toast.remove(), 300);
        }, 4000);
    }

    /* ---- Badge carrito ---- */
    function actualizarBadge(count) {
        const badge = document.getElementById('cartBadge');
        if (!badge) return;
        badge.textContent = count;
        if (count > 0) { badge.classList.remove('hidden'); badge.classList.add('flex'); }
        else { badge.classList.add('hidden'); badge.classList.remove('flex'); }
    }
</script>

</body>
</html>
