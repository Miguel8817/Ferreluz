<?php
require __DIR__ . '/config.php';
require_login();

$pageTitle = 'Ventas';
$active    = 'ventas';
$cssFile   = 'ventas.css';

$error = '';
$ok    = '';

// Crear tabla de ventas si no existe
$pdo->exec("CREATE TABLE IF NOT EXISTS ventas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    producto_id INT NOT NULL,
    usuario_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio_unitario DECIMAL(10,2) NOT NULL,
    total DECIMAL(10,2) NOT NULL,
    fecha DATETIME DEFAULT CURRENT_TIMESTAMP
)");

// Procesar registro de venta
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto_id = (int)($_POST['producto_id'] ?? 0);
    $cantidad    = (int)($_POST['cantidad'] ?? 0);

    if ($producto_id <= 0 || $cantidad <= 0) {
        $error = 'Por favor selecciona un producto y especifica una cantidad válida.';
    } else {
        // Buscar el producto
        $stmt = $pdo->prepare("SELECT * FROM productos WHERE id = ?");
        $stmt->execute([$producto_id]);
        $prod = $stmt->fetch();

        if (!$prod) {
            $error = 'El producto seleccionado no existe.';
        } elseif ($prod['stock'] < $cantidad) {
            $error = 'No hay suficiente stock para realizar esta venta. Disponible: ' . $prod['stock'];
        } else {
            // Registrar la venta y descontar stock
            $precio = (float)$prod['precio'];
            $total  = $precio * $cantidad;

            // 1. Insertar en ventas
            $ins = $pdo->prepare("INSERT INTO ventas (producto_id, usuario_id, cantidad, precio_unitario, total) VALUES (?, ?, ?, ?, ?)");
            $ins->execute([$producto_id, $_SESSION['user_id'], $cantidad, $precio, $total]);

            // 2. Descontar del inventario
            $upd = $pdo->prepare("UPDATE productos SET stock = stock - ? WHERE id = ?");
            $upd->execute([$cantidad, $producto_id]);

            $ok = 'Venta registrada con éxito. Total cobrado: RD$ ' . number_format($total, 2);
        }
    }
}

// Obtener lista de productos para el combo
$productos = $pdo->query("SELECT id, nombre, precio, stock FROM productos WHERE stock > 0 ORDER BY nombre ASC")->fetchAll();

// Obtener ventas realizadas
$ventas = $pdo->query("
    SELECT v.*, p.nombre as producto_nombre, u.nombre as vendedor_nombre 
    FROM ventas v 
    JOIN productos p ON v.producto_id = p.id 
    JOIN usuarios u ON v.usuario_id = u.id 
    ORDER BY v.id DESC
")->fetchAll();

require __DIR__ . '/header.php';
?>

<h2>Módulo de Ventas / Facturación</h2>
<p>Permite registrar salidas de mercancía y emitir ventas (disponible para <strong>Empleado</strong> y <strong>Administrador</strong>).</p>

<?php if ($ok): ?>
    <div class="alert alert-success"><?= h($ok) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-error"><?= h($error) ?></div>
<?php endif; ?>

<!-- Formulario para registrar venta -->
<div class="sales-box">
    <h3>Registrar Nueva Venta</h3>
    <form method="post">
        <div class="form-group">
            <label>Seleccionar Producto:</label>
            <select name="producto_id" required>
                <option value="">-- Seleccione un producto --</option>
                <?php foreach ($productos as $p): ?>
                    <option value="<?= (int)$p['id'] ?>">
                        <?= h($p['nombre']) ?> - RD$ <?= number_format($p['precio'], 2) ?> (Stock: <?= $p['stock'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="form-group">
            <label>Cantidad a vender:</label>
            <input type="number" name="cantidad" min="1" value="1" required>
        </div>

        <button type="submit" class="btn btn-success">Completar Venta</button>
    </form>
</div>

<!-- Tabla de ventas realizadas -->
<h3>Historial de Ventas Registradas</h3>
<table>
    <thead>
        <tr>
            <th>ID Venta</th>
            <th>Fecha / Hora</th>
            <th>Producto</th>
            <th>Vendedor</th>
            <th>Cant.</th>
            <th>Precio Unit.</th>
            <th>Total</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($ventas)): ?>
            <tr>
                <td colspan="7" style="text-align: center;">No hay ventas registradas aún.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($ventas as $v): ?>
                <tr>
                    <td>#<?= (int)$v['id'] ?></td>
                    <td><?= h(date('d/m/Y H:i', strtotime($v['fecha']))) ?></td>
                    <td><strong><?= h($v['producto_nombre']) ?></strong></td>
                    <td><?= h($v['vendedor_nombre']) ?></td>
                    <td><?= (int)$v['cantidad'] ?></td>
                    <td>RD$ <?= number_format($v['precio_unitario'], 2) ?></td>
                    <td><strong>RD$ <?= number_format($v['total'], 2) ?></strong></td>
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
