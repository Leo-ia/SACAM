<?php
$titulo_pagina = 'SACAM · Registro de motocicleta — ESCOM';
$paso_actual = 2;
require __DIR__ . '/../app/vistas/partials/header.php';

// Dato que llega del paso 1 solo para mostrarlo en pantalla durante la demo
// (ver nota técnica más abajo). El backend real (tarea 8) asociará la moto
// al usuario mediante sesión de PHP y la relación usuario_id, no por la URL.
$nombre_usuario = trim($_GET['nombre_completo'] ?? '');
?>
<section class="formulario-seccion">
    <h1>Datos de la motocicleta</h1>
    <?php if ($nombre_usuario !== ''): ?>
        <p class="formulario-contexto">Registrando la motocicleta de <strong><?= htmlspecialchars($nombre_usuario) ?></strong>.</p>
    <?php endif; ?>
    <p class="texto-guia">
        La motocicleta queda asociada a la persona registrada en el paso anterior; no se
        puede registrar una moto sin un usuario ya guardado.
    </p>

    <!--
        NOTA TÉCNICA (frontend, Sprint 1):
        Igual que en registro_usuario.php, este formulario reenvía por GET los
        campos del paso 1 (recibidos como hidden) más los propios de la moto,
        únicamente para poder mostrar confirmacion.php con datos reales en la
        demo. Nada se guarda en base de datos todavía: eso corresponde a la
        tarea 8 (procesar_moto.php), que deberá usar method="post" y una
        transacción SQL para cumplir la HU-04 (todo o nada).
    -->
    <form class="formulario js-validar" method="get" action="confirmacion.php" novalidate>
        <?php foreach ($_GET as $campo => $valor): ?>
            <input type="hidden" name="<?= htmlspecialchars($campo) ?>" value="<?= htmlspecialchars($valor) ?>">
        <?php endforeach; ?>

        <div class="campo">
            <label for="marca">Marca</label>
            <input type="text" id="marca" name="marca" required maxlength="50">
            <p class="campo__error" data-error-para="marca"></p>
        </div>

        <div class="campo">
            <label for="modelo">Modelo</label>
            <input type="text" id="modelo" name="modelo" required maxlength="50">
            <p class="campo__error" data-error-para="modelo"></p>
        </div>

        <div class="campo">
            <label for="color">Color</label>
            <input type="text" id="color" name="color" required maxlength="50">
            <p class="campo__error" data-error-para="color"></p>
        </div>

        <div class="campo">
            <span class="campo__etiqueta-grupo">¿Tu motocicleta ya tiene placa?</span>
            <div class="campo__opciones">
                <label class="campo__opcion">
                    <input type="radio" name="tipo_placa" value="con_placa" checked>
                    <span>Sí, tiene placa definitiva</span>
                </label>
                <label class="campo__opcion">
                    <input type="radio" name="tipo_placa" value="permiso_provisional">
                    <span>No, tengo permiso provisional</span>
                </label>
            </div>
        </div>

        <div class="campo">
            <label for="placa" id="etiqueta_placa">Placa</label>
            <input type="text" id="placa" name="placa" required maxlength="20">
            <p class="campo__ayuda" id="ayuda_placa">Captura la placa tal como aparece en la tarjeta de circulación.</p>
            <p class="campo__error" data-error-para="placa"></p>
        </div>

        <div class="campo">
            <label for="foto_motocicleta">Foto de la motocicleta</label>
            <p class="campo__ayuda">Formato JPG o PNG, máximo 5 MB.</p>
            <input type="file" id="foto_motocicleta" name="foto_motocicleta" accept="image/jpeg,image/png" required>
            <p class="campo__error" data-error-para="foto_motocicleta"></p>
        </div>

        <div class="formulario__acciones formulario__acciones--dos">
            <a class="boton boton--secundario" href="registro_usuario.php">Regresar</a>
            <button type="submit" class="boton boton--primario">Finalizar registro</button>
        </div>
    </form>
</section>
<?php require __DIR__ . '/../app/vistas/partials/footer.php'; ?>
