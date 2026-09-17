<?php
require __DIR__ . '/../../app/administrador/requiere_login.php';
require_once __DIR__ . '/../../app/includes/conexion.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: usuarios.php');
    exit;
}

$pdo = sacam_conexion();
$stmt = $pdo->prepare('SELECT * FROM motocicletas WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$moto = $stmt->fetch();

if (!$moto) {
    header('Location: usuarios.php');
    exit;
}

$errores = $_SESSION['editar_moto_errores'] ?? [];
$viejo   = $_SESSION['editar_moto_viejo']   ?? [];
unset($_SESSION['editar_moto_errores'], $_SESSION['editar_moto_viejo']);

$valores = $viejo ?: $moto;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/estilos_admin.css">
    <title>SACAM - Editar motocicleta</title>
</head>
<body>
    <h1>Editar motocicleta</h1>
    <p>
        <a href="ver_usuario.php?id=<?= (int) $moto['usuario_id'] ?>">Volver a ver usuario</a>
        |
        <a href="usuarios.php">Volver al listado</a>
        |
        <a href="cerrar_sesion.php">Cerrar sesión</a>
    </p>

    <?php if (!empty($errores['general'])): ?>
        <p style="color:red;"><?= htmlspecialchars($errores['general']) ?></p>
    <?php endif; ?>

    <form method="post" action="procesar_editar_moto.php" enctype="multipart/form-data" novalidate>
        <input type="hidden" name="id" value="<?= $id ?>">

        <div>
            <label for="marca">Marca</label><br>
            <input type="text" id="marca" name="marca" required maxlength="50" value="<?= htmlspecialchars($valores['marca'] ?? '') ?>">
            <span style="color:red;"><?= htmlspecialchars($errores['marca'] ?? '') ?></span>
        </div>

        <div>
            <label for="modelo">Modelo</label><br>
            <input type="text" id="modelo" name="modelo" required maxlength="50" value="<?= htmlspecialchars($valores['modelo'] ?? '') ?>">
            <span style="color:red;"><?= htmlspecialchars($errores['modelo'] ?? '') ?></span>
        </div>

        <div>
            <label for="color">Color</label><br>
            <input type="text" id="color" name="color" required maxlength="50" value="<?= htmlspecialchars($valores['color'] ?? '') ?>">
            <span style="color:red;"><?= htmlspecialchars($errores['color'] ?? '') ?></span>
        </div>

        <div>
            <span>¿La motocicleta ya tiene placa?</span><br>
            <label>
                <input type="radio" name="tipo_placa" value="con_placa" <?= ($valores['tipo_placa'] ?? 'con_placa') !== 'permiso_provisional' ? 'checked' : '' ?>>
                Sí, tiene placa definitiva
            </label><br>
            <label>
                <input type="radio" name="tipo_placa" value="permiso_provisional" <?= ($valores['tipo_placa'] ?? '') === 'permiso_provisional' ? 'checked' : '' ?>>
                No, tiene permiso provisional
            </label><br>
            <span style="color:red;"><?= htmlspecialchars($errores['tipo_placa'] ?? '') ?></span>
        </div>

        <div>
            <label for="placa">Placa</label><br>
            <input type="text" id="placa" name="placa" required maxlength="20" value="<?= htmlspecialchars($valores['placa'] ?? '') ?>">
            <span style="color:red;"><?= htmlspecialchars($errores['placa'] ?? '') ?></span>
        </div>

        <div>
            <label for="foto_motocicleta">Foto de la motocicleta</label><br>
            <p>Deja este campo vacío para conservar la foto actual (<?= htmlspecialchars($moto['foto']) ?>). Formato JPG o PNG, máximo 5 MB.</p>
            <input type="file" id="foto_motocicleta" name="foto_motocicleta" accept="image/jpeg,image/png">
            <span style="color:red;"><?= htmlspecialchars($errores['foto_motocicleta'] ?? '') ?></span>
        </div>

        <br>
        <button type="submit">Guardar cambios</button>
    </form>
</body>
</html>
