<?php
require __DIR__ . '/config.php';

// Si ya está logueado va al inicio
if (!empty($_SESSION['user_id'])) {
    header('Location: index.php');
    exit;
}

$error    = '';
$ok       = '';
$nombre   = '';
$apellido = '';
$correo   = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre'] ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $correo   = trim($_POST['correo'] ?? '');
    $password = $_POST['password'] ?? '';

    $usuarioBase = strtolower(explode('@', $correo)[0] ?? '');

    if ($nombre === '' || $apellido === '' || $correo === '' || strlen($password) < 4) {
        $error = 'Por favor completa todos los campos. La contraseña debe tener al menos 4 caracteres.';
    } elseif (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
        $error = 'Por favor ingresa un correo electrónico válido.';
    } else {
        try {
            $usuario = $usuarioBase;
            $count   = 1;
            $check   = $pdo->prepare("SELECT COUNT(*) FROM usuarios WHERE usuario = ?");

            while (true) {
                $check->execute([$usuario]);
                if ($check->fetchColumn() == 0) {
                    break;
                }
                $usuario = $usuarioBase . $count;
                $count++;
            }

            $nombreCompleto = $nombre . ' ' . $apellido;
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, correo, usuario, password_hash, rol) VALUES (?, ?, ?, ?, 'empleado')");
            $stmt->execute([
                $nombreCompleto,
                $correo,
                $usuario,
                password_hash($password, PASSWORD_DEFAULT)
            ]);

            $ok       = "Registro exitoso. Tu usuario asignado es: {$usuario}. Ya puedes iniciar sesión.";
            $nombre   = '';
            $apellido = '';
            $correo   = '';
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $error = 'El correo electrónico ya está registrado.';
            } else {
                $error = 'Ocurrió un error al procesar el registro.';
            }
        }
    }
}
?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Registro - Ferreluz</title>
    <link rel="stylesheet" href="css/global.css">
    <link rel="stylesheet" href="css/login.css">
</head>
<body>

<div class="auth-box">
    <div style="text-align: center; margin-bottom: 15px;">
        <span class="logo-box">FL</span>
        <h2>Ferreluz S.R.L.</h2>
    </div>

    <h3 style="text-align: center; margin-bottom: 15px;">Crear Cuenta</h3>

    <?php if ($ok): ?>
        <div class="alert alert-success">
            <?= h($ok) ?><br><br>
            <a href="login.php" class="btn btn-success" style="display: block; text-align: center;">Ir a Iniciar Sesión</a>
        </div>
    <?php endif; ?>

    <?php if ($error): ?>
        <div class="alert alert-error"><?= h($error) ?></div>
    <?php endif; ?>

    <?php if (!$ok): ?>
    <form method="post">
        <div class="form-group">
            <label>Nombre:</label>
            <input type="text" name="nombre" required value="<?= h($nombre) ?>" placeholder="Ingresa tu nombre">
        </div>

        <div class="form-group">
            <label>Apellido:</label>
            <input type="text" name="apellido" required value="<?= h($apellido) ?>" placeholder="Ingresa tu apellido">
        </div>

        <div class="form-group">
            <label>Correo Electrónico:</label>
            <input type="email" name="correo" required value="<?= h($correo) ?>" placeholder="ejemplo@correo.com">
        </div>

        <div class="form-group">
            <label>Contraseña:</label>
            <input type="password" name="password" required placeholder="Crea tu contraseña (mínimo 4 caracteres)">
        </div>

        <button type="submit" class="btn btn-success" style="width: 100%; font-size: 16px; padding: 10px;">Crear Cuenta</button>
    </form>
    <?php endif; ?>

    <div class="auth-links">
        <p>¿Ya tienes una cuenta? <a href="login.php">Inicia sesión aquí</a></p>
    </div>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> Ferreluz S.R.L. - Desarrollado por Eilin</p>
</footer>

</body>
</html>