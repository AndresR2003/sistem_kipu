-- =====================================================
-- MIGRACION: Invitados de eventos del calendario
-- Fecha: 2026-08-15
-- Descripcion: Permite invitar VARIOS departamentos y/o
--              VARIOS usuarios a un evento del calendario.
-- =====================================================

-- 1) Pivot departamentos invitados a eventos
CREATE TABLE IF NOT EXISTS `evento_invitados_departamentos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `evento_id` INT NOT NULL,
    `departamento_id` INT NOT NULL,
    UNIQUE KEY `uk_evento_dept` (`evento_id`, `departamento_id`),
    CONSTRAINT `fk_edept_evento` FOREIGN KEY (`evento_id`) REFERENCES `eventos`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_edept_departamento` FOREIGN KEY (`departamento_id`) REFERENCES `departamentos`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2) Pivot usuarios invitados a eventos
CREATE TABLE IF NOT EXISTS `evento_invitados_usuarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `evento_id` INT NOT NULL,
    `usuario_id` INT NOT NULL,
    UNIQUE KEY `uk_evento_usuario` (`evento_id`, `usuario_id`),
    CONSTRAINT `fk_eusr_evento` FOREIGN KEY (`evento_id`) REFERENCES `eventos`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_eusr_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `admin_usuarios`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
