-- =====================================================
-- Tareas predeterminadas para Entregas / Pases de turno
-- =====================================================

INSERT INTO `entregas` (`titulo`, `descripcion`, `repetir_diario`, `fecha_inicio`, `fecha_fin`, `publicado`) VALUES
('Revisar habitaciones vacias', 'Verificar que las habitaciones vacias esten limpias, ventiladas y con amenities completos.', 1, CURDATE(), NULL, 1),
('Verificar limpieza de lobby y areas comunes', 'Revisar lobby, pasillos, elevadores y areas de descanso.', 1, CURDATE(), NULL, 1),
('Chequear stock de amenities y minibar', 'Contar y reponer shampoo, jabon, papel y productos de minibar.', 1, CURDATE(), NULL, 1),
('Revisar mantenimiento de aire acondicionado', 'Comprobar funcionamiento de unidades de AC en habitaciones y areas comunes.', 1, CURDATE(), NULL, 1),
('Verificar seguridad y cierre de puertas', 'Comprobar que puertas de emergencia, ventanas y accesos esten seguros.', 1, CURDATE(), NULL, 1),
('Reportar incidencias de huespedes', 'Registrar cualquier incidencia o solicitud de los huespedes en el turno.', 1, CURDATE(), NULL, 1),
('Revisar reservas del dia siguiente', 'Confirmar reservas programadas y preparar llaves / check-in anticipado.', 1, CURDATE(), NULL, 1),
('Verificar caja y fondo de cambio', 'Conteo de caja al inicio y cierre de turno, registro de movimientos.', 1, CURDATE(), NULL, 1),
('Chequear estado de lavanderia', 'Verificar ropa recibida, programada y entregada en lavanderia.', 1, CURDATE(), NULL, 1),
('Revisar estacionamiento y exteriores', 'Verificar iluminacion, orden y seguridad del estacionamiento y exteriores.', 1, CURDATE(), NULL, 1);
