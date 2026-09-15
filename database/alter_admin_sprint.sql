-- =============================================================================
-- SACAM · Cambio de esquema para el perfil de administrador
-- =============================================================================
-- Proyecto:   SACAM
-- Sprint:     3 (avance parcial · administrador)
-- Archivo:    `alter_admin_sprint.sql`
-- Motor:      MySQL 8+ / MariaDB 10.2+
-- -----------------------------------------------------------------------------
-- QUÉ HACE
--   Agrega a `usuarios` la columna `activo`, que permite al administrador
--   revocar el acceso de un usuario sin borrar sus datos (paso 4 del avance
--   de administrador). Un usuario con `activo = 0` sigue existiendo en la
--   base (sus motocicletas y su historial se conservan) pero pierde su
--   condición de activo/vigente.
--
-- CÓMO APLICARLO
--   Después de haber montado `sacam_bd_sprint1.sql`:
--     mysql -u usuario -p sacam < database/alter_admin_sprint.sql
--
-- NOTA
--   `sacam_bd_sprint1.sql` NO se modifica: este script es un parche aparte
--   para no reconstruir la base existente.
-- =============================================================================

USE sacam;

ALTER TABLE usuarios
    ADD COLUMN activo BOOLEAN NOT NULL DEFAULT TRUE
        COMMENT 'TRUE = acceso vigente · FALSE = acceso revocado por el administrador'
        AFTER licencia_permiso;