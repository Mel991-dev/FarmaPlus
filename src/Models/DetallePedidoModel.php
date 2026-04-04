<?php declare(strict_types=1);
namespace App\Models;
use PDO;

/** DetallePedidoModel — Ítems de pedidos en línea. */
class DetallePedidoModel
{
    public function __construct(private PDO $db) {}

    public function insertar(array $datos): void
    {
        $sql  = "INSERT INTO detalle_pedido (pedido_id, producto_id, lote_id, cantidad, precio_unitario, subtotal)
                 VALUES (:pedido_id, :producto_id, :lote_id, :cantidad, :precio_unitario, :subtotal)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($datos);
    }

    /** Alias de insertar() compatible con TiendaController (sin lote_id). */
    public function crear(array $datos): void
    {
        $sql  = "INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio_unitario, subtotal)
                 VALUES (:pedido_id, :producto_id, :cantidad, :precio_unitario, :subtotal)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':pedido_id'      => $datos['pedido_id'],
            ':producto_id'    => $datos['producto_id'],
            ':cantidad'       => $datos['cantidad'],
            ':precio_unitario' => $datos['precio_unitario'],
            ':subtotal'       => $datos['subtotal'],
        ]);
    }

    public function obtenerPorPedido(int $pedidoId): array
    {
        $sql  = "SELECT dp.*, p.nombre AS producto_nombre
                 FROM detalle_pedido dp
                 INNER JOIN productos p ON dp.producto_id = p.producto_id
                 WHERE dp.pedido_id = :pedido_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':pedido_id' => $pedidoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
