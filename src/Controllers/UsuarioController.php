<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Models\UsuarioModel;
use App\Models\LogAuditoriaModel;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * UsuarioController — CRUD de empleados (solo Administrador).
 * Módulo 13: Gestión de Usuarios (RF-3.2, HU-ADMIN-01 a 02)
 */
class UsuarioController
{
    private UsuarioModel      $usuarioModel;
    private LogAuditoriaModel $logModel;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $this->usuarioModel = new UsuarioModel($db);
        $this->logModel     = new LogAuditoriaModel($db);
    }

    // ─────────────────────────────────────────────────────────
    // GET /admin/usuarios — Listar empleados
    // ─────────────────────────────────────────────────────────
    public function listar(Request $request, Response $response): Response
    {
        $usuarios = $this->usuarioModel->listar();
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');

        // Filtrar solo empleados (excluir clientes si los hay)
        $empleados = array_filter($usuarios, fn($u) => $u['rol_nombre'] !== 'cliente');
        $empleados = array_values($empleados);

        $titulo = 'Gestión de Usuarios';
        ob_start();
        include __DIR__ . '/../../views/usuarios/listar.php';
        $contenido = ob_get_clean();

        ob_start();
        include __DIR__ . '/../../views/layouts/base.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    // ─────────────────────────────────────────────────────────
    // GET /admin/usuarios/crear
    // ─────────────────────────────────────────────────────────
    public function mostrarCrear(Request $request, Response $response): Response
    {
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $roles    = $this->obtenerRoles();
        $usuario  = null; // modo creación

        $titulo = 'Crear Usuario';
        ob_start();
        include __DIR__ . '/../../views/usuarios/form.php';
        $contenido = ob_get_clean();

        ob_start();
        include __DIR__ . '/../../views/layouts/base.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    // ─────────────────────────────────────────────────────────
    // POST /admin/usuarios/crear
    // ─────────────────────────────────────────────────────────
    public function crear(Request $request, Response $response): Response
    {
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $body     = (array) $request->getParsedBody();

        $errores = $this->validarFormulario($body, null);
        if (count($errores) > 0) {
            $roles   = $this->obtenerRoles();
            $usuario = null;
            $titulo  = 'Crear Usuario';
            ob_start();
            include __DIR__ . '/../../views/usuarios/form.php';
            $contenido = ob_get_clean();
            ob_start();
            include __DIR__ . '/../../views/layouts/base.php';
            $html = ob_get_clean();
            $response->getBody()->write($html);
            return $response;
        }

        // Verificar duplicados
        if ($this->usuarioModel->buscarPorCorreo($body['correo'])) {
            $_SESSION['flash_error'] = 'Ya existe un usuario con ese correo electrónico.';
            return $response->withHeader('Location', $basePath . '/admin/usuarios/crear')->withStatus(302);
        }

        $rolId = (int) ($body['rol_id'] ?? 0);
        $hash  = password_hash($body['contrasena'], PASSWORD_BCRYPT, ['cost' => 12]);

        $id = $this->usuarioModel->crear([
            ':rol_id'          => $rolId,
            ':tipo_documento'  => $body['tipo_documento']  ?? 'CC',
            ':documento'       => trim($body['documento']),
            ':nombres'         => trim($body['nombres']),
            ':apellidos'       => trim($body['apellidos']),
            ':correo'          => trim(strtolower($body['correo'])),
            ':telefono'        => trim($body['telefono'] ?? ''),
            ':contrasena_hash' => $hash,
        ]);

        $this->logModel->registrar(
            (int) ($_SESSION['usuario_id'] ?? 0),
            'usuario_creado',
            "Creado usuario ID {$id}: {$body['nombres']} {$body['apellidos']}",
            $_SERVER['REMOTE_ADDR'] ?? ''
        );

        $_SESSION['flash_success'] = 'Usuario creado correctamente.';
        return $response->withHeader('Location', $basePath . '/admin/usuarios')->withStatus(302);
    }

    // ─────────────────────────────────────────────────────────
    // GET /admin/usuarios/{id}/editar
    // ─────────────────────────────────────────────────────────
    public function mostrarEditar(Request $request, Response $response, array $args): Response
    {
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $id       = (int) ($args['id'] ?? 0);
        $usuario  = $this->usuarioModel->buscarPorId($id);

        if (!$usuario) {
            return $response->withHeader('Location', $basePath . '/admin/usuarios')->withStatus(302);
        }

        $roles  = $this->obtenerRoles();
        $titulo = 'Editar Usuario';
        ob_start();
        include __DIR__ . '/../../views/usuarios/form.php';
        $contenido = ob_get_clean();

        ob_start();
        include __DIR__ . '/../../views/layouts/base.php';
        $html = ob_get_clean();
        $response->getBody()->write($html);
        return $response;
    }

    // ─────────────────────────────────────────────────────────
    // POST /admin/usuarios/{id}/editar
    // ─────────────────────────────────────────────────────────
    public function actualizar(Request $request, Response $response, array $args): Response
    {
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $id       = (int) ($args['id'] ?? 0);
        $body     = (array) $request->getParsedBody();

        $usuario = $this->usuarioModel->buscarPorId($id);
        if (!$usuario) {
            return $response->withHeader('Location', $basePath . '/admin/usuarios')->withStatus(302);
        }

        // Actualizar datos
        $this->usuarioModel->actualizar($id, [
            ':nombres'   => trim($body['nombres']   ?? ''),
            ':apellidos' => trim($body['apellidos'] ?? ''),
            ':correo'    => trim(strtolower($body['correo'] ?? '')),
            ':telefono'  => trim($body['telefono']  ?? ''),
            ':activo'    => (int) ($body['activo']   ?? 1),
        ]);

        // Cambio de rol (efectivo en próxima sesión — RN-13)
        if (!empty($body['rol_id']) && (int)$body['rol_id'] !== (int)$usuario['rol_id']) {
            $db   = Database::getInstance()->getConnection();
            $stmt = $db->prepare("UPDATE usuarios SET rol_id = :rol WHERE usuario_id = :id");
            $stmt->execute([':rol' => (int)$body['rol_id'], ':id' => $id]);
        }

        // Cambio de contraseña opcional
        if (!empty($body['contrasena']) && strlen($body['contrasena']) >= 6) {
            $this->usuarioModel->cambiarContrasena(
                $id,
                password_hash($body['contrasena'], PASSWORD_BCRYPT, ['cost' => 12])
            );
        }

        $this->logModel->registrar(
            (int) ($_SESSION['usuario_id'] ?? 0),
            'usuario_editado',
            "Editado usuario ID {$id}",
            $_SERVER['REMOTE_ADDR'] ?? ''
        );

        $_SESSION['flash_success'] = 'Usuario actualizado correctamente.';
        return $response->withHeader('Location', $basePath . '/admin/usuarios')->withStatus(302);
    }

    // ─────────────────────────────────────────────────────────
    // POST /admin/usuarios/{id}/suspender
    // ─────────────────────────────────────────────────────────
    public function suspender(Request $request, Response $response, array $args): Response
    {
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $id       = (int) ($args['id'] ?? 0);

        // No puede suspenderse a sí mismo
        if ($id === (int) ($_SESSION['usuario_id'] ?? 0)) {
            $_SESSION['flash_error'] = 'No puedes suspender tu propia cuenta.';
            return $response->withHeader('Location', $basePath . '/admin/usuarios')->withStatus(302);
        }

        $usuario = $this->usuarioModel->buscarPorId($id);
        if (!$usuario) {
            return $response->withHeader('Location', $basePath . '/admin/usuarios')->withStatus(302);
        }

        $nuevoEstado = $usuario['activo'] == 1 ? 0 : 1;
        $db   = Database::getInstance()->getConnection();
        $stmt = $db->prepare("UPDATE usuarios SET activo = :activo WHERE usuario_id = :id");
        $stmt->execute([':activo' => $nuevoEstado, ':id' => $id]);

        $accion = $nuevoEstado ? 'activado' : 'suspendido';
        $this->logModel->registrar(
            (int) ($_SESSION['usuario_id'] ?? 0),
            "usuario_{$accion}",
            "Usuario ID {$id} fue {$accion}",
            $_SERVER['REMOTE_ADDR'] ?? ''
        );

        $_SESSION['flash_success'] = "Usuario {$accion} correctamente.";
        return $response->withHeader('Location', $basePath . '/admin/usuarios')->withStatus(302);
    }

    // ─────────────────────────────────────────────────────────
    // POST /admin/usuarios/{id}/eliminar
    // ─────────────────────────────────────────────────────────
    public function eliminar(Request $request, Response $response, array $args): Response
    {
        $basePath = rtrim($_ENV['APP_BASEPATH'] ?? '', '/');
        $id       = (int) ($args['id'] ?? 0);

        if ($id === (int) ($_SESSION['usuario_id'] ?? 0)) {
            $_SESSION['flash_error'] = 'No puedes eliminar tu propia cuenta.';
            return $response->withHeader('Location', $basePath . '/admin/usuarios')->withStatus(302);
        }

        // Verificar FK: ventas o pedidos asociados → soft delete
        $db       = Database::getInstance()->getConnection();
        $stmtChk  = $db->prepare("SELECT COUNT(*) FROM ventas_presenciales WHERE vendedor_id = :id");
        $stmtChk->execute([':id' => $id]);
        $tieneVentas = (int) $stmtChk->fetchColumn() > 0;

        if ($tieneVentas) {
            // Solo desactivar, no eliminar (integridad referencial)
            $this->usuarioModel->desactivar($id);
            $_SESSION['flash_success'] = 'El usuario fue desactivado (tiene ventas registradas y no puede eliminarse).';
        } else {
            $stmtDel = $db->prepare("DELETE FROM usuarios WHERE usuario_id = :id");
            $stmtDel->execute([':id' => $id]);
            $_SESSION['flash_success'] = 'Usuario eliminado correctamente.';
        }

        $this->logModel->registrar(
            (int)($_SESSION['usuario_id'] ?? 0),
            'usuario_eliminado',
            "Eliminado/desactivado usuario ID {$id}",
            $_SERVER['REMOTE_ADDR'] ?? ''
        );

        return $response->withHeader('Location', $basePath . '/admin/usuarios')->withStatus(302);
    }

    // ─────────────────────────────────────────────────────────
    // Helpers privados
    // ─────────────────────────────────────────────────────────
    private function obtenerRoles(): array
    {
        $db   = Database::getInstance()->getConnection();
        $stmt = $db->query("SELECT rol_id, nombre, descripcion FROM roles WHERE nombre != 'cliente' ORDER BY nombre ASC");
        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    private function validarFormulario(array $body, ?int $id): array
    {
        $errores = [];
        if (empty(trim($body['nombres'] ?? '')))   $errores[] = 'El nombre es obligatorio.';
        if (empty(trim($body['apellidos'] ?? '')))  $errores[] = 'El apellido es obligatorio.';
        if (empty(trim($body['documento'] ?? '')))  $errores[] = 'El documento es obligatorio.';
        if (empty(trim($body['correo'] ?? '')))     $errores[] = 'El correo es obligatorio.';
        if (!filter_var($body['correo'] ?? '', FILTER_VALIDATE_EMAIL)) $errores[] = 'El correo no es válido.';
        if ($id === null && empty($body['contrasena'])) $errores[] = 'La contraseña es obligatoria.';
        if (!empty($body['contrasena']) && strlen($body['contrasena']) < 6) $errores[] = 'La contraseña debe tener al menos 6 caracteres.';
        return $errores;
    }
}
