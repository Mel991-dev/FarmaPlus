<?php declare(strict_types=1);
namespace App\Models;
use PDO;

/** CategoriaModel — Consultas sobre `categorias_producto`. */
class CategoriaModel
{
    public function __construct(private PDO $db) {}

    public function listar(): array
    {
        $sql  = "SELECT * FROM categorias_producto ORDER BY nombre ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
