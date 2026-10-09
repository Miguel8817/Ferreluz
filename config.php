<?php
#Estos son los datos para conectar la base de datos
$host = 'localhost';
$db   = 'ferreluz';
$user = 'root';
$pass = '';

#Esto es para la conexion y si falla tambien
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
    
}

session_start();

function h($texto) {
    return htmlspecialchars($texto ?? '', ENT_QUOTES, 'UTF-8');
}

function require_login() {
    if (empty($_SESSION['user_id'])) {
        header('Location: login.php');
        exit;
    }
}

function require_admin() {
    if (empty($_SESSION['user_id']) || ($_SESSION['rol'] ?? '') !== 'admin') {
        header('Location: index.php');
        exit;
    }
}

?>



