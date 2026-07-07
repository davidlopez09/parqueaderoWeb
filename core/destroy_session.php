<?php
if (session_status() === PHP_SESSION_NONE) session_start();
session_destroy();
$baseUrl = defined('BASE_URL') ? BASE_URL : 'http://localhost/parqueaderoWeb';
header('Location: ' . $baseUrl . '/login.php');
exit;
