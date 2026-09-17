-- Pestañas del sidebar deshabilitadas por el superadmin (JSON array de claves)
-- Ejemplo: ["ideas","manual"]

ALTER TABLE `configuracion_visual`
    ADD COLUMN `menu_disabled` TEXT NULL;
