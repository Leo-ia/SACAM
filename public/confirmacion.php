<?php
$titulo_pagina = 'SACAM · Confirmación de registro — ESCOM';
$paso_actual = 3;
require __DIR__ . '/../app/vistas/partials/header.php';

// Datos arrastrados por la URL desde los pasos 1 y 2, solo para previsualizar
// la pantalla final. Ver la nota técnica en registro_usuario.php y
// registro_moto.php: el backend real (tareas 6 y 8) sustituirá este arreglo
// por la confirmación de que el registro ya quedó guardado en la BD.
$datos = array_map('trim', $_GET);

$tipos_persona = [
    'alumno'         => 'Alumno',
    'docente'        => 'Docente',
    'administrativo' => 'Administrativo',
    'intendencia'    => 'Intendencia',
    'otro'           => 'Otro',
];

$pendiente = ($datos['tipo_placa'] ?? '') === 'permiso_provisional';
$hay_datos = !empty($datos['nombre_completo']);
?>
<section class="confirmacion">
    <svg class="confirmacion__icono" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
        <circle cx="24" cy="24" r="19"/>
        <path d="M15 24.5l6 6 12-13"/>
    </svg>

    <h1>Registro recibido</h1>
    <p class="texto-guia" style="margin-inline:auto;">
        <?php if ($hay_datos): ?>
            Esta pantalla es una vista previa del flujo del Sprint 1. Cuando el backend de
            las tareas 6 y 8 esté conectado, aquí se confirmará que tus datos y fotografías
            ya quedaron guardados en la base de datos.
        <?php else: ?>
            Aún no hay datos que mostrar: sigue el trámite desde el
            <a href="index.php">inicio</a> para ver esta pantalla con tu información.
        <?php endif; ?>
    </p>

    <?php if ($hay_datos): ?>
    <dl class="resumen">
        <div class="resumen__fila">
            <dt>Nombre</dt>
            <dd><?= htmlspecialchars($datos['nombre_completo']) ?></dd>
        </div>
        <div class="resumen__fila">
            <dt>Tipo de persona</dt>
            <dd><?= htmlspecialchars($tipos_persona[$datos['tipo_persona'] ?? ''] ?? '—') ?></dd>
        </div>
        <div class="resumen__fila">
            <dt>Identificador</dt>
            <dd><?= htmlspecialchars($datos['identificador_institucional'] ?? '—') ?></dd>
        </div>
        <div class="resumen__fila">
            <dt>Motocicleta</dt>
            <dd><?= htmlspecialchars(trim(($datos['marca'] ?? '') . ' ' . ($datos['modelo'] ?? ''))) ?>, <?= htmlspecialchars($datos['color'] ?? '') ?></dd>
        </div>
        <div class="resumen__fila">
            <dt>Placa</dt>
            <dd>
                <?= htmlspecialchars($datos['placa'] ?? '—') ?>
                <?php if ($pendiente): ?><span class="etiqueta etiqueta--pendiente">Pendiente de actualizar</span><?php endif; ?>
            </dd>
        </div>
    </dl>
    <?php endif; ?>

    <a class="boton boton--secundario" href="index.php">Volver al inicio</a>
</section>
<?php require __DIR__ . '/../app/vistas/partials/footer.php'; ?>
