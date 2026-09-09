# SACAM — Sistema Automatizado de Control de Acceso a Estacionamientos de Motocicletas

Aplicación para digitalizar el registro y la verificación de motocicletas en los dos accesos vehiculares de la **Escuela Superior de Cómputo (ESCOM, IPN)**, sustituyendo el registro manual por fotografía con un **QR único por motocicleta** vinculado a los datos del propietario y su unidad.

> Proyecto desarrollado por **Dev Core Systems, S.A.S. de C.V.** para la materia **Análisis y Diseño de Sistemas Digitales · 2027-1**.
>
> Equipo: Armenta Romero Kevin Fred · Limón Díaz Víctor Manuel · Olivares Salcedo Leonardo Alejandro · Salinas Ortega Isaac Yahir.

---

## Contexto del negocio

Se busca eliminar el cuello de botella del registro manual (fotografía + papel) en las entradas de la escuela: el guardia en turno podrá escanear un QR, ver al instante los datos y fotografías del conductor, y dejar un **historial trazable de entradas y salidas** que sirva como evidencia ante incidentes, disuadiendo el robo por suplantación.

El sistema **no** automatiza barreras ni faculta al personal a impedir el tránsito: opera como herramienta de registro y verificación, conforme al reglamento del IPN.

## Tecnologías

| Capa               | Tecnología                          |
|--------------------|-------------------------------------|
| Frontend           | HTML5 + CSS3 + JavaScript           |
| Backend            | PHP plano (sin framework)           |
| Base de datos      | MySQL (InnoDB, utf8mb4)            |
| Acceso a BD        | PDO (PHP Data Objects)              |
| QR                 | endroid/qr-code (SP2) · html5-qrcode (SP2) |
| Autenticación      | Sesiones nativas de PHP (`$_SESSION`) |
| Hosting            | Hostinger                           |
| Control de versiones | Git + GitHub                      |

## Metodología y sprint

Desarrollo iterativo bajo **Scrum** en un plazo de 3 meses. El repositorio se organiza por **sprints**; el estado de cada uno queda documentado en `docs/`.

### Sprint 1 (entrega: este corte)
Flujo funcional de registro del propietario y su moto. **Objetivo del sprint:** que cualquier miembro de la comunidad politécnica pueda registrarse a sí mismo y a su motocicleta, quedando la información almacenada correctamente en la BD.

| # | Tarea                                        | Dep. | Estado   |
|---|----------------------------------------------|------|----------|
| 1 | Crear base de datos con las tablas diseñadas  | —    | Hecho    |
| 2 | Archivo de conexión PDO reutilizable          | 1    | Pendiente |
| 3 | Funciones de validación reutilizables         | —    | Pendiente |
| 4 | Página de inicio (HTML/CSS colores IPN)        | —    | Hecho (frontend) |
| 5 | Formulario de registro de usuario (frontend)   | 4    | Hecho (frontend) |
| 6 | Procesamiento backend del formulario de usuario | 2,3,5 | Pendiente |
| 7 | Formulario de registro de moto (frontend)       | 6    | Hecho (frontend) |
| 8 | Procesamiento backend del formulario de moto (transacción) | 2,3,7 | Pendiente |
| 9 | Aviso de privacidad simplificado (LGPDPPSO)    | 5,7  | Hecho (frontend, en registro_usuario.php) |
| 10 | Pruebas manuales del flujo completo            | todas | Pendiente |

Las tareas 4, 5, 7 y 9 quedan cubiertas solo del lado del **frontend**: maquetado,
estilos con la paleta IPN, validación de cliente y el checklist del aviso de
privacidad. El guardado real en la base de datos (backend, tareas 2, 3, 6 y 8)
sigue pendiente. Detalle completo en
[`docs/sprint1/sprint1.md`](docs/sprint1/sprint1.md).

**Historias de usuario:** HU-01 página de inicio · HU-02 registro de usuario · HU-03 registro de motocicleta · HU-04 persistencia confiable (transacción todo-o-nada).

## Estructura de carpetas

La división de carpetas ya está pensada para **todo el proyecto** (los tres
meses de la propuesta), no solo para el Sprint 1. Los tres perfiles de la
propuesta técnica (usuario, guardia, administrador) tienen ya su lugar
reservado en `app/` y `public/`, aunque `guardia/` y `administrador/` se
llenan hasta los Sprints 2 y 3 (QR, escaneo, panel de control).

```
SACAM/
├── app/
│   ├── includes/           # Tarea 2: conexión PDO · Tarea 3: validaciones
│   │                       # (compartidas por los tres perfiles)
│   ├── procesos/           # Backend de los formularios públicos (tareas 6 y 8)
│   ├── vistas/
│   │   └── partials/       # Header, footer y aviso de privacidad reutilizables
│   ├── guardia/            # Lógica del perfil guardia — Sprint 2+
│   └── administrador/      # Lógica del perfil administrador — Sprint 3+
├── config/                 # Configuración (credenciales fuera de public/)
├── database/
│   └── sacam_bd_sprint1.sql   # ← Tarea 1 · esquema de la BD (entregable actual)
├── docs/
│   └── sprint1/             # Análisis y evidencia del sprint
├── public/                  # Raíz pública del servidor web (document root)
│   ├── css/estilos.css      # Colores IPN: guinda #750946, gris #636569
│   ├── js/validaciones.js   # Validación de cliente (HU-02, HU-03)
│   ├── img/                 # Imágenes públicas (vacío por ahora)
│   ├── index.php            # HU-01 · página de inicio
│   ├── registro_usuario.php # HU-02 · paso 1 del trámite
│   ├── registro_moto.php    # HU-03 · paso 2 del trámite
│   ├── confirmacion.php     # Paso 3 · confirmación
│   ├── guardia/              # Páginas del perfil guardia — Sprint 2+
│   └── administrador/        # Páginas del perfil administrador — Sprint 3+
└── uploads/                 # Archivos subidos (fuera del control de versiones)
    ├── credenciales/        # Fotos de credencial IPN (LGPDPPSO)
    └── motocicletas/        # Fotos de las motos
```

**Por qué `vistas/partials/` y no todo suelto en `public/`.** El header, el
footer y el aviso de privacidad se repiten en las cuatro páginas del trámite;
vivir en `app/vistas/partials/` (fuera de la raíz pública) evita que alguien
los pida por URL directamente y mantiene `app/includes/` reservado solo para
infraestructura (conexión a BD y validaciones), tal como pide el backlog.

**Por qué las páginas públicas siguen siendo controladores delgados.** Cada
archivo en `public/` (`index.php`, `registro_usuario.php`, …) es el punto de
entrada que el servidor sí puede servir; cuando lleguen las tareas 6 y 8,
esos mismos archivos incluirán la lógica de `app/procesos/` para procesar el
`$_POST`, sin mover nada de carpeta.

## Base de datos (Sprint 1 · Tarea 1)

El esquema vive en [`database/sacam_bd_sprint1.sql`](database/sacam_bd_sprint1.sql). Diseño pensado para **no rediseñarse** cuando lleguen QR, cuentas de guardia/administrador y bitácora (esa parte se entrega comentada, para su sprint).

- **`usuarios`** → dueño de la moto (tipo de persona, identificador institucional único por tipo, nombre, correo único validado, ruta de foto de credencial, licencia opcional).
- **`motocicletas`** → pertenece a un usuario (FK 1:N con `ON DELETE CASCADE`), marca/modelo/color, placa o permiso provisional, y estado `pendiente_actualizacion` garantizado por `CHECK` cuando el trámite es provisional.
- Las **fotos no van como BLOB**: se guarda la ruta del archivo, manteniendo la BD ligera.
- Índices y `TIMESTAMP` de auditoría (`created_at` / `updated_at`) automáticos.

### Cómo montarla

```bash
mysql -u usuario -p < database/sacam_bd_sprint1.sql
```

Requiere MySQL 8+ o MariaDB 10.2+ (por las `CHECK CONSTRAINT`).

## Frontend (Sprint 1 · Tareas 4, 5, 7 y 9)

Cubre el maquetado y la interacción de cliente de las cuatro pantallas del
trámite: `index.php` (HU-01), `registro_usuario.php` (HU-02),
`registro_moto.php` (HU-03) y `confirmacion.php`.

- **Identidad visual.** Guinda `#750946` como color de acción (botones,
  enlaces, encabezado, paso activo), gris `#636569` para texto secundario,
  blanco de fondo y negro para texto de alto contraste. Tipografía Noto Sans
  en todo el sitio. El rojo de error y el ámbar de "pendiente de actualizar"
  no vienen del manual del IPN: son colores funcionales, necesarios para que
  el formulario pueda señalar errores y datos pendientes (documentado en
  `public/css/estilos.css`).
- **Flujo como trámite, no como sitio de marketing.** Contenido alineado a la
  izquierda, ancho de línea corto y un indicador de pasos (1 Datos
  personales · 2 Motocicleta · 3 Confirmación) porque el registro sí es un
  proceso secuencial real.
- **Validación de cliente (`public/js/validaciones.js`).** Errores junto a
  cada campo (no una alerta genérica), verificación de formato de correo,
  verificación de formato/peso de imagen antes de enviarse, y textos que
  cambian según el tipo de persona (boleta vs. número de empleado) y el tipo
  de placa (placa definitiva vs. permiso provisional). Esta validación es
  solo de experiencia de usuario: la validación obligatoria sigue siendo la
  del servidor (tareas 3, 6 y 8).
- **Aviso de privacidad (LGPDPPSO).** Vive en
  `app/vistas/partials/aviso_privacidad.php` y se incluye una sola vez, en
  `registro_usuario.php`, con checkbox obligatorio para continuar.
- **Sin backend todavía.** Como las tareas 6 y 8 (procesamiento y guardado
  real) no están hechas, los formularios usan temporalmente `method="get"`
  para encadenar `registro_usuario.php → registro_moto.php →
  confirmacion.php` y así poder mostrar el flujo completo en la demo del
  jueves. Está documentado con comentarios `NOTA TÉCNICA` en cada archivo:
  cuando el backend exista, el cambio a `method="post"` es directo y no
  requiere tocar el HTML de los formularios.

### Previsualizar el frontend

```bash
php -S localhost:8000 -t public
```

Y abrir `http://localhost:8000/index.php` en el navegador.

## Definition of Done (Sprint 1)

- El formulario correspondiente guarda correctamente en la BD.
- Validaciones de servidor (no solo de cliente) funcionando.
- Imágenes guardadas con nombre único en la carpeta correcta.
- Probado manualmente: caso exitoso, campo obligatorio vacío, imagen inválida y permiso provisional sin placa.

## Privacidad

El IPN, al ser sujeto obligado por la LGPDPPSO, debe informar qué datos se recaban antes de recabarlos. Por eso los formularios del sprint incluyen un **aviso de privacidad simplificado** con checkbox de aceptación, y las fotos personales **nunca se versionan** en este repositorio (ver `.gitignore`).