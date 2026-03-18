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
  <style>
    :root { --color-primary:#1A6B8A; --color-primary-light:#4A9BB5; --color-secondary:#2A9D8F; --color-sidebar-bg:#1A3A4A; --color-success:#27AE60; --color-error:#E74C3C; --color-text-primary:#2C3E50; --color-text-secondary:#7F8C8D; --color-border:#BDC3C7; --color-bg-card:#E9F5F8; --radius-md:8px; --radius-lg:12px; --radius-full:9999px; --font-main:'Inter',sans-serif; --font-mono:'JetBrains Mono',monospace; --shadow-focus:0 0 0 3px rgba(26,107,138,0.15); }
    *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
    html, body { height:100%; font-family:var(--font-main); background:var(--color-sidebar-bg); color:var(--color-text-primary); }
    .auth-wrapper { display:flex; min-height:100vh; }
    .brand-panel { flex:1; background:var(--color-sidebar-bg); display:flex; flex-direction:column; justify-content:center; align-items:center; padding:48px 40px; position:relative; overflow:hidden; }
    .brand-panel::before { content:''; position:absolute; width:400px; height:400px; border-radius:50%; border:1.5px solid rgba(42,157,143,0.18); top:-80px; right:-80px; }
    .brand-content { position:relative; z-index:1; text-align:center; max-width:360px; }
    .logo-wrap { display:inline-flex; align-items:center; gap:12px; margin-bottom:40px; }
    .logo-icon { width:52px; height:52px; background:var(--color-secondary); border-radius:var(--radius-lg); display:flex; align-items:center; justify-content:center; box-shadow:0 4px 16px rgba(42,157,143,0.35); }
    .logo-icon svg { color:#fff; width:28px; height:28px; }
    .logo-text { display:flex; flex-direction:column; align-items:flex-start; }
    .logo-name { font-size:26px; font-weight:700; color:#fff; letter-spacing:-0.5px; }
    .logo-name span { color:var(--color-secondary); }
    .logo-sub { font-size:11px; font-weight:500; color:rgba(236,240,241,0.55); text-transform:uppercase; letter-spacing:1.5px; margin-top:3px; }
    .brand-tagline { font-size:18px; font-weight:600; color:#ECF0F1; line-height:1.4; margin-bottom:12px; }
    .brand-desc { font-size:14px; color:rgba(236,240,241,0.6); line-height:1.6; }
    .brand-divider { width:48px; height:2px; background:var(--color-secondary); border-radius:var(--radius-full); margin:32px auto; opacity:0.5; }
    .feature-list { display:flex; flex-direction:column; gap:12px; text-align:left; }
    .feature-item { display:flex; align-items:center; gap:12px; }
    .feature-icon { width:32px; height:32px; border-radius:var(--radius-md); background:rgba(42,157,143,0.15); display:flex; align-items:center; justify-content:center; flex-shrink:0; }
    .feature-icon svg { color:var(--color-secondary); width:16px; height:16px; }
    .feature-item span { font-size:14px; color:rgba(236,240,241,0.75); font-weight:500; }
    .form-panel { flex:1; background:#fff; display:flex; flex-direction:column; justify-content:center; align-items:center; padding:48px 40px; }
    .form-container { width:100%; max-width:440px; }
    .back-link { display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:500; color:var(--color-text-secondary); text-decoration:none; margin-bottom:40px; transition:color 0.2s; }
    .back-link:hover { color:var(--color-primary); }
    .back-link svg { width:15px; height:15px; }
    .lock-icon-wrap { width:72px; height:72px; background:var(--color-bg-card); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 28px; border:2px solid rgba(26,107,138,0.12); }
    .lock-icon-wrap svg { width:32px; height:32px; color:var(--color-primary); }
    .form-header { text-align:center; margin-bottom:36px; }
    .form-title { font-size:26px; font-weight:700; color:var(--color-text-primary); margin-bottom:10px; }
    .form-subtitle { font-size:14px; color:var(--color-text-secondary); line-height:1.65; }
    .form-group { margin-bottom:20px; }
    .form-label { display:block; font-size:14px; font-weight:500; color:var(--color-text-primary); margin-bottom:8px; }
    .input-wrap { position:relative; }
    .input-icon { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--color-text-secondary); width:18px; height:18px; pointer-events:none; }
    .input-wrap:focus-within .input-icon { color:var(--color-primary); }
    .form-input { width:100%; height:48px; padding:0 44px 0 44px; border:1.5px solid var(--color-border); border-radius:var(--radius-md); font-family:var(--font-main); font-size:15px; outline:none; transition:border-color 0.2s, box-shadow 0.2s; }
    .form-input::placeholder { color:#B0B8C1; }
    .form-input:focus { border-color:var(--color-primary); box-shadow:var(--shadow-focus); }
    .form-input.input-error { border-color:var(--color-error); }
    .toggle-password { position:absolute; right:14px; top:50%; transform:translateY(-50%); background:none; border:none; cursor:pointer; color:var(--color-text-secondary); display:flex; align-items:center; padding:4px; border-radius:4px; }
    .toggle-password:hover { color:var(--color-primary); }
    .toggle-password svg { width:18px; height:18px; }
    .field-error { display:none; align-items:center; gap:4px; margin-top:6px; font-size:12px; color:var(--color-error); }
    .field-error.visible { display:flex; }
    .field-error svg { width:13px; height:13px; }
    .alert-error { background:#FDEDEC; border:1px solid rgba(231,76,60,0.25); border-left:4px solid var(--color-error); border-radius:var(--radius-md); padding:12px 16px; margin-bottom:20px; display:flex; gap:10px; align-items:center; }
    .alert-error svg { color:var(--color-error); flex-shrink:0; width:16px; height:16px; }
    .alert-error-text { font-size:14px; color:#922B21; }
    .alert-expired { background:#FEF9E7; border:1px solid rgba(243,156,18,0.3); border-left:4px solid #F39C12; border-radius:var(--radius-md); padding:16px; text-align:center; }
    .alert-expired svg { color:#F39C12; width:32px; height:32px; margin-bottom:12px; }
    .alert-expired h2 { font-size:18px; font-weight:700; color:var(--color-text-primary); margin-bottom:8px; }
    .alert-expired p { font-size:14px; color:var(--color-text-secondary); margin-bottom:16px; }
    .btn-primary { width:100%; height:48px; background:var(--color-primary); color:#fff; border:none; border-radius:var(--radius-md); font-family:var(--font-main); font-size:15px; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; margin-top:8px; transition:background 0.2s; }
    .btn-primary:hover { background:var(--color-primary-light); }
    .btn-primary:disabled { opacity:0.6; cursor:not-allowed; }
    .btn-primary svg { width:18px; height:18px; }
    .btn-outline { width:100%; height:44px; border:2px solid var(--color-primary); border-radius:var(--radius-md); font-family:var(--font-main); font-size:14px; font-weight:600; color:var(--color-primary); background:transparent; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:7px; text-decoration:none; transition:background 0.2s; }
    .btn-outline:hover { background:var(--color-bg-card); }
    .btn-outline svg { width:16px; height:16px; }
    /* Indicador de fortaleza */
    .strength-bar-wrap { height:4px; background:#F0F3F4; border-radius:9999px; overflow:hidden; margin-top:8px; }
    .strength-bar { height:100%; border-radius:9999px; transition:width 0.3s, background 0.3s; width:0%; }
    .strength-text { font-size:11px; margin-top:4px; font-weight:600; }
    .form-footer { margin-top:24px; text-align:center; padding-top:20px; border-top:1px solid #F0F3F4; }
    .form-footer p { font-size:13px; color:var(--color-text-secondary); }
    .version-tag { margin-top:10px; font-size:11px; font-family:var(--font-mono); color:#C8D0D5; }
    @media (max-width:1023px) { .brand-panel { display:none; } .form-panel { background:#F4F9FC; padding:32px 24px; } .form-container { background:#fff; border-radius:var(--radius-lg); padding:36px 32px; box-shadow:0 2px 8px rgba(0,0,0,0.08); } }
  </style>
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
