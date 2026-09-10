<?php
/**
 * SACAM — Conexión reutilizable a la base de datos (Sprint 1 · Tarea 2).
 *
 * Toda página que necesite escribir o leer en la base de datos debe incluir
 * este archivo y usar la función sacam_conexion(). NINGÚN archivo del proyecto
 * instancia PDO directamente: la configuración (credenciales) viene de
 * config/config.local.php, que está fuera de la raíz pública y no se versiona.
 *
 * Uso:
 *   require_once __DIR__ . '/../includes/conexion.php';
 *   $pdo = sacam_conexion();
 */

require_once __DIR__ . '/../../config/config.local.php';

/**
 * Devuelve una instancia única de PDO conectada a la base `sacam`.
 *
 * La conexión se reutiliza entre llamadas dentro de la misma petición (patrón
 * singleton) para no abrir un socket nuevo cada vez que se consulta la BD.
 *
 * Errores: las excepciones de PDO están activadas a propósito. Si el host o
 * las credenciales fallan, el error se propaga para diagnosticar el entorno;
 * en producción se centralizará el manejo de errores (Sprint 2+).
 *
 * @return PDO
 */
function sacam_conexion(): PDO
{
    static $pdo = null;

    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $config = require __DIR__ . '/../../config/config.local.php';

    $dsn = sprintf(
        'mysql:host=%s;port=%d;dbname=%s;charset=%s',
        $config['host'],
        $config['port'],
        $config['dbname'],
        $config['charset']
    );

    $pdo = new PDO($dsn, $config['usuario'], $config['password'], [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // Excepciones en errores SQL
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // Arreglos asociativos por defecto
        PDO::ATTR_EMULATE_PREPARES   => false,                  // Sentencias preparadas reales
    ]);

    return $pdo;
}