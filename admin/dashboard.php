<?php
/**
 * Ubicación del archivo: admin/dashboard.php
 */
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !hasRole('administrador')) {
    redirect('../login.php');
}

// Estadísticas de la Estructura del Proyecto
$totalEmpleados = $pdo->query("SELECT COUNT(*) FROM usuarios u JOIN roles r ON u.rol_id = r.id WHERE r.nombre = 'empleado'")->fetchColumn();
$totalDeptos = $pdo->query("SELECT COUNT(*) FROM departamentos")->fetchColumn();
$totalCargos = $pdo->query("SELECT COUNT(*) FROM cargos")->fetchColumn();

// Obtener lista de usuarios para gestión con info extra
$stmt = $pdo->query("SELECT u.*, r.nombre as rol_nombre, d.nombre as dept_nombre, c.nombre as cargo_nombre
                     FROM usuarios u
                     JOIN roles r ON u.rol_id = r.id
                     LEFT JOIN departamentos d ON u.departamento_id = d.id
                     LEFT JOIN cargos c ON u.cargo_id = c.id");
$usuarios = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Administrador - Nómina VZLA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        .card-counter {
            box-shadow: 2px 2px 10px #DADADA;
            margin: 5px;
            padding: 20px 10px;
            background-color: #fff;
            height: 100px;
            border-radius: 5px;
            transition: .3s linear all;
        }
        .card-counter:hover {
            box-shadow: 4px 4px 20px #DADADA;
            transition: .3s linear all;
        }
        .card-counter.primary { background-color: #007bff; color: #FFF; }
        .card-counter.success { background-color: #66bb6a; color: #FFF; }
        .card-counter.info { background-color: #26c6da; color: #FFF; }
        .card-counter i { font-size: 5em; opacity: 0.2; }
        .card-counter .count-numbers { position: absolute; right: 35px; top: 20px; font-size: 32px; display: block; }
        .card-counter .count-name { position: absolute; right: 35px; top: 65px; font-style: italic; text-transform: capitalize; opacity: 0.5; display: block; font-size: 18px; }
    </style>
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow">
    <div class="container">
        <a class="navbar-brand fw-bold" href="dashboard.php"><i class="bi bi-briefcase-fill me-2"></i>Nómina VZLA Admin</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <span class="nav-link text-white">Bienvenido, <strong><?php echo sanitize($_SESSION['nombre']); ?></strong></span>
                </li>
                <li class="nav-item ms-lg-3">
                    <a class="btn btn-outline-light btn-sm mt-1" href="../logout.php">Cerrar Sesión</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container mt-4">
    <div class="row">
        <div class="col-md-4">
            <div class="card-counter primary position-relative overflow-hidden">
                <i class="bi bi-people-fill"></i>
                <span class="count-numbers"><?php echo $totalEmpleados; ?></span>
                <span class="count-name">Empleados</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-counter success position-relative overflow-hidden">
                <i class="bi bi-building"></i>
                <span class="count-numbers"><?php echo $totalDeptos; ?></span>
                <span class="count-name">Departamentos</span>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card-counter info position-relative overflow-hidden">
                <i class="bi bi-person-badge"></i>
                <span class="count-numbers"><?php echo $totalCargos; ?></span>
                <span class="count-name">Cargos</span>
            </div>
        </div>
    </div>

    <div class="card mt-4 shadow-sm border-0">
        <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
            <h5 class="mb-0 fw-bold text-primary">Estructura Organizacional y Personal</h5>
            <button class="btn btn-primary btn-sm"><i class="bi bi-person-plus me-1"></i>Nuevo Usuario</button>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Identificación</th>
                            <th>Nombre Completo</th>
                            <th>Departamento</th>
                            <th>Cargo</th>
                            <th>Sueldo Base</th>
                            <th>Rol</th>
                            <th class="text-center">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($usuarios as $u): ?>
                            <tr>
                                <td><span class="fw-bold"><?php echo sanitize($u['cedula']); ?></span></td>
                                <td><?php echo sanitize($u['nombre']) . ' ' . sanitize($u['apellido']); ?></td>
                                <td><?php echo sanitize($u['dept_nombre'] ?? 'Sin asignar'); ?></td>
                                <td><?php echo sanitize($u['cargo_nombre'] ?? 'Sin asignar'); ?></td>
                                <td><?php echo formatCurrency($u['sueldo_base']); ?></td>
                                <td><span class="badge bg-secondary"><?php echo ucfirst(sanitize($u['rol_nombre'])); ?></span></td>
                                <td class="text-center">
                                    <div class="btn-group">
                                        <button title="Editar" class="btn btn-outline-warning btn-sm"><i class="bi bi-pencil"></i></button>
                                        <a href="../generate_voucher.php?id=<?php echo $u['id']; ?>" title="Generar Pago" class="btn btn-outline-success btn-sm"><i class="bi bi-currency-dollar"></i></a>
                                        <button title="Eliminar" class="btn btn-outline-danger btn-sm"><i class="bi bi-trash"></i></button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<footer class="mt-5 py-3 text-center text-muted border-top bg-white">
    <small>Sistema de Nómina VZLA - Gestión de Estructura Organizacional</small>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
