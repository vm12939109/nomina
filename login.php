<?php
/**
 * Ubicación del archivo: login.php
 */
require_once 'includes/db.php';
require_once 'includes/functions.php';

if (isLoggedIn()) {
    $role_folder = [
        'administrador' => 'admin',
        'supervisor'    => 'supervisor',
        'empleado'      => 'employee'
    ];
    $folder = isset($role_folder[$_SESSION['role']]) ? $role_folder[$_SESSION['role']] : 'employee';
    redirect("$folder/dashboard.php");
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = sanitize($_POST['cedula']);
    $password = $_POST['password'];

    // Lógica para crear administrador si no existen usuarios
    $stmtCount = $pdo->query("SELECT COUNT(*) FROM usuarios");
    $totalUsers = $stmtCount->fetchColumn();

    if ($totalUsers == 0) {
        $rolAdminStmt = $pdo->prepare("SELECT id FROM roles WHERE nombre = 'administrador'");
        $rolAdminStmt->execute();
        $rolAdminId = $rolAdminStmt->fetchColumn();

        $hashedPassword = password_hash('admin123', PASSWORD_DEFAULT);
        $insertAdmin = $pdo->prepare("INSERT INTO usuarios (cedula, nombre, apellido, email, password, rol_id) VALUES (?, ?, ?, ?, ?, ?)");
        $insertAdmin->execute(['V00000000', 'Admin', 'Principal', 'admin@nomina.com', $hashedPassword, $rolAdminId]);

        $error = "Se ha creado un administrador por defecto (Cédula: V00000000, Pass: admin123). Intente de nuevo.";
    } else {
        $stmt = $pdo->prepare("SELECT u.*, r.nombre as rol_nombre FROM usuarios u JOIN roles r ON u.rol_id = r.id WHERE u.cedula = ?");
        $stmt->execute([$cedula]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['nombre'] = $user['nombre'];
            $_SESSION['role'] = $user['rol_nombre'];

            // Mapeo de nombres de roles a directorios
            $role_folder = [
                'administrador' => 'admin',
                'supervisor'    => 'supervisor',
                'empleado'      => 'employee'
            ];

            $folder = isset($role_folder[$user['rol_nombre']]) ? $role_folder[$user['rol_nombre']] : 'employee';
            redirect($folder . "/dashboard.php");
        } else {
            $error = "Cédula o contraseña incorrectos.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar Sesión - Nómina VZLA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light d-flex align-items-center" style="height: 100vh;">

<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h3 class="text-center mb-4">Iniciar Sesión</h3>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Cédula</label>
                            <input type="text" name="cedula" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Entrar</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="register.php">¿No tienes cuenta? Regístrate aquí</a><br>
                        <a href="recovery.php">¿Olvidaste tu contraseña?</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
