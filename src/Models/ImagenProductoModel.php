<?php

declare(strict_types=1);

namespace App\Models;

use PDO;

/**
 * ImagenProductoModel — Gestión de imágenes asociadas a productos.
 *
 * Reglas de negocio (RN-IMG):
 *  - Máximo 4 imágenes por producto (orden 1-4).
 *  - orden=1 es la imagen principal (thumbnail en catálogo y grande en ficha).
 *  - Al eliminar una imagen, el resto se reordena de forma consecutiva.
 *  - Al eliminar la imagen de orden=1, la siguiente pasa a ser orden=1.
 */
class ImagenProductoModel
{
    public function __construct(private PDO $db) {}

    /** Todas las imágenes de un producto ordenadas por orden ASC */
    public function obtenerPorProducto(int $productoId): array
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM producto_imagenes
              WHERE producto_id = :pid
              ORDER BY orden ASC'
        );
        $stmt->execute([':pid' => $productoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /** Solo la imagen principal (orden=1) de un producto */
    public function obtenerPrincipal(int $productoId): array|false
    {
        $stmt = $this->db->prepare(
            'SELECT * FROM producto_imagenes
              WHERE producto_id = :pid AND orden = 1
              LIMIT 1'
        );
        $stmt->execute([':pid' => $productoId]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /** Cuántas imágenes tiene el producto actualmente */
    public function contarPorProducto(int $productoId): int
    {
        $stmt = $this->db->prepare(
            'SELECT COUNT(*) FROM producto_imagenes WHERE producto_id = :pid'
        );
        $stmt->execute([':pid' => $productoId]);
        return (int) $stmt->fetchColumn();
    }

    /**
     * Guardar nueva imagen en la base de datos.
     *
     * @param int    $productoId
     * @param string $nombreArchivo  Nombre del archivo físico (ej: img_2.webp)
     * @param int    $orden          Posición 1-4
     * @return int   imagen_id insertado
     */
    public function guardar(int $productoId, string $nombreArchivo, int $orden): int
    {
        $stmt = $this->db->prepare(
            'INSERT INTO producto_imagenes (producto_id, nombre_archivo, orden)
              VALUES (:pid, :archivo, :orden)'
        );
        $stmt->execute([
            ':pid'     => $productoId,
            ':archivo' => $nombreArchivo,
            ':orden'   => $orden,
        ]);
        return (int) $this->db->lastInsertId();
    }

    /**
     * Eliminar una imagen por su ID.
     *
     * @return string nombre_archivo eliminado (para borrar el archivo físico desde el controlador)
     */
    public function eliminar(int $imagenId): string
    {
        // Obtener nombre_archivo y producto_id antes de borrar
        $stmt = $this->db->prepare(
            'SELECT producto_id, nombre_archivo, orden
              FROM producto_imagenes
              WHERE imagen_id = :id
              LIMIT 1'
        );
        $stmt->execute([':id' => $imagenId]);
        $imagen = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$imagen) {
            return '';
        }

        // Borrar el registro
        $del = $this->db->prepare(
            'DELETE FROM producto_imagenes WHERE imagen_id = :id'
        );
        $del->execute([':id' => $imagenId]);

        // Reordenar el resto de imágenes del mismo producto
        $this->reordenar((int) $imagen['producto_id']);

        return (string) $imagen['nombre_archivo'];
    }

    /**
     * Reordenar imágenes de un producto de forma consecutiva (sin huecos).
     * Se llama automáticamente tras eliminar una imagen.
     * Ejemplo: si quedan órdenes 1,3 → pasan a ser 1,2.
     */
    public function reordenar(int $productoId): void
    {
        $stmt = $this->db->prepare(
            'SELECT imagen_id FROM producto_imagenes
              WHERE producto_id = :pid
              ORDER BY orden ASC'
        );
        $stmt->execute([':pid' => $productoId]);
        $imagenes = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $upd = $this->db->prepare(
            'UPDATE producto_imagenes SET orden = :orden WHERE imagen_id = :id'
        );
        foreach ($imagenes as $i => $img) {
            $upd->execute([
                ':orden' => $i + 1,
                ':id'    => $img['imagen_id'],
            ]);
        }
    }

    /** Devolver el siguiente orden disponible para un producto (siguiente hueco) */
    public function siguienteOrden(int $productoId): int
    {
        return $this->contarPorProducto($productoId) + 1;
    }
}
