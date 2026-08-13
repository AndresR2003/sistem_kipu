-- Agregar solo la columna seccion a recordatorios
ALTER TABLE `recordatorios`
  ADD COLUMN `seccion` VARCHAR(50) NULL AFTER `origen_tipo`,
  ADD INDEX `idx_seccion` (`seccion`);
