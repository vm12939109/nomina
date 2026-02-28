<?php
/**
 * Ubicación del archivo: generate_voucher.php
 */
require_once 'includes/db.php';
require_once 'includes/functions.php';
require_once 'includes/payroll_logic.php';
require_once 'lib/fpdf/fpdf.php';

if (!isLoggedIn()) {
    redirect('login.php');
}

$usuario_id = isset($_GET['id']) ? (int)$_GET['id'] : $_SESSION['user_id'];

// Obtener datos del usuario con cargo y depto
$stmt = $pdo->prepare("SELECT u.*, d.nombre as dept_nombre, c.nombre as cargo_nombre
                       FROM usuarios u
                       LEFT JOIN departamentos d ON u.departamento_id = d.id
                       LEFT JOIN cargos c ON u.cargo_id = c.id
                       WHERE u.id = ?");
$stmt->execute([$usuario_id]);
$user = $stmt->fetch();

if (!$user) {
    die("Usuario no encontrado.");
}

// Usamos el sueldo base real del usuario
$sueldoBaseReal = $user['sueldo_base'] > 0 ? $user['sueldo_base'] : 5000.00;
$diasSimulados = 15;
$calculos = calcularNomina($sueldoBaseReal, $diasSimulados);

// Intentar persistir en la base de datos si es una generación real (simplificado)
$stmtCheck = $pdo->prepare("SELECT id FROM nomina WHERE usuario_id = ? AND fecha_pago = ?");
$fecha_hoy = date('Y-m-d');
$stmtCheck->execute([$usuario_id, $fecha_hoy]);

if (!$stmtCheck->fetch()) {
    $insNomina = $pdo->prepare("INSERT INTO nomina (usuario_id, tipo_pago, sueldo_base, asignaciones, deducciones, total_neto, fecha_pago) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $insNomina->execute([
        $usuario_id,
        (string)$diasSimulados,
        $sueldoBaseSimulado,
        $calculos['total_asignaciones'],
        $calculos['total_deducciones'],
        $calculos['total_neto'],
        $fecha_hoy
    ]);
}

// Crear PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Encabezado
$pdf->Cell(190, 10, 'RECIBO DE PAGO - NOMINA VENEZUELA', 0, 1, 'C');
$pdf->Ln(10);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(95, 10, 'Empleado: ' . $user['nombre'] . ' ' . $user['apellido'], 0, 0);
$pdf->Cell(95, 10, 'Cedula: ' . $user['cedula'], 0, 1);
$pdf->Cell(95, 10, 'Departamento: ' . ($user['dept_nombre'] ?? 'N/A'), 0, 0);
$pdf->Cell(95, 10, 'Cargo: ' . ($user['cargo_nombre'] ?? 'N/A'), 0, 1);
$pdf->Cell(95, 10, 'Fecha: ' . date('d/m/Y'), 0, 0);
$pdf->Cell(95, 10, 'Ciclo: ' . $diasSimulados . ' dias', 0, 1);
$pdf->Ln(10);

// Tabla de Conceptos
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(100, 10, 'Concepto', 1);
$pdf->Cell(45, 10, 'Asignaciones', 1);
$pdf->Cell(45, 10, 'Deducciones', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
// Sueldo
$pdf->Cell(100, 10, 'Sueldo Base Proporcional', 1);
$pdf->Cell(45, 10, number_format($calculos['sueldo_ciclo'], 2), 1);
$pdf->Cell(45, 10, '', 1);
$pdf->Ln();

// Cesta Ticket
$pdf->Cell(100, 10, 'Cesta Ticket (Bono Alim.)', 1);
$pdf->Cell(45, 10, number_format($calculos['cesta_ticket'], 2), 1);
$pdf->Cell(45, 10, '', 1);
$pdf->Ln();

// Deducciones SSO
$pdf->Cell(100, 10, 'S.S.O. (4%)', 1);
$pdf->Cell(45, 10, '', 1);
$pdf->Cell(45, 10, number_format($calculos['sso'], 2), 1);
$pdf->Ln();

// SPF
$pdf->Cell(100, 10, 'S.P.F. (0.5%)', 1);
$pdf->Cell(45, 10, '', 1);
$pdf->Cell(45, 10, number_format($calculos['spf'], 2), 1);
$pdf->Ln();

// LPH
$pdf->Cell(100, 10, 'L.P.H. (1%)', 1);
$pdf->Cell(45, 10, '', 1);
$pdf->Cell(45, 10, number_format($calculos['lph'], 2), 1);
$pdf->Ln();

// Totales
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(100, 10, 'TOTALES', 1);
$pdf->Cell(45, 10, number_format($calculos['total_asignaciones'] + $calculos['sueldo_ciclo'], 2), 1);
$pdf->Cell(45, 10, number_format($calculos['total_deducciones'], 2), 1);
$pdf->Ln(15);

$pdf->Cell(190, 10, 'NETO A COBRAR: ' . formatCurrency($calculos['total_neto']), 0, 1, 'R');

$pdf->Output('I', 'recibo_' . $user['cedula'] . '.pdf');
?>
