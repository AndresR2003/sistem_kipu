-- Tabla de recordatorios
CREATE TABLE IF NOT EXISTS `recordatorios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `titulo` VARCHAR(255) NOT NULL,
    `descripcion` TEXT NULL,
    `fecha` DATETIME NOT NULL,
    `prioridad` ENUM('baja','media','alta') DEFAULT 'media',
    `completado` TINYINT(1) DEFAULT 0,
    `usuario_id` INT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_fecha` (`fecha`),
    INDEX `idx_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
