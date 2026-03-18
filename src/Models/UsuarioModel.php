<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

/**
 * UsuarioModel — Consultas sobre la tabla `usuarios` y `roles`.
 * Todos los métodos usan prepared statements.
 */
class UsuarioModel
{
    public function __construct(private PDO $db) {}

    /* ── LECTURA ─────────────────────────────────────────────── */

    /**
     * Buscar usuario activo por correo o número de documento.
     * Incluye el nombre del rol para la sesión PHP.
     */
    public function buscarPorCredencial(string $credencial): array|false
    {
        $sql = "SELECT u.*, r.nombre AS rol_nombre
                FROM   usuarios u
                INNER JOIN roles r ON u.rol_id = r.rol_id
                WHERE  u.activo = 1
                  AND  (u.correo = :cred1 OR u.documento = :cred2)
                LIMIT  1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cred1' => $credencial, ':cred2' => $credencial]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarPorId(int $id): array|false
    {
        $sql  = "SELECT u.*, r.nombre AS rol_nombre
                 FROM   usuarios u
                 INNER JOIN roles r ON u.rol_id = r.rol_id
                 WHERE  u.usuario_id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function buscarPorCorreo(string $correo): array|false
    {
        $sql  = "SELECT u.*, r.nombre AS rol_nombre
                 FROM   usuarios u
                 INNER JOIN roles r ON u.rol_id = r.rol_id
                 WHERE  u.correo = :correo AND u.activo = 1 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':correo' => $correo]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function listar(): array
    {
        $sql  = "SELECT u.*, r.nombre AS rol_nombre
                 FROM   usuarios u
                 INNER JOIN roles r ON u.rol_id = r.rol_id
                 ORDER  BY u.apellidos ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /* ── CREACIÓN / MODIFICACIÓN ─────────────────────────────── */

    public function crear(array $datos): string
    {
        $sql  = "INSERT INTO usuarios
                    (rol_id, tipo_documento, documento, nombres, apellidos, correo, telefono, contrasena_hash)
                 VALUES
                    (:rol_id, :tipo_documento, :documento, :nombres, :apellidos, :correo, :telefono, :contrasena_hash)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($datos);
        return $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): int
    {
        $datos[':id'] = $id;
        $sql = "UPDATE usuarios
                SET    nombres=:nombres, apellidos=:apellidos, correo=:correo, telefono=:telefono, activo=:activo
                WHERE  usuario_id=:id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($datos);
        return $stmt->rowCount();
    }

    public function cambiarContrasena(int $id, string $hash): int
    {
        $sql  = "UPDATE usuarios SET contrasena_hash=:hash WHERE usuario_id=:id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':hash' => $hash, ':id' => $id]);
        return $stmt->rowCount();
    }

    /* ── CONTROL DE ACCESO ───────────────────────────────────── */

    /** Incrementar contador de intentos fallidos */
    public function incrementarIntentosFallidos(int $id): void
    {
        $sql  = "UPDATE usuarios SET intentos_fallidos = intentos_fallidos + 1 WHERE usuario_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    /** Bloquear cuenta por 15 minutos (RN-15) */
    public function bloquearCuenta(int $id): void
    {
        $sql  = "UPDATE usuarios
                 SET    bloqueado_hasta = DATE_ADD(NOW(), INTERVAL 15 MINUTE)
                 WHERE  usuario_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    /** Resetear intentos después de login exitoso */
    public function resetearIntentos(int $id): void
    {
        $sql  = "UPDATE usuarios
                 SET    intentos_fallidos = 0, bloqueado_hasta = NULL
                 WHERE  usuario_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
    }

    /* ── ELIMINACIÓN LÓGICA ──────────────────────────────────── */

    public function desactivar(int $id): int
    {
        $sql  = "UPDATE usuarios SET activo = 0 WHERE usuario_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->rowCount();
    }
}
