<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

/**
 * ClienteModel — Consultas PDO sobre las tablas `clientes` y `direcciones_entrega`.
 *
 * El consentimiento Ley 1581/2012 (fecha, hora, IP) se registra en la tabla `clientes`.
 */
class ClienteModel
{
    public function __construct(private PDO $db) {}

    public function obtenerPorUsuarioId(int $usuarioId): array|false
    {
        $sql  = "SELECT c.*, u.correo, u.documento, u.nombres, u.apellidos, u.telefono
                 FROM clientes c
                 INNER JOIN usuarios u ON c.usuario_id = u.usuario_id
                 WHERE c.usuario_id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $usuarioId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear(array $datos): string
    {
        $sql = "INSERT INTO clientes (usuario_id, consentimiento_ley1581, fecha_consentimiento, ip_consentimiento)
                VALUES (:usuario_id, 1, NOW(), :ip)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($datos);
        return $this->db->lastInsertId();
    }

    public function listarDirecciones(int $clienteId): array
    {
        $sql  = "SELECT * FROM direcciones_entrega
                 WHERE cliente_id = :cliente_id
                 ORDER BY predeterminada DESC, created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cliente_id' => $clienteId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crearDireccion(array $datos): string
    {
        $sql = "INSERT INTO direcciones_entrega (cliente_id, alias, direccion, barrio, ciudad, referencia, predeterminada)
                VALUES (:cliente_id, :alias, :direccion, :barrio, :ciudad, :referencia, :predeterminada)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($datos);
        return $this->db->lastInsertId();
    }

    public function obtenerDireccion(int $dirId, int $clienteId): array|false
    {
        $sql = "SELECT * FROM direcciones_entrega WHERE direccion_id = :id AND cliente_id = :cliente_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $dirId, ':cliente_id' => $clienteId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function actualizarDireccion(int $dirId, int $clienteId, array $datos): int
    {
        $sql = "UPDATE direcciones_entrega 
                SET alias = :alias, direccion = :direccion, barrio = :barrio, ciudad = :ciudad, referencia = :referencia, predeterminada = :predeterminada
                WHERE direccion_id = :id AND cliente_id = :cliente_id";
        
        $datos[':id'] = $dirId;
        $datos[':cliente_id'] = $clienteId;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($datos);
        return $stmt->rowCount();
    }

    public function desmarcarPredeterminadas(int $clienteId): void
    {
        $sql = "UPDATE direcciones_entrega SET predeterminada = 0 WHERE cliente_id = :cliente_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cliente_id' => $clienteId]);
    }

    public function eliminarDireccion(int $dirId, int $clienteId): int
    {
        $sql  = "DELETE FROM direcciones_entrega WHERE direccion_id = :id AND cliente_id = :cliente_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $dirId, ':cliente_id' => $clienteId]);
        return $stmt->rowCount();
    }

    /**
     * Alias de listarDirecciones() — compatibilidad con TiendaController.
     * También mapea el campo `predeterminada` como `principal` para las vistas.
     */
    public function obtenerDirecciones(int $clienteId): array
    {
        $dirs = $this->listarDirecciones($clienteId);
        return array_map(function ($d) {
            $d['principal'] = (bool)($d['predeterminada'] ?? false);
            return $d;
        }, $dirs);
    }
}

