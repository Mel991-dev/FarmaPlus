<?php

declare(strict_types=1);

namespace App\Services;

use PDO;
use App\Models\UsuarioModel;
use App\Models\LogAuditoriaModel;

/**
 * AuthService — Lógica de negocio de autenticación.
 *
 * - Hash bcrypt cost 12 (RNF-03)
 * - Bloqueo de cuenta tras 5 intentos fallidos / 15 minutos (RF-1.3, RN-15)
 * - Tokens de recuperación de contraseña (RF-1.4)
 */
class AuthService
{
    private UsuarioModel $usuarioModel;
    private LogAuditoriaModel $logModel;

    public function __construct(private PDO $db)
    {
        $this->usuarioModel = new UsuarioModel($db);
        $this->logModel     = new LogAuditoriaModel($db);
    }

    /**
     * Verificar credenciales y gestionar intentos fallidos.
     * Retorna el array del usuario si es válido, false en caso contrario.
     */
    public function verificarCredenciales(string $credencial, string $contrasena): array|false
    {
        $usuario = $this->usuarioModel->buscarPorCredencial($credencial);

        if (!$usuario) {
            return false;
        }

        // Verificar bloqueo activo
        if (!empty($usuario['bloqueado_hasta']) && strtotime($usuario['bloqueado_hasta']) > time()) {
            return false;
        }

        // Verificar contraseña
        if (!password_verify($contrasena, $usuario['contrasena_hash'])) {
            $this->usuarioModel->incrementarIntentosFallidos($usuario['usuario_id']);

            if ((int) $usuario['intentos_fallidos'] + 1 >= 5) {
                $this->usuarioModel->bloquearCuenta($usuario['usuario_id']);
            }
            return false;
        }

        // Login exitoso — resetear intentos
        $this->usuarioModel->resetearIntentos($usuario['usuario_id']);
        return $usuario;
    }

    /**
     * Generar hash bcrypt con cost 12 (RNF-03).
     */
    public function hashContrasena(string $contrasena): string
    {
        return password_hash($contrasena, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Generar token de recuperación de contraseña (un solo uso, 30 minutos).
     */
    public function generarTokenRecuperacion(int $usuarioId): string
    {
        $token = bin2hex(random_bytes(32));

        // Invalidar tokens anteriores del mismo usuario
        $sql  = "UPDATE recuperacion_contrasena SET usado = 1 WHERE usuario_id = :id AND usado = 0";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $usuarioId]);

        // Insertar nuevo token con expiración de 30 minutos
        $sql  = "INSERT INTO recuperacion_contrasena (usuario_id, token, expira_at)
                 VALUES (:usuario_id, :token, DATE_ADD(NOW(), INTERVAL 30 MINUTE))";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':usuario_id' => $usuarioId, ':token' => $token]);

        return $token;
    }

    /**
     * Verificar token de recuperación: activo, no usado, no expirado.
     */
    public function verificarTokenRecuperacion(string $token): array|false
    {
        $sql  = "SELECT rc.*, u.usuario_id, u.correo
                 FROM recuperacion_contrasena rc
                 INNER JOIN usuarios u ON rc.usuario_id = u.usuario_id
                 WHERE rc.token = :token
                   AND rc.usado = 0
                   AND rc.expira_at > NOW()
                 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':token' => $token]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Consumir token de recuperación (marcarlo como usado).
     */
    public function consumirToken(string $token): void
    {
        $sql  = "UPDATE recuperacion_contrasena SET usado = 1 WHERE token = :token";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':token' => $token]);
    }

    /**
     * Iniciar sesión PHP tras autenticación exitosa.
     */
    public function iniciarSesion(array $usuario): void
    {
        session_regenerate_id(true);
        $_SESSION['usuario_id'] = $usuario['usuario_id'];
        $_SESSION['rol']        = $usuario['rol_nombre'];
        $_SESSION['nombres']    = $usuario['nombres'];
        $_SESSION['apellidos']  = $usuario['apellidos'];
        $_SESSION['correo']     = $usuario['correo'];
    }

    /**
     * Destruir sesión PHP por completo.
     */
    public function cerrarSesion(): void
    {
        $_SESSION = [];
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 3600, $params['path'], $params['domain'],
                $params['secure'], $params['httponly']);
        }
        session_destroy();
    }
}
