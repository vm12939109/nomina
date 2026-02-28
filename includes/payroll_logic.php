<?php
/**
 * Ubicación del archivo: includes/payroll_logic.php
 */

/**
 * Calcular nómina basada en la legislación venezolana
 * @param float $sueldoBase Sueldo mensual base
 * @param int $dias Ciclo de pago (7, 15, 30)
 * @return array Desglose de cálculos
 */
function calcularNomina($sueldoBase, $dias) {
    // Proporción del sueldo base según el ciclo
    $proporcion = $dias / 30;
    $sueldoCiclo = $sueldoBase * $proporcion;

    // Asignaciones (Ejemplo: Bono de alimentación proporcinal - Cesta Ticket)
    // Supongamos un Cesta Ticket mensual fijo (valor referencial)
    $cestaTicketMensual = 1450.00;
    $asignacionCestaTicket = $cestaTicketMensual * $proporcion;

    // Deducciones de ley
    // SSO: 4% (sobre sueldo base)
    // SPF: 0.5% (sobre sueldo base)
    // LPH: 1% (sobre sueldo base + asignaciones salariales)

    $sso = $sueldoCiclo * 0.04;
    $spf = $sueldoCiclo * 0.005;
    $lph = ($sueldoCiclo) * 0.01;

    $totalDeducciones = $sso + $spf + $lph;
    $totalAsignaciones = $asignacionCestaTicket;

    $totalNeto = ($sueldoCiclo + $totalAsignaciones) - $totalDeducciones;

    return [
        'sueldo_ciclo' => $sueldoCiclo,
        'cesta_ticket' => $asignacionCestaTicket,
        'sso' => $sso,
        'spf' => $spf,
        'lph' => $lph,
        'total_asignaciones' => $totalAsignaciones,
        'total_deducciones' => $totalDeducciones,
        'total_neto' => $totalNeto
    ];
}
?>
