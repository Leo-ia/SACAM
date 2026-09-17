<?php
/**
 * SACAM - Edición de motocicleta desde el panel de administrador.
 *
 * La foto es opcional al editar: si no se sube una nueva, se conserva la que
 * ya estaba. Si se sube una nueva, se valida, se guarda con nombre único y se
 * borra el archivo anterior para no dejar residuos en uploads/.
 */
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/validaciones.php';

const RUTA_LISTADO    = 'usuarios.php';
const RUTA_FORMULARIO = 'editar_moto.php';
const RUTA_DETALLE    = 'ver_usuario.php';
const RUTA_UPLOADS    = __DIR__ . '/../../uploads';

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
if ($id <= 0) {
    header('Location: ' . RUTA_LISTADO);
    exit;
}

$pdo = sacam_conexion();

$stmt = $pdo->prepare('SELECT * FROM motocicletas WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $id]);
$moto_actual = $stmt->fetch();

if (!$moto_actual) {
    header('Location: ' . RUTA_LISTADO);
    exit;
}

$validacion = sacam_validar_motocicleta($_POST);
$errores    = $validacion['errores'];

// La foto es opcional al editar: solo se valida si el admin subió una nueva.
$foto_nueva     = null;
$hay_foto_nueva = ($_FILES['foto_motocicleta']['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_NO_FILE;
if ($errores === [] && $hay_foto_nueva) {
    try {
        $foto_nueva = sacam_validar_imagen($_FILES['foto_motocicleta'], 'foto_motocicleta');
    } catch (UnexpectedValueException $e) {
        $errores['foto_motocicleta'] = $e->getMessage();
    }
}

if ($errores !== []) {
    $_SESSION['editar_moto_errores'] = $errores;
    $_SESSION['editar_moto_viejo']   = $validacion['valores'];
    header('Location: ' . RUTA_FORMULARIO . '?id=' . $id);
    exit;
}

$ruta_foto = $moto_actual['foto'];
if ($hay_foto_nueva) {
    $ruta_foto = sacam_admin_guardar_foto_moto(
        $_FILES['foto_motocicleta'], 'motocicletas', 'moto', $foto_nueva['extension']
    );
    // Ya se guardó la nueva; se borra la anterior para no dejar residuos.
    @unlink(RUTA_UPLOADS . '/' . $moto_actual['foto']);
}

$stmt = $pdo->prepare(
    'UPDATE motocicletas
        SET marca = :marca,
            modelo = :modelo,
            color = :color,
            placa = :placa,
            tipo_placa = :tipo_placa,
            estado = :estado,
            foto = :foto
      WHERE id = :id'
);
$stmt->execute([
    ':marca'      => $validacion['valores']['marca'],
    ':modelo'     => $validacion['valores']['modelo'],
    ':color'      => $validacion['valores']['color'],
    ':placa'      => $validacion['valores']['placa'],
    ':tipo_placa' => $validacion['valores']['tipo_placa'],
    ':estado'     => $validacion['valores']['estado'],
    ':foto'       => $ruta_foto,
    ':id'         => $id,
]);

header('Location: ' . RUTA_DETALLE . '?id=' . (int) $moto_actual['usuario_id']);
exit;

function sacam_admin_guardar_foto_moto(array $archivo, string $directorio, string $prefijo, string $extension): string
{
    $carpeta = RUTA_UPLOADS . '/' . $directorio;
    if (!is_dir($carpeta)) {
        mkdir($carpeta, 0755, true);
    }

    $nombre_unico = $prefijo . '_' . bin2hex(random_bytes(12)) . '.' . $extension;
    $destino      = $carpeta . '/' . $nombre_unico;

    if (!move_uploaded_file($archivo['tmp_name'], $destino)) {
        throw new RuntimeException('No se pudo guardar el archivo subido. Revisa los permisos de uploads/.');
    }

    return $directorio . '/' . $nombre_unico;
}
