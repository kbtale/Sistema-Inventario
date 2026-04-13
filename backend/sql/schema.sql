CREATE DATABASE IF NOT EXISTS siotic;
USE siotic;

CREATE TABLE IF NOT EXISTS Estatus (
    id_estatus INT AUTO_INCREMENT PRIMARY KEY,
    comentarios TEXT,
    estado_ingreso VARCHAR(255),
    c_ent INT DEFAULT 0,
    estado_actual_unidad INT DEFAULT 1,
    fallas TEXT
);

CREATE TABLE IF NOT EXISTS Hardware (
    id_hardware INT AUTO_INCREMENT PRIMARY KEY,
    tipo_hardware VARCHAR(255),
    marca_hardware VARCHAR(255),
    modelo_hardware VARCHAR(255),
    bienes_hardware VARCHAR(255),
    usuario_hardware VARCHAR(255),
    fecha_ingreso DATE,
    id_estatus INT,
    INDEX (id_estatus),
    FOREIGN KEY (id_estatus) REFERENCES Estatus(id_estatus) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS Telefonos (
    id_telefono INT AUTO_INCREMENT PRIMARY KEY,
    tipo_telefono VARCHAR(255),
    marca_telefono VARCHAR(255),
    modelo_telefono VARCHAR(255),
    nro_telefono VARCHAR(255),
    imei_telefono VARCHAR(255),
    imeisim_telefono VARCHAR(255),
    puk_telefono VARCHAR(255),
    usuario_asignado VARCHAR(255),
    id_estatus INT,
    INDEX (id_estatus),
    FOREIGN KEY (id_estatus) REFERENCES Estatus(id_estatus) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS Usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    nombre_usuario VARCHAR(255),
    apellido_usuario VARCHAR(255),
    ci_usuario VARCHAR(255)
);

CREATE TABLE IF NOT EXISTS Entradas (
    id_entrada INT AUTO_INCREMENT PRIMARY KEY,
    fecha_entrada DATE,
    id_hardware INT,
    id_unit_hardware INT,
    id_encargado INT,
    salida_pcp VARCHAR(255),
    fecha_salida_pcp DATE,
    numero_orden VARCHAR(255),
    nom_responsable VARCHAR(255),
    INDEX (id_hardware),
    INDEX (id_encargado),
    FOREIGN KEY (id_hardware) REFERENCES Hardware(id_hardware) ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS Salidas (
    id_salida INT AUTO_INCREMENT PRIMARY KEY,
    id_entrada INT,
    fecha_salida DATE,
    nom_responsable VARCHAR(255),
    pcp VARCHAR(255),
    fecha_pcp DATE,
    reporte TEXT,
    receptor VARCHAR(255),
    INDEX (id_entrada),
    FOREIGN KEY (id_entrada) REFERENCES Entradas(id_entrada) ON DELETE CASCADE
);
