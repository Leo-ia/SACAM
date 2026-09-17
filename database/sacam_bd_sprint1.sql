-- DROP DATABASE IF EXISTS sacam;

CREATE DATABASE IF NOT EXISTS sacam
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE sacam;

CREATE TABLE IF NOT EXISTS usuarios
(
    id                      BIGINT UNSIGNED            NOT NULL AUTO_INCREMENT,
    tipo_persona            ENUM('alumno',
                                 'docente',
                                 'administrativo',
                                 'intendencia',
                                 'otro')               NOT NULL,
    identificador_institucional
                            VARCHAR(20)                 NOT NULL,
    nombre_completo         VARCHAR(120)                NOT NULL,
    correo_electronico      VARCHAR(190)                NOT NULL,
    foto_credencial         VARCHAR(255)                NOT NULL,
    licencia_permiso        VARCHAR(30)                 NULL,
    created_at              TIMESTAMP                   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at              TIMESTAMP                   NOT NULL DEFAULT CURRENT_TIMESTAMP
                                                                ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    CONSTRAINT uq_usuarios_tipo_identificador
        UNIQUE (tipo_persona, identificador_institucional),
    CONSTRAINT uq_usuarios_correo
        UNIQUE (correo_electronico)
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE INDEX idx_usuarios_identificador
    ON usuarios (identificador_institucional);

CREATE TABLE IF NOT EXISTS motocicletas
(
    id                      BIGINT UNSIGNED            NOT NULL AUTO_INCREMENT,
    usuario_id              BIGINT UNSIGNED            NOT NULL,
    marca                   VARCHAR(50)                 NOT NULL,
    modelo                  VARCHAR(50)                 NOT NULL,
    color                   VARCHAR(50)                 NOT NULL,
    placa                   VARCHAR(20)                 NOT NULL,
    tipo_placa              ENUM('con_placa',
                                 'permiso_provisional') NOT NULL DEFAULT 'con_placa',
    estado                  ENUM('activa',
                                 'pendiente_actualizacion') NOT NULL DEFAULT 'activa',
    foto                    VARCHAR(255)                NOT NULL,
    created_at              TIMESTAMP                   NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at              TIMESTAMP                   NOT NULL DEFAULT CURRENT_TIMESTAMP
                                                                ON UPDATE CURRENT_TIMESTAMP,

    PRIMARY KEY (id),
    CONSTRAINT chk_motocicletas_estado_tipo
        CHECK ((estado = 'pendiente_actualizacion')
               =
               (tipo_placa = 'permiso_provisional')),
    CONSTRAINT fk_motocicletas_usuario
        FOREIGN KEY (usuario_id) REFERENCES usuarios (id)
        ON UPDATE CASCADE
        ON DELETE CASCADE
) ENGINE = InnoDB
  DEFAULT CHARACTER SET = utf8mb4
  COLLATE = utf8mb4_unicode_ci;

CREATE INDEX idx_motocicletas_usuario_estado
    ON motocicletas (usuario_id, estado);