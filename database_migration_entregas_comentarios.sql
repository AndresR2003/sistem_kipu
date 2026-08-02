-- Comentarios en tareas diarias (entregas)
-- Permite que la tabla 'comentarios' guarde comentarios tanto de borradores como de tareas diarias.

ALTER TABLE `comentarios`
    MODIFY `borrador_id` INT NULL DEFAULT NULL,
    ADD COLUMN `entrega_id` INT NULL DEFAULT NULL AFTER `borrador_id`,
    ADD INDEX `idx_entrega` (`entrega_id`);
