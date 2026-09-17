<?php
/**
 * SACAM - Cierre de sesion del administrador.
 */
session_start();

unset($_SESSION['admin_autenticado']);
session_destroy();

header('Location: login.php');
exit;