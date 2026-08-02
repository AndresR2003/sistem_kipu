-- Seed: Borradores de prueba
-- Algunos publicados en distintas secciones, otros como borrador

INSERT INTO `borradores` (`titulo`, `contenido`, `usuario_id`, `publicado`, `seccion_destino`, `destinatario_tipo`, `destinatario_id`, `fijado`, `created_at`, `updated_at`) VALUES
-- Publicados en Noticias
('Nuevos horarios de atencion al cliente',
 'Se informa a todos los colaboradores que a partir del proximo mes los horarios de atencion al cliente seran de 8:00 a 17:00 hrs de lunes a viernes. Los sabados atencion solo hasta las 13:00 hrs.\n\nFavor de tomar las medidas necesarias para informar a sus equipos.',
 1, 1, 'noticias', 'todos', NULL, 0, '2026-07-01 08:30:00', '2026-07-01 08:30:00'),

('Inauguracion de nueva sucursal',
 'Nos complace anunciar la apertura de nuestra nueva sucursal en la zona sur de la ciudad. La direccion es Av. Principal #123, Col. Centro.\n\nLa sucursal comenzara operaciones el 15 de agosto con un equipo de 10 colaboradores. ¡Los esperamos!',
 2, 1, 'noticias', 'todos', NULL, 1, '2026-07-05 10:00:00', '2026-07-06 09:15:00'),

('Resultados de encuesta de clima laboral',
 'Compartimos los resultados de la encuesta de clima laboral correspondiente al segundo trimestre:\n\n- Satisfaccion general: 82%\n- Ambiente laboral: 78%\n- Comunicacion interna: 74%\n- Oportunidades de crecimiento: 65%\n\nAgradecemos a todos los que participaron. Estaremos trabajando en las areas de oportunidad detectadas.',
 1, 1, 'noticias', 'departamento', 1, 0, '2026-07-10 14:00:00', '2026-07-10 14:00:00'),

-- Publicados en Ideas
('Propuesta: Dia de integracion mensual',
 'Propongo que el ultimo viernes de cada mes organicemos una convivencia de 1 hora con snacks y juegos para fortalecer la integracion entre equipos.\n\nEsto ayuda a mejorar la comunicacion y el ambiente laboral. Los costos son minimos y los beneficios enormes.',
 4, 1, 'ideas', 'todos', NULL, 0, '2026-07-03 09:00:00', '2026-07-03 09:00:00'),

('Sistema de reconocimiento entre pares',
 'Que tal si implementamos un sistema donde los colaboradores puedan reconocer el trabajo de sus companeros? Algo como un "Gracias" publico en el sistema cada mes.\n\nEl companero mas reconocido podria tener un incentivo o simplemente el reconocimiento de todos.',
 5, 1, 'ideas', 'usuarios', 4, 0, '2026-07-08 11:30:00', '2026-07-08 11:30:00'),

-- Publicados en Manual
('Politica de uso de uniforme',
 'El uso del uniforme es obligatorio para todo el personal operativo de lunes a sabado.\n\n- Uniforme completo: pantalon, camisa y chaleco con logo\n- Zapatos cerrados antiderrapantes\n- Gorra para personal de campo\n- El uniforme debe mantenerse limpio y en buen estado\n\nCada colaborador tiene derecho a 2 uniformes por ano.',
 1, 1, 'manual', 'todos', NULL, 0, '2026-06-15 08:00:00', '2026-06-15 08:00:00'),

('Procedimiento para solicitar vacaciones',
 'Pasos para solicitar vacaciones:\n\n1. Ingresar al sistema con tu usuario\n2. Ir a la seccion de solicitudes\n3. Seleccionar "Solicitar vacaciones"\n4. Elegir fechas (minimo 5 dias, maximo 15 dias corridos)\n5. Esperar autorizacion de tu jefe directo\n6. Recibiras una notificacion con la respuesta\n\nPlazo minimo de solicitud: 15 dias antes.',
 2, 1, 'manual', 'todos', NULL, 0, '2026-06-20 10:00:00', '2026-06-20 10:00:00'),

('Protocolo de seguridad en instalaciones',
 'Todo el personal debe seguir estas normas de seguridad:\n\n1. Mantener puertas de emergencia despejadas\n2. No obstruir extintores ni gabinetes contra incendios\n3. Reportar inmediatamente cualquier condicion insegura\n4. Conocer las rutas de evacuacion de tu area\n5. Participar en los simulacros programados\n\nEn caso de emergencia, llamar al 9-1-1 y reportar al encargado de seguridad.',
 1, 1, 'manual', 'todos', NULL, 1, '2026-06-25 09:00:00', '2026-06-25 09:00:00'),

-- Publicados en Tareas
('Actualizar inventario de almacen',
 'Se requiere realizar el inventario fisico del almacen central antes del 15 de agosto.\n\nEquipo asignado: Almacen\nFecha limite: 15/08/2026\nPrioridad: Alta',
 1, 1, 'tareas', 'departamento', 5, 0, '2026-07-12 07:00:00', '2026-07-12 07:00:00'),

('Revision de equipos de computo',
 'Hacer revision y limpieza de todos los equipos de computo del area administrativa.\n\nAsignado a: Soporte Tecnico\nFecha limite: 31/07/2026\nPrioridad: Media',
 6, 1, 'tareas', 'departamento', 3, 0, '2026-07-11 09:30:00', '2026-07-11 09:30:00'),

('Capacitacion nuevo sistema de ventas',
 'Agendar capacitacion para el equipo de ventas sobre el nuevo modulo del sistema.\n\nInstructor: Soporte Tecnico\nDuracion: 4 horas\nFecha propuesta: 05/08/2026',
 4, 1, 'tareas', 'departamento', 2, 0, '2026-07-09 11:00:00', '2026-07-09 11:00:00'),

-- Borradores sin publicar
('Presupuesto anual 2026',
 'Borrador del presupuesto anual para la revision del equipo directivo.\n\nConceptos a incluir:\n- Nomina\n- Operacion\n- Mantenimiento\n- Capacitacion\n- Equipo nuevo\n\nPendiente de revision por contabilidad.',
 1, 0, NULL, NULL, NULL, 0, '2026-07-02 16:00:00', '2026-07-02 16:00:00'),

('Plan de marketing digital',
 'Estrategia para aumentar la presencia en redes sociales durante el proximo trimestre.\n\nRedes: Facebook, Instagram, LinkedIn\nPublicaciones: 3 por semana\nInversion sugerida: $5,000 mensuales\n\nEsperando aprobacion del presupuesto.',
 4, 0, NULL, NULL, NULL, 0, '2026-07-06 15:00:00', '2026-07-07 10:30:00'),

('Reporte de ventas junio',
 'Reporte detallado de ventas del mes de junio con graficos comparativos vs el mes anterior.\n\nPendiente de terminar la seccion de conclusiones y recomendaciones.',
 5, 0, NULL, NULL, NULL, 0, '2026-07-04 13:00:00', '2026-07-04 13:00:00'),

('Formato de evaluacion de desempeno',
 'Disenar el nuevo formato de evaluacion de desempeno para aplicar a todo el personal en septiembre.\n\nSecciones:\n1. Datos del empleado\n2. Autoevaluacion\n3. Evaluacion del jefe directo\n4. Plan de mejora\n\nMostrar ejemplo al equipo de RH antes de finalizar.',
 2, 0, NULL, NULL, NULL, 1, '2026-07-07 08:00:00', '2026-07-07 08:00:00'),

('Guia de onboarding para nuevos empleados',
 'Crear una guia paso a paso para la integracion de nuevos colaboradores.\n\nIncluir:\n- Documentacion requerida\n- Recorrido por instalaciones\n- Presentacion con el equipo\n- Acceso a sistemas\n- Asignacion de mentor\n\nTrabajando en la version final.',
 1, 0, NULL, NULL, NULL, 0, '2026-07-10 16:30:00', '2026-07-10 16:30:00');
