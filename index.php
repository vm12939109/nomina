<?php
/**
 * Ubicación del archivo: index.php
 */
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Nómina - Venezuela</title>
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body class="bg-light">

<nav class="navbar navbar-expand-lg navbar-dark bg-primary">
    <div class="container">
        <a class="navbar-brand" href="index.php">Nómina VZLA</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <?php if (isLoggedIn()): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">Cerrar Sesión</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php">Iniciar Sesión</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="register.php">Registrarse</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<header class="bg-white py-5 mb-5 shadow-sm">
    <div class="container text-center">
        <h1 class="display-4 fw-bold">Gestión de Nómina según Legislación Venezolana</h1>
        <p class="lead">Simplifique sus procesos de pago con eficiencia y legalidad.</p>
        <div class="mt-4">
            <a href="login.php" class="btn btn-primary btn-lg px-4 me-md-2">Acceder al Sistema</a>
            <a href="register.php" class="btn btn-outline-secondary btn-lg px-4">Registro Empleado</a>
        </div>
    </div>
</header>

<main class="container">
    <div class="row g-4 py-5 row-cols-1 row-cols-lg-3">
        <div class="col d-flex align-items-start">
            <div class="icon-square bg-light text-dark flex-shrink-0 me-3">
                <i class="bi bi-shield-check"></i>
            </div>
            <div>
                <h2>Cumplimiento Legal</h2>
                <p>Cálculos precisos basados en la LOTTT y reglamentos vigentes en Venezuela.</p>
            </div>
        </div>
        <div class="col d-flex align-items-start">
            <div class="icon-square bg-light text-dark flex-shrink-0 me-3">
                <i class="bi bi-clock-history"></i>
            </div>
            <div>
                <h2>Ciclos de Pago</h2>
                <p>Flexibilidad para pagos semanales (7 días), quincenales (15 días) y mensuales (30 días).</p>
            </div>
        </div>
        <div class="col d-flex align-items-start">
            <div class="icon-square bg-light text-dark flex-shrink-0 me-3">
                <i class="bi bi-file-pdf"></i>
            </div>
            <div>
                <h2>Recibos Digitales</h2>
                <p>Generación automática de comprobantes de pago en formato PDF con la librería FPDF.</p>
            </div>
        </div>
    </div>
</main>

<footer class="bg-dark text-white py-4 mt-auto">
    <div class="container text-center">
        <p class="mb-0">&copy; <?php echo date('Y'); ?> Sistema de Nómina VZLA - Desarrollado en PHP Puro</p>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
