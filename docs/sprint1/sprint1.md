# Sprint 1 — SACAM

| | |
|---|---|
| **Proyecto** | SACAM — Sistema Automatizado de Control de Acceso a Estacionamientos de Motocicletas |
| **Materia** | Análisis y Diseño de Sistemas Digitales · 2027-1 |
| **Institución** | ESCOM · IPN |
| **Equipo** | Dev Core Systems, S.A.S. de C.V. |
| **Repositorio** | https://github.com/Leo-ia/SACAM |
| **Entregable** | Este documento (`docs/sprint1/sprint1.md`) |

---

## 1. Objetivo del sprint

Entregar un flujo funcional de principio a fin donde cualquier miembro de la
comunidad politécnica (alumno o empleado) pueda registrarse a sí mismo y a su
motocicleta, quedando esa información almacenada correctamente en la base de
datos del sistema. No incluye QR, login de guardia/administrador ni panel de
administración: eso se definió para el Sprint 2 en adelante.

## 2. Historias de usuario y su estado

| Historia | Descripción | Estado |
|---|---|---|
| HU-01 | Página de inicio informativa | Hecho |
| HU-02 | Registro de usuario (dueño) | Hecho (frontend + backend) |
| HU-03 | Registro de motocicleta | Hecho (frontend + backend) |
| HU-04 | Persistencia confiable (transacción todo-o-nada) | Hecho (transacción PHP + `CHECK`, FK, InnoDB) |

## 3. Backlog y estado por tarea

| # | Tarea | Estado | Evidencia |
|---|---|---|---|
| 1 | Crear base de datos con las tablas ya diseñadas | Hecho | `database/sacam_bd_sprint1.sql`, detallado en `Informe_Tecnico_Sprint1.md` |
| 2 | Archivo de conexión PDO reutilizable | Hecho | `app/includes/conexion.php` + `config/config.local.php` |
| 3 | Funciones de validación reutilizables (servidor) | Hecho | `app/includes/validaciones.php` |
| 4 | Página de inicio (HTML/CSS con colores IPN) | Hecho | `public/index.php` |
| 5 | Formulario de registro de usuario (frontend) | Hecho | `public/registro_usuario.php` |
| 6 | Procesamiento backend del formulario de usuario | Hecho | `app/procesos/procesar_registro.php` (paso 1) |
| 7 | Formulario de registro de moto (frontend) | Hecho | `public/registro_moto.php` |
| 8 | Procesamiento backend del formulario de moto (transacción) | Hecho | `app/procesos/procesar_registro.php` (paso 2, transacción HU-04) |
| 9 | Aviso de privacidad simplificado en ambos formularios | Hecho (en el primer formulario) | `app/vistas/partials/aviso_privacidad.php`, incluido en `registro_usuario.php` |
| 10 | Pruebas manuales del flujo completo | Hecho (9/9) | Ver sección 6 (pruebas end-to-end con guardado real) |

## 4. Qué se construyó en este corte (frontend + backend)

Se maquetaron, conectaron y dotaron de persistencia las cuatro pantallas del
trámite:

1. **`public/index.php`** — explica qué es SACAM, qué documentos pide el
   formulario (credencial, datos y foto de la moto) y enlaza al registro.
2. **`public/registro_usuario.php`** — formulario de la HU-02: tipo de
   persona, identificador institucional (la etiqueta cambia entre "Boleta" y
   "Número de empleado" según el tipo elegido), nombre, correo, foto de
   credencial, licencia opcional y el aviso de privacidad con checkbox.
3. **`public/registro_moto.php`** — formulario de la HU-03: marca, modelo,
   color, selector de placa definitiva o permiso provisional (la etiqueta y
   la ayuda del campo "placa" cambian según la opción) y foto de la moto.
4. **`public/confirmacion.php`** — confirma el guardado en BD, muestra el
   folio asignado y el resumen de lo registrado, incluida la etiqueta visual
   "Pendiente de actualizar" cuando la moto quedó con permiso provisional
   (criterio de aceptación de la HU-03).

Piezas compartidas del frontend:

- **`public/css/estilos.css`** — un solo archivo con la paleta institucional
  del IPN (guinda `#750946`, gris `#636569`, blanco, negro) y tipografía Noto
  Sans, más los tokens funcionales de error/advertencia que necesita
  cualquier formulario (no forman parte del manual del IPN).
- **`public/js/validaciones.js`** — validación de cliente: campos
  obligatorios, formato de correo, formato y peso de imagen (JPG/PNG, máx.
  5 MB) antes de intentar subirla, y los textos dinámicos ya mencionados.
- **`app/vistas/partials/`** — `header.php`, `footer.php` y
  `aviso_privacidad.php`, incluidos con `require` en las cuatro páginas para
  no repetir el encabezado institucional ni el aviso de privacidad.

### 4.1 Decisión de diseño: backend con transacción y sesión

Las tareas 2, 3, 6 y 8 están resueltas: el flujo ya persiste en la base de
datos.

- Los formularios envían con `method="post"` hacia `public/procesar_registro.php`,
  la única puerta pública del backend (la lógica real vive en
  `app/procesos/procesar_registro.php`, fuera de la raíz pública).
- Los datos del paso 1 se conservan en `$_SESSION` entre pantallas; no se
  exponen por la URL como ocurría en la demo con `GET`.
- El guardado de `usuarios` + `motocicletas` ocurre dentro de una
  **transacción SQL** (`BEGIN` … `COMMIT` / `ROLLBACK`): si cualquiera de los
  dos `INSERT` falla, no queda un usuario sin moto ni una moto sin dueño
  (HU-04, todo-o-nada).
- Las fotografías se guardan con **nombre aleatorio único** en
  `uploads/credenciales/` y `uploads/motocicletas/`; en la BD solo se guarda
  la ruta, tal como diseñaron (regla de normalización de la tarea 1).
- Ante cualquier error de validación o duplicado, se regresa al formulario con
  los mensajes junto a cada campo y los valores previos conservados (mensajes
  flash en sesión).

## 5. Estructura de carpetas (ver detalle en `README.md`)

Se ajustó la división de carpetas para que sirva para todo el proyecto y no
solo para este sprint: se agregó `app/vistas/partials/` para las piezas de
interfaz compartidas, y se reservaron `app/guardia/`, `app/administrador/`,
`public/guardia/` y `public/administrador/` para los perfiles de guardia y
administrador de la propuesta técnica (Sprint 2 y Sprint 3), de modo que no
haya que reacomodar carpetas más adelante.

## 6. Pruebas realizadas

### 6.1 Frontend (validación de cliente)

| # | Caso probado | Resultado esperado | Resultado |
|---|---|---|---|
| 1 | Cargar las 4 páginas (`index`, `registro_usuario`, `registro_moto`, `confirmacion`) con `php -S` | Responden 200, sin errores de PHP | OK |
| 2 | Enviar `registro_usuario.php` sin llenar un campo obligatorio | El formulario no se envía; aparece el error junto al campo | OK |
| 3 | Escribir un correo sin `@` | Error específico de formato de correo | OK |
| 4 | Seleccionar una imagen que no sea JPG/PNG o mayor a 5 MB | Error junto al campo de archivo, sin llegar a enviarse | OK |
| 5 | Elegir "alumno" vs. otro tipo de persona | La etiqueta cambia entre "Boleta" y "Número de empleado" | OK |
| 6 | Elegir "permiso provisional" en la moto | La etiqueta de "Placa" cambia y el resumen final muestra "Pendiente de actualizar" | OK |
| 7 | Completar el flujo de punta a punta (usuario → moto → confirmación) | Los datos capturados se ven correctamente en la pantalla de confirmación | OK |

### 6.2 Backend y persistencia (end-to-end con guardado real en BD)

Pruebas automatizadas sobre un servidor local (`php -S`), un MariaDB
(Docker) y solicitudes HTTP reales simulando el navegador (curl con cookies).

| # | Caso probado | Resultado esperado | Resultado |
|---|---|---|---|
| 1 | Paso 1 válido | Redirige a `registro_moto.php` con los datos en sesión | OK |
| 2 | Paso 2 válido con placa definitiva | Redirige a `confirmacion.php`; en BD queda `con_placa` / `activa` | OK |
| 3 | Paso 2 con permiso provisional | En BD queda `permiso_provisional` / `pendiente_actualizacion` (CHECK respetado) | OK |
| 4 | Correo o identificador duplicado | Rechazado y regresado al paso 1 con mensaje | OK |
| 5 | No aceptar el aviso de privacidad | Rechazado en el servidor, no solo en el cliente | OK |
| 6 | Imagen inválida (archivo de texto renombrado a `.jpg`) | Rechazada por contenido real; regresa al formulario | OK |
| 7 | Acceder a `registro_moto.php` sin sesión previa | Redirige al paso 1 | OK |
| 8 | Confirmación sin datos | Muestra mensaje "no hay registro" | OK |
| 9 | Confirmación con datos | Muestra folio y resumen; se consume una sola vez (recargar no duplica) | OK |

**Resultado de la batería end-to-end: 9/9 casos OK.** Los archivos subidos
quedan con nombre único en `uploads/credenciales/` y `uploads/motocicletas/`,
y las rutas se reflejan correctamente en las tablas `usuarios` y
`motocicletas`.

## 7. Pendiente para el resto del equipo

El objetivo del Sprint 1 (registro con persistencia) está cubierto. Queda
abierto hacia los siguientes sprints:

- Sprint 2 — QR único por motocicleta y cuentas de guardia/administrador
  (esquema ya comentado al final de `database/sacam_bd_sprint1.sql`).
- Sprint 2/3 — bitácora de accesos (`accesos`), login por sesión y panel de
  administración.
- Definir el manejo centralizado de errores para producción (logs sin exponer
  credenciales) y un usuario MySQL de mínimos privilegios
  (`sacam_app`, documentado en el script SQL).
- Publicar la configuración para el hosting (Hostinger), reemplazando
  `config/config.local.php`.

---

Documento generado como entregable del Sprint 1.
