<?php
/**
 * Ubicación del archivo: supervisor/dashboard.php
 */
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !hasRole('supervisor')) {
    redirect('../login.php');
}

// Supervisor solo visualiza empleados con info de cargo y depto
$stmt = $pdo->prepare("SELECT u.*, d.nombre as dept_nombre, c.nombre as cargo_nombre
                       FROM usuarios u
                       JOIN roles r ON u.rol_id = r.id
                       LEFT JOIN departamentos d ON u.departamento_id = d.id
                       LEFT JOIN cargos c ON u.cargo_id = c.id
                       WHERE r.nombre = 'empleado'");
$stmt->execute();
$empleados = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Supervisor - Nómina VZLA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-secondary">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">Panel Supervisor</a>
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
    <div class="card shadow-sm">
        <div class="card-header bg-white">
            <h4 class="mb-0">Control de Empleados</h4>
        </div>
        <div class="card-body">
            <p>Usted tiene acceso de visualización a la lista de empleados a su cargo.</p>
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                        <tr>
                            <th>Cédula</th>
                            <th>Nombre Completo</th>
                            <th>Departamento</th>
                            <th>Cargo</th>
                            <th>Email</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($empleados as $e): ?>
                            <tr>
                                <td><?php echo sanitize($e['cedula']); ?></td>
                                <td><?php echo sanitize($e['nombre']) . ' ' . sanitize($e['apellido']); ?></td>
                                <td><?php echo sanitize($e['dept_nombre'] ?? 'N/A'); ?></td>
                                <td><?php echo sanitize($e['cargo_nombre'] ?? 'N/A'); ?></td>
                                <td><?php echo sanitize($e['email']); ?></td>
                                <td><span class="badge bg-success">Activo</span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

</body>
</html>
