<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Models\UsuarioModel;
use App\Models\LogAuditoriaModel;
use App\Services\AuthService;
use App\Services\EmailService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
use Slim\Views\PhpRenderer;

/**
 * AuthController — Módulo 1: Autenticación (RF-1.1 a RF-1.5)
 *
 * Rutas:
 *   GET  /login                → mostrarLogin
 *   POST /login                → procesarLogin
 *   POST /logout               → logout
 *   GET  /recuperar-contrasena → mostrarRecuperar
 *   POST /recuperar-contrasena → procesarRecuperar
 *   GET  /nueva-contrasena     → mostrarNuevaContrasena
 *   POST /nueva-contrasena     → procesarNuevaContrasena
 */
class AuthController
{
    private UsuarioModel      $usuarioModel;
    private LogAuditoriaModel $logModel;
    private AuthService       $authService;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $this->usuarioModel = new UsuarioModel($db);
        $this->logModel     = new LogAuditoriaModel($db);
        $this->authService  = new AuthService($db);
    }

    /* ── LOGIN ─────────────────────────────────────────────── */

    public function mostrarLogin(Request $request, Response $response): Response
    {
        // Si ya tiene sesión activa, redirigir al dashboard correspondiente
        if (!empty($_SESSION['usuario_id'])) {
            return $this->redirigirPorRol($response, $_SESSION['rol'] ?? '');
        }

        return $this->renderizar($response, 'auth/login.php', [
            'error'   => null,
            'blocked' => false,
        ]);
    }

    public function procesarLogin(Request $request, Response $response): Response
    {
        $body      = (array) $request->getParsedBody();
        $credencial = trim($body['credencial'] ?? '');
        $contrasena = trim($body['contrasena'] ?? '');

        // Validación básica
        if (empty($credencial) || empty($contrasena)) {
            return $this->renderizar($response, 'auth/login.php', [
                'error'   => 'Completa todos los campos.',
                'blocked' => false,
            ]);
        }

        // Verificar si la cuenta está bloqueada antes de consultar
        $usuario = $this->usuarioModel->buscarPorCredencial($credencial);

        if ($usuario && !empty($usuario['bloqueado_hasta'])
            && strtotime($usuario['bloqueado_hasta']) > time()) {
            return $this->renderizar($response, 'auth/login.php', [
                'error'   => 'Cuenta bloqueada temporalmente por múltiples intentos fallidos.',
                'blocked' => true,
            ]);
        }

        // Verificar credenciales a través del AuthService
        $usuarioValido = $this->authService->verificarCredenciales($credencial, $contrasena);

        if (!$usuarioValido) {
            // Log de intento fallido
            if ($usuario) {
                $this->logModel->registrar(
                    $usuario['usuario_id'],
                    'login_fallido',
                    "Intento fallido para: {$credencial}",
                    $_SERVER['REMOTE_ADDR'] ?? ''
                );
            }
            return $this->renderizar($response, 'auth/login.php', [
                'error'   => 'Correo/cédula o contraseña incorrectos.',
                'blocked' => false,
            ]);
        }

        // Login exitoso — iniciar sesión PHP
        $this->authService->iniciarSesion($usuarioValido);

        // Log de auditoría (RF-1.5)
        $this->logModel->registrar(
            $usuarioValido['usuario_id'],
            'login_exitoso',
            "Sesión iniciada por: {$credencial}",
            $_SERVER['REMOTE_ADDR'] ?? ''
        );

        // Redirigir al dashboard del rol
        return $this->redirigirPorRol($response, $usuarioValido['rol_nombre']);
    }

    /* ── LOGOUT ────────────────────────────────────────────── */

    public function logout(Request $request, Response $response): Response
    {
        if (!empty($_SESSION['usuario_id'])) {
            $this->logModel->registrar(
                (int) $_SESSION['usuario_id'],
                'logout',
                'Sesión cerrada',
                $_SERVER['REMOTE_ADDR'] ?? ''
            );
        }
        $this->authService->cerrarSesion();

        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        return $response
            ->withHeader('Location', $basePath . '/login')
            ->withStatus(302);
    }

    /* ── RECUPERACIÓN DE CONTRASEÑA ────────────────────────── */

    public function mostrarRecuperar(Request $request, Response $response): Response
    {
        return $this->renderizar($response, 'auth/recuperar.php', [
            'enviado' => false,
            'error'   => null,
            'correo'  => '',
        ]);
    }

    public function procesarRecuperar(Request $request, Response $response): Response
    {
        $body   = (array) $request->getParsedBody();
        $correo = trim(strtolower($body['correo'] ?? ''));

        // Validar formato
        if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
            return $this->renderizar($response, 'auth/recuperar.php', [
                'enviado' => false,
                'error'   => 'Ingresa un correo electrónico válido.',
                'correo'  => $correo,
            ]);
        }

        // Buscar usuario sin revelar si existe o no (seguridad: respuesta siempre positiva)
        $usuario = $this->usuarioModel->buscarPorCorreo($correo);

        if ($usuario) {
            $token  = $this->authService->generarTokenRecuperacion($usuario['usuario_id']);
            $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
            $enlace   = ($_ENV['APP_URL'] ?? 'http://localhost') . $basePath . '/nueva-contrasena?token=' . $token;

            // Enviar correo (no bloquear si falla)
            try {
                $emailService = new EmailService();
                $emailService->enviarRecuperacion(
                    $usuario['correo'],
                    $usuario['nombres'] . ' ' . $usuario['apellidos'],
                    $enlace
                );
            } catch (\Throwable) {
                // Silenciar — el token quedó guardado de todas formas
            }

            $this->logModel->registrar(
                $usuario['usuario_id'],
                'recuperacion_solicitada',
                "Solicitud de recuperación para: {$correo}",
                $_SERVER['REMOTE_ADDR'] ?? ''
            );
        }

        // Siempre mostrar éxito (no filtrar correos existentes)
        return $this->renderizar($response, 'auth/recuperar.php', [
            'enviado' => true,
            'error'   => null,
            'correo'  => $this->enmascararCorreo($correo),
        ]);
    }

    /* ── NUEVA CONTRASEÑA ──────────────────────────────────── */

    public function mostrarNuevaContrasena(Request $request, Response $response): Response
    {
        $params = $request->getQueryParams();
        $token  = trim($params['token'] ?? '');

        $registro = $this->authService->verificarTokenRecuperacion($token);

        if (!$registro) {
            return $this->renderizar($response, 'auth/nueva_contrasena.php', [
                'error'       => 'El enlace es inválido o ha expirado. Solicita uno nuevo.',
                'token_valido' => false,
                'token'       => '',
            ]);
        }

        return $this->renderizar($response, 'auth/nueva_contrasena.php', [
            'error'       => null,
            'token_valido' => true,
            'token'       => $token,
        ]);
    }

    public function procesarNuevaContrasena(Request $request, Response $response): Response
    {
        $body          = (array) $request->getParsedBody();
        $token         = trim($body['token'] ?? '');
        $contrasena    = $body['contrasena'] ?? '';
        $confirmar     = $body['confirmar']  ?? '';

        // Validar token
        $registro = $this->authService->verificarTokenRecuperacion($token);
        if (!$registro) {
            return $this->renderizar($response, 'auth/nueva_contrasena.php', [
                'error'       => 'El enlace es inválido o ha expirado.',
                'token_valido' => false,
                'token'       => '',
            ]);
        }

        // Validar política de contraseña (mínimo 8 chars, 1 mayúscula, 1 número)
        if (strlen($contrasena) < 8
            || !preg_match('/[A-Z]/', $contrasena)
            || !preg_match('/[0-9]/', $contrasena)) {
            return $this->renderizar($response, 'auth/nueva_contrasena.php', [
                'error'       => 'La contraseña debe tener mínimo 8 caracteres, una mayúscula y un número.',
                'token_valido' => true,
                'token'       => $token,
            ]);
        }

        if ($contrasena !== $confirmar) {
            return $this->renderizar($response, 'auth/nueva_contrasena.php', [
                'error'       => 'Las contraseñas no coinciden.',
                'token_valido' => true,
                'token'       => $token,
            ]);
        }

        // Cambiar contraseña y consumir token
        $hash = $this->authService->hashContrasena($contrasena);
        $this->usuarioModel->cambiarContrasena((int) $registro['usuario_id'], $hash);
        $this->authService->consumirToken($token);

        $this->logModel->registrar(
            (int) $registro['usuario_id'],
            'contrasena_cambiada',
            'Contraseña restablecida mediante enlace de recuperación',
            $_SERVER['REMOTE_ADDR'] ?? ''
        );

        // Redirigir al login con mensaje flash en sesión
        $_SESSION['flash_msg']  = '¡Contraseña actualizada! Ya puedes iniciar sesión.';
        $_SESSION['flash_tipo'] = 'success';

        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        return $response
            ->withHeader('Location', $basePath . '/login')
            ->withStatus(302);
    }

    /* ── HELPERS PRIVADOS ──────────────────────────────────── */

    /**
     * Renderizar una vista PHP con variables.
     */
    private function renderizar(Response $response, string $vista, array $vars = []): Response
    {
        extract($vars);
        $rutaVista = __DIR__ . '/../../views/' . $vista;

        if (!file_exists($rutaVista)) {
            $response->getBody()->write("<h1>Vista no encontrada: {$vista}</h1>");
            return $response->withStatus(500);
        }

        ob_start();
        include $rutaVista;
        $html = ob_get_clean();

        $response->getBody()->write($html);
        return $response;
    }

    /**
     * Redirigir al dashboard según el rol del usuario.
     */
    private function redirigirPorRol(Response $response, string $rol): Response
    {
        $destinos = [
            'administrador' => '/dashboard',
            'gerente'       => '/gerente/dashboard',
            'auxiliar'      => '/inventario/productos',
            'vendedor'      => '/ventas/pos',
            'repartidor'    => '/pedidos/repartidor',
            'cliente'       => '/tienda',
        ];

        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $destino = $basePath . ($destinos[$rol] ?? '/dashboard');

        return $response
            ->withHeader('Location', $destino)
            ->withStatus(302);
    }

    /**
     * Enmascarar correo para mostrarlo de forma segura: u***@dominio.com
     */
    private function enmascararCorreo(string $correo): string
    {
        if (!str_contains($correo, '@')) {
            return $correo;
        }
        [$user, $domain] = explode('@', $correo, 2);
        $masked = strlen($user) <= 2 ? $user[0] . '***' : substr($user, 0, 2) . '***' . substr($user, -1);
        return $masked . '@' . $domain;
    }
}
