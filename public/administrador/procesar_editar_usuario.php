<?php
/**
 * SACAM — Puerta de entrada pública del backend para editar un usuario.
 *
 * El documento raíz del servidor es `public/`, por lo que NADA de `app/` puede
 * pedirse por URL. Este archivo solo delega la lógica real a
 * app/administrador/procesar_editar_usuario.php.
 */
require __DIR__ . '/../../app/administrador/procesar_editar_usuario.php';