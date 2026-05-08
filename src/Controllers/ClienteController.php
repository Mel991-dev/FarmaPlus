<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Models\ClienteModel;
use App\Models\DevolucionModel;
use App\Models\UsuarioModel;
use App\Services\AuthService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * ClienteController — Registro y perfil del cliente.
 *
 * Módulo 2: Gestión de Clientes (RF-2.1 a RF-2.5)
 * - Autoregistro con consentimiento Ley 1581/2012 (RN-16, RN-17)
 * - Gestión de múltiples direcciones de entrega
 * - Historial de pedidos
 */
class ClienteController
{
    private ClienteModel $clienteModel;
    private DevolucionModel $devolucionModel;
    private UsuarioModel $usuarioModel;
    private AuthService  $authService;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $this->clienteModel = new ClienteModel($db);
        $this->devolucionModel = new DevolucionModel($db);
        $this->usuarioModel = new UsuarioModel($db);
        $this->authService  = new AuthService($db);
    }

    /** GET /registro */
    public function mostrarRegistro(Request $request, Response $response): Response
    {
        ob_start();
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $error = $request->getQueryParams()['error'] ?? null;
        include __DIR__ . '/../../views/clientes/registro.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    /** POST /registro */
    public function registrar(Request $request, Response $response): Response
    {
        $body = (array) $request->getParsedBody();
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');

        // 1. Validar campos obligatorios
        $required = ['documento', 'nombres', 'apellidos', 'correo', 'contrasena'];
        foreach ($required as $field) {
            if (empty($body[$field])) {
                return $response->withHeader('Location', $basePath . '/registro?error=Faltan campos obligatorios')->withStatus(302);
            }
        }

        // 2. Verificar consentimiento Ley 1581 explícito (Checkbox)
        if (empty($body['ley1581'])) {
            return $response->withHeader('Location', $basePath . '/registro?error=Debe aceptar la política de tratamiento de datos')->withStatus(302);
        }

        // 3. Buscar rol "cliente"
        $rolId = $this->usuarioModel->obtenerRolIdPorNombre('cliente');
        if (!$rolId) {
            return $response->withHeader('Location', $basePath . '/registro?error=Error interno: Rol no encontrado')->withStatus(302);
        }

        try {
            $db = Database::getInstance()->getConnection();
            $db->beginTransaction();

            // Insertar Usuario
            $hash = $this->authService->hashContrasena($body['contrasena']);
            $usuarioId = $this->usuarioModel->crear([
                ':rol_id'          => $rolId,
                ':tipo_documento'  => $body['tipo_documento'] ?? 'CC',
                ':documento'       => $body['documento'],
                ':nombres'         => $body['nombres'],
                ':apellidos'       => $body['apellidos'],
                ':correo'          => $body['correo'],
                ':telefono'        => $body['telefono'] ?? '',
                ':contrasena_hash' => $hash
            ]);

            // Insertar Cliente (Ley 1581)
            $this->clienteModel->crear([
                ':usuario_id' => $usuarioId,
                ':ip'         => $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1'
            ]);

            $db->commit();

            $_SESSION['flash_msg'] = 'Registro exitoso. Ya puedes iniciar sesión.';
            $_SESSION['flash_tipo'] = 'success';
            return $response->withHeader('Location', $basePath . '/login')->withStatus(302);

        } catch (\PDOException $e) {
            $db->rollBack();
            // 1062 es Duplicate entry (correo o documento ya registrado)
            $errorMsg = $e->errorInfo[1] === 1062 ? 'El correo o documento ya se encuentra registrado.' : 'Error al procesar el registro.';
            return $response->withHeader('Location', $basePath . '/registro?error=' . urlencode($errorMsg))->withStatus(302);
        }
    }

    /** GET /mi-cuenta */
    public function perfil(Request $request, Response $response): Response
    {
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $cliente = $this->clienteModel->obtenerPorUsuarioId((int)$_SESSION['usuario_id']);
        
        if (!$cliente) {
            return $response->withHeader('Location', $basePath . '/login')->withStatus(302);
        }

        ob_start();
        $titulo = 'Mi Perfil';
        $error = $_SESSION['flash_error'] ?? null;
        $success = $_SESSION['flash_msg'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_msg']);
        
        include __DIR__ . '/../../views/clientes/perfil.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    /** POST /mi-cuenta/actualizar */
    public function actualizar(Request $request, Response $response): Response
    {
        $body = (array) $request->getParsedBody();
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $usuarioId = (int)$_SESSION['usuario_id'];

        // Validaciones básicas
        if (empty($body['nombres']) || empty($body['apellidos']) || empty($body['correo'])) {
            $_SESSION['flash_error'] = 'Los campos nombre, apellidos y correo son obligatorios.';
            return $response->withHeader('Location', $basePath . '/mi-cuenta')->withStatus(302);
        }

        // Evitar duplicidad de correos
        $existente = $this->usuarioModel->buscarPorCorreo($body['correo']);
        if ($existente && $existente['usuario_id'] !== $usuarioId) {
            $_SESSION['flash_error'] = 'El correo ya está en uso por otra cuenta.';
            return $response->withHeader('Location', $basePath . '/mi-cuenta')->withStatus(302);
        }

        // Actualizar datos básicos (excluyendo contraseña y rol)
        $this->usuarioModel->actualizar($usuarioId, [
            ':nombres'   => $body['nombres'],
            ':apellidos' => $body['apellidos'],
            ':correo'    => $body['correo'],
            ':telefono'  => $body['telefono'] ?? '',
            ':activo'    => 1 // El cliente sigue activo
        ]);

        // Actualizar sesión para reflejar en el header/sidebar visualmente
        $_SESSION['nombres'] = $body['nombres'];
        $_SESSION['apellidos'] = $body['apellidos'];

        $_SESSION['flash_msg'] = 'Perfil actualizado exitosamente.';
        return $response->withHeader('Location', $basePath . '/mi-cuenta')->withStatus(302);
    }

    /** GET /mi-cuenta/pedidos */
    public function misPedidos(Request $request, Response $response): Response
    {
        $basePath  = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $usuarioId = (int)($_SESSION['usuario_id'] ?? 0);

        $cliente = $this->clienteModel->obtenerPorUsuarioId($usuarioId);
        if (!$cliente) {
            return $response->withHeader('Location', $basePath . '/login')->withStatus(302);
        }

        // Filtrar por cliente_id (la tabla pedidos no tiene usuario_id directamente)
        $db  = Database::getInstance()->getConnection();
        $sql = "SELECT p.*
                FROM pedidos p
                WHERE p.cliente_id = :cliente_id
                ORDER BY p.created_at DESC
                LIMIT 50";
        $stmt = $db->prepare($sql);
        $stmt->execute([':cliente_id' => (int)$cliente['cliente_id']]);
        $pedidos = $stmt->fetchAll(\PDO::FETCH_ASSOC);

        $detallesPorPedido = [];
        $devolucionesPorPedido = [];

        if (!empty($pedidos)) {
            $pedidoIds = array_map(static fn(array $p) => (int)$p['pedido_id'], $pedidos);
            $placeholders = implode(',', array_fill(0, count($pedidoIds), '?'));

            $sqlDetalles = "SELECT dp.pedido_id, dp.producto_id, dp.cantidad, dp.precio_unitario,
                                   pr.nombre AS producto_nombre, pr.control_especial
                            FROM detalle_pedido dp
                            INNER JOIN productos pr ON pr.producto_id = dp.producto_id
                            WHERE dp.pedido_id IN ({$placeholders})
                            ORDER BY dp.pedido_id DESC, dp.detalle_id ASC";
            $stmtDetalles = $db->prepare($sqlDetalles);
            $stmtDetalles->execute($pedidoIds);
            foreach ($stmtDetalles->fetchAll(\PDO::FETCH_ASSOC) as $det) {
                $detallesPorPedido[(int)$det['pedido_id']][] = $det;
            }

            $sqlDevoluciones = "SELECT d.devolucion_id, d.pedido_id, d.producto_id, d.estado, d.cantidad, d.motivo, d.created_at,
                                       pr.nombre AS producto_nombre
                                FROM devoluciones d
                                INNER JOIN productos pr ON pr.producto_id = d.producto_id
                                WHERE d.pedido_id IN ({$placeholders})
                                ORDER BY d.created_at DESC";
            $stmtDevs = $db->prepare($sqlDevoluciones);
            $stmtDevs->execute($pedidoIds);
            foreach ($stmtDevs->fetchAll(\PDO::FETCH_ASSOC) as $dev) {
                $devolucionesPorPedido[(int)$dev['pedido_id']][] = $dev;
            }
        }

        $error = $_SESSION['flash_error'] ?? null;
        $success = $_SESSION['flash_msg'] ?? null;
        unset($_SESSION['flash_error'], $_SESSION['flash_msg']);

        $titulo = 'Mis Pedidos';
        ob_start();
        require __DIR__ . '/../../views/clientes/mis_pedidos.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    /** POST /mi-cuenta/pedidos/{id}/devoluciones */
    public function solicitarDevolucionPedido(Request $request, Response $response, array $args): Response
    {
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $usuarioId = (int)($_SESSION['usuario_id'] ?? 0);
        $pedidoId = (int)($args['id'] ?? 0);
        $body = (array)$request->getParsedBody();

        $cliente = $this->clienteModel->obtenerPorUsuarioId($usuarioId);
        if (!$cliente) {
            return $response->withHeader('Location', $basePath . '/login')->withStatus(302);
        }

        $db = Database::getInstance()->getConnection();
        $stmtPedido = $db->prepare(
            "SELECT pedido_id, estado, updated_at
             FROM pedidos
             WHERE pedido_id = :pedido_id AND cliente_id = :cliente_id
             LIMIT 1"
        );
        $stmtPedido->execute([
            ':pedido_id' => $pedidoId,
            ':cliente_id' => (int)$cliente['cliente_id'],
        ]);
        $pedido = $stmtPedido->fetch(\PDO::FETCH_ASSOC);

        if (!$pedido) {
            $_SESSION['flash_error'] = 'El pedido no existe o no pertenece a tu cuenta.';
            return $response->withHeader('Location', $basePath . '/mi-cuenta/pedidos')->withStatus(302);
        }

        if (($pedido['estado'] ?? '') !== 'entregado') {
            $_SESSION['flash_error'] = 'Solo puedes solicitar devoluciones de pedidos entregados.';
            return $response->withHeader('Location', $basePath . '/mi-cuenta/pedidos')->withStatus(302);
        }

        $fechaEntrega = new \DateTime($pedido['updated_at'] ?? 'now');
        $hoy = new \DateTime('now');
        if ($this->diasHabilesTranscurridos($fechaEntrega, $hoy) > 5) {
            $_SESSION['flash_error'] = 'La devolución excede el plazo de 5 días hábiles desde la entrega.';
            return $response->withHeader('Location', $basePath . '/mi-cuenta/pedidos')->withStatus(302);
        }

        $productoId = (int)($body['producto_id'] ?? 0);
        $cantidad = (int)($body['cantidad'] ?? 0);
        $motivo = trim((string)($body['motivo'] ?? ''));
        $observacion = trim((string)($body['observacion'] ?? ''));
        $motivosPermitidos = ['producto_danado', 'error_envio', 'cambio_opinion', 'vencido', 'otro'];

        if ($productoId <= 0 || $cantidad <= 0 || !in_array($motivo, $motivosPermitidos, true)) {
            $_SESSION['flash_error'] = 'Debes completar correctamente el formulario de devolución.';
            return $response->withHeader('Location', $basePath . '/mi-cuenta/pedidos')->withStatus(302);
        }

        $stmtDetalle = $db->prepare(
            "SELECT dp.cantidad, pr.control_especial, pr.nombre
             FROM detalle_pedido dp
             INNER JOIN productos pr ON pr.producto_id = dp.producto_id
             WHERE dp.pedido_id = :pedido_id AND dp.producto_id = :producto_id
             LIMIT 1"
        );
        $stmtDetalle->execute([
            ':pedido_id' => $pedidoId,
            ':producto_id' => $productoId,
        ]);
        $detalle = $stmtDetalle->fetch(\PDO::FETCH_ASSOC);

        if (!$detalle) {
            $_SESSION['flash_error'] = 'El producto seleccionado no pertenece a este pedido.';
            return $response->withHeader('Location', $basePath . '/mi-cuenta/pedidos')->withStatus(302);
        }

        if ((int)($detalle['control_especial'] ?? 0) === 1) {
            $_SESSION['flash_error'] = 'Los medicamentos de control especial no aplican para devolución.';
            return $response->withHeader('Location', $basePath . '/mi-cuenta/pedidos')->withStatus(302);
        }

        if ($cantidad > (int)$detalle['cantidad']) {
            $_SESSION['flash_error'] = 'La cantidad solicitada supera la cantidad comprada.';
            return $response->withHeader('Location', $basePath . '/mi-cuenta/pedidos')->withStatus(302);
        }

        $stmtPendiente = $db->prepare(
            "SELECT COUNT(*) FROM devoluciones
             WHERE pedido_id = :pedido_id
               AND producto_id = :producto_id
               AND estado = 'pendiente'"
        );
        $stmtPendiente->execute([
            ':pedido_id' => $pedidoId,
            ':producto_id' => $productoId,
        ]);

        if ((int)$stmtPendiente->fetchColumn() > 0) {
            $_SESSION['flash_error'] = 'Ya existe una solicitud pendiente para este producto en el pedido.';
            return $response->withHeader('Location', $basePath . '/mi-cuenta/pedidos')->withStatus(302);
        }

        $this->devolucionModel->crear([
            ':tipo_origen' => 'pedido',
            ':pedido_id' => $pedidoId,
            ':venta_id' => null,
            ':producto_id' => $productoId,
            ':cantidad' => $cantidad,
            ':motivo' => $motivo,
            ':observacion' => $observacion !== '' ? $observacion : null,
        ]);

        $_SESSION['flash_msg'] = 'Solicitud de devolución registrada. Será revisada por gerencia.';
        return $response->withHeader('Location', $basePath . '/mi-cuenta/pedidos')->withStatus(302);
    }

    private function diasHabilesTranscurridos(\DateTime $inicio, \DateTime $fin): int
    {
        if ($fin < $inicio) {
            return 0;
        }

        $inicioDia = (clone $inicio)->setTime(0, 0, 0);
        $finDia = (clone $fin)->setTime(0, 0, 0);
        $dias = 0;

        while ($inicioDia <= $finDia) {
            $diaSemana = (int)$inicioDia->format('N'); // 1..7 (Lun..Dom)
            if ($diaSemana <= 5) {
                $dias++;
            }
            $inicioDia->modify('+1 day');
        }

        return max(0, $dias - 1);
    }



    /** GET /mi-cuenta/direcciones */
    public function misDirectiones(Request $request, Response $response): Response
    {
        $cliente = $this->clienteModel->obtenerPorUsuarioId((int)$_SESSION['usuario_id']);
        if (!$cliente) {
            return $response->withHeader('Location', $_ENV['APP_BASEPATH'] . '/login')->withStatus(302);
        }
        
        $direcciones = $this->clienteModel->listarDirecciones((int)$cliente['cliente_id']);
        
        ob_start();
        $titulo = 'Mis Direcciones';
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $error = $request->getQueryParams()['error'] ?? null;
        $success = $request->getQueryParams()['success'] ?? null;
        include __DIR__ . '/../../views/clientes/direcciones.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    /** POST /mi-cuenta/direcciones/crear */
    public function crearDireccion(Request $request, Response $response): Response
    {
        $body = (array) $request->getParsedBody();
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $cliente = $this->clienteModel->obtenerPorUsuarioId((int)$_SESSION['usuario_id']);
        
        if (!$cliente) {
            return $response->withHeader('Location', $basePath . '/login')->withStatus(302);
        }

        if (empty($body['direccion']) || empty($body['ciudad'])) {
            return $response->withHeader('Location', $basePath . '/mi-cuenta/direcciones?error=La dirección y ciudad son obligatorias')->withStatus(302);
        }

        if (isset($body['predeterminada'])) {
            $this->clienteModel->desmarcarPredeterminadas((int)$cliente['cliente_id']);
        }

        $this->clienteModel->crearDireccion([
            ':cliente_id' => $cliente['cliente_id'],
            ':alias'      => $body['alias'] ?: 'Casa',
            ':direccion'  => $body['direccion'],
            ':barrio'     => $body['barrio'] ?? '',
            ':ciudad'     => $body['ciudad'],
            ':referencia' => $body['referencia'] ?? '',
            ':predeterminada' => isset($body['predeterminada']) ? 1 : 0
        ]);

        return $response->withHeader('Location', $basePath . '/mi-cuenta/direcciones?success=' . urlencode('Dirección creada'))->withStatus(302);
    }

    /** POST /mi-cuenta/direcciones/{id}/eliminar */
    public function eliminarDireccion(Request $request, Response $response, array $args): Response
    {
        $dirId = (int) $args['id'];
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $cliente = $this->clienteModel->obtenerPorUsuarioId((int)$_SESSION['usuario_id']);
        
        if ($cliente) {
            try {
                $this->clienteModel->eliminarDireccion($dirId, (int)$cliente['cliente_id']);
                return $response->withHeader('Location', $basePath . '/mi-cuenta/direcciones?success=' . urlencode('Dirección eliminada'))->withStatus(302);
            } catch (\PDOException $e) {
                if ($e->getCode() == '23000') {
                    return $response->withHeader('Location', $basePath . '/mi-cuenta/direcciones?error=' . urlencode('No se puede eliminar la dirección porque tiene pedidos asociados.'))->withStatus(302);
                }
                return $response->withHeader('Location', $basePath . '/mi-cuenta/direcciones?error=' . urlencode('Error al eliminar la dirección.'))->withStatus(302);
            }
        }
        
        return $response->withHeader('Location', $basePath . '/mi-cuenta/direcciones')->withStatus(302);
    }

    /** GET /mi-cuenta/direcciones/{id}/editar */
    public function editarDireccion(Request $request, Response $response, array $args): Response
    {
        $dirId = (int) $args['id'];
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $cliente = $this->clienteModel->obtenerPorUsuarioId((int)$_SESSION['usuario_id']);
        
        if (!$cliente) {
            return $response->withHeader('Location', $basePath . '/login')->withStatus(302);
        }

        $direccionEdit = $this->clienteModel->obtenerDireccion($dirId, (int)$cliente['cliente_id']);
        if (!$direccionEdit) {
            return $response->withHeader('Location', $basePath . '/mi-cuenta/direcciones?error=' . urlencode('Dirección no encontrada'))->withStatus(302);
        }

        $direcciones = $this->clienteModel->listarDirecciones((int)$cliente['cliente_id']);
        
        ob_start();
        $titulo = 'Editar Dirección';
        $error = $request->getQueryParams()['error'] ?? null;
        $success = $request->getQueryParams()['success'] ?? null;
        include __DIR__ . '/../../views/clientes/direcciones.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    /** POST /mi-cuenta/direcciones/{id}/actualizar */
    public function actualizarDireccion(Request $request, Response $response, array $args): Response
    {
        $dirId = (int) $args['id'];
        $body = (array) $request->getParsedBody();
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $cliente = $this->clienteModel->obtenerPorUsuarioId((int)$_SESSION['usuario_id']);
        
        if (!$cliente) {
            return $response->withHeader('Location', $basePath . '/login')->withStatus(302);
        }

        if (empty($body['direccion']) || empty($body['ciudad'])) {
            return $response->withHeader('Location', $basePath . '/mi-cuenta/direcciones/' . $dirId . '/editar?error=' . urlencode('La dirección y ciudad son obligatorias'))->withStatus(302);
        }

        if (isset($body['predeterminada'])) {
            $this->clienteModel->desmarcarPredeterminadas((int)$cliente['cliente_id']);
        }

        $this->clienteModel->actualizarDireccion($dirId, (int)$cliente['cliente_id'], [
            ':alias'      => $body['alias'] ?: 'Casa',
            ':direccion'  => $body['direccion'],
            ':barrio'     => $body['barrio'] ?? '',
            ':ciudad'     => $body['ciudad'],
            ':referencia' => $body['referencia'] ?? '',
            ':predeterminada' => isset($body['predeterminada']) ? 1 : 0
        ]);

        return $response->withHeader('Location', $basePath . '/mi-cuenta/direcciones?success=' . urlencode('Dirección actualizada'))->withStatus(302);
    }
}
