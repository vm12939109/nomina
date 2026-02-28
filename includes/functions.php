<?php
/**
 * Ubicación del archivo: includes/functions.php
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Redirigir a una URL específica
 */
function redirect($url) {
    header("Location: $url");
    exit();
}

/**
 * Verificar si el usuario está logueado
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Verificar si el usuario tiene un rol específico
 */
function hasRole($role_name) {
    return isset($_SESSION['role']) && $_SESSION['role'] === $role_name;
}

/**
 * Sanitizar entradas de usuario
 */
function sanitize($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Formatear moneda (Bolívares)
 */
function formatCurrency($amount) {
    return 'Bs. ' . number_format($amount, 2, ',', '.');
}

/**
 * Obtener nombre de rol por ID
 */
function getRoleName($pdo, $role_id) {
    $stmt = $pdo->prepare("SELECT nombre FROM roles WHERE id = ?");
    $stmt->execute([$role_id]);
    $role = $stmt->fetch();
    return $role ? $role['nombre'] : 'Desconocido';
}
?>
