<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Models\AlertaModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * DashboardController
 * Renderiza el dashboard principal con KPIs y datos reales de sesión.
 *
 * Ruta: GET /dashboard
 */
class DashboardController
{
    public function index(Request $request, Response $response): Response
    {
        $db = Database::getInstance()->getConnection();

        // ── KPI: Ventas del día ─────────────────────────────────
        $stmtVentas = $db->prepare(
            "SELECT COALESCE(SUM(total), 0) AS ventas_dia
             FROM   ventas
             WHERE  DATE(created_at) = CURDATE()"
        );
        $stmtVentas->execute();
        $ventasDia = (float) ($stmtVentas->fetchColumn() ?? 0);

        // ── KPI: Pedidos pendientes ─────────────────────────────
        $stmtPedidos = $db->prepare(
            "SELECT COUNT(*) FROM pedidos WHERE estado IN ('pendiente','preparando')"
        );
        $stmtPedidos->execute();
        $pedidosPendientes = (int) ($stmtPedidos->fetchColumn() ?? 0);

        // ── KPI: Clientes registrados ───────────────────────────
        $stmtClientes = $db->prepare(
            "SELECT COUNT(*) FROM usuarios u
             INNER JOIN roles r ON u.rol_id = r.rol_id
             WHERE  r.nombre = 'cliente' AND u.activo = 1"
        );
        $stmtClientes->execute();
        $clientesTotal = (int) ($stmtClientes->fetchColumn() ?? 0);

        // ── KPI: Alertas activas ────────────────────────────────
        $alertaModel  = new AlertaModel($db);
        $alertas      = $alertaModel->obtenerActivas();
        $alertasTotal = count($alertas);

        // ── Últimas 5 ventas del día ────────────────────────────
        $stmtUltimas = $db->prepare(
            "SELECT v.numero_comprobante, v.total, v.created_at,
                    CONCAT(u.nombres, ' ', u.apellidos) AS vendedor_nombre
             FROM   ventas v
             LEFT JOIN usuarios u ON v.vendedor_id = u.usuario_id
             WHERE  DATE(v.created_at) = CURDATE()
             ORDER  BY v.created_at DESC
             LIMIT  5"
        );
        $stmtUltimas->execute();
        $ventasHoy = $stmtUltimas->fetchAll(\PDO::FETCH_ASSOC);

        // ── Renderizar ──────────────────────────────────────────
        $vars = [
            'usuario'    => [
                'nombres'    => $_SESSION['nombres']   ?? 'Usuario',
                'apellidos'  => $_SESSION['apellidos'] ?? '',
                'rol_nombre' => $_SESSION['rol']       ?? 'usuario',
            ],
            'alertas'    => $alertas,
            'ventas_hoy' => $ventasHoy,
            'kpis'       => [
                'ventas_dia'         => $ventasDia,
                'pedidos_pendientes' => $pedidosPendientes,
                'alertas_total'      => $alertasTotal,
                'clientes_total'     => $clientesTotal,
            ],
        ];

        extract($vars);
        ob_start();
        include __DIR__ . '/../../views/dashboard/index.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);
        return $response;
    }
}
