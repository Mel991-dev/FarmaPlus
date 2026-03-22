<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

/**
 * ProductoModel — Consultas PDO sobre la tabla `productos`.
 *
 * El stock_actual NO se almacena en la tabla productos.
 * Se calcula dinámicamente sumando cantidad_actual de los lotes activos.
 */
class ProductoModel
{
    public function __construct(private PDO $db) {}

    /** Listar productos con stock calculado desde los lotes */
    public function listarConStock(): array
    {
        $sql = "SELECT p.*,
                       c.nombre AS categoria_nombre,
                       pr.nombre AS proveedor_nombre,
                       COALESCE(SUM(l.cantidad_actual), 0) AS stock_actual
                FROM productos p
                LEFT JOIN categorias_producto c ON p.categoria_id = c.categoria_id
                LEFT JOIN proveedores pr ON p.proveedor_id = pr.proveedor_id
                LEFT JOIN lotes l ON p.producto_id = l.producto_id AND l.activo = 1
                GROUP BY p.producto_id
                ORDER BY p.nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): array|false
    {
        $sql  = "SELECT p.*,
                        c.nombre AS categoria_nombre,
                        pr.nombre AS proveedor_nombre,
                        COALESCE(SUM(l.cantidad_actual), 0) AS stock_actual
                 FROM productos p
                 LEFT JOIN categorias_producto c ON p.categoria_id = c.categoria_id
                 LEFT JOIN proveedores pr ON p.proveedor_id = pr.proveedor_id
                 LEFT JOIN lotes l ON p.producto_id = l.producto_id AND l.activo = 1
                 WHERE p.producto_id = :id
                 GROUP BY p.producto_id
                 LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /** Buscar productos para autocompletado del POS */
    public function buscarParaPos(string $termino): array
    {
        $sql  = "SELECT p.producto_id, p.nombre, p.precio_venta, p.control_especial,
                        COALESCE(SUM(l.cantidad_actual), 0) AS stock_actual
                 FROM productos p
                 LEFT JOIN lotes l ON p.producto_id = l.producto_id AND l.activo = 1
                 WHERE p.nombre LIKE :termino OR p.codigo_invima LIKE :termino
                 GROUP BY p.producto_id
                 HAVING stock_actual > 0
                 ORDER BY p.nombre ASC
                 LIMIT 20";
        $like = '%' . $termino . '%';
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':termino' => $like]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Listar productos para la tienda (sin control especial) */
    public function listarParaTienda(): array
    {
        $sql  = "SELECT p.*,
                        c.nombre AS categoria_nombre,
                        COALESCE(SUM(l.cantidad_actual), 0) AS stock_actual
                 FROM productos p
                 LEFT JOIN categorias_producto c ON p.categoria_id = c.categoria_id
                 LEFT JOIN lotes l ON p.producto_id = l.producto_id AND l.activo = 1
                 WHERE p.control_especial = 0 AND p.activo = 1
                 GROUP BY p.producto_id
                 ORDER BY p.nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function crear(array $datos): string
    {
        // Usar parámetros posicionales para evitar problemas con named params duplicados (HY093)
        $sql  = "INSERT INTO productos
                    (nombre, principio_activo, concentracion, forma_farmaceutica,
                     codigo_invima, categoria_id, proveedor_id, control_especial,
                     precio_compra, precio_venta, stock_minimo)
                 VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $datos[':nombre'],
            $datos[':principio_activo'],
            $datos[':concentracion'],
            $datos[':forma_farmaceutica'],
            $datos[':codigo_invima'],
            $datos[':categoria_id'],
            $datos[':proveedor_id'],
            $datos[':control_especial'],
            $datos[':precio_compra'],
            $datos[':precio_venta'],
            $datos[':stock_minimo'],
        ]);
        return $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): int
    {
        $datos[':id'] = $id;
        $sql  = "UPDATE productos SET nombre=:nombre, principio_activo=:principio_activo,
                 concentracion=:concentracion, forma_farmaceutica=:forma_farmaceutica,
                 codigo_invima=:codigo_invima, categoria_id=:categoria_id,
                 proveedor_id=:proveedor_id, control_especial=:control_especial,
                 precio_compra=:precio_compra, precio_venta=:precio_venta, stock_minimo=:stock_minimo
                 WHERE producto_id=:id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($datos);
        return $stmt->rowCount();
    }
}
