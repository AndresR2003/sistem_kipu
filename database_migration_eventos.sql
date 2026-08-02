-- =====================================================
-- Migration: Crear tabla eventos para el calendario
-- =====================================================
-- Ejecutar en la base de datos: litio_pagos
-- =====================================================

CREATE TABLE IF NOT EXISTS `eventos` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `titulo` VARCHAR(255) NOT NULL COMMENT 'Titulo del evento',
    `descripcion` TEXT DEFAULT NULL COMMENT 'Descripcion opcional del evento',
    `fecha_inicio` DATETIME NOT NULL COMMENT 'Fecha y hora de inicio del evento',
    `fecha_fin` DATETIME DEFAULT NULL COMMENT 'Fecha y hora de fin del evento',
    `color` VARCHAR(7) DEFAULT '#4669FA' COMMENT 'Color del evento en hex (ej: #4669FA)',
    `usuario_id` INT(11) DEFAULT NULL COMMENT 'ID del usuario opcional asociado al evento',
    `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha de creacion del registro',
    `updated_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT 'Fecha de ultima actualizacion',
    PRIMARY KEY (`id`),
    KEY `idx_fecha_inicio` (`fecha_inicio`),
    KEY `idx_fecha_fin` (`fecha_fin`),
    KEY `idx_usuario_id` (`usuario_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
