USE sacam;

ALTER TABLE usuarios
    ADD COLUMN activo BOOLEAN NOT NULL DEFAULT TRUE
        AFTER licencia_permiso;