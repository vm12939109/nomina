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
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-info">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">Mi Nómina</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <span class="nav-link text-white">Hola, <?php echo sanitize($_SESSION['nombre']); ?></span>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="../logout.php">Cerrar Sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="card shadow">
        <div class="card-body">
            <h3>Mis Comprobantes de Pago</h3>
            <p>Aquí puede visualizar y descargar sus bauches de pago históricos.</p>

            <div class="table-responsive mt-4">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Fecha de Pago</th>
                            <th>Tipo de Ciclo</th>
                            <th>Total Neto</th>
                            <th>Bauche</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($nominas)): ?>
                            <tr>
                                <td colspan="4" class="text-center">No tiene pagos registrados aún.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($nominas as $n): ?>
                                <tr>
                                    <td><?php echo sanitize($n['fecha_pago']); ?></td>
                                    <td><?php echo sanitize($n['tipo_pago']); ?> Días</td>
                                    <td><?php echo formatCurrency($n['total_neto']); ?></td>
                                    <td>
                                        <a href="../generate_voucher.php?id=<?php echo $user_id; ?>&nomina_id=<?php echo $n['id']; ?>" class="btn btn-sm btn-primary">Descargar PDF</a>
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

</body>
</html>
