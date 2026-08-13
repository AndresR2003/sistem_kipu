-- =====================================================
-- Seed: Tareas de prueba para el nuevo modulo
-- =====================================================

INSERT INTO `tareas` (`titulo`, `descripcion`, `prioridad`, `fecha_limite`, `modalidad`, `departamento_id`, `destinatario_tipo`, `destinatario_id`, `created_by`, `publicado`, `completada`, `completada_por`, `completada_at`) VALUES

-- ─── Administracion ───
('Preparar informe mensual de ventas', 'Elaborar el reporte consolidado de ventas del mes anterior para presentar a direccion.', 'alta', DATE_ADD(NOW(), INTERVAL 1 DAY), 'single_completes_all', 1, 'departamento', 1, 1, 1, 0, NULL, NULL),

('Actualizar directorio de proveedores', 'Revisar y actualizar los datos de contacto de todos los proveedores registrados en el sistema.', 'media', DATE_ADD(NOW(), INTERVAL 3 DAY), 'single_completes_all', 1, 'departamento', 1, 2, 1, 0, NULL, NULL),

('Organizar documentacion fiscal', 'Clasificar y digitalizar documentos fiscales del trimestre para auditoria.', 'baja', DATE_ADD(NOW(), INTERVAL 7 DAY), 'all_must_complete', 1, 'departamento', 1, 1, 1, 0, NULL, NULL),

-- ─── Ventas ───
('Llamar a clientes pendientes de respuesta', 'Contactar a los 15 clientes que tienen cotizaciones pendientes desde hace mas de 1 semana.', 'alta', DATE_ADD(NOW(), INTERVAL 4 HOUR), 'single_completes_all', 2, 'departamento', 2, 3, 1, 1, 4, NOW() - INTERVAL 2 HOUR),

('Actualizar base de datos de clientes', 'Verificar que los datos de contacto de los clientes esten actualizados en el CRM.', 'media', DATE_ADD(NOW(), INTERVAL 2 DAY), 'all_must_complete', 2, 'departamento', 2, 3, 1, 0, NULL, NULL),

('Preparar presentacion para reunion de equipo', 'Crear diapositivas con los resultados de ventas de la semana.', 'baja', DATE_ADD(NOW(), INTERVAL 5 DAY), 'single_completes_all', 2, 'departamento', 2, 3, 1, 0, NULL, NULL),

-- ─── Soporte ───
('Revisar tickets de soporte pendientes', 'Atender los 8 tickets abiertos de usuarios con problemas tecnicos.', 'alta', DATE_ADD(NOW(), INTERVAL 2 HOUR), 'single_completes_all', 3, 'departamento', 3, 1, 1, 1, 6, NOW() - INTERVAL 1 HOUR),

('Actualizar sistema de soporte a la ultima version', 'Aplicar las actualizaciones de seguridad pendientes en el servidor de soporte.', 'media', DATE_ADD(NOW(), INTERVAL 1 DAY), 'single_completes_all', 3, 'departamento', 3, 6, 1, 0, NULL, NULL),

('Crear manual de usuario para nuevo modulo', 'Documentar el uso del modulo de tareas para los usuarios finales.', 'baja', DATE_ADD(NOW(), INTERVAL 4 DAY), 'all_must_complete', 3, 'departamento', 3, 1, 1, 0, NULL, NULL),

-- ─── Tecnico ───
('Inspeccionar instalaciones electricas', 'Revision preventiva del sistema electrico en areas comunes y habitaciones.', 'alta', DATE_ADD(NOW(), INTERVAL 6 HOUR), 'single_completes_all', 4, 'departamento', 4, 1, 1, 0, NULL, NULL),

('Calibrar equipos de measuring', 'Realizar calibracion trimestral de todos los equipos de medicion del laboratorio.', 'media', DATE_ADD(NOW(), INTERVAL 3 DAY), 'all_must_complete', 4, 'departamento', 4, 8, 1, 0, NULL, NULL),

('Verificar sistema de seguridad', 'Chequear camaras, alarmas y sensores de todo el complejo.', 'baja', DATE_ADD(NOW(), INTERVAL 6 DAY), 'single_completes_all', 4, 'departamento', 4, 1, 1, 0, NULL, NULL),

-- ─── Almacen ───
('Conteo fisico de inventario', 'Realizar conteo fisico de todos los productos del almacen y comparar con sistema.', 'alta', DATE_ADD(NOW(), INTERVAL 1 DAY), 'all_must_complete', 5, 'departamento', 5, 1, 1, 0, NULL, NULL),

('Reorganizar estanterias del almacen', 'Etiquetar y reorganizar productos por categoria para optimizar espacio.', 'media', DATE_ADD(NOW(), INTERVAL 5 DAY), 'single_completes_all', 5, 'departamento', 5, 10, 1, 0, NULL, NULL),

('Verificar fecha de vencimiento de productos', 'Revisar todos los productos con fecha de vencimiento y retirar los vencidos.', 'baja', DATE_ADD(NOW(), INTERVAL 2 DAY), 'single_completes_all', 5, 'departamento', 5, 10, 1, 0, NULL, NULL),

-- ─── Tarea oculta (no publicada) ───
('Tarea en borrador - Proyecto especial', 'Esta tarea aun no esta publicada, solo visible para admins.', 'media', NULL, 'single_completes_all', 1, 'todos', NULL, 1, 0, 0, NULL, NULL);


-- ─── Asignaciones para tareas all_must_complete ───

-- Asignar a Carlos, Ana y Luis para "Actualizar base de datos de clientes"
INSERT INTO `tarea_asignaciones` (`tarea_id`, `usuario_id`, `completado`, `completado_at`) VALUES
(5, 3, 1, NOW() - INTERVAL 3 HOUR),
(5, 4, 1, NOW() - INTERVAL 1 HOUR),
(5, 5, 0, NULL);

-- Asignar a Pedro y Diana para "Crear manual de usuario"
INSERT INTO `tarea_asignaciones` (`tarea_id`, `usuario_id`, `completado`, `completado_at`) VALUES
(9, 6, 1, NOW() - INTERVAL 5 HOUR),
(9, 7, 0, NULL);

-- Asignar a Roberto y Gabriela para "Calibrar equipos"
INSERT INTO `tarea_asignaciones` (`tarea_id`, `usuario_id`, `completado`, `completado_at`) VALUES
(11, 8, 0, NULL),
(11, 9, 0, NULL);

-- Asignar a Miguel y Laura para "Conteo fisico de inventario"
INSERT INTO `tarea_asignaciones` (`tarea_id`, `usuario_id`, `completado`, `completado_at`) VALUES
(13, 10, 0, NULL),
(13, 11, 0, NULL);

-- Asignar a Juan, Maria y Carlos para "Organizar documentacion fiscal"
INSERT INTO `tarea_asignaciones` (`tarea_id`, `usuario_id`, `completado`, `completado_at`) VALUES
(3, 1, 1, NOW() - INTERVAL 2 HOUR),
(3, 2, 0, NULL),
(3, 3, 0, NULL);


-- ─── Comentarios de prueba ───

INSERT INTO `comentarios` (`tarea_id`, `usuario_id`, `comentario`, `created_at`) VALUES
(4, 4,  'Ya termine de llamar a los 15 clientes, 12 respondieron. Quedan 3 pendientes por llamar manana.', NOW() - INTERVAL 2 HOUR),
(4, 3,  'Buen trabajo Ana. Los 3 pendientes son clientes prioritarios, no los dejes para despues.', NOW() - INTERVAL 1 HOUR),
(7, 6,  'Ya atendi los 8 tickets. El mas urgente era el del cliente XYZ que no podia acceder al sistema.', NOW() - INTERVAL 1 HOUR),
(7, 1,  'Perfecto Pedro, gracias por la rapidez. Que paso con el cliente XYZ?', NOW() - INTERVAL 30 MINUTE),
(7, 6,  'Era un problema de cache del navegador. Le indique como limpiar y quedo resuelto.', NOW() - INTERVAL 25 MINUTE),
(1, 2, 'Juan, el informe necesita incluir las graficas comparativas del trimestre anterior.', NOW() - INTERVAL 4 HOUR),
(1, 1, 'Cierto, ya lo estoy agregando. Estara listo para el mediodia.', NOW() - INTERVAL 3 HOUR);
