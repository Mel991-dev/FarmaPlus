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
  <link rel="stylesheet" href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/assets/css/app.min.css?v=<?= time() ?>" />
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
      <div id="viewForm" style="display: <?= !$enviado ? 'block' : 'none' ?>;">
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
      <div id="viewSuccess" role="alert" aria-live="polite" style="display: <?= $enviado ? 'block' : 'none' ?>;">
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
