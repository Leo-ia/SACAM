# Informe Técnico — Sprint 1 · Tarea 1 (Base de Datos)

| | |
|---|---|
| **Proyecto** | SACAM — Sistema Automatizado de Control de Acceso a Estacionamientos de Motocicletas |
| **Materia** | Análisis y Diseño de Sistemas Digitales · 2027-1 |
| **Institucion** | ESCOM · IPN |
| **Equipo** | Dev Core Systems, S.A.S. de C.V. |
| **Fecha de corte** | Septiembre de 2026 |
| **Entregable principal** | `database/sacam_bd_sprint1.sql` |

---

## 1. Objetivo de este documento

Dejar constancia de qué se hizo, con qué herramientas y de qué manera quedó
compuesto el entregable de la **Tarea 1 del Sprint 1** ("Crear base de datos con
las tablas ya diseñadas"), de modo que cualquier integrante del equipo pueda
repetir el proceso, entender las decisiones de diseño y continuar con las tareas
2 a 10 sin tener que reconstruir nada.

## 2. Alcance

- Solo se completó la **parte de base de datos** del Sprint 1, tal como se
  delimitó con el alumno solicitante. Las tareas 2 a 10 (conexión PDO,
  validaciones, formularios, aviso de privacidad y pruebas E2E) quedan para
  los integrantes correspondientes.
- La base queda definida con las tablas `usuarios` y `motocicletas` y, ademas,
  preparada (mediante comentarios DDL) para los modulos de los Sprint 2 y 3
  (QR, cuentas de guardia/administrador y bitácora de accesos).

## 3. Herramientas y entorno utilizados

| Herramienta | Version / Uso | Caso de uso en este avance |
|---|---|---|
| Git + GitHub | Repositorio `https://github.com/Leo-ia/SACAM` | Clonado, versionado y publicacion del entregable |
| PowerShell 7 | Shell del entorno (Win32) | Creacion de carpetas, marcadores `.gitkeep`, automatizacion |
| pdftotext (MiKTeX) | `C:\Program Files\MiKTeX\miktex\bin\x64\pdftotext.exe` | Extraccion de texto plano de `propuesta_cliente.pdf` (la sesion no admite PDF, pero el texto se extrajo localmente para leer la propuesta) |
| Script PowerShell + XML (OpenXML) | Extraccion de `word/document.xml` | Lectura del contenido de `sacam_sprint1.docx` (los `.docx` son ZIP con XML) |
| Docker Desktop | Motor de contenedores, version del daemon 29.5.2 | Levantar una instancia real de MariaDB 11.8.9 para **validar** el script SQL |
| MariaDB (imagen oficial) | `mariadb:11` (11.8.9-MariaDB-ubu2404) | Ejecucion del DDL y pruebas de integridad |
| GitHub (via HTTPS) | Credenciales del usuario | `git push`

### 3.1 Comandos clave ejecutados

```bash
# Clonado del repositorio
git clone https://github.com/Leo-ia/SACAM.git "<ruta>/SACAM"

# Extraccion de la propuesta del cliente (PDF → texto)
pdftotext -layout propuesta_cliente.pdf propuesta.txt

# Levantar MariaDB para validar el script (imagen oficial)
docker run --rm -d --name sacam-db -e MARIADB_ROOT_PASSWORD=toor_2026 -v "<ruta>/sacam:/schema:ro" mariadb:11

# Ejecutar el esquema dentro del contenedor
docker exec sacam-db mariadb -uroot -ptoor_2026 -e "source /schema/sacam_bd_sprint1.sql"

# Versionado y publicacion
git add -A
git commit -m "Sprint 1: estructura del proyecto y base de datos (tarea 1)"
git push origin main
```

Nota: el commit fue enmendado (amend) tras corregir el `.gitignore` para
incluir los marcadores `uploads/**/.gitkeep` y dejar la identidad de autor
configurada del usuario (`Leonardo Olivares <leolivares512@gmail.com>`).

## 4. Estructura de carpetas creada

```
SACAM/
├── app/
│   ├── includes/            # Tarea 2: conexion PDO · Tarea 3: validaciones
│   └── procesos/            # Tarea 6 y 8: backend de los formularios
├── config/                  # Credenciales de BD, fuera de la raiz publica
├── database/
│   └── sacam_bd_sprint1.sql # Tarea 1: esquema de la base de datos
├── docs/
│   └── sprint1/             # Analisis y evidencia del sprint
├── public/
│   ├── css/                 # Paleta IPN (guinda #750946, gris #636569)
│   └── js/
└── uploads/
    ├── credenciales/        # Fotos de credencial IPN (LGPDPPSO)
    └── motocicletas/        # Fotos de las motocicletas
```

**Razonamiento de la division.** La propuesta tecnica y el backlog del Sprint 1
exigen: (a) un unico archivo de conexion reutilizado por todo el sitio, (b)
funciones de validacion en un archivo aparte, (c) carpetas distintas para cada
tipo de archivo subido, y (d) la raiz publica separada de la logica interna
para no exponer configuraciones. Los directorios vacios se conservan con un
marcador `.gitkeep` porque Git no versiona carpetas sin contenido.

Las carpetas `uploads/` estan excluidas del repositorio en `.gitignore`:
contienen datos personales (fotos de credencial) y, conforme a la LGPDPPSO,
no deben versionarse.

## 5. Diseno de la base de datos

Archivo: `database/sacam_bd_sprint1.sql` (221 lineas).

### 5.1 Bloques del script y referencias

| Seccion | Lineas | Contenido |
|---|---|---|
| Encabezado y decisiones de diseno | 1-38 | Metadatos del proyecto y las 8 consideraciones de diseno |
| Creacion del schema | 40-50 | `CREATE DATABASE IF NOT EXISTS sacam` con utf8mb4 |
| Tabla `usuarios` | 52-97 | Formulario HU-02 (dueno de la moto) |
| Tabla `motocicletas` | 99-153 | Formulario HU-03 (moto, relacion 1:N) |
| Seguridad | 155-164 | Usuario de aplicacion con privilegios minimos (comentado) |
| Datos de ejemplo | 166-178 | Casos de prueba manual (comentados) |
| Modulos futuros | 180-221 | QR, cuentas de acceso y bitacora (comentados, Sprint 2+) |

### 5.2 Decisiones de diseno principales (justificacion)

1. **Motor InnoDB y codificacion utf8mb4.** El Sprint 1 exige una transaccion
   "todo o nada" (HU-04): si falla la moto, tambien se revierte el usuario.
   Eso solo es posible con InnoDB. `utf8mb4_unicode_ci` garantiza acentos
   del espanol y comparaciones correctas en nombres y correos.

2. **Fotos como ruta, no como BLOB.** Las imagenes se almacenan en `uploads/`
   y en la tabla solo se guarda la ruta relativa. Ventajas: base ligera,
   respaldos rapidos, y si la foto cambia no se reescribe el registro con
   datos binarios.

3. **Unicidad compuesta en el identificador.** La boleta de un alumno puede
   coincidir numericamente con el numero de empleado de un docente. Por eso
   la restriccion es `UNIQUE (tipo_persona, identificador_institucional)`
   (lineas 83-85) y no solo sobre el identificador.

4. **Un correo, una cuenta.** `UNIQUE (correo_electronico)` (lineas 87-89)
   impide duplicados; la colacion insensible a mayusculas hace que
   `ANA@correo.com` y `ana@correo.com` se traten como el mismo.

5. **Regla de negocio garantizada a nivel de BD.** La moto queda
   `pendiente_actualizacion` si y solo si el tramite es
   `permiso_provisional` (lineas 129-136). Se implemento como
   `CHECK (estado = 'pendiente_actualizacion') = (tipo_placa = 'permiso_provisional')`.
   Aunque la capa de aplicacion tambien validara (tareas 3 y 8), la base
   rechaza datos inconsistentes aunque alguien escriba SQL directo.

6. **Clave foranea con `ON DELETE CASCADE`.** Una moto sin dueno no tiene
   sentido (lineas 138-143). Si se elimina el usuario, se eliminan sus motos;
   nunca quedan filas huerfanas apuntando a un id inexistente.

7. **Auditoria automatica.** `created_at` y `updated_at` con
   `DEFAULT CURRENT_TIMESTAMP ... ON UPDATE CURRENT_TIMESTAMP` (lineas 77-79
   y 123-125) registran cuándo se creo y modifico cada fila sin escribir una
   sola linea de codigo en la aplicacion.

8. **Indices pensados para los proximos sprints.** `idx_usuarios_identificador`
   (lineas 95-97) prepara las busquedas del guardia por boleta/empleado;
   `idx_motocicletas_usuario_estado` (lineas 149-153) prepara "listar motos
   de un usuario" que usara el panel en el Sprint 2.

9. **Extensibilidad sin rediseno.** Las secciones de modulos futuros
   (lineas 180-221) ya dejan el `ALTER` de QR y el DDL de `cuentas_acceso`
   y `accesos` documentados y funcionales; se aplicaran en su sprint sin
   reconstruir el schema.

### 5.3 Mapeo con las historias de usuario

| Historia | Como se cubre en el schema |
|---|---|
| HU-02 (registro de usuario) | Tabla `usuarios` los 6 campos del formulario, incluida la licencia opcional (`licencia_permiso`, anulable) |
| HU-03 (registro de motocicleta) | Tabla `motocicletas`, incluido el campo `placa` que acepta permiso provisional en el mismo campo |
| HU-04 (persistencia confiable) | Motor InnoDB para transacciones + FK `ON DELETE CASCADE` |

## 6. Pruebas de validacion ejecutadas

Las pruebas se corrieron contra MariaDB 11.8.9 en contenedor Docker, con el
script real del repositorio.

| # | Caso probado | Resultado esperado | Resultado real |
|---|---|---|---|
| 1 | Ejecucion del DDL completo sobre BD vacia | Sin errores, 2 tablas creadas | OK (`SHOW TABLES`: `motocicletas`, `usuarios`) |
| 2 | Insert de usuario + moto (flujo feliz, con acentos) | Insercion acaptada | OK (`Ana Garcia Perez`, `Vento Fighter 180`) |
| 3 | Misma boleta con distinto tipo de persona | No debe chocar | OK (insert `docente` con mismo numero) |
| 4 | Correo duplicado | Rechazado | ERROR 1062 en `uq_usuarios_correo` |
| 5 | `permiso_provisional` con estado `activa` | Rechazado | ERROR 4025 en `chk_motocicletas_estado_tipo` |
| 6 | `con_placa` con estado `pendiente_actualizacion` | Rechazado | ERROR 4025 en `chk_motocicletas_estado_tipo` |
| 7 | Borrar usuario con motos asociadas | Motos eliminadas | OK (2 motos antes, 0 despues del `DELETE`) |
| 8 | Actualizar un registro | `updated_at` cambia solo | OK (00:36:20 → 00:36:30 en el UPDATE de prueba) |

Durante la primera pasada el `CHECK` (casos 5 y 6) dejo pasar la combinacion
invalida por una expresion mal formulada; se corrigio a una equivalencia
exacta `(pendiente) = (provisional)` y se repitio la suite completa sobre una
base limpia con resultado favorable. Ese es el punto exacto en el que una
prueba real sobre el motor (y no solo la revision visual del DDL) pago la
pena: si no se hubiera ejecutado, la regla habria quedado documentada pero no
cumplida.

## 7. Estado de versionado

| Commit | Descripcion |
|---|---|
| `6930cb4` | Initial commit (solo `README.md` de dos lineas) |
| `f6ffc61` | Sprint 1: estructura del proyecto y base de datos (tarea 1) |

Rama `main` publicada en `https://github.com/Leo-ia/SACAM`.

## 8. Como reproducir el entregable

Requisito: MySQL 8+ o MariaDB 10.2+ (las `CHECK CONSTRAINT` exigen esas
versiones).

```bash
# Opcion A: importar con el cliente MySQL en el destino final
mysql -u <usuario> -p < database/sacam_bd_sprint1.sql

# Opcion B: validar localmente con Docker (como se hizo en este avance)
docker run -d --name sacam-db -e MARIADB_ROOT_PASSWORD=<clave> -v "$PWD/database:/schema:ro" mariadb:11
docker exec sacam-db mariadb -uroot -p<clave> -e "source /schema/sacam_bd_sprint1.sql"
```

Despues de montarla, se puede comprobar la integridad con:

```sql
SHOW TABLES;
SHOW CREATE TABLE motocicletas\G
```

## 9. Estado del Sprint 1 y trabajo pendiente

**Completado:** Tarea 1 (base de datos).

**Pendiente para el resto del equipo:** Tareas 2 (conexion PDO reutilizable),
3 (validaciones), 4 (pagina de inicio), 5 y 7 (formularios frontend),
6 y 8 (backend con transaccion), 9 (aviso de privacidad LGPDPPSO) y 10
(pruebas manuales del flujo completo).

---

Documento generado como avance del Sprint 1.