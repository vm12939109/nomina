<?php
/**
 * Ubicación del archivo: config/config.php
 */

// Configuración de la Base de Datos
define('DB_HOST', 'localhost');
define('DB_NAME', 'nomina_venezuela');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuración de errores
ini_set('log_errors', 1);
ini_set('error_log', __DIR__ . '/../logs/index_error.log');
error_reporting(E_ALL);

// Zona horaria
date_default_timezone_set('America/Caracas');

// URL Base (Ajustar según sea necesario)
define('BASE_URL', 'http://localhost/');
?>
