<?php
session_start();
require_once __DIR__ . '/../../app/includes/conexion.php';

$pdo = sacam_conexion();
$stmt = $pdo->query('SELECT id, nombre_completo, identificador_institucional, correo_electronico, activo FROM usuarios ORDER BY id ASC');
$usuarios = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/estilos_admin.css">
    <title>SACAM - Listado de usuarios</title>
</head>
<body>
    <h1>Listado de usuarios</h1>
    <p><a href="../index.php">Volver al inicio</a></p>

    <table border="1" cellpadding="6" cellspacing="0">
        <thead>
            <tr>
                <th>ID</th>
                <th>Nombre</th>
                <th>Identificador</th>
                <th>Correo</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($usuarios)): ?>
                <tr>
                    <td colspan="6">No hay usuarios registrados.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($usuarios as $u): ?>
                    <tr>
                        <td><?= htmlspecialchars($u['id']) ?></td>
                        <td><?= htmlspecialchars($u['nombre_completo']) ?></td>
                        <td><?= htmlspecialchars($u['identificador_institucional']) ?></td>
                        <td><?= htmlspecialchars($u['correo_electronico']) ?></td>
                        <td><?= htmlspecialchars($u['activo'] ? 'Activo' : 'Revocado') ?></td>
                        <td>
                            <a href="ver_usuario.php?id=<?= (int) $u['id'] ?>">Ver</a>
                            |
                            <a href="editar_usuario.php?id=<?= (int) $u['id'] ?>">Editar</a>
                            |
                            <form method="post" action="procesar_eliminar_usuario.php" style="display:inline;" onsubmit="return confirm('Seguro que quieres eliminar este usuario? Se borraran tambien sus motocicletas.')">
                                <input type="hidden" name="id" value="<?= (int) $u['id'] ?>">
                                <button type="submit">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</body>
</html>
