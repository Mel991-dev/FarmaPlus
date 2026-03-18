<?php declare(strict_types=1);
namespace App\Models;
use PDO;

/** DetalleVentaModel — Ítems de ventas presenciales. */
class DetalleVentaModel
{
    public function __construct(private PDO $db) {}

    public function insertar(array $datos): void
    {
        $sql  = "INSERT INTO detalle_venta (venta_id, producto_id, lote_id, cantidad, precio_unitario, subtotal)
                 VALUES (:venta_id, :producto_id, :lote_id, :cantidad, :precio_unitario, :subtotal)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($datos);
    }
}
