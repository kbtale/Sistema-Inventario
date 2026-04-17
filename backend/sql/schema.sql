CREATE DATABASE IF NOT EXISTS siotic;
USE siotic;

CREATE TABLE IF NOT EXISTS Municipios (
    id_municipio INT AUTO_INCREMENT PRIMARY KEY,
    nombre_municipio VARCHAR(255) NOT NULL
);

CREATE TABLE IF NOT EXISTS Sedes (
    id_sede INT AUTO_INCREMENT PRIMARY KEY,
    nombre_sede VARCHAR(255) NOT NULL,
    id_municipio INT,
    direccion TEXT,
    latitud DECIMAL(10, 8),
    longitud DECIMAL(11, 8),
    FOREIGN KEY (id_municipio) REFERENCES Municipios(id_municipio) ON DELETE SET NULL
);

CREATE TABLE IF NOT EXISTS Estatus (
    id_estatus INT AUTO_INCREMENT PRIMARY KEY,
    comentarios TEXT,
    estado_ingreso VARCHAR(255),
    c_ent INT DEFAULT 0,
    estado_actual_unidad INT DEFAULT 1,
    fallas TEXT,
    pulse_score INT DEFAULT 100
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
    id_sede INT,
    qr_code VARCHAR(255),
    INDEX (id_estatus),
    INDEX (id_sede),
    FOREIGN KEY (id_estatus) REFERENCES Estatus(id_estatus) ON DELETE SET NULL,
    FOREIGN KEY (id_sede) REFERENCES Sedes(id_sede) ON DELETE SET NULL
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
    fecha_ingreso DATE,
    id_estatus INT,
    id_sede INT,
    qr_code VARCHAR(255),
    INDEX (id_estatus),
    INDEX (id_sede),
    FOREIGN KEY (id_estatus) REFERENCES Estatus(id_estatus) ON DELETE SET NULL,
    FOREIGN KEY (id_sede) REFERENCES Sedes(id_sede) ON DELETE SET NULL
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
    id_sede INT,
    salida_pcp VARCHAR(255),
    fecha_salida_pcp DATE,
    numero_orden VARCHAR(255),
    nom_responsable VARCHAR(255),
    foto_url VARCHAR(255),
    INDEX (id_hardware),
    INDEX (id_encargado),
    INDEX (id_sede),
    FOREIGN KEY (id_hardware) REFERENCES Hardware(id_hardware) ON DELETE CASCADE,
    FOREIGN KEY (id_sede) REFERENCES Sedes(id_sede) ON DELETE SET NULL
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
