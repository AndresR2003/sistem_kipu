-- =====================================================
-- Entregas / Pases de turno
-- =====================================================

-- Tabla de tareas predeterminadas
CREATE TABLE IF NOT EXISTS `entregas` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `titulo` VARCHAR(255) NOT NULL,
    `descripcion` TEXT NULL,
    `repetir_diario` TINYINT(1) DEFAULT 1,
    `fecha_inicio` DATE NOT NULL,
    `fecha_fin` DATE NULL,
    `publicado` TINYINT(1) DEFAULT 1,
    `created_by` INT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de registros de ejecucion (quien realizo la tarea)
CREATE TABLE IF NOT EXISTS `entrega_registros` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `entrega_id` INT NOT NULL,
    `usuario_id` INT NULL,
    `fecha` DATE NOT NULL,
    `completado_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    UNIQUE KEY `uk_entrega_usuario_fecha` (`entrega_id`, `usuario_id`, `fecha`),
    KEY `idx_fecha` (`fecha`),
    CONSTRAINT `fk_entrega_registro_entrega` FOREIGN KEY (`entrega_id`) REFERENCES `entregas`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
