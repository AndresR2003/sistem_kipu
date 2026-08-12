-- Marca de la empresa cliente (logo y nombre) en Configuracion visual

ALTER TABLE `configuracion_visual`
    ADD COLUMN `marca_activa` TINYINT(1) NOT NULL DEFAULT 0 AFTER `card_bg`,
    ADD COLUMN `marca_nombre` VARCHAR(120) NULL AFTER `marca_activa`,
    ADD COLUMN `marca_logo` VARCHAR(255) NULL AFTER `marca_nombre`;