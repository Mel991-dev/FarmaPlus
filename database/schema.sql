-- ============================================================
--  CRM FARMA+ — Script de Base de Datos
--  Versión: 1.0.0
--  Motor: MySQL 8.x
--  Encoding: UTF8MB4
--  Fecha: 2026-02-22
--  Autor: Aprendiz SENA — Etapa de Aprendizaje
-- ============================================================
--  INSTRUCCIONES DE USO:
--  1. Abrir MySQL Workbench o cliente MySQL
--  2. Ejecutar este archivo completo
--  3. El script crea la base de datos si no existe
--  4. Incluye datos iniciales (seeders) al final
-- ============================================================

-- ────────────────────────────────────────────────────────────
--  CONFIGURACIÓN INICIAL
-- ────────────────────────────────────────────────────────────

SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0;
SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0;
SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='ONLY_FULL_GROUP_BY,STRICT_TRANS_TABLES,NO_ZERO_IN_DATE,NO_ZERO_DATE,ERROR_FOR_DIVISION_BY_ZERO,NO_ENGINE_SUBSTITUTION';

-- ────────────────────────────────────────────────────────────
--  BASE DE DATOS
-- ────────────────────────────────────────────────────────────

DROP DATABASE IF EXISTS crm_farma;
CREATE DATABASE crm_farma
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE crm_farma;

-- ============================================================
--  SECCIÓN 1: TABLAS DE CATÁLOGO
--  (sin dependencias externas — se crean primero)
-- ============================================================

-- ────────────────────────────────────────────────────────────
--  TABLA: roles
--  Catálogo de roles del sistema
--  RN-01: Un usuario solo puede tener un rol activo a la vez
-- ────────────────────────────────────────────────────────────
CREATE TABLE roles (
    rol_id          INT             NOT NULL AUTO_INCREMENT,
    nombre          VARCHAR(30)     NOT NULL,
    descripcion     VARCHAR(150)    NOT NULL,
    activo          TINYINT(1)      NOT NULL DEFAULT 1,
    creado_en       TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT pk_roles             PRIMARY KEY (rol_id),
    CONSTRAINT uq_roles_nombre      UNIQUE (nombre),
    CONSTRAINT ck_roles_activo      CHECK (activo IN (0, 1))
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Catálogo de roles del sistema CRM';

-- ────────────────────────────────────────────────────────────
--  TABLA: departamentos
--  Departamentos de Colombia según DANE
--  3FN: separado para evitar dependencia transitiva en ciudades
-- ────────────────────────────────────────────────────────────
CREATE TABLE departamentos (
    departamento_id INT             NOT NULL AUTO_INCREMENT,
    nombre          VARCHAR(80)     NOT NULL,
    codigo_dane     CHAR(2)         NOT NULL,

    CONSTRAINT pk_departamentos             PRIMARY KEY (departamento_id),
    CONSTRAINT uq_departamentos_nombre      UNIQUE (nombre),
    CONSTRAINT uq_departamentos_codigo      UNIQUE (codigo_dane)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Departamentos de Colombia - fuente DANE';

-- ────────────────────────────────────────────────────────────
--  TABLA: ciudades
--  Ciudades vinculadas a departamento
--  3FN: ciudad_id → nombre + departamento_id (no transitivo)
-- ────────────────────────────────────────────────────────────
CREATE TABLE ciudades (
    ciudad_id       INT             NOT NULL AUTO_INCREMENT,
    departamento_id INT             NOT NULL,
    nombre          VARCHAR(100)    NOT NULL,
    codigo_dane     CHAR(5)         NOT NULL,

    CONSTRAINT pk_ciudades                  PRIMARY KEY (ciudad_id),
    CONSTRAINT uq_ciudades_codigo           UNIQUE (codigo_dane),
    CONSTRAINT fk_ciudades_departamento     FOREIGN KEY (departamento_id)
        REFERENCES departamentos (departamento_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Ciudades de Colombia vinculadas a departamento';

-- ────────────────────────────────────────────────────────────
--  TABLA: especialidades
--  Especialidades médicas con código RETHUS
--  3FN: catálogo independiente — no depende del médico
-- ────────────────────────────────────────────────────────────
CREATE TABLE especialidades (
    especialidad_id INT             NOT NULL AUTO_INCREMENT,
    nombre          VARCHAR(100)    NOT NULL,
    codigo_rethus   VARCHAR(10)     NULL,
    activo          TINYINT(1)      NOT NULL DEFAULT 1,

    CONSTRAINT pk_especialidades            PRIMARY KEY (especialidad_id),
    CONSTRAINT uq_especialidades_nombre     UNIQUE (nombre),
    CONSTRAINT ck_especialidades_activo     CHECK (activo IN (0, 1))
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Especialidades médicas registradas en RETHUS';

-- ────────────────────────────────────────────────────────────
--  TABLA: tipos_documento_paciente
--  Catálogo de tipos de documento de identidad
-- ────────────────────────────────────────────────────────────
CREATE TABLE tipos_documento_paciente (
    tipo_doc_id     INT             NOT NULL AUTO_INCREMENT,
    codigo          CHAR(2)         NOT NULL,
    descripcion     VARCHAR(60)     NOT NULL,

    CONSTRAINT pk_tipos_doc                 PRIMARY KEY (tipo_doc_id),
    CONSTRAINT uq_tipos_doc_codigo          UNIQUE (codigo)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Tipos de documento de identidad para pacientes';

-- ────────────────────────────────────────────────────────────
--  TABLA: tipos_interaccion
--  Catálogo de tipos de interacción con clientes
-- ────────────────────────────────────────────────────────────
CREATE TABLE tipos_interaccion (
    tipo_interaccion_id INT          NOT NULL AUTO_INCREMENT,
    nombre              VARCHAR(60)  NOT NULL,
    color_hex           CHAR(7)      NOT NULL DEFAULT '#3498DB',
    activo              TINYINT(1)   NOT NULL DEFAULT 1,

    CONSTRAINT pk_tipos_interaccion         PRIMARY KEY (tipo_interaccion_id),
    CONSTRAINT uq_tipos_interaccion_nombre  UNIQUE (nombre),
    CONSTRAINT ck_tipos_interaccion_activo  CHECK (activo IN (0, 1))
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Tipos de interacción con clientes: Llamada, Visita, Correo, WhatsApp';

-- ────────────────────────────────────────────────────────────
--  TABLA: categorias_producto
--  Categorías farmacológicas
-- ────────────────────────────────────────────────────────────
CREATE TABLE categorias_producto (
    categoria_id    INT             NOT NULL AUTO_INCREMENT,
    nombre          VARCHAR(100)    NOT NULL,
    descripcion     VARCHAR(200)    NULL,
    activo          TINYINT(1)      NOT NULL DEFAULT 1,

    CONSTRAINT pk_categorias_producto       PRIMARY KEY (categoria_id),
    CONSTRAINT uq_categorias_nombre         UNIQUE (nombre),
    CONSTRAINT ck_categorias_activo         CHECK (activo IN (0, 1))
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Categorías farmacológicas del catálogo de productos';

-- ────────────────────────────────────────────────────────────
--  TABLA: laboratorios
--  Laboratorios farmacéuticos fabricantes
--  3FN: separado para evitar redundancia en productos
-- ────────────────────────────────────────────────────────────
CREATE TABLE laboratorios (
    laboratorio_id      INT             NOT NULL AUTO_INCREMENT,
    nombre              VARCHAR(150)    NOT NULL,
    pais_origen         VARCHAR(80)     NULL,
    telefono_soporte    VARCHAR(15)     NULL,
    correo_soporte      VARCHAR(120)    NULL,
    activo              TINYINT(1)      NOT NULL DEFAULT 1,
    creado_en           TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT pk_laboratorios              PRIMARY KEY (laboratorio_id),
    CONSTRAINT uq_laboratorios_nombre       UNIQUE (nombre),
    CONSTRAINT ck_laboratorios_activo       CHECK (activo IN (0, 1))
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Laboratorios farmacéuticos fabricantes';

-- ============================================================
--  SECCIÓN 2: TABLAS PRINCIPALES
--  (dependen de los catálogos)
-- ============================================================

-- ────────────────────────────────────────────────────────────
--  TABLA: usuarios
--  Usuarios internos del sistema CRM
--  RN-04: correo UNIQUE
--  RN-05: validación de contraseña en capa de negocio (bcrypt)
-- ────────────────────────────────────────────────────────────
CREATE TABLE usuarios (
    usuario_id          INT             NOT NULL AUTO_INCREMENT,
    rol_id              INT             NOT NULL,
    nombres             VARCHAR(80)     NOT NULL,
    apellidos           VARCHAR(80)     NOT NULL,
    numero_documento    VARCHAR(20)     NOT NULL,
    correo              VARCHAR(120)    NOT NULL,
    telefono            VARCHAR(15)     NULL,
    contrasena_hash     VARCHAR(255)    NOT NULL,
    activo              TINYINT(1)      NOT NULL DEFAULT 1,
    ultimo_acceso       TIMESTAMP       NULL,
    creado_en           TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    creado_por          INT             NULL,
    actualizado_en      TIMESTAMP       NULL ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT pk_usuarios                  PRIMARY KEY (usuario_id),
    CONSTRAINT uq_usuarios_correo           UNIQUE (correo),
    CONSTRAINT uq_usuarios_documento        UNIQUE (numero_documento),
    CONSTRAINT fk_usuarios_rol              FOREIGN KEY (rol_id)
        REFERENCES roles (rol_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT fk_usuarios_creado_por       FOREIGN KEY (creado_por)
        REFERENCES usuarios (usuario_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT ck_usuarios_activo           CHECK (activo IN (0, 1))
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Usuarios internos del sistema CRM';

-- ────────────────────────────────────────────────────────────
--  TABLA: medicos
--  Médicos y profesionales de salud
--  Incluye datos RETHUS y estado de licencia profesional
-- ────────────────────────────────────────────────────────────
CREATE TABLE medicos (
    medico_id                   INT             NOT NULL AUTO_INCREMENT,
    especialidad_id             INT             NOT NULL,
    ciudad_id                   INT             NOT NULL,
    nombres                     VARCHAR(80)     NOT NULL,
    apellidos                   VARCHAR(80)     NOT NULL,
    tipo_documento              ENUM('CC','CE','PA') NOT NULL DEFAULT 'CC',
    numero_documento            VARCHAR(20)     NOT NULL,
    registro_medico             VARCHAR(20)     NOT NULL,
    tarjeta_profesional         VARCHAR(20)     NOT NULL,
    licencia_vigente            TINYINT(1)      NOT NULL DEFAULT 1,
    fecha_vencimiento_licencia  DATE            NULL,
    nombre_consultorio          VARCHAR(150)    NULL,
    direccion_consultorio       VARCHAR(200)    NULL,
    telefono_consultorio        VARCHAR(15)     NULL,
    telefono_personal           VARCHAR(15)     NULL,
    correo                      VARCHAR(120)    NULL,
    acepta_visitas              TINYINT(1)      NOT NULL DEFAULT 1,
    horario_visitas             VARCHAR(100)    NULL,
    activo                      TINYINT(1)      NOT NULL DEFAULT 1,
    creado_en                   TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    creado_por                  INT             NOT NULL,
    actualizado_en              TIMESTAMP       NULL ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT pk_medicos                   PRIMARY KEY (medico_id),
    CONSTRAINT uq_medicos_documento         UNIQUE (numero_documento),
    CONSTRAINT uq_medicos_registro          UNIQUE (registro_medico),
    CONSTRAINT uq_medicos_tarjeta           UNIQUE (tarjeta_profesional),
    CONSTRAINT fk_medicos_especialidad      FOREIGN KEY (especialidad_id)
        REFERENCES especialidades (especialidad_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT fk_medicos_ciudad            FOREIGN KEY (ciudad_id)
        REFERENCES ciudades (ciudad_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT fk_medicos_creado_por        FOREIGN KEY (creado_por)
        REFERENCES usuarios (usuario_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT ck_medicos_licencia          CHECK (licencia_vigente IN (0, 1)),
    CONSTRAINT ck_medicos_acepta_visitas    CHECK (acepta_visitas IN (0, 1)),
    CONSTRAINT ck_medicos_activo            CHECK (activo IN (0, 1))
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Médicos y profesionales de salud — datos RETHUS incluidos';

-- ────────────────────────────────────────────────────────────
--  TABLA: pacientes
--  Pacientes — clientes finales de la droguería
--  RN-06: numero_documento UNIQUE
--  RN-07: máximo un médico tratante a la vez
-- ────────────────────────────────────────────────────────────
CREATE TABLE pacientes (
    paciente_id             INT             NOT NULL AUTO_INCREMENT,
    medico_id               INT             NULL,
    ciudad_id               INT             NOT NULL,
    tipo_doc_id             INT             NOT NULL,
    numero_documento        VARCHAR(20)     NOT NULL,
    nombres                 VARCHAR(80)     NOT NULL,
    apellidos               VARCHAR(80)     NOT NULL,
    fecha_nacimiento        DATE            NOT NULL,
    genero                  ENUM('M','F','NB','NE') NULL,
    telefono_principal      VARCHAR(15)     NOT NULL,
    telefono_alternativo    VARCHAR(15)     NULL,
    correo                  VARCHAR(120)    NULL,
    direccion               VARCHAR(200)    NULL,
    condicion_medica_general VARCHAR(300)   NULL,
    alergias_conocidas      VARCHAR(300)    NULL,
    eps                     VARCHAR(100)    NULL,
    activo                  TINYINT(1)      NOT NULL DEFAULT 1,
    creado_en               TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    creado_por              INT             NOT NULL,
    actualizado_en          TIMESTAMP       NULL ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT pk_pacientes                 PRIMARY KEY (paciente_id),
    CONSTRAINT uq_pacientes_documento       UNIQUE (numero_documento),
    CONSTRAINT fk_pacientes_medico          FOREIGN KEY (medico_id)
        REFERENCES medicos (medico_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE,
    CONSTRAINT fk_pacientes_ciudad          FOREIGN KEY (ciudad_id)
        REFERENCES ciudades (ciudad_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT fk_pacientes_tipo_doc        FOREIGN KEY (tipo_doc_id)
        REFERENCES tipos_documento_paciente (tipo_doc_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT fk_pacientes_creado_por      FOREIGN KEY (creado_por)
        REFERENCES usuarios (usuario_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT ck_pacientes_activo          CHECK (activo IN (0, 1))
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Pacientes — clientes finales de la droguería';

-- ────────────────────────────────────────────────────────────
--  TABLA: productos
--  Catálogo farmacéutico con control de stock
--  RN-12: stock_actual >= 0 (CHECK constraint)
--  RN-13: stock_minimo > 0
--  RN-15: nombre_comercial UNIQUE
-- ────────────────────────────────────────────────────────────
CREATE TABLE productos (
    producto_id         INT             NOT NULL AUTO_INCREMENT,
    categoria_id        INT             NOT NULL,
    laboratorio_id      INT             NOT NULL,
    nombre_comercial    VARCHAR(150)    NOT NULL,
    principio_activo    VARCHAR(150)    NOT NULL,
    concentracion       VARCHAR(50)     NOT NULL,
    forma_farmaceutica  ENUM(
                            'Tableta',
                            'Cápsula',
                            'Jarabe',
                            'Crema',
                            'Inyectable',
                            'Gotas',
                            'Otro'
                        )               NOT NULL,
    codigo_invima       VARCHAR(30)     NULL,
    requiere_formula    TINYINT(1)      NOT NULL DEFAULT 0,
    precio_compra       DECIMAL(10,2)   NOT NULL,
    precio_venta        DECIMAL(10,2)   NOT NULL,
    stock_actual        INT             NOT NULL DEFAULT 0,
    stock_minimo        INT             NOT NULL DEFAULT 10,
    unidad_medida       VARCHAR(30)     NOT NULL,
    fecha_vencimiento   DATE            NULL,
    activo              TINYINT(1)      NOT NULL DEFAULT 1,
    creado_en           TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    creado_por          INT             NOT NULL,
    actualizado_en      TIMESTAMP       NULL ON UPDATE CURRENT_TIMESTAMP,

    CONSTRAINT pk_productos                 PRIMARY KEY (producto_id),
    CONSTRAINT uq_productos_nombre          UNIQUE (nombre_comercial),
    CONSTRAINT uq_productos_invima          UNIQUE (codigo_invima),
    CONSTRAINT fk_productos_categoria       FOREIGN KEY (categoria_id)
        REFERENCES categorias_producto (categoria_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT fk_productos_laboratorio     FOREIGN KEY (laboratorio_id)
        REFERENCES laboratorios (laboratorio_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT fk_productos_creado_por      FOREIGN KEY (creado_por)
        REFERENCES usuarios (usuario_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT ck_productos_stock_actual    CHECK (stock_actual >= 0),
    CONSTRAINT ck_productos_stock_minimo    CHECK (stock_minimo > 0),
    CONSTRAINT ck_productos_precio_compra   CHECK (precio_compra > 0),
    CONSTRAINT ck_productos_precio_venta    CHECK (precio_venta > 0),
    CONSTRAINT ck_productos_requiere_formula CHECK (requiere_formula IN (0, 1)),
    CONSTRAINT ck_productos_activo          CHECK (activo IN (0, 1))
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Catálogo farmacéutico con control de stock — registro INVIMA incluido';

-- ============================================================
--  SECCIÓN 3: TABLAS DE SOPORTE
--  (dependen de las tablas principales)
-- ============================================================

-- ────────────────────────────────────────────────────────────
--  TABLA: interacciones
--  Registro INMUTABLE de interacciones con clientes
--  RN-10: campos obligatorios
--  RN-11: NO tiene actualizado_en — no se puede editar
-- ────────────────────────────────────────────────────────────
CREATE TABLE interacciones (
    interaccion_id          INT             NOT NULL AUTO_INCREMENT,
    tipo_interaccion_id     INT             NOT NULL,
    usuario_id              INT             NOT NULL,
    medico_id               INT             NULL,
    paciente_id             INT             NULL,
    fecha_interaccion       DATETIME        NOT NULL,
    asunto                  VARCHAR(200)    NOT NULL,
    descripcion             TEXT            NOT NULL,
    resultado               VARCHAR(200)    NULL,
    proxima_accion          VARCHAR(200)    NULL,
    fecha_proxima_accion    DATE            NULL,
    creado_en               TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    -- NOTA ARQUITECTÓNICA: Esta tabla NO tiene columna actualizado_en
    -- Las interacciones son inmutables por RN-11.
    -- Para aclaraciones se usa la tabla notas_interaccion.

    CONSTRAINT pk_interacciones             PRIMARY KEY (interaccion_id),
    CONSTRAINT fk_interacciones_tipo        FOREIGN KEY (tipo_interaccion_id)
        REFERENCES tipos_interaccion (tipo_interaccion_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT fk_interacciones_usuario     FOREIGN KEY (usuario_id)
        REFERENCES usuarios (usuario_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT fk_interacciones_medico      FOREIGN KEY (medico_id)
        REFERENCES medicos (medico_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT fk_interacciones_paciente    FOREIGN KEY (paciente_id)
        REFERENCES pacientes (paciente_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,

    -- Al menos un cliente debe estar asociado
    CONSTRAINT ck_interacciones_cliente     CHECK (
        medico_id IS NOT NULL OR paciente_id IS NOT NULL
    )
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Registro inmutable de interacciones con clientes — RN-11';

-- ────────────────────────────────────────────────────────────
--  TABLA: notas_interaccion
--  Notas aclaratorias sobre interacciones existentes
--  Respeta RN-11: no modifica el registro original
-- ────────────────────────────────────────────────────────────
CREATE TABLE notas_interaccion (
    nota_id             INT             NOT NULL AUTO_INCREMENT,
    interaccion_id      INT             NOT NULL,
    usuario_id          INT             NOT NULL,
    nota                TEXT            NOT NULL,
    creado_en           TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    -- NOTA: Esta tabla tampoco tiene actualizado_en — inmutable
    CONSTRAINT pk_notas_interaccion         PRIMARY KEY (nota_id),
    CONSTRAINT fk_notas_interaccion_inter   FOREIGN KEY (interaccion_id)
        REFERENCES interacciones (interaccion_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT fk_notas_interaccion_usuario FOREIGN KEY (usuario_id)
        REFERENCES usuarios (usuario_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Notas aclaratorias sobre interacciones — no modifica el registro original';

-- ────────────────────────────────────────────────────────────
--  TABLA: ajustes_stock
--  Trazabilidad INMUTABLE de cambios de stock
--  RN-16: registra cantidad anterior, nueva, motivo y usuario
-- ────────────────────────────────────────────────────────────
CREATE TABLE ajustes_stock (
    ajuste_id           INT             NOT NULL AUTO_INCREMENT,
    producto_id         INT             NOT NULL,
    usuario_id          INT             NOT NULL,
    stock_anterior      INT             NOT NULL,
    stock_nuevo         INT             NOT NULL,
    variacion           INT             NOT NULL,
    tipo_ajuste         ENUM('Entrada','Salida','Corrección') NOT NULL,
    motivo              VARCHAR(300)    NOT NULL,
    documento_soporte   VARCHAR(200)    NULL,
    creado_en           TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    -- NOTA: Inmutable — sin actualizado_en por diseño (RN-16)
    CONSTRAINT pk_ajustes_stock             PRIMARY KEY (ajuste_id),
    CONSTRAINT fk_ajustes_producto          FOREIGN KEY (producto_id)
        REFERENCES productos (producto_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT fk_ajustes_usuario           FOREIGN KEY (usuario_id)
        REFERENCES usuarios (usuario_id)
        ON DELETE RESTRICT
        ON UPDATE CASCADE,
    CONSTRAINT ck_ajustes_stock_nuevo       CHECK (stock_nuevo >= 0)
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Trazabilidad inmutable de ajustes de stock — RN-16';

-- ────────────────────────────────────────────────────────────
--  TABLA: logs_auditoria
--  Registro INMUTABLE de acciones críticas del sistema
--  RF-05: auditoría de acciones con fecha, usuario y detalle
-- ────────────────────────────────────────────────────────────
CREATE TABLE logs_auditoria (
    log_id              INT             NOT NULL AUTO_INCREMENT,
    usuario_id          INT             NULL,
    modulo              VARCHAR(60)     NOT NULL,
    accion              VARCHAR(60)     NOT NULL,
    tabla_afectada      VARCHAR(60)     NULL,
    registro_id         INT             NULL,
    detalle             TEXT            NULL,
    ip_origen           VARCHAR(45)     NULL,
    creado_en           TIMESTAMP       NOT NULL DEFAULT CURRENT_TIMESTAMP,

    -- usuario_id NULL permite registrar acciones antes del login
    CONSTRAINT pk_logs_auditoria            PRIMARY KEY (log_id),
    CONSTRAINT fk_logs_usuario              FOREIGN KEY (usuario_id)
        REFERENCES usuarios (usuario_id)
        ON DELETE SET NULL
        ON UPDATE CASCADE
) ENGINE=InnoDB
  DEFAULT CHARSET=utf8mb4
  COLLATE=utf8mb4_unicode_ci
  COMMENT='Log inmutable de auditoría — RF-05';

-- ============================================================
--  SECCIÓN 4: ÍNDICES DE RENDIMIENTO
--  Optimizan las consultas más frecuentes del sistema
--  RNF-04: respuesta < 2 segundos con hasta 10.000 registros
-- ============================================================

-- Índices para búsquedas de médicos
CREATE INDEX idx_medicos_nombres
    ON medicos (apellidos, nombres);

CREATE INDEX idx_medicos_ciudad
    ON medicos (ciudad_id);

CREATE INDEX idx_medicos_especialidad
    ON medicos (especialidad_id);

CREATE INDEX idx_medicos_activo
    ON medicos (activo);

CREATE INDEX idx_medicos_licencia
    ON medicos (licencia_vigente);

-- Índices para búsquedas de pacientes
CREATE INDEX idx_pacientes_nombres
    ON pacientes (apellidos, nombres);

CREATE INDEX idx_pacientes_ciudad
    ON pacientes (ciudad_id);

CREATE INDEX idx_pacientes_medico
    ON pacientes (medico_id);

CREATE INDEX idx_pacientes_activo
    ON pacientes (activo);

-- Índices para búsquedas de productos
CREATE INDEX idx_productos_nombre
    ON productos (nombre_comercial);

CREATE INDEX idx_productos_categoria
    ON productos (categoria_id);

CREATE INDEX idx_productos_laboratorio
    ON productos (laboratorio_id);

CREATE INDEX idx_productos_activo
    ON productos (activo);

CREATE INDEX idx_productos_stock_alerta
    ON productos (stock_actual, stock_minimo);

-- Índices para interacciones (historial de clientes)
CREATE INDEX idx_interacciones_medico
    ON interacciones (medico_id, fecha_interaccion);

CREATE INDEX idx_interacciones_paciente
    ON interacciones (paciente_id, fecha_interaccion);

CREATE INDEX idx_interacciones_usuario
    ON interacciones (usuario_id);

CREATE INDEX idx_interacciones_fecha
    ON interacciones (fecha_interaccion);

-- Índices para ajustes de stock
CREATE INDEX idx_ajustes_producto
    ON ajustes_stock (producto_id, creado_en);

-- Índices para logs de auditoría
CREATE INDEX idx_logs_usuario
    ON logs_auditoria (usuario_id, creado_en);

CREATE INDEX idx_logs_modulo
    ON logs_auditoria (modulo, accion);

-- ============================================================
--  SECCIÓN 5: VISTAS ÚTILES
--  Simplifican las consultas más comunes del sistema
-- ============================================================

-- Vista: productos con alerta de stock bajo activa
CREATE VIEW v_productos_stock_bajo AS
    SELECT
        p.producto_id,
        p.nombre_comercial,
        p.principio_activo,
        p.concentracion,
        p.forma_farmaceutica,
        p.codigo_invima,
        p.stock_actual,
        p.stock_minimo,
        (p.stock_minimo - p.stock_actual) AS unidades_faltantes,
        c.nombre                          AS categoria,
        l.nombre                          AS laboratorio
    FROM productos p
    INNER JOIN categorias_producto c ON p.categoria_id = c.categoria_id
    INNER JOIN laboratorios l        ON p.laboratorio_id = l.laboratorio_id
    WHERE p.activo = 1
      AND p.stock_actual <= p.stock_minimo
    ORDER BY unidades_faltantes DESC;

-- Vista: médicos con licencia vencida o próxima a vencer (90 días)
CREATE VIEW v_medicos_licencia_alerta AS
    SELECT
        m.medico_id,
        CONCAT(m.nombres, ' ', m.apellidos)   AS nombre_completo,
        m.registro_medico,
        m.tarjeta_profesional,
        e.nombre                               AS especialidad,
        c.nombre                               AS ciudad,
        m.licencia_vigente,
        m.fecha_vencimiento_licencia,
        DATEDIFF(m.fecha_vencimiento_licencia, CURDATE()) AS dias_para_vencer
    FROM medicos m
    INNER JOIN especialidades e ON m.especialidad_id = e.especialidad_id
    INNER JOIN ciudades c       ON m.ciudad_id = c.ciudad_id
    WHERE m.activo = 1
      AND (
          m.licencia_vigente = 0
          OR m.fecha_vencimiento_licencia <= DATE_ADD(CURDATE(), INTERVAL 90 DAY)
      )
    ORDER BY m.fecha_vencimiento_licencia ASC;

-- Vista: dashboard — métricas principales (RF-18)
CREATE VIEW v_dashboard_metricas AS
    SELECT
        (SELECT COUNT(*) FROM medicos  WHERE activo = 1) AS total_medicos_activos,
        (SELECT COUNT(*) FROM pacientes WHERE activo = 1) AS total_pacientes_activos,
        (SELECT COUNT(*) FROM productos
            WHERE activo = 1
              AND stock_actual <= stock_minimo)           AS productos_stock_bajo,
        (SELECT COUNT(*) FROM interacciones
            WHERE MONTH(fecha_interaccion) = MONTH(CURDATE())
              AND YEAR(fecha_interaccion)  = YEAR(CURDATE()))
                                                          AS interacciones_mes_actual,
        (SELECT COUNT(*) FROM usuarios WHERE activo = 1)  AS usuarios_activos;

-- Vista: historial completo de interacciones con datos de cliente
CREATE VIEW v_interacciones_completo AS
    SELECT
        i.interaccion_id,
        ti.nombre                               AS tipo_interaccion,
        ti.color_hex,
        i.fecha_interaccion,
        i.asunto,
        i.descripcion,
        i.resultado,
        i.proxima_accion,
        i.fecha_proxima_accion,
        i.creado_en,
        CONCAT(u.nombres, ' ', u.apellidos)     AS registrado_por,
        -- Datos del médico (si aplica)
        CONCAT(m.nombres, ' ', m.apellidos)     AS nombre_medico,
        m.medico_id,
        -- Datos del paciente (si aplica)
        CONCAT(pa.nombres, ' ', pa.apellidos)   AS nombre_paciente,
        pa.paciente_id
    FROM interacciones i
    INNER JOIN tipos_interaccion ti ON i.tipo_interaccion_id = ti.tipo_interaccion_id
    INNER JOIN usuarios u           ON i.usuario_id = u.usuario_id
    LEFT  JOIN medicos m            ON i.medico_id = m.medico_id
    LEFT  JOIN pacientes pa         ON i.paciente_id = pa.paciente_id
    ORDER BY i.fecha_interaccion DESC;

-- ============================================================
--  SECCIÓN 6: SEEDERS — DATOS INICIALES
--  Datos necesarios para que el sistema funcione desde el inicio
-- ============================================================

-- ────────────────────────────────────────────────────────────
--  Roles del sistema
-- ────────────────────────────────────────────────────────────
INSERT INTO roles (nombre, descripcion) VALUES
    ('Administrador', 'Acceso total al sistema. Gestiona usuarios, roles y configuración general.'),
    ('Vendedor',      'Gestiona clientes, productos, interacciones y reportes comerciales.'),
    ('Operativo',     'Acceso de consulta. Puede registrar interacciones básicas con clientes.');

-- ────────────────────────────────────────────────────────────
--  Tipos de documento de identidad
-- ────────────────────────────────────────────────────────────
INSERT INTO tipos_documento_paciente (codigo, descripcion) VALUES
    ('CC', 'Cédula de Ciudadanía'),
    ('TI', 'Tarjeta de Identidad'),
    ('RC', 'Registro Civil'),
    ('CE', 'Cédula de Extranjería'),
    ('PA', 'Pasaporte'),
    ('NV', 'Certificado de Nacido Vivo');

-- ────────────────────────────────────────────────────────────
--  Tipos de interacción
-- ────────────────────────────────────────────────────────────
INSERT INTO tipos_interaccion (nombre, color_hex) VALUES
    ('Llamada',   '#3498DB'),
    ('Visita',    '#2A9D8F'),
    ('Correo',    '#F39C12'),
    ('WhatsApp',  '#27AE60');

-- ────────────────────────────────────────────────────────────
--  Categorías farmacológicas
-- ────────────────────────────────────────────────────────────
INSERT INTO categorias_producto (nombre, descripcion) VALUES
    ('Analgésico',          'Medicamentos para el alivio del dolor'),
    ('Antibiótico',         'Medicamentos para tratar infecciones bacterianas'),
    ('Antiinflamatorio',    'Medicamentos para reducir la inflamación'),
    ('Antihipertensivo',    'Medicamentos para el control de la presión arterial'),
    ('Antidiabético',       'Medicamentos para el control de la glucosa en sangre'),
    ('Gastroprotector',     'Medicamentos para proteger el tracto gastrointestinal'),
    ('Antihistamínico',     'Medicamentos para tratar reacciones alérgicas'),
    ('Antidepresivo',       'Medicamentos para el tratamiento de la depresión'),
    ('Vitaminas y Suplementos', 'Suplementos vitamínicos y nutricionales'),
    ('Dermatológico',       'Medicamentos y cremas de uso tópico'),
    ('Antiparasitario',     'Medicamentos para el tratamiento de parásitos'),
    ('Broncodilatador',     'Medicamentos para el tratamiento de enfermedades respiratorias');

-- ────────────────────────────────────────────────────────────
--  Laboratorios farmacéuticos (Colombia y multinacionales)
-- ────────────────────────────────────────────────────────────
INSERT INTO laboratorios (nombre, pais_origen, telefono_soporte, correo_soporte) VALUES
    ('Tecnoquímicas S.A.',          'Colombia',     '(602) 3930000', 'info@tecnoquimicas.com'),
    ('Laboratorios Mk',             'Colombia',     '(601) 4230000', 'contacto@labmk.com.co'),
    ('Genfar S.A.',                 'Colombia',     '(601) 5930000', 'info@genfar.com.co'),
    ('Bayer S.A.',                  'Alemania',     '(601) 6388000', 'colombia@bayer.com'),
    ('GlaxoSmithKline Colombia',    'Reino Unido',  '(601) 6298080', 'co.medical@gsk.com'),
    ('Pfizer Colombia',             'Estados Unidos','(601) 3265500', 'colombia.info@pfizer.com'),
    ('Novartis Colombia',           'Suiza',        '(601) 7456000', 'colombia@novartis.com'),
    ('Sanofi Colombia',             'Francia',      '(601) 7456300', 'colombia@sanofi.com'),
    ('Abbott Laboratorios',         'Estados Unidos','(601) 6434040', 'info@abbott.com.co'),
    ('Lafrancol S.A.',              'Colombia',     '(602) 6608080', 'info@lafrancol.com');

-- ────────────────────────────────────────────────────────────
--  Especialidades médicas (RETHUS Colombia)
-- ────────────────────────────────────────────────────────────
INSERT INTO especialidades (nombre, codigo_rethus) VALUES
    ('Medicina General',            'MG-001'),
    ('Medicina Interna',            'MI-002'),
    ('Cardiología',                 'CA-003'),
    ('Pediatría',                   'PE-004'),
    ('Ginecología y Obstetricia',   'GO-005'),
    ('Dermatología',                'DE-006'),
    ('Oftalmología',                'OF-007'),
    ('Ortopedia y Traumatología',   'OT-008'),
    ('Neurología',                  'NE-009'),
    ('Psiquiatría',                 'PS-010'),
    ('Endocrinología',              'EN-011'),
    ('Gastroenterología',           'GS-012'),
    ('Urología',                    'UR-013'),
    ('Otorrinolaringología',        'OL-014'),
    ('Medicina Familiar',           'MF-015');

-- ────────────────────────────────────────────────────────────
--  Departamentos de Colombia (principales)
-- ────────────────────────────────────────────────────────────
INSERT INTO departamentos (nombre, codigo_dane) VALUES
    ('Amazonas',            '91'),
    ('Antioquia',           '05'),
    ('Arauca',              '81'),
    ('Atlántico',           '08'),
    ('Bolívar',             '13'),
    ('Boyacá',              '15'),
    ('Caldas',              '17'),
    ('Caquetá',             '18'),
    ('Casanare',            '85'),
    ('Cauca',               '19'),
    ('Cesar',               '20'),
    ('Chocó',               '27'),
    ('Córdoba',             '23'),
    ('Cundinamarca',        '25'),
    ('Guainía',             '94'),
    ('Guaviare',            '95'),
    ('Huila',               '41'),
    ('La Guajira',          '44'),
    ('Magdalena',           '47'),
    ('Meta',                '50'),
    ('Nariño',              '52'),
    ('Norte de Santander',  '54'),
    ('Putumayo',            '86'),
    ('Quindío',             '63'),
    ('Risaralda',           '66'),
    ('San Andrés',          '88'),
    ('Santander',           '68'),
    ('Sucre',               '70'),
    ('Tolima',              '73'),
    ('Valle del Cauca',     '76'),
    ('Vaupés',              '97'),
    ('Vichada',             '99'),
    ('Bogotá D.C.',         '11');

-- ────────────────────────────────────────────────────────────
--  Ciudades principales de Colombia
-- ────────────────────────────────────────────────────────────
INSERT INTO ciudades (departamento_id, nombre, codigo_dane) VALUES
    -- Bogotá D.C. (departamento_id = 33)
    (33, 'Bogotá D.C.',         '11001'),
    -- Antioquia (departamento_id = 2)
    (2,  'Medellín',            '05001'),
    (2,  'Bello',               '05088'),
    (2,  'Itagüí',              '05360'),
    (2,  'Envigado',            '05266'),
    -- Valle del Cauca (departamento_id = 30)
    (30, 'Cali',                '76001'),
    (30, 'Buenaventura',        '76109'),
    -- Atlántico (departamento_id = 4)
    (4,  'Barranquilla',        '08001'),
    (4,  'Soledad',             '08758'),
    -- Bolívar (departamento_id = 5)
    (5,  'Cartagena',           '13001'),
    -- Santander (departamento_id = 27)
    (27, 'Bucaramanga',         '68001'),
    (27, 'Floridablanca',       '68276'),
    -- Cundinamarca (departamento_id = 14)
    (14, 'Soacha',              '25754'),
    (14, 'Chía',                '25175'),
    -- Córdoba (departamento_id = 13)
    (13, 'Montería',            '23001'),
    -- Norte de Santander (departamento_id = 22)
    (22, 'Cúcuta',              '54001'),
    -- Risaralda (departamento_id = 25)
    (25, 'Pereira',             '66001'),
    -- Quindío (departamento_id = 24)
    (24, 'Armenia',             '63001'),
    -- Caldas (departamento_id = 7)
    (7,  'Manizales',           '17001'),
    -- Huila (departamento_id = 17)
    (17, 'Neiva',               '41001'),
    -- Tolima (departamento_id = 29)
    (29, 'Ibagué',              '73001'),
    -- Nariño (departamento_id = 21)
    (21, 'Pasto',               '52001'),
    -- Meta (departamento_id = 20)
    (20, 'Villavicencio',       '50001');

-- ────────────────────────────────────────────────────────────
--  Usuario administrador inicial del sistema
--  CONTRASEÑA: Admin2026* (hash bcrypt cost 12)
--  IMPORTANTE: Cambiar en el primer inicio de sesión
-- ────────────────────────────────────────────────────────────
INSERT INTO usuarios (
    rol_id,
    nombres,
    apellidos,
    numero_documento,
    correo,
    telefono,
    contrasena_hash,
    activo,
    creado_por
) VALUES (
    1,
    'Super',
    'Administrador',
    '000000001',
    'admin@crmfarma.com',
    '3001234567',
    '$2b$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMnK4jWAE5f8wFGh2mRpN0X1Zy',
    1,
    NULL
);

-- ────────────────────────────────────────────────────────────
--  Datos de ejemplo — Entorno real colombiano
-- ────────────────────────────────────────────────────────────

-- Usuarios de prueba del equipo
INSERT INTO usuarios (
    rol_id, nombres, apellidos, numero_documento,
    correo, telefono, contrasena_hash, activo, creado_por
) VALUES
    (2, 'Juan Esteban', 'Pérez Vargas',   '1045234567',
     'juan.vendedor@crmfarma.com', '3154321098',
     '$2b$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMnK4jWAE5f8wFGh2mRpN0X1Zy', 1, 1),

    (2, 'María José',   'Cano Restrepo',  '1032198765',
     'maria.vendedor@crmfarma.com', '3187654321',
     '$2b$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMnK4jWAE5f8wFGh2mRpN0X1Zy', 1, 1),

    (3, 'Carlos',       'Ruiz Morales',   '79876543',
     'carlos.operativo@crmfarma.com', '3112345678',
     '$2b$12$LQv3c1yqBWVHxkd0LHAkCOYz6TtxMnK4jWAE5f8wFGh2mRpN0X1Zy', 1, 1);

-- Médicos de ejemplo
INSERT INTO medicos (
    especialidad_id, ciudad_id, nombres, apellidos,
    tipo_documento, numero_documento,
    registro_medico, tarjeta_profesional,
    licencia_vigente, fecha_vencimiento_licencia,
    nombre_consultorio, direccion_consultorio,
    telefono_consultorio, telefono_personal, correo,
    acepta_visitas, horario_visitas, activo, creado_por
) VALUES
    (1, 2, 'Carlos Andrés', 'Ríos Montoya', 'CC', '71385924',
     'RM-2019-047823', 'TP-2019-147823',
     1, '2027-03-15',
     'Centro Médico Salud Integral', 'Cra 43A #18-12 Consultorio 302',
     '6043125789', '3146289034', 'crios@saludintegral.com.co',
     1, 'Lunes, Miércoles y Viernes 3:00pm - 5:00pm', 1, 2),

    (3, 1, 'Valentina',    'Solis Herrera', 'CC', '52876543',
     'RM-2015-023145', 'TP-2015-098765',
     1, '2026-08-20',
     'Clínica del Corazón', 'Av. El Dorado #68-95 Piso 4',
     '6017654321', '3208871234', 'vsolis@clinicacorazon.com.co',
     1, 'Martes y Jueves 2:00pm - 6:00pm', 1, 2),

    (4, 6, 'Roberto',      'Mora Castaño',  'CC', '94321678',
     'RM-2018-061234', 'TP-2018-234567',
     0, '2024-12-31',
     'Consultorio Pediátrico Cali', 'Cll 5 #38-12 Of. 201',
     '6023456789', '3156789012', 'rmora@pediatriacali.com',
     0, NULL, 1, 2),

    (6, 11, 'Ana Lucía',   'Bermúdez Prada','CC', '63219874',
     'RM-2020-089456', 'TP-2020-345678',
     1, '2028-05-10',
     'DermatoCentro Bucaramanga', 'Cra 35 #52-96 Consultorio 105',
     '6076789012', '3001234567', 'abermudez@dermatocentro.com',
     1, 'Lunes a Viernes 8:00am - 12:00pm', 1, 3),

    (1, 20, 'Jorge Iván',  'Torres Lozano', 'CC', '12987654',
     'RM-2022-102345', 'TP-2022-456789',
     1, '2027-11-30',
     'Clínica Neiva Salud', 'Cll 5 #4-30 Consultorio 8',
     '6088901234', '3181234567', 'jtorres@neivasalud.com',
     1, 'Miércoles y Viernes 3:00pm - 5:00pm', 1, 3);

-- Pacientes de ejemplo
INSERT INTO pacientes (
    medico_id, ciudad_id, tipo_doc_id,
    numero_documento, nombres, apellidos,
    fecha_nacimiento, genero,
    telefono_principal, telefono_alternativo,
    correo, direccion,
    condicion_medica_general, alergias_conocidas,
    eps, activo, creado_por
) VALUES
    (1, 2, 1, '1037845623', 'Lucía Fernanda',  'Torres Ospina',
     '1989-07-22', 'F',
     '3208847562', NULL,
     'luciatorres89@gmail.com', 'Cll 48 #70-25 Apt 301, Laureles',
     'Hipertensión arterial controlada', 'Penicilina, Ibuprofeno',
     'Sura EPS', 1, 2),

    (1, 2, 1, '98234156',   'Pedro Alejandro', 'Gómez Reyes',
     '1975-03-14', 'M',
     '3145678901', '6043219876',
     'pgomez75@hotmail.com', 'Cra 80 #32-45, Robledo',
     'Diabetes tipo 2 en tratamiento', 'Sulfas',
     'Sanitas EPS', 1, 2),

    (2, 1, 1, '51234789',   'Gloria Patricia', 'Henao Salazar',
     '1968-11-05', 'F',
     '3187654321', NULL,
     'gloriahenao68@yahoo.com', 'Av. Boyacá #62-15, Bogotá',
     'Insuficiencia cardíaca leve', 'Ninguna conocida',
     'Compensar EPS', 1, 3),

    (3, 6, 1, '1112345678', 'Santiago',        'Ospina Mejía',
     '2010-08-20', 'M',
     '3156789012', '6023456789',
     'familiaospina@gmail.com', 'Cll 23 #45-67, San Fernando, Cali',
     'Asma bronquial', 'Aspirina',
     'Coomeva EPS', 1, 3),

    (4, 11, 1, '37654321',  'Isabel Cristina', 'Vargas Duarte',
     '1992-04-18', 'F',
     '3001234567', NULL,
     'icvargas92@gmail.com', 'Cra 27 #36-15, Cabecera del Llano, Bucaramanga',
     'Dermatitis atópica crónica', 'Penicilina',
     'Nueva EPS', 1, 3),

    (NULL, 20, 1, '1083456789','Andrés Felipe',  'Castro Penagos',
     '1995-09-30', 'M',
     '3123456789', NULL,
     'acastro95@gmail.com', 'Cll 8 #12-34, Neiva',
     'Sin antecedentes relevantes', 'Ninguna conocida',
     'Medimás EPS', 1, 2);

-- Productos farmacéuticos de ejemplo
INSERT INTO productos (
    categoria_id, laboratorio_id, nombre_comercial,
    principio_activo, concentracion, forma_farmaceutica,
    codigo_invima, requiere_formula,
    precio_compra, precio_venta,
    stock_actual, stock_minimo,
    unidad_medida, fecha_vencimiento,
    activo, creado_por
) VALUES
    (2, 1, 'Amoxicilina Mk 500mg',
     'Amoxicilina trihidrato', '500mg', 'Cápsula',
     'M-2003AR-R2', 1,
     8200.00, 12500.00,
     8, 30,
     'Caja x 10 cápsulas', '2026-11-30', 1, 2),

    (1, 5, 'Dolex 500mg',
     'Acetaminofén', '500mg', 'Tableta',
     'M-1998AR-R4', 0,
     3500.00, 5800.00,
     145, 50,
     'Caja x 100 tabletas', '2027-06-30', 1, 2),

    (4, 2, 'Losartán Mk 50mg',
     'Losartán potásico', '50mg', 'Tableta',
     'M-2008AR-R1', 1,
     12000.00, 18500.00,
     67, 40,
     'Caja x 30 tabletas', '2027-03-31', 1, 2),

    (5, 3, 'Metformina Genfar 850mg',
     'Metformina clorhidrato', '850mg', 'Tableta',
     'M-2005AR-R3', 1,
     9800.00, 15200.00,
     23, 25,
     'Caja x 30 tabletas', '2026-09-30', 1, 2),

    (6, 1, 'Omeprazol Mk 20mg',
     'Omeprazol', '20mg', 'Cápsula',
     'M-2001AR-R2', 1,
     7500.00, 11800.00,
     98, 30,
     'Caja x 14 cápsulas', '2027-02-28', 1, 3),

    (7, 4, 'Cetirizina Bayer 10mg',
     'Cetirizina clorhidrato', '10mg', 'Tableta',
     'M-2004AR-R1', 0,
     6200.00, 9500.00,
     12, 20,
     'Caja x 10 tabletas', '2026-12-31', 1, 3),

    (1, 3, 'Ibuprofeno Genfar 400mg',
     'Ibuprofeno', '400mg', 'Tableta',
     'M-2000AR-R5', 0,
     4800.00, 7500.00,
     210, 60,
     'Caja x 100 tabletas', '2027-08-31', 1, 2),

    (2, 6, 'Azitromicina Pfizer 500mg',
     'Azitromicina', '500mg', 'Tableta',
     'M-2010AR-R2', 1,
     18500.00, 28000.00,
     34, 20,
     'Caja x 3 tabletas', '2026-10-31', 1, 2),

    (3, 5, 'Meloxicam GSK 15mg',
     'Meloxicam', '15mg', 'Tableta',
     'M-2006AR-R1', 1,
     11200.00, 17000.00,
     56, 25,
     'Caja x 10 tabletas', '2027-04-30', 1, 3),

    (8, 7, 'Sertralina Novartis 50mg',
     'Sertralina clorhidrato', '50mg', 'Tableta',
     'M-2009AR-R3', 1,
     22000.00, 34500.00,
     29, 20,
     'Caja x 30 tabletas', '2027-01-31', 1, 2);

-- Interacciones de ejemplo
INSERT INTO interacciones (
    tipo_interaccion_id, usuario_id,
    medico_id, paciente_id,
    fecha_interaccion, asunto, descripcion,
    resultado, proxima_accion, fecha_proxima_accion
) VALUES
    (2, 2, 1, NULL,
     '2026-02-10 14:30:00',
     'Primera visita al consultorio',
     'Se realizó visita al Dr. Carlos Ríos en su consultorio del Centro Médico Salud Integral. Se presentó el portafolio de productos antibióticos y analgésicos. El médico mostró interés especial en Amoxicilina Mk y solicitó muestras médicas para la próxima visita.',
     'Médico interesado en Amoxicilina Mk 500mg — solicitó muestras',
     'Llevar muestras de Amoxicilina Mk y Dolex en próxima visita',
     '2026-02-24'),

    (1, 2, 1, NULL,
     '2026-02-15 09:15:00',
     'Seguimiento post-visita',
     'Llamada telefónica de seguimiento. El Dr. Ríos confirmó que revisó el material entregado y que tiene pacientes candidatos a los productos presentados. Solicitó información sobre descuentos por volumen para Amoxicilina.',
     'Confirmó interés — solicita información de descuentos por volumen',
     'Enviar tabla de precios y condiciones comerciales por correo',
     '2026-02-17'),

    (3, 2, 1, NULL,
     '2026-02-17 11:00:00',
     'Envío tabla de precios comerciales',
     'Se envió al Dr. Ríos correo electrónico con la tabla de precios actualizada, condiciones de descuento por volumen y catálogo digital de productos. Se adjuntó información técnica de Amoxicilina Mk 500mg.',
     'Correo enviado con tabla de precios y catálogo digital',
     'Llamar para confirmar recepción y programar segunda visita',
     '2026-02-22'),

    (2, 3, 2, NULL,
     '2026-02-12 15:00:00',
     'Visita Dra. Valentina Solis — Cardiología',
     'Primera visita a la Dra. Solis en Clínica del Corazón. Se presentaron los productos antihipertensivos: Losartán Mk y se explicaron los beneficios del Metformina Genfar para pacientes con síndrome metabólico. La médica solicitó literatura científica sobre Losartán.',
     'Interesada en Losartán Mk — solicitó evidencia clínica',
     'Enviar estudios clínicos de Losartán por correo',
     '2026-02-20'),

    (1, 2, NULL, 1,
     '2026-02-18 10:30:00',
     'Contacto paciente Lucía Torres — seguimiento tratamiento',
     'Llamada a la paciente Lucía Fernanda Torres para verificar adherencia al tratamiento antihipertensivo. Refiere buen cumplimiento del medicamento. Pregunta por disponibilidad de Losartán 50mg. Se le confirma stock disponible.',
     'Paciente cumple tratamiento — confirma compra de Losartán en próxima visita',
     'Reservar stock de Losartán para paciente',
     '2026-02-25');

-- Ajustes de stock de ejemplo
INSERT INTO ajustes_stock (
    producto_id, usuario_id,
    stock_anterior, stock_nuevo, variacion,
    tipo_ajuste, motivo, documento_soporte
) VALUES
    (1, 2, 50, 8, -42,
     'Salida', 'Ventas del mes de enero 2026 — despacho a mostrador',
     'FACT-2026-0145'),

    (2, 2, 100, 145, 45,
     'Entrada', 'Recepción de pedido a GlaxoSmithKline — orden de compra OC-0023',
     'REM-2026-0089'),

    (4, 3, 30, 23, -7,
     'Salida', 'Ventas primera quincena febrero 2026',
     'FACT-2026-0198'),

    (6, 3, 25, 12, -13,
     'Salida', 'Ventas enero y primera quincena febrero 2026',
     'FACT-2026-0201'),

    (10, 2, 20, 29, 9,
     'Entrada', 'Recepción pedido Novartis — orden de compra OC-0031',
     'REM-2026-0102');

-- ============================================================
--  RESTAURAR CONFIGURACIÓN INICIAL
-- ============================================================

SET SQL_MODE=@OLD_SQL_MODE;
SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS;
SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS;

-- ============================================================
--  VERIFICACIÓN FINAL
-- ============================================================

SELECT '✅ Base de datos crm_farma creada exitosamente' AS estado;

SELECT
    table_name      AS 'Tabla',
    table_rows      AS 'Filas aprox.',
    table_comment   AS 'Descripción'
FROM information_schema.tables
WHERE table_schema = 'crm_farma'
  AND table_type = 'BASE TABLE'
ORDER BY table_name;

-- ============================================================
--  FIN DEL SCRIPT
--  CRM FARMA+ v1.0.0
-- ============================================================
