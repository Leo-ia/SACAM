<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

require_once __DIR__ . '/../includes/conexion.php';

const RUTA_LISTADO = 'usuarios.php';

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
if ($id <= 0) {
    header('Location: ' . RUTA_LISTADO);
    exit;
}

$pdo = sacam_conexion();

$stmt = $pdo->prepare('DELETE FROM usuarios WHERE id = :id');
$stmt->execute([':id' => $id]);

header('Location: ' . RUTA_LISTADO);
exit;