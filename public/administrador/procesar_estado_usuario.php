<?php
/**
 * SACAM — Puerta de entrada pública del backend para revocar/restaurar acceso.
 *
 * El documento raíz del servidor es `public/`, por lo que NADA de `app/` puede
 * pedirse por URL. Este archivo solo delega la lógica real a
 * app/administrador/procesar_estado_usuario.php.
 */
require __DIR__ . '/../../app/administrador/procesar_estado_usuario.php';