<?php
require __DIR__ . '/config.php';
require_login();

$pageTitle = 'Inventario';
$active    = 'inventario';
$cssFile   = 'inventario.css';

$q = trim($_GET['q'] ?? '');
$params = [];

$sql = "SELECT * FROM productos";
if ($q !== '') {
    $sql .= " WHERE nombre LIKE ? OR codigo LIKE ? OR categoria LIKE ?";
    $like = "%{$q}%";
    $params = [$like, $like, $like];
}
$sql .= " ORDER BY id DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$productos = $stmt->fetchAll();

$ok    = $_GET['ok'] ?? '';
$error = $_GET['error'] ?? '';

require __DIR__ . '/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
    <h2>Módulo de Inventario</h2>
    <?php if (($_SESSION['rol'] ?? '') === 'admin'): ?>
        <a href="producto.php" class="btn btn-success">+ Nuevo Producto</a>
    <?php endif; ?>
</div>

<?php if ($ok): ?>
    <div class="alert alert-success"><?= h($ok) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error"><?= h($error) ?></div>
<?php endif; ?>

<!-- Formulario de búsqueda -->
<form method="get" class="search-bar">
    <input type="text" name="q" value="<?= h($q) ?>" placeholder="Buscar por nombre, código o categoría...">
    <button type="submit" class="btn">Buscar</button>
    <?php if ($q !== ''): ?>
        <a href="inventario.php" class="btn btn-danger">Limpiar</a>
    <?php endif; ?>
</form>

<!-- Tabla de Inventario -->
<table>
    <thead>
        <tr>
            <th>Código</th>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Mínimo</th>
            <th>Estado</th>
            <?php if (($_SESSION['rol'] ?? '') === 'admin'): ?>
                <th>Acciones</th>
            <?php endif; ?>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($productos)): ?>
            <tr>
                <td colspan="<?= ($_SESSION['rol'] ?? '') === 'admin' ? 8 : 7 ?>" style="text-align: center;">No se encontraron productos.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($productos as $p): ?>
                <?php $esBajo = (int)$p['stock'] <= (int)$p['stock_minimo']; ?>
                <tr>
                    <td><?= h($p['codigo']) ?></td>
                    <td><strong><?= h($p['nombre']) ?></strong></td>
                    <td><?= h($p['categoria']) ?></td>
                    <td>RD$ <?= number_format($p['precio'], 2) ?></td>
                    <td><?= (int)$p['stock'] ?></td>
                    <td><?= (int)$p['stock_minimo'] ?></td>
                    <td>
                        <?php if ($esBajo): ?>
                            <span class="badge badge-bajo">Stock Bajo</span>
                        <?php else: ?>
                            <span class="badge badge-ok">Disponible</span>
                        <?php endif; ?>
                    </td>
                    <?php if (($_SESSION['rol'] ?? '') === 'admin'): ?>
                        <td>
                            <a href="producto.php?id=<?= (int)$p['id'] ?>" class="btn" style="padding: 3px 8px; font-size: 12px;">Editar</a>
                            <form method="post" action="eliminar.php" style="display: inline;" onsubmit="return confirm('¿Seguro de eliminar este producto?');">
                                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
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
    <p>&copy; <?= date('Y') ?> Ferreluz S.R.L. - Sistema de Gestión | Desarrollado por Estudiante</p>
</footer>

</div> <!-- Cierre de container -->
</body>
</html>
