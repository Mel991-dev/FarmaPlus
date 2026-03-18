<?php
/**
 * views/auth/recuperar.php
 * Vista de Recuperación de Contraseña — fiel al mockup fp-02-recuperar.html
 *
 * Variables inyectadas por AuthController:
 *   $enviado  bool         — TRUE después de enviar el correo correctamente
 *   $error    string|null  — Error del servidor
 */
$enviado = $enviado ?? false;
$error   = $error   ?? null;
$correo  = $correo  ?? '';
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FarmaPlus CRM — Recuperar contraseña</title>
  <meta name="description" content="Recuperación de contraseña para FarmaPlus CRM">
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <script src="https://unpkg.com/lucide@latest"></script>
  <style>
    :root {
      --color-primary:#1A6B8A; --color-primary-dark:#1A3A4A; --color-primary-light:#4A9BB5;
      --color-secondary:#2A9D8F; --color-bg-main:#F4F9FC; --color-bg-card:#E9F5F8;
      --color-success:#27AE60; --color-error:#E74C3C;
      --color-success-soft:#EAFAF1; --color-error-soft:#FDEDEC;
      --color-text-primary:#2C3E50; --color-text-secondary:#7F8C8D; --color-border:#BDC3C7;
      --color-sidebar-bg:#1A3A4A;
      --radius-sm:4px; --radius-md:8px; --radius-lg:12px; --radius-full:9999px;
      --font-main:'Inter',sans-serif; --font-mono:'JetBrains Mono',monospace;
      --shadow-focus:0 0 0 3px rgba(26,107,138,0.15);
    }
    *, *::before, *::after { box-sizing:border-box; margin:0; padding:0; }
    html, body { height:100%; font-family:var(--font-main); font-size:16px; color:var(--color-text-primary); background:var(--color-sidebar-bg); }
    .auth-wrapper { display:flex; min-height:100vh; }

    /* PANEL IZQUIERDO */
    .brand-panel { flex:1; background:var(--color-sidebar-bg); display:flex; flex-direction:column; justify-content:center; align-items:center; padding:48px 40px; position:relative; overflow:hidden; }
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

    /* Back link */
    .back-link { display:inline-flex; align-items:center; gap:6px; font-size:13px; font-weight:500; color:var(--color-text-secondary); text-decoration:none; margin-bottom:40px; transition:color 0.2s; border-radius:var(--radius-sm); }
    .back-link:hover { color:var(--color-primary); }
    .back-link svg { width:15px; height:15px; }

    /* STEPS */
    .steps-wrap { display:flex; align-items:center; justify-content:center; gap:8px; margin-bottom:32px; }
    .step { display:flex; align-items:center; gap:6px; }
    .step-dot { width:28px; height:28px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:12px; font-weight:700; transition:background 0.3s,color 0.3s; }
    .step-dot.active  { background:var(--color-primary); color:#fff; }
    .step-dot.done    { background:var(--color-success); color:#fff; }
    .step-dot.pending { background:#EEF2F3; color:var(--color-text-secondary); }
    .step-label { font-size:12px; font-weight:500; color:var(--color-text-secondary); }
    .step-label.active { color:var(--color-primary); font-weight:600; }
    .step-label.done   { color:var(--color-success);  font-weight:600; }
    .step-connector { flex:1; height:1.5px; background:var(--color-border); max-width:40px; border-radius:var(--radius-full); position:relative; overflow:hidden; }
    .step-connector-fill { position:absolute; top:0; left:0; height:100%; width:0%; background:var(--color-success); transition:width 0.5s ease; }

    /* LOCK icono */
    .lock-icon-wrap { width:72px; height:72px; background:var(--color-bg-card); border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 28px; border:2px solid rgba(26,107,138,0.12); }
    .lock-icon-wrap svg { width:32px; height:32px; color:var(--color-primary); }

    /* Header */
    .form-header { text-align:center; margin-bottom:36px; }
    .form-title { font-size:26px; font-weight:700; color:var(--color-text-primary); margin-bottom:10px; letter-spacing:-0.4px; }
    .form-subtitle { font-size:14px; color:var(--color-text-secondary); line-height:1.65; max-width:360px; margin:0 auto; }

    /* FORM */
    .form-group { margin-bottom:20px; }
    .form-label { display:block; font-size:14px; font-weight:500; color:var(--color-text-primary); margin-bottom:8px; }
    .input-wrap { position:relative; }
    .input-icon { position:absolute; left:14px; top:50%; transform:translateY(-50%); color:var(--color-text-secondary); width:18px; height:18px; pointer-events:none; transition:color 0.2s; }
    .input-wrap:focus-within .input-icon { color:var(--color-primary); }
    .form-input { width:100%; height:48px; padding:0 16px 0 44px; border:1.5px solid var(--color-border); border-radius:var(--radius-md); font-family:var(--font-main); font-size:15px; color:var(--color-text-primary); background:#fff; outline:none; transition:border-color 0.2s, box-shadow 0.2s; }
    .form-input::placeholder { color:#B0B8C1; }
    .form-input:focus { border-color:var(--color-primary); box-shadow:var(--shadow-focus); }
    .form-input.input-error { border-color:var(--color-error); }
    .field-error { display:none; align-items:center; gap:4px; margin-top:6px; font-size:12px; color:var(--color-error); }
    .field-error.visible { display:flex; }
    .field-error svg { width:13px; height:13px; flex-shrink:0; }
    .field-hint { font-size:12px; color:var(--color-text-secondary); margin-top:6px; display:flex; align-items:center; gap:4px; }
    .field-hint svg { width:13px; height:13px; flex-shrink:0; }

    /* BOTÓN */
    .btn-primary { width:100%; height:48px; background:var(--color-primary); color:#fff; border:none; border-radius:var(--radius-md); font-family:var(--font-main); font-size:15px; font-weight:600; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:8px; margin-top:8px; transition:background 0.2s, transform 0.1s, box-shadow 0.2s; letter-spacing:0.1px; }
    .btn-primary:hover { background:var(--color-primary-light); box-shadow:0 4px 16px rgba(26,107,138,0.28); }
    .btn-primary:active { transform:scale(0.99); }
    .btn-primary:disabled { opacity:0.55; cursor:not-allowed; }
    .btn-primary svg { width:18px; height:18px; }
    .spinner { width:18px; height:18px; border:2px solid rgba(255,255,255,0.35); border-top-color:#fff; border-radius:50%; animation:spin 0.7s linear infinite; display:none; }
    @keyframes spin { to { transform:rotate(360deg); } }
    .btn-primary.loading .spinner { display:block; }
    .btn-primary.loading .btn-text,.btn-primary.loading .btn-icon { display:none; }

    /* VISTAS */
    #viewForm    { display: <?= !$enviado ? 'block' : 'none' ?>; }
    #viewSuccess { display: <?=  $enviado ? 'block' : 'none' ?>; }

    /* SUCCESS CARD */
    .success-card { background:var(--color-success-soft); border:1px solid rgba(39,174,96,0.2); border-left:4px solid var(--color-success); border-radius:var(--radius-lg); padding:28px 28px 24px; text-align:center; }
    .success-icon-wrap { width:64px; height:64px; background:#fff; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 20px; box-shadow:0 2px 8px rgba(39,174,96,0.2); }
    .success-icon-wrap svg { width:30px; height:30px; color:var(--color-success); }
    .success-title { font-size:20px; font-weight:700; color:var(--color-text-primary); margin-bottom:10px; }
    .success-desc { font-size:14px; color:var(--color-text-secondary); line-height:1.65; margin-bottom:20px; }
    .success-email-pill { display:inline-flex; align-items:center; gap:6px; background:#fff; border:1px solid rgba(39,174,96,0.3); border-radius:var(--radius-full); padding:6px 14px; font-size:13px; font-weight:600; color:#1D6E3A; margin-bottom:20px; font-family:var(--font-mono); }
    .success-email-pill svg { width:14px; height:14px; color:var(--color-success); }
    .success-expiry { display:inline-flex; align-items:center; gap:6px; font-size:12px; color:#7D6608; background:#FEF9E7; border:1px solid rgba(243,156,18,0.25); border-radius:var(--radius-full); padding:5px 12px; font-weight:500; }
    .success-expiry svg { width:13px; height:13px; color:#F39C12; }
    .success-divider { height:1px; background:rgba(39,174,96,0.15); margin:20px 0; }
    .success-actions { display:flex; flex-direction:column; gap:10px; }
    .btn-secondary { width:100%; height:44px; background:transparent; border:2px solid var(--color-primary); border-radius:var(--radius-md); font-family:var(--font-main); font-size:14px; font-weight:600; color:var(--color-primary); cursor:pointer; display:flex; align-items:center; justify-content:center; gap:7px; transition:background 0.2s,color 0.2s; text-decoration:none; }
    .btn-secondary:hover { background:var(--color-bg-card); }
    .btn-secondary svg { width:16px; height:16px; }
    .resend-note { font-size:12px; color:var(--color-text-secondary); text-align:center; }
    .resend-note button { background:none; border:none; font-family:var(--font-main); font-size:12px; font-weight:600; color:var(--color-primary); cursor:pointer; padding:0; text-decoration:underline; }

    /* FOOTER */
    .form-footer { margin-top:28px; text-align:center; padding-top:20px; border-top:1px solid #F0F3F4; }
    .form-footer p { font-size:13px; color:var(--color-text-secondary); }
    .version-tag { margin-top:10px; font-size:11px; font-family:var(--font-mono); color:#C8D0D5; letter-spacing:0.5px; }

    /* ALERTA ERROR SERVIDOR */
    .alert-error { display:none; background:#FDEDEC; border:1px solid rgba(231,76,60,0.25); border-left:4px solid var(--color-error); border-radius:var(--radius-md); padding:12px 16px; margin-bottom:20px; gap:10px; align-items:center; }
    .alert-error.visible { display:flex; }
    .alert-error svg { color:var(--color-error); flex-shrink:0; width:16px; height:16px; }
    .alert-error-text { font-size:14px; color:#922B21; }

    @media (max-width:1023px) { .brand-panel { display:none; } .form-panel { background:var(--color-bg-main); padding:32px 24px; } .form-container { background:#fff; border-radius:var(--radius-lg); padding:36px 32px; box-shadow:0 2px 8px rgba(0,0,0,0.08); } }
    @media (max-width:767px) { .form-panel { padding:24px 16px; align-items:flex-start; padding-top:48px; } .form-container { padding:28px 20px; } .form-title { font-size:22px; } }
  </style>
</head>
<body>

<div class="auth-wrapper">

  <!-- PANEL IZQUIERDO -->
  <aside class="brand-panel" role="complementary" aria-label="Información de la plataforma">
    <div class="deco-circle"></div>
    <div class="brand-content">
      <div class="logo-wrap">
        <div class="logo-icon"><i data-lucide="pill"></i></div>
        <div class="logo-text">
          <span class="logo-name">Farma<span>Plus</span></span>
          <span class="logo-sub">Plataforma de gestión</span>
        </div>
      </div>
      <p class="brand-tagline">Recupera el acceso<br>a tu cuenta</p>
      <p class="brand-desc">Te enviaremos un enlace seguro para<br>restablecer tu contraseña en minutos.</p>
      <div class="brand-divider"></div>
      <div class="feature-list">
        <div class="feature-item"><div class="feature-icon"><i data-lucide="shield-check"></i></div><span>Enlace seguro con expiración de 30 min</span></div>
        <div class="feature-item"><div class="feature-icon"><i data-lucide="mail-check"></i></div><span>Envío inmediato a tu correo registrado</span></div>
        <div class="feature-item"><div class="feature-icon"><i data-lucide="lock-keyhole"></i></div><span>Nueva contraseña cifrada y protegida</span></div>
      </div>
    </div>
  </aside>

  <!-- PANEL DERECHO -->
  <main class="form-panel" role="main">
    <div class="form-container">

      <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/login" class="back-link" aria-label="Volver al inicio de sesión">
        <i data-lucide="arrow-left"></i>
        Volver al inicio de sesión
      </a>

      <!-- INDICADOR DE PASOS -->
      <div class="steps-wrap" aria-label="Pasos del proceso">
        <div class="step">
          <div class="step-dot <?= !$enviado ? 'active' : 'done' ?>" id="step1dot">
            <?php if ($enviado): ?><i data-lucide="check" style="width:14px;height:14px;"></i><?php else: ?>1<?php endif; ?>
          </div>
          <span class="step-label <?= !$enviado ? 'active' : 'done' ?>">Solicitar</span>
        </div>
        <div class="step-connector">
          <div class="step-connector-fill" style="width:<?= $enviado ? '100%' : '0%' ?>;"></div>
        </div>
        <div class="step">
          <div class="step-dot <?= $enviado ? 'active' : 'pending' ?>">2</div>
          <span class="step-label <?= $enviado ? 'active' : '' ?>">Enlace enviado</span>
        </div>
        <div class="step-connector"><div class="step-connector-fill"></div></div>
        <div class="step">
          <div class="step-dot pending">3</div>
          <span class="step-label">Nueva clave</span>
        </div>
      </div>

      <!-- VISTA 1: FORMULARIO -->
      <div id="viewForm">
        <div class="lock-icon-wrap" aria-hidden="true"><i data-lucide="lock-keyhole"></i></div>
        <header class="form-header">
          <h1 class="form-title">¿Olvidaste tu contraseña?</h1>
          <p class="form-subtitle">Ingresa el correo electrónico asociado a tu cuenta y te enviaremos un enlace para restablecerla.</p>
        </header>

        <!-- Error del servidor -->
        <?php if ($error): ?>
        <div class="alert-error visible" role="alert">
          <i data-lucide="circle-x"></i>
          <span class="alert-error-text"><?= htmlspecialchars($error) ?></span>
        </div>
        <?php endif; ?>

        <form id="resetForm" method="POST" action="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/recuperar-contrasena" novalidate>
          <div class="form-group">
            <label class="form-label" for="correo">Correo electrónico</label>
            <div class="input-wrap">
              <i data-lucide="mail" class="input-icon"></i>
              <input type="email" id="correo" name="correo" class="form-input <?= $error ? 'input-error' : '' ?>"
                     placeholder="correo@farmaplus.co" autocomplete="email"
                     value="<?= htmlspecialchars($_POST['correo'] ?? '') ?>"
                     aria-describedby="correoError correoHint" />
            </div>
            <div class="field-error" id="correoError" role="alert">
              <i data-lucide="alert-circle"></i>
              <span id="correoErrorMsg">Ingresa un correo electrónico válido.</span>
            </div>
            <p class="field-hint" id="correoHint">
              <i data-lucide="info"></i>
              Usa el correo con el que te registraron en el sistema.
            </p>
          </div>

          <button type="submit" class="btn-primary" id="btnEnviar">
            <div class="spinner"></div>
            <i data-lucide="send" class="btn-icon"></i>
            <span class="btn-text">Enviar enlace de recuperación</span>
          </button>
        </form>
      </div>

      <!-- VISTA 2: ÉXITO (cuando $enviado = true) -->
      <div id="viewSuccess" role="alert" aria-live="polite">
        <div class="success-card">
          <div class="success-icon-wrap" aria-hidden="true"><i data-lucide="mail-check"></i></div>
          <h2 class="success-title">¡Enlace enviado!</h2>
          <p class="success-desc">Hemos enviado un correo de recuperación a la dirección:</p>
          <div class="success-email-pill">
            <i data-lucide="at-sign"></i>
            <span><?= htmlspecialchars($correo) ?></span>
          </div>
          <div class="success-expiry">
            <i data-lucide="clock"></i>
            Este enlace expira en <strong>&nbsp;30 minutos</strong>
          </div>
          <div class="success-divider"></div>
          <p style="font-size:13px;color:var(--color-text-secondary);margin-bottom:16px;line-height:1.6;">
            Revisa también tu carpeta de <strong>spam o correo no deseado</strong> si no encuentras el mensaje.
          </p>
          <div class="success-actions">
            <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/login" class="btn-secondary">
              <i data-lucide="log-in"></i>
              Volver al inicio de sesión
            </a>
          </div>
        </div>
        <p class="resend-note" style="margin-top:16px;">
          ¿No recibiste el correo?
          <button type="button" id="btnReenviar" onclick="window.location='<?= $_ENV['APP_BASEPATH'] ?? '' ?>/recuperar-contrasena'">Reenviar enlace</button>
        </p>
      </div>

      <footer class="form-footer">
        <p>¿Necesitas ayuda? Contacta al <strong>administrador del sistema</strong>.</p>
        <p class="version-tag">v1.0.0 · FarmaPlus CRM · Colombia</p>
      </footer>

    </div>
  </main>

</div>

<script>
  lucide.createIcons();

  const resetForm  = document.getElementById('resetForm');
  const correoInp  = document.getElementById('correo');
  const correoErr  = document.getElementById('correoError');
  const correoMsg  = document.getElementById('correoErrorMsg');
  const btnEnviar  = document.getElementById('btnEnviar');

  if (correoInp && resetForm) {
    correoInp.addEventListener('input', () => {
      correoInp.classList.remove('input-error');
      correoErr.classList.remove('visible');
    });

    resetForm.addEventListener('submit', (e) => {
      const correo = correoInp.value.trim();
      const regex  = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
      if (!correo || !regex.test(correo)) {
        e.preventDefault();
        correoInp.classList.add('input-error');
        correoMsg.textContent = 'Ingresa un correo electrónico válido.';
        correoErr.classList.add('visible');
        correoInp.focus();
        lucide.createIcons();
        return;
      }
      btnEnviar.classList.add('loading');
      btnEnviar.disabled = true;
      lucide.createIcons();
    });
  }
</script>
</body>
</html>
