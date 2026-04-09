<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

/**
 * ReporteModel — Queries para reportes gerenciales.
 * RF-8.1 a RF-8.4: ventas por período, productos más vendidos,
 * rendimiento por vendedor e inventario exportable.
 */
class ReporteModel
{
    public function __construct(private PDO $db) {}

    // =========================================================
    // VENTAS
    // =========================================================

    /**
     * Ventas presenciales agrupadas por día dentro de un rango.
     */
    public function ventasPorDia(string $desde, string $hasta): array
    {
        $sql = "SELECT DATE(created_at)            AS fecha,
                       COUNT(*)                    AS num_ventas,
                       COALESCE(SUM(total), 0)     AS total_dia
                FROM   ventas_presenciales
                WHERE  DATE(created_at) BETWEEN :desde AND :hasta
                GROUP  BY DATE(created_at)
                ORDER  BY fecha ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * KPIs totales para el período: total vendido, número de ventas, ticket promedio.
     */
    public function kpisVentasPeriodo(string $desde, string $hasta): array
    {
        $sql = "SELECT COUNT(*)                AS num_ventas,
                       COALESCE(SUM(total), 0) AS total_ventas,
                       COALESCE(AVG(total), 0) AS ticket_promedio,
                       COALESCE(MAX(total), 0) AS venta_maxima
                FROM   ventas_presenciales
                WHERE  DATE(created_at) BETWEEN :desde AND :hasta";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Ventas agrupadas por método de pago en el período.
     */
    public function ventasPorMetodoPago(string $desde, string $hasta): array
    {
        $sql = "SELECT metodo_pago,
                       COUNT(*)                AS num_ventas,
                       COALESCE(SUM(total), 0) AS total
                FROM   ventas_presenciales
                WHERE  DATE(created_at) BETWEEN :desde AND :hasta
                GROUP  BY metodo_pago
                ORDER  BY total DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Pedidos online por estado en el período.
     */
    public function pedidosPorEstado(string $desde, string $hasta): array
    {
        $sql = "SELECT estado,
                       COUNT(*)                AS num_pedidos,
                       COALESCE(SUM(total), 0) AS total
                FROM   pedidos
                WHERE  DATE(created_at) BETWEEN :desde AND :hasta
                GROUP  BY estado
                ORDER  BY num_pedidos DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // PRODUCTOS
    // =========================================================

    /**
     * Top N productos más vendidos (ventas presenciales) en el período.
     */
    public function productosMasVendidos(string $desde, string $hasta, int $limite = 10): array
    {
        $sql = "SELECT p.nombre,
                       p.producto_id,
                       SUM(dv.cantidad)        AS unidades_vendidas,
                       SUM(dv.subtotal)        AS ingreso_total,
                       c.nombre                AS categoria
                FROM   detalle_venta           dv
                INNER  JOIN ventas_presenciales vp ON vp.venta_id    = dv.venta_id
                INNER  JOIN productos           p  ON p.producto_id  = dv.producto_id
                LEFT   JOIN categorias_producto c  ON c.categoria_id = p.categoria_id
                WHERE  DATE(vp.created_at) BETWEEN :desde AND :hasta
                GROUP  BY dv.producto_id
                ORDER  BY unidades_vendidas DESC
                LIMIT  :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':desde',  $desde,  PDO::PARAM_STR);
        $stmt->bindValue(':hasta',  $hasta,  PDO::PARAM_STR);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Top N productos más pedidos (pedidos online) en el período.
     */
    public function productosMasPedidos(string $desde, string $hasta, int $limite = 10): array
    {
        $sql = "SELECT p.nombre,
                       p.producto_id,
                       SUM(dp.cantidad)        AS unidades_pedidas,
                       SUM(dp.subtotal)        AS ingreso_total
                FROM   detalle_pedido dp
                INNER  JOIN pedidos   pe ON pe.pedido_id   = dp.pedido_id
                INNER  JOIN productos p  ON p.producto_id  = dp.producto_id
                WHERE  pe.estado NOT IN ('cancelado','devuelto')
                  AND  DATE(pe.created_at) BETWEEN :desde AND :hasta
                GROUP  BY dp.producto_id
                ORDER  BY unidades_pedidas DESC
                LIMIT  :limite";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':desde',  $desde,  PDO::PARAM_STR);
        $stmt->bindValue(':hasta',  $hasta,  PDO::PARAM_STR);
        $stmt->bindValue(':limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // RENDIMIENTO POR VENDEDOR
    // =========================================================

    /**
     * Rendimiento de cada vendedor en el período.
     */
    public function rendimientoPorVendedor(string $desde, string $hasta): array
    {
        $sql = "SELECT CONCAT(u.nombres, ' ', u.apellidos) AS vendedor,
                       u.usuario_id,
                       COUNT(v.venta_id)        AS num_ventas,
                       COALESCE(SUM(v.total), 0) AS total_vendido,
                       COALESCE(AVG(v.total), 0) AS ticket_promedio
                FROM   ventas_presenciales v
                INNER  JOIN usuarios u ON u.usuario_id = v.vendedor_id
                WHERE  DATE(v.created_at) BETWEEN :desde AND :hasta
                GROUP  BY v.vendedor_id
                ORDER  BY total_vendido DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':desde' => $desde, ':hasta' => $hasta]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // INVENTARIO
    // =========================================================

    /**
     * Productos con stock actual por debajo de su stock_minimo.
     */
    public function productosStockBajo(): array
    {
        $sql = "SELECT p.producto_id,
                       p.nombre,
                       p.stock_minimo,
                       COALESCE(SUM(l.cantidad_actual), 0) AS stock_actual,
                       c.nombre                             AS categoria
                FROM   productos           p
                LEFT   JOIN lotes          l ON l.producto_id = p.producto_id AND l.activo = 1
                LEFT   JOIN categorias_producto c ON c.categoria_id = p.categoria_id
                WHERE  p.activo = 1
                GROUP  BY p.producto_id
                HAVING stock_actual < p.stock_minimo
                ORDER  BY stock_actual ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lotes próximos a vencer en los próximos N días.
     */
    public function lotesProximosVencer(int $dias = 30): array
    {
        $sql = "SELECT l.lote_id,
                       l.numero_lote,
                       l.cantidad_actual,
                       l.fecha_vencimiento,
                       DATEDIFF(l.fecha_vencimiento, CURDATE()) AS dias_restantes,
                       p.nombre AS producto_nombre
                FROM   lotes    l
                INNER  JOIN productos p ON p.producto_id = l.producto_id
                WHERE  l.activo = 1
                  AND  l.cantidad_actual > 0
                  AND  l.fecha_vencimiento BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL :dias DAY)
                ORDER  BY l.fecha_vencimiento ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->bindValue(':dias', $dias, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Resumen general del inventario por categoría.
     */
    public function resumenInventarioPorCategoria(): array
    {
        $sql = "SELECT c.nombre                             AS categoria,
                       COUNT(DISTINCT p.producto_id)        AS num_productos,
                       COALESCE(SUM(l.cantidad_actual), 0)  AS stock_total,
                       COALESCE(SUM(l.cantidad_actual * p.precio_venta), 0) AS valor_inventario
                FROM   categorias_producto c
                LEFT   JOIN productos p ON p.categoria_id = c.categoria_id AND p.activo = 1
                LEFT   JOIN lotes     l ON l.producto_id  = p.producto_id  AND l.activo = 1
                GROUP  BY c.categoria_id
                ORDER  BY valor_inventario DESC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // =========================================================
    // DASHBOARD GERENTE — KPIs rápidos
    // =========================================================

    /**
     * Comparativa de ventas: mes actual vs mes anterior.
     */
    public function comparativaVentasMensual(): array
    {
        $sql = "SELECT
                    COALESCE(SUM(CASE WHEN MONTH(created_at) = MONTH(CURDATE()) AND YEAR(created_at) = YEAR(CURDATE()) THEN total ELSE 0 END), 0) AS mes_actual,
                    COALESCE(SUM(CASE WHEN MONTH(created_at) = MONTH(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) AND YEAR(created_at) = YEAR(DATE_SUB(CURDATE(), INTERVAL 1 MONTH)) THEN total ELSE 0 END), 0) AS mes_anterior
                FROM ventas_presenciales";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [];
    }

    /**
     * Ventas de los últimos 7 días (para mini-gráfica).
     */
    public function ventasUltimos7Dias(): array
    {
        $sql = "SELECT DATE(created_at) AS fecha, COALESCE(SUM(total), 0) AS total
                FROM   ventas_presenciales
                WHERE  created_at >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
                GROUP  BY DATE(created_at)
                ORDER  BY fecha ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
