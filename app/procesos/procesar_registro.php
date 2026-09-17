<?php
session_start();

require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/validaciones.php';

const RUTA_UPLOADS = __DIR__ . '/../../uploads';

$paso = $_POST['paso'] ?? '';

if ($paso === '1') {
    procesar_paso_usuario();
}

if ($paso === '2') {
    procesar_paso_motocicleta();
}

header('Location: registro_usuario.php');
exit;

function procesar_paso_usuario(): void
{
    $validacion = sacam_validar_usuario($_POST);
    $errores    = $validacion['errores'];

    if (empty($_POST['acepta_privacidad'])) {
        $errores['acepta_privacidad'] = 'Debes aceptar el aviso de privacidad para continuar.';
    }

    $foto_credencial = null;
    if ($errores === []) {
        try {
            $foto_credencial = sacam_validar_imagen($_FILES['foto_credencial'] ?? [], 'foto_credencial');
        } catch (UnexpectedValueException $e) {
            $errores['foto_credencial'] = $e->getMessage();
        }
    }

    if ($errores === []) {
        $pdo = sacam_conexion();
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
            $errores['correo_electronico'] =
                'Ya existe un registro con ese correo o identificador. Si el trámite es tuyo, '
                . 'contáctanos para actualizarlo.';
        }
    }

    if ($errores !== []) {
        volver_a('registro_usuario.php', $validacion['valores'], $errores);
    }

    $ruta_credencial = sacam_guardar_foto(
        $_FILES['foto_credencial'], 'credenciales', 'credencial', $foto_credencial['extension']
    );

    $_SESSION['registro'] = [
        'usuario' => $validacion['valores'] + ['foto_credencial' => $ruta_credencial],
        'moto'    => null,
    ];

    header('Location: registro_moto.php');
    exit;
}

function procesar_paso_motocicleta(): void
{
    $datos_usuario = $_SESSION['registro']['usuario'] ?? null;
    if ($datos_usuario === null) {
        header('Location: registro_usuario.php');
        exit;
    }

    $validacion = sacam_validar_motocicleta($_POST);
    $errores    = $validacion['errores'];

    $foto_motocicleta = null;
    if ($errores === []) {
        try {
            $foto_motocicleta = sacam_validar_imagen($_FILES['foto_motocicleta'] ?? [], 'foto_motocicleta');
        } catch (UnexpectedValueException $e) {
            $errores['foto_motocicleta'] = $e->getMessage();
        }
    }

    if ($errores !== []) {
        volver_a('registro_moto.php', $validacion['valores'], $errores);
    }

    $ruta_motocicleta = sacam_guardar_foto(
        $_FILES['foto_motocicleta'], 'motocicletas', 'moto', $foto_motocicleta['extension']
    );

    $pdo = sacam_conexion();

    try {
        $pdo->beginTransaction();

        $stmt_usuario = $pdo->prepare(
            'INSERT INTO usuarios
                 (tipo_persona, identificador_institucional, nombre_completo,
                  correo_electronico, foto_credencial, licencia_permiso)
             VALUES (:tipo_persona, :identificador, :nombre, :correo, :foto_credencial, :licencia)'
        );
        $stmt_usuario->execute([
            ':tipo_persona'     => $datos_usuario['tipo_persona'],
            ':identificador'    => $datos_usuario['identificador_institucional'],
            ':nombre'           => $datos_usuario['nombre_completo'],
            ':correo'           => $datos_usuario['correo_electronico'],
            ':foto_credencial'  => $datos_usuario['foto_credencial'],
            ':licencia'         => $datos_usuario['licencia_permiso'],
        ]);

        $usuario_id = (int) $pdo->lastInsertId();

        $stmt_moto = $pdo->prepare(
            'INSERT INTO motocicletas
                 (usuario_id, marca, modelo, color, placa, tipo_placa, estado, foto)
             VALUES (:usuario_id, :marca, :modelo, :color, :placa, :tipo_placa, :estado, :foto)'
        );
        $stmt_moto->execute([
            ':usuario_id' => $usuario_id,
            ':marca'      => $validacion['valores']['marca'],
            ':modelo'     => $validacion['valores']['modelo'],
            ':color'      => $validacion['valores']['color'],
            ':placa'      => $validacion['valores']['placa'],
            ':tipo_placa' => $validacion['valores']['tipo_placa'],
            ':estado'     => $validacion['valores']['estado'],
            ':foto'       => $ruta_motocicleta,
        ]);

        $pdo->commit();
    } catch (Throwable $e) {
        $pdo->rollBack();

        @unlink(__DIR__ . '/../../uploads/' . $datos_usuario['foto_credencial']);
        @unlink(__DIR__ . '/../../uploads/' . $ruta_motocicleta);

        $duplicado = strpos($e->getMessage(), 'Duplicate entry') !== false;
        $errores   = $duplicado
            ? ['correo_electronico' => 'Ya existe un registro con esos datos. Revisa el correo, '
                . 'la boleta o la placa e inténtalo de nuevo.']
            : ['general' => 'Ocurrió un error al guardar el registro. Inténtalo de nuevo más tarde.'];

        error_log('[SACAM] Error en registro (paso 2): ' . $e->getMessage());
        volver_a('registro_usuario.php', $datos_usuario, $errores);
    }

    $confirmacion = [
        'usuario_id'        => $usuario_id,
        'nombre_completo'   => $datos_usuario['nombre_completo'],
        'tipo_persona'      => $datos_usuario['tipo_persona'],
        'identificador'     => $datos_usuario['identificador_institucional'],
        'marca'             => $validacion['valores']['marca'],
        'modelo'            => $validacion['valores']['modelo'],
        'color'             => $validacion['valores']['color'],
        'placa'             => $validacion['valores']['placa'],
        'tipo_placa'        => $validacion['valores']['tipo_placa'],
    ];

    unset($_SESSION['registro']);
    $_SESSION['confirmacion'] = $confirmacion;

    header('Location: confirmacion.php');
    exit;
}

function sacam_guardar_foto(array $archivo, string $directorio, string $prefijo, string $extension): string
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

function volver_a(string $formulario, array $valores, array $errores): void
{
    $_SESSION['registro_errores'] = $errores;
    $_SESSION['registro_viejo']   = $valores;

    header('Location: ' . $formulario);
    exit;
}