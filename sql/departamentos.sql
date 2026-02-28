-- Ubicación del archivo: sql/departamentos.sql

CREATE TABLE IF NOT EXISTS departamentos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL UNIQUE
);

INSERT IGNORE INTO departamentos (nombre) VALUES
('Administración'),
('Recursos Humanos'),
('Operaciones'),
('IT'),
('Ventas');
