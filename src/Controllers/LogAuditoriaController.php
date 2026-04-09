<?php declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Models\LogAuditoriaModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * LogAuditoriaController — Visualización del log de auditoría (solo Admin).
 * RNF-11: Log inmutable, retención mínima 1 año.
 * Ruta: GET /admin/logs
 */
class LogAuditoriaController
{
    private LogAuditoriaModel $logModel;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $this->logModel = new LogAuditoriaModel($db);
    }

    public function listar(Request $request, Response $response): Response
    {
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $params   = $request->getQueryParams();

        $filtroUsuario = trim($params['usuario'] ?? '');
        $filtroAccion  = trim($params['accion']  ?? '');
        $filtroFecha   = $params['fecha'] ?? '';

        $db  = Database::getInstance()->getConnection();
        $sql = "SELECT l.*, CONCAT(u.nombres, ' ', u.apellidos) AS nombre_usuario, r.nombre AS rol
                FROM   logs_auditoria l
                INNER  JOIN usuarios u ON u.usuario_id = l.usuario_id
                INNER  JOIN roles    r ON r.rol_id     = u.rol_id
                WHERE  1=1";
        $bind = [];

        if ($filtroUsuario !== '') {
            $sql .= " AND (u.nombres LIKE :usr OR u.apellidos LIKE :usr2 OR u.documento LIKE :usr3)";
            $bind[':usr']  = "%{$filtroUsuario}%";
            $bind[':usr2'] = "%{$filtroUsuario}%";
            $bind[':usr3'] = "%{$filtroUsuario}%";
        }
        if ($filtroAccion !== '') {
            $sql .= " AND l.accion LIKE :acc";
            $bind[':acc'] = "%{$filtroAccion}%";
        }
        if ($filtroFecha !== '') {
            $sql .= " AND DATE(l.created_at) = :fecha";
            $bind[':fecha'] = $filtroFecha;
        }

        $sql .= " ORDER BY l.created_at DESC LIMIT 200";
        $stmt = $db->prepare($sql);
        $stmt->execute($bind);
        $logs = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $titulo = 'Logs de Auditoría';
        ob_start();
        include __DIR__ . '/../../views/admin/logs.php';
        $contenido = ob_get_clean();

        ob_start();
        include __DIR__ . '/../../views/layouts/base.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }
}
