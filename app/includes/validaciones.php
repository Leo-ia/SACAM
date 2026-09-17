<?php
function sacam_tipo_placa_validado(string $tipo_placa): array
{
    if ($tipo_placa === 'permiso_provisional') {
        return ['tipo_placa' => 'permiso_provisional', 'estado' => 'pendiente_actualizacion'];
    }
    return ['tipo_placa' => 'con_placa', 'estado' => 'activa'];
}

function sacam_validar_usuario(array $entrada): array
{
    $errores = [];

    $tipo_persona             = trim($entrada['tipo_persona'] ?? '');
    $identificador_institucional = trim($entrada['identificador_institucional'] ?? '');
    $nombre_completo          = trim($entrada['nombre_completo'] ?? '');
    $correo_electronico       = trim($entrada['correo_electronico'] ?? '');
    $licencia_permiso         = trim($entrada['licencia_permiso'] ?? '');

    if (!in_array($tipo_persona, ['alumno', 'docente', 'administrativo', 'intendencia', 'otro'], true)) {
        $errores['tipo_persona'] = 'Selecciona un tipo de persona válido.';
    }

    if ($identificador_institucional === '' || mb_strlen($identificador_institucional) > 20) {
        $errores['identificador_institucional'] = 'Captura tu boleta o número de empleado (máx. 20 caracteres).';
    }

    if ($nombre_completo === '' || mb_strlen($nombre_completo) > 120) {
        $errores['nombre_completo'] = 'El nombre completo es obligatorio (máx. 120 caracteres).';
    }

    if ($correo_electronico === '' || !filter_var($correo_electronico, FILTER_VALIDATE_EMAIL)) {
        $errores['correo_electronico'] = 'Escribe un correo electrónico válido.';
    }

    if ($licencia_permiso !== '' && mb_strlen($licencia_permiso) > 30) {
        $errores['licencia_permiso'] = 'La licencia o permiso debe tener máximo 30 caracteres.';
    }

    return [
        'valores' => [
            'tipo_persona'               => $tipo_persona,
            'identificador_institucional' => $identificador_institucional,
            'nombre_completo'            => $nombre_completo,
            'correo_electronico'         => $correo_electronico,
            'licencia_permiso'           => $licencia_permiso === '' ? null : $licencia_permiso,
        ],
        'errores' => $errores,
    ];
}

function sacam_validar_motocicleta(array $entrada): array
{
    $errores = [];

    $marca    = trim($entrada['marca'] ?? '');
    $modelo   = trim($entrada['modelo'] ?? '');
    $color    = trim($entrada['color'] ?? '');
    $placa    = strtoupper(trim($entrada['placa'] ?? ''));
    $tipo_val = sacam_tipo_placa_validado($entrada['tipo_placa'] ?? 'con_placa');

    if ($marca === '' || mb_strlen($marca) > 50) {
        $errores['marca'] = 'La marca es obligatoria (máx. 50 caracteres).';
    }
    if ($modelo === '' || mb_strlen($modelo) > 50) {
        $errores['modelo'] = 'El modelo es obligatorio (máx. 50 caracteres).';
    }
    if ($color === '' || mb_strlen($color) > 50) {
        $errores['color'] = 'El color es obligatorio (máx. 50 caracteres).';
    }
    if ($placa === '' || mb_strlen($placa) > 20) {
        $errores['placa'] = 'Captura la placa o el número de permiso provisional (máx. 20 caracteres).';
    }
    if (!in_array($tipo_val['tipo_placa'], ['con_placa', 'permiso_provisional'], true)) {
        $errores['tipo_placa'] = 'Selecciona un tipo de placa válido.';
    }

    return [
        'valores' => [
            'marca'      => $marca,
            'modelo'     => $modelo,
            'color'      => $color,
            'placa'      => $placa,
            'tipo_placa' => $tipo_val['tipo_placa'],
            'estado'     => $tipo_val['estado'],
        ],
        'errores' => $errores,
    ];
}

function sacam_validar_imagen(array $archivo, string $campo_archivo): array
{
    if (($archivo['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) {
        throw new UnexpectedValueException("El campo $campo_archivo no recibió ningún archivo.");
    }

    if ($archivo['error'] !== UPLOAD_ERR_OK) {
        throw new UnexpectedValueException("Error al subir $campo_archivo (código {$archivo['error']}).");
    }

    $tamano_maximo = 5 * 1024 * 1024;
    if ($archivo['size'] > $tamano_maximo) {
        throw new UnexpectedValueException("$campo_archivo pesa más de 5 MB. Elige un archivo más ligero.");
    }

    $tipo_real   = mime_content_type($archivo['tmp_name']) ?: '';
    $extension   = $tipo_real === 'image/jpeg' ? 'jpg' : ($tipo_real === 'image/png' ? 'png' : '');
    if ($extension === '') {
        throw new UnexpectedValueException("$campo_archivo debe ser una imagen JPG o PNG.");
    }

    return [
        'ruta_relativa' => '',
        'extension'     => $extension,
    ];
}