<?php
session_start();

require_once __DIR__ . '/../includes/conexion.php';

const RUTA_LISTADO = 'usuarios.php';

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
$accion = $_POST['accion'] ?? '';

if ($id <= 0 || !in_array($accion, ['revocar', 'restaurar'], true)) {
    header('Location: ' . RUTA_LISTADO);
    exit;
}

$activo = $accion === 'restaurar' ? 1 : 0;

$pdo = sacam_conexion();

$stmt = $pdo->prepare('UPDATE usuarios SET activo = :activo WHERE id = :id');
$stmt->execute([':activo' => $activo, ':id' => $id]);

header('Location: ' . RUTA_LISTADO);
exit;