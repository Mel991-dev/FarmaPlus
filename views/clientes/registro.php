<?php
$titulo = 'Registro Médico';
$error = $error ?? $_GET['error'] ?? null;
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>FarmaPlus CRM — Registro Cliente</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <script src="https://unpkg.com/lucide@latest"></script>
  <link rel="stylesheet" href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/assets/css/app.min.css?v=<?= time() ?>" />
  <style>
    /* Bloqueo de scroll global: el usuario solo debe scrollear la caja del form */
    html, body {
        height: 100%;
        overflow: hidden;
    }
    .auth-wrapper {
        height: 100vh;
        overflow: hidden;
    }
    .form-panel {
        height: 100vh;
        overflow: hidden !important;
        padding: 0 !important;
    }
    .form-container {
        height: 100%;
        width: 100%;
        max-width: 500px !important;
        overflow-y: auto;
        padding: 40px !important;
        margin: 0 auto;
    }

    /* Scrollbar estético para el formulario */
    .form-container::-webkit-scrollbar { width: 6px; }
    .form-container::-webkit-scrollbar-track { background: transparent; }
    .form-container::-webkit-scrollbar-thumb { background: #CBD5E1; border-radius: 10px; }
    .form-container::-webkit-scrollbar-thumb:hover { background: #94A3B8; }
    
    /* En móviles, aseguramos paddings limpios */
    @media (max-width: 767px) {
        .form-container { padding: 30px 20px !important; }
    }
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
          <span class="logo-sub">Tu farmacia de confianza</span>
        </div>
      </div>
      <p class="brand-tagline">Únete y gestiona<br>tus medicamentos easily.</p>
      <p class="brand-desc">Al registrarte en FarmaPlus, tendrás un perfil único para comprar, consultar historial de fórmulas y gestionar envíos.</p>
      <div class="brand-divider"></div>
      <div class="feature-list">
        <div class="feature-item">
          <div class="feature-icon"><i data-lucide="shield-check"></i></div>
          <span>Privacidad 100% garantizada por la Ley 1581</span>
        </div>
        <div class="feature-item">
          <div class="feature-icon"><i data-lucide="clock"></i></div>
          <span>Guarda y automatiza domicilios a tu puerta</span>
        </div>
      </div>
    </div>
  </aside>

  <!-- PANEL DERECHO: FORMULARIO -->
  <main class="form-panel" role="main">
    <div class="form-container">

      <header class="form-header">
        <p class="form-welcome">Bienvenido a la comunidad</p>
        <h1 class="form-title">Crear cuenta</h1>
        <p class="form-subtitle">Ingresa tus datos personales a continuación.</p>
      </header>

      <?php if ($error): ?>
      <div class="alert-error visible" id="alertError" role="alert" style="margin-bottom:15px;">
        <i data-lucide="circle-x"></i>
        <div class="alert-error-text">
          <strong>Error en el registro</strong>
          <?= htmlspecialchars($error) ?>
        </div>
      </div>
      <?php endif; ?>

      <form id="registerForm" method="POST" action="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/registro" novalidate>
        
        <div style="display:flex; gap:15px; margin-bottom:15px;">
            <div class="form-group" style="flex:1; margin-bottom:0;">
                <label class="form-label" for="nombres">Nombres</label>
                <div class="input-wrap">
                    <i data-lucide="user" class="input-icon"></i>
                    <input type="text" id="nombres" name="nombres" class="form-input" placeholder="Ej: Maria" required autofocus />
                </div>
            </div>
            <div class="form-group" style="flex:1; margin-bottom:0;">
                <label class="form-label" for="apellidos">Apellidos</label>
                <div class="input-wrap">
                    <input type="text" id="apellidos" name="apellidos" class="form-input" placeholder="Ej: Perez" style="padding-left:14px;" required />
                </div>
            </div>
        </div>

        <div style="display:flex; gap:15px; margin-bottom:15px;">
            <div class="form-group" style="flex:0.6; margin-bottom:0;">
                <label class="form-label" for="tipo_documento">Tipo Doc.</label>
                <div class="input-wrap">
                    <select id="tipo_documento" name="tipo_documento" class="form-input" style="padding-left:14px; appearance:none;" required>
                        <option value="CC">CC</option>
                        <option value="CE">CE</option>
                        <option value="NIT">NIT</option>
                        <option value="PEP">PEP</option>
                    </select>
                </div>
            </div>
            <div class="form-group" style="flex:1; margin-bottom:0;">
                <label class="form-label" for="documento">Documento</label>
                <div class="input-wrap">
                    <i data-lucide="credit-card" class="input-icon"></i>
                    <input type="text" id="documento" name="documento" class="form-input" placeholder="Número" required />
                </div>
            </div>
        </div>

        <div class="form-group" style="margin-bottom:15px;">
          <label class="form-label" for="correo">Correo electrónico</label>
          <div class="input-wrap">
            <i data-lucide="mail" class="input-icon"></i>
            <input type="email" id="correo" name="correo" class="form-input" placeholder="tu@ejemplo.com" required autocomplete="username" />
          </div>
        </div>
        
        <div class="form-group" style="margin-bottom:15px;">
          <label class="form-label" for="telefono">Teléfono (Opcional)</label>
          <div class="input-wrap">
            <i data-lucide="phone" class="input-icon"></i>
            <input type="tel" id="telefono" name="telefono" class="form-input" placeholder="300 000 0000" />
          </div>
        </div>

        <div class="form-group" style="margin-bottom:20px;">
          <label class="form-label" for="contrasena">Contraseña</label>
          <div class="input-wrap">
            <i data-lucide="lock" class="input-icon"></i>
            <input type="password" id="contrasena" name="contrasena" class="form-input" placeholder="Mínimo 6 caracteres" required autocomplete="new-password" />
            <button type="button" class="toggle-password" id="togglePassword">
              <i data-lucide="eye" id="eyeIcon"></i>
            </button>
          </div>
        </div>

        <div class="form-group" style="margin-bottom:25px;background:var(--color-bg-card);padding:15px;border-radius:8px;border:1px solid var(--color-border);display:flex;align-items:flex-start;gap:12px;">
            <input type="checkbox" name="ley1581" id="ley1581" value="1" style="margin-top:4px;" required>
            <label for="ley1581" style="font-size:12px;color:var(--color-text-secondary);line-height:1.5;cursor:pointer;">
                <strong>Autorización de Datos - Ley 1581 🇨🇴</strong><br>
                Autorizo a FarmaPlus a registrar mi información y guardar mi IP segura (<?= $_SERVER['REMOTE_ADDR'] ?? 'Local' ?>) para la prestación de servicios, garantizando el respeto de mi privacidad y Hábeas Data.
            </label>
        </div>

        <button type="submit" class="btn-primary" id="btnRegister" style="width:100%;">
          <i data-lucide="user-plus"></i> Completar Registro
        </button>

        <div style="margin-top: 20px; text-align: center; font-size: 14px; color: #64748B;">
          ¿Ya tienes cuenta? <a href="<?= $_ENV['APP_BASEPATH'] ?? '' ?>/login" style="color: var(--color-primary); font-weight: 600; text-decoration: none;">Inicia Sesión aquí</a>
        </div>

      </form>

      <footer class="form-footer" style="margin-top:30px;">
        <p>Sistema FarmaPlus — Acceso para <strong>Personal y Clientes</strong></p>
        <p class="version-tag">v1.0.0 · FarmaPlus CRM · Colombia</p>
      </footer>

    </div>
  </main>
</div>

<script>
  lucide.createIcons();

  const togglePassword = document.getElementById('togglePassword');
  const contrasenaInp  = document.getElementById('contrasena');
  const eyeIcon        = document.getElementById('eyeIcon');
  const registerForm   = document.getElementById('registerForm');

  togglePassword.addEventListener('click', () => {
    const isPass = contrasenaInp.type === 'password';
    contrasenaInp.type = isPass ? 'text' : 'password';
    eyeIcon.setAttribute('data-lucide', isPass ? 'eye-off' : 'eye');
    lucide.createIcons();
  });

  registerForm.addEventListener('submit', (e) => {
      if(!document.getElementById('nombres').value || !document.getElementById('correo').value || !document.getElementById('contrasena').value || !document.getElementById('ley1581').checked){
          // Dejar que el form lance errores HTML5.
          return;
      }
      document.getElementById('btnRegister').disabled = true;
      document.getElementById('btnRegister').innerHTML = '<i data-lucide="loader-2" style="animation:spin 1s linear infinite;"></i> Creando cuenta...';
      lucide.createIcons();
  });
</script>
</body>
</html>
