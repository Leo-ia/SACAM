<?php
session_start();
$titulo_pagina = 'SACAM · Registro de motocicleta — ESCOM';
$paso_actual = 2;

// El paso 2 solo tiene sentido después de completar el paso 1: los datos
// personales viajan en sesión (nunca por la URL) y aquí solo se muestran.
$datos_usuario = $_SESSION['registro']['usuario'] ?? null;
if ($datos_usuario === null) {
    header('Location: registro_usuario.php');
    exit;
}

// Errores y valores previos del paso 2 (flash del backend).
$errores = $_SESSION['registro_errores'] ?? [];
$viejo   = $_SESSION['registro_viejo']   ?? [];
unset($_SESSION['registro_errores'], $_SESSION['registro_viejo']);

require __DIR__ . '/../app/vistas/partials/header.php';
?>
<section class="formulario-seccion">
    <h1>Datos de la motocicleta</h1>
    <p class="formulario-contexto">
        Registrando la motocicleta de <strong><?= htmlspecialchars($datos_usuario['nombre_completo']) ?></strong>
        (<?= htmlspecialchars($datos_usuario['tipo_persona']) ?> · <?= htmlspecialchars($datos_usuario['identificador_institucional']) ?>).
    </p>
    <p class="texto-guia">
        La motocicleta queda asociada a la persona registrada en el paso anterior; no se
        puede registrar una moto sin un usuario ya guardado.
    </p>

    <?php if (!empty($errores['general'])): ?>
        <p class="alerta alerta--error"><?= htmlspecialchars($errores['general']) ?></p>
    <?php endif; ?>

    <form class="formulario js-validar" method="post" enctype="multipart/form-data" action="procesar_registro.php" novalidate>
        <input type="hidden" name="paso" value="2">

        <div class="campo">
            <label for="marca">Marca</label>
            <input type="text" id="marca" name="marca" required maxlength="50" value="<?= htmlspecialchars($viejo['marca'] ?? '') ?>">
            <p class="campo__error" data-error-para="marca"><?= htmlspecialchars($errores['marca'] ?? '') ?></p>
        </div>

        <div class="campo">
            <label for="modelo">Modelo</label>
            <input type="text" id="modelo" name="modelo" required maxlength="50" value="<?= htmlspecialchars($viejo['modelo'] ?? '') ?>">
            <p class="campo__error" data-error-para="modelo"><?= htmlspecialchars($errores['modelo'] ?? '') ?></p>
        </div>

        <div class="campo">
            <label for="color">Color</label>
            <input type="text" id="color" name="color" required maxlength="50" value="<?= htmlspecialchars($viejo['color'] ?? '') ?>">
            <p class="campo__error" data-error-para="color"><?= htmlspecialchars($errores['color'] ?? '') ?></p>
        </div>

        <div class="campo">
            <span class="campo__etiqueta-grupo">¿Tu motocicleta ya tiene placa?</span>
            <div class="campo__opciones">
                <label class="campo__opcion">
                    <input type="radio" name="tipo_placa" value="con_placa" <?= ($viejo['tipo_placa'] ?? 'con_placa') !== 'permiso_provisional' ? 'checked' : '' ?>>
                    <span>Sí, tiene placa definitiva</span>
                </label>
                <label class="campo__opcion">
                    <input type="radio" name="tipo_placa" value="permiso_provisional" <?= ($viejo['tipo_placa'] ?? '') === 'permiso_provisional' ? 'checked' : '' ?>>
                    <span>No, tengo permiso provisional</span>
                </label>
            </div>
            <p class="campo__error" data-error-para="tipo_placa"><?= htmlspecialchars($errores['tipo_placa'] ?? '') ?></p>
        </div>

        <div class="campo">
            <label for="placa" id="etiqueta_placa">Placa</label>
            <input type="text" id="placa" name="placa" required maxlength="20" value="<?= htmlspecialchars($viejo['placa'] ?? '') ?>">
            <p class="campo__ayuda" id="ayuda_placa">Captura la placa tal como aparece en la tarjeta de circulación.</p>
            <p class="campo__error" data-error-para="placa"><?= htmlspecialchars($errores['placa'] ?? '') ?></p>
        </div>

        <div class="campo">
            <label for="foto_motocicleta">Foto de la motocicleta</label>
            <p class="campo__ayuda">Formato JPG o PNG, máximo 5 MB.</p>
            <input type="file" id="foto_motocicleta" name="foto_motocicleta" accept="image/jpeg,image/png" required>
            <p class="campo__error" data-error-para="foto_motocicleta"><?= htmlspecialchars($errores['foto_motocicleta'] ?? '') ?></p>
        </div>

        <div class="formulario__acciones formulario__acciones--dos">
            <a class="boton boton--secundario" href="registro_usuario.php">Regresar</a>
            <button type="submit" class="boton boton--primario">Finalizar registro</button>
        </div>
    </form>
</section>
<?php require __DIR__ . '/../app/vistas/partials/footer.php'; ?>