<?php
$titulo_pagina = 'SACAM · Registro de usuario — ESCOM';
$paso_actual = 1;
require __DIR__ . '/../app/vistas/partials/header.php';
?>
<section class="formulario-seccion">
    <h1>Datos personales</h1>
    <p class="texto-guia">
        Estos datos identifican al dueño de la motocicleta. Todos los campos son
        obligatorios, salvo donde se indique.
    </p>

    <!--
        NOTA TÉCNICA (frontend, Sprint 1):
        Este formulario todavía no tiene backend (tarea 6, procesar_usuario.php,
        pendiente en el backlog). Para poder mostrar el flujo visual completo
        (usuario → moto → confirmación) durante la demo del jueves, se usa
        method="get" hacia registro_moto.php: NO se guarda nada, solo se
        arrastran los datos en la URL para previsualizar la siguiente pantalla.

        Cuando exista el backend, cambiar a:
            method="post" action=""
        para que esta misma página reciba el $_POST y llame a
        app/procesos/procesar_usuario.php (que hará el guardado real y la
        transacción usuario+moto de la HU-04).
    -->
    <form class="formulario js-validar" method="get" action="registro_moto.php" novalidate>
        <div class="campo">
            <label for="tipo_persona">Tipo de persona</label>
            <select id="tipo_persona" name="tipo_persona" required>
                <option value="" disabled selected>Selecciona una opción</option>
                <option value="alumno">Alumno</option>
                <option value="docente">Docente</option>
                <option value="administrativo">Administrativo</option>
                <option value="intendencia">Intendencia</option>
                <option value="otro">Otro</option>
            </select>
            <p class="campo__error" data-error-para="tipo_persona"></p>
        </div>

        <div class="campo">
            <label for="identificador_institucional" id="etiqueta_identificador">Boleta o número de empleado</label>
            <input type="text" id="identificador_institucional" name="identificador_institucional" required maxlength="20">
            <p class="campo__ayuda" id="ayuda_identificador">Selecciona primero tu tipo de persona.</p>
            <p class="campo__error" data-error-para="identificador_institucional"></p>
        </div>

        <div class="campo">
            <label for="nombre_completo">Nombre completo</label>
            <input type="text" id="nombre_completo" name="nombre_completo" required maxlength="120">
            <p class="campo__error" data-error-para="nombre_completo"></p>
        </div>

        <div class="campo">
            <label for="correo_electronico">Correo electrónico</label>
            <input type="email" id="correo_electronico" name="correo_electronico" required maxlength="190">
            <p class="campo__ayuda">Puede ser cualquier correo; no es necesario que sea institucional.</p>
            <p class="campo__error" data-error-para="correo_electronico"></p>
        </div>

        <div class="campo">
            <label for="foto_credencial">Foto de tu credencial IPN</label>
            <p class="campo__ayuda">Formato JPG o PNG, máximo 5 MB.</p>
            <input type="file" id="foto_credencial" name="foto_credencial" accept="image/jpeg,image/png" required>
            <p class="campo__error" data-error-para="foto_credencial"></p>
        </div>

        <div class="campo">
            <label for="licencia_permiso">Licencia o permiso de conducir <span class="campo__opcional">(opcional)</span></label>
            <input type="text" id="licencia_permiso" name="licencia_permiso" maxlength="30">
            <p class="campo__ayuda">Dato solo informativo: no se valida su vigencia ni autenticidad.</p>
        </div>

        <?php require __DIR__ . '/../app/vistas/partials/aviso_privacidad.php'; ?>

        <div class="formulario__acciones">
            <button type="submit" class="boton boton--primario">Continuar</button>
        </div>
    </form>
</section>
<?php require __DIR__ . '/../app/vistas/partials/footer.php'; ?>
