-- =====================================================
-- MIGRACION: Preferencias del perfil de usuario
-- Fecha: 2026-08-15
-- Descripcion: Agrega idioma y preferencias de notificacion
--              a la tabla admin_usuarios para Mi Perfil.
-- =====================================================

ALTER TABLE `admin_usuarios`
  ADD COLUMN `idioma` VARCHAR(10) NOT NULL DEFAULT 'es'
  AFTER `fecha_contratacion`,
  ADD COLUMN `preferencias_notificacion` TEXT DEFAULT NULL
  AFTER `idioma`;
