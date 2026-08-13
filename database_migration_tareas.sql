-- =====================================================
-- MIGRACION: Nuevo modulo de Tareas
-- Fecha: 2026-08-13
-- Descripcion: Crea tablas tareas y tarea_asignaciones
-- =====================================================

CREATE TABLE IF NOT EXISTS `tareas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `titulo` VARCHAR(255) NOT NULL,
    `descripcion` TEXT NULL,
    `prioridad` ENUM('alta','media','baja') NOT NULL DEFAULT 'media',
    `fecha_limite` DATETIME NULL COMMENT 'Fecha y hora limite de la tarea',
    `modalidad` ENUM('single_completes_all','all_must_complete') NOT NULL DEFAULT 'single_completes_all',
    `departamento_id` INT NULL COMMENT 'Agrupacion visual por departamento',
    `destinatario_tipo` VARCHAR(20) NOT NULL DEFAULT 'todos' COMMENT 'todos|usuarios|departamento',
    `destinatario_id` INT NULL,
    `created_by` INT NOT NULL COMMENT 'Admin que creo la tarea',
    `publicado` TINYINT(1) DEFAULT 0,
    `completada` TINYINT(1) DEFAULT 0 COMMENT 'Se marca 1 cuando se completa segun modalidad',
    `completada_por` INT NULL COMMENT 'ID usuario que completo (solo modalidad single)',
    `completada_at` DATETIME NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_tareas_dept` (`departamento_id`),
    INDEX `idx_tareas_fecha` (`fecha_limite`),
    INDEX `idx_tareas_publicado` (`publicado`),
    INDEX `idx_tareas_created_by` (`created_by`),
    CONSTRAINT `fk_tareas_dept` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_tareas_creador` FOREIGN KEY (`created_by`) REFERENCES `admin_usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS `tarea_asignaciones` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `tarea_id` INT NOT NULL,
    `usuario_id` INT NOT NULL,
    `completado` TINYINT(1) DEFAULT 0,
    `completado_at` DATETIME NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_tarea_usuario` (`tarea_id`, `usuario_id`),
    CONSTRAINT `fk_tasig_tarea` FOREIGN KEY (`tarea_id`) REFERENCES `tareas`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_tasig_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `admin_usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Agregar columna tarea_id a comentarios para vincular comentarios a tareas
ALTER TABLE `comentarios` ADD COLUMN `tarea_id` INT NULL DEFAULT NULL AFTER `entrega_id`;
ALTER TABLE `comentarios` ADD INDEX `idx_comentarios_tarea` (`tarea_id`);
