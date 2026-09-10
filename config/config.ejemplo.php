<?php
/**
 * SACAM — Plantilla de configuración local (NO contiene secretos reales).
 *
 * Copia este archivo a config/config.local.php y ajusta los valores a tu
 * entorno:
 *
 *   cp config/config.ejemplo.php config/config.local.php
 *
 * `config.local.php` está en .gitignore: nunca se sube al repositorio para no
 * exponer las credenciales de la base de datos en producción.
 */

return [
    'host'     => 'localhost',   // Servidor de la base de datos
    'port'     => 3306,          // Puerto (MySQL/MariaDB usa 3306 por defecto)
    'dbname'   => 'sacam',       // Nombre de la base de datos (del script SQL)
    'charset'  => 'utf8mb4',     // Juego de caracteres con acentos seguros
    'usuario'  => 'root',        // Usuario de la base de datos
    'password' => 'tu_password', // Contraseña del usuario
];