<?php
require __DIR__ . '/config.php';

// Si ya está logueado va al inicio
if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario  = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';

    // Permite iniciar sesión con usuario o correo (si existe la columna correo)
    try {
        $stmt = $pdo->prepare("SELECT id, nombre, usuario, password_hash, rol FROM usuarios WHERE usuario = ? OR correo = ? LIMIT 1");
        $stmt->execute([$usuario, $usuario]);
    } catch (PDOException $e) {
        $stmt = $pdo->prepare("SELECT id, nombre, usuario, password_hash, rol FROM usuarios WHERE usuario = ? LIMIT 1");
        $stmt->execute([$usuario]);
    }
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password_hash'])) {
        $_SESSION['user_id'] = (int) $user['id'];
        $_SESSION['nombre']  = $user['nombre'];
        $_SESSION['usuario'] = $user['usuario'];
        $_SESSION['rol']     = $user['rol'];

        header('Location: index.php');
        exit;
    } else {
        $error = 'Usuario / correo o contraseña incorrectos.';
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Iniciar Sesión - Ferreluz</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

<div class="auth-box">
    <div style="text-align: center; margin-bottom: 15px;">
        <span class="logo-box">FL</span>
        <h2>Ferreluz S.R.L.</h2>
    </div>

    <h3 style="text-align: center; margin-bottom: 15px;">Iniciar Sesión</h3>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= h($error) ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="form-group">
            <label>Usuario o Correo:</label>
            <input type="text" name="usuario" required placeholder="Ingresa tu usuario o correo">
        </div>

        <div class="form-group">
            <label>Contraseña:</label>
            <input type="password" name="password" required placeholder="Ingresa tu contraseña">
        </div>

        <button type="submit" class="btn btn-success" style="width: 100%; font-size: 16px; padding: 10px;">Entrar</button>
    </form>

    <div class="auth-links">
        <p>¿No tienes cuenta? <a href="registro.php">Regístrate aquí</a></p>
    </div>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> Ferreluz S.R.L. - Desarrollado por Eilin</p>
</footer>

</body>
</html>