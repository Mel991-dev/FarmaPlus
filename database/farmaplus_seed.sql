-- ============================================================
-- FarmaPlus CRM — Seed de datos de demostración
-- Ejecutar DESPUÉS de farmaplus_schema.sql
-- Usuarios de prueba:
--   admin@farmaplus.com / Admin1234
--   gerente@farmaplus.com / Admin1234
--   cajero@farmaplus.com / Admin1234
--   repartidor@farmaplus.com / Admin1234
--   cliente@farmaplus.com / Admin1234
-- ============================================================

USE farmaplus;

-- ============================================================
-- ROLES (idempotente)
-- ============================================================
INSERT IGNORE INTO roles (nombre, descripcion) VALUES
    ('administrador', 'Acceso total al sistema. Configura usuarios, variables y logs.'),
    ('gerente',       'Dashboard gerencial y reportes'),
    ('farmaceutico',  'Validación y control especial'),
    ('cajero',        'Ventas presenciales POS'),
    ('bodeguero',     'Recepción de lotes e inventario'),
    ('repartidor',    'Gestiona sus pedidos asignados y actualiza estados de entrega.'),
    ('vendedor',      'Registra ventas presenciales en el punto de venta.'),
    ('cliente',       'Cliente de la tienda en línea');

-- ============================================================
-- CATEGORÍAS
-- ============================================================
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
('Cuidado personal',    'Jabones, shampoo, cremas corporales, desodorantes'),
('Higiene oral',        'Cremas dentales, enjuagues bucales, cepillos dentales, hilo dental'),
('Cosméticos',          'Maquillaje, protectores solares, cremas faciales'),
('Bebés y maternidad',  'Pañales, toallitas, cremas antipañalitis, leches de fórmula'),
('Artículos de aseo',   'Rasuradores, papel higiénico, algodón, curitas y gasas'),
('Snacks saludables',   'Barras energéticas, suplementos vitamínicos en presentación alimenticia'),
('Dispositivos médicos','Tensiómetros, termómetros, glucómetros, oxímetros — sin INVIMA de medicamento');

-- ============================================================
-- PROVEEDORES
-- ============================================================
INSERT IGNORE INTO proveedores (nit, nombre, pais_origen, telefono, correo) VALUES
    ('800123456-1', 'Laboratorios Bayer Colombia', 'Alemania',       '6012345678', 'ventas@bayer.com.co'),
    ('900234567-2', 'Pfizer Colombia S.A.S.',       'Estados Unidos', '6019876543', 'pedidos@pfizer.com.co'),
    ('800345678-3', 'Genfar S.A.',                  'Colombia',       '6023456789', 'info@genfar.com.co'),
    ('900456789-4', 'Tecnoquímicas S.A.',            'Colombia',       '6025678901', 'comercial@tq.com.co');

-- ============================================================
-- USUARIOS (contraseña: Admin1234)
-- Hash generado con: password_hash('Admin1234', PASSWORD_BCRYPT, ['cost'=>12])
-- ============================================================
INSERT IGNORE INTO usuarios
    (rol_id, tipo_documento, documento, nombres, apellidos, correo, telefono, contrasena_hash, activo)
VALUES
((SELECT rol_id FROM roles WHERE nombre='administrador'),
 'CC','10000001','Juan','Administrador','admin@farmaplus.com','3001234567',
 '$2y$12$uvVGaQlLlCVtqz3a7CUNHePgHSHtq1lZnRnDijGMjwxlKnF1kgKYi',1),
((SELECT rol_id FROM roles WHERE nombre='gerente'),
 'CC','10000002','Ana','Gerente','gerente@farmaplus.com','3002345678',
 '$2y$12$uvVGaQlLlCVtqz3a7CUNHePgHSHtq1lZnRnDijGMjwxlKnF1kgKYi',1),
((SELECT rol_id FROM roles WHERE nombre='cajero'),
 'CC','10000003','Luis','Cajero','cajero@farmaplus.com','3003456789',
 '$2y$12$uvVGaQlLlCVtqz3a7CUNHePgHSHtq1lZnRnDijGMjwxlKnF1kgKYi',1),
((SELECT rol_id FROM roles WHERE nombre='bodeguero'),
 'CC','10000004','María','Bodeguera','bodeguero@farmaplus.com','3004567890',
 '$2y$12$uvVGaQlLlCVtqz3a7CUNHePgHSHtq1lZnRnDijGMjwxlKnF1kgKYi',1),
((SELECT rol_id FROM roles WHERE nombre='repartidor'),
 'CC','10000005','Carlos','Repartidor','repartidor@farmaplus.com','3005678901',
 '$2y$12$uvVGaQlLlCVtqz3a7CUNHePgHSHtq1lZnRnDijGMjwxlKnF1kgKYi',1),
((SELECT rol_id FROM roles WHERE nombre='cliente'),
 'CC','10000006','Cliente','Demo','cliente@farmaplus.com','3006789012',
 '$2y$12$uvVGaQlLlCVtqz3a7CUNHePgHSHtq1lZnRnDijGMjwxlKnF1kgKYi',1);

-- ============================================================
-- CLIENTE + DIRECCIÓN
-- ============================================================
INSERT IGNORE INTO clientes (usuario_id, consentimiento_ley1581, fecha_consentimiento, ip_consentimiento)
SELECT usuario_id, 1, NOW(), '127.0.0.1' FROM usuarios WHERE correo='cliente@farmaplus.com';

INSERT IGNORE INTO direcciones_entrega (cliente_id, alias, direccion, barrio, ciudad, predeterminada)
SELECT c.cliente_id,'Casa','Cra 5 #23-45','La Vega','Neiva',1
FROM clientes c
INNER JOIN usuarios u ON u.usuario_id = c.usuario_id
WHERE u.correo='cliente@farmaplus.com';

-- ============================================================
-- PRODUCTOS (20 SKUs)
-- ============================================================
INSERT IGNORE INTO productos
    (categoria_id, proveedor_id, nombre, principio_activo, concentracion,
     forma_farmaceutica, codigo_invima, control_especial, precio_compra, precio_venta, stock_minimo)
VALUES
-- Analgésicos
(1,3,'Acetaminofén 500mg x20','Acetaminofén','500mg','Tableta','INVIMA2024M-001',0,800,2500,50),
(1,3,'Ibuprofeno 400mg x20','Ibuprofeno','400mg','Tableta','INVIMA2024M-002',0,1000,3200,40),
(1,1,'Aspirina 100mg x20','Ácido acetilsalicílico','100mg','Tableta','INVIMA2024M-003',0,600,1800,30),
(1,3,'Naproxeno 250mg x10','Naproxeno sódico','250mg','Tableta','INVIMA2024M-004',0,900,2800,25),
-- Antibióticos (control especial)
(2,2,'Amoxicilina 500mg x21','Amoxicilina','500mg','Cápsula','INVIMA2024M-010',1,4500,12000,20),
(2,4,'Azitromicina 500mg x3','Azitromicina','500mg','Tableta','INVIMA2024M-011',1,5000,14500,15),
-- Vitaminas
(3,3,'Vitamina C 500mg x30','Ácido ascórbico','500mg','Tableta','INVIMA2024M-020',0,700,2200,60),
(3,3,'Vitamina D3 1000IU x30','Colecalciferol','1000IU','Cápsula blanda','INVIMA2024M-021',0,1200,3800,40),
(3,3,'Complejo B x30','Vitamina B complejo','Varios','Tableta','INVIMA2024M-022',0,900,2800,40),
(3,1,'Zinc 20mg x30','Zinc elemental','20mg','Tableta','INVIMA2024M-023',0,800,2500,30),
-- Dermatología
(4,2,'Hidrocortisona crema 1%','Hidrocortisona','1%','Crema dermatológica','INVIMA2024M-030',0,2000,5500,20),
(4,4,'Clotrimazol crema 1%','Clotrimazol','1%','Crema','INVIMA2024M-031',0,1800,4900,15),
-- Gastroenterología
(5,3,'Omeprazol 20mg x14','Omeprazol','20mg','Cápsula','INVIMA2024M-040',0,1500,4200,35),
(5,3,'Ranitidina 150mg x20','Ranitidina','150mg','Tableta','INVIMA2024M-041',0,1200,3600,25),
(5,4,'Loperamida 2mg x10','Loperamida','2mg','Cápsula','INVIMA2024M-042',0,800,2400,20),
-- Respiratorio
(6,3,'Loratadina 10mg x10','Loratadina','10mg','Tableta','INVIMA2024M-050',0,700,2100,40),
(6,3,'Ambroxol jarabe 30mg','Ambroxol HCl','30mg/5ml','Jarabe','INVIMA2024M-051',0,2200,6500,20),
-- Cardiovascular
(7,1,'Losartán 50mg x30','Losartán potásico','50mg','Tableta','INVIMA2024M-060',0,3500,9800,20),
(7,2,'Atorvastatina 20mg x30','Atorvastatina cálcica','20mg','Tableta','INVIMA2024M-061',0,4000,11500,15),
-- Diabetes
(8,4,'Metformina 850mg x30','Metformina HCl','850mg','Tableta','INVIMA2024M-070',0,2500,7200,20);

-- ============================================================
-- LOTES
-- ============================================================
SET @admin = (SELECT usuario_id FROM usuarios WHERE correo='admin@farmaplus.com' LIMIT 1);
SET @prov3 = (SELECT proveedor_id FROM proveedores WHERE nit='800345678-3' LIMIT 1);

-- Lote principal (stock normal, vencimiento lejano)
INSERT INTO lotes
    (producto_id, proveedor_id, numero_lote, cantidad_inicial, cantidad_actual, fecha_vencimiento, registrado_por)
SELECT producto_id, @prov3,
       CONCAT('L24A-', LPAD(producto_id,3,'0')),
       100, 60,
       DATE_ADD(CURDATE(), INTERVAL 350 DAY),
       @admin
FROM productos WHERE activo=1;

-- Lote próximo a vencer (para disparar alertas de inventario)
INSERT INTO lotes
    (producto_id, proveedor_id, numero_lote, cantidad_inicial, cantidad_actual, fecha_vencimiento, registrado_por)
SELECT producto_id, @prov3,
       CONCAT('L23B-', LPAD(producto_id,3,'0')),
       40, 12,
       DATE_ADD(CURDATE(), INTERVAL 18 DAY),
       @admin
FROM productos WHERE activo=1 LIMIT 5;

-- ============================================================
-- CONFIGURACIÓN INICIAL
-- ============================================================
INSERT IGNORE INTO configuracion (clave, valor, descripcion) VALUES
    ('costo_envio_base',      '3000',      'Costo de envío fijo por domicilio (COP)'),
    ('stock_minimo_global',   '10',        'Stock mínimo global si el producto no tiene uno definido'),
    ('dias_alerta_vencim',    '30',        'Días antes del vencimiento para activar alerta'),
    ('nombre_farmacia',       'FarmaPlus', 'Nombre del establecimiento'),
    ('ciudad_cobertura',      'Neiva',     'Ciudad principal de cobertura de domicilios'),
    ('correo_notificaciones', '',          'Correo para notificaciones internas administrativas');

-- ============================================================
-- VENTAS DE DEMOSTRACIÓN (últimos 7 días)
-- ============================================================
SET @cajero = (SELECT usuario_id FROM usuarios WHERE correo='cajero@farmaplus.com' LIMIT 1);
SET @year   = YEAR(CURDATE());

INSERT IGNORE INTO ventas_presenciales
    (vendedor_id, numero_comprobante, subtotal, total, metodo_pago, created_at) VALUES
(@cajero, CONCAT('FP-',@year,'-0001'), 18700, 18700, 'efectivo',        DATE_SUB(NOW(), INTERVAL 6 DAY)),
(@cajero, CONCAT('FP-',@year,'-0002'),  6500,  6500, 'tarjeta_debito',  DATE_SUB(NOW(), INTERVAL 5 DAY)),
(@cajero, CONCAT('FP-',@year,'-0003'), 12200, 12200, 'efectivo',        DATE_SUB(NOW(), INTERVAL 4 DAY)),
(@cajero, CONCAT('FP-',@year,'-0004'),  9800,  9800, 'transferencia',   DATE_SUB(NOW(), INTERVAL 3 DAY)),
(@cajero, CONCAT('FP-',@year,'-0005'), 22400, 22400, 'tarjeta_credito', DATE_SUB(NOW(), INTERVAL 2 DAY)),
(@cajero, CONCAT('FP-',@year,'-0006'),  7500,  7500, 'efectivo',        DATE_SUB(NOW(), INTERVAL 1 DAY)),
(@cajero, CONCAT('FP-',@year,'-0007'), 15000, 15000, 'efectivo',        NOW());

-- Detalles de venta
SET @v7  = (SELECT venta_id FROM ventas_presenciales WHERE numero_comprobante = CONCAT('FP-',YEAR(NOW()),'-0007') LIMIT 1);
SET @lA1 = (SELECT lote_id FROM lotes WHERE numero_lote = 'L24A-001' LIMIT 1);
SET @lA3 = (SELECT lote_id FROM lotes WHERE numero_lote = 'L24A-003' LIMIT 1);
SET @lA7 = (SELECT lote_id FROM lotes WHERE numero_lote = 'L24A-007' LIMIT 1);
SET @pA1 = (SELECT producto_id FROM productos WHERE codigo_invima='INVIMA2024M-001' LIMIT 1);
SET @pA3 = (SELECT producto_id FROM productos WHERE codigo_invima='INVIMA2024M-003' LIMIT 1);
SET @pA7 = (SELECT producto_id FROM productos WHERE codigo_invima='INVIMA2024M-020' LIMIT 1);

INSERT IGNORE INTO detalle_venta
    (venta_id, producto_id, lote_id, cantidad, precio_unitario, subtotal) VALUES
(@v7, @pA1, @lA1, 4, 2500, 10000),
(@v7, @pA3, @lA3, 3, 1800,  5400),
(@v7, @pA7, @lA7, 2, 2200,  4400);

-- Resultado final
SELECT CONCAT(
    'Seed OK — Usuarios:', (SELECT COUNT(*) FROM usuarios),
    ' | Productos:', (SELECT COUNT(*) FROM productos),
    ' | Lotes:', (SELECT COUNT(*) FROM lotes),
    ' | Ventas:', (SELECT COUNT(*) FROM ventas_presenciales)
) AS resultado;
