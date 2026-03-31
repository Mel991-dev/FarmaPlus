<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Models\ClienteModel;
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
    private UsuarioModel $usuarioModel;
    private AuthService  $authService;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $this->clienteModel = new ClienteModel($db);
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
        // TODO: Implementar en Semana 4
        return $response;
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

        $this->clienteModel->crearDireccion([
            ':cliente_id' => $cliente['cliente_id'],
            ':alias'      => $body['alias'] ?: 'Casa',
            ':direccion'  => $body['direccion'],
            ':barrio'     => $body['barrio'] ?? '',
            ':ciudad'     => $body['ciudad'],
            ':referencia' => $body['referencia'] ?? '',
            ':predeterminada' => isset($body['predeterminada']) ? 1 : 0
        ]);

        return $response->withHeader('Location', $basePath . '/mi-cuenta/direcciones?success=Dirección creada')->withStatus(302);
    }

    /** POST /mi-cuenta/direcciones/{id}/eliminar */
    public function eliminarDireccion(Request $request, Response $response, array $args): Response
    {
        $dirId = (int) $args['id'];
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $cliente = $this->clienteModel->obtenerPorUsuarioId((int)$_SESSION['usuario_id']);
        
        if ($cliente) {
            $this->clienteModel->eliminarDireccion($dirId, (int)$cliente['cliente_id']);
        }
        
        return $response->withHeader('Location', $basePath . '/mi-cuenta/direcciones?success=Dirección eliminada')->withStatus(302);
    }
}
