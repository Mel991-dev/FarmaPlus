<?php
/**
 * views/auth/login.php
 * Vista de Login — fiel al mockup fp-01-login.html
 *
 * Variables disponibles (inyectadas por AuthController):
 *   $error   string|null  — Mensaje de error del servidor
 *   $blocked bool         — Cuenta bloqueada
 */
$error   = $error ?? null;
$blocked = $blocked ?? false;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FarmaPlus CRM — Iniciar sesión</title>
  <meta name="description" content="Acceso al sistema de gestión FarmaPlus CRM">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    :root {
      --color-primary:        #1A6B8A;
      --color-primary-dark:   #1A3A4A;
      --color-primary-light:  #4A9BB5;
      --color-secondary:      #2A9D8F;
      --color-bg-main:        #F4F9FC;
      --color-bg-card:        #E9F5F8;
      --color-success:        #27AE60;
      --color-error:          #E74C3C;
      --color-error-soft:     #FDEDEC;
      --color-text-primary:   #2C3E50;
      --color-text-secondary: #7F8C8D;
      --color-border:         #BDC3C7;
      --color-sidebar-bg:     #1A3A4A;
      --radius-sm: 4px; --radius-md: 8px; --radius-lg: 12px; --radius-full: 9999px;
      --font-main: 'Inter', sans-serif; --font-mono: 'JetBrains Mono', monospace;
      --shadow-focus: 0 0 0 3px rgba(26,107,138,0.15);
    }
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
    html, body { height: 100%; font-family: var(--font-main); font-size: 16px; color: var(--color-text-primary); background: var(--color-sidebar-bg); }

    /* LAYOUT */
    .auth-wrapper { display: flex; min-height: 100vh; }

    /* PANEL IZQUIERDO */
    .brand-panel { flex: 1; background: var(--color-sidebar-bg); display: flex; flex-direction: column; justify-content: center; align-items: center; padding: 48px 40px; position: relative; overflow: hidden; }
    .brand-panel::before { content:''; position:absolute; width:400px; height:400px; border-radius:50%; border:1.5px solid rgba(42,157,143,0.18); top:-80px; right:-80px; pointer-events:none; }
    .brand-panel::after  { content:''; position:absolute; width:260px; height:260px; border-radius:50%; border:1.5px solid rgba(26,107,138,0.25); bottom:60px; left:-60px; pointer-events:none; }
    .brand-panel .deco-circle { position:absolute; width:160px; height:160px; border-radius:50%; border:1px solid rgba(255,255,255,0.06); bottom:200px; right:40px; pointer-events:none; }
    .brand-content { position:relative; z-index:1; text-align:center; max-width:360px; }
    .logo-wrap { display:inline-flex; align-items:center; gap:12px; margin-bottom:40px; }
    .logo-icon { width:52px; height:52px; background:var(--color-secondary); border-radius:var(--radius-lg); display:flex; align-items:center; justify-content:center; box-shadow:0 4px 16px rgba(42,157,143,0.35); }
    .logo-icon svg { color:#fff; width:28px; height:28px; }
    .logo-text { display:flex; flex-direction:column; align-items:flex-start; }
    .logo-name { font-size:26px; font-weight:700; color:#fff; line-height:1; letter-spacing:-0.5px; }
    .logo-name span { color:var(--color-secondary); }
    .logo-sub { font-size:11px; font-weight:500; color:rgba(236,240,241,0.55); text-transform:uppercase; letter-spacing:1.5px; margin-top:3px; }
    .brand-tagline { font-size:18px; font-weight:600; color:#ECF0F1; line-height:1.4; margin-bottom:12px; }
    .brand-desc { font-size:14px; color:rgba(236,240,241,0.6); line-height:1.6; margin-bottom:48px; }
    .feature-list { display:flex; flex-direction:column; gap:12px; text-align:left; }
    .feature-item { display:flex; align-items:center; gap:12px; }
    .feature-icon { width:32px; height:32px; border-radius:var(--radius-md); background:rgba(42,157,143,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .feature-icon svg { color:var(--color-secondary); width:16px; height:16px; }
    .feature-item span { font-size:14px; color:rgba(236,240,241,0.75); font-weight:500; }
    .brand-divider { width:48px; height:2px; background:var(--color-secondary); border-radius:var(--radius-full); margin:32px auto; opacity:0.5; }

    /* PANEL DERECHO */
    .form-panel { flex:1; background:#fff; display:flex; flex-direction:column; justify-content:center; align-items:center; padding:48px 40px; }
    .form-container { width:100%; max-width:440px; }
    .form-header { margin-bottom:40px; }
    .form-welcome { font-size:12px; font-weight:600; text-transform:uppercase; letter-spacing:1.5px; color:var(--color-secondary); margin-bottom:8px; }
    .form-title { font-size:28px; font-weight:700; color:var(--color-text-primary); margin-bottom:8px; letter-spacing:-0.5px; }
    .form-subtitle { font-size:15px; color:var(--color-text-secondary); }

    /* ALERTA ERROR (servidor) */
    .alert-error { display:none; background:var(--color-error-soft); border:1px solid rgba(231,76,60,0.25); border-left:4px solid var(--color-error); border-radius:var(--radius-md); padding:12px 16px; margin-bottom:24px; gap:10px; align-items:flex-start; }
    .alert-error.visible { display:flex; }
    .alert-error svg { color:var(--color-error); flex-shrink:0; margin-top:2px; width:16px; height:16px; }
    .alert-error-text { font-size:14px; color:#922B21; line-height:1.5; }
    .alert-error-text strong { display:block; font-weight:600; margin-bottom:2px; }

    /* ALERTA BLOQUEO */
    .alert-blocked { display:none; background:#FEF9E7; border:1px solid rgba(243,156,18,0.3); border-left:4px solid #F39C12; border-radius:var(--radius-md); padding:12px 16px; margin-bottom:24px; gap:10px; align-items:flex-start; }
    .alert-blocked.visible { display:flex; }
    .alert-blocked svg { color:#F39C12; flex-shrink:0; margin-top:2px; width:16px; height:16px; }
    .alert-blocked-text { font-size:14px; color:#7D6608; line-height:1.5; }
    .alert-blocked-text strong { display:block; font-weight:600; margin-bottom:2px; }

    /* FORM FIELDS */
    .form-group { margin-bottom:20px; }
    .form-label { display:block; font-size:14px; font-weight:500; color:var(--color-text-primary); margin-bottom:8px; }
    .input-wrap { position:relative; }
    .input-icon { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--color-text-secondary); width:18px; height:18px; pointer-events:none; transition:color 0.2s; }
    .input-wrap:focus-within .input-icon { color:var(--color-primary); }
    .form-input { width:100%; height:48px; padding:0 44px; border:1.5px solid var(--color-border); border-radius:var(--radius-md); font-family:var(--font-main); font-size:15px; color:var(--color-text-primary); background:#fff; outline:none; transition:border-color 0.2s, box-shadow 0.2s; }
    .form-input::placeholder { color:#B0B8C1; }
    .form-input:focus { border-color:var(--color-primary); box-shadow:var(--shadow-focus); }
    .form-input.input-error { border-color:var(--color-error); }
    .field-error { display:none; align-items:center; gap:4px; margin-top:6px; font-size:12px; color:var(--color-error); }
    .field-error.visible { display:flex; }
    .field-error svg { width:13px; height:13px; flex-shrink:0; }
    .toggle-password { position:absolute; right:14px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:var(--color-text-secondary); display:flex; align-items:center; justify-content:center; padding:4px; border-radius:var(--radius-sm); transition:color 0.2s; width:28px; height:28px; }
    .toggle-password:hover { color:var(--color-primary); }
    .toggle-password svg { width:18px; height:18px; }
    .forgot-wrap { display:flex; justify-content:flex-end; margin-top:8px; }
    .forgot-link { font-size:13px; font-weight:500; color:var(--color-primary); text-decoration:none; transition:color 0.2s; }
    .forgot-link:hover { color:var(--color-primary-light); text-decoration:underline; }

    /* BOTÓN SUBMIT */
    .btn-primary { width:100%; height:48px; background:var(--color-primary); color:#fff; border:none; border-radius:var(--radius-md); font-family:var(--font-main); font-size:15px; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; margin-top:28px; transition:background 0.2s, transform 0.1s, box-shadow 0.2s; letter-spacing:0.1px; }
    .btn-primary:hover { background:var(--color-primary-light); box-shadow:0 4px 16px rgba(26,107,138,0.28); }
    .btn-primary:active { transform:scale(0.99); }
    .btn-primary svg { width:18px; height:18px; }
    .btn-primary:disabled { opacity:0.6; cursor:not-allowed; }

    /* FOOTER */
    .form-footer { margin-top:32px; text-align:center; padding-top:24px; border-top:1px solid #F0F3F4; }
    .form-footer p { font-size:13px; color:var(--color-text-secondary); }
    .form-footer strong { font-weight:600; color:var(--color-text-primary); }
    .version-tag { margin-top:16px; font-size:11px; font-family:var(--font-mono); color:#C8D0D5; letter-spacing:0.5px; }

    /* RESPONSIVE */
    @media (max-width: 1023px) { .brand-panel { display:none; } .form-panel { background:var(--color-bg-main); padding:32px 24px; } .form-container { background:#fff; border-radius:var(--radius-lg); padding:36px 32px; box-shadow:0 2px 8px rgba(0,0,0,0.08); } }
    @media (max-width: 767px) { .form-panel { padding:24px 16px; align-items:flex-start; padding-top:48px; } .form-container { padding:28px 20px; } .form-title { font-size:24px; } }
  </style>
</head>
<body>

<div class="auth-wrapper">

  <!-- PANEL IZQUIERDO: MARCA -->
  <aside class="brand-panel" role="complementary" aria-label="Información de la plataforma">
    <div class="deco-circle"></div>
    <div class="brand-content">
      <div class="logo-wrap">
        <div class="logo-icon"><i data-lucide="pill"></i></div>
        <div class="logo-text">
          <span class="logo-name">Farma<span>Plus</span></span>
          <span class="logo-sub">Sistema CRM Farmacéutico</span>
        </div>
      </div>
      <p class="brand-tagline">Gestión de droguería<br>centralizada y segura</p>
      <p class="brand-desc">Inventario, ventas, domicilios y reportes<br>en un solo sistema. Cumplimiento normativo colombiano.</p>
      <div class="brand-divider"></div>
      <div class="feature-list">
        <div class="feature-item">
          <div class="feature-icon"><i data-lucide="package-open"></i></div>
          <span>Control de inventario por lotes (FEFO)</span>
        </div>
        <div class="feature-item">
          <div class="feature-icon"><i data-lucide="shopping-cart"></i></div>
          <span>Ventas presenciales y en línea</span>
        </div>
        <div class="feature-item">
          <div class="feature-icon"><i data-lucide="truck"></i></div>
          <span>Domicilios con cálculo automático</span>
        </div>
        <div class="feature-item">
          <div class="feature-icon"><i data-lucide="shield-check"></i></div>
          <span>Normativa INVIMA y Ley 1581/2012</span>
        </div>
      </div>
    </div>
  </aside>

  <!-- PANEL DERECHO: FORMULARIO -->
  <main class="form-panel" role="main">
    <div class="form-container">

      <header class="form-header">
        <p class="form-welcome">Bienvenido de nuevo</p>
        <h1 class="form-title">Iniciar sesión</h1>
        <p class="form-subtitle">Accede con tu correo o número de cédula.</p>
      </header>

      <!-- Error de bloqueo (servidor) -->
      <div class="alert-blocked <?= $blocked ? 'visible' : '' ?>" id="alertBlocked" role="alert">
        <i data-lucide="clock"></i>
        <div class="alert-blocked-text">
          <strong>Cuenta bloqueada temporalmente</strong>
          Demasiados intentos fallidos. Espera 15 minutos o contacta al administrador.
        </div>
      </div>

      <!-- Error de credenciales (servidor) -->
      <div class="alert-error <?= $error && !$blocked ? 'visible' : '' ?>" id="alertError" role="alert" aria-live="assertive">
        <i data-lucide="circle-x"></i>
        <div class="alert-error-text">
          <strong>Credenciales incorrectas</strong>
          <?= htmlspecialchars($error ?? 'Verifica tu correo y contraseña e intenta de nuevo.') ?>
        </div>
      </div>

      <form id="loginForm" method="POST" action="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/login" novalidate>

        <!-- Campo: Correo o cédula -->
        <div class="form-group">
          <label class="form-label" for="credencial">Correo electrónico o cédula</label>
          <div class="input-wrap">
            <i data-lucide="user" class="input-icon"></i>
            <input
              type="text"
              id="credencial"
              name="credencial"
              class="form-input <?= $error ? 'input-error' : '' ?>"
              placeholder="correo@farmaplus.co o N.º cédula"
              autocomplete="username"
              aria-describedby="credencialError"
              value="<?= htmlspecialchars($_POST['credencial'] ?? '') ?>"
            />
          </div>
          <div class="field-error" id="credencialError" role="alert">
            <i data-lucide="alert-circle"></i>
            <span>Ingresa tu correo electrónico o número de cédula.</span>
          </div>
        </div>

        <!-- Campo: Contraseña -->
        <div class="form-group">
          <label class="form-label" for="contrasena">Contraseña</label>
          <div class="input-wrap">
            <i data-lucide="lock" class="input-icon"></i>
            <input
              type="password"
              id="contrasena"
              name="contrasena"
              class="form-input <?= $error ? 'input-error' : '' ?>"
              placeholder="••••••••"
              autocomplete="current-password"
              aria-describedby="contrasenaError"
            />
            <button type="button" class="toggle-password" id="togglePassword"
                    aria-label="Mostrar u ocultar contraseña" aria-pressed="false">
              <i data-lucide="eye" id="eyeIcon"></i>
            </button>
          </div>
          <div class="field-error" id="contrasenaError" role="alert">
            <i data-lucide="alert-circle"></i>
            <span>La contraseña no puede estar vacía.</span>
          </div>
          <div class="forgot-wrap">
            <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/recuperar-contrasena" class="forgot-link">¿Olvidaste tu contraseña?</a>
          </div>
        </div>

        <!-- Botón submit -->
        <button type="submit" class="btn-primary" id="btnLogin">
          <i data-lucide="log-in"></i>
          Iniciar sesión
        </button>

      </form>

      <footer class="form-footer">
        <p>Sistema restringido — solo <strong>personal autorizado</strong></p>
        <p class="version-tag">v1.0.0 · FarmaPlus CRM · Colombia</p>
      </footer>

    </div>
  </main>

</div>

<script>
  lucide.createIcons();

  const form           = document.getElementById('loginForm');
  const credencialInp  = document.getElementById('credencial');
  const contrasenaInp  = document.getElementById('contrasena');
  const credencialErr  = document.getElementById('credencialError');
  const contrasenaErr  = document.getElementById('contrasenaError');
  const alertError     = document.getElementById('alertError');
  const togglePassword = document.getElementById('togglePassword');
  const eyeIcon        = document.getElementById('eyeIcon');

  // Toggle mostrar / ocultar contraseña
  togglePassword.addEventListener('click', () => {
    const isPass = contrasenaInp.type === 'password';
    contrasenaInp.type = isPass ? 'text' : 'password';
    togglePassword.setAttribute('aria-pressed', isPass ? 'true' : 'false');
    eyeIcon.setAttribute('data-lucide', isPass ? 'eye-off' : 'eye');
    lucide.createIcons();
  });

  // Limpiar errores al escribir
  credencialInp.addEventListener('input', () => {
    credencialInp.classList.remove('input-error');
    credencialErr.classList.remove('visible');
    alertError.classList.remove('visible');
  });
  contrasenaInp.addEventListener('input', () => {
    contrasenaInp.classList.remove('input-error');
    contrasenaErr.classList.remove('visible');
    alertError.classList.remove('visible');
  });

  // Validación cliente (antes del POST)
  form.addEventListener('submit', (e) => {
    let hasError = false;
    const credencial = credencialInp.value.trim();
    const contrasena = contrasenaInp.value.trim();

    if (!credencial || credencial.length < 6) {
      credencialInp.classList.add('input-error');
      credencialErr.classList.add('visible');
      hasError = true;
    }
    if (!contrasena) {
      contrasenaInp.classList.add('input-error');
      contrasenaErr.classList.add('visible');
      hasError = true;
    }
    if (hasError) { e.preventDefault(); return; }

    // Deshabilitar botón para evitar doble envío
    document.getElementById('btnLogin').disabled = true;
    document.getElementById('btnLogin').innerHTML = '<i data-lucide="loader-2"></i> Verificando...';
    lucide.createIcons();
  });
</script>
</body>
</html>
