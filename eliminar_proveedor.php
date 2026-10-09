<?php
require __DIR__ . '/config.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: proveedores.php');
    exit;
}

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header('Location: proveedores.php?error=Solicitud+no+válida');
    exit;
}

$s = $pdo->prepare("DELETE FROM proveedores WHERE id=?");
$s->execute([$id]);

header('Location: proveedores.php?ok=Proveedor+eliminado+correctamente');
exit;
