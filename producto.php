<?php
require __DIR__ . '/config.php';
require_admin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: null;

$p = [
    'codigo'       => '',
    'nombre'       => '',
    'categoria'    => 'General',
    'precio'       => '0.00',
    'stock'        => '0',
    'stock_minimo' => '5'
];

if ($id) {
    $s = $pdo->prepare("SELECT * FROM productos WHERE id=?");
    $s->execute([$id]);
    $found = $s->fetch();

    if (!$found) {
        header('Location: inventario.php?error=Producto+no+encontrado');
        exit;
    }
    $p = $found;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $codigo       = trim($_POST['codigo'] ?? '');
    $nombre       = trim($_POST['nombre'] ?? '');
    $categoria    = trim($_POST['categoria'] ?? 'General');
    $precio       = (float) ($_POST['precio'] ?? 0);
    $stock        = (int) ($_POST['stock'] ?? 0);
    $stock_minimo = (int) ($_POST['stock_minimo'] ?? 5);

    if ($codigo === '' || $nombre === '') {
        $error = 'El código y el nombre son obligatorios.';
    } elseif ($precio < 0 || $stock < 0 || $stock_minimo < 0) {
        $error = 'Los valores no pueden ser negativos.';
    } else {
        try {
            if ($id) {
                $s = $pdo->prepare("UPDATE productos SET codigo=?, nombre=?, categoria=?, precio=?, stock=?, stock_minimo=? WHERE id=?");
                $s->execute([$codigo, $nombre, $categoria, $precio, $stock, $stock_minimo, $id]);
            } else {
                $s = $pdo->prepare("INSERT INTO productos (codigo, nombre, categoria, precio, stock, stock_minimo) VALUES (?, ?, ?, ?, ?, ?)");
                $s->execute([$codigo, $nombre, $categoria, $precio, $stock, $stock_minimo]);
            }

            header('Location: inventario.php?ok=Producto+guardado+correctamente');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $error = 'Ese código de producto ya existe.';
            } else {
                $error = 'Error al guardar en la base de datos.';
            }
        }
    }
    $p = ['codigo' => $codigo, 'nombre' => $nombre, 'categoria' => $categoria, 'precio' => $precio, 'stock' => $stock, 'stock_minimo' => $stock_minimo];
}

$pageTitle = $id ? 'Editar Producto' : 'Nuevo Producto';
$active    = 'inventario';
$cssFile   = 'producto.css';

require __DIR__ . '/header.php';
?>

<h2><?= $id ? 'Editar Producto' : 'Registrar Nuevo Producto' ?></h2>
<p>Completa el formulario para <?= $id ? 'actualizar los datos' : 'añadir un nuevo producto al inventario' ?>.</p>

<?php if ($error): ?>
    <div class="alert alert-error"><?= h($error) ?></div>
<?php endif; ?>

<div class="product-form-container">
    <form method="post">
        <div class="form-grid">
            <div class="form-group">
                <label>Código del producto *</label>
                <input type="text" name="codigo" required value="<?= h($p['codigo']) ?>" placeholder="Ej: FER-101">
            </div>

            <div class="form-group">
                <label>Nombre del producto *</label>
                <input type="text" name="nombre" required value="<?= h($p['nombre']) ?>" placeholder="Ej: Martillo de mano">
            </div>

            <div class="form-group">
                <label>Categoría</label>
                <input type="text" name="categoria" value="<?= h($p['categoria']) ?>" placeholder="Ej: Herramientas">
            </div>

            <div class="form-group">
                <label>Precio (RD$)</label>
                <input type="number" step="0.01" min="0" name="precio" required value="<?= h($p['precio']) ?>">
            </div>

            <div class="form-group">
                <label>Stock inicial</label>
                <input type="number" min="0" name="stock" required value="<?= h($p['stock']) ?>">
            </div>

            <div class="form-group">
                <label>Alerta Stock Mínimo</label>
                <input type="number" min="0" name="stock_minimo" required value="<?= h($p['stock_minimo']) ?>">
            </div>
        </div>

        <div style="margin-top: 15px;">
            <button type="submit" class="btn btn-success"><?= $id ? 'Guardar Cambios' : 'Guardar Producto' ?></button>
            <a href="inventario.php" class="btn btn-danger">Cancelar</a>
        </div>
    </form>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> Ferreluz S.R.L. - Sistema de Gestión | Desarrollado por Eilin</p>
</footer>

</div> <!-- Cierre de container -->
</body>
</html>