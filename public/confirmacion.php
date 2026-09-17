<?php
session_start();
$titulo_pagina = 'SACAM · Confirmación de registro — ESCOM';
$paso_actual = 3;

// La confirmación solo aparece después de que el backend insertó usuario + moto
// (app/procesos/procesar_registro.php) y guardó aquí el folio de la operación.
$registro = $_SESSION['confirmacion'] ?? null;

require __DIR__ . '/../app/vistas/partials/header.php';

$tipos_persona = [
    'alumno'         => 'Alumno',
    'docente'        => 'Docente',
    'administrativo' => 'Administrativo',
    'intendencia'    => 'Intendencia',
    'otro'           => 'Otro',
];

$pendiente = ($registro['tipo_placa'] ?? '') === 'permiso_provisional';
?>
<section class="confirmacion">
    <svg class="confirmacion__icono" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true">
        <circle cx="24" cy="24" r="19"/>
        <path d="M15 24.5l6 6 12-13"/>
    </svg>

    <h1>Registro confirmado</h1>
    <p class="texto-guia" style="margin-inline:auto;">
        <?php if ($registro !== null): ?>
            Tu registro y el de tu motocicleta ya quedaron guardados en la base de datos.
        <?php else: ?>
            Aún no hay un registro que confirmar: sigue el trámite desde el
            <a href="index.php">inicio</a> para registrar tu motocicleta.
        <?php endif; ?>
    </p>

    <?php if ($registro !== null): ?>
    <p class="formulario-contexto">Folio de registro: <strong>#<?= (int) $registro['usuario_id'] ?></strong></p>
    <dl class="resumen">
        <div class="resumen__fila">
            <dt>Nombre</dt>
            <dd><?= htmlspecialchars($registro['nombre_completo']) ?></dd>
        </div>
        <div class="resumen__fila">
            <dt>Tipo de persona</dt>
            <dd><?= htmlspecialchars($tipos_persona[$registro['tipo_persona']] ?? '—') ?></dd>
        </div>
        <div class="resumen__fila">
            <dt>Identificador</dt>
            <dd><?= htmlspecialchars($registro['identificador']) ?></dd>
        </div>
        <div class="resumen__fila">
            <dt>Motocicleta</dt>
            <dd><?= htmlspecialchars(trim(($registro['marca'] ?? '') . ' ' . ($registro['modelo'] ?? ''))) ?>, <?= htmlspecialchars($registro['color'] ?? '') ?></dd>
        </div>
        <div class="resumen__fila">
            <dt>Placa</dt>
            <dd>
                <?= htmlspecialchars($registro['placa'] ?? '—') ?>
                <?php if ($pendiente): ?><span class="etiqueta etiqueta--pendiente">Pendiente de actualizar</span><?php endif; ?>
            </dd>
        </div>
    </dl>

    <?php // La confirmación se consume una sola vez: recargar no duplica el registro. ?>
    <?php unset($_SESSION['confirmacion']); ?>
    <?php endif; ?>

    <a class="boton boton--secundario" href="index.php">Volver al inicio</a>
</section>
<?php require __DIR__ . '/../app/vistas/partials/footer.php'; ?>