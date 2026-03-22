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
  <link rel="stylesheet" href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/assets/css/app.min.css?v=<?= time() ?>" />
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
