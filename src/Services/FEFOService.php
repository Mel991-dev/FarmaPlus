<?php

declare(strict_types=1);

namespace App\Services;

use PDO;
use App\Models\LoteModel;
use App\Models\AlertaModel;

/**
 * FEFOService — First Expired, First Out.
 *
 * Implementa el método FEFO para descontar stock del lote más próximo a vencer.
 * Referencia: RF-4.4, RN-06.
 *
 * Uso:
 *   $resultado = $fefoService->descontarStock($productoId, $cantidadSolicitada);
 *   // Retorna array con los lotes afectados o lanza excepción si no hay stock suficiente.
 */
class FEFOService
{
    private LoteModel $loteModel;

    public function __construct(private PDO $db)
    {
        $this->loteModel = new LoteModel($db);
    }

    /**
     * Descontar la cantidad solicitada del stock usando método FEFO.
     *
     * @param int $productoId  ID del producto a descontar
     * @param int $cantidad    Unidades a descontar
     * @return array           Lista de [{lote_id, cantidad_descontada}] para el detalle de venta
     * @throws \RuntimeException Si el stock disponible es insuficiente
     */
    public function descontarStock(int $productoId, int $cantidad): array
    {
        $lotes = $this->loteModel->obtenerLotesFEFO($productoId);

        $totalDisponible = array_sum(array_column($lotes, 'cantidad_actual'));
        if ($totalDisponible < $cantidad) {
            throw new \RuntimeException(
                "Stock insuficiente para producto ID {$productoId}. Disponible: {$totalDisponible}, Solicitado: {$cantidad}."
            );
        }

        $movimientos = [];
        $restante    = $cantidad;

        foreach ($lotes as $lote) {
            if ($restante <= 0) {
                break;
            }

            $aDescontar = min($lote['cantidad_actual'], $restante);
            $this->loteModel->descontarUnidades($lote['lote_id'], $aDescontar);

            $movimientos[] = [
                'lote_id'            => $lote['lote_id'],
                'cantidad_descontada' => $aDescontar,
            ];

            $restante -= $aDescontar;
        }

        return $movimientos;
    }
}
