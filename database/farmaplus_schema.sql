-- ============================================================
-- FarmaPlus CRM — Schema de base de datos
-- MySQL 8.0 compatible · 3FN · 17 tablas
-- ============================================================
-- Ejecutar en MySQL Workbench como usuario root
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
    cliente_id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id            INT UNSIGNED NOT NULL UNIQUE,
    consentimiento_ley1581 TINYINT(1)  NOT NULL DEFAULT 0,
    fecha_consentimiento  DATETIME     NULL,
    ip_consentimiento     VARCHAR(45)  NOT NULL DEFAULT '',
    created_at            DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
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
-- ============================================================
CREATE TABLE IF NOT EXISTS productos (
    producto_id        INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    categoria_id       INT UNSIGNED NULL,
    proveedor_id       INT UNSIGNED NULL,
    nombre             VARCHAR(200) NOT NULL,
    principio_activo   VARCHAR(200) NOT NULL DEFAULT '',
    concentracion      VARCHAR(80)  NOT NULL DEFAULT '',
    forma_farmaceutica VARCHAR(80)  NOT NULL DEFAULT '',
    codigo_invima      VARCHAR(50)  NOT NULL COMMENT 'Obligatorio — Decreto 677/1995',
    control_especial   TINYINT(1)   NOT NULL DEFAULT 0 COMMENT 'Ley 2300/2023 — antibióticos/opioides/psicotrópicos',
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
    CONSTRAINT fk_lotes_producto   FOREIGN KEY (producto_id)   REFERENCES productos (producto_id)  ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT fk_lotes_proveedor  FOREIGN KEY (proveedor_id)  REFERENCES proveedores (proveedor_id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT fk_lotes_usuario    FOREIGN KEY (registrado_por) REFERENCES usuarios (usuario_id)   ON UPDATE RESTRICT ON DELETE RESTRICT
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
    CONSTRAINT fk_ajustes_lote     FOREIGN KEY (lote_id)     REFERENCES lotes    (lote_id)     ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT fk_ajustes_usuario  FOREIGN KEY (usuario_id)  REFERENCES usuarios (usuario_id)  ON UPDATE RESTRICT ON DELETE RESTRICT
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
    CONSTRAINT fk_alertas_lote     FOREIGN KEY (lote_id)     REFERENCES lotes    (lote_id)     ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 11. pedidos (pedidos en línea)
-- ============================================================
CREATE TABLE IF NOT EXISTS pedidos (
    pedido_id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    cliente_id          INT UNSIGNED NOT NULL,
    direccion_entrega_id INT UNSIGNED NOT NULL,
    repartidor_id       INT UNSIGNED NULL,
    estado              ENUM('pendiente','pagado','en_preparacion','en_camino','entregado','cancelado','devuelto') NOT NULL DEFAULT 'pendiente',
    subtotal            DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    costo_envio         DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total               DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    mp_referencia       VARCHAR(100) NOT NULL DEFAULT '',
    mp_payment_id       VARCHAR(100) NOT NULL DEFAULT '',
    observacion         TEXT         NULL,
    created_at          DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at          DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_pedidos_cliente      FOREIGN KEY (cliente_id)           REFERENCES clientes          (cliente_id)   ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT fk_pedidos_direccion    FOREIGN KEY (direccion_entrega_id) REFERENCES direcciones_entrega (direccion_id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT fk_pedidos_repartidor   FOREIGN KEY (repartidor_id)        REFERENCES usuarios           (usuario_id)   ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 12. detalle_pedido
-- ============================================================
CREATE TABLE IF NOT EXISTS detalle_pedido (
    detalle_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    pedido_id       INT UNSIGNED    NOT NULL,
    producto_id     INT UNSIGNED    NOT NULL,
    lote_id         INT UNSIGNED    NOT NULL COMMENT 'Trazabilidad INVIMA',
    cantidad        INT UNSIGNED    NOT NULL,
    precio_unitario DECIMAL(12,2)   NOT NULL,
    subtotal        DECIMAL(12,2)   NOT NULL,
    CONSTRAINT fk_dp_pedido   FOREIGN KEY (pedido_id)   REFERENCES pedidos   (pedido_id)   ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT fk_dp_producto FOREIGN KEY (producto_id) REFERENCES productos (producto_id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT fk_dp_lote     FOREIGN KEY (lote_id)     REFERENCES lotes     (lote_id)     ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 13. ventas_presenciales (POS)
-- ============================================================
CREATE TABLE IF NOT EXISTS ventas_presenciales (
    venta_id           INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    vendedor_id        INT UNSIGNED NOT NULL,
    numero_comprobante VARCHAR(20)  NOT NULL UNIQUE COMMENT 'Formato FP-{AÑO}-{SEQ}',
    subtotal           DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    total              DECIMAL(12,2) NOT NULL DEFAULT 0.00,
    metodo_pago        ENUM('efectivo','tarjeta_debito','tarjeta_credito','transferencia') NOT NULL DEFAULT 'efectivo',
    formula_medica     VARCHAR(100) NOT NULL DEFAULT '' COMMENT 'Número de fórmula para control especial',
    created_at         DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_ventas_vendedor FOREIGN KEY (vendedor_id) REFERENCES usuarios (usuario_id) ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 14. detalle_venta
-- ============================================================
CREATE TABLE IF NOT EXISTS detalle_venta (
    detalle_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    venta_id        INT UNSIGNED    NOT NULL,
    producto_id     INT UNSIGNED    NOT NULL,
    lote_id         INT UNSIGNED    NOT NULL COMMENT 'Trazabilidad INVIMA',
    cantidad        INT UNSIGNED    NOT NULL,
    precio_unitario DECIMAL(12,2)   NOT NULL,
    subtotal        DECIMAL(12,2)   NOT NULL,
    CONSTRAINT fk_dv_venta    FOREIGN KEY (venta_id)    REFERENCES ventas_presenciales (venta_id)    ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT fk_dv_producto FOREIGN KEY (producto_id) REFERENCES productos           (producto_id) ON UPDATE RESTRICT ON DELETE RESTRICT,
    CONSTRAINT fk_dv_lote     FOREIGN KEY (lote_id)     REFERENCES lotes               (lote_id)     ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- 15. configuracion (patrón clave-valor)
-- ============================================================
CREATE TABLE IF NOT EXISTS configuracion (
    config_id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    clave        VARCHAR(80)  NOT NULL UNIQUE,
    valor        VARCHAR(255) NOT NULL,
    descripcion  VARCHAR(255) NOT NULL DEFAULT '',
    editado_por  INT UNSIGNED NULL,
    updated_at   DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
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
    log_id      INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    usuario_id  INT UNSIGNED NOT NULL,
    accion      VARCHAR(100) NOT NULL,
    detalle     TEXT         NOT NULL,
    ip          VARCHAR(45)  NOT NULL DEFAULT '',
    created_at  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_logs_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios (usuario_id) ON UPDATE RESTRICT ON DELETE RESTRICT
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
