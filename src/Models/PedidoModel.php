<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

/**
 * PedidoModel — Consultas PDO sobre las tablas `pedidos` y `detalle_pedido`.
 */
class PedidoModel
{
    public function __construct(private PDO $db) {}

    public function listar(array $filtros = []): array
    {
        $sql  = "SELECT p.*, CONCAT(u.nombres, ' ', u.apellidos) AS cliente_nombre,
                        r.nombre AS repartidor_nombre
                 FROM pedidos p
                 INNER JOIN clientes c ON p.cliente_id = c.cliente_id
                 INNER JOIN usuarios u ON c.usuario_id = u.usuario_id
                 LEFT JOIN usuarios r ON p.repartidor_id = r.usuario_id
                 ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): array|false
    {
        $sql  = "SELECT p.*, CONCAT(u.nombres, ' ', u.apellidos) AS cliente_nombre,
                        u.correo AS cliente_correo, d.direccion, d.barrio, d.ciudad, d.referencia
                 FROM pedidos p
                 INNER JOIN clientes c ON p.cliente_id = c.cliente_id
                 INNER JOIN usuarios u ON c.usuario_id = u.usuario_id
                 LEFT JOIN direcciones_entrega d ON p.direccion_entrega_id = d.direccion_id
                 WHERE p.pedido_id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerDetalle(int $pedidoId): array
    {
        $sql  = "SELECT dp.*, p.nombre AS producto_nombre
                 FROM detalle_pedido dp
                 INNER JOIN productos p ON dp.producto_id = p.producto_id
                 WHERE dp.pedido_id = :pedido_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':pedido_id' => $pedidoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear(array $datos): string
    {
        $sql  = "INSERT INTO pedidos (cliente_id, direccion_entrega_id, estado, subtotal, costo_envio, total, mp_referencia)
                 VALUES (:cliente_id, :direccion_entrega_id, 'pendiente', :subtotal, :costo_envio, :total, :mp_referencia)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($datos);
        return $this->db->lastInsertId();
    }

    public function actualizarEstado(int $id, string $estado): int
    {
        $sql  = "UPDATE pedidos SET estado = :estado WHERE pedido_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':estado' => $estado, ':id' => $id]);
        return $stmt->rowCount();
    }

    public function asignarRepartidor(int $pedidoId, int $repartidorId): int
    {
        $sql  = "UPDATE pedidos SET repartidor_id = :repartidor_id, estado = 'en_preparacion' WHERE pedido_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':repartidor_id' => $repartidorId, ':id' => $pedidoId]);
        return $stmt->rowCount();
    }

    public function misPedidosRepartidor(int $repartidorId): array
    {
        $sql  = "SELECT p.*, CONCAT(u.nombres, ' ', u.apellidos) AS cliente_nombre,
                        d.direccion, d.barrio, d.ciudad, d.referencia, u.telefono AS cliente_telefono
                 FROM pedidos p
                 INNER JOIN clientes c ON p.cliente_id = c.cliente_id
                 INNER JOIN usuarios u ON c.usuario_id = u.usuario_id
                 LEFT JOIN direcciones_entrega d ON p.direccion_entrega_id = d.direccion_id
                 WHERE p.repartidor_id = :repartidor_id
                   AND p.estado NOT IN ('entregado', 'cancelado')
                 ORDER BY p.created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':repartidor_id' => $repartidorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
