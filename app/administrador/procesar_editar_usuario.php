<?php
session_start();

require_once __DIR__ . '/../includes/conexion.php';
require_once __DIR__ . '/../includes/validaciones.php';

const RUTA_LISTADO       = 'usuarios.php';
const RUTA_FORMULARIO    = 'editar_usuario.php';
const RUTA_DETALLE       = 'ver_usuario.php';

$id = isset($_POST['id']) ? (int) $_POST['id'] : 0;
if ($id <= 0) {
    header('Location: ' . RUTA_LISTADO);
    exit;
}

$pdo = sacam_conexion();

$stmt = $pdo->prepare('SELECT id FROM usuarios WHERE id = :id LIMIT 1');
$stmt->execute([':id' => $id]);
if (!$stmt->fetch()) {
    header('Location: ' . RUTA_LISTADO);
    exit;
}

// Se reutilizan las validaciones de app/includes/validaciones.php (HU-02).
$validacion = sacam_validar_usuario($_POST);
$errores    = $validacion['errores'];

// Duplicados: el correo/identificador debe seguir siendo único, pero
// excluir al propio usuario que se está editando.
if ($errores === []) {
    $stmt = $pdo->prepare(
        'SELECT id FROM usuarios
          WHERE (correo_electronico = :correo
               OR (tipo_persona = :tipo AND identificador_institucional = :identificador))
            AND id <> :id
          LIMIT 1'
    );
    $stmt->execute([
        ':correo'        => $validacion['valores']['correo_electronico'],
        ':tipo'          => $validacion['valores']['tipo_persona'],
        ':identificador' => $validacion['valores']['identificador_institucional'],
        ':id'            => $id,
    ]);
    if ($stmt->fetch()) {
        $errores['general'] =
            'Ya existe otro usuario con ese correo o identificador. Revisa los datos.';
    }
}

if ($errores !== []) {
    $_SESSION['editar_errores'] = $errores;
    $_SESSION['editar_viejo']   = $validacion['valores'];
    header('Location: ' . RUTA_FORMULARIO . '?id=' . $id);
    exit;
}

$stmt = $pdo->prepare(
    'UPDATE usuarios
        SET tipo_persona = :tipo_persona,
            identificador_institucional = :identificador,
            nombre_completo = :nombre,
            correo_electronico = :correo,
            licencia_permiso = :licencia
      WHERE id = :id'
);
$stmt->execute([
    ':tipo_persona' => $validacion['valores']['tipo_persona'],
    ':identificador' => $validacion['valores']['identificador_institucional'],
    ':nombre'        => $validacion['valores']['nombre_completo'],
    ':correo'        => $validacion['valores']['correo_electronico'],
    ':licencia'      => $validacion['valores']['licencia_permiso'],
    ':id'            => $id,
]);

header('Location: ' . RUTA_DETALLE . '?id=' . $id);
exit;