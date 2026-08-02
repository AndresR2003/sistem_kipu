-- =====================================================
-- Asignacion de tareas de entregas por usuario/departamento
-- =====================================================

ALTER TABLE `entregas`
    ADD COLUMN `destinatario_tipo` VARCHAR(20) NOT NULL DEFAULT 'todos' AFTER `created_by`,
    ADD COLUMN `destinatario_id` INT NULL AFTER `destinatario_tipo`,
    ADD KEY `idx_destinatario` (`destinatario_tipo`, `destinatario_id`);
