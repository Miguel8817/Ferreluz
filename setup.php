<?php
#Para traer la conexion a mysql
require __DIR__ . '/config.php';



$check   = (int) $pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
$mensaje = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $check === 0) {
    $hash = password_hash('password', PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, usuario, password_hash, rol) VALUES (?, ?, ?, ?)");
    $stmt->execute(['Administrador Ferreluz', 'admin', $hash, 'admin']);

    $hashEmpleado = password_hash('empleado123', PASSWORD_DEFAULT);
    $stmt->execute(['Empleado Ferreluz', 'empleado', $hashEmpleado, 'empleado']);

    $productos = [
        ['FER-001', 'Martillo de acero', 'Herramientas', 395, 18, 5],
        ['FER-002', 'Cinta métrica 5 m', 'Medición', 225, 24, 6],
        ['FER-003', 'Pintura blanca 1 galón', 'Pinturas', 890, 8, 3],
        ['FER-004', 'Taladro percutor', 'Herramientas eléctricas', 3290, 4, 5],
        ['FER-005', 'Tornillos multiuso (caja)', 'Tornillería', 290, 32, 8]
    ];

    $insert = $pdo->prepare("INSERT INTO productos (codigo, nombre, categoria, precio, stock, stock_minimo) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($productos as $p) {
        $insert->execute($p);
    }

    $proveedores = [
        ['101000123', 'Distribuidora Ferretera Nacional', '809-555-0101', 'ventas@ferretera.do', 'Av. John F. Kennedy #12, Santo Domingo', 'Ing. Carlos Mendoza'],
        ['130987654', 'Suministros Eléctricos del Caribe', '809-555-0202', 'contacto@secaribe.com', 'Av. 27 de Febrero #88, Santiago', 'Lic. Maria Rodriguez']
    ];

    $insertProv = $pdo->prepare("INSERT INTO proveedores (rnc_cedula, nombre, telefono, email, direccion, contacto_nombre) VALUES (?, ?, ?, ?, ?, ?)");
    foreach ($proveedores as $pr) {
        $insertProv->execute($pr);
    }

    $mensaje = 'Configuración completada. Ya puedes iniciar sesión.';
    $check   = 2;
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Configurar Ferreluz</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="auth-body">
    <main class="auth-card">
        <a class="brand auth-brand" href="login.php">
            <span class="brand-mark">F<span>+</span></span>
            <span><strong>ferreluz</strong><small>SUMINISTROS & MÁS</small></span>
        </a>

        <p class="eyebrow">CONFIGURACIÓN INICIAL</p>
        <h1>Preparar Ferreluz</h1>
        <p class="muted">Crea las cuentas de acceso iniciales y carga algunos productos de ejemplo.</p>

        <?php if ($mensaje): ?>
            <div class="notice success"><?= h($mensaje) ?></div>
        <?php endif; ?>

        <?php if ($check === 0): ?>
            <form method="post">
                <button class="primary-btn full-btn" type="submit">Crear usuarios y productos</button>
            </form>
        <?php else: ?>
            <div class="credentials">
                <strong>Administrador</strong>
                <span>Usuario: <b>admin</b></span>
                <span>Contraseña: <b>password</b></span>
                <br>
                <strong>Empleado</strong>
                <span>Usuario: <b>empleado</b></span>
                <span>Contraseña: <b>empleado123</b></span>
            </div>
            <a class="primary-btn full-btn" href="login.php">Ir al inicio de sesión →</a>
        <?php endif; ?>

        <p class="security-note">Por seguridad, elimina setup.php después de configurar el proyecto y cambia las contraseñas iniciales.</p>
    </main>
</body>
</html>