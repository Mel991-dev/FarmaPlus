<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

/**
 * AjusteStockModel — Trazabilidad de todos los movimientos de inventario.
 *
 * Módulo 5: Control de Inventario (RN-05)
 * Tipos: entrada | salida_venta | salida_pedido | ajuste_manual | baja_vencimiento
 */
class AjusteStockModel
{
    public function __construct(private PDO $db) {}

    /**
     * Registrar un ajuste de stock (movimiento).
     */
    public function registrar(
        int $productoId,
        ?int $loteId,
        int $usuarioId,
        string $tipo,
        int $cantidad,
        string $observacion = ''
    ): int {
        $sql = "INSERT INTO ajustes_stock 
                    (producto_id, lote_id, usuario_id, tipo, cantidad, observacion, created_at)
                VALUES 
                    (:producto_id, :lote_id, :usuario_id, :tipo, :cantidad, :observacion, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':producto_id'  => $productoId,
            ':lote_id'      => $loteId,
            ':usuario_id'   => $usuarioId,
            ':tipo'         => $tipo,
            ':cantidad'     => $cantidad,
            ':observacion'  => $observacion,
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Historial de movimientos de un producto específico.
     */
    public function historialPorProducto(int $productoId, int $limite = 50): array
    {
        $sql = "SELECT a.*, 
                       u.nombres, u.apellidos,
                       l.numero_lote
                FROM ajustes_stock a
                INNER JOIN usuarios u ON a.usuario_id = u.usuario_id
                LEFT JOIN lotes l ON a.lote_id = l.lote_id
                WHERE a.producto_id = :producto_id
                ORDER BY a.created_at DESC
                LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':producto_id', $productoId, PDO::PARAM_INT);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Último N ajustes globales (para Dashboard/auditoría).
     */
    public function ultimos(int $limite = 20): array
    {
        $sql = "SELECT a.*, 
                       p.nombre AS producto_nombre,
                       u.nombres, u.apellidos,
                       l.numero_lote
                FROM ajustes_stock a
                INNER JOIN productos p ON a.producto_id = p.producto_id
                INNER JOIN usuarios u ON a.usuario_id = u.usuario_id
                LEFT JOIN lotes l ON a.lote_id = l.lote_id
                ORDER BY a.created_at DESC
                LIMIT :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
