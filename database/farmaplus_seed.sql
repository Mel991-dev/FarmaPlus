-- ============================================================
-- FarmaPlus CRM — Datos iniciales (seed)
-- Ejecutar DESPUÉS de farmaplus_schema.sql
-- ============================================================

USE farmaplus;

-- Roles del sistema
INSERT INTO `roles` (`nombre`, `descripcion`) VALUES
('administrador', 'Acceso total al sistema. Configura usuarios, variables y logs.'),
('gerente',       'Director operativo. Supervisa personal, reportes y devoluciones.'),
('auxiliar',      'Gestiona inventario: productos, lotes, alertas y proveedores.'),
('vendedor',      'Registra ventas presenciales en el punto de venta.'),
('repartidor',    'Gestiona sus pedidos asignados y actualiza estados de entrega.'),
('cliente',       'Usuario externo que compra en línea.');

-- Categorías de producto iniciales
INSERT INTO `categorias_producto` (`nombre`, `descripcion`) VALUES
('Antibiótico',       'Medicamentos para tratar infecciones bacterianas'),
('Analgésico',        'Medicamentos para el control del dolor'),
('Antihistamínico',   'Medicamentos para alergias y reacciones alérgicas'),
('Antidiabético',     'Medicamentos para el control de la diabetes'),
('Antiácido',         'Medicamentos para problemas gástricos y acidez'),
('Antihipertensivo',  'Medicamentos para el control de la presión arterial'),
('Vitamina',          'Suplementos vitamínicos y minerales'),
('Antiinflamatorio',  'Medicamentos para reducir la inflamación'),
('Dermatológico',     'Medicamentos de aplicación tópica para la piel'),
('Otro',              'Otros medicamentos y productos farmacéuticos');

-- Proveedores de ejemplo
INSERT INTO `proveedores` (`nit`, `nombre`, `pais_origen`, `telefono`, `correo`) VALUES
('860.002.462-2', 'Tecnoquímicas S.A.',  'Colombia', '(2) 524 5555', 'ventas@tecnoquimicas.com'),
('860.007.335-2', 'Genfar S.A.',         'Colombia', '(1) 423 4000', 'distribuciones@genfar.com'),
('890.904.430-2', 'Bayer S.A.',          'Colombia', '(1) 638 5555', 'info@bayer.com.co'),
('890.903.874-6', 'GlaxoSmithKline',     'Colombia', '(1) 638 0600', 'info@gsk.com.co'),
('890.200.144-2', 'Lafrancol S.A.',      'Colombia', '(2) 387 3000', 'ventas@lafrancol.com');

-- Usuario administrador por defecto
-- Contraseña: Admin2025* (bcrypt cost 12 — cambiar en producción)
INSERT INTO `usuarios` (
    `rol_id`, `tipo_documento`, `documento`, `nombres`, `apellidos`,
    `correo`, `contrasena_hash`, `activo`
) VALUES (
    1, 'CC', '12345678', 'Carlos', 'Andrade',
    'admin@farmaplus.co',
    '$2y$12$LyijRlBivMqFfqfBSXHqnOmIj5/S8HzXwLyRHkgKtAfGqMw0BkFOu',
    1
);

-- Configuración inicial del sistema
INSERT INTO `configuracion` (`clave`, `valor`, `descripcion`) VALUES
('tarifa_base_envio',       '3000',           'Tarifa base de envío en COP'),
('recargo_km_1_3',          '400',            'Recargo por km para distancias 1-3 km'),
('recargo_km_3_6',          '800',            'Recargo por km para distancias 3-6 km'),
('recargo_km_6_mas',        '1200',           'Recargo por km para distancias > 6 km'),
('recargo_volumen_umbral',  '5',              'Cantidad de productos a partir de la cual aplica recargo'),
('recargo_volumen_valor',   '1500',           'Recargo adicional por volumen en COP'),
('stock_minimo_global',     '10',             'Stock mínimo por defecto para nuevos productos'),
('dias_alerta_vencimiento', '30',             'Días antes del vencimiento para generar alerta'),
('app_nombre',              'FarmaPlus CRM',  'Nombre de la aplicación'),
('drogueria_nombre',        'FarmaPlus Droguería', 'Nombre comercial de la droguería'),
('drogueria_nit',           '900.123.456-7', 'NIT de la droguería'),
('drogueria_direccion',     'Cra. 5 #12-80, Centro · Neiva, Huila', 'Dirección física'),
('drogueria_telefono',      '(8) 871 2345',  'Teléfono de contacto'),
('drogueria_correo',        'soporte@farmaplus.co', 'Correo de soporte');
