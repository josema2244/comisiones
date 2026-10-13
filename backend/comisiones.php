<?php
// Calcula la comisión de cada vendedor en un rango de fechas.
// 1) Suma las ventas del vendedor en el rango.
// 2) Busca la regla con el monto mínimo más alto que ese total alcance.
// 3) Aplica el porcentaje de esa regla.
function calcularComisiones($fechaInicio, $fechaFin)
{
    // Se suma un día al final para incluir todas las ventas de la fecha final.
    $limite = date('Y-m-d', strtotime($fechaFin . ' +1 day'));

    $reglas = consultar('SELECT Porcentaje, MontoMinimo FROM Regla ORDER BY MontoMinimo DESC');

    $totales = consultar(
        'SELECT v.Id, v.Nombre, SUM(ve.Monto) AS Total
         FROM Vendedor v
         INNER JOIN Venta ve ON ve.VendedorId = v.Id
         WHERE ve.FechaVenta >= ? AND ve.FechaVenta < ?
         GROUP BY v.Id, v.Nombre
         ORDER BY v.Nombre',
        [$fechaInicio, $limite]
    );

    $resultado = [];

    foreach ($totales as $fila) {
        $total = (float) $fila['Total'];
        $porcentaje = 0;

        foreach ($reglas as $regla) {
            if ($total >= (float) $regla['MontoMinimo']) {
                $porcentaje = (float) $regla['Porcentaje'];
                break;
            }
        }

        $resultado[] = [
            'vendedor'     => $fila['Nombre'],
            'totalVendido' => round($total, 2),
            'porcentaje'   => $porcentaje,
            'comision'     => round($total * $porcentaje / 100, 2),
        ];
    }

    return $resultado;
}