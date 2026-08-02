CREATE TABLE IF NOT EXISTS `borradores` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `titulo` VARCHAR(255) NOT NULL,
    `contenido` TEXT NULL,
    `etiqueta` VARCHAR(50) DEFAULT NULL,
    `fijado` TINYINT(1) DEFAULT 0,
    `usuario_id` INT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX `idx_usuario` (`usuario_id`),
    INDEX `idx_fijado` (`fijado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
