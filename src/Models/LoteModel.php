<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

/**
 * LoteModel — Consultas PDO sobre la tabla `lotes`.
 *
 * Clave para el método FEFO (First Expired, First Out).
 * El stock se ordena por fecha_vencimiento ASC para descontar el más próximo a vencer.
 */
class LoteModel
{
    public function __construct(private PDO $db) {}

    /**
     * Obtener lotes activos de un producto ordenados por FEFO.
     * Usado por FEFOService para descontar stock.
     */
    public function obtenerLotesFEFO(int $productoId): array
    {
        $sql = "SELECT lote_id, numero_lote, cantidad_actual, fecha_vencimiento
                FROM lotes
                WHERE producto_id = :producto_id
                  AND cantidad_actual > 0
                  AND activo = 1
                ORDER BY fecha_vencimiento ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':producto_id' => $productoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Registrar nueva entrada de lote (RF-4.2) */
    public function registrar(array $datos): string
    {
        $sql = "INSERT INTO lotes (producto_id, proveedor_id, numero_lote, cantidad_inicial,
                cantidad_actual, fecha_vencimiento, fecha_entrada, registrado_por)
                VALUES (:producto_id, :proveedor_id, :numero_lote, :cantidad_inicial,
                :cantidad_inicial, :fecha_vencimiento, NOW(), :registrado_por)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($datos);
        return $this->db->lastInsertId();
    }

    /** Descontar unidades de un lote específico */
    public function descontarUnidades(int $loteId, int $cantidad): int
    {
        $sql  = "UPDATE lotes SET cantidad_actual = cantidad_actual - :cantidad
                 WHERE lote_id = :lote_id AND cantidad_actual >= :cantidad";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':cantidad' => $cantidad, ':lote_id' => $loteId]);
        return $stmt->rowCount();
    }

    /** Lotes próximos a vencer (< N días configurables) */
    public function lotesPorVencer(int $dias = 30): array
    {
        $sql  = "SELECT l.*, p.nombre AS producto_nombre
                 FROM lotes l
                 INNER JOIN productos p ON l.producto_id = p.producto_id
                 WHERE l.fecha_vencimiento <= DATE_ADD(CURDATE(), INTERVAL :dias DAY)
                   AND l.fecha_vencimiento >= CURDATE()
                   AND l.cantidad_actual > 0
                   AND l.activo = 1
                 ORDER BY l.fecha_vencimiento ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':dias' => $dias]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Listar todos los lotes con información del producto */
    public function listarConProducto(): array
    {
        $sql  = "SELECT l.*, p.nombre AS producto_nombre, pr.nombre AS proveedor_nombre
                 FROM lotes l
                 INNER JOIN productos p ON l.producto_id = p.producto_id
                 LEFT JOIN proveedores pr ON l.proveedor_id = pr.proveedor_id
                 ORDER BY l.fecha_vencimiento ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
