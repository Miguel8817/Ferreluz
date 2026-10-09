<?php
require __DIR__ . '/config.php';
require_login();

$pageTitle = 'Inicio';
$active    = 'inicio';
$cssFile   = 'index.css';

// Consultas sencillas para las tarjetas de resumen
$totalProductos = (int) $pdo->query("SELECT COUNT(*) FROM productos")->fetchColumn();
$totalStock     = (int) $pdo->query("SELECT COALESCE(SUM(stock), 0) FROM productos")->fetchColumn();
$stockBajo      = (int) $pdo->query("SELECT COUNT(*) FROM productos WHERE stock <= stock_minimo")->fetchColumn();

// Obtener los últimos 5 productos agregados
$recientes = $pdo->query("SELECT * FROM productos ORDER BY id DESC LIMIT 5")->fetchAll();

require __DIR__ . '/header.php';
?>

<h2>Panel de Inicio</h2>
<p>Bienvenido al sistema, <strong><?= h($_SESSION['nombre']) ?></strong>. Tu rol actual es: 
    <span class="badge <?= $_SESSION['rol'] === 'admin' ? 'badge-admin' : 'badge-emp' ?>">
        <?= $_SESSION['rol'] === 'admin' ? 'Administrador' : 'Empleado' ?>
    </span>
</p>

<!-- Tarjetas resumen -->
<div class="cards">
    <div class="card">
        <h3>Total Productos</h3>
        <div class="numero"><?= $totalProductos ?></div>
        <small>Productos en catálogo</small>
    </div>

    <div class="card">
        <h3>Total Stock Existente</h3>
        <div class="numero"><?= $totalStock ?></div>
        <small>Unidades almacenadas</small>
    </div>

    <div class="card alerta">
        <h3>Alertas Stock Bajo</h3>
        <div class="numero"><?= $stockBajo ?></div>
        <small>Productos con bajo inventario</small>
    </div>
</div>

<!-- Tabla de productos recientes -->
<h3>Últimos Productos Agregados</h3>
<table>
    <thead>
        <tr>
            <th>Código</th>
            <th>Nombre</th>
            <th>Categoría</th>
            <th>Precio</th>
            <th>Stock</th>
            <th>Estado</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($recientes)): ?>
            <tr>
                <td colspan="6" style="text-align: center;">No hay productos registrados.</td>
            </tr>
        <?php else: ?>
            <?php foreach ($recientes as $p): ?>
                <?php $esBajo = (int)$p['stock'] <= (int)$p['stock_minimo']; ?>
                <tr>
                    <td><?= h($p['codigo']) ?></td>
                    <td><strong><?= h($p['nombre']) ?></strong></td>
                    <td><?= h($p['categoria']) ?></td>
                    <td>RD$ <?= number_format($p['precio'], 2) ?></td>
                    <td><?= (int)$p['stock'] ?></td>
                    <td>
                        <?php if ($esBajo): ?>
                            <span class="badge badge-bajo">Stock Bajo</span>
                        <?php else: ?>
                            <span class="badge badge-ok">Disponible</span>
                        <?php endif; ?>
                    </td>
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