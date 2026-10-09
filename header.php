<?php require_login(); ?>
<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($pageTitle ?? 'Ferreluz') ?></title>
    <!-- CSS Global -->
    <link rel="stylesheet" href="css/global.css">
    <!-- CSS Específico del Archivo -->
    <?php if (isset($cssFile)): ?>
        <link rel="stylesheet" href="css/<?= h($cssFile) ?>">
    <?php endif; ?>
</head>
<body>

<header>
    <div class="logo-container">
        <div class="logo-box">FL</div>
        <div>
            <strong>FERRELUZ</strong>
        </div>
    </div>

    <nav>
        <ul>
            <li><a href="index.php" class="<?= ($active ?? '') === 'inicio' ? 'activo' : '' ?>">Inicio</a></li>
            <li><a href="inventario.php" class="<?= ($active ?? '') === 'inventario' ? 'activo' : '' ?>">Inventario</a></li>
            <li><a href="ventas.php" class="<?= ($active ?? '') === 'ventas' ? 'activo' : '' ?>">Ventas</a></li>
            <li><a href="proveedores.php" class="<?= ($active ?? '') === 'proveedores' ? 'activo' : '' ?>">Proveedores</a></li>
            <?php if (($_SESSION['rol'] ?? '') === 'admin'): ?>
                <li><a href="usuarios.php" class="<?= ($active ?? '') === 'usuarios' ? 'activo' : '' ?>">Usuarios</a></li>
            <?php endif; ?>
        </ul>
    </nav>

    <div class="user-info">
        <span>Hola, <strong><?= h($_SESSION['nombre']) ?></strong> (<?= ($_SESSION['rol'] ?? '') === 'admin' ? 'Admin' : 'Empleado' ?>)</span>
        <a href="logout.php" class="btn-logout">Salir</a>
    </div>
</header>

<div class="container">