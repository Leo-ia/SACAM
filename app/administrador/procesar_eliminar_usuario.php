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

// El borrado de las motocicletas no se hace aquí de forma manual: la
// restricción FK fk_motocicletas_usuario (ON DELETE CASCADE) definida en
// database/sacam_bd_sprint1.sql borra las motos del usuario automáticamente.
$stmt = $pdo->prepare('DELETE FROM usuarios WHERE id = :id');
$stmt->execute([':id' => $id]);

header('Location: ' . RUTA_LISTADO);
exit;