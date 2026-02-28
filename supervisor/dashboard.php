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
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-secondary shadow">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php"><i class="bi bi-eye-fill me-2"></i>Supervisor Hub</a>
        <div class="collapse navbar-collapse">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <span class="nav-link text-white">Sesión iniciada: <strong><?php echo sanitize($_SESSION['nombre']); ?></strong></span>
                </li>
                <li class="nav-item ms-lg-3">
                    <a class="btn btn-outline-light btn-sm mt-1" href="../logout.php">Salir</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-5">
    <div class="row mb-4">
        <div class="col-12">
            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center">
                <i class="bi bi-info-circle-fill fs-4 me-3"></i>
                <div>
                    <h5 class="alert-heading mb-1">Control de Estructura</h5>
                    <p class="mb-0">Usted tiene acceso a la visualización detallada del personal bajo su supervisión y su ubicación en la estructura organizacional.</p>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-bold text-secondary"><i class="bi bi-people me-2"></i>Personal Supervisado</h5>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover table-bordered align-middle">
                    <thead class="table-secondary">
                        <tr>
                            <th>Identificación</th>
                            <th>Nombre Completo</th>
                            <th>Ubicación (Departamento)</th>
                            <th>Cargo / Función</th>
                            <th>Email Institucional</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($empleados)): ?>
                            <tr>
                                <td colspan="6" class="text-center py-4 text-muted">No hay empleados registrados bajo su supervisión.</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($empleados as $e): ?>
                                <tr>
                                    <td><span class="badge bg-light text-dark border"><?php echo sanitize($e['cedula']); ?></span></td>
                                    <td><?php echo sanitize($e['nombre']) . ' ' . sanitize($e['apellido']); ?></td>
                                    <td><i class="bi bi-geo-alt me-1 text-primary"></i><?php echo sanitize($e['dept_nombre'] ?? 'Sin asignar'); ?></td>
                                    <td><i class="bi bi-briefcase me-1 text-primary"></i><?php echo sanitize($e['cargo_nombre'] ?? 'Sin asignar'); ?></td>
                                    <td><small><?php echo sanitize($e['email']); ?></small></td>
                                    <td class="text-center"><span class="badge rounded-pill bg-success">Activo</span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<footer class="mt-auto py-3 text-center text-muted">
    <hr>
    <p class="small">&copy; <?php echo date('Y'); ?> Sistema de Nómina VZLA - Módulo de Supervisión</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
