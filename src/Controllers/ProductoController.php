<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Models\ProductoModel;
use App\Services\AlertaService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * ProductoController — CRUD del catálogo farmacéutico.
 *
 * Módulo 4: Gestión de Productos e Inventario (RF-4.1, RF-4.3)
 * - Registro con INVIMA obligatorio (RN-01)
 * - Tipo de venta: libre / control especial (RN-02)
 * - Búsqueda por nombre, categoría, laboratorio y lote
 */
class ProductoController
{
    private ProductoModel $productoModel;
    private AlertaService $alertaService;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $this->productoModel = new ProductoModel($db);
        $this->alertaService = new AlertaService($db);
    }

    /** GET /inventario/productos */
    public function listar(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 2
        return $response;
    }

    /** GET /inventario/productos/crear */
    public function mostrarCrear(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 2
        return $response;
    }

    /** POST /inventario/productos/crear */
    public function crear(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 2
        // INVIMA es campo obligatorio — sin él no se guarda (RN-01)
        return $response;
    }

    /** GET /inventario/productos/{id} */
    public function detalle(Request $request, Response $response, array $args): Response
    {
        // TODO: Implementar en Semana 2
        return $response;
    }

    /** GET /inventario/productos/{id}/editar */
    public function mostrarEditar(Request $request, Response $response, array $args): Response
    {
        // TODO: Implementar en Semana 2
        return $response;
    }

    /** POST /inventario/productos/{id}/editar */
    public function actualizar(Request $request, Response $response, array $args): Response
    {
        // TODO: Implementar en Semana 2
        return $response;
    }
}
