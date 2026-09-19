-- Campos usados por la API movil y almacenamiento de tokens FCM.
-- Ejecutar despues de database.sql y database_migration_empleados.sql.

ALTER TABLE `admin_usuarios`
  ADD COLUMN `direccion` VARCHAR(255) DEFAULT NULL AFTER `telefono`,
  ADD COLUMN `observacion` TEXT DEFAULT NULL AFTER `direccion`;

CREATE TABLE IF NOT EXISTS `fcm_tokens` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `user_id` INT(11) NOT NULL,
    `user_name` VARCHAR(100) DEFAULT NULL,
    `token` VARCHAR(512) NOT NULL,
    `is_active` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `updated_at` DATETIME DEFAULT NULL,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_fcm_token` (`token`),
    KEY `idx_fcm_user` (`user_id`),
    CONSTRAINT `fk_fcm_user` FOREIGN KEY (`user_id`)
        REFERENCES `admin_usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
