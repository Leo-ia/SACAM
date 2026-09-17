<?php
/**
 * SACAM — Puerta de entrada pública del backend para eliminar un usuario.
 *
 * El documento raíz del servidor es `public/`, por lo que NADA de `app/` puede
 * pedirse por URL. Este archivo solo delega la lógica real a
 * app/administrador/procesar_eliminar_usuario.php.
 */
require __DIR__ . '/../../app/administrador/requiere_login.php';
require __DIR__ . '/../../app/administrador/procesar_eliminar_usuario.php';