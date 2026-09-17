<?php
require __DIR__ . '/../../app/administrador/requiere_login.php';
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

$errores = $_SESSION['editar_errores'] ?? [];
$viejo   = $_SESSION['editar_viejo']   ?? [];
unset($_SESSION['editar_errores'], $_SESSION['editar_viejo']);

$valores = $viejo ?: $usuario;
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/estilos_admin.css">
    <title>SACAM - Editar usuario</title>
</head>
<body>
    <h1>Editar usuario</h1>
    <p>
        <a href="ver_usuario.php?id=<?= $id ?>">Volver a ver usuario</a>
        |
        <a href="usuarios.php">Volver al listado</a>
        |
        <a href="cerrar_sesion.php">Cerrar sesión</a>
    </p>

    <?php if (!empty($errores['general'])): ?>
        <p style="color:red;"><?= htmlspecialchars($errores['general']) ?></p>
    <?php endif; ?>

    <form method="post" action="procesar_editar_usuario.php" novalidate>
        <input type="hidden" name="id" value="<?= $id ?>">

        <div>
            <label for="tipo_persona">Tipo de persona</label><br>
            <select id="tipo_persona" name="tipo_persona" required>
                <?php foreach (['alumno', 'docente', 'administrativo', 'intendencia'] as $opcion): ?>
                    <option value="<?= $opcion ?>" <?= ($valores['tipo_persona'] ?? '') === $opcion ? 'selected' : '' ?>>
                        <?= ucfirst($opcion) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <span style="color:red;"><?= htmlspecialchars($errores['tipo_persona'] ?? '') ?></span>
        </div>

        <div>
            <label for="identificador_institucional">Identificador institucional</label><br>
            <input type="text" id="identificador_institucional" name="identificador_institucional" required maxlength="20" value="<?= htmlspecialchars($valores['identificador_institucional'] ?? '') ?>">
            <span style="color:red;"><?= htmlspecialchars($errores['identificador_institucional'] ?? '') ?></span>
        </div>

        <div>
            <label for="nombre_completo">Nombre completo</label><br>
            <input type="text" id="nombre_completo" name="nombre_completo" required maxlength="120" value="<?= htmlspecialchars($valores['nombre_completo'] ?? '') ?>">
            <span style="color:red;"><?= htmlspecialchars($errores['nombre_completo'] ?? '') ?></span>
        </div>

        <div>
            <label for="correo_electronico">Correo electronico</label><br>
            <input type="email" id="correo_electronico" name="correo_electronico" required maxlength="190" value="<?= htmlspecialchars($valores['correo_electronico'] ?? '') ?>">
            <span style="color:red;"><?= htmlspecialchars($errores['correo_electronico'] ?? '') ?></span>
        </div>

        <div>
            <label for="licencia_permiso">Licencia o permiso (opcional)</label><br>
            <input type="text" id="licencia_permiso" name="licencia_permiso" maxlength="30" value="<?= htmlspecialchars($valores['licencia_permiso'] ?? '') ?>">
            <span style="color:red;"><?= htmlspecialchars($errores['licencia_permiso'] ?? '') ?></span>
        </div>

        <br>
        <button type="submit">Guardar cambios</button>
    </form>
</body>
</html>
