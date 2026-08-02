-- Asignar recordatorios existentes (sin dueno) al usuario admin
UPDATE `recordatorios` SET `usuario_id` = 1 WHERE `usuario_id` IS NULL;
