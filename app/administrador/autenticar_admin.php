<?php
/**
 * SACAM - Login del administrador (validacion por coincidencia en PHP).
 *
 * De proposito NO usa la base de datos ni una tabla de cuentas de admin: las
 * credenciales estan fijas en el codigo para este avance minimo. En Sprint 2
 * el esquema ya tiene prevista la tabla `cuentas_acceso` con password_hash.
 */
session_start();

// Credenciales fijas del avance minimo. Comparacion en tiempo constante.
$usuario_correcto = 'admin';
$clave_correcta = '1234';

$usuario = $_POST['usuario'] ?? '';
$clave   = $_POST['clave']   ?? '';

if (hash_equals($usuario_correcto, $usuario) && hash_equals($clave_correcta, $clave)) {
    $_SESSION['admin_autenticado'] = true;
    header('Location: usuarios.php');
    exit;
}

$_SESSION['login_error'] = 'Usuario o contraseña incorrectos.';
header('Location: login.php');
exit;