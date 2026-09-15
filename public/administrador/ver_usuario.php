<?php
require_once __DIR__ . '/../../app/includes/conexion.php';

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($id <= 0) {
    header('Location: usuarios.php');
    exit;
}

$pdo = sacam_conexion();

$stmt = $pdo->prepare('SELECT * FROM usuarios WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$usuario = $stmt->fetch();

if (!$usuario) {
    header('Location: usuarios.php');
    exit;
}

$stmt_motos = $pdo->prepare('SELECT * FROM motocicletas WHERE usuario_id = :usuario_id ORDER BY id ASC');
$stmt_motos->execute([':usuario_id' => $id]);
$motos = $stmt_motos->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/estilos_admin.css">
    <title>SACAM - Ver usuario</title>
</head>
<body>
    <h1>Detalle de usuario</h1>
    <p>
        <a href="usuarios.php">Volver al listado</a>
        |
        <a href="editar_usuario.php?id=<?= $id ?>">Editar</a>
    </p>

    <?php if ($usuario['activo']): ?>
        <form method="post" action="procesar_estado_usuario.php" onsubmit="return confirm('Revocar el acceso de este usuario?')">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="accion" value="revocar">
            <button type="submit">Revocar acceso</button>
        </form>
    <?php else: ?>
        <p><strong>Acceso revocado</strong></p>
        <form method="post" action="procesar_estado_usuario.php">
            <input type="hidden" name="id" value="<?= $id ?>">
            <input type="hidden" name="accion" value="restaurar">
            <button type="submit">Restaurar acceso</button>
        </form>
    <?php endif; ?>

    <h2>Datos personales</h2>
    <table border="1" cellpadding="6" cellspacing="0">
        <tr>
            <th>ID</th>
            <td><?= htmlspecialchars($usuario['id']) ?></td>
        </tr>
        <tr>
            <th>Tipo de persona</th>
            <td><?= htmlspecialchars(ucfirst($usuario['tipo_persona'])) ?></td>
        </tr>
        <tr>
            <th>Identificador institucional</th>
            <td><?= htmlspecialchars($usuario['identificador_institucional']) ?></td>
        </tr>
        <tr>
            <th>Nombre completo</th>
            <td><?= htmlspecialchars($usuario['nombre_completo']) ?></td>
        </tr>
        <tr>
            <th>Correo electronico</th>
            <td><?= htmlspecialchars($usuario['correo_electronico']) ?></td>
        </tr>
        <tr>
            <th>Licencia / Permiso</th>
            <td><?= htmlspecialchars($usuario['licencia_permiso'] ?: 'N/A') ?></td>
        </tr>
        <tr>
            <th>Acceso</th>
            <td><?= htmlspecialchars($usuario['activo'] ? 'Activo' : 'Revocado') ?></td>
        </tr>
        <tr>
            <th>Foto credencial</th>
            <td><?= htmlspecialchars($usuario['foto_credencial']) ?></td>
        </tr>
        <tr>
            <th>Fecha de registro</th>
            <td><?= htmlspecialchars($usuario['created_at']) ?></td>
        </tr>
        <tr>
            <th>Ultima actualizacion</th>
            <td><?= htmlspecialchars($usuario['updated_at']) ?></td>
        </tr>
    </table>

    <h2>Motocicleta(s)</h2>
    <?php if (empty($motos)): ?>
        <p>No tiene motocicletas registradas.</p>
    <?php else: ?>
        <?php foreach ($motos as $i => $moto): ?>
            <h3>Motocicleta #<?= $i + 1 ?></h3>
            <table border="1" cellpadding="6" cellspacing="0">
                <tr>
                    <th>ID</th>
                    <td><?= htmlspecialchars($moto['id']) ?></td>
                </tr>
                <tr>
                    <th>Marca</th>
                    <td><?= htmlspecialchars($moto['marca']) ?></td>
                </tr>
                <tr>
                    <th>Modelo</th>
                    <td><?= htmlspecialchars($moto['modelo']) ?></td>
                </tr>
                <tr>
                    <th>Color</th>
                    <td><?= htmlspecialchars($moto['color']) ?></td>
                </tr>
                <tr>
                    <th>Placa</th>
                    <td><?= htmlspecialchars($moto['placa']) ?></td>
                </tr>
                <tr>
                    <th>Tipo de placa</th>
                    <td><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $moto['tipo_placa']))) ?></td>
                </tr>
                <tr>
                    <th>Estado</th>
                    <td><?= htmlspecialchars(ucfirst(str_replace('_', ' ', $moto['estado']))) ?></td>
                </tr>
                <tr>
                    <th>Foto</th>
                    <td><?= htmlspecialchars($moto['foto']) ?></td>
                </tr>
                <tr>
                    <th>Fecha de registro</th>
                    <td><?= htmlspecialchars($moto['created_at']) ?></td>
                </tr>
                <tr>
                    <th>Ultima actualizacion</th>
                    <td><?= htmlspecialchars($moto['updated_at']) ?></td>
                </tr>
            </table>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
