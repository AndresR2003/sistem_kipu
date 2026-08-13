-- =====================================================
-- MIGRACION: Publicaciones multi-destino
-- Fecha: 2026-08-13
-- Descripcion: Permite seleccionar VARIOS departamentos y
--              VARIOS usuarios al publicar borradores y tareas.
--              Agrega columnas borrador_id y tablas pivot.
-- =====================================================

-- 1) Pivot departamentos de borradores (publicacion visible en varios deptos)
CREATE TABLE IF NOT EXISTS `borrador_departamentos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `borrador_id` INT NOT NULL,
    `departamento_id` INT NOT NULL,
    UNIQUE KEY `uk_borrador_dept` (`borrador_id`, `departamento_id`),
    CONSTRAINT `fk_bdep_borrador` FOREIGN KEY (`borrador_id`) REFERENCES `borradores`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_bdep_departamento` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2) Pivot usuarios de borradores (publicacion visible para varios usuarios)
CREATE TABLE IF NOT EXISTS `borrador_usuarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `borrador_id` INT NOT NULL,
    `usuario_id` INT NOT NULL,
    UNIQUE KEY `uk_borrador_usuario` (`borrador_id`, `usuario_id`),
    CONSTRAINT `fk_busr_borrador` FOREIGN KEY (`borrador_id`) REFERENCES `borradores`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_busr_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `admin_usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3) Pivot departamentos de tareas (una tarea puede pertenecer a varios deptos)
CREATE TABLE IF NOT EXISTS `tarea_departamentos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tarea_id` INT NOT NULL,
    `departamento_id` INT NOT NULL,
    UNIQUE KEY `uk_tarea_dept` (`tarea_id`, `departamento_id`),
    CONSTRAINT `fk_tdep_tarea` FOREIGN KEY (`tarea_id`) REFERENCES `tareas`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tdep_departamento` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4) Vincular tareas con su borrador de origen
ALTER TABLE `tareas` ADD COLUMN `borrador_id` INT NULL DEFAULT NULL AFTER `id`;
ALTER TABLE `tareas` ADD INDEX `idx_tareas_borrador` (`borrador_id`);

-- 5) Backfill: tareas existentes heredan su departamento unico al pivot
INSERT IGNORE INTO `tarea_departamentos` (`tarea_id`, `departamento_id`)
SELECT `id`, `departamento_id` FROM `tareas` WHERE `departamento_id` IS NOT NULL;

-- 6) Backfill: borradores existentes con destinatario unico pasan al pivot
INSERT IGNORE INTO `borrador_departamentos` (`borrador_id`, `departamento_id`)
SELECT `id`, `destinatario_id` FROM `borradores` WHERE `destinatario_tipo` = 'departamento' AND `destinatario_id` IS NOT NULL;

INSERT IGNORE INTO `borrador_usuarios` (`borrador_id`, `usuario_id`)
SELECT `id`, `destinatario_id` FROM `borradores` WHERE `destinatario_tipo` = 'usuarios' AND `destinatario_id` IS NOT NULL;
