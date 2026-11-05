CREATE DATABASE IF NOT EXISTS sistema_gestion CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sistema_gestion;

-- 1. Roles (Gerente y Empleado)
CREATE TABLE IF NOT EXISTS roles (
    id_rol INT AUTO_INCREMENT PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL,
    descripcion VARCHAR(255)
);

-- 2. Usuarios
CREATE TABLE IF NOT EXISTS usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    id_rol INT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    estado TINYINT DEFAULT 1,
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol) ON DELETE SET NULL
);

-- 3. Entidad de Gestión: Medicamentos Naturales
CREATE TABLE IF NOT EXISTS medicamentos (
    id_medicamento INT AUTO_INCREMENT PRIMARY KEY,
    id_usuario_responsable INT,
    codigo_ref VARCHAR(50) NOT NULL UNIQUE,          -- Ej: NAT-001
    codigo_ubicacion VARCHAR(50) NOT NULL,           -- Ej: EST-A-03 (Estante A, Fila 3)
    nombre VARCHAR(100) NOT NULL,                    -- Nombre del producto natural
    laboratorio VARCHAR(100) NOT NULL,               -- Laboratorio / Casa naturista proveedora
    descripcion VARCHAR(255),                        -- Propiedades o indicaciones
    precio DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    stock INT NOT NULL DEFAULT 0,
    estado TINYINT DEFAULT 1,                        -- 1 = Activo, 0 = Inactivo (Borrado Lógico)
    fecha_registro DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario_responsable) REFERENCES usuarios(id_usuario) ON DELETE CASCADE
);

-- 4. Roles base
INSERT INTO roles (id_rol, nombre_rol, descripcion) VALUES 
(1, 'Gerente', 'Control total del inventario, personal y reportes'),
(2, 'Empleado', 'Registro y actualización de medicamentos botánicos');

-- 5. Usuarios de prueba (contraseña: password123)
INSERT INTO usuarios (id_usuario, id_rol, username, password, estado) VALUES 
(1, 1, 'gerente', '$2y$10$wEkg/oBv1/p0d1i4i99s1.oH2b7o4Wn5vM0B2Y8lWp2mY3K7FqH7.', 1),
(2, 2, 'empleado', '$2y$10$wEkg/oBv1/p0d1i4i99s1.oH2b7o4Wn5vM0B2Y8lWp2mY3K7FqH7.', 1);

-- 6. Semilla de datos con laboratorio y ubicación en estantería
INSERT INTO medicamentos (id_usuario_responsable, codigo_ref, codigo_ubicacion, nombre, laboratorio, descripcion, precio, stock, estado) VALUES 
(1, 'NAT-001', 'EST-A-01', 'Extracto de Valeriana 60ml', 'Laboratorios BioNatura', 'Sedante y relajante muscular para el insomnio', 4.50, 25, 1),
(2, 'NAT-002', 'EST-A-03', 'Jarabe de Sábila y Miel 250ml', 'Herbal Medic', 'Desinflamatorio gastrointestinal y regenerador gástrico', 6.00, 40, 1),
(1, 'NAT-003', 'EST-B-02', 'Cápsulas de Moringa 500mg (60 caps)', 'Fitofarma C.A.', 'Suplemento antioxidante y energizante natural', 7.50, 18, 1),
(2, 'NAT-004', 'EST-C-01', 'Infusión de Toronjil y Manzanilla', 'Botánica El Edén', 'Digestivo y calmante estomacal tradicional', 2.80, 50, 1);