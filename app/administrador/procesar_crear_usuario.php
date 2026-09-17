<?php
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/validaciones.php';

const RUTA_LISTADO    = 'usuarios.php';
const RUTA_FORMULARIO = 'nuevo_usuario.php';
const RUTA_DETALLE    = 'ver_usuario.php';
const RUTA_UPLOADS    = __DIR__ . '/../../uploads';

$validacion      = sacam_validar_usuario($_POST);
$validacion_moto = sacam_validar_motocicleta($_POST);
$agregar_moto    = ($_POST['agregar_moto'] ?? '') === '1';
$errores         = $validacion['errores'];
$errores_moto    = $agregar_moto ? $validacion_moto['errores'] : [];

$foto_credencial = null;
if ($errores === []) {
    try {
        $foto_credencial = sacam_validar_imagen($_FILES['foto_credencial'] ?? [], 'foto_credencial');
    } catch (UnexpectedValueException $e) {
        $errores['foto_credencial'] = $e->getMessage();
    }
}

$foto_moto = null;
if ($agregar_moto && $errores_moto === []) {
    try {
        $foto_moto = sacam_validar_imagen($_FILES['foto_motocicleta'] ?? [], 'foto_motocicleta');
    } catch (UnexpectedValueException $e) {
        $errores_moto['foto_motocicleta'] = $e->getMessage();
    }
}

$pdo = sacam_conexion();

if ($errores === []) {
    $stmt = $pdo->prepare(
        'SELECT 1 FROM usuarios
          WHERE correo_electronico = :correo
             OR (tipo_persona = :tipo AND identificador_institucional = :identificador)
         LIMIT 1'
    );
    $stmt->execute([
        ':correo'        => $validacion['valores']['correo_electronico'],
        ':tipo'          => $validacion['valores']['tipo_persona'],
        ':identificador' => $validacion['valores']['identificador_institucional'],
    ]);
    if ($stmt->fetch()) {
        $errores['correo_electronico'] = 'Ya existe un usuario con ese correo o identificador.';
    }
}

$errores = array_merge($errores, $errores_moto);

if ($errores !== []) {
    $_SESSION['nuevo_errores'] = $errores;
    $_SESSION['nuevo_viejo']   = $validacion['valores'];
    $_SESSION['nuevo_moto']    = array_merge($validacion_moto['valores'], ['agregar_moto' => $agregar_moto]);
    header('Location: ' . RUTA_FORMULARIO);
    exit;
}

$ruta_credencial = sacam_admin_guardar_foto(
    $_FILES['foto_credencial'], 'credenciales', 'credencial', $foto_credencial['extension']
);

try {
    $pdo->beginTransaction();

    $stmt = $pdo->prepare(
        'INSERT INTO usuarios
             (tipo_persona, identificador_institucional, nombre_completo,
              correo_electronico, foto_credencial, licencia_permiso)
         VALUES (:tipo_persona, :identificador, :nombre, :correo, :foto_credencial, :licencia)'
    );
    $stmt->execute([
        ':tipo_persona'    => $validacion['valores']['tipo_persona'],
        ':identificador'   => $validacion['valores']['identificador_institucional'],
        ':nombre'          => $validacion['valores']['nombre_completo'],
        ':correo'          => $validacion['valores']['correo_electronico'],
        ':foto_credencial' => $ruta_credencial,
        ':licencia'        => $validacion['valores']['licencia_permiso'],
    ]);
    $usuario_id = (int) $pdo->lastInsertId();

    if ($agregar_moto) {
        $ruta_foto_moto = sacam_admin_guardar_foto(
            $_FILES['foto_motocicleta'], 'motocicletas', 'moto', $foto_moto['extension']
        );
        $stmt = $pdo->prepare(
            'INSERT INTO motocicletas
                 (usuario_id, marca, modelo, color, placa, tipo_placa, estado, foto)
             VALUES (:usuario_id, :marca, :modelo, :color, :placa, :tipo_placa, :estado, :foto)'
        );
        $stmt->execute([
            ':usuario_id' => $usuario_id,
            ':marca'      => $validacion_moto['valores']['marca'],
            ':modelo'     => $validacion_moto['valores']['modelo'],
            ':color'      => $validacion_moto['valores']['color'],
            ':placa'      => $validacion_moto['valores']['placa'],
            ':tipo_placa' => $validacion_moto['valores']['tipo_placa'],
            ':estado'     => $validacion_moto['valores']['estado'],
            ':foto'       => $ruta_foto_moto,
        ]);
    }

    $pdo->commit();
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    $_SESSION['nuevo_errores'] = ['general' => 'No se pudo guardar el registro. Intenta de nuevo.'];
    $_SESSION['nuevo_viejo']   = $validacion['valores'];
    $_SESSION['nuevo_moto']    = array_merge($validacion_moto['valores'], ['agregar_moto' => $agregar_moto]);
    header('Location: ' . RUTA_FORMULARIO);
    exit;
}

header('Location: ' . RUTA_DETALLE . '?id=' . $usuario_id);
exit;

function sacam_admin_guardar_foto(array $archivo, string $directorio, string $prefijo, string $extension): string
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