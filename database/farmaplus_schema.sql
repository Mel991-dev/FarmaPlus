-- ============================================================
-- FarmaPlus CRM — Schema de base de datos
-- MySQL 5.7+ compatible · 3FN · 17 tablas
-- Última actualización: Semana 4 (E-commerce + Domicilios)
-- ============================================================
-- Instrucciones:
--   1. Ejecutar en phpMyAdmin o línea de comandos MySQL como root
--   2. Este script crea la BD completa desde cero (idempotente)
--   3. Incluye todos los cambios de la migración semana4
-- ============================================================

CREATE DATABASE IF NOT EXISTS farmaplus
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE farmaplus;

-- ============================================================
-- 1. roles
-- ============================================================
CREATE TABLE IF NOT EXISTS roles (
    rol_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre      VARCHAR(50)  NOT NULL UNIQUE,
    descripcion VARCHAR(255) NOT NULL DEFAULT '',
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Roles base del sistema
INSERT IGNORE INTO roles (nombre, descripcion) VALUES
    ('admin',       'Administrador con acceso total al sistema'),
    ('farmaceutico', 'Farmacéutico regente — validación y control especial'),
    ('cajero',      'Auxiliar de ventas presenciales (POS)'),
    ('bodeguero',   'Gestión de inventario y recepción de lotes'),
    ('repartidor',  'Domicilios — actualización de estado de pedidos'),
    ('cliente',     'Cliente registrado en la tienda en línea');

-- ============================================================
-- 2. categorias_producto
-- ============================================================
CREATE TABLE IF NOT EXISTS categorias_producto (
    categoria_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nombre       VARCHAR(100) NOT NULL UNIQUE,
    descripcion  VARCHAR(255) NOT NULL DEFAULT '',
    created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 3. proveedores
-- ============================================================
CREATE TABLE IF NOT EXISTS proveedores (
    proveedor_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    nit          VARCHAR(20)  NOT NULL UNIQUE,
    nombre       VARCHAR(150) NOT NULL,
    pais_origen  VARCHAR(80)  NOT NULL DEFAULT 'Colombia',
    telefono     VARCHAR(30)  NOT NULL DEFAULT '',
    correo       VARCHAR(120) NOT NULL DEFAULT '',
    sitio_web    VARCHAR(200) NOT NULL DEFAULT '',
    activo       TINYINT(1)   NOT NULL DEFAULT 1,
    created_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 4. usuarios (todos los actores internos + clientes)
-- ============================================================
CREATE TABLE IF NOT EXISTS usuarios (
    usuario_id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    rol_id            INT UNSIGNED NOT NULL,
    tipo_documento    ENUM('CC','CE','NIT','PEP','PPT') NOT NULL DEFAULT 'CC',
    documento         VARCHAR(20)  NOT NULL,
    nombres           VARCHAR(100) NOT NULL,
    apellidos         VARCHAR(100) NOT NULL,
    correo            VARCHAR(120) NOT NULL,
    telefono          VARCHAR(20)  NOT NULL DEFAULT '',
    contrasena_hash   VARCHAR(255) NOT NULL,
    activo            TINYINT(1)   NOT NULL DEFAULT 1,
    intentos_fallidos TINYINT(1)   NOT NULL DEFAULT 0,
    bloqueado_hasta   DATETIME     NULL,
    created_at        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_documento (documento),
    UNIQUE KEY uq_correo    (correo),
    CONSTRAINT fk_usuarios_rol FOREIGN KEY (rol_id)
        REFERENCES roles (rol_id)
        ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 5. clientes (extensión de usuarios para el rol cliente)
-- ============================================================
CREATE TABLE IF NOT EXISTS clientes (
    cliente_id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id             INT UNSIGNED NOT NULL UNIQUE,
    consentimiento_ley1581 TINYINT(1)  NOT NULL DEFAULT 0
        COMMENT 'Ley 1581/2012 — Habeas Data',
    fecha_consentimiento   DATETIME     NULL,
    ip_consentimiento      VARCHAR(45)  NOT NULL DEFAULT '',
    created_at             DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_clientes_usuario FOREIGN KEY (usuario_id)
        REFERENCES usuarios (usuario_id)
        ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 6. direcciones_entrega
-- ============================================================
CREATE TABLE IF NOT EXISTS direcciones_entrega (
    direccion_id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_id     INT UNSIGNED NOT NULL,
    alias          VARCHAR(50)  NOT NULL DEFAULT 'Principal',
    direccion      VARCHAR(200) NOT NULL,
    barrio         VARCHAR(100) NOT NULL DEFAULT '',
    ciudad         VARCHAR(100) NOT NULL DEFAULT 'Neiva',
    referencia     VARCHAR(255) NOT NULL DEFAULT '',
    predeterminada TINYINT(1)   NOT NULL DEFAULT 0,
    created_at     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_direcciones_cliente FOREIGN KEY (cliente_id)
        REFERENCES clientes (cliente_id)
        ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 7. productos
-- Actualización: soporte para productos de miscelánea (no medicamentos)
--   · es_medicamento: 1=Medicamento, 0=Miscelánea/Consumo
--   · principio_activo, concentracion, forma_farmaceutica, codigo_invima
--     ahora son NULL para productos que no requieren registro farmacéutico
--   · UNIQUE KEY uq_invima permite múltiples NULLs en MySQL (no hay conflicto)
-- ============================================================
CREATE TABLE IF NOT EXISTS productos (
    producto_id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    categoria_id       INT UNSIGNED NULL,
    proveedor_id       INT UNSIGNED NULL,
    es_medicamento     TINYINT(1)   NOT NULL DEFAULT 1
        COMMENT '1=Medicamento (requiere INVIMA), 0=Miscelánea/Consumo',
    nombre             VARCHAR(200) NOT NULL,
    principio_activo   VARCHAR(200) NULL DEFAULT NULL,
    concentracion      VARCHAR(80)  NULL DEFAULT NULL,
    forma_farmaceutica VARCHAR(80)  NULL DEFAULT NULL,
    codigo_invima      VARCHAR(50)  NULL DEFAULT NULL
        COMMENT 'Obligatorio solo para medicamentos — Decreto 677/1995',
    control_especial   TINYINT(1)   NOT NULL DEFAULT 0
        COMMENT 'Ley 2300/2023 — antibióticos/opioides/psicotrópicos',
    precio_compra      DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    precio_venta       DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    stock_minimo       INT UNSIGNED  NOT NULL DEFAULT 10,
    activo             TINYINT(1)   NOT NULL DEFAULT 1,
    created_at         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    UNIQUE KEY uq_invima (codigo_invima),
    CONSTRAINT fk_productos_categoria FOREIGN KEY (categoria_id)
        REFERENCES categorias_producto (categoria_id)
        ON UPDATE RESTRICT ON DELETE SET NULL,
    CONSTRAINT fk_productos_proveedor FOREIGN KEY (proveedor_id)
        REFERENCES proveedores (proveedor_id)
        ON UPDATE RESTRICT ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 8. lotes (entradas de mercancía con fecha vencimiento — FEFO)
-- ============================================================
CREATE TABLE IF NOT EXISTS lotes (
    lote_id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    producto_id       INT UNSIGNED NOT NULL,
    proveedor_id      INT UNSIGNED NULL,
    numero_lote       VARCHAR(50)  NOT NULL,
    cantidad_inicial  INT UNSIGNED NOT NULL,
    cantidad_actual   INT UNSIGNED NOT NULL,
    fecha_vencimiento DATE         NOT NULL,
    fecha_entrada     DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    registrado_por    INT UNSIGNED NOT NULL,
    activo            TINYINT(1)   NOT NULL DEFAULT 1,
    created_at        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_lotes_producto  FOREIGN KEY (producto_id)    REFERENCES productos   (producto_id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT fk_lotes_proveedor FOREIGN KEY (proveedor_id)   REFERENCES proveedores (proveedor_id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT fk_lotes_usuario   FOREIGN KEY (registrado_por) REFERENCES usuarios    (usuario_id)  ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 9. ajustes_stock (historial de todos los movimientos)
-- ============================================================
CREATE TABLE IF NOT EXISTS ajustes_stock (
    ajuste_id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    producto_id INT UNSIGNED NOT NULL,
    lote_id     INT UNSIGNED NULL,
    usuario_id  INT UNSIGNED NOT NULL,
    tipo        ENUM('entrada','salida_venta','salida_pedido','ajuste_manual','baja_vencimiento') NOT NULL,
    cantidad    INT          NOT NULL,
    observacion VARCHAR(255) NOT NULL DEFAULT '',
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ajustes_producto FOREIGN KEY (producto_id) REFERENCES productos (producto_id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT fk_ajustes_lote     FOREIGN KEY (lote_id)     REFERENCES lotes     (lote_id)     ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT fk_ajustes_usuario  FOREIGN KEY (usuario_id)  REFERENCES usuarios  (usuario_id)  ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 10. alertas
-- ============================================================
CREATE TABLE IF NOT EXISTS alertas (
    alerta_id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    producto_id INT UNSIGNED NOT NULL,
    lote_id     INT UNSIGNED NULL,
    tipo        ENUM('stock_minimo','vencimiento') NOT NULL,
    mensaje     VARCHAR(255) NOT NULL,
    estado      ENUM('activa','resuelta') NOT NULL DEFAULT 'activa',
    resuelta_at DATETIME     NULL,
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_alertas_producto FOREIGN KEY (producto_id) REFERENCES productos (producto_id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT fk_alertas_lote     FOREIGN KEY (lote_id)     REFERENCES lotes     (lote_id)     ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 11. pedidos (pedidos en línea — e-commerce)
-- Cambios semana 4:
--   · mp_referencia ahora es NULL (se asigna tras crear el pedido)
--   · mp_payment_id permanece con DEFAULT '' (puede ser NULL en nuevas BD)
--   · mp_status: estado del pago devuelto por MercadoPago
--   · observacion_devolucion: motivo registrado por el repartidor
--   · Índices de rendimiento: mp_referencia y (repartidor_id, estado)
-- ============================================================
CREATE TABLE IF NOT EXISTS pedidos (
    pedido_id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_id            INT UNSIGNED  NOT NULL,
    direccion_entrega_id  INT UNSIGNED  NOT NULL,
    repartidor_id         INT UNSIGNED  NULL,
    estado                ENUM('pendiente','pagado','en_preparacion','en_camino','entregado','cancelado','devuelto')
                                        NOT NULL DEFAULT 'pendiente',
    subtotal              DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    costo_envio           DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total                 DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    mp_referencia         VARCHAR(100)  NULL
        COMMENT 'Referencia externa enviada a MercadoPago (= pedido_id)',
    mp_payment_id         VARCHAR(100)  NOT NULL DEFAULT ''
        COMMENT 'ID del pago retornado por MercadoPago',
    mp_status             VARCHAR(50)   NULL
        COMMENT 'Estado del pago: approved, rejected, pending, in_process',
    observacion           TEXT          NULL,
    observacion_devolucion TEXT         NULL
        COMMENT 'Motivo de devolución registrado por el repartidor',
    created_at            DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at            DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    KEY idx_pedidos_mp_referencia (mp_referencia),
    KEY idx_pedidos_repartidor    (repartidor_id, estado),
    CONSTRAINT fk_pedidos_cliente    FOREIGN KEY (cliente_id)           REFERENCES clientes          (cliente_id)   ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT fk_pedidos_direccion  FOREIGN KEY (direccion_entrega_id) REFERENCES direcciones_entrega (direccion_id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT fk_pedidos_repartidor FOREIGN KEY (repartidor_id)        REFERENCES usuarios           (usuario_id)   ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 12. detalle_pedido
-- Cambios semana 4:
--   · lote_id pasa a INT NULL (sin FK fk_dp_lote)
--     → el lote se asigna post-pago mediante algoritmo FEFO
--     → permite insertar pedidos online sin lote inmediato
-- ============================================================
CREATE TABLE IF NOT EXISTS detalle_pedido (
    detalle_id      INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    pedido_id       INT UNSIGNED  NOT NULL,
    producto_id     INT UNSIGNED  NOT NULL,
    lote_id         INT           NULL
        COMMENT 'Opcional: se asigna al procesar el envío (FEFO post-pago)',
    cantidad        INT UNSIGNED  NOT NULL,
    precio_unitario DECIMAL(12,2) NOT NULL,
    subtotal        DECIMAL(12,2) NOT NULL,
    KEY fk_dp_pedido   (pedido_id),
    KEY fk_dp_producto (producto_id),
    KEY fk_dp_lote     (lote_id),
    CONSTRAINT fk_dp_pedido   FOREIGN KEY (pedido_id)   REFERENCES pedidos   (pedido_id)   ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT fk_dp_producto FOREIGN KEY (producto_id) REFERENCES productos (producto_id) ON UPDATE RESTRICT ON DELETE RESTRICT
    -- NOTA: fk_dp_lote eliminada en Semana 4 para permitir pedidos online sin asignación inmediata de lote
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 13. ventas_presenciales (POS)
-- ============================================================
CREATE TABLE IF NOT EXISTS ventas_presenciales (
    venta_id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vendedor_id        INT UNSIGNED NOT NULL,
    numero_comprobante VARCHAR(20)  NOT NULL UNIQUE
        COMMENT 'Formato FP-{AÑO}-{SEQ}',
    subtotal           DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total              DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    metodo_pago        ENUM('efectivo','tarjeta_debito','tarjeta_credito','transferencia') NOT NULL DEFAULT 'efectivo',
    formula_medica     VARCHAR(100) NOT NULL DEFAULT ''
        COMMENT 'Número de fórmula para dispensación de control especial',
    created_at         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ventas_vendedor FOREIGN KEY (vendedor_id) REFERENCES usuarios (usuario_id) ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 14. detalle_venta
-- ============================================================
CREATE TABLE IF NOT EXISTS detalle_venta (
    detalle_id      INT UNSIGNED  AUTO_INCREMENT PRIMARY KEY,
    venta_id        INT UNSIGNED  NOT NULL,
    producto_id     INT UNSIGNED  NOT NULL,
    lote_id         INT UNSIGNED  NOT NULL
        COMMENT 'Trazabilidad INVIMA — obligatorio en ventas presenciales',
    cantidad        INT UNSIGNED  NOT NULL,
    precio_unitario DECIMAL(12,2) NOT NULL,
    subtotal        DECIMAL(12,2) NOT NULL,
    CONSTRAINT fk_dv_venta    FOREIGN KEY (venta_id)    REFERENCES ventas_presenciales (venta_id)    ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT fk_dv_producto FOREIGN KEY (producto_id) REFERENCES productos           (producto_id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT fk_dv_lote     FOREIGN KEY (lote_id)     REFERENCES lotes               (lote_id)     ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 15. configuracion (patrón clave-valor)
-- ============================================================
CREATE TABLE IF NOT EXISTS configuracion (
    config_id   INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clave       VARCHAR(80)  NOT NULL UNIQUE,
    valor       VARCHAR(255) NOT NULL,
    descripcion VARCHAR(255) NOT NULL DEFAULT '',
    editado_por INT UNSIGNED NULL,
    updated_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_config_usuario FOREIGN KEY (editado_por) REFERENCES usuarios (usuario_id) ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 16. recuperacion_contrasena
-- ============================================================
CREATE TABLE IF NOT EXISTS recuperacion_contrasena (
    recuperacion_id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id      INT UNSIGNED NOT NULL,
    token           VARCHAR(64)  NOT NULL UNIQUE,
    expira_at       DATETIME     NOT NULL,
    usado           TINYINT(1)   NOT NULL DEFAULT 0,
    created_at      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_recuperacion_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (usuario_id) ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 17. logs_auditoria
-- ============================================================
CREATE TABLE IF NOT EXISTS logs_auditoria (
    log_id     INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT UNSIGNED NOT NULL,
    accion     VARCHAR(100) NOT NULL,
    detalle    TEXT         NOT NULL,
    ip         VARCHAR(45)  NOT NULL DEFAULT '',
    created_at DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_logs_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (usuario_id) ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 18. producto_imagenes
-- Hasta 4 imágenes por producto. orden=1 → imagen principal.
-- ON DELETE CASCADE: al borrar el producto se eliminan los
-- registros; el archivo físico debe eliminarse desde PHP.
-- ============================================================
CREATE TABLE IF NOT EXISTS producto_imagenes (
    imagen_id      INT UNSIGNED    AUTO_INCREMENT PRIMARY KEY,
    producto_id    INT UNSIGNED    NOT NULL,
    nombre_archivo VARCHAR(255)    NOT NULL
        COMMENT 'Nombre del archivo en public/assets/uploads/productos/{id}/',
    orden          TINYINT(1)      NOT NULL DEFAULT 1
        COMMENT '1 = imagen principal, 2-4 = imágenes adicionales',
    created_at     DATETIME        NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pimg_producto  FOREIGN KEY (producto_id)
        REFERENCES productos (producto_id)
        ON UPDATE RESTRICT ON DELETE CASCADE,
    CONSTRAINT uq_producto_orden UNIQUE (producto_id, orden),
    CONSTRAINT chk_orden         CHECK (orden BETWEEN 1 AND 4)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- FIN DEL SCHEMA
-- Total: 18 tablas
-- Versión: Semana 5 — Catálogo Multi-Producto + Imágenes
-- ============================================================