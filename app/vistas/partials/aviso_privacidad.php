<?php
/**
 * Aviso de privacidad simplificado (frontend, Sprint 1 · Tarea 9).
 *
 * El IPN, como sujeto obligado por la Ley General de Protección de Datos
 * Personales en Posesión de Sujetos Obligados (LGPDPPSO), debe informar qué
 * datos recaba y para qué antes de recabarlos. Se incluye una sola vez, en
 * el primer formulario del trámite (registro_usuario.php), porque ahí es
 * donde se recaban los primeros datos personales y fotografías.
 */
?>
<div class="aviso-privacidad">
    <h3 class="aviso-privacidad__titulo">Aviso de privacidad simplificado</h3>
    <p class="aviso-privacidad__texto">
        El Instituto Politécnico Nacional recaba tu nombre, identificador
        institucional, correo electrónico y las fotografías de tu credencial
        y de tu motocicleta únicamente para gestionar tu acceso en las
        entradas de ESCOM. Tus datos se resguardan conforme a la Ley General
        de Protección de Datos Personales en Posesión de Sujetos Obligados y
        no se comparten con terceros ajenos a este trámite.
    </p>
    <label class="aviso-privacidad__checkbox">
        <input type="checkbox" name="acepta_privacidad" id="acepta_privacidad" required>
        <span>He leído y acepto el aviso de privacidad</span>
    </label>
</div>
