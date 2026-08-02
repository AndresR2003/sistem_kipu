CREATE TABLE IF NOT EXISTS `comentarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `borrador_id` INT NOT NULL,
    `usuario_id` INT DEFAULT NULL,
    `comentario` TEXT NOT NULL,
    `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_borrador` (`borrador_id`),
    INDEX `idx_usuario` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
