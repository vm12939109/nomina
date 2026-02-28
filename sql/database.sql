-- Archivo: sql/database.sql

CREATE DATABASE IF NOT EXISTS nomina_venezuela;
USE nomina_venezuela;

-- 1. Roles
CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

INSERT IGNORE INTO roles (nombre) VALUES ('administrador'), ('supervisor'), ('empleado');

-- 2. Departamentos
CREATE TABLE IF NOT EXISTS departamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);

INSERT IGNORE INTO departamentos (nombre) VALUES ('Administración'), ('Recursos Humanos'), ('Operaciones'), ('IT'), ('Ventas');

-- 3. Cargos
CREATE TABLE IF NOT EXISTS cargos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE,
    sueldo_sugerido DECIMAL(15, 2) DEFAULT 0
);

INSERT IGNORE INTO cargos (nombre, sueldo_sugerido) VALUES
('Gerente General', 15000.00),
('Analista Contable', 8000.00),
('Especialista IT', 10000.00),
('Asistente Administrativo', 5000.00),
('Obrero Especializado', 4500.00);

-- 4. Usuarios (Empleados)
CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol_id INT NOT NULL,
    cargo_id INT DEFAULT NULL,
    departamento_id INT DEFAULT NULL,
    fecha_ingreso DATE DEFAULT (CURRENT_DATE),
    sueldo_base DECIMAL(15, 2) DEFAULT 0,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id),
    FOREIGN KEY (cargo_id) REFERENCES cargos(id),
    FOREIGN KEY (departamento_id) REFERENCES departamentos(id)
);

-- 5. Nómina (Historial de Pagos)
CREATE TABLE IF NOT EXISTS nomina (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo_pago ENUM('7', '15', '30') NOT NULL, -- Días del ciclo
    sueldo_base DECIMAL(15, 2) NOT NULL,      -- Sueldo base al momento del pago
    asignaciones DECIMAL(15, 2) DEFAULT 0,
    deducciones DECIMAL(15, 2) DEFAULT 0,
    total_neto DECIMAL(15, 2) NOT NULL,
    fecha_pago DATE NOT NULL,
    periodo_desde DATE DEFAULT NULL,
    periodo_hasta DATE DEFAULT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
