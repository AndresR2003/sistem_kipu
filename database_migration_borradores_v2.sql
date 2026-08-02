-- Agregar columnas a borradores para publicacion
ALTER TABLE `borradores`
  ADD COLUMN `seccion_destino` VARCHAR(50) DEFAULT NULL COMMENT 'noticias|ideas|manual|tareas' AFTER `fijado`,
  ADD COLUMN `destinatario_tipo` VARCHAR(20) DEFAULT 'todos' COMMENT 'todos|usuarios|departamento' AFTER `seccion_destino`,
  ADD COLUMN `destinatario_id` INT DEFAULT NULL AFTER `destinatario_tipo`,
  ADD COLUMN `publicado` TINYINT(1) DEFAULT 0 AFTER `destinatario_id`,
  ADD INDEX `idx_publicado` (`publicado`),
  ADD INDEX `idx_seccion` (`seccion_destino`);

-- Tabla departamentos (si no existe)
CREATE TABLE IF NOT EXISTS `departamentos` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `descripcion` VARCHAR(100) NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
