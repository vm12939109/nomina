<?php
/**
 * Ubicación del archivo: employee/dashboard.php
 */
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !hasRole('empleado')) {
    redirect('../login.php');
}

$user_id = $_SESSION['user_id'];
// Obtener info del empleado con estructura
$stmtUser = $pdo->prepare("SELECT u.*, d.nombre as dept_nombre, c.nombre as cargo_nombre
                           FROM usuarios u
                           LEFT JOIN departamentos d ON u.departamento_id = d.id
                           LEFT JOIN cargos c ON u.cargo_id = c.id
                           WHERE u.id = ?");
$stmtUser->execute([$user_id]);
$empleado = $stmtUser->fetch();

$stmt = $pdo->prepare("SELECT * FROM nomina WHERE usuario_id = ? ORDER BY fecha_pago DESC");
$stmt->execute([$user_id]);
$nominas = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mi Panel de Nómina - Nómina VZLA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .profile-card { border-left: 5px solid #0dcaf0; }
        .payment-row:hover { background-color: #f8f9fa; cursor: pointer; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-info shadow">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php"><i class="bi bi-person-workspace me-2"></i>Portal del Empleado</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a class="nav-link active" href="#"><i class="bi bi-house-door me-1"></i>Inicio</a>
                </li>
                <li class="nav-item ms-lg-3">
                    <a class="btn btn-light btn-sm mt-1" href="../logout.php">Cerrar Sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="row">
        <!-- Perfil y Estructura -->
        <div class="col-lg-4">
            <div class="card shadow-sm border-0 profile-card mb-4">
                <div class="card-body text-center py-4">
                    <div class="mb-3">
                        <i class="bi bi-person-circle text-info" style="font-size: 4rem;"></i>
                    </div>
                    <h4 class="fw-bold mb-1"><?php echo sanitize($empleado['nombre'] . ' ' . $empleado['apellido']); ?></h4>
                    <p class="text-muted small mb-3"><?php echo sanitize($empleado['email']); ?></p>
                    <hr>
                    <div class="text-start">
                        <p class="mb-2"><strong><i class="bi bi-building me-2"></i>Depto:</strong> <span class="float-end"><?php echo sanitize($empleado['dept_nombre'] ?? 'N/A'); ?></span></p>
                        <p class="mb-2"><strong><i class="bi bi-briefcase me-2"></i>Cargo:</strong> <span class="float-end"><?php echo sanitize($empleado['cargo_nombre'] ?? 'N/A'); ?></span></p>
                        <p class="mb-2"><strong><i class="bi bi-calendar-event me-2"></i>Ingreso:</strong> <span class="float-end"><?php echo date('d/m/Y', strtotime($empleado['fecha_ingreso'])); ?></span></p>
                        <p class="mb-0 text-success"><strong><i class="bi bi-cash-stack me-2"></i>Sueldo Base:</strong> <span class="float-end fw-bold"><?php echo formatCurrency($empleado['sueldo_base']); ?></span></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Historial de Pagos -->
        <div class="col-lg-8">
            <div class="card shadow-sm border-0">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-bold text-dark"><i class="bi bi-file-earmark-pdf me-2 text-danger"></i>Historial de Pagos y Vales</h5>
                </div>
                <div class="card-body">
                    <p class="text-muted small">Visualice y descargue sus comprobantes de pago generados bajo la normativa legal venezolana.</p>

                    <div class="table-responsive">
                        <table class="table align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Fecha de Pago</th>
                                    <th>Tipo de Ciclo</th>
                                    <th>Monto Neto</th>
                                    <th class="text-center">Acción</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (empty($nominas)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5">
                                            <i class="bi bi-cloud-slash fs-1 text-muted d-block mb-2"></i>
                                            No se han emitido pagos para este perfil todavía.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($nominas as $n): ?>
                                        <tr class="payment-row">
                                            <td><i class="bi bi-calendar-check me-2 text-info"></i><?php echo sanitize($n['fecha_pago']); ?></td>
                                            <td><?php echo sanitize($n['tipo_pago']); ?> Días</td>
                                            <td class="fw-bold text-success"><?php echo formatCurrency($n['total_neto']); ?></td>
                                            <td class="text-center">
                                                <a href="../generate_voucher.php?id=<?php echo $user_id; ?>&nomina_id=<?php echo $n['id']; ?>" class="btn btn-sm btn-primary px-3 shadow-sm">
                                                    <i class="bi bi-download me-1"></i> PDF
                                                </a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<footer class="mt-5 py-4 bg-white border-top">
    <div class="container text-center">
        <span class="text-muted small">Sistema de Nómina Venezolana &copy; <?php echo date('Y'); ?> | Transparencia y Cumplimiento Legal</span>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
