CREATE TABLE IF NOT EXISTS `chat_lecturas` (
    `mensaje_id` INT UNSIGNED NOT NULL,
    `usuario_id` INT(11) NOT NULL,
    `leido_en` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`mensaje_id`, `usuario_id`),
    KEY `idx_chat_lectura_usuario` (`usuario_id`),
    CONSTRAINT `fk_chat_lectura_mensaje` FOREIGN KEY (`mensaje_id`) REFERENCES `chat_mensajes` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_chat_lectura_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `admin_usuarios` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
