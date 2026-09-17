<?php
/**
 * SACAM - Verificacion de sesion para el perfil de administrador.
 *
 * Este archivo no se puede invocar por URL (vive en app/). Las paginas y los
 * procesadores de administrador lo incluyen al inicio para bloquear el acceso
 * a quien no haya iniciado sesion.
 */
session_start();

if (empty($_SESSION['admin_autenticado'])) {
    header('Location: login.php');
    exit;
}