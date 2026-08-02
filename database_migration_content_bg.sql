-- Fondo del contenido (vistas) en Configuracion visual

ALTER TABLE `configuracion_visual`
    ADD COLUMN `content_bg` VARCHAR(40) NOT NULL DEFAULT '#0f0f1a' AFTER `primary_color`;
