<?php
/**
 * SACAM - Puerta de entrada pública para editar una motocicleta desde el admin.
 *
 * El documento raíz del servidor es `public/`, por lo que NADA de `app/` puede
 * pedirse por URL. Este archivo solo delega la lógica real a
 * app/administrador/procesar_editar_moto.php.
 */
require __DIR__ . '/../../app/administrador/requiere_login.php';
require __DIR__ . '/../../app/administrador/procesar_editar_moto.php';
