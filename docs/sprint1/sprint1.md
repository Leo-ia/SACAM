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
| HU-01 | Página de inicio informativa | Frontend hecho |
| HU-02 | Registro de usuario (dueño) | Frontend hecho · backend pendiente |
| HU-03 | Registro de motocicleta | Frontend hecho · backend pendiente |
| HU-04 | Persistencia confiable (transacción todo-o-nada) | Base de datos lista (`CHECK`, FK, InnoDB) · transacción en PHP pendiente |

## 3. Backlog y estado por tarea

| # | Tarea | Estado | Evidencia |
|---|---|---|---|
| 1 | Crear base de datos con las tablas ya diseñadas | Hecho | `database/sacam_bd_sprint1.sql`, detallado en `Informe_Tecnico_Sprint1.md` |
| 2 | Archivo de conexión PDO reutilizable | Pendiente | — |
| 3 | Funciones de validación reutilizables (servidor) | Pendiente | — |
| 4 | Página de inicio (HTML/CSS con colores IPN) | Hecho | `public/index.php` |
| 5 | Formulario de registro de usuario (frontend) | Hecho | `public/registro_usuario.php` |
| 6 | Procesamiento backend del formulario de usuario | Pendiente | — |
| 7 | Formulario de registro de moto (frontend) | Hecho | `public/registro_moto.php` |
| 8 | Procesamiento backend del formulario de moto (transacción) | Pendiente | — |
| 9 | Aviso de privacidad simplificado en ambos formularios | Hecho (en el primer formulario) | `app/vistas/partials/aviso_privacidad.php`, incluido en `registro_usuario.php` |
| 10 | Pruebas manuales del flujo completo | Parcial | Ver sección 6 (pruebas de frontend); las pruebas end-to-end contra la BD requieren las tareas 2, 3, 6 y 8 |

## 4. Qué se construyó en este corte (frontend)

Se maquetaron y conectaron visualmente las cuatro pantallas del trámite:

1. **`public/index.php`** — explica qué es SACAM, qué documentos pide el
   formulario (credencial, datos y foto de la moto) y enlaza al registro.
2. **`public/registro_usuario.php`** — formulario de la HU-02: tipo de
   persona, identificador institucional (la etiqueta cambia entre "Boleta" y
   "Número de empleado" según el tipo elegido), nombre, correo, foto de
   credencial, licencia opcional y el aviso de privacidad con checkbox.
3. **`public/registro_moto.php`** — formulario de la HU-03: marca, modelo,
   color, selector de placa definitiva o permiso provisional (la etiqueta y
   la ayuda del campo "placa" cambian según la opción) y foto de la moto.
4. **`public/confirmacion.php`** — resumen de lo capturado, incluida la
   etiqueta visual "Pendiente de actualizar" cuando la moto quedó con
   permiso provisional (criterio de aceptación de la HU-03).

Piezas compartidas:

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

### 4.1 Decisión de diseño: sin backend, flujo demostrable por `GET`

Las tareas 6 y 8 (procesamiento y guardado real en la base de datos) siguen
sin hacerse. Para poder mostrar el trámite completo en la demo del jueves sin
esa parte, los tres formularios se encadenan temporalmente con
`method="get"`: `registro_usuario.php` pasa sus campos a `registro_moto.php`,
y este a su vez los junta con los suyos y los pasa a `confirmacion.php`, que
los muestra en un resumen. No se guarda nada en ningún momento; es solo
navegación. Cada archivo trae un comentario `NOTA TÉCNICA` explicando el
cambio exacto que hay que hacer cuando el backend exista
(`method="post"` + `app/procesos/procesar_usuario.php` /
`procesar_moto.php`).

## 5. Estructura de carpetas (ver detalle en `README.md`)

Se ajustó la división de carpetas para que sirva para todo el proyecto y no
solo para este sprint: se agregó `app/vistas/partials/` para las piezas de
interfaz compartidas, y se reservaron `app/guardia/`, `app/administrador/`,
`public/guardia/` y `public/administrador/` para los perfiles de guardia y
administrador de la propuesta técnica (Sprint 2 y Sprint 3), de modo que no
haya que reacomodar carpetas más adelante.

## 6. Pruebas realizadas sobre el frontend

| # | Caso probado | Resultado esperado | Resultado |
|---|---|---|---|
| 1 | Cargar las 4 páginas (`index`, `registro_usuario`, `registro_moto`, `confirmacion`) con `php -S` | Responden 200, sin errores de PHP | OK |
| 2 | Enviar `registro_usuario.php` sin llenar un campo obligatorio | El formulario no se envía; aparece el error junto al campo | OK |
| 3 | Escribir un correo sin `@` | Error específico de formato de correo | OK |
| 4 | Seleccionar una imagen que no sea JPG/PNG o mayor a 5 MB | Error junto al campo de archivo, sin llegar a enviarse | OK |
| 5 | Elegir "alumno" vs. otro tipo de persona | La etiqueta cambia entre "Boleta" y "Número de empleado" | OK |
| 6 | Elegir "permiso provisional" en la moto | La etiqueta de "Placa" cambia y el resumen final muestra "Pendiente de actualizar" | OK |
| 7 | Completar el flujo de punta a punta (usuario → moto → confirmación) | Los datos capturados se ven correctamente en la pantalla de confirmación | OK |

Nota: al no existir todavía la tarea 3 (validación de servidor) ni la
persistencia (tareas 6 y 8), estas pruebas cubren solo la capa de frontend;
las pruebas contra la base de datos real ya se hicieron para la tarea 1 (ver
`Informe_Tecnico_Sprint1.md`, sección 6) y se repetirán de punta a punta
cuando el backend esté integrado.

## 7. Pendiente para el resto del equipo

- Tarea 2 — archivo de conexión PDO reutilizable.
- Tarea 3 — funciones de validación de servidor (correo, campos, imágenes).
- Tarea 6 — `app/procesos/procesar_usuario.php`.
- Tarea 8 — `app/procesos/procesar_moto.php`, con la transacción todo-o-nada
  de la HU-04.
- Tarea 10 — pruebas manuales del flujo completo ya con guardado real en la
  base de datos.

---

Documento generado como entregable del Sprint 1.
