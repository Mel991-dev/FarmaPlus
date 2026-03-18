<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Database\Database;
use App\Models\LoteModel;
use App\Models\ProveedorModel;
use App\Models\AlertaModel;
use App\Services\AlertaService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

/**
 * InventarioController — Lotes FEFO, alertas y proveedores.
 *
 * Módulo 5: Control de Lotes FEFO (RF-4.2, RF-4.4, RF-4.5)
 * Módulo 6: Alertas de Inventario (RN-04)
 * Módulo 4 parte: Proveedores (RF-4.6)
 */
class InventarioController
{
    private LoteModel $loteModel;
    private ProveedorModel $proveedorModel;
    private AlertaModel $alertaModel;
    private AlertaService $alertaService;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $this->loteModel      = new LoteModel($db);
        $this->proveedorModel = new ProveedorModel($db);
        $this->alertaModel    = new AlertaModel($db);
        $this->alertaService  = new AlertaService($db);
    }

    /** GET /inventario/lotes */
    public function listarLotes(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 2
        return $response;
    }

    /** GET /inventario/lotes/registrar */
    public function mostrarRegistroLote(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 2
        return $response;
    }

    /** POST /inventario/lotes/registrar */
    public function registrarLote(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 2
        // 1. Validar fecha_vencimiento no pasada (HU-AUX-02)
        // 2. Insertar en lotes
        // 3. Llamar AlertaService.verificarVencimiento() si < 30 días (RN-04)
        return $response;
    }

    /** GET /inventario/alertas */
    public function alertas(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 2
        return $response;
    }

    /** POST /inventario/alertas/{id}/resolver */
    public function resolverAlerta(Request $request, Response $response, array $args): Response
    {
        // TODO: Implementar en Semana 2
        // alerta.estado = 'resuelta' (HU-AUX-04)
        return $response;
    }

    /** GET /inventario/proveedores */
    public function listarProveedores(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 2
        return $response;
    }

    /** GET /inventario/proveedores/crear */
    public function mostrarCrearProveedor(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 2
        return $response;
    }

    /** POST /inventario/proveedores/crear */
    public function crearProveedor(Request $request, Response $response): Response
    {
        // TODO: Implementar en Semana 2
        // NIT único (HU-AUX-03)
        return $response;
    }

    /** GET /inventario/proveedores/{id}/editar */
    public function mostrarEditarProveedor(Request $request, Response $response, array $args): Response
    {
        // TODO: Implementar en Semana 2
        return $response;
    }

    /** POST /inventario/proveedores/{id}/editar */
    public function actualizarProveedor(Request $request, Response $response, array $args): Response
    {
        // TODO: Implementar en Semana 2
        return $response;
    }
}
