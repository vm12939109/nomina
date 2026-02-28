<?php
/**
 * Ubicación del archivo: admin/dashboard.php
 */
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !hasRole('administrador')) {
    redirect('../login.php');
}

$section = isset($_GET['section']) ? sanitize($_GET['section']) : 'inicio';

// Estadísticas para el Inicio
$totalEmpleados = $pdo->query("SELECT COUNT(*) FROM usuarios u JOIN roles r ON u.rol_id = r.id WHERE r.nombre = 'empleado'")->fetchColumn();
$totalDeptos = $pdo->query("SELECT COUNT(*) FROM departamentos")->fetchColumn();
$totalCargos = $pdo->query("SELECT COUNT(*) FROM cargos")->fetchColumn();

// Datos para Empleados
$stmtUsers = $pdo->query("SELECT u.*, r.nombre as rol_nombre, d.nombre as dept_nombre, c.nombre as cargo_nombre
                          FROM usuarios u
                          JOIN roles r ON u.rol_id = r.id
                          LEFT JOIN departamentos d ON u.departamento_id = d.id
                          LEFT JOIN cargos c ON u.cargo_id = c.id");
$usuarios = $stmtUsers->fetchAll();

// Datos para Logs
$logContent = "";
if ($section === 'logs' && file_exists('../logs/index_error.log')) {
    $logContent = file_get_contents('../logs/index_error.log');
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Hub - Sistema de Nómina VZLA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <style>
        body { overflow-x: hidden; }
        .sidebar { min-height: 100vh; background: #212529; color: white; transition: all 0.3s; }
        .sidebar a { color: rgba(255,255,255,0.8); text-decoration: none; padding: 15px 20px; display: block; }
        .sidebar a:hover, .sidebar a.active { background: #343a40; color: white; border-left: 4px solid #0d6efd; }
        .main-content { background: #f8f9fa; min-height: 100vh; padding: 20px; }
        .card-stat { border: none; border-radius: 10px; transition: transform 0.2s; }
        .card-stat:hover { transform: translateY(-5px); }
    </style>
</head>
<body>

<div class="container-fluid">
    <div class="row">
        <!-- Sidebar -->
        <nav class="col-md-3 col-lg-2 d-md-block sidebar collapse p-0">
            <div class="p-4 text-center">
                <h4 class="fw-bold">Nómina VZLA</h4>
                <small class="text-muted text-uppercase">Panel de Control</small>
            </div>
            <div class="mt-3">
                <a href="?section=inicio" class="<?php echo $section === 'inicio' ? 'active' : ''; ?>">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
                <a href="?section=empleados" class="<?php echo $section === 'empleados' ? 'active' : ''; ?>">
                    <i class="bi bi-people me-2"></i> Empleados
                </a>
                <a href="?section=nomina" class="<?php echo $section === 'nomina' ? 'active' : ''; ?>">
                    <i class="bi bi-calculator me-2"></i> Procesar Nómina
                </a>
                <a href="?section=departamentos" class="<?php echo $section === 'departamentos' ? 'active' : ''; ?>">
                    <i class="bi bi-building me-2"></i> Departamentos
                </a>
                <a href="?section=logs" class="<?php echo $section === 'logs' ? 'active' : ''; ?>">
                    <i class="bi bi-journal-text me-2"></i> Logs del Sistema
                </a>
                <hr class="mx-3">
                <a href="../logout.php" class="text-danger">
                    <i class="bi bi-box-arrow-left me-2"></i> Cerrar Sesión
                </a>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4 main-content">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-4 border-bottom">
                <h1 class="h2 text-capitalize"><?php echo str_replace('_', ' ', $section); ?></h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <span class="me-3 text-muted"><i class="bi bi-person-circle me-1"></i><?php echo sanitize($_SESSION['nombre']); ?></span>
                </div>
            </div>

            <?php if ($section === 'inicio'): ?>
                <!-- Dashboard Section -->
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="card card-stat bg-primary text-white shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title text-uppercase opacity-75">Empleados Totales</h6>
                                <h2 class="display-6 fw-bold"><?php echo $totalEmpleados; ?></h2>
                                <i class="bi bi-people position-absolute bottom-0 end-0 p-3 opacity-25 fs-1"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-stat bg-success text-white shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title text-uppercase opacity-75">Departamentos</h6>
                                <h2 class="display-6 fw-bold"><?php echo $totalDeptos; ?></h2>
                                <i class="bi bi-building position-absolute bottom-0 end-0 p-3 opacity-25 fs-1"></i>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card card-stat bg-info text-white shadow-sm">
                            <div class="card-body">
                                <h6 class="card-title text-uppercase opacity-75">Cargos Definidos</h6>
                                <h2 class="display-6 fw-bold"><?php echo $totalCargos; ?></h2>
                                <i class="bi bi-briefcase position-absolute bottom-0 end-0 p-3 opacity-25 fs-1"></i>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($section === 'empleados'): ?>
                <!-- Employees Section -->
                <div class="card border-0 shadow-sm">
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="bg-light">
                                    <tr>
                                        <th class="ps-4">Cédula</th>
                                        <th>Nombre</th>
                                        <th>Departamento</th>
                                        <th>Cargo</th>
                                        <th>Sueldo Base</th>
                                        <th class="text-center">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($usuarios as $u): ?>
                                        <tr>
                                            <td class="ps-4 fw-bold"><?php echo sanitize($u['cedula']); ?></td>
                                            <td><?php echo sanitize($u['nombre'] . ' ' . $u['apellido']); ?></td>
                                            <td><?php echo sanitize($u['dept_nombre'] ?? 'N/A'); ?></td>
                                            <td><?php echo sanitize($u['cargo_nombre'] ?? 'N/A'); ?></td>
                                            <td><?php echo formatCurrency($u['sueldo_base']); ?></td>
                                            <td class="text-center">
                                                <a href="../generate_voucher.php?id=<?php echo $u['id']; ?>" class="btn btn-sm btn-outline-primary">Generar Pago</a>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            <?php elseif ($section === 'nomina'): ?>
                <!-- Payroll Section -->
                <div class="row">
                    <div class="col-md-6">
                        <div class="card border-0 shadow-sm">
                            <div class="card-body p-4">
                                <h5 class="fw-bold mb-3">Ciclos de Pago (Venezuela)</h5>
                                <p class="text-muted">Seleccione el ciclo de pago para procesar la nómina actual de todos los empleados.</p>
                                <div class="list-group">
                                    <button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        Pago Semanal (7 días)
                                        <span class="badge bg-primary rounded-pill">Procesar</span>
                                    </button>
                                    <button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        Pago Quincenal (15 días)
                                        <span class="badge bg-primary rounded-pill">Procesar</span>
                                    </button>
                                    <button class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                        Pago Mensual (30 días)
                                        <span class="badge bg-primary rounded-pill">Procesar</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            <?php elseif ($section === 'departamentos'): ?>
                <!-- Departments Section -->
                <?php
                $stmtDepts = $pdo->query("SELECT * FROM departamentos");
                $deptos = $stmtDepts->fetchAll();
                ?>
                <div class="card border-0 shadow-sm">
                    <div class="card-body">
                        <h5 class="fw-bold mb-3">Gestión de Departamentos</h5>
                        <ul class="list-group">
                            <?php foreach ($deptos as $d): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?php echo sanitize($d['nombre']); ?>
                                    <span class="badge bg-secondary rounded-pill">ID: <?php echo $d['id']; ?></span>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>

            <?php elseif ($section === 'logs'): ?>
                <!-- Logs Section -->
                <div class="card border-0 shadow-sm bg-dark text-white">
                    <div class="card-header bg-dark border-secondary">
                        <h5 class="mb-0">Visor de Errores (index_error.log)</h5>
                    </div>
                    <div class="card-body">
                        <pre style="height: 400px; overflow-y: auto;" class="small"><?php echo !empty($logContent) ? sanitize($logContent) : "No hay registros de error actualmente."; ?></pre>
                    </div>
                    <div class="card-footer bg-dark border-secondary">
                        <button onclick="location.reload()" class="btn btn-sm btn-outline-light">Actualizar Logs</button>
                    </div>
                </div>
            <?php endif; ?>

        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
