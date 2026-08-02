ALTER TABLE `recordatorios` ADD COLUMN `tipo` VARCHAR(20) DEFAULT 'recordatorio' AFTER `usuario_id`, ADD INDEX `idx_tipo` (`tipo`);
