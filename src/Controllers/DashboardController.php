<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Models\AlertaModel;
use App\Models\ReporteModel;
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

        $stmtVentas = $db->prepare(
            "SELECT COALESCE(SUM(total), 0) AS ventas_dia
             FROM   ventas_presenciales
             WHERE  DATE(created_at) = CURDATE()"
        );
        $stmtVentas->execute();
        $ventasDia = (float) ($stmtVentas->fetchColumn() ?? 0);

        $stmtPedidos = $db->prepare(
            "SELECT COUNT(*) FROM pedidos WHERE estado IN ('pendiente','en_preparacion')"
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
        $alertas      = $alertaModel->listarActivas();
        $alertasTotal = count($alertas);

        $stmtUltimas = $db->prepare(
            "SELECT v.numero_comprobante, v.total, v.created_at,
                    CONCAT(u.nombres, ' ', u.apellidos) AS vendedor_nombre
             FROM   ventas_presenciales v
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

    // ─────────────────────────────────────────────────────────
    // GET /gerente/dashboard
    // ─────────────────────────────────────────────────────────
    public function gerente(Request $request, Response $response): Response
    {
        $db    = Database::getInstance()->getConnection();
        $model = new ReporteModel($db);

        // KPIs comparativa mensual
        $comparativa  = $model->comparativaVentasMensual();
        $mesActual    = (float) ($comparativa['mes_actual']   ?? 0);
        $mesAnterior  = (float) ($comparativa['mes_anterior'] ?? 0);
        $variacionPct = $mesAnterior > 0
            ? round((($mesActual - $mesAnterior) / $mesAnterior) * 100, 1)
            : ($mesActual > 0 ? 100 : 0);

        // Mini-gráfica ventas últimos 7 días
        $ventas7dias = $model->ventasUltimos7Dias();

        // Top 5 productos esta semana
        $desde7     = date('Y-m-d', strtotime('-6 days'));
        $hoy        = date('Y-m-d');
        $topSemana  = $model->productosMasVendidos($desde7, $hoy, 5);

        // Pedidos por estado (todo el mes)
        $inicioMes    = date('Y-m-01');
        $pedidosEstado = $model->pedidosPorEstado($inicioMes, $hoy);

        // Alertas activas
        $alertaModel  = new AlertaModel($db);
        $alertas      = $alertaModel->listarActivas();
        $alertasTotal = count($alertas);

        // Vendedor del mes (top 1)
        $vendedores  = $model->rendimientoPorVendedor($inicioMes, $hoy);
        $topVendedor = $vendedores[0] ?? null;

        // KPIs período actual (mes corriente)
        $kpisMes = $model->kpisVentasPeriodo($inicioMes, $hoy);

        $usuario = [
            'nombres'    => $_SESSION['nombres']   ?? 'Gerente',
            'apellidos'  => $_SESSION['apellidos'] ?? '',
            'rol_nombre' => $_SESSION['rol']       ?? 'gerente',
        ];

        $titulo = 'Dashboard Gerencial';
        ob_start();
        include __DIR__ . '/../../views/dashboard/gerente.php';
        $contenido = ob_get_clean();

        ob_start();
        include __DIR__ . '/../../views/layouts/base.php';
        $html = ob_get_clean();

        $response->getBody()->write($html);
        return $response;
    }
}
