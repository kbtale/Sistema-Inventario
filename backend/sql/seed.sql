-- Clean existing data (Optional, safety check)
SET FOREIGN_KEY_CHECKS = 0;
TRUNCATE TABLE Salidas;
TRUNCATE TABLE Entradas;
TRUNCATE TABLE Usuarios;
TRUNCATE TABLE Telefonos;
TRUNCATE TABLE Hardware;
TRUNCATE TABLE Estatus;
TRUNCATE TABLE Sedes;
TRUNCATE TABLE Municipios;
SET FOREIGN_KEY_CHECKS = 1;

-- 1. Municipios
INSERT INTO Municipios (id_municipio, nombre_municipio) VALUES 
(1, 'Libertador'),
(2, 'Chacao'),
(3, 'Baruta'),
(4, 'Sucre'),
(5, 'El Hatillo');

-- 2. Sedes
INSERT INTO Sedes (id_sede, nombre_sede, id_municipio, direccion) VALUES 
(1, 'Sede Principal OTIC', 1, 'Av. Urdaneta, Edif. Central, Piso 4'),
(2, 'Oficina Regional Chacao', 2, 'Av. Francisco de Miranda, Torre Kyra'),
(3, 'Centro de Datos Baruta', 3, 'Calle La Guairita, Edif. Tecnológico'),
(4, 'Almacén de Soporte Sucre', 4, 'Av. Rómulo Gallegos, Galpón 12');

-- 3. Usuarios de Staff (Technicians/Encargados)
INSERT INTO Usuarios (id_usuario, nombre_usuario, apellido_usuario, ci_usuario) VALUES 
(1, 'Admin', 'SIOTIC', '00000000'),
(2, 'Carlos', 'Mendoza', '15678345'),
(3, 'Elena', 'Rodriguez', '21334556');

-- 4. Initial Status Records
-- id_estatus 1-5 for Hardware
INSERT INTO Estatus (id_estatus, comentarios, estado_actual_unidad, fallas, pulse_score) VALUES 
(1, 'Recién ingresado, excelente estado', 1, NULL, 100),
(2, 'En revisión por falla de fuente', 2, 'Falla de encendido intermitente', 75),
(3, 'Batería degradada, requiere cambio', 2, 'Batería inflada', 45),
(4, 'Asignado a Dirección General', 1, NULL, 95),
(5, 'Disponible para reasignación', 1, NULL, 88);

-- 5. Hardware Assets
INSERT INTO Hardware (tipo_hardware, marca_hardware, modelo_hardware, bienes_hardware, usuario_hardware, fecha_ingreso, id_estatus, id_sede, qr_code) VALUES 
('Laptop', 'Dell', 'Latitude 5420', 'OTIC-H-001', 'Dirección General', '2023-01-15', 4, 1, 'QR-H-5420-001'),
('Desktop', 'HP', 'EliteDesk 800 G6', 'OTIC-H-002', 'Staff OTIC', '2023-03-20', 1, 1, 'QR-H-800-002'),
('Monitor', 'Samsung', 'F24T35', 'OTIC-H-003', 'Elena Rodriguez', '2023-05-10', 5, 2, 'QR-H-F24-003'),
('Laptop', 'Lenovo', 'ThinkPad T14', 'OTIC-H-004', 'Carlos Mendoza', '2023-06-05', 2, 4, 'QR-H-T14-004');

-- 6. Mobile Assets
INSERT INTO Telefonos (tipo_telefono, marca_telefono, modelo_telefono, nro_telefono, imei_telefono, imeisim_telefono, puk_telefono, usuario_asignado, id_estatus, id_sede, qr_code) VALUES 
('Smartphone', 'Samsung', 'Galaxy A54', '04121234567', '356789123456789', '8958123456789012345', '1234', 'Director OTIC', 1, 1, 'QR-M-A54-001'),
('Smartphone', 'Xiaomi', 'Redmi Note 12', '04149876543', '864213579024681', '8958987654321098765', '5678', 'Soporte Móvil', 3, 3, 'QR-M-RN12-002'),
('Tablet', 'Lenovo', 'Tab M10', '04240001122', '350000000000123', '8958000000000000123', '0000', 'Logística Sucre', 2, 4, 'QR-M-TM10-003');

-- 7. Support Entries
INSERT INTO Entradas (fecha_entrada, id_hardware, id_unit_hardware, id_encargado, id_sede, numero_orden, nom_responsable, foto_url) VALUES 
('2024-02-10', 4, NULL, 2, 4, 'WO-2024-001', 'Carlos Mendoza', 'assets/photos/wo-2024-001.jpg'),
('2024-02-12', NULL, 3, 3, 4, 'WO-2024-002', 'Elena Rodriguez', 'assets/photos/wo-2024-002.jpg');
