<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Forzar UTF-8
ini_set('default_charset', 'UTF-8');
mb_internal_encoding('UTF-8');

// Configurar conexion Supabase
require_once __DIR__ . '/modelo/Conexion.php';

// Cargar rutas
if (file_exists(__DIR__ . '/routes/web.php')) {
    include_once __DIR__ . '/routes/web.php';
} else {
    die('500 - Archivo de rutas no encontrado.');
}

// Obtener URL
$url = $_GET['url'] ?? '';
$url = trim($url, '/');

// Determinar ruta
if ($url === '') {
    $route = $routes[''] ?? 'pages/auth/controller/AuthController.php';
} elseif (array_key_exists($url, $routes)) {
    $route = $routes[$url];
} else {
    http_response_code(404);
    echo "404 - Página no encontrada";
    exit;
}

// Incluir página
if (file_exists(__DIR__ . '/' . $route)) {
    require_once __DIR__ . '/' . $route;
} else {
    http_response_code(500);
    echo "500 - Archivo no existe: " . htmlspecialchars($route);
}
