CREATE TABLE IF NOT EXISTS `chat_mensajes` (
    `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
    `usuario_id` INT(11) NOT NULL,
    `destinatario_id` INT(11) DEFAULT NULL,
    `mensaje` VARCHAR(2000) NOT NULL DEFAULT '',
    `archivo_nombre` VARCHAR(255) DEFAULT NULL,
    `archivo_ruta` VARCHAR(255) DEFAULT NULL,
    `archivo_mime` VARCHAR(120) DEFAULT NULL,
    `archivo_tamano` INT UNSIGNED DEFAULT NULL,
    `creado_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    KEY `idx_chat_creado_en` (`creado_en`),
    KEY `idx_chat_usuario` (`usuario_id`),
    KEY `idx_chat_destinatario` (`destinatario_id`),
    CONSTRAINT `fk_chat_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `admin_usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
