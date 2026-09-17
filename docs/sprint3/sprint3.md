# Sprint 3 — SACAM

| | |
|---|---|
| **Proyecto** | SACAM — Sistema Automatizado de Control de Acceso a Estacionamientos de Motocicletas |
| **Materia** | Análisis y Diseño de Sistemas Digitales · 2027-1 |
| **Institución** | ESCOM · IPN |
| **Equipo** | Dev Core Systems, S.A.S. de C.V. |
| **Repositorio** | https://github.com/Leo-ia/SACAM |
| **Entregable** | Este documento (`docs/sprint3/sprint3.md`) |

---

## 1. Objetivo del sprint

Cubrir la historia del perfil de administrador (HU-05): que el administrador
pueda consultar y gestionar a los usuarios registrados. Para este corte se
entrega el **panel con cinco pantallas funcionales** sobre los datos ya
persistidos en el Sprint 1. El acceso al panel quedó sin protección en este
sprint; el login se cierra en el Sprint 4.

## 2. Historias de usuario y su estado

| Historia | Descripción | Estado |
|---|---|---|
| HU-05 | Panel de administración sobre los usuarios registrados | Hecho (5 pantallas; login pendiente → Sprint 4) |

## 3. Qué se construyó en este corte

### 3.1 Pantallas (frontend)

| Pantalla | Archivo | Función |
|---|---|---|
| 1. Listado de usuarios | `public/administrador/usuarios.php` | Tabla con todos los usuarios, su estado (`Activo`/`Acceso revocado`) y enlaces Ver/Editar/Eliminar. |
| 2. Ver un usuario | `public/administrador/ver_usuario.php?id=N` | Detalle de datos personales, licencia, motos del usuario, botón "Revocar acceso" / "Restaurar acceso". |
| 3. Editar un usuario | `public/administrador/editar_usuario.php?id=N` | Formulario precargado para corregir los datos personales. |
| 4. Revocar / restaurar acceso | dentro de la pantalla 2 | Cambia la columna `activo` y se refleja en el listado. |
| 5. Eliminar un usuario | desde el listado (con confirmación) | Borra el usuario y sus motos. |

### 3.2 Backend

| Archivo | Función |
|---|---|
| `app/administrador/procesar_editar_usuario.php` | Valida y actualiza los datos editados; regresa a `ver_usuario.php` o al formulario con mensaje en caso de error. |
| `app/administrador/procesar_estado_usuario.php` | Cambia `activo` entre 0 y 1 según la acción (`revocar` / `restaurar`). |
| `app/administrador/procesar_eliminar_usuario.php` | Elimina el usuario; las motocicletas se borran solas por la FK con `ON DELETE CASCADE` definida en el esquema. |
| `public/administrador/procesar_*.php` | Puertas públicas delgadas que solo delegan en `app/` (la lógica nunca se sirve por URL). |

### 3.3 Base de datos

- `database/alter_admin_sprint.sql` — agrega la columna
  `activo BOOLEAN NOT NULL DEFAULT TRUE` a `usuarios` para el control de
  revocado/restaurado, sin necesidad de rediseñar la tabla.

### 3.4 Otros

- `public/css/estilos_admin.css` — estilos mínimos para el panel (se reutiliza
  desde el Sprint 4 para el login).
- `public/index.php` — enlace "Acceso interno · Panel de administración" para
  entrar desde la portada.

## 4. Decisiones de diseño

- **Mismo patrón de controladores delgados** del Sprint 1: las pantallas del
  panel viven en `public/administrador/` y la lógica en `app/administrador/`;
  los formularios posteran a las puertas públicas `procesar_*`.
- **Redirects relativos.** Los `Location:` del backend usan rutas relativas
  (`usuarios.php`), que el navegador resuelve desde `/administrador/`; así el
  panel funciona igual sin importar el dominio o el puerto.
- **Seguridad.** Todo POST se valida con `htmlspecialchars()` a la salida y
  consultas con sentencias preparadas PDO. No se acepta la edición de campos
  que el producto no permite cambiar (identificador institucional con el mismo
  tipo de persona).
- **Eliminación en cascada.** El esquema del Sprint 1 define la FK
  `motocicletas.usuario_id → usuarios.id` con `ON DELETE CASCADE`; el panel se
  apoya en ella para no dejar motos huérfanas.

## 5. Cómo probarlo

Servicios activos, luego (tras el Sprint 4, todo exige el login `admin` /
`1234` en `http://127.0.0.1:8000/administrador/login.php`):

- Listado: `http://127.0.0.1:8000/administrador/usuarios.php`
- Ver: `http://127.0.0.1:8000/administrador/ver_usuario.php?id=1`
- Editar: `http://127.0.0.1:8000/administrador/editar_usuario.php?id=1`

## 6. Pruebas realizadas

Batería automatizada `test_admin.sh` (27/27 OK) que cubre: mostrar listado,
ver usuario, editar con datos válidos (cambio real en BD) e inválidos (no
cambia nada), revocar y restaurar el acceso (`activo` 0/1), eliminar con
cascada de motos, y el bloqueo/permiso del login del Sprint 4.

## 7. Pendiente para el resto del equipo

- Sprint 4: login de administrador (se entrega en el siguiente corte).
- Sprint 2: perfil de guardia (QR y bitácora de accesos).
- Revisión del senior: confirmar el flujo de revocar/restaurar y el criterio de
  quién puede ver qué documento.

---

Documento generado como entregable del Sprint 3.