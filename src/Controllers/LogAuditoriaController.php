<?php declare(strict_types=1);
namespace App\Controllers;

use App\Database\Database;
use App\Models\LogAuditoriaModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * LogAuditoriaController — Vizualización del log de auditoría (solo Admin).
 * RNF-11: Log inmutable, retención mínima 1 año.
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
        // TODO: Semana 1 — Listar con filtros por usuario y fecha (HU-ADMIN-04)
        return $response;
    }
}
