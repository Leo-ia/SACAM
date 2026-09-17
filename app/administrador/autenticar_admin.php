<?php
session_start();

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