-- Archivo: sql/database.sql

CREATE DATABASE IF NOT EXISTS nomina_venezuela;
USE nomina_venezuela;

CREATE TABLE IF NOT EXISTS roles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50) NOT NULL UNIQUE
);

INSERT IGNORE INTO roles (nombre) VALUES ('administrador'), ('supervisor'), ('empleado');

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(20) NOT NULL UNIQUE,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol_id INT NOT NULL,
    fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (rol_id) REFERENCES roles(id)
);

CREATE TABLE IF NOT EXISTS nomina (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo_pago ENUM('7', '15', '30') NOT NULL, -- Días
    sueldo_base DECIMAL(15, 2) NOT NULL,
    asignaciones DECIMAL(15, 2) DEFAULT 0,
    deducciones DECIMAL(15, 2) DEFAULT 0,
    total_neto DECIMAL(15, 2) NOT NULL,
    fecha_pago DATE NOT NULL,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
