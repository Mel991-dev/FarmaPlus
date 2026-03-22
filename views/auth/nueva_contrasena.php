<?php
/**
 * views/auth/nueva_contrasena.php
 * Vista para establecer nueva contraseña tras enlace de recuperación.
 *
 * Variables:
 *   $token_valido bool         — TRUE si el token sigue siendo válido
 *   $token        string       — Token de recuperación (en campo oculto)
 *   $error        string|null  — Mensaje de error del servidor
 */
$token_valido = $token_valido ?? false;
$token        = $token        ?? '';
$error        = $error        ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FarmaPlus CRM — Nueva contraseña</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/assets/css/app.min.css?v=<?= time() ?>" />
</head>
<body>

<div class="auth-wrapper">
  <!-- PANEL IZQUIERDO -->
  <aside class="brand-panel" role="complementary">
    <div class="brand-content">
      <div class="logo-wrap">
        <div class="logo-icon"><i data-lucide="pill"></i></div>
        <div class="logo-text">
          <span class="logo-name">Farma<span>Plus</span></span>
          <span class="logo-sub">Plataforma de gestión</span>
        </div>
      </div>
      <p class="brand-tagline">Establece tu<br>nueva contraseña</p>
      <p class="brand-desc">Elige una contraseña segura para proteger tu cuenta.</p>
      <div class="brand-divider"></div>
      <div class="feature-list">
        <div class="feature-item"><div class="feature-icon"><i data-lucide="key-round"></i></div><span>Mínimo 8 caracteres con mayúscula y número</span></div>
        <div class="feature-item"><div class="feature-icon"><i data-lucide="shield-check"></i></div><span>Cifrado bcrypt de alta seguridad</span></div>
        <div class="feature-item"><div class="feature-icon"><i data-lucide="clock"></i></div><span>Enlace válido por 30 minutos</span></div>
      </div>
    </div>
  </aside>

  <!-- PANEL DERECHO -->
  <main class="form-panel" role="main">
    <div class="form-container">
      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/login" class="back-link"><i data-lucide="arrow-left"></i> Volver al inicio de sesión</a>

      <?php if (!$token_valido): ?>
        <!-- Token expirado o inválido -->
        <div class="alert-expired" role="alert">
          <div style="display:flex;justify-content:center;"><i data-lucide="clock-x"></i></div>
          <h2>Enlace expirado</h2>
          <p><?= htmlspecialchars($error ?? 'El enlace de recuperación es inválido o ha expirado.') ?></p>
          <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/recuperar-contrasena" class="btn-outline"><i data-lucide="refresh-cw"></i>Solicitar nuevo enlace</a>
        </div>

      <?php else: ?>
        <!-- Formulario nueva contraseña -->
        <div class="lock-icon-wrap" aria-hidden="true"><i data-lucide="key-round"></i></div>
        <header class="form-header">
          <h1 class="form-title">Nueva contraseña</h1>
          <p class="form-subtitle">Crea una contraseña segura para tu cuenta.</p>
        </header>

        <?php if ($error): ?>
        <div class="alert-error" role="alert">
          <i data-lucide="circle-x"></i>
          <span class="alert-error-text"><?= htmlspecialchars($error) ?></span>
        </div>
        <?php endif; ?>

        <form id="newPasswordForm" method="POST" action="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/nueva-contrasena" novalidate>
          <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

          <div class="form-group">
            <label class="form-label" for="contrasena">Nueva contraseña</label>
            <div class="input-wrap">
              <i data-lucide="lock" class="input-icon"></i>
              <input type="password" id="contrasena" name="contrasena" class="form-input"
                     placeholder="Mínimo 8 caracteres" autocomplete="new-password" />
              <button type="button" class="toggle-password" data-target="contrasena" aria-label="Mostrar contraseña">
                <i data-lucide="eye"></i>
              </button>
            </div>
            <div class="strength-bar-wrap"><div class="strength-bar" id="strengthBar"></div></div>
            <div class="strength-text" id="strengthText" style="color:var(--color-text-secondary)"></div>
            <div class="field-error" id="contrasenaError"><i data-lucide="alert-circle"></i><span id="contrasenaErrorMsg">Mínimo 8 caracteres, 1 mayúscula y 1 número.</span></div>
          </div>

          <div class="form-group">
            <label class="form-label" for="confirmar">Confirmar contraseña</label>
            <div class="input-wrap">
              <i data-lucide="lock-keyhole" class="input-icon"></i>
              <input type="password" id="confirmar" name="confirmar" class="form-input"
                     placeholder="Repite la contraseña" autocomplete="new-password" />
              <button type="button" class="toggle-password" data-target="confirmar" aria-label="Mostrar contraseña">
                <i data-lucide="eye"></i>
              </button>
            </div>
            <div class="field-error" id="confirmarError"><i data-lucide="alert-circle"></i><span>Las contraseñas no coinciden.</span></div>
          </div>

          <button type="submit" class="btn-primary" id="btnGuardar">
            <i data-lucide="check-circle"></i>
            Guardar nueva contraseña
          </button>
        </form>
      <?php endif; ?>

      <footer class="form-footer">
        <p>¿Necesitas ayuda? Contacta al <strong>administrador del sistema</strong>.</p>
        <p class="version-tag">v1.0.0 · FarmaPlus CRM · Colombia</p>
      </footer>
    </div>
  </main>
</div>

<script>
  lucide.createIcons();

  // Toggle show/hide password
  document.querySelectorAll('.toggle-password').forEach(btn => {
    btn.addEventListener('click', () => {
      const target = document.getElementById(btn.dataset.target);
      const isPass = target.type === 'password';
      target.type = isPass ? 'text' : 'password';
      const icon = btn.querySelector('[data-lucide]');
      icon.setAttribute('data-lucide', isPass ? 'eye-off' : 'eye');
      lucide.createIcons();
    });
  });

  // Indicador fortaleza contraseña
  const contrasenaInp = document.getElementById('contrasena');
  const strengthBar   = document.getElementById('strengthBar');
  const strengthText  = document.getElementById('strengthText');

  if (contrasenaInp) {
    contrasenaInp.addEventListener('input', () => {
      const val   = contrasenaInp.value;
      let score   = 0;
      if (val.length >= 8)              score++;
      if (/[A-Z]/.test(val))           score++;
      if (/[0-9]/.test(val))           score++;
      if (/[^A-Za-z0-9]/.test(val))    score++;

      const colors = ['#E74C3C','#E74C3C','#F39C12','#27AE60','#1A6B8A'];
      const texts  = ['','Muy débil','Débil','Aceptable','Fuerte','Muy fuerte'];
      strengthBar.style.width     = (score * 25) + '%';
      strengthBar.style.background = colors[score];
      strengthText.textContent    = texts[score];
      strengthText.style.color    = colors[score];
    });
  }

  // Validación al enviar
  const form = document.getElementById('newPasswordForm');
  if (form) {
    form.addEventListener('submit', (e) => {
      let ok = true;
      const pass = contrasenaInp.value;
      const conf = document.getElementById('confirmar').value;
      const passErr = document.getElementById('contrasenaError');
      const confErr = document.getElementById('confirmarError');

      passErr.classList.remove('visible');
      confErr.classList.remove('visible');

      if (pass.length < 8 || !/[A-Z]/.test(pass) || !/[0-9]/.test(pass)) {
        passErr.classList.add('visible');
        contrasenaInp.focus();
        ok = false;
      }
      if (pass !== conf) {
        confErr.classList.add('visible');
        ok = false;
      }
      if (!ok) { e.preventDefault(); return; }

      document.getElementById('btnGuardar').disabled = true;
      lucide.createIcons();
    });
  }
</script>
</body>
</html>
