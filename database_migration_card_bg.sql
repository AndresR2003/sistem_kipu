-- Fondo de las tarjetas en Configuracion visual

ALTER TABLE `configuracion_visual`
    ADD COLUMN `card_bg` VARCHAR(40) NOT NULL DEFAULT '#1a1a2e' AFTER `content_bg`;
