<?php declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Models\LogAuditoriaModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * ConfiguracionController — Módulo 14: Configuración del Sistema.
 * Solo Administrador. Gestiona variables de negocio en la tabla `configuracion`.
 *
 * Rutas:
 *   GET  /admin/configuracion
 *   POST /admin/configuracion
 */
class ConfiguracionController
{
    private LogAuditoriaModel $logModel;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $this->logModel = new LogAuditoriaModel($db);
    }

    public function mostrar(Request $request, Response $response): Response
    {
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $db       = Database::getInstance()->getConnection();

        // Asegurar claves base del sistema (INSERT IGNORE)
        $clavesBase = [
            ['costo_envio_base',      '3000',    'Costo de envío fijo por domicilio (COP)'],
            ['stock_minimo_global',   '10',       'Stock mínimo global si el producto no tiene uno definido'],
            ['dias_alerta_vencim',    '30',       'Días antes del vencimiento para activar alerta'],
            ['nombre_farmacia',       'FarmaPlus', 'Nombre del establecimiento'],
            ['ciudad_cobertura',      'Neiva',    'Ciudad principal de cobertura de domicilios'],
            ['correo_notificaciones', '',          'Correo para notificaciones internas administrativas'],
        ];
        $stmtIns = $db->prepare("INSERT IGNORE INTO configuracion (clave, valor, descripcion) VALUES (:clave, :valor, :descripcion)");
        foreach ($clavesBase as [$clave, $valor, $desc]) {
            $stmtIns->execute([':clave' => $clave, ':valor' => $valor, ':descripcion' => $desc]);
        }

        // Leer todos los valores actuales
        $stmt = $db->query("SELECT * FROM configuracion ORDER BY clave ASC");
        $configuraciones = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        // Índice clave => valor para fácil acceso en la vista
        $config = [];
        foreach ($configuraciones as $row) {
            $config[$row['clave']] = $row;
        }

        $flash_success = $_SESSION['flash_success'] ?? null;
        $flash_error   = $_SESSION['flash_error']   ?? null;
        unset($_SESSION['flash_success'], $_SESSION['flash_error']);

        $titulo = 'Configuración del Sistema';
        ob_start();
        include __DIR__ . '/../../views/configuracion/index.php';
        $contenido = ob_get_clean();

        ob_start();
        include __DIR__ . '/../../views/layouts/base.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    public function actualizar(Request $request, Response $response): Response
    {
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $db       = Database::getInstance()->getConnection();
        $body     = (array) $request->getParsedBody();

        $stmt = $db->prepare(
            "UPDATE configuracion SET valor = :valor, editado_por = :uid WHERE clave = :clave"
        );
        $uid = (int) ($_SESSION['usuario_id'] ?? 0);

        foreach ($body as $clave => $valor) {
            // Evitar procesar tokens CSRF o campos no esperados
            if (!preg_match('/^[a-z_]+$/', $clave)) continue;

            $stmt->execute([
                ':clave' => $clave,
                ':valor' => trim((string)$valor),
                ':uid'   => $uid,
            ]);
        }

        $this->logModel->registrar(
            $uid,
            'configuracion_actualizada',
            'Se actualizaron parámetros del sistema',
            $_SERVER['REMOTE_ADDR'] ?? ''
        );

        $_SESSION['flash_success'] = 'Configuración guardada correctamente.';
        return $response->withHeader('Location', $basePath . '/admin/configuracion')->withStatus(302);
    }
}
