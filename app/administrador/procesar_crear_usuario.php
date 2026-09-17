<?php
/**
 * SACAM - Alta de usuario desde el panel de administrador.
 *
 * A diferencia del alta pública (app/procesos/procesar_registro.php), aquí el
 * administrador da de alta a la persona directamente, sin pasar por sesión de
 * trámite en dos pasos ni registrar una motocicleta: solo crea la cuenta.
 */
if (session_status() !== PHP_SESSION_ACTIVE) session_start();

require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/validaciones.php';

const RUTA_LISTADO    = 'usuarios.php';
const RUTA_FORMULARIO = 'nuevo_usuario.php';
const RUTA_DETALLE    = 'ver_usuario.php';
const RUTA_UPLOADS    = __DIR__ . '/../../uploads';

$validacion = sacam_validar_usuario($_POST);
$errores    = $validacion['errores'];

$foto_credencial = null;
if ($errores === []) {
    try {
        $foto_credencial = sacam_validar_imagen($_FILES['foto_credencial'] ?? [], 'foto_credencial');
    } catch (UnexpectedValueException $e) {
        $errores['foto_credencial'] = $e->getMessage();
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

if ($errores !== []) {
    $_SESSION['nuevo_errores'] = $errores;
    $_SESSION['nuevo_viejo']   = $validacion['valores'];
    header('Location: ' . RUTA_FORMULARIO);
    exit;
}

$ruta_credencial = sacam_admin_guardar_foto(
    $_FILES['foto_credencial'], 'credenciales', 'credencial', $foto_credencial['extension']
);

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

header('Location: ' . RUTA_DETALLE . '?id=' . $pdo->lastInsertId());
exit;

/**
 * Guarda la foto de credencial subida por el administrador con nombre único.
 */
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
