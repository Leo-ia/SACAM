<?php
/**
 * Encabezado institucional reutilizable (frontend, Sprint 1).
 *
 * Variables opcionales que la página debe definir ANTES de incluir este
 * archivo:
 *   $titulo_pagina  (string) Título mostrado en la pestaña del navegador.
 *   $paso_actual    (int)    1, 2 o 3 cuando la página forma parte del
 *                             trámite de registro (registro_usuario.php,
 *                             registro_moto.php, confirmacion.php). Se omite
 *                             en páginas que no son parte del flujo, como
 *                             index.php.
 */
$titulo_pagina = $titulo_pagina ?? 'SACAM · ESCOM, IPN';
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars($titulo_pagina) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="css/estilos.css">
</head>
<body>
<a class="salto-contenido" href="#contenido">Saltar al contenido</a>

<header class="cabecera">
    <div class="cabecera__franja"></div>
    <div class="cabecera__contenido contenedor">
        <div class="cabecera__institucion">
            <span class="cabecera__ipn">Instituto Politécnico Nacional</span>
            <span class="cabecera__escom">Escuela Superior de Cómputo</span>
        </div>
        <a class="cabecera__sistema" href="index.php">SACAM</a>
    </div>
</header>

<?php if (!empty($paso_actual)): ?>
<nav class="pasos contenedor" aria-label="Progreso del registro">
    <ol class="pasos__lista">
        <li class="pasos__item<?= $paso_actual >= 1 ? ' pasos__item--activo' : '' ?><?= $paso_actual > 1 ? ' pasos__item--hecho' : '' ?>">
            <span class="pasos__numero">1</span>
            <span class="pasos__etiqueta">Datos personales</span>
        </li>
        <li class="pasos__item<?= $paso_actual >= 2 ? ' pasos__item--activo' : '' ?><?= $paso_actual > 2 ? ' pasos__item--hecho' : '' ?>">
            <span class="pasos__numero">2</span>
            <span class="pasos__etiqueta">Motocicleta</span>
        </li>
        <li class="pasos__item<?= $paso_actual >= 3 ? ' pasos__item--activo' : '' ?>">
            <span class="pasos__numero">3</span>
            <span class="pasos__etiqueta">Confirmación</span>
        </li>
    </ol>
</nav>
<?php endif; ?>

<main id="contenido" class="contenedor">
