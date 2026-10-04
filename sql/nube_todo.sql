A-- ============================================================
-- SISTEMA DE VENTAS
-- ============================================================

-- Reinicio total: borra la base si ya existe y la crea de nuevo desde cero.

-- ------------------------------------------------------------
-- TABLA: usuarios
-- ------------------------------------------------------------
CREATE TABLE usuarios (
    id_usuario   INT          NOT NULL AUTO_INCREMENT,
    nombre       VARCHAR(100) NOT NULL,
    email        VARCHAR(150) NOT NULL UNIQUE,
    telefono     VARCHAR(20),
    activo       TINYINT(1)   NOT NULL DEFAULT 1,
    created_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT pk_usuarios PRIMARY KEY (id_usuario)
);
-- ------------------------------------------------------------
-- TABLA: productos
-- ------------------------------------------------------------
CREATE TABLE productos (
    id_producto  INT            NOT NULL AUTO_INCREMENT,
    nombre       VARCHAR(150)   NOT NULL,
    descripcion  TEXT,
    precio       DECIMAL(10, 2) NOT NULL,
    stock        INT            NOT NULL DEFAULT 0,
    imagen       VARCHAR(255),  -- nombre del archivo guardado en la carpeta imagenes_productos/
    activo       TINYINT(1)     NOT NULL DEFAULT 1,
    created_at   TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at   TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT pk_productos PRIMARY KEY (id_producto),
    CONSTRAINT chk_precio   CHECK (precio >= 0),
    CONSTRAINT chk_stock    CHECK (stock  >= 0)
);

-- ------------------------------------------------------------
-- TABLA: ventas
-- ------------------------------------------------------------
CREATE TABLE ventas (
    id_venta      INT            NOT NULL AUTO_INCREMENT,
    id_usuario    INT            NOT NULL,
    id_producto   INT            NOT NULL,
    cantidad      INT            NOT NULL DEFAULT 1,
    precio_unit   DECIMAL(10, 2) NOT NULL,  -- precio al momento de la venta
    total_bruto   DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    tasa_impuesto DECIMAL(5, 2)  NOT NULL DEFAULT 16.00, -- porcentaje, ej: 16.00 = 16%
    total_impuesto DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    total_final   DECIMAL(10, 2) NOT NULL DEFAULT 0.00,
    created_at    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at    TIMESTAMP      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT pk_ventas        PRIMARY KEY (id_venta),
    CONSTRAINT fk_venta_usuario FOREIGN KEY (id_usuario)  REFERENCES usuarios  (id_usuario),
    CONSTRAINT fk_venta_producto FOREIGN KEY (id_producto) REFERENCES productos (id_producto),
    CONSTRAINT chk_cantidad     CHECK (cantidad > 0)
);

-- ------------------------------------------------------------
-- TRIGGERS: calculan los totales de ventas (una sola instrucción, sin DELIMITER)
-- ------------------------------------------------------------
CREATE TRIGGER trg_ventas_before_insert
BEFORE INSERT ON ventas
FOR EACH ROW
SET NEW.total_bruto    = NEW.cantidad * NEW.precio_unit,
    NEW.total_impuesto = NEW.cantidad * NEW.precio_unit * (NEW.tasa_impuesto / 100),
    NEW.total_final    = NEW.cantidad * NEW.precio_unit * (1 + NEW.tasa_impuesto / 100);

CREATE TRIGGER trg_ventas_before_update
BEFORE UPDATE ON ventas
FOR EACH ROW
SET NEW.total_bruto    = NEW.cantidad * NEW.precio_unit,
    NEW.total_impuesto = NEW.cantidad * NEW.precio_unit * (NEW.tasa_impuesto / 100),
    NEW.total_final    = NEW.cantidad * NEW.precio_unit * (1 + NEW.tasa_impuesto / 100);

-- ============================================================
-- DATOS DE PRUEBA
-- ============================================================

INSERT INTO usuarios (nombre, email, telefono) VALUES
    ('Ana García',   'ana.garcia@correo.com',   '555-1001'),
    ('Luis Pérez',   'luis.perez@correo.com',   '555-1002'),
    ('María López',  'maria.lopez@correo.com',  '555-1003');

INSERT INTO productos (nombre, descripcion, precio, stock) VALUES
    ('Laptop Pro 15',  'Laptop de alto rendimiento 15"', 18500.00, 10),
    ('Mouse Inalámbrico', 'Mouse ergonómico 2.4 GHz',    350.00,   50),
    ('Teclado Mecánico',  'Teclado retroiluminado RGB',  850.00,   30);

-- Los totales se calculan automáticamente por el trigger
INSERT INTO ventas (id_usuario, id_producto, cantidad, precio_unit, tasa_impuesto) VALUES
    (1, 1, 1, 18500.00, 16.00),  -- Ana compra 1 Laptop
    (2, 2, 3,   350.00, 16.00),  -- Luis compra 3 Mouse
    (3, 3, 2,   850.00, 16.00);  -- María compra 2 Teclados


-- ============================================================
-- ENCUESTA Y REGISTRO
-- ============================================================
CREATE TABLE respuestas (
    id_respuesta  INT          NOT NULL AUTO_INCREMENT,
    carrera       VARCHAR(80)  NOT NULL,
    cuatrimestre  TINYINT      NOT NULL,
    profesores    TINYINT      NOT NULL,
    instalaciones VARCHAR(30)  NOT NULL,
    mejorar       VARCHAR(80)  NOT NULL,
    created_at    TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_respuestas PRIMARY KEY (id_respuesta)
);

CREATE TABLE registros (
    id_registro INT          NOT NULL AUTO_INCREMENT,
    nombre      VARCHAR(100) NOT NULL,
    edad        TINYINT UNSIGNED NOT NULL,
    correo      VARCHAR(150) NOT NULL UNIQUE,
    sexo        VARCHAR(20)  NOT NULL,
    created_at  TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT pk_registros PRIMARY KEY (id_registro)
);
