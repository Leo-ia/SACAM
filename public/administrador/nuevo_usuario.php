<?php
require __DIR__ . '/../../app/administrador/requiere_login.php';

$errores = $_SESSION['nuevo_errores'] ?? [];
$valores = $_SESSION['nuevo_viejo']   ?? [];
unset($_SESSION['nuevo_errores'], $_SESSION['nuevo_viejo']);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../css/estilos_admin.css">
    <title>SACAM - Registrar nuevo usuario</title>
</head>
<body>
    <h1>Registrar nuevo usuario</h1>
    <p>
        <a href="usuarios.php">Volver al listado</a>
        |
        <a href="cerrar_sesion.php">Cerrar sesión</a>
    </p>

    <?php if (!empty($errores['general'])): ?>
        <p style="color:red;"><?= htmlspecialchars($errores['general']) ?></p>
    <?php endif; ?>

    <form method="post" action="procesar_crear_usuario.php" enctype="multipart/form-data" novalidate>
        <div>
            <label for="tipo_persona">Tipo de persona</label><br>
            <select id="tipo_persona" name="tipo_persona" required>
                <option value="" disabled <?= empty($valores['tipo_persona']) ? 'selected' : '' ?>>Selecciona una opción</option>
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
            <label for="correo_electronico">Correo electrónico</label><br>
            <input type="email" id="correo_electronico" name="correo_electronico" required maxlength="190" value="<?= htmlspecialchars($valores['correo_electronico'] ?? '') ?>">
            <span style="color:red;"><?= htmlspecialchars($errores['correo_electronico'] ?? '') ?></span>
        </div>

        <div>
            <label for="foto_credencial">Foto de credencial IPN</label><br>
            <p>Formato JPG o PNG, máximo 5 MB.</p>
            <input type="file" id="foto_credencial" name="foto_credencial" accept="image/jpeg,image/png" required>
            <span style="color:red;"><?= htmlspecialchars($errores['foto_credencial'] ?? '') ?></span>
        </div>

        <div>
            <label for="licencia_permiso">Licencia o permiso (opcional)</label><br>
            <input type="text" id="licencia_permiso" name="licencia_permiso" maxlength="30" value="<?= htmlspecialchars($valores['licencia_permiso'] ?? '') ?>">
            <span style="color:red;"><?= htmlspecialchars($errores['licencia_permiso'] ?? '') ?></span>
        </div>

        <br>
        <button type="submit">Registrar usuario</button>
    </form>
</body>
</html>