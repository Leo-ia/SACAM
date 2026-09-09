-- =============================================================================
-- SACAM · Sistema Automatizado de Control de Acceso a Estacionamientos de
--        Motocicletas (ESCOM · IPN)
-- =============================================================================
-- Proyecto:        SACAM
-- Equipo:          Dev Core Systems, S.A.S. de C.V.
-- Materia:         Análisis y Diseño de Sistemas Digitales · 2027-1
-- Sprint:          1 (Historia de usuario HU-04 · Tarea 1 del Backlog)
-- Entregable:      `sacam_bd_sprint1.sql`
-- Motor:           MySQL 8+ / MariaDB 10.2+ (InnoDB)
-- Codificación:    utf8mb4 · utf8mb4_unicode_ci (acentos del español seguros)
-- -----------------------------------------------------------------------------
-- ALCANCE DE ESTE ARCHIVO
--   Crea la base de datos con las tablas `usuarios` y `motocicletas`
--   (relación 1:N usuario → moto) más todas sus restricciones e índices.
--
-- CONSIDERACIONES DE DISEÑO (GURU DB)
--   1. InnoDB: obligatorio para soportar la transacción "todo o nada" de la
--      HU-04 (usuario + moto se guardan juntos o no se guarda nada).
--   2. NORMALIZACIÓN: las fotos NO se guardan como BLOB dentro de la BD.
--      En la tabla solo se almacena la RUTA del archivo (upload/…), lo que
--      mantiene la base pequeña, ágil y fácil de respaldar.
--   3. INTEGRIDAD POR CLAVE FORÁNEA: una moto sin dueño no existe
--      (FK + ON DELETE CASCADE). Se evita que el usuario quede "huérfano"
--      con motos colgadas.
--   4. IDENTIFICADOR ÚNICO POR TIPO DE PERSONA: la boleta de un alumno puede
--      coincidir numéricamente con el número de empleado de un docente, por
--      eso la unicidad es COMPUESTA (tipo_persona, identificador_institucional).
--   5. CORREO ÚNICO: ninguna cuenta se registra dos veces con el mismo correo
--      (la colación utf8mb4_unicode_ci ya lo vuelve insensible a mayúsculas).
--   6. REGLA DE NEGOCIO GARANTIZADA EN LA BD: una moto no puede quedar
--      marcada como "pendiente de actualizar" si su placa es definitiva
--      (CHECK CONSTRAINT en `motocicletas`).
--   7. AUDITORÍA: `created_at` y `updated_at` se actualizan solos (TIMESTAMP).
--   8. ESCALABLE SIN REDISEÑO: el esquema está listo para que los sprints 2+
--      agreguen QR, cuentas de guardia/administrador y bitácora (ver sección
--      "MÓDULOS FUTUROS", comentada, al final del archivo).
-- =============================================================================

-- -----------------------------------------------------------------------------
-- 0. CREAR LA BASE DE DATOS
--    Si quieres rehacerla desde cero, descomenta la línea del DROP.
-- -----------------------------------------------------------------------------
-- DROP DATABASE IF EXISTS sacam;

CREATE DATABASE IF NOT EXISTS sacam
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE sacam;

-- =============================================================================
-- TABLA: usuarios
-- -----------------------------------------------------------------------------
-- Representa al DUEÑO de la motocicleta (miembro de la comunidad politécnica).
-- Campos del formulario HU-02 mapeados 1:1 con las reglas de negocio.
-- =============================================================================
CREATE TABLE IF NOT EXISTS usuarios
(
    id                      BIGINT UNSIGNED            NOT NULL AUTO_INCREMENT,
    tipo_persona            ENUM('alumno',             -- alumno       → boleta
                                 'docente',            -- docente      → n° empleado
                                 'administrativo',     -- administrativo→ n° empleado
                                 'intendencia',        -- intendencia  → n° empleado
                                 'otro')               -- otro         → n° empleado
                            NOT NULL                    COMMENT 'Determina qué identificador se captura',
    identificador_institucional
                            VARCHAR(20)                 NOT NULL
                            COMMENT 'Boleta (alumno) o número de empleado (cualquier otro tipo de persona)',
    nombre_completo         VARCHAR(120)                NOT NULL,
    correo_electronico      VARCHAR(190)                NOT NULL
                            COMMENT 'Cualquier correo válido; no se exige dominio institucional',
    foto_credencial         VARCHAR(255)                NOT NULL
                            COMMENT 'Ruta relativa del archivo subido (uploads/credenciales/<id>_…). Como el IPN es sujeto obligado LGPDPPSO, se guarda ruta, no se duplica el archivo en la BD',
    licencia_permiso        VARCHAR(30)                 NULL
                            COMMENT 'Número de licencia o permiso de conducir. OPCIONAL e informativo: el sistema NO valida vigencia ni autenticidad',
    created_at              TIMESTAMP                   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at              TIMESTAMP                   NOT NULL DEFAULT CURRENT_TIMESTAMP
                                                                    ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    -- Regla 4: la misma boleta/empleado no se registra dos veces dentro de su tipo.
    CONSTRAINT uq_usuarios_tipo_identificador
        UNIQUE (tipo_persona, identificador_institucional),

    -- Regla 5: el correo es única llave de identidad de la cuenta.
    CONSTRAINT uq_usuarios_correo
        UNIQUE (correo_electronico)
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  COMMENT = 'Dueños de motocicleta (HU-02)';

-- Índice para búsquedas del guardia/administrador por identificador (sprints 2+).
CREATE INDEX idx_usuarios_identificador
    ON usuarios (identificador_institucional);

-- =============================================================================
-- TABLA: motocicletas
-- -----------------------------------------------------------------------------
-- Cada motocicleta pertenece SIEMPRE a un usuario ya registrado (relación 1:N,
-- criterio de aceptación de la HU-03).
-- =============================================================================
CREATE TABLE IF NOT EXISTS motocicletas
(
    id                      BIGINT UNSIGNED            NOT NULL AUTO_INCREMENT,
    usuario_id              BIGINT UNSIGNED            NOT NULL
                            COMMENT 'Propietario (FK → usuarios.id)',
    marca                   VARCHAR(50)                 NOT NULL,
    modelo                  VARCHAR(50)                 NOT NULL,
    color                   VARCHAR(50)                 NOT NULL,
    placa                   VARCHAR(20)                 NOT NULL
                            COMMENT 'Placa definitiva o, si la moto aún no la tiene, número del permiso provisional (regla 5)',
    tipo_placa              ENUM('con_placa',           -- placa definitiva
                                 'permiso_provisional') NOT NULL DEFAULT 'con_placa'
                            COMMENT 'Regla 5: si es provisional, la moto queda marcada para actualización posterior',
    estado                  ENUM('activa',              -- registro completo y vigente
                                 'pendiente_actualizacion') NOT NULL DEFAULT 'activa'
                            COMMENT 'Permite identificarla visualmente como pendiente de actualizar',
    foto                    VARCHAR(255)                NOT NULL
                            COMMENT 'Ruta relativa del archivo subido (uploads/motocicletas/<id>_…)',
    created_at              TIMESTAMP                   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at              TIMESTAMP                   NOT NULL DEFAULT CURRENT_TIMESTAMP
                                                                    ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),

    -- Regla de negocio 6 garantizada a nivel BD (equivalencia exacta):
    --   estado = 'pendiente_actualizacion'  ⟺  tipo_placa = 'permiso_provisional'
    -- Es decir, una moto queda pendiente SI Y SOLO SI tiene permiso provisional.
    -- (MySQL 8.0.16+ / MariaDB 10.2+ la hacen cumplir en escrituras directas.)
    CONSTRAINT chk_motocicletas_estado_tipo
        CHECK ((estado = 'pendiente_actualizacion')
               =
               (tipo_placa = 'permiso_provisional')),

    -- La moto pertenece a un dueño real; si el dueño se elimina, se eliminan
    -- sus motos con él (no quedan registros huérfanos).
    CONSTRAINT fk_motocicletas_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci
  COMMENT = 'Motocicletas registradas por cada usuario (HU-03)';

-- InnoDB ya crea el índice del FK, pero se declara explícitamente para que el
-- optimizador lo use también en consultas por (usuario_id, estado) del sprint 2
-- ("consultar motos de un usuario"), en vez de hacer recorridos completos.
CREATE INDEX idx_motocicletas_usuario_estado
    ON motocicletas (usuario_id, estado);

-- =============================================================================
-- SEGURIDAD (BUENA PRÁCTICA, descomentar si el hosting lo permite)
-- -----------------------------------------------------------------------------
-- El usuario de la aplicación NO necesita privilegios de DDL ni DROP globales.
-- En Hostinger normalmente la BD y el usuario van juntos y no se puede cambiar
-- los permisos; esta sección queda documentada para ambientes donde sí.
-- -----------------------------------------------------------------------------
-- CREATE USER IF NOT EXISTS 'sacam_app'@'localhost' IDENTIFIED BY 'cambia_esta_contrasena';
-- GRANT SELECT, INSERT, UPDATE, DELETE ON sacam.* TO 'sacam_app'@'localhost';
-- FLUSH PRIVILEGES;

-- =============================================================================
-- DATOS DE EJEMPLO (para pruebas manuales; descomentar si se requieren)
-- -----------------------------------------------------------------------------
-- INSERT INTO usuarios (tipo_persona, identificador_institucional, nombre_completo,
--                       correo_electronico, foto_credencial, licencia_permiso)
-- VALUES ('alumno', '2023B1234', 'Ana García Pérez', 'ana@correo.com', 'credenciales/1_credencial.jpg', 'PER-123456');
--
-- INSERT INTO motocicletas (usuario_id, marca, modelo, color, placa, tipo_placa, estado, foto)
-- VALUES (1, 'Vento', 'Fighter 180', 'Negro', 'FGH-123-A', 'con_placa', 'activa', 'motocicletas/1_moto.jpg');
--
-- INSERT INTO motocicletas (usuario_id, marca, modelo, color, placa, tipo_placa, estado, foto)
-- VALUES (1, 'Italika', 'FT 150', 'Rojo', 'PERM-000987', 'permiso_provisional', 'pendiente_actualizacion', 'motocicletas/2_moto.jpg');
-- =============================================================================

-- =============================================================================
-- MÓDULOS FUTUROS (Sprint 2 en adelante) — NO DESCOMENTAR EN ESTE SPRINT
-- -----------------------------------------------------------------------------
-- El diseño NO se rediseñará cuando lleguen estos módulos; solo se ejecutarán
-- estos CREATE en su sprint correspondiente. Se dejan aquí documentados para
-- que las tareas 1–3 del siguiente sprint no dependan de reconstruir la BD.
-- -----------------------------------------------------------------------------

-- Sprint 2: QR único por motocicleta (endroid/qr-code) y cuentas de acceso.
--   ALTER TABLE motocicletas
--       ADD COLUMN qr_slug VARCHAR(24) NULL UNIQUE COMMENT 'Identificador único del QR (generado por el backend)',
--       ADD COLUMN qr_ruta  VARCHAR(255) NULL COMMENT 'Ruta relativa de la imagen del QR',
--       ADD INDEX idx_motocicletas_qr (qr_slug);

-- Sprint 2: perfiles guardia / administrador (el propietario NO requiere contraseña).
--   CREATE TABLE cuentas_acceso (
--       id                BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
--       rol               ENUM('guardia','administrador') NOT NULL,
--       usuario_cuenta    VARCHAR(60) NOT NULL UNIQUE,
--       password_hash     VARCHAR(255) NOT NULL,
--       nombre            VARCHAR(120) NOT NULL,
--       activo            BOOLEAN NOT NULL DEFAULT TRUE,
--       created_at        TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
--       PRIMARY KEY (id)
--   ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Sprint 2/3: bitácora trazable de entradas y salidas.
--   CREATE TABLE accesos (
--       id             BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
--       motocicleta_id BIGINT UNSIGNED NOT NULL,
--       tipo           ENUM('entrada','salida','incidencia') NOT NULL,
--       guardia_id     BIGINT UNSIGNED NULL,
--       accedido_at    TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
--       detalle        VARCHAR(255) NULL COMMENT 'Descripción de incidentes/discrepancias',
--       PRIMARY KEY (id),
--       CONSTRAINT fk_accesos_moto  FOREIGN KEY (motocicleta_id) REFERENCES motocicletas(id)
--           ON UPDATE CASCADE ON DELETE CASCADE,
--       CONSTRAINT fk_accesos_guardia FOREIGN KEY (guardia_id) REFERENCES cuentas_acceso(id)
--           ON UPDATE CASCADE ON DELETE SET NULL
--   ) ENGINE=InnoDB DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================= FIN DEL SCRIPT ================================