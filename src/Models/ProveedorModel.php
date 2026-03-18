<?php declare(strict_types=1);
namespace App\Models;
use PDO;

/** ProveedorModel — Consultas sobre la tabla `proveedores`. */
class ProveedorModel
{
    public function __construct(private PDO $db) {}

    public function listar(): array
    {
        $sql  = "SELECT * FROM proveedores ORDER BY nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function obtenerPorId(int $id): array|false
    {
        $sql  = "SELECT * FROM proveedores WHERE proveedor_id = :id LIMIT 1";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function crear(array $datos): string
    {
        $sql  = "INSERT INTO proveedores (nit, nombre, pais_origen, telefono, correo, sitio_web)
                 VALUES (:nit, :nombre, :pais_origen, :telefono, :correo, :sitio_web)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($datos);
        return $this->db->lastInsertId();
    }

    public function actualizar(int $id, array $datos): int
    {
        $datos[':id'] = $id;
        $sql  = "UPDATE proveedores SET nombre=:nombre, nit=:nit, telefono=:telefono, correo=:correo WHERE proveedor_id=:id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute($datos);
        return $stmt->rowCount();
    }
}
