<?php
session_start();
$titulo_pagina = 'SACAM · Registro de usuario — ESCOM';
$paso_actual = 1;

// Errores y valores previos: vienen como mensaje flash desde el backend
// (app/procesos/procesar_registro.php) cuando una validación falló. Se consumen
// una sola vez para que un error no se repita al recargar la página.
$errores = $_SESSION['registro_errores'] ?? [];
$viejo   = $_SESSION['registro_viejo']   ?? [];
unset($_SESSION['registro_errores'], $_SESSION['registro_viejo']);

require __DIR__ . '/../app/vistas/partials/header.php';
?>
<section class="formulario-seccion">
    <h1>Datos personales</h1>
    <p class="texto-guia">
        Estos datos identifican al dueño de la motocicleta. Todos los campos son
        obligatorios, salvo donde se indique.
    </p>

    <?php if (!empty($errores['general'])): ?>
        <p class="alerta alerta--error"><?= htmlspecialchars($errores['general']) ?></p>
    <?php endif; ?>

    <!--
        El formulario también puede regresarse a esta misma pantalla tras una
        validación fallida del servidor: los errores se pintan junto a cada
        campo (igual que en el cliente) y los valores capturados se conservan.
    -->
    <form class="formulario js-validar" method="post" enctype="multipart/form-data" action="procesar_registro.php" novalidate>
        <input type="hidden" name="paso" value="1">

        <div class="campo">
            <label for="tipo_persona">Tipo de persona</label>
            <select id="tipo_persona" name="tipo_persona" required>
                <option value="" disabled <?= empty($viejo['tipo_persona']) ? 'selected' : '' ?>>Selecciona una opción</option>
                <?php foreach (['alumno', 'docente', 'administrativo', 'intendencia', 'otro'] as $opcion): ?>
                    <option value="<?= $opcion ?>" <?= ($viejo['tipo_persona'] ?? '') === $opcion ? 'selected' : '' ?>>
                        <?= ucfirst($opcion) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <p class="campo__error" data-error-para="tipo_persona"><?= htmlspecialchars($errores['tipo_persona'] ?? '') ?></p>
        </div>

        <div class="campo">
            <label for="identificador_institucional" id="etiqueta_identificador">Boleta o número de empleado</label>
            <input type="text" id="identificador_institucional" name="identificador_institucional" required maxlength="20" value="<?= htmlspecialchars($viejo['identificador_institucional'] ?? '') ?>">
            <p class="campo__ayuda" id="ayuda_identificador">Selecciona primero tu tipo de persona.</p>
            <p class="campo__error" data-error-para="identificador_institucional"><?= htmlspecialchars($errores['identificador_institucional'] ?? '') ?></p>
        </div>

        <div class="campo">
            <label for="nombre_completo">Nombre completo</label>
            <input type="text" id="nombre_completo" name="nombre_completo" required maxlength="120" value="<?= htmlspecialchars($viejo['nombre_completo'] ?? '') ?>">
            <p class="campo__error" data-error-para="nombre_completo"><?= htmlspecialchars($errores['nombre_completo'] ?? '') ?></p>
        </div>

        <div class="campo">
            <label for="correo_electronico">Correo electrónico</label>
            <input type="email" id="correo_electronico" name="correo_electronico" required maxlength="190" value="<?= htmlspecialchars($viejo['correo_electronico'] ?? '') ?>">
            <p class="campo__ayuda">Puede ser cualquier correo; no es necesario que sea institucional.</p>
            <p class="campo__error" data-error-para="correo_electronico"><?= htmlspecialchars($errores['correo_electronico'] ?? '') ?></p>
        </div>

        <div class="campo">
            <label for="foto_credencial">Foto de tu credencial IPN</label>
            <p class="campo__ayuda">Formato JPG o PNG, máximo 5 MB.</p>
            <input type="file" id="foto_credencial" name="foto_credencial" accept="image/jpeg,image/png" required>
            <p class="campo__error" data-error-para="foto_credencial"><?= htmlspecialchars($errores['foto_credencial'] ?? '') ?></p>
        </div>

        <div class="campo">
            <label for="licencia_permiso">Licencia o permiso de conducir <span class="campo__opcional">(opcional)</span></label>
            <input type="text" id="licencia_permiso" name="licencia_permiso" maxlength="30" value="<?= htmlspecialchars($viejo['licencia_permiso'] ?? '') ?>">
            <p class="campo__ayuda">Dato solo informativo: no se valida su vigencia ni autenticidad.</p>
            <p class="campo__error" data-error-para="licencia_permiso"><?= htmlspecialchars($errores['licencia_permiso'] ?? '') ?></p>
        </div>

        <?php require __DIR__ . '/../app/vistas/partials/aviso_privacidad.php'; ?>
        <?php if (!empty($errores['acepta_privacidad'])): ?>
            <p class="campo__error"><?= htmlspecialchars($errores['acepta_privacidad'] ?? '') ?></p>
        <?php endif; ?>

        <div class="formulario__acciones">
            <button type="submit" class="boton boton--primario">Continuar</button>
        </div>
    </form>
</section>
<?php require __DIR__ . '/../app/vistas/partials/footer.php'; ?>