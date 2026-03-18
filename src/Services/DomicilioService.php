<?php

declare(strict_types=1);

namespace App\Services;

use PDO;

/**
 * DomicilioService — Cálculo de tarifa de domicilio.
 *
 * Módulo 10: Domicilios (RF-6.3, RN-09, RN-10, RN-11)
 *
 * Fórmula:
 *   Costo = Tarifa base + Recargo por distancia (rangos en km) + Recargo por volumen
 *
 * Todos los valores son configurables en la tabla `configuracion`.
 */
class DomicilioService
{
    private array $config = [];

    public function __construct(private PDO $db)
    {
        $this->cargarConfiguracion();
    }

    private function cargarConfiguracion(): void
    {
        try {
            $sql  = "SELECT clave, valor FROM configuracion WHERE clave LIKE 'tarifa_%' OR clave LIKE 'recargo_%'";
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
            foreach ($rows as $row) {
                $this->config[$row['clave']] = (float) $row['valor'];
            }
        } catch (\Throwable) {
            // Usar valores por defecto
            $this->config = [
                'tarifa_base_envio'      => 3000,
                'recargo_km_1_3'         => 400,
                'recargo_km_3_6'         => 800,
                'recargo_km_6_mas'       => 1200,
                'recargo_volumen_umbral' => 5,
                'recargo_volumen_valor'  => 1500,
            ];
        }
    }

    /**
     * Calcular costo de domicilio según distancia (km) y cantidad de productos.
     *
     * @param float $distanciaKm   Distancia al punto de entrega en kilómetros
     * @param int   $cantidadItems Total de unidades en el pedido
     * @return float               Costo total del domicilio en COP
     */
    public function calcular(float $distanciaKm, int $cantidadItems): float
    {
        $tarifaBase = $this->config['tarifa_base_envio'] ?? 3000;

        // Recargo por distancia
        $recargoDist = match (true) {
            $distanciaKm <= 1  => 0,
            $distanciaKm <= 3  => $distanciaKm * ($this->config['recargo_km_1_3'] ?? 400),
            $distanciaKm <= 6  => $distanciaKm * ($this->config['recargo_km_3_6'] ?? 800),
            default            => $distanciaKm * ($this->config['recargo_km_6_mas'] ?? 1200),
        };

        // Recargo por volumen
        $umbral        = (int) ($this->config['recargo_volumen_umbral'] ?? 5);
        $recargoVol    = $cantidadItems >= $umbral ? ($this->config['recargo_volumen_valor'] ?? 1500) : 0;

        return round($tarifaBase + $recargoDist + $recargoVol, 2);
    }
}
