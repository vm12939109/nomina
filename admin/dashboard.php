<?php
/**
 * Ubicación del archivo: admin/dashboard.php
 */
require_once '../includes/db.php';
require_once '../includes/functions.php';

if (!isLoggedIn() || !hasRole('administrador')) {
    redirect('../login.php');
}

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
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="dashboard.php">Panel Admin</a>
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
    <h2>Gestión de Personal</h2>
    <div class="table-responsive mt-4">
        <table class="table table-striped table-hover">
            <thead class="table-primary">
                <tr>
                    <th>Cédula</th>
                    <th>Nombre</th>
                    <th>Departamento</th>
                    <th>Cargo</th>
                    <th>Sueldo Base</th>
                    <th>Rol</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?php echo sanitize($u['cedula']); ?></td>
                        <td><?php echo sanitize($u['nombre']) . ' ' . sanitize($u['apellido']); ?></td>
                        <td><?php echo sanitize($u['dept_nombre'] ?? 'N/A'); ?></td>
                        <td><?php echo sanitize($u['cargo_nombre'] ?? 'N/A'); ?></td>
                        <td><?php echo formatCurrency($u['sueldo_base']); ?></td>
                        <td><span class="badge bg-info text-dark"><?php echo ucfirst(sanitize($u['rol_nombre'])); ?></span></td>
                        <td>
                            <button class="btn btn-sm btn-warning">Editar</button>
                            <button class="btn btn-sm btn-danger">Eliminar</button>
                            <a href="../generate_voucher.php?id=<?php echo $u['id']; ?>" class="btn btn-sm btn-success">Generar Pago</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

</body>
</html>
