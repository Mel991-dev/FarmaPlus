<?php declare(strict_types=1);
namespace App\Models;
use PDO;

/** LogAuditoriaModel — Registro inmutable de acciones críticas. */
class LogAuditoriaModel
{
    public function __construct(private PDO $db) {}

    /**
     * Registrar una acción en el log de auditoría.
     * El log no puede ser modificado por ningún usuario (RNF-11).
     */
    public function registrar(int $usuarioId, string $accion, string $detalle = '', string $ip = ''): void
    {
        $sql  = "INSERT INTO logs_auditoria (usuario_id, accion, detalle, ip, created_at)
                 VALUES (:usuario_id, :accion, :detalle, :ip, NOW())";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':usuario_id' => $usuarioId,
            ':accion'     => $accion,
            ':detalle'    => $detalle,
            ':ip'         => $ip ?: ($_SERVER['REMOTE_ADDR'] ?? '0.0.0.0'),
        ]);
    }

    public function listar(array $filtros = []): array
    {
        $sql  = "SELECT l.*, CONCAT(u.nombres, ' ', u.apellidos) AS usuario_nombre
                 FROM logs_auditoria l
                 INNER JOIN usuarios u ON l.usuario_id = u.usuario_id
                 ORDER BY l.created_at DESC
                 LIMIT 500";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
