-- Seed: Empleados de prueba
-- Las passwords estan hasheadas con bcrypt: todos = "123456"

INSERT INTO `admin_usuarios` (`username`, `email`, `nombre`, `password`, `rol`, `activo`, `id_departamento`, `puesto`, `telefono`, `fecha_nacimiento`, `fecha_contratacion`) VALUES
('jperez',    'jperez@litio.com',    'Juan Perez',        '$2y$10$W2jV88DhoQRqRsrL470nqe7rXpuntxWKoy8FtGuZ3.vw0OiF2OflS', 'superadmin', 1, 1, 'Gerente General',      '555-1001', '1985-03-15', '2020-01-10'),
('mlopez',    'mlopez@litio.com',    'Maria Lopez',       '$2y$10$W2jV88DhoQRqRsrL470nqe7rXpuntxWKoy8FtGuZ3.vw0OiF2OflS', 'admin',      1, 1, 'Subgerente',           '555-1002', '1990-07-22', '2021-03-15'),
('crodriguez','crodriguez@litio.com','Carlos Rodriguez',  '$2y$10$W2jV88DhoQRqRsrL470nqe7rXpuntxWKoy8FtGuZ3.vw0OiF2OflS', 'admin',      1, 2, 'Jefe de Ventas',       '555-1003', '1988-11-02', '2020-06-01'),
('agarcia',   'agarcia@litio.com',   'Ana Garcia',        '$2y$10$W2jV88DhoQRqRsrL470nqe7rXpuntxWKoy8FtGuZ3.vw0OiF2OflS', 'vendedor',   1, 2, 'Vendedor Senior',      '555-1004', '1992-05-18', '2021-08-20'),
('lmartinez', 'lmartinez@litio.com', 'Luis Martinez',     '$2y$10$W2jV88DhoQRqRsrL470nqe7rXpuntxWKoy8FtGuZ3.vw0OiF2OflS', 'vendedor',   1, 2, 'Vendedor Junior',      '555-1005', '1995-09-30', '2023-02-10'),
('pramirez',  'pramirez@litio.com',  'Pedro Ramirez',     '$2y$10$W2jV88DhoQRqRsrL470nqe7rXpuntxWKoy8FtGuZ3.vw0OiF2OflS', 'soporte',    1, 3, 'Soporte Tecnico Sr',   '555-1006', '1991-12-12', '2020-11-05'),
('dhernandez','dhernandez@litio.com','Diana Hernandez',   '$2y$10$W2jV88DhoQRqRsrL470nqe7rXpuntxWKoy8FtGuZ3.vw0OiF2OflS', 'soporte',    1, 3, 'Soporte Tecnico Jr',   '555-1007', '1997-04-08', '2022-07-18'),
('rtorres',   'rtorres@litio.com',   'Roberto Torres',    '$2y$10$W2jV88DhoQRqRsrL470nqe7rXpuntxWKoy8FtGuZ3.vw0OiF2OflS', 'tecnico',    1, 4, 'Tecnico Instalador',   '555-1008', '1993-08-25', '2021-01-20'),
('gflores',   'gflores@litio.com',   'Gabriela Flores',   '$2y$10$W2jV88DhoQRqRsrL470nqe7rXpuntxWKoy8FtGuZ3.vw0OiF2OflS', 'tecnico',    1, 4, 'Tecnico de Campo',     '555-1009', '1996-02-14', '2022-04-01'),
('mruiz',     'mruiz@litio.com',     'Miguel Ruiz',       '$2y$10$W2jV88DhoQRqRsrL470nqe7rXpuntxWKoy8FtGuZ3.vw0OiF2OflS', 'empleado',   1, 5, 'Auxiliar Almacen',     '555-1010', '1998-06-21', '2023-05-15'),
('lcastro',   'lcastro@litio.com',   'Laura Castro',      '$2y$10$W2jV88DhoQRqRsrL470nqe7rXpuntxWKoy8FtGuZ3.vw0OiF2OflS', 'empleado',   1, 5, 'Encargada Inventario', '555-1011', '1994-10-10', '2021-09-01'),
('jmedina',   'jmedina@litio.com',   'Jorge Medina',      '$2y$10$W2jV88DhoQRqRsrL470nqe7rXpuntxWKoy8FtGuZ3.vw0OiF2OflS', 'empleado',   0, 1, 'Auxiliar Admin',       '555-1012', '1999-01-05', '2024-01-08');
