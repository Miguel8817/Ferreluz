<?php
require __DIR__ . '/config.php';
require_admin();

$pageTitle = 'Gestión de Usuarios';
$active    = 'usuarios';
$cssFile   = 'usuarios.css';

$error = '';
$ok    = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre'] ?? '');
    $usuario  = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    $rol      = $_POST['rol'] ?? 'empleado';

    if ($nombre === '' || $usuario === '' || strlen($password) < 4 || !in_array($rol, ['admin', 'empleado'], true)) {
        $error = 'Por favor completa los campos correctamente. Contraseña mínimo 4 caracteres.';
    } else {
        try {
            $stmt = $pdo->prepare("INSERT INTO usuarios (nombre, usuario, password_hash, rol) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nombre, $usuario, password_hash($password, PASSWORD_DEFAULT), $rol]);
            $ok = 'Usuario creado con éxito.';
        } catch (PDOException $e) {
            $error = 'Ese nombre de usuario ya está registrado.';
        }
    }
}

// Consultar todos los usuarios
$usuarios = $pdo->query("SELECT id, nombre, usuario, rol, creado_en FROM usuarios ORDER BY id ASC")->fetchAll();

require __DIR__ . '/header.php';
?>

<h2>Gestión de Usuarios y Roles</h2>
<p>Módulo exclusivo para el <strong>Administrador</strong>. Aquí puedes crear y consultar usuarios.</p>

<?php if ($ok): ?>
    <div class="alert alert-success"><?= h($ok) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error"><?= h($error) ?></div>
<?php endif; ?>

<!-- Formulario para crear usuario -->
<div class="user-create-box">
    <h3>Registrar Nuevo Usuario</h3>
    <form method="post">
        <div class="form-grid">
            <div class="form-group">
                <label>Nombre Completo:</label>
                <input type="text" name="nombre" required placeholder="Ej: Pedro Martínez">
            </div>

            <div class="form-group">
                <label>Usuario:</label>
                <input type="text" name="usuario" required placeholder="Ej: pedrom">
            </div>

            <div class="form-group">
                <label>Contraseña:</label>
                <input type="password" name="password" required placeholder="Contraseña">
            </div>

            <div class="form-group">
                <label>Rol de Usuario:</label>
                <select name="rol">
                    <option value="empleado">Empleado (Solo lectura inventario + Registrar ventas)</option>
                    <option value="admin">Administrador (Acceso total)</option>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Crear Usuario</button>
    </form>
</div>

<!-- Tabla de usuarios -->
<h3>Lista de Usuarios Registrados</h3>
<table>
    <thead>
        <tr>
            <th>ID</th>
            <th>Nombre</th>
            <th>Usuario</th>
            <th>Rol</th>
            <th>Fecha Registro</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= (int)$u['id'] ?></td>
                <td><strong><?= h($u['nombre']) ?></strong></td>
                <td><?= h($u['usuario']) ?></td>
                <td>
                    <span class="badge <?= $u['rol'] === 'admin' ? 'badge-admin' : 'badge-emp' ?>">
                        <?= $u['rol'] === 'admin' ? 'Administrador' : 'Empleado' ?>
                    </span>
                </td>
                <td><?= h(date('d/m/Y', strtotime($u['creado_en']))) ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<footer>
    <p>&copy; <?= date('Y') ?> Ferreluz S.R.L. - Sistema de Gestión | Desarrollado por Estudiante</p>
</footer>

</div> <!-- Cierre de container -->
</body>
</html>