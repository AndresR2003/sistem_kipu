-- Agregar columnas de origen y seccion a recordatorios
ALTER TABLE `recordatorios`
  ADD COLUMN `origen_id` INT NULL AFTER `tipo`,
  ADD COLUMN `origen_tipo` VARCHAR(20) NULL AFTER `origen_id`,
  ADD COLUMN `seccion` VARCHAR(50) NULL AFTER `origen_tipo`,
  ADD INDEX `idx_origen` (`origen_id`, `origen_tipo`),
  ADD INDEX `idx_seccion` (`seccion`);
