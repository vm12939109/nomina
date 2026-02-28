<?php
/**
 * Ubicación del archivo: register.php
 */
require_once 'includes/db.php';
require_once 'includes/functions.php';

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cedula = sanitize($_POST['cedula']);
    $nombre = sanitize($_POST['nombre']);
    $apellido = sanitize($_POST['apellido']);
    $email = sanitize($_POST['email']);
    $password = $_POST['password'];

    // Obtener ID del rol 'empleado'
    $roleStmt = $pdo->prepare("SELECT id FROM roles WHERE nombre = 'empleado'");
    $roleStmt->execute();
    $roleId = $roleStmt->fetchColumn();

    // Comprobar si ya existe la cédula
    $checkStmt = $pdo->prepare("SELECT id FROM usuarios WHERE cedula = ? OR email = ?");
    $checkStmt->execute([$cedula, $email]);
    if ($checkStmt->fetch()) {
        $error = "La cédula o el correo electrónico ya están registrados.";
    } else {
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $insert = $pdo->prepare("INSERT INTO usuarios (cedula, nombre, apellido, email, password, rol_id) VALUES (?, ?, ?, ?, ?, ?)");
        if ($insert->execute([$cedula, $nombre, $apellido, $email, $hashedPassword, $roleId])) {
            $success = "Registro exitoso. Ya puede <a href='login.php'>iniciar sesión</a>.";
        } else {
            $error = "Ocurrió un error al registrar el empleado.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Empleado - Nómina VZLA</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-body p-5">
                    <h3 class="text-center mb-4">Registro de Empleado</h3>
                    <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label">Cédula</label>
                            <input type="text" name="cedula" class="form-control" required placeholder="Ej: V12345678">
                        </div>
                        <div class="row mb-3">
                            <div class="col">
                                <label class="form-label">Nombre</label>
                                <input type="text" name="nombre" class="form-control" required>
                            </div>
                            <div class="col">
                                <label class="form-label">Apellido</label>
                                <input type="text" name="apellido" class="form-control" required>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Registrarse</button>
                    </form>
                    <div class="text-center mt-3">
                        <a href="login.php">¿Ya tienes cuenta? Inicia sesión aquí</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

</body>
</html>
