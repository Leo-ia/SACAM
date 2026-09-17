# Sprint 4 — SACAM

| | |
|---|---|
| **Proyecto** | SACAM — Sistema Automatizado de Control de Acceso a Estacionamientos de Motocicletas |
| **Materia** | Análisis y Diseño de Sistemas Digitales · 2027-1 |
| **Institución** | ESCOM · IPN |
| **Equipo** | Dev Core Systems, S.A.S. de C.V. |
| **Repositorio** | https://github.com/Leo-ia/SACAM |
| **Entregable** | Este documento (`docs/sprint4/sprint4.md`) |

---

## 1. Objetivo del sprint

Evitar que cualquier persona abra el panel de administración del Sprint 3:
se agrega una pantalla **mínima de acceso** (usuario `admin`, contraseña
`1234`) que protege las páginas y los procesadores del perfil administrador.
Por indicación del solicitante, las credenciales **no se guardan en la base de
datos** ni en una tabla de cuentas de administrador: quedan fijas en el código
del avance actual (en un Sprint posterior se migrará a una tabla real, como ya
prevé el esquema de `database/sacam_bd_sprint1.sql`).

## 2. Historias de usuario y su estado

| Historia | Descripción | Estado |
|---|---|---|
| HU-05 | El administrador inicia sesión con usuario y contraseña | Hecho (credenciales fijas `admin` / `1234`) |
| HU-05b | Sin sesión, el panel no es accesible (ni páginas ni POST) | Hecho |
| HU-05c | El administrador puede cerrar sesión | Hecho |

> Nota: el Sprint 3 ya cubrió la HU-05 incompleta (acceso sin protección). En
> este sprint se cierra el hueco de seguridad.

## 3. Qué se construyó en este corte

### 3.1 Archivos nuevos

| Archivo | Función |
|---|---|
| `app/administrador/requiere_login.php` | Guarda de sesión: si `$_SESSION['admin_autenticado']` no existe, redirige a `login.php` y detiene la ejecución. |
| `app/administrador/autenticar_admin.php` | Recibe el POST del formulario, compara las credenciales fijas con `hash_equals` (comparación en tiempo constante), abre la sesión y redirige a `usuarios.php`; si fallan, guarda un mensaje de error y regresa a `login.php`. |
| `app/administrador/cerrar_sesion.php` | Destruye la sesión y redirige a `login.php`. |
| `public/administrador/login.php` | Formulario de acceso (usuario y contraseña) con estilos mínimos reutilizando `public/css/estilos_admin.css`. Muestra el error devuelto por el backend. |
| `public/administrador/autenticar_admin.php` | Puerta pública delgada del login (delega en `app/`). |
| `public/administrador/cerrar_sesion.php` | Puerta pública delgada del cierre de sesión. |

### 3.2 Archivos modificados

| Archivo | Cambio |
|---|---|
| `public/administrador/usuarios.php` | Incluye el guarda; agrega enlace "Cerrar sesión". |
| `public/administrador/ver_usuario.php` | Incluye el guarda; agrega enlace "Cerrar sesión". |
| `public/administrador/editar_usuario.php` | Incluye el guarda; agrega enlace "Cerrar sesión". |
| `public/administrador/procesar_editar_usuario.php` | Incluye el guarda (el POST también exige sesión). |
| `public/administrador/procesar_estado_usuario.php` | Incluye el guarda. |
| `public/administrador/procesar_eliminar_usuario.php` | Incluye el guarda. |
| `app/administrador/procesar_editar_usuario.php` | `session_start()` protegido con `session_status()` para no duplicar el inicio de sesión. |
| `app/administrador/procesar_estado_usuario.php` | Ídem. |
| `app/administrador/procesar_eliminar_usuario.php` | Ídem. |
| `public/index.php` | El enlace del pie apunta ahora a `administrador/login.php` (antes iba directo al panel). |

## 4. Decisiones de diseño

- **Por qué sesión PHP y no JavaScript.** Se evaluó hacer la validación "por
  coincidencias" con JS dentro de la página, pero las credenciales quedarían
  visibles en el código fuente que recibe el navegador y la revisión del
  docente lo reprobaría. Con sesión PHP el resultado es idéntico en pantalla
  (un formulario simple) pero las credenciales viven solo en el servidor.
- **Por qué credenciales fijas en código y no en la BD.** Es un requisito
  explícito de este avance. En el Sprint 2 se creará la tabla de cuentas de
  acceso (`cuentas_acceso`) con `password_hash`; este código se descartará
  cuando esa tabla exista.
- **`hash_equals()` para comparar** usuario y contraseña: evita ataques de
  timing y es la forma correcta de comparar credenciales.
- **`requiere_login.php` también en los procesadores.** Proteger solo las
  páginas sería insuficiente: un POST directo a
  `procesar_eliminar_usuario.php` tendría que estar bloqueado, y lo está.
- **Puertas públicas delgadas.** Se sigue la regla del proyecto (la raíz
  pública es `public/`; `app/` no puede pedirse por URL), por lo que el login y
  el cierre de sesión también pasan por un archivo público que solo delega en
  `app/administrador/`.
- **Corrección heredada del Sprint 3.** Los tres procesadores llamaban
  `session_start()` y el guarda también lo hace; en PHP 8.5 ese segundo llamado
  emite un *Notice* que rompe los `Location:` (responde 200 en vez de 302). Se
  dejó `if (session_status() !== PHP_SESSION_ACTIVE) session_start();`.

## 5. Cómo probarlo

Servicios activos (`systemctl --user is-active sacam-mariadb.service sacam-php.service`),
luego en el navegador:

1. `http://127.0.0.1:8000/administrador/login.php`
2. Credenciales incorrectas → se recarga el login con el mensaje de error.
3. `admin` / `1234` → redirige al listado de usuarios.
4. Páginas y POST del panel sin sesión → redirigen al login.
5. Enlace "Cerrar sesión" en el panel → vuelve al login y bloquea de nuevo.

## 6. Pruebas realizadas

Batería automatizada `test_admin.sh` (27/27 OK) con su propia MariaDB temporal
y servidor PHP de prueba, cubriendo:

| # | Caso probado | Resultado |
|---|---|---|
| 1 | `usuarios.php` sin sesión | Redirige a `login.php` |
| 2 | POST a eliminar sin sesión | Redirige a `login.php` |
| 3 | Credenciales incorrectas | Muestra el error, no entra |
| 4 | `admin` / `1234` | Abre sesión y redirige al listado |
| 5 | Listado, ver, editar, revocar/restaurar y eliminar con sesión | Todo conservado del Sprint 3 |
| 6 | Cerrar sesión | El panel vuelve a bloquearse |

## 7. Pendiente

- Sustituir las credenciales fijas por la tabla de cuentas de acceso del
  esquema (Sprint 2) con `password_hash` / `password_verify`.
- Aplicar el mismo patrón de sesión al perfil de guardia (Sprint 2).
- Página de "recuperación/creación" de la primera cuenta administradora.

---

Documento generado como entregable del Sprint 4.