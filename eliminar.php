<?php
#Eso es pa indicar donde esta la base de datos
require __DIR__ . '/config.php';
#Eso es pa una cosa del admin
require_admin();

#Eso es pa verificar si es post
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: inventario.php');
    exit;
}
#Eso es pa obtener el indentificador unico
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
#Eso es pa verificar si el indentificador unico es valido
if (!$id) {
    header('Location: inventario.php?error=Solicitud+no+valida');
    exit;
}
#Eso es pa eliminar un producto
$s = $pdo->prepare("DELETE FROM productos WHERE id=?");
$s->execute([$id]);
#Eso es pa regresar a inventario
header('Location: inventario.php?ok=Producto+eliminado');
exit;