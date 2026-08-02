-- Expandir admin_usuarios con campos de empleado y roles
ALTER TABLE `admin_usuarios`
  ADD COLUMN `telefono` VARCHAR(20) DEFAULT NULL AFTER `id_departamento`,
  ADD COLUMN `puesto` VARCHAR(100) DEFAULT NULL AFTER `telefono`,
  ADD COLUMN `fecha_nacimiento` DATE DEFAULT NULL AFTER `puesto`,
  ADD COLUMN `fecha_contratacion` DATE DEFAULT NULL AFTER `fecha_nacimiento`;

-- Actualizar columna rol para aceptar mas valores (si es ENUM, cambiar a VARCHAR)
ALTER TABLE `admin_usuarios` MODIFY COLUMN `rol` VARCHAR(30) NOT NULL DEFAULT 'empleado';

-- Insertar departamentos base si no existen
INSERT IGNORE INTO `departamentos` (`id`, `descripcion`) VALUES
(1, 'Administracion'),
(2, 'Ventas'),
(3, 'Soporte'),
(4, 'Tecnico'),
(5, 'Almacen');
