<?php
/**
 * SACAM — Puerta de entrada del procesamiento de registro.
 *
 * El documento raíz del servidor web es `public/`, por lo que NADA de
 * `app/` puede pedirse por URL. Este archivo es el punto de entrada público
 * del formulario: solo delega la lógica real (validaciones, transacción y
 * guardado de archivos) a app/procesos/procesar_registro.php, que vive
 * fuera de la raíz pública y no se puede invocar directamente.
 */
require __DIR__ . '/../app/procesos/procesar_registro.php';