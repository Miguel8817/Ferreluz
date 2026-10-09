<?php
require __DIR__ . '/config.php';
require_login();

$pageTitle = 'Proveedores';
$active    = 'proveedores';

$q = trim($_GET['q'] ?? '');
$params = [];

$sql = "SELECT * FROM proveedores";
if ($q !== '') {
    $sql .= " WHERE nombre LIKE ? OR rnc_cedula LIKE ? OR contacto_nombre LIKE ?";
    $like = "%{$q}%";
    $params = [$like, $like, $like];
}
$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$proveedores = $stmt->fetchAll();

$ok    = $_GET['ok'] ?? '';
$error = $_GET['error'] ?? '';

require __DIR__ . '/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
    <h2>Módulo de Proveedores</h2>
    <?php if (($_SESSION['rol'] ?? '') === 'admin'): ?>
        <a href="proveedor.php" class="btn btn-success">+ Nuevo Proveedor</a>
    <?php endif; ?>
</div>

<?php if ($ok): ?>
    <div class="alert alert-success"><?= h($ok) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error"><?= h($error) ?></div>
<?php endif; ?>

<!-- Formulario de búsqueda -->
<form method="get" class="search-bar" style="display: flex; gap: 10px; margin-bottom: 20px;">
    <input type="text" name="q" value="<?= h($q) ?>" placeholder="Buscar por RNC/Cédula, Nombre o Contacto..." style="flex: 1; padding: 8px; border: 1px solid #ccc; border-radius: 4px;">
    <button type="submit" class="btn">Buscar</button>
    <?php if ($q !== ''): ?>
        <a href="proveedores.php" class="btn btn-danger">Limpiar</a>
    <?php endif; ?>
</form>

<!-- Tabla de Proveedores -->
<table>
    <thead>
        <tr>
            <th>RNC / Cédula</th>
            <th>Nombre / Empresa</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>Contacto</th>
            <th>Dirección</th>
            <?php if (($_SESSION['rol'] ?? '') === 'admin'): ?>
                <th>Acciones</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($proveedores)): ?>
            <tr>
                <td colspan="<?= ($_SESSION['rol'] ?? '') === 'admin' ? 7 : 6 ?>" style="text-align: center;">No se encontraron proveedores registradas.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($proveedores as $prov): ?>
                <tr>
                    <td><strong><?= h($prov['rnc_cedula']) ?></strong></td>
                    <td><?= h($prov['nombre']) ?></td>
                    <td><?= h($prov['telefono']) ?></td>
                    <td><?= h($prov['email'] ?: 'N/A') ?></td>
                    <td><?= h($prov['contacto_nombre'] ?: 'N/A') ?></td>
                    <td><?= h($prov['direccion'] ?: 'N/A') ?></td>
                    <?php if (($_SESSION['rol'] ?? '') === 'admin'): ?>
                        <td>
                            <a href="proveedor.php?id=<?= (int)$prov['id'] ?>" class="btn" style="padding: 3px 8px; font-size: 12px;">Editar</a>
                            <form method="post" action="eliminar_proveedor.php" style="display: inline;" onsubmit="return confirm('¿Seguro que deseas eliminar este proveedor?');">
                                <input type="hidden" name="id" value="<?= (int)$prov['id'] ?>">
                                <button type="submit" class="btn btn-danger" style="padding: 3px 8px; font-size: 12px;">Eliminar</button>
                            </form>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<footer>
    <p>&copy; <?= date('Y') ?> Ferreluz S.R.L. - Sistema de Gestión | Desarrollado por Eilin</p>
</footer>

</div> <!-- Cierre de container -->
</body>
</html>
