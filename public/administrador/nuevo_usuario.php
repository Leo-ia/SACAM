<?php
require __DIR__ . '/../../app/administrador/requiere_login.php';

$errores      = $_SESSION['nuevo_errores'] ?? [];
$valores      = $_SESSION['nuevo_viejo']   ?? [];
$valores_moto = $_SESSION['nuevo_moto']    ?? [];
unset($_SESSION['nuevo_errores'], $_SESSION['nuevo_viejo'], $_SESSION['nuevo_moto']);
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

        <div>
            <input type="checkbox" id="agregar_moto" name="agregar_moto" value="1" <?= ($valores_moto['agregar_moto'] ?? false) ? 'checked' : '' ?>>
            <label for="agregar_moto">Registrar también su motocicleta</label>
        </div>

        <fieldset id="seccion_moto" <?= ($valores_moto['agregar_moto'] ?? false) ? '' : 'hidden' ?>>
            <legend>Datos de la motocicleta</legend>

            <div>
                <label for="marca">Marca</label><br>
                <input type="text" id="marca" name="marca" maxlength="50" value="<?= htmlspecialchars($valores_moto['marca'] ?? '') ?>">
                <span style="color:red;"><?= htmlspecialchars($errores['marca'] ?? '') ?></span>
            </div>

            <div>
                <label for="modelo">Modelo</label><br>
                <input type="text" id="modelo" name="modelo" maxlength="50" value="<?= htmlspecialchars($valores_moto['modelo'] ?? '') ?>">
                <span style="color:red;"><?= htmlspecialchars($errores['modelo'] ?? '') ?></span>
            </div>

            <div>
                <label for="color">Color</label><br>
                <input type="text" id="color" name="color" maxlength="50" value="<?= htmlspecialchars($valores_moto['color'] ?? '') ?>">
                <span style="color:red;"><?= htmlspecialchars($errores['color'] ?? '') ?></span>
            </div>

            <div>
                <span>¿La motocicleta ya tiene placa?</span><br>
                <label>
                    <input type="radio" name="tipo_placa" value="con_placa" <?= ($valores_moto['tipo_placa'] ?? 'con_placa') !== 'permiso_provisional' ? 'checked' : '' ?>>
                    Sí, tiene placa definitiva
                </label><br>
                <label>
                    <input type="radio" name="tipo_placa" value="permiso_provisional" <?= ($valores_moto['tipo_placa'] ?? '') === 'permiso_provisional' ? 'checked' : '' ?>>
                    No, tengo permiso provisional
                </label><br>
                <span style="color:red;"><?= htmlspecialchars($errores['tipo_placa'] ?? '') ?></span>
            </div>

            <div>
                <label for="placa">Placa</label><br>
                <input type="text" id="placa" name="placa" maxlength="20" value="<?= htmlspecialchars($valores_moto['placa'] ?? '') ?>">
                <span style="color:red;"><?= htmlspecialchars($errores['placa'] ?? '') ?></span>
            </div>

            <div>
                <label for="foto_motocicleta">Foto de la motocicleta</label><br>
                <p>Formato JPG o PNG, máximo 5 MB.</p>
                <input type="file" id="foto_motocicleta" name="foto_motocicleta" accept="image/jpeg,image/png">
                <span style="color:red;"><?= htmlspecialchars($errores['foto_motocicleta'] ?? '') ?></span>
            </div>
        </fieldset>

        <br>
        <button type="submit">Registrar usuario</button>
    </form>

    <script>
        var seccionMoto = document.getElementById('seccion_moto');
        var checkMoto = document.getElementById('agregar_moto');
        function alternarMoto() {
            seccionMoto.hidden = !checkMoto.checked;
        }
        checkMoto.addEventListener('change', alternarMoto);
        alternarMoto();
    </script>
</body>
</html>