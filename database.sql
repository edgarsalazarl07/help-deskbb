CREATE DATABASE IF NOT EXISTS helpdesk_mvc_db;
USE helpdesk_mvc_db;

-- Table for users (Admin / Client)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    apellido_paterno VARCHAR(100),
    apellido_materno VARCHAR(100),
    nombre VARCHAR(100),
    fecha_nacimiento DATE,
    sexo ENUM('M', 'F'),
    telefono VARCHAR(20),
    correo VARCHAR(100),
    usuario VARCHAR(50) NOT NULL UNIQUE,
    password_sha1 VARCHAR(40) NOT NULL,
    rol ENUM('admin', 'cliente') NOT NULL DEFAULT 'cliente',
    ubicacion TEXT,
    activo TINYINT(1) DEFAULT 1,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Table for departments
CREATE TABLE IF NOT EXISTS departamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);

-- Table for personnel
CREATE TABLE IF NOT EXISTS personal (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellidos VARCHAR(100) NOT NULL,
    telefono VARCHAR(20),
    correo VARCHAR(100),
    usuario_id INT,
    departamento_id INT,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE SET NULL,
    FOREIGN KEY (departamento_id) REFERENCES departamentos(id) ON DELETE SET NULL
);

-- Table for equipment categories
CREATE TABLE IF NOT EXISTS categorias_equipo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);

-- Table for equipment
CREATE TABLE IF NOT EXISTS equipos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    marca VARCHAR(50) NOT NULL,
    modelo VARCHAR(50) NOT NULL,
    numero_serie VARCHAR(100) NOT NULL UNIQUE,
    categoria_id INT,
    estado ENUM('disponible', 'asignado', 'en_reparacion', 'baja') DEFAULT 'disponible',
    FOREIGN KEY (categoria_id) REFERENCES categorias_equipo(id) ON DELETE SET NULL
);

-- Table for asset assignments
CREATE TABLE IF NOT EXISTS asignaciones (
    id INT AUTO_INCREMENT PRIMARY KEY,
    equipo_id INT NOT NULL,
    personal_id INT NOT NULL,
    fecha_asignacion DATE NOT NULL,
    fecha_devolucion DATE NULL,
    estado_asignacion ENUM('activa', 'devuelto') DEFAULT 'activa',
    FOREIGN KEY (equipo_id) REFERENCES equipos(id) ON DELETE CASCADE,
    FOREIGN KEY (personal_id) REFERENCES personal(id) ON DELETE CASCADE
);

-- Table for tickets
CREATE TABLE IF NOT EXISTS tickets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    asignacion_id INT NOT NULL,
    titulo VARCHAR(200) NOT NULL,
    descripcion TEXT NOT NULL,
    estado ENUM('abierto', 'en_proceso', 'cerrado') DEFAULT 'abierto',
    departamento_id INT NULL,
    fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    fecha_cierre TIMESTAMP NULL,
    FOREIGN KEY (asignacion_id) REFERENCES asignaciones(id) ON DELETE CASCADE,
    FOREIGN KEY (departamento_id) REFERENCES departamentos(id) ON DELETE SET NULL
);

-- Table for ticket replies (thread)
CREATE TABLE IF NOT EXISTS ticket_respuestas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    ticket_id INT NOT NULL,
    usuario_id INT NOT NULL,
    mensaje TEXT NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (ticket_id) REFERENCES tickets(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

-- Insert default admin user (password: admin is SHA1 hashed)
INSERT INTO usuarios (usuario, password_sha1, rol) 
VALUES ('admin', SHA1('admin'), 'admin')
ON DUPLICATE KEY UPDATE id=id;

-- Insert default categories
INSERT INTO categorias_equipo (nombre) VALUES 
('Laptop'), 
('PC Desktop'), 
('Monitor'), 
('Impresora'), 
('Teléfono IP')
ON DUPLICATE KEY UPDATE id=id;

-- Insert default departments
INSERT INTO departamentos (nombre) VALUES 
('Soporte Técnico'),
('Sistemas / IT'),
('Administración'),
('Recursos Humanos'),
('Ventas')
ON DUPLICATE KEY UPDATE id=id;
