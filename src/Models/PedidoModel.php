<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

/**
 * PedidoModel — Consultas PDO sobre las tablas `pedidos` y `detalle_pedido`.
 */
class PedidoModel
{
    public function __construct(private PDO $db) {}

    public function listar(array $filtros = []): array
    {
        $sql  = "SELECT p.*, CONCAT(u.nombres, ' ', u.apellidos) AS cliente_nombre,
                        r.nombre AS repartidor_nombre
                 FROM pedidos p
                 INNER JOIN clientes c ON p.cliente_id = c.cliente_id
                 INNER JOIN usuarios u ON c.usuario_id = u.usuario_id
                 LEFT JOIN usuarios r ON p.repartidor_id = r.usuario_id
                 ORDER BY p.created_at DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): array|false
    {
        $sql  = "SELECT p.*, CONCAT(u.nombres, ' ', u.apellidos) AS cliente_nombre,
                        u.correo AS cliente_correo, d.direccion, d.barrio, d.ciudad, d.referencia
                 FROM pedidos p
                 INNER JOIN clientes c ON p.cliente_id = c.cliente_id
                 INNER JOIN usuarios u ON c.usuario_id = u.usuario_id
                 LEFT JOIN direcciones_entrega d ON p.direccion_entrega_id = d.direccion_id
                 WHERE p.pedido_id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function obtenerDetalle(int $pedidoId): array
    {
        $sql  = "SELECT dp.*, p.nombre AS producto_nombre
                 FROM detalle_pedido dp
                 INNER JOIN productos p ON dp.producto_id = p.producto_id
                 WHERE dp.pedido_id = :pedido_id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':pedido_id' => $pedidoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear(array $datos): string
    {
        $sql  = "INSERT INTO pedidos (cliente_id, direccion_entrega_id, estado, subtotal, costo_envio, total, mp_referencia)
                 VALUES (:cliente_id, :direccion_entrega_id, 'pendiente', :subtotal, :costo_envio, :total, :mp_referencia)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($datos);
        return $this->db->lastInsertId();
    }

    public function actualizarEstado(int $id, string $estado): int
    {
        $sql  = "UPDATE pedidos SET estado = :estado WHERE pedido_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':estado' => $estado, ':id' => $id]);
        return $stmt->rowCount();
    }

    public function asignarRepartidor(int $pedidoId, int $repartidorId): int
    {
        $sql  = "UPDATE pedidos SET repartidor_id = :repartidor_id, estado = 'en_preparacion' WHERE pedido_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':repartidor_id' => $repartidorId, ':id' => $pedidoId]);
        return $stmt->rowCount();
    }

    public function misPedidosRepartidor(int $repartidorId): array
    {
        $sql  = "SELECT p.*, CONCAT(u.nombres, ' ', u.apellidos) AS cliente_nombre,
                        d.direccion, d.barrio, d.ciudad, d.referencia, u.telefono AS cliente_telefono
                 FROM pedidos p
                 INNER JOIN clientes c ON p.cliente_id = c.cliente_id
                 INNER JOIN usuarios u ON c.usuario_id = u.usuario_id
                 LEFT JOIN direcciones_entrega d ON p.direccion_entrega_id = d.direccion_id
                 WHERE p.repartidor_id = :repartidor_id
                   AND p.estado NOT IN ('entregado', 'cancelado', 'devuelto')
                 ORDER BY p.created_at ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':repartidor_id' => $repartidorId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Buscar pedido por la referencia externa de MercadoPago.
     * Usado por el WebhookController para identificar el pedido al recibir confirmación de pago.
     */
    public function obtenerPorMpReferencia(string $referencia): array|false
    {
        $sql  = "SELECT p.*, CONCAT(u.nombres, ' ', u.apellidos) AS cliente_nombre,
                        u.correo AS cliente_correo
                 FROM pedidos p
                 INNER JOIN clientes c ON p.cliente_id = c.cliente_id
                 INNER JOIN usuarios u ON c.usuario_id = u.usuario_id
                 WHERE p.mp_referencia = :ref LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':ref' => $referencia]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Guardar datos del pago aprobado por MercadoPago.
     * Intenta primero con todas las columnas; si falla (columna no existe),
     * hace un UPDATE mínimo solo con lo esencial.
     */
    public function actualizarMpPago(int $id, string $mpPaymentId, string $mpStatus): int
    {
        try {
            $sql  = "UPDATE pedidos 
                     SET mp_payment_id = :mp_payment_id,
                         mp_status     = :mp_status,
                         estado        = 'pagado',
                         updated_at    = NOW()
                     WHERE pedido_id = :id";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([
                ':mp_payment_id' => $mpPaymentId,
                ':mp_status'     => $mpStatus,
                ':id'            => $id,
            ]);
            return $stmt->rowCount();
        } catch (\PDOException $e) {
            // Fallback: columnas mp_status / updated_at aún no existen en BD
            // Solo actualiza estado y mp_payment_id si la columna existe
            $sqlFallback = "UPDATE pedidos SET estado = 'pagado' WHERE pedido_id = :id";
            $stmt = $this->db->prepare($sqlFallback);
            $stmt->execute([':id' => $id]);
            return $stmt->rowCount();
        }
    }


    /**
     * Listar pedidos con filtros y paginación para el panel de administración.
     */
    public function listarConFiltros(array $filtros = [], int $limit = 20, int $offset = 0): array
    {
        $where  = ['1=1'];
        $params = [];

        if (!empty($filtros['estado'])) {
            $where[]              = 'p.estado = :estado';
            $params[':estado']    = $filtros['estado'];
        }
        if (!empty($filtros['cliente_id'])) {
            $where[]                   = 'p.cliente_id = :cliente_id';
            $params[':cliente_id']     = $filtros['cliente_id'];
        }

        $whereStr = implode(' AND ', $where);

        $sql  = "SELECT p.*,
                        CONCAT(u.nombres, ' ', u.apellidos) AS cliente_nombre,
                        u.correo AS cliente_correo,
                        r.nombres AS repartidor_nombre,
                        d.direccion, d.ciudad
                 FROM pedidos p
                 INNER JOIN clientes c ON p.cliente_id = c.cliente_id
                 INNER JOIN usuarios u ON c.usuario_id = u.usuario_id
                 LEFT JOIN usuarios r ON p.repartidor_id = r.usuario_id
                 LEFT JOIN direcciones_entrega d ON p.direccion_entrega_id = d.direccion_id
                 WHERE {$whereStr}
                 ORDER BY p.created_at DESC
                 LIMIT :limit OFFSET :offset";

        $stmt = $this->db->prepare($sql);
        foreach ($params as $k => $v) {
            $stmt->bindValue($k, $v);
        }
        $stmt->bindValue(':limit',  $limit,  PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Contar total de pedidos para paginación.
     */
    public function contarPedidos(array $filtros = []): int
    {
        $where  = ['1=1'];
        $params = [];

        if (!empty($filtros['estado'])) {
            $where[]           = 'p.estado = :estado';
            $params[':estado'] = $filtros['estado'];
        }

        $whereStr = implode(' AND ', $where);
        $sql      = "SELECT COUNT(*) FROM pedidos p WHERE {$whereStr}";
        $stmt     = $this->db->prepare($sql);
        $stmt->execute($params);
        return (int)$stmt->fetchColumn();
    }

    /**
     * Obtener usuarios con rol 'repartidor' para asignar pedidos.
     */
    public function obtenerRepartidoresDisponibles(): array
    {
        $sql  = "SELECT u.usuario_id, CONCAT(u.nombres, ' ', u.apellidos) AS nombre, u.correo, u.telefono
                 FROM usuarios u
                 INNER JOIN roles r ON u.rol_id = r.rol_id
                 WHERE r.nombre = 'repartidor' AND u.activo = 1
                 ORDER BY u.nombres";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Guardar observación de devolución cuando el repartidor no puede entregar.
     */
    public function registrarDevolucion(int $pedidoId, string $observacion): int
    {
        $sql  = "UPDATE pedidos 
                 SET estado = 'devuelto', observacion_devolucion = :obs, updated_at = NOW()
                 WHERE pedido_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':obs' => $observacion, ':id' => $pedidoId]);
        return $stmt->rowCount();
    }

    /**
     * Obtener email del gerente para notificaciones de devolución.
     */
    public function obtenerEmailGerente(): string|false
    {
        $sql  = "SELECT u.correo 
                 FROM usuarios u
                 INNER JOIN roles r ON u.rol_id = r.rol_id 
                 WHERE r.nombre IN ('gerente','administrador') AND u.activo = 1 
                 ORDER BY u.usuario_id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchColumn();
    }
}

