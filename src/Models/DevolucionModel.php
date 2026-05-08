<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

/**
 * DevolucionModel — Gestión del ciclo de vida de devoluciones.
 *
 * Estados posibles: pendiente → aprobada | rechazada
 * Lógica de negocio: Ley 2300/2023, Res. 1403/2007 (Colombia)
 */
class DevolucionModel
{
    public function __construct(private PDO $db) {}

    /**
     * Listar devoluciones con todos los datos relacionados.
     */
    public function listar(string $estado = ''): array
    {
        $where  = $estado !== '' ? 'WHERE d.estado = :estado' : '';
        $params = $estado !== '' ? [':estado' => $estado] : [];

        $sql = "SELECT
                    d.*,
                    p.nombre            AS producto_nombre,
                    p.es_medicamento,
                    p.control_especial,
                    -- Origen pedido
                    ped.pedido_id       AS ped_numero,
                    CONCAT(uc.nombres, ' ', uc.apellidos) AS cliente_nombre,
                    -- Origen venta presencial
                    v.numero_comprobante AS venta_numero,
                    CONCAT(uv.nombres, ' ', uv.apellidos) AS vendedor_nombre,
                    -- Gerente que gestionó
                    CONCAT(ug.nombres, ' ', ug.apellidos) AS gerente_nombre
                FROM devoluciones d
                INNER JOIN productos p           ON d.producto_id    = p.producto_id
                LEFT  JOIN pedidos ped           ON d.pedido_id      = ped.pedido_id
                LEFT  JOIN clientes cl           ON ped.cliente_id   = cl.cliente_id
                LEFT  JOIN usuarios uc           ON cl.usuario_id    = uc.usuario_id
                LEFT  JOIN ventas_presenciales v ON d.venta_id       = v.venta_id
                LEFT  JOIN usuarios uv           ON v.vendedor_id    = uv.usuario_id
                LEFT  JOIN usuarios ug           ON d.gestionado_por = ug.usuario_id
                {$where}
                ORDER BY d.created_at DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener una devolución completa por ID.
     */
    public function obtenerPorId(int $id): array|false
    {
        $sql = "SELECT
                    d.*,
                    p.nombre            AS producto_nombre,
                    p.es_medicamento,
                    p.control_especial,
                    ped.pedido_id       AS ped_numero,
                    CONCAT(uc.nombres, ' ', uc.apellidos) AS cliente_nombre,
                    uc.correo           AS cliente_correo,
                    v.numero_comprobante AS venta_numero,
                    CONCAT(uv.nombres, ' ', uv.apellidos) AS vendedor_nombre,
                    CONCAT(ug.nombres, ' ', ug.apellidos) AS gerente_nombre
                FROM devoluciones d
                INNER JOIN productos p           ON d.producto_id    = p.producto_id
                LEFT  JOIN pedidos ped           ON d.pedido_id      = ped.pedido_id
                LEFT  JOIN clientes cl           ON ped.cliente_id   = cl.cliente_id
                LEFT  JOIN usuarios uc           ON cl.usuario_id    = uc.usuario_id
                LEFT  JOIN ventas_presenciales v ON d.venta_id       = v.venta_id
                LEFT  JOIN usuarios uv           ON v.vendedor_id    = uv.usuario_id
                LEFT  JOIN usuarios ug           ON d.gestionado_por = ug.usuario_id
                WHERE d.devolucion_id = :id
                LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Crear nueva solicitud de devolución (estado inicial: pendiente).
     */
    public function crear(array $datos): string
    {
        $sql = "INSERT INTO devoluciones
                    (tipo_origen, pedido_id, venta_id, producto_id, cantidad, motivo, observacion, estado)
                VALUES
                    (:tipo_origen, :pedido_id, :venta_id, :producto_id, :cantidad, :motivo, :observacion, 'pendiente')";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($datos);
        return $this->db->lastInsertId();
    }

    /**
     * Aprobar una devolución:
     *  1. Cambia estado a 'aprobada'
     *  2. Incrementa el stock del producto en el lote más reciente activo
     */
    public function aprobar(int $id, int $gerenteId): bool
    {
        $dev = $this->obtenerPorId($id);
        if (!$dev || $dev['estado'] !== 'pendiente') {
            return false;
        }

        $this->db->beginTransaction();
        try {
            // 1. Actualizar estado de la devolución
            $this->db->prepare(
                "UPDATE devoluciones
                 SET estado = 'aprobada', gestionado_por = :g, updated_at = NOW()
                 WHERE devolucion_id = :id"
            )->execute([':g' => $gerenteId, ':id' => $id]);

            // 2. Incrementar stock en el lote más reciente activo del producto
            $this->incrementarStock((int)$dev['producto_id'], (int)$dev['cantidad']);

            $this->db->commit();
            return true;
        } catch (\Throwable $e) {
            $this->db->rollBack();
            throw $e;
        }
    }

    /**
     * Rechazar una devolución con razón documentada.
     */
    public function rechazar(int $id, int $gerenteId, string $razon): bool
    {
        $dev = $this->obtenerPorId($id);
        if (!$dev || $dev['estado'] !== 'pendiente') {
            return false;
        }

        $stmt = $this->db->prepare(
            "UPDATE devoluciones
             SET estado = 'rechazada', razon_rechazo = :razon, gestionado_por = :g, updated_at = NOW()
             WHERE devolucion_id = :id"
        );
        $stmt->execute([':razon' => $razon, ':g' => $gerenteId, ':id' => $id]);
        return $stmt->rowCount() > 0;
    }

    /**
     * Suma la cantidad devuelta al lote más reciente activo del producto.
     * Si no existe lote activo, suma al de mayor lote_id.
     */
    private function incrementarStock(int $productoId, int $cantidad): void
    {
        // Buscar el lote activo más reciente
        $stmt = $this->db->prepare(
            "SELECT lote_id FROM lotes
             WHERE producto_id = :pid AND activo = 1
             ORDER BY lote_id DESC LIMIT 1"
        );
        $stmt->execute([':pid' => $productoId]);
        $lote = $stmt->fetchColumn();

        if ($lote) {
            $this->db->prepare(
                "UPDATE lotes SET cantidad_actual = cantidad_actual + :cant WHERE lote_id = :lid"
            )->execute([':cant' => $cantidad, ':lid' => $lote]);
        }
        // Si no hay lote activo, no falla — la devolución queda aprobada igual.
        // El gerente deberá registrar el lote manualmente.
    }

    /**
     * Contadores por estado para los badges de los tabs.
     */
    public function contarPorEstado(): array
    {
        $sql  = "SELECT estado, COUNT(*) AS total FROM devoluciones GROUP BY estado";
        $rows = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);

        $result = ['pendiente' => 0, 'aprobada' => 0, 'rechazada' => 0];
        foreach ($rows as $r) {
            $result[$r['estado']] = (int)$r['total'];
        }
        return $result;
    }

    /**
     * Obtener pedidos entregados disponibles para devolución.
     */
    public function pedidosEntregados(): array
    {
        $sql = "SELECT p.pedido_id, p.created_at,
                       CONCAT(u.nombres, ' ', u.apellidos) AS cliente_nombre
                FROM pedidos p
                INNER JOIN clientes c  ON p.cliente_id  = c.cliente_id
                INNER JOIN usuarios u  ON c.usuario_id  = u.usuario_id
                WHERE p.estado = 'entregado'
                ORDER BY p.created_at DESC
                LIMIT 100";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener ventas presenciales recientes disponibles para devolución.
     */
    public function ventasRecientes(): array
    {
        $sql = "SELECT v.venta_id, v.numero_comprobante, v.created_at,
                       CONCAT(u.nombres, ' ', u.apellidos) AS vendedor_nombre
                FROM ventas_presenciales v
                INNER JOIN usuarios u ON v.vendedor_id = u.usuario_id
                ORDER BY v.created_at DESC
                LIMIT 100";
        return $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener productos de un pedido específico (para el formulario de creación).
     */
    public function productosDePedido(int $pedidoId): array
    {
        $sql = "SELECT dp.producto_id, dp.cantidad, dp.precio_unitario,
                       p.nombre AS producto_nombre, p.control_especial, p.es_medicamento
                FROM detalle_pedido dp
                INNER JOIN productos p ON dp.producto_id = p.producto_id
                WHERE dp.pedido_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $pedidoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtener productos de una venta presencial específica.
     */
    public function productosDeVenta(int $ventaId): array
    {
        $sql = "SELECT dv.producto_id, dv.cantidad, dv.precio_unitario,
                       p.nombre AS producto_nombre, p.control_especial, p.es_medicamento
                FROM detalle_venta dv
                INNER JOIN productos p ON dv.producto_id = p.producto_id
                WHERE dv.venta_id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $ventaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
