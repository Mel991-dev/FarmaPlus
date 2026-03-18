<?php

declare(strict_types=1);

namespace App\Services;

use PDO;
use App\Models\AlertaModel;
use App\Models\ProductoModel;
use App\Models\LoteModel;

/**
 * AlertaService — Generación automática de alertas de inventario.
 *
 * Módulo 6: Alertas de Inventario (RF-4.5, RN-04)
 * - Alerta 'stock_minimo': cuando stock_actual <= stock_minimo del producto
 * - Alerta 'vencimiento': cuando fecha_vencimiento del lote es < N días (configurable)
 *
 * Se llama desde InventarioController tras registrar un lote o desde una tarea cron.
 */
class AlertaService
{
    private AlertaModel $alertaModel;
    private ProductoModel $productoModel;
    private LoteModel $loteModel;
    private int $diasAlertaVencimiento = 30;

    public function __construct(private PDO $db)
    {
        $this->alertaModel    = new AlertaModel($db);
        $this->productoModel  = new ProductoModel($db);
        $this->loteModel      = new LoteModel($db);

        // Leer configuración de días de alerta
        try {
            $sql  = "SELECT valor FROM configuracion WHERE clave = 'dias_alerta_vencimiento' LIMIT 1";
            $stmt = $db->prepare($sql);
            $stmt->execute();
            $row  = $stmt->fetch(PDO::FETCH_ASSOC);
            if ($row) {
                $this->diasAlertaVencimiento = (int) $row['valor'];
            }
        } catch (\Throwable) {
            // Usar valor por defecto si la tabla aún no existe
        }
    }

    /**
     * Verificar y generar alertas para un producto específico.
     * Llamar tras cualquier movimiento de inventario.
     */
    public function verificarProducto(int $productoId): void
    {
        $producto = $this->productoModel->obtenerPorId($productoId);
        if (!$producto) {
            return;
        }

        // Alerta stock mínimo
        if ((int) $producto['stock_actual'] <= (int) $producto['stock_minimo']) {
            if (!$this->alertaModel->existeActiva($productoId, null, 'stock_minimo')) {
                $this->alertaModel->crear(
                    $productoId,
                    null,
                    'stock_minimo',
                    "Stock bajo: {$producto['nombre']} tiene {$producto['stock_actual']} unidades (mínimo: {$producto['stock_minimo']})"
                );
            }
        }

        // Alertas de vencimiento
        $lotesPorVencer = $this->loteModel->lotesPorVencer($this->diasAlertaVencimiento);
        foreach ($lotesPorVencer as $lote) {
            if ($lote['producto_id'] === $productoId) {
                if (!$this->alertaModel->existeActiva($productoId, $lote['lote_id'], 'vencimiento')) {
                    $this->alertaModel->crear(
                        $productoId,
                        $lote['lote_id'],
                        'vencimiento',
                        "Lote {$lote['numero_lote']} vence el {$lote['fecha_vencimiento']}"
                    );
                }
            }
        }
    }
}
