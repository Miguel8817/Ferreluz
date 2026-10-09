<?php
#Estos son los datos para conectar la base de datos
# Obtener variables de entorno (Railway / producción) o usar defaults locales
$host = getenv('MYSQLHOST') ?: getenv('DB_HOST') ?: 'localhost';
$port = getenv('MYSQLPORT') ?: getenv('DB_PORT') ?: '3306';
$db   = getenv('MYSQLDATABASE') ?: getenv('DB_NAME') ?: 'ferreluz';
$user = getenv('MYSQLUSER') ?: getenv('DB_USER') ?: 'root';
$pass = getenv('MYSQLPASSWORD') !== false ? getenv('MYSQLPASSWORD') : (getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');

# En caso de que se use DATABASE_URL de Railway
if (getenv('DATABASE_URL')) {
    $dbUrl = parse_url(getenv('DATABASE_URL'));
    $host = $dbUrl['host'] ?? $host;
    $port = $dbUrl['port'] ?? $port;
    $user = $dbUrl['user'] ?? $user;
    $pass = $dbUrl['pass'] ?? $pass;
    $db   = ltrim($dbUrl['path'] ?? '', '/') ?: $db;
}

# Conexión a la base de datos
try {
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$db;charset=utf8mb4", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
} catch (PDOException $e) {
    die("Error de conexión a la base de datos: " . $e->getMessage());
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



