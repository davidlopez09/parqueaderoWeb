<?php

$routes = [
    // Auth
    ''                    => 'pages/auth/controller/AuthController.php',
    'login'               => 'pages/auth/controller/AuthController.php',
    'logout'              => 'core/destroy_session.php',

    // Dashboard
    'dashboard'           => 'pages/dashboard/controller/DashboardController.php',

    // Tipos de vehículo
    'tipos'               => 'pages/tipos/controller/TiposController.php',

    // Historial
    'historial'           => 'pages/historial/controller/HistorialController.php',

    // Usuarios
    'usuarios'            => 'pages/usuarios/controller/UsuariosController.php',
];
