ALTER TABLE `chat_mensajes`
    ADD COLUMN `destinatario_id` INT(11) DEFAULT NULL AFTER `usuario_id`,
    ADD KEY `idx_chat_destinatario` (`destinatario_id`);
