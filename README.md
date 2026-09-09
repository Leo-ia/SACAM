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
| 4 | Página de inicio (HTML/CSS colores IPN)        | —    | Pendiente |
| 5 | Formulario de registro de usuario (frontend)   | 4    | Pendiente |
| 6 | Procesamiento backend del formulario de usuario | 2,3,5 | Pendiente |
| 7 | Formulario de registro de moto (frontend)       | 6    | Pendiente |
| 8 | Procesamiento backend del formulario de moto (transacción) | 2,3,7 | Pendiente |
| 9 | Aviso de privacidad simplificado (LGPDPPSO)    | 5,7  | Pendiente |
| 10 | Pruebas manuales del flujo completo            | todas | Pendiente |

**Historias de usuario:** HU-01 página de inicio · HU-02 registro de usuario · HU-03 registro de motocicleta · HU-04 persistencia confiable (transacción todo-o-nada).

## Estructura de carpetas

```
sacam/
├── app/
│   ├── includes/          # Tarea 2: conexión PDO · Tarea 3: validaciones
│   └── procesos/          # Backend de formularios (tareas 6 y 8)
├── config/                # Configuración (credenciales fuera de public/)
├── database/
│   └── sacam_bd_sprint1.sql   # ← Tarea 1 · esquema de la BD (entregable actual)
├── docs/
│   └── sprint1/           # Análisis y evidencia del sprint
├── public/                # Raíz pública del servidor web (document root)
│   ├── css/               # Colores IPN: guinda #750946, gris #636569
│   ├── js/
│   └── …páginas (inicio, registro_usuario, registro_moto, confirmación)
└── uploads/               # Archivos subidos (fuera del control de versiones)
    ├── credenciales/      # Fotos de credencial IPN (LGPDPPSO)
    └── motocicletas/      # Fotos de las motos
```

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

## Definition of Done (Sprint 1)

- El formulario correspondiente guarda correctamente en la BD.
- Validaciones de servidor (no solo de cliente) funcionando.
- Imágenes guardadas con nombre único en la carpeta correcta.
- Probado manualmente: caso exitoso, campo obligatorio vacío, imagen inválida y permiso provisional sin placa.

## Privacidad

El IPN, al ser sujeto obligado por la LGPDPPSO, debe informar qué datos se recaban antes de recabarlos. Por eso los formularios del sprint incluyen un **aviso de privacidad simplificado** con checkbox de aceptación, y las fotos personales **nunca se versionan** en este repositorio (ver `.gitignore`).