<?php
require __DIR__ . '/config.php';
require_admin();

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT) ?: null;

$prov = [
    'rnc_cedula'      => '',
    'nombre'          => '',
    'telefono'        => '',
    'email'           => '',
    'direccion'       => '',
    'contacto_nombre' => ''
];

if ($id) {
    $s = $pdo->prepare("SELECT * FROM proveedores WHERE id=?");
    $s->execute([$id]);
    $found = $s->fetch();

    if (!$found) {
        header('Location: proveedores.php?error=Proveedor+no+encontrado');
        exit;
    }
    $prov = $found;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $rnc_cedula      = trim($_POST['rnc_cedula'] ?? '');
    $nombre          = trim($_POST['nombre'] ?? '');
    $telefono        = trim($_POST['telefono'] ?? '');
    $email           = trim($_POST['email'] ?? '');
    $direccion       = trim($_POST['direccion'] ?? '');
    $contacto_nombre = trim($_POST['contacto_nombre'] ?? '');

    if ($rnc_cedula === '' || $nombre === '' || $telefono === '') {
        $error = 'El RNC/Cédula, el Nombre y el Teléfono son campos obligatorios.';
    } else {
        try {
            if ($id) {
                $s = $pdo->prepare("UPDATE proveedores SET rnc_cedula=?, nombre=?, telefono=?, email=?, direccion=?, contacto_nombre=? WHERE id=?");
                $s->execute([$rnc_cedula, $nombre, $telefono, $email, $direccion, $contacto_nombre, $id]);
            } else {
                $s = $pdo->prepare("INSERT INTO proveedores (rnc_cedula, nombre, telefono, email, direccion, contacto_nombre) VALUES (?, ?, ?, ?, ?, ?)");
                $s->execute([$rnc_cedula, $nombre, $telefono, $email, $direccion, $contacto_nombre]);
            }

            header('Location: proveedores.php?ok=Proveedor+guardado+correctamente');
            exit;
        } catch (PDOException $e) {
            if ($e->getCode() === '23000') {
                $error = 'El RNC/Cédula ingresado ya está registrado para otro proveedor.';
            } else {
                $error = 'Error al guardar en la base de datos.';
            }
        }
    }
    $prov = [
        'rnc_cedula'      => $rnc_cedula,
        'nombre'          => $nombre,
        'telefono'        => $telefono,
        'email'           => $email,
        'direccion'       => $direccion,
        'contacto_nombre' => $contacto_nombre
    ];
}

$pageTitle = $id ? 'Editar Proveedor' : 'Nuevo Proveedor';
$active    = 'proveedores';

require __DIR__ . '/header.php';
?>

<h2><?= $id ? 'Editar Proveedor' : 'Registrar Nuevo Proveedor' ?></h2>
<p>Completa el formulario para <?= $id ? 'actualizar la información del proveedor' : 'añadir un nuevo proveedor al sistema' ?>.</p>

<?php if ($error): ?>
    <div class="alert alert-error"><?= h($error) ?></div>
<?php endif; ?>

<div style="background-color: #f8f9fa; padding: 20px; border-radius: 5px; border: 1px solid #e9ecef; margin-top: 15px;">
    <form method="post">
        <div class="form-grid">
            <div class="form-group">
                <label>RNC / Cédula *</label>
                <input type="text" name="rnc_cedula" required value="<?= h($prov['rnc_cedula']) ?>" placeholder="Ej: 130-123456-7 o 101000000">
            </div>

            <div class="form-group">
                <label>Nombre / Razón Social *</label>
                <input type="text" name="nombre" required value="<?= h($prov['nombre']) ?>" placeholder="Ej: Distribuidora Ferretera SRL">
            </div>

            <div class="form-group">
                <label>Teléfono *</label>
                <input type="text" name="telefono" required value="<?= h($prov['telefono']) ?>" placeholder="Ej: 809-555-0199">
            </div>

            <div class="form-group">
                <label>Correo Electrónico (Email)</label>
                <input type="email" name="email" value="<?= h($prov['email']) ?>" placeholder="Ej: contacto@distribuidora.com">
            </div>

            <div class="form-group">
                <label>Nombre de Contacto</label>
                <input type="text" name="contacto_nombre" value="<?= h($prov['contacto_nombre']) ?>" placeholder="Ej: Lic. Juan Pérez">
            </div>

            <div class="form-group">
                <label>Dirección</label>
                <input type="text" name="direccion" value="<?= h($prov['direccion']) ?>" placeholder="Ej: Av. 27 de Febrero #45, Santo Domingo">
            </div>
        </div>

        <div style="margin-top: 20px;">
            <button type="submit" class="btn btn-success"><?= $id ? 'Guardar Cambios' : 'Guardar Proveedor' ?></button>
            <a href="proveedores.php" class="btn btn-danger">Cancelar</a>
        </div>
    </form>
</div>

<footer>
    <p>&copy; <?= date('Y') ?> Ferreluz S.R.L. - Sistema de Gestión | Desarrollado por Eilin</p>
</footer>

</div> <!-- Cierre de container -->
</body>
</html>
