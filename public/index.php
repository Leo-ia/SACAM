<?php
$titulo_pagina = 'SACAM · Registro de acceso para motocicletas — ESCOM';
require __DIR__ . '/../app/vistas/partials/header.php';
?>
<section class="inicio-hero">
    <h1>Registra tu motocicleta para entrar a ESCOM</h1>
    <p class="texto-guia">
        SACAM sustituye el registro manual por fotografía en los dos accesos vehiculares
        de la escuela. Completas el trámite una sola vez: guardamos tus datos y los de tu
        motocicleta para que el guardia en turno pueda verificarte al instante en la entrada.
    </p>
    <a class="boton boton--primario" href="registro_usuario.php">Iniciar mi registro</a>
</section>

<section class="inicio-requisitos">
    <h2>Antes de empezar, ten a la mano</h2>
    <ul class="lista-requisitos">
        <li class="lista-requisitos__item">
            <svg class="icono" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                <rect x="6" y="11" width="36" height="26" rx="3"/>
                <circle cx="17" cy="21.5" r="4.5"/>
                <path d="M10.5 32c1.5-4 4-6 6.5-6s5 2 6.5 6"/>
                <line x1="29" y1="18" x2="37" y2="18"/>
                <line x1="29" y1="23" x2="37" y2="23"/>
                <line x1="29" y1="28" x2="34" y2="28"/>
            </svg>
            <div>
                <p class="lista-requisitos__titulo">Tu credencial vigente del IPN</p>
                <p class="lista-requisitos__detalle">Una fotografía legible, en formato JPG o PNG (máximo 5 MB).</p>
            </div>
        </li>
        <li class="lista-requisitos__item">
            <svg class="icono" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                <circle cx="13" cy="33" r="5.5"/>
                <circle cx="35" cy="33" r="5.5"/>
                <path d="M13 33 20 20h9l4 6h4"/>
                <path d="M20 20l-4-6"/>
                <path d="M29 26l-3 7"/>
            </svg>
            <div>
                <p class="lista-requisitos__titulo">Los datos de tu motocicleta</p>
                <p class="lista-requisitos__detalle">Marca, modelo, color y placa (o el número de tu permiso provisional, si aún no la tienes).</p>
            </div>
        </li>
        <li class="lista-requisitos__item">
            <svg class="icono" viewBox="0 0 48 48" fill="none" stroke="currentColor" stroke-width="1.6" aria-hidden="true">
                <rect x="6" y="15" width="36" height="22" rx="3"/>
                <path d="M17 15l2.5-4h9l2.5 4"/>
                <circle cx="24" cy="26" r="7"/>
            </svg>
            <div>
                <p class="lista-requisitos__titulo">Una fotografía de tu motocicleta</p>
                <p class="lista-requisitos__detalle">Formato JPG o PNG (máximo 5 MB), de frente o de tres cuartos.</p>
            </div>
        </li>
    </ul>
</section>

<section class="inicio-quien">
    <h2>¿Quién puede registrarse?</h2>
    <p class="texto-guia">
        Cualquier integrante de la comunidad politécnica que ingrese en motocicleta:
        alumnado, docentes, personal administrativo y de intendencia. No necesitas una
        cuenta previa para empezar.
    </p>
</section>

<?php require __DIR__ . '/../app/vistas/partials/footer.php'; ?>
