--miprograma para la tarea extra ---
-- Tabla de roles de usuario
CREATE TABLE roles (
    id_rol SERIAL PRIMARY KEY,
    nombre_rol VARCHAR(50) NOT NULL UNIQUE,
    descripcion VARCHAR(200),
    fecha_creacion DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND
);

-- Tabla de usuarios
CREATE TABLE usuarios (
    id_usuario SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    id_rol INTEGER NOT NULL,
    activo CHAR(1) DEFAULT 'S' CHECK (activo IN ('S', 'N')),
    fecha_creacion DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
    fecha_actualizacion DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
    FOREIGN KEY (id_rol) REFERENCES roles(id_rol)
);

-- Tabla de marcas de celulares
CREATE TABLE marcas (
    id_marca SERIAL PRIMARY KEY,
    nombre_marca VARCHAR(100) NOT NULL UNIQUE,
    descripcion VARCHAR(200),
    activo CHAR(1) DEFAULT 'S' CHECK (activo IN ('S', 'N')),
    fecha_creacion DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND
);

-- Tabla de clientes

CREATE TABLE clientes (
    id_cliente SERIAL PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    apellido VARCHAR(100) NOT NULL,
    cedula VARCHAR(20) UNIQUE,
    nit VARCHAR(20),
    email VARCHAR(150),
    telefono VARCHAR(20),
    direccion VARCHAR(100),
    fecha_creacion DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
    fecha_actualizacion DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND
);

-- Tabla de inventario de celulares
CREATE TABLE inventario (
    id_inventario SERIAL PRIMARY KEY,
    modelo VARCHAR(100) NOT NULL,
    id_marca INTEGER NOT NULL,
    imei VARCHAR(20) UNIQUE,
    numero_serie VARCHAR(50),
    estado_dispositivo VARCHAR(50) NOT NULL, -- Nuevo, Usado, Reparado, etc.
    precio_compra DECIMAL(10,2),
    precio_venta DECIMAL(10,2),
    stock INTEGER DEFAULT 0,
    descripcion TEXT,
    fecha_ingreso DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
    fecha_actualizacion DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
    FOREIGN KEY (id_marca) REFERENCES marcas(id_marca)
);

-- Tabla de ventas
CREATE TABLE ventas (
    id_venta SERIAL PRIMARY KEY,
    id_cliente INTEGER NOT NULL,
    id_usuario INTEGER NOT NULL, -- Usuario que realiza la venta
    fecha_venta DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
    subtotal DECIMAL(10,2) NOT NULL DEFAULT 0,
    impuesto DECIMAL(10,2) NOT NULL DEFAULT 0,
    total DECIMAL(10,2) NOT NULL DEFAULT 0,
    estado_venta VARCHAR(20) DEFAULT 'Completada' CHECK (estado_venta IN ('Pendiente', 'Completada', 'Cancelada')),
    observaciones TEXT,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- Tabla de detalle de ventas
CREATE TABLE detalle_ventas (
    id_detalle SERIAL PRIMARY KEY,
    id_venta INTEGER NOT NULL,
    id_inventario INTEGER NOT NULL,
    cantidad INTEGER NOT NULL DEFAULT 1,
    precio_unitario DECIMAL(10,2) NOT NULL,
    subtotal DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (id_venta) REFERENCES ventas(id_venta) ON DELETE CASCADE,
    FOREIGN KEY (id_inventario) REFERENCES inventario(id_inventario)
);

-- Tabla de reparaciones
CREATE TABLE reparaciones (
    id_reparacion SERIAL PRIMARY KEY,
    id_cliente INTEGER NOT NULL,
    tipo_celular VARCHAR(100) NOT NULL,
    marca_celular VARCHAR(100) NOT NULL,
    modelo_celular VARCHAR(100),
    imei VARCHAR(20),
    motivo_ingreso TEXT NOT NULL,
    descripcion_problema TEXT,
    fecha_ingreso DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
    fecha_entrega_estimada DATE,
    fecha_entrega_real DATETIME YEAR TO SECOND,
    id_trabajador INTEGER, -- Usuario asignado
    estado_reparacion VARCHAR(30) DEFAULT 'Recibido' CHECK (estado_reparacion IN ('Recibido', 'En Proceso', 'Terminado', 'Entregado', 'Cancelado')),
    costo_reparacion DECIMAL(10,2),
    observaciones TEXT,
    FOREIGN KEY (id_cliente) REFERENCES clientes(id_cliente),
    FOREIGN KEY (id_trabajador) REFERENCES usuarios(id_usuario)
);

-- Tabla de historial de ventas (para estadísticas)
CREATE TABLE historial_ventas (
    id_historial SERIAL PRIMARY KEY,
    id_venta INTEGER NOT NULL,
    tipo_operacion VARCHAR(20) NOT NULL, -- Venta, Reparacion
    fecha_operacion DATETIME YEAR TO SECOND DEFAULT CURRENT YEAR TO SECOND,
    monto DECIMAL(10,2) NOT NULL,
    id_usuario INTEGER NOT NULL,
    descripcion VARCHAR(200),
    FOREIGN KEY (id_venta) REFERENCES ventas(id_venta),
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id_usuario)
);

-- Crear índices para mejorar el rendimiento
CREATE INDEX idx_usuarios_email ON usuarios(email);
CREATE INDEX idx_usuarios_rol ON usuarios(id_rol);
CREATE INDEX idx_inventario_marca ON inventario(id_marca);
CREATE INDEX idx_inventario_imei ON inventario(imei);
CREATE INDEX idx_ventas_cliente ON ventas(id_cliente);
CREATE INDEX idx_ventas_fecha ON ventas(fecha_venta);
CREATE INDEX idx_reparaciones_cliente ON reparaciones(id_cliente);
CREATE INDEX idx_reparaciones_estado ON reparaciones(estado_reparacion);
CREATE INDEX idx_detalle_venta ON detalle_ventas(id_venta);

-- Insertar datos iniciales
INSERT INTO roles (nombre_rol, descripcion) VALUES ('Administrador', 'Acceso completo al sistema');
INSERT INTO roles (nombre_rol, descripcion) VALUES ('Empleado', 'Acceso a ventas, inventario y reparaciones');
INSERT INTO roles (nombre_rol, descripcion) VALUES ('Gerente', 'Acceso a estadísticas y reportes');

INSERT INTO marcas (nombre_marca, descripcion) VALUES ('Samsung', 'Smartphones y accesorios Samsung');
INSERT INTO marcas (nombre_marca, descripcion) VALUES ('Apple', 'iPhone y accesorios Apple');
INSERT INTO marcas (nombre_marca, descripcion) VALUES ('Huawei', 'Dispositivos Huawei');
INSERT INTO marcas (nombre_marca, descripcion) VALUES ('Xiaomi', 'Smartphones Xiaomi');
INSERT INTO marcas (nombre_marca, descripcion) VALUES ('LG', 'Dispositivos LG');
INSERT INTO marcas (nombre_marca, descripcion) VALUES ('Motorola', 'Smartphones Motorola');

-- Usuario administrador por defecto (password: admin123)
INSERT INTO usuarios (nombre, apellido, email, password, id_rol) VALUES 
('Admin', 'Sistema', 'admin@inventario.com', 'admin123', 1);