-- Tiempo de inactividad de sesion configurable en Configuracion visual

ALTER TABLE `configuracion_visual`
    ADD COLUMN `session_idle_minutes` INT NOT NULL DEFAULT 10 AFTER `marca_logo`;
