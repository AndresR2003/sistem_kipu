-- =====================================================
-- Litio - Control de Pagos - Database Schema
-- =====================================================
-- Base de datos: litio_pagos
-- Motor: InnoDB
-- Charset: utf8mb4
-- =====================================================

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";

-- =====================================================
-- Tabla: usuarios
-- =====================================================
CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `nombre` VARCHAR(100) NOT NULL,
    `telefono` VARCHAR(20) DEFAULT NULL,
    `monto` DECIMAL(10,2) NOT NULL DEFAULT 12.00 COMMENT 'Monto mensual en soles',
    `token` VARCHAR(64) NOT NULL,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `fecha_creacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_token` (`token`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- Tabla: pagos
-- =====================================================
CREATE TABLE IF NOT EXISTS `pagos` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `id_usuario` INT(11) NOT NULL,
    `mes` INT(11) NOT NULL COMMENT 'Mes del pago (1-12)',
    `anio` INT(11) NOT NULL COMMENT 'Ano del pago',
    `monto` DECIMAL(10,2) NOT NULL DEFAULT 12.00 COMMENT 'Monto en soles',
    `estado` ENUM('NO_PAGADO','PENDIENTE','PAGADO','RECHAZADO') NOT NULL DEFAULT 'NO_PAGADO',
    `captura` VARCHAR(255) DEFAULT NULL COMMENT 'Ruta de la imagen del comprobante',
    `observacion` TEXT DEFAULT NULL COMMENT 'Observacion del admin al rechazar',
    `fecha_envio` DATETIME DEFAULT NULL COMMENT 'Fecha en que el usuario envio el comprobante',
    `fecha_aprobacion` DATETIME DEFAULT NULL COMMENT 'Fecha en que el admin aprobo/rechazo',
    `fecha_creacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_pago_usuario_mes_anio` (`id_usuario`, `mes`, `anio`),
    KEY `idx_estado` (`estado`),
    KEY `idx_mes_anio` (`mes`, `anio`),
    CONSTRAINT `fk_pagos_usuario` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- Tabla: admin_usuarios (usuarios del sistema)
-- =====================================================
CREATE TABLE IF NOT EXISTS `admin_usuarios` (
    `id` INT(11) NOT NULL AUTO_INCREMENT,
    `username` VARCHAR(50) NOT NULL,
    `email` VARCHAR(100) NOT NULL,
    `password` VARCHAR(255) NOT NULL COMMENT 'Contrasena encriptada con password_hash(bcrypt)',
    `nombre` VARCHAR(100) NOT NULL,
    `rol` ENUM('admin','superadmin') NOT NULL DEFAULT 'admin',
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `ultimo_acceso` DATETIME DEFAULT NULL,
    `fecha_creacion` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`),
    UNIQUE KEY `uk_username` (`username`),
    UNIQUE KEY `uk_email` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- =====================================================
-- Datos iniciales: 3 usuarios de pago
-- =====================================================
INSERT INTO `usuarios` (`nombre`, `telefono`, `token`, `activo`) VALUES
('Kevin',  '999999999', 'a1b2c3d4e5f6g7h8i9j0k1l2m3n4o5p6q7r8s9t0u1v2w3x4y5z6a7b8c9d0e1f2', 1),
('Andrés', '888888888', 'z9y8x7w6v5u4t3s2r1q0p9o8n7m6l5k4j3i2h1g0f9e8d7c6b5a4z3y2x1w0v9u8', 1),
('Joel',   '777777777', 'm1n2o3p4q5r6s7t8u9v0w1x2y3z4a5b6c7d8e9f0g1h2i3j4k5l6m7n8o9p0q1r2', 1);

-- =====================================================
-- Admin por defecto: admin / admin123
-- =====================================================
INSERT INTO `admin_usuarios` (`username`, `email`, `password`, `nombre`, `rol`) VALUES
('admin', 'admin@sistemalito.com', '$2y$10$BrC9y1qYQ9OwqES86rAFluao7jFX.J7DaRUKv6HxsY7eC1/8pXA5W', 'Administrador', 'superadmin');

COMMIT;
