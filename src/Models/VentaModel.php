<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

/** VentaModel — Consultas sobre `ventas_presenciales` y `detalle_venta`. */
class VentaModel
{
    public function __construct(private PDO $db) {}

    public function iniciarTransaccion(): void { $this->db->beginTransaction(); }
    public function confirmarTransaccion(): void { $this->db->commit(); }
    public function revertirTransaccion(): void { $this->db->rollBack(); }

    public function crear(array $datos): string
    {
        $sql  = "INSERT INTO ventas_presenciales (numero_comprobante, vendedor_id, subtotal, total, metodo_pago, formula_medica)
                 VALUES (:numero_comprobante, :vendedor_id, :subtotal, :total, :metodo_pago, :formula_medica)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($datos);
        return $this->db->lastInsertId();
    }

    public function insertarDetalle(array $datos): void
    {
        $sql  = "INSERT INTO detalle_venta (venta_id, producto_id, lote_id, cantidad, precio_unitario, subtotal)
                 VALUES (:venta_id, :producto_id, :lote_id, :cantidad, :precio_unitario, :subtotal)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($datos);
    }

    public function generarNumeroComprobante(): string
    {
        $anio = date('Y');
        $sql  = "SELECT COUNT(*) AS total FROM ventas_presenciales WHERE YEAR(created_at) = :anio";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':anio' => $anio]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        $seq = str_pad((string)($row['total'] + 1), 4, '0', STR_PAD_LEFT);
        return "FP-{$anio}-{$seq}";
    }

    public function obtenerConDetalle(int $ventaId): array|false
    {
        $sql  = "SELECT v.*, CONCAT(u.nombres, ' ', u.apellidos) AS vendedor_nombre
                 FROM ventas_presenciales v
                 INNER JOIN usuarios u ON v.vendedor_id = u.usuario_id
                 WHERE v.venta_id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $ventaId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerDetalle(int $ventaId): array
    {
        $sql  = "SELECT dv.*, p.nombre AS producto_nombre, p.codigo_invima, p.control_especial, l.numero_lote
                 FROM detalle_venta dv
                 INNER JOIN productos p ON dv.producto_id = p.producto_id
                 LEFT JOIN lotes l ON dv.lote_id = l.lote_id
                 WHERE dv.venta_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $ventaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function misVentas(int $vendedorId): array
    {
        $sql  = "SELECT * FROM ventas_presenciales WHERE vendedor_id = :id AND DATE(created_at) = CURDATE() ORDER BY created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $vendedorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
