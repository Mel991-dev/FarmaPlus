<?php declare(strict_types=1);
namespace App\Models;
use PDO;

/** AlertaModel — Consultas sobre la tabla `alertas`. */
class AlertaModel
{
    public function __construct(private PDO $db) {}

    public function listarActivas(): array
    {
        $sql  = "SELECT a.*, p.nombre AS producto_nombre, l.numero_lote, l.fecha_vencimiento
                 FROM alertas a
                 INNER JOIN productos p ON a.producto_id = p.producto_id
                 LEFT JOIN lotes l ON a.lote_id = l.lote_id
                 WHERE a.estado = 'activa'
                 ORDER BY a.tipo ASC, a.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear(int $productoId, ?int $loteId, string $tipo, string $mensaje): string
    {
        $sql  = "INSERT INTO alertas (producto_id, lote_id, tipo, mensaje, estado)
                 VALUES (:producto_id, :lote_id, :tipo, :mensaje, 'activa')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':producto_id' => $productoId, ':lote_id' => $loteId, ':tipo' => $tipo, ':mensaje' => $mensaje]);
        return $this->db->lastInsertId();
    }

    public function resolver(int $alertaId): int
    {
        $sql  = "UPDATE alertas SET estado = 'resuelta', resuelta_at = NOW() WHERE alerta_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $alertaId]);
        return $stmt->rowCount();
    }

    /** Verificar si ya existe una alerta activa para ese producto/lote/tipo */
    public function existeActiva(int $productoId, ?int $loteId, string $tipo): bool
    {
        $sql  = "SELECT COUNT(*) FROM alertas WHERE producto_id=:p AND lote_id<=>:l AND tipo=:t AND estado='activa'";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':p' => $productoId, ':l' => $loteId, ':t' => $tipo]);
        return (int) $stmt->fetchColumn() > 0;
    }
}
