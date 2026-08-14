-- =====================================================
-- MIGRACION: Pases de turno (intercambio de informacion entre turnos)
-- Fecha: 2026-08-13
-- Descripcion: Nuevo modulo de pases de turno orientado a transmitir
--              informacion/pendientes de un turno al siguiente.
--              Tablas: turnos (catalogo), pases_turno, pase_puntos,
--              pase_punto_comentarios. Los puntos pueden convertirse en
--              tareas del modulo Tareas (columna tarea_id).
-- =====================================================

-- 1) Catalogo de turnos
CREATE TABLE IF NOT EXISTS `turnos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(80) NOT NULL,
    `descripcion` VARCHAR(255) DEFAULT NULL,
    `orden` INT NOT NULL DEFAULT 0,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 2) Pases de turno (cabecera: De turno -> A turno)
CREATE TABLE IF NOT EXISTS `pases_turno` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `titulo` VARCHAR(160) DEFAULT NULL,
    `de_turno_id` INT NOT NULL,
    `a_turno_id` INT NOT NULL,
    `fecha` DATE NOT NULL,
    `estado` ENUM('abierto','cerrado') NOT NULL DEFAULT 'abierto',
    `creado_por` INT NOT NULL,
    `cerrado_por` INT DEFAULT NULL,
    `cerrado_at` DATETIME DEFAULT NULL,
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    KEY `idx_pases_fecha` (`fecha`),
    KEY `idx_pases_estado` (`estado`),
    CONSTRAINT `fk_pase_de_turno` FOREIGN KEY (`de_turno_id`) REFERENCES `turnos`(`id`),
    CONSTRAINT `fk_pase_a_turno` FOREIGN KEY (`a_turno_id`) REFERENCES `turnos`(`id`),
    CONSTRAINT `fk_pase_creador` FOREIGN KEY (`creado_por`) REFERENCES `admin_usuarios`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 3) Puntos/items dentro de un pase de turno (agrupados por area/departamento)
CREATE TABLE IF NOT EXISTS `pase_puntos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `pase_id` INT NOT NULL,
    `area_id` INT DEFAULT NULL COMMENT 'Departamento/area al que pertenece el punto (NULL = general)',
    `contenido` TEXT NOT NULL,
    `estado` ENUM('pendiente','revisado','completado') NOT NULL DEFAULT 'pendiente',
    `creado_por` INT NOT NULL,
    `actualizado_por` INT DEFAULT NULL,
    `tarea_id` INT DEFAULT NULL COMMENT 'Tarea creada a partir de este punto (modulo Tareas)',
    `created_at` DATETIME DEFAULT NULL,
    `updated_at` DATETIME DEFAULT NULL,
    KEY `idx_puntos_pase` (`pase_id`),
    KEY `idx_puntos_area` (`area_id`),
    KEY `idx_puntos_tarea` (`tarea_id`),
    CONSTRAINT `fk_punto_pase` FOREIGN KEY (`pase_id`) REFERENCES `pases_turno`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_punto_area` FOREIGN KEY (`area_id`) REFERENCES `departamentos`(`id`) ON DELETE SET NULL,
    CONSTRAINT `fk_punto_creador` FOREIGN KEY (`creado_por`) REFERENCES `admin_usuarios`(`id`),
    CONSTRAINT `fk_punto_tarea` FOREIGN KEY (`tarea_id`) REFERENCES `tareas`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 4) Comentarios/respuestas por punto del pase
CREATE TABLE IF NOT EXISTS `pase_punto_comentarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `punto_id` INT NOT NULL,
    `usuario_id` INT NOT NULL,
    `comentario` TEXT NOT NULL,
    `created_at` DATETIME DEFAULT NULL,
    KEY `idx_pcom_punto` (`punto_id`),
    CONSTRAINT `fk_pcom_punto` FOREIGN KEY (`punto_id`) REFERENCES `pase_puntos`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_pcom_usuario` FOREIGN KEY (`usuario_id`) REFERENCES `admin_usuarios`(`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- 5) Seed de turnos por defecto (Mañana, Tarde, Noche)
INSERT INTO `turnos` (`nombre`, `descripcion`, `orden`, `activo`) VALUES
('Mañana', 'Turno de la mañana', 1, 1),
('Tarde', 'Turno de la tarde', 2, 1),
('Noche', 'Turno de la noche', 3, 1)
ON DUPLICATE KEY UPDATE `nombre` = `nombre`;
