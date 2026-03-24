# FarmaPlus CRM

> Sistema de gestión integral para droguería pequeña — Colombia  
> Versión: 1.0.0 (MVP) · Estado actual: En desarrollo  
> Programa: Análisis y Desarrollo de Software — SENA  
> Fecha estimada de entrega MVP: Abril 2026

---

## Tabla de contenido

1. [¿Qué es FarmaPlus?](#1-qué-es-farmaplus)
2. [Problemática y justificación](#2-problemática-y-justificación)
3. [Objetivos del sistema](#3-objetivos-del-sistema)
4. [Marco legal colombiano](#4-marco-legal-colombiano)
5. [Actores del sistema](#5-actores-del-sistema)
6. [Módulos del sistema](#6-módulos-del-sistema)
7. [Requerimientos funcionales](#7-requerimientos-funcionales)
8. [Requerimientos no funcionales](#8-requerimientos-no-funcionales)
9. [Reglas de negocio](#9-reglas-de-negocio)
10. [Historias de usuario](#10-historias-de-usuario)
11. [Stack tecnológico](#11-stack-tecnológico)
12. [Arquitectura del sistema](#12-arquitectura-del-sistema)
13. [Modelo de datos](#13-modelo-de-datos)
14. [Estructura de carpetas](#14-estructura-de-carpetas)
15. [Mockups diseñados](#15-mockups-diseñados)
16. [Sistema de diseño](#16-sistema-de-diseño)
17. [Cronograma de desarrollo](#17-cronograma-de-desarrollo)
18. [Configuración del entorno](#18-configuración-del-entorno)
19. [Variables de entorno](#19-variables-de-entorno)
20. [Convenciones del proyecto](#20-convenciones-del-proyecto)

---

## 1. ¿Qué es FarmaPlus?

**FarmaPlus CRM** es una aplicación web de gestión integral para una droguería pequeña de una sola sede en Colombia. Centraliza en un único sistema:

- El control de inventario farmacéutico por lotes con método FEFO
- Las ventas presenciales (POS) y en línea (e-commerce)
- El servicio de domicilios con cálculo automático de tarifas
- La gestión de clientes con cumplimiento de la Ley 1581/2012
- Los reportes gerenciales con exportación PDF/Excel

El sistema está orientado a una **droguería pequeña** (menos de 20 empleados), con una sola sede física y un canal de ventas en línea. La arquitectura está diseñada para escalar a múltiples sedes en versiones futuras sin refactorización estructural.

---

## 2. Problemática y justificación

### Problemática identificada

FarmaPlus Droguería opera sin un sistema de información centralizado, apoyándose en Excel y procesos manuales. Las problemáticas se agrupan en tres categorías:

#### Problemas Tecnológicos
- Uso de sistemas obsoletos o Excel manual para registrar ventas e inventario
- Ausencia de respaldo automático de información: ante una falla los datos se pierden permanentemente
- Falta de seguridad en datos: cualquier empleado puede modificar registros sin control ni auditoría

#### Problemas de Inventario
- Medicamentos vencidos por mala rotación de stock: se vende primero lo que llega último (ausencia de método FEFO)
- No se lleva control por lote ni fecha de vencimiento, impidiendo identificar unidades específicas próximas a caducar
- Ausencia de alertas automáticas de stock mínimo, provocando quiebres de inventario y pérdida de ventas por desabastecimiento

#### Problemas Legales y Sanitarios
- Incumplimiento potencial de las normas de almacenamiento y dispensación exigidas por la Resolución 1403 de 2007
- Falta de trazabilidad por lote: ante una alerta sanitaria del INVIMA, la droguería no puede identificar ni rastrear las unidades afectadas

### Justificación

| Dimensión | Argumento |
|---|---|
| **Técnica** | Reemplaza herramientas obsoletas con un sistema centralizado, autenticado, con respaldo automático y log de auditoría |
| **Sanitaria/Legal** | Permite cumplir Res. 1403/2007, Decreto 677/1995, Ley 2300/2023 y Ley 1581/2012 de forma sistemática |
| **Económica** | Reduce pérdidas por vencimiento (FEFO + alertas), elimina errores manuales y habilita canal de ventas en línea |

---

## 3. Objetivos del sistema

### Objetivo general

Desarrollar un sistema CRM web para la gestión integral de FarmaPlus Droguería que resuelva las problemáticas tecnológicas, de inventario y legales-sanitarias identificadas, integrando control de stock por lotes, ventas presenciales y en línea, domicilios y reportes gerenciales, en cumplimiento con la normativa colombiana vigente.

### Objetivos específicos

1. Implementar módulo de autenticación con control de acceso por roles (RBAC) y log de auditoría
2. Desarrollar sistema de gestión de inventario por lotes con método FEFO y alertas automáticas
3. Registrar el Registro INVIMA como campo obligatorio en todos los productos y controlar medicamentos de control especial
4. Implementar canal e-commerce con integración de pasarela MercadoPago (PSE, Nequi, tarjetas)
5. Construir módulo de domicilios con cálculo automático de tarifas por distancia y volumen
6. Garantizar cumplimiento de la Ley 1581/2012 mediante consentimiento explícito y gestión de datos personales
7. Generar reportes exportables de ventas, productos más vendidos y alertas de inventario

---

## 4. Marco legal colombiano

| Norma | Descripción | Impacto en el sistema |
|---|---|---|
| **Ley 1581/2012** (Habeas Data) | Protección de datos personales | Consentimiento explícito al registro del cliente; registro de fecha, hora e IP del consentimiento |
| **Decreto 1377/2013** | Reglamenta Ley 1581 | Política de tratamiento de datos; gestión de solicitudes de supresión |
| **Resolución 1403/2007** | Modelo de gestión farmacéutica; exige Director Técnico Químico Farmacéutico | El rol Gerente representa al Director Técnico; trazabilidad de dispensación |
| **Decreto 677/1995** (INVIMA) | Regula registros sanitarios | Campo `codigo_invima` obligatorio en todos los productos |
| **Ley 2300/2023** | Prohíbe venta sin fórmula de antibióticos, opioides y psicotrópicos | Productos de control especial bloqueados en e-commerce; requieren fórmula en POS |
| **Resolución 2954/2023** | Receta electrónica | Contemplado para v2.0; arquitectura preparada |
| **Ley 527/1999** | Comercio electrónico | Comprobantes digitales con validez legal; logs de transacciones |
| **Circular 007/2018 SFC** | Pasarelas de pago | No se almacenan datos bancarios; MercadoPago maneja el procesamiento |

> **Decisión técnica MVP:** Se priorizan Ley 1581, Decreto 677 (INVIMA) y Ley 2300 (control especial). Las demás se contemplan en el diseño pero se implementan completamente en versiones posteriores.

---

## 5. Actores del sistema

| Actor | Descripción | Permisos principales |
|---|---|---|
| **Administrador del Sistema** | Perfil TI/propietario. Acceso total. Puede ser externo | CRUD total · Configuración · Logs · Gestión de roles |
| **Gerente del Local** | Director operativo (equivale al Director Técnico Res. 1403/2007). Controla personal, aprueba devoluciones | Gestión de empleados · Devoluciones · Reportes · Alertas |
| **Auxiliar / Gestor de Stock** | Controla el inventario físico. Registra entradas de mercancía y lotes | CRUD productos e inventario · Registro de lotes · Alertas |
| **Vendedor** | Atiende clientes presencialmente. Registra ventas POS | Ventas presenciales · Comprobantes · Consulta catálogo |
| **Cliente** | Usuario externo. Se registra para comprar en línea | Catálogo · Carrito · Checkout · Historial de pedidos |
| **Repartidor** | Personal de entrega. Solo ve sus pedidos asignados | Lectura de pedidos · Actualización de estados de entrega |

> **Nota técnica:** Gerente y Administrador son roles separados por el principio de mínimo privilegio. El Administrador configura el sistema; el Gerente opera el negocio.

---

## 6. Módulos del sistema

| # | Módulo | Descripción |
|---|---|---|
| 1 | **Autenticación y RBAC** | Login por correo o cédula + contraseña, bloqueo por intentos, recuperación de contraseña, sesiones PHP, log de auditoría |
| 2 | **Gestión de Clientes** | Registro con consentimiento Ley 1581, múltiples direcciones de entrega, historial de pedidos, perfil editable |
| 3 | **Dashboard** | KPIs en tiempo real: ventas del día, pedidos pendientes, alertas de inventario, clientes. Accesos rápidos por rol |
| 4 | **Gestión de Productos e Inventario** | Catálogo con INVIMA obligatorio, tipo de venta (libre/control especial), categorías, proveedores, stock mínimo |
| 5 | **Control de Lotes (FEFO)** | Registro de entradas por lote con fecha de vencimiento, método FEFO en descuentos, alertas automáticas < 30 días |
| 6 | **Alertas de Inventario** | Panel diferenciado: stock mínimo (ámbar) y vencimiento próximo (rojo/naranja). Resolución de alertas |
| 7 | **Ventas Presenciales (POS)** | Pantalla full optimizada para mostrador. Carrito, control especial con fórmula médica, métodos de pago, comprobante |
| 8 | **E-commerce (Tienda)** | Catálogo filtrado (sin control especial), carrito, checkout en 3 pasos, MercadoPago |
| 9 | **Pedidos en Línea** | Listado de pedidos con estados, asignación de repartidor, detalle con timeline |
| 10 | **Domicilios** | Cálculo automático: tarifa base + recargo por distancia + recargo por volumen. Zonas de cobertura configurables |
| 11 | **Panel Repartidor** | Vista simplificada solo con pedidos asignados, actualización de estados, reporte de problemas |
| 12 | **Reportes** | Ventas por período, productos más vendidos, categorías, rendimiento por vendedor. Exportación PDF/Excel |
| 13 | **Gestión de Usuarios** | CRUD de empleados por el Administrador. Asignación de roles, suspensión de cuentas |
| 14 | **Configuración del Sistema** | Tarifas de domicilio, zonas de cobertura, stock mínimo global, variables de negocio |

---

## 7. Requerimientos funcionales

### Módulo 1 — Autenticación

| ID | Descripción | Prioridad |
|---|---|---|
| RF-1.1 | El sistema permite iniciar sesión con correo electrónico o número de cédula + contraseña | Alta |
| RF-1.2 | El sistema asigna vistas, menús y permisos según el rol del usuario autenticado (RBAC) | Alta |
| RF-1.3 | El sistema bloquea la cuenta 15 minutos tras 5 intentos fallidos consecutivos | Alta |
| RF-1.4 | El sistema permite recuperar contraseña por correo con enlace de 30 minutos de expiración | Media |
| RF-1.5 | El sistema registra en log: usuario, fecha, hora, IP y acción en cada sesión | Media |

### Módulo 2 — Clientes

| ID | Descripción | Prioridad |
|---|---|---|
| RF-2.1 | El sistema permite al cliente crear cuenta con nombre, cédula, correo, teléfono y contraseña | Alta |
| RF-2.2 | El sistema solicita y registra consentimiento explícito (Ley 1581/2012) con fecha, hora e IP | Alta |
| RF-2.3 | El sistema permite al cliente registrar múltiples direcciones de entrega | Alta |
| RF-2.4 | El sistema permite al cliente editar su perfil y gestionar sus direcciones | Media |
| RF-2.5 | El sistema permite al cliente consultar su historial de pedidos y estado de cada uno | Media |

### Módulo 3 — Dashboard

| ID | Descripción | Prioridad |
|---|---|---|
| RF-3.1 | El dashboard muestra KPIs en tiempo real: ventas del día, pedidos pendientes, alertas de inventario, clientes | Alta |
| RF-3.2 | El Administrador puede crear, editar, suspender y eliminar cuentas de empleados | Alta |
| RF-3.3 | El Administrador puede configurar variables de domicilio: tarifa base, rangos de distancia, zonas de cobertura | Alta |
| RF-3.4 | El Gerente puede ver el rendimiento del personal activo (ventas por vendedor) | Media |

### Módulo 4 — Inventario y Productos

| ID | Descripción | Prioridad |
|---|---|---|
| RF-4.1 | El sistema permite registrar productos con: nombre, principio activo, concentración, forma farmacéutica, INVIMA (obligatorio), tipo de venta, precio compra/venta, stock mínimo | Alta |
| RF-4.2 | El sistema permite registrar lotes de entrada con: número de lote, cantidad, fecha de vencimiento, proveedor | Alta |
| RF-4.3 | El sistema organiza y permite búsqueda por nombre, categoría, laboratorio y lote | Alta |
| RF-4.4 | Al confirmarse una venta, el sistema descuenta automáticamente del lote más próximo a vencer (FEFO) | Alta |
| RF-4.5 | El sistema genera alertas cuando un producto llega al stock mínimo o la fecha de vencimiento es < 30 días | Alta |
| RF-4.6 | El sistema mantiene datos maestros de proveedores: nombre, NIT, teléfono, correo | Media |

### Módulo 5 — Ventas POS

| ID | Descripción | Prioridad |
|---|---|---|
| RF-5.1 | El vendedor puede registrar ventas presenciales con búsqueda de productos, carrito y emisión de comprobante | Alta |
| RF-5.2 | El sistema bloquea la venta de productos de control especial sin número de fórmula médica ingresado | Alta |
| RF-5.3 | El sistema soporta métodos de pago: efectivo, tarjeta débito, tarjeta crédito y transferencia | Alta |
| RF-5.4 | El sistema genera comprobante digital con número único, fecha, productos, precios y total | Alta |
| RF-5.5 | El vendedor puede enviar el comprobante por correo al cliente vía PHPMailer | Media |

### Módulo 6 — E-commerce

| ID | Descripción | Prioridad |
|---|---|---|
| RF-6.1 | El sistema ofrece al cliente un catálogo filtrado: sin medicamentos de control especial visibles | Alta |
| RF-6.2 | El cliente puede agregar productos al carrito y visualizar subtotal en tiempo real | Alta |
| RF-6.3 | El sistema calcula automáticamente el costo del domicilio (tarifa base + distancia + volumen) antes del pago | Alta |
| RF-6.4 | El sistema integra MercadoPago para pagos por PSE, Nequi y tarjeta | Alta |
| RF-6.5 | El sistema emite confirmación de compra por correo al aprobar el pago (webhook MercadoPago) | Alta |
| RF-6.6 | El sistema descuenta stock por FEFO al confirmar el pago | Alta |

### Módulo 7 — Pedidos y Domicilios

| ID | Descripción | Prioridad |
|---|---|---|
| RF-7.1 | El sistema muestra listado de pedidos con estados diferenciados y permite asignar repartidor | Alta |
| RF-7.2 | El sistema muestra al repartidor solo sus pedidos asignados con dirección y contacto del cliente | Alta |
| RF-7.3 | El repartidor puede actualizar el estado del pedido: Preparando / En camino / Entregado / Devuelto-Fallido | Alta |
| RF-7.4 | El sistema notifica al cliente por correo cuando su pedido cambia de estado | Media |

### Módulo 8 — Reportes

| ID | Descripción | Prioridad |
|---|---|---|
| RF-8.1 | El sistema permite generar reportes de ventas filtrados por rango de fechas (diario, semanal, mensual) | Alta |
| RF-8.2 | El sistema muestra productos más vendidos, categorías populares y rendimiento por vendedor | Media |
| RF-8.3 | El sistema permite exportar reportes en PDF y Excel | Media |
| RF-8.4 | El sistema genera reporte de productos próximos a vencer y con stock bajo, exportable en PDF | Alta |

---

## 8. Requerimientos no funcionales

| ID | Categoría | Descripción | Métrica |
|---|---|---|---|
| RNF-01 | Rendimiento | Tiempo de respuesta de páginas principales | ≤ 3 seg en p95 |
| RNF-02 | Disponibilidad | Uptime en horario de operación (6am–10pm) | ≥ 99% |
| RNF-03 | Seguridad — Auth | Contraseñas con hashing seguro; sesiones con expiración | bcrypt cost ≥ 12 |
| RNF-04 | Seguridad — Comunicación | Toda comunicación sobre HTTPS | TLS 1.2 o superior |
| RNF-05 | Seguridad — Datos | Datos personales bajo Ley 1581/2012; sin datos bancarios en BD propia | Cumplimiento Ley 1581 |
| RNF-06 | Usabilidad | Interfaz responsiva en móvil, tableta y escritorio | Responsive design |
| RNF-07 | Usabilidad | Vendedor nuevo puede registrar su primera venta en ≤ 5 minutos | ≤ 5 min |
| RNF-08 | Mantenibilidad | Código en capas separadas (presentación, lógica, datos); principios SOLID | Arquitectura MVC |
| RNF-09 | Escalabilidad | Arquitectura permite escalar a múltiples sedes sin rediseño | Multi-sede ready |
| RNF-10 | Compatibilidad | Funciona en Chrome 120+, Firefox 120+, Edge 120+ | 3 navegadores principales |
| RNF-11 | Auditoría | Log persistente de acciones críticas con usuario, fecha, hora e IP | Retención mínima 1 año |
| RNF-12 | Recuperación | Copias de seguridad automáticas diarias de la base de datos | Backup diario, retención 30 días |

---

## 9. Reglas de negocio

### Control de medicamentos y dispensación

- **RN-01:** Todo medicamento registrado debe tener número de Registro INVIMA. Campo obligatorio. Sin él el sistema no guarda el producto.
- **RN-02:** Los medicamentos de control especial (antibióticos, psicotrópicos, opioides) no pueden ser agregados al carrito en línea. En POS requieren número de fórmula médica antes de proceder.
- **RN-03:** Cada producto debe estar asociado a al menos un lote con fecha de vencimiento. Sin lote no puede ser vendido.
- **RN-04:** El sistema alerta cuando el stock es igual o inferior al mínimo configurado, o cuando la fecha de vencimiento es menor a 30 días.

### Ventas y transacciones

- **RN-05:** Una venta solo se confirma cuando el pago es aprobado. Ningún pedido en línea queda confirmado sin validación de MercadoPago.
- **RN-06:** Al confirmarse una venta, el sistema descuenta las unidades del lote más próximo a vencer (FEFO — First Expired, First Out).
- **RN-07:** El sistema no permite vender un producto con stock en cero, ni en POS ni en línea.
- **RN-08:** Todo comprobante debe incluir: número único, fecha/hora, productos con cantidad y precio unitario, total y método de pago.

### Cálculo de domicilios

- **RN-09:** Costo de envío = Tarifa base (configurable) + Recargo por distancia (rangos en km, configurable) + Recargo por volumen (configurable).
- **RN-10:** El sistema solo acepta pedidos a domicilio dentro de las zonas de cobertura configuradas por el Administrador.
- **RN-11:** El costo del domicilio se muestra al cliente antes de confirmar la compra como parte del resumen.

### Usuarios y acceso

- **RN-12:** Un usuario tiene un solo rol activo. Los permisos son exclusivos del rol asignado.
- **RN-13:** Solo el Administrador puede crear, editar, suspender o eliminar cuentas de empleados.
- **RN-14:** Los clientes se autoregistran. Los empleados solo pueden ser creados por el Administrador.
- **RN-15:** Tras 5 intentos fallidos de inicio de sesión, la cuenta se bloquea 15 minutos.

### Protección de datos (Ley 1581/2012)

- **RN-16:** Al registrarse, el cliente debe aceptar explícitamente la Política de Tratamiento de Datos Personales. Sin este consentimiento el registro no puede completarse.
- **RN-17:** El sistema registra fecha, hora e IP del consentimiento de cada cliente.
- **RN-18:** El sistema no almacena datos de tarjetas en su base de datos. El procesamiento es delegado completamente a MercadoPago.

---

## 10. Historias de usuario

Se listan las 29 historias de usuario organizadas por actor. Cada una incluye criterios de aceptación en formato Given/When/Then.

### Autenticación (todos los roles)

| ID | Historia | Criterios clave |
|---|---|---|
| HU-AUTH-01 | Como usuario, quiero iniciar sesión con correo o cédula | Redirección por rol; bloqueo a los 5 intentos; mensaje genérico sin revelar campo incorrecto |
| HU-AUTH-02 | Como usuario, quiero recuperar mi contraseña por correo | Enlace expira en 30 min; token de un solo uso; correo inexistente no se confirma |
| HU-AUTH-03 | Como usuario, quiero cerrar sesión de forma segura | Sesión destruida en servidor; botón "atrás" redirige al login |

### Cliente (6 historias)

| ID | Historia | Criterios clave |
|---|---|---|
| HU-CLI-01 | Registrarme con mis datos personales | Validación en tiempo real; consentimiento Ley 1581 obligatorio; registro de IP |
| HU-CLI-02 | Gestionar mis direcciones de entrega | Múltiples direcciones; historial preservado aunque se eliminen |
| HU-CLI-03 | Explorar el catálogo de productos | Filtros por categoría; control especial invisible; sin stock = botón deshabilitado |
| HU-CLI-04 | Gestionar mi carrito de compras | Límite de stock; carrito persiste 24h; recálculo inmediato |
| HU-CLI-05 | Completar checkout y pagar en línea | Cálculo de domicilio antes de pago; fuera de zona = bloqueado; correo de confirmación |
| HU-CLI-06 | Consultar el estado de mis pedidos | Historial cronológico; notificación por correo en cada cambio de estado |

### Administrador (5 historias)

| ID | Historia | Criterios clave |
|---|---|---|
| HU-ADMIN-01 | Crear cuentas de empleados | Sin duplicados de correo/cédula; log automático de creación |
| HU-ADMIN-02 | Editar y suspender empleados | Cambio de rol efectivo en próxima sesión; suspensión inmediata |
| HU-ADMIN-03 | Configurar variables de domicilio | Tarifas aplicadas a pedidos nuevos; pedidos anteriores no afectados |
| HU-ADMIN-04 | Consultar logs de auditoría | Filtro por usuario y fecha; log no modificable por ningún usuario |
| HU-ADMIN-05 | Configurar stock mínimo por producto | Umbral por producto; alerta automática al alcanzarlo |

### Gerente (4 historias)

| ID | Historia | Criterios clave |
|---|---|---|
| HU-GER-01 | Ver dashboard gerencial con KPIs | Alertas destacadas; clic en alerta lleva al producto afectado |
| HU-GER-02 | Supervisar rendimiento del personal | Ventas por vendedor; entregas por repartidor |
| HU-GER-03 | Gestionar devoluciones | Aprobación actualiza inventario; rechazo documenta razón |
| HU-GER-04 | Generar y exportar reportes | PDF y Excel; período personalizable |

### Auxiliar / Stock (4 historias)

| ID | Historia | Criterios clave |
|---|---|---|
| HU-AUX-01 | Registrar un nuevo producto | INVIMA obligatorio; control especial no visible en tienda |
| HU-AUX-02 | Registrar entrada de lote | Fecha pasada bloqueada; alerta automática si vencimiento < 30 días |
| HU-AUX-03 | Gestionar proveedores | NIT único; datos visibles en lotes vinculados |
| HU-AUX-04 | Consultar y gestionar alertas | Secciones diferenciadas stock/vencimiento; alerta se cierra al resolver |

### Vendedor (4 historias)

| ID | Historia | Criterios clave |
|---|---|---|
| HU-VEND-01 | Registrar venta presencial | Control especial exige fórmula médica; stock en cero bloquea venta; descuento FEFO automático |
| HU-VEND-02 | Emitir comprobante de venta | Número único; envío por correo vía PHPMailer |
| HU-VEND-03 | Consultar catálogo y stock en tiempo real | Stock actualizado; marcado claro de sin stock |
| HU-VEND-04 | Consultar mis ventas del día | Solo las propias; búsqueda por comprobante |

### Repartidor (3 historias)

| ID | Historia | Criterios clave |
|---|---|---|
| HU-REP-01 | Ver mis pedidos asignados | Solo los propios; dirección y contacto del cliente |
| HU-REP-02 | Actualizar estado de entrega | Cliente notificado por correo; devuelto exige observación + notifica Gerente |
| HU-REP-03 | Ver detalle completo de un pedido | Dirección con referencia destacada; productos y cantidades |

---

## 11. Stack tecnológico

| Capa | Tecnología | Versión | Justificación |
|---|---|---|---|
| **Backend** | PHP | 8.2+ | Lenguaje robusto, amplio soporte en Colombia, familiar en el ecosistema SENA |
| **Framework Backend** | Slim Framework | 4.x | Microframework ligero ideal para MVC en PHP. Sin magia oculta, código explícito |
| **Base de datos** | MySQL Server | 8.0 | Motor relacional sólido para datos financieros y de inventario. Open source |
| **Cliente BD** | MySQL Workbench | 8.0 | Herramienta de diseño y administración visual |
| **Consultas BD** | PDO + consultas preparadas | — | Sin ORM. SQL puro, transparente, enseña la base de datos real. Previene SQL injection |
| **Frontend** | HTML5 + CSS3 + JavaScript ES6+ | — | Sin frameworks pesados. Código propio, sin dependencias |
| **Framework CSS** | Tailwind CSS | 3.x (CDN para MVP) | Diseño responsivo rápido. Solo utilidades predefinidas |
| **Iconos** | Lucide Icons | Latest (CDN) | Librería consistente, ligera, 1000+ íconos SVG |
| **Tipografía** | Inter + JetBrains Mono | Google Fonts | Inter para UI; JetBrains Mono para códigos INVIMA, lotes y comprobantes |
| **Correos** | PHPMailer | 6.x | Librería estándar de la industria para correo en PHP |
| **Pagos** | MercadoPago PHP SDK | 3.x | Mayor penetración en Colombia. Soporta PSE, Nequi, tarjetas. SDK oficial |
| **Gestión de deps.** | Composer | 2.x | Gestor de dependencias estándar de PHP |
| **Servidor local** | WAMPSERVER64 | 3.x | Entorno de desarrollo Windows con PHP, MySQL y Apache preconfigurados |

> **Decisión clave:** Se usa PDO con consultas preparadas en lugar de un ORM (Eloquent/Doctrine). Razón: para un aprendiz SENA, escribir SQL real enseña la base de datos directamente, el código es más legible y la depuración más sencilla.

---

## 12. Arquitectura del sistema

### Patrón: MVC en 3 capas

```
Cliente (Navegador)
        │
        │  HTTP/HTTPS
        ▼
┌──────────────────┐
│   Views (PHP)    │  ← HTML renderizado por PHP, Tailwind CSS, JS vanilla
│   /views/**      │
└────────┬─────────┘
         │ request/response
         ▼
┌──────────────────┐
│   Router (Slim)  │  ← Enruta la URL al controlador correcto
│   /src/Routes/   │     + aplica Middleware (AuthMiddleware, RolMiddleware)
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│  Controllers     │  ← Recibe la petición, llama servicios, responde
│  /src/Controllers│
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│   Services       │  ← Lógica de negocio (FEFO, domicilio, alertas, correos)
│   /src/Services/ │
└────────┬─────────┘
         │
         ▼
┌──────────────────┐
│    Models        │  ← Acceso a BD con PDO + consultas preparadas
│   /src/Models/   │
└────────┬─────────┘
         │  SQL
         ▼
┌──────────────────┐
│   MySQL 8.0      │  ← Base de datos relacional, 13 tablas, 3FN
└──────────────────┘
         
Servicios externos:
  MercadoPago API  ←→  MercadoPagoService.php
  SMTP (Gmail/Mailtrap) ←→  EmailService.php (PHPMailer)
```

### Flujo de una petición típica

1. Usuario interactúa en la Vista (HTML/JS)
2. El navegador envía petición HTTP a Slim Router
3. `AuthMiddleware` verifica sesión PHP activa
4. `RolMiddleware` verifica que el rol tiene permiso sobre esa ruta
5. El Controller apropiado procesa la lógica
6. Llama a Services (FEFO, Alertas, Email) si es necesario
7. El Model ejecuta la consulta preparada sobre MySQL
8. Controller pasa datos a la Vista PHP para renderizar
9. Respuesta HTML llega al navegador

---

## 13. Modelo de datos

### Tablas (13 en total, 3FN)

```
roles                    → Roles del sistema (no enum, tabla separada)
usuarios                 → Todos los actores con FK a roles
clientes                 → Extensión de usuarios para rol cliente + consentimiento Ley 1581
direcciones_entrega      → Múltiples direcciones por cliente
categorias_producto      → Datos maestros de categorías
proveedores              → Laboratorios y distribuidores (NIT único)
productos                → Catálogo farmacéutico (INVIMA obligatorio)
lotes                    → Entradas de mercancía con fecha vencimiento (FEFO)
ajustes_stock            → Historial de todos los movimientos de inventario
alertas                  → Alertas activas de stock mínimo y vencimiento
pedidos                  → Pedidos en línea con estados y referencia MercadoPago
detalle_pedido           → Ítems de cada pedido (vinculado a lote para trazabilidad)
ventas_presenciales      → Transacciones del POS
detalle_venta            → Ítems de cada venta POS (vinculado a lote)
configuracion            → Variables del negocio en patrón clave-valor
recuperacion_contrasena  → Tokens de un solo uso para recuperación
logs_auditoria           → Registro inmutable de acciones críticas
```

### Decisiones de diseño relevantes

| Decisión | Justificación |
|---|---|
| `roles` tabla separada (no ENUM) | 3FN: el nombre del rol no depende de `usuario_id`. Permite agregar roles sin tocar el código |
| `clientes` tabla separada | 3FN: el consentimiento Ley 1581 solo aplica al rol cliente |
| `fecha_vencimiento` en `lotes`, no en `productos` | 2FN: depende del lote, no del producto. Sin esto FEFO es imposible |
| Sin campo `stock_actual` en `productos` | Evita anomalías de actualización. El stock se calcula sumando `cantidad_actual` de los lotes activos |
| `lote_id` en `detalle_pedido` y `detalle_venta` | Trazabilidad sanitaria: permite identificar clientes ante retiro INVIMA |
| `RESTRICT` en todas las FK | Protege integridad: no se puede borrar un producto con ventas registradas |
| `TINYINT(1)` en lugar de `BOOLEAN` | MySQL implementa BOOLEAN internamente como TINYINT(1). Más explícito |
| `recuperacion_contrasena` tabla propia | Token de un solo uso con expiración explícita. Seguridad correcta |

---

## 14. Estructura de carpetas

```
farmaplus-crm/
│
├── public/                         # Única carpeta expuesta al servidor web (DocumentRoot)
│   ├── index.php                   # Front Controller — punto de entrada único
│   └── assets/
│       ├── css/
│       │   ├── app.css             # Entrada de Tailwind (@tailwind base/components/utilities)
│       │   └── app.min.css         # CSS compilado por Tailwind CLI (no editar manualmente)
│       ├── js/
│       │   ├── app.js              # JS global (helpers, toasts, utils)
│       │   ├── carrito.js          # Lógica del carrito de compras
│       │   ├── pos.js              # Lógica del POS (búsqueda, qty, pago)
│       │   ├── validaciones.js     # Validaciones de formularios
│       │   └── dashboard.js        # KPIs y gráficas del dashboard
│       └── img/
│           ├── logo.png
│           └── favicon.ico
│
├── src/                            # Código fuente PHP — no expuesto al servidor
│   │
│   ├── Database/
│   │   └── Database.php            # Singleton PDO — única instancia de conexión
│   │
│   ├── Controllers/
│   │   ├── AuthController.php      # Login, logout, recuperación de contraseña
│   │   ├── UsuarioController.php   # CRUD de empleados (solo Admin)
│   │   ├── ClienteController.php   # Registro de cliente con consentimiento
│   │   ├── ProductoController.php  # CRUD de productos
│   │   ├── InventarioController.php# Lotes, alertas, proveedores
│   │   ├── VentaController.php     # Ventas presenciales POS
│   │   ├── PedidoController.php    # Pedidos en línea y domicilios
│   │   ├── RepartidorController.php# Panel del repartidor
│   │   ├── ReporteController.php   # Reportes y estadísticas
│   │   └── WebhookController.php   # Webhooks de MercadoPago
│   │
│   ├── Models/
│   │   ├── UsuarioModel.php        # Consultas PDO sobre tabla usuarios
│   │   ├── ClienteModel.php
│   │   ├── ProductoModel.php
│   │   ├── LoteModel.php
│   │   ├── PedidoModel.php
│   │   ├── DetallePedidoModel.php
│   │   ├── VentaModel.php
│   │   ├── DetalleVentaModel.php
│   │   ├── ProveedorModel.php
│   │   ├── CategoriaModel.php
│   │   ├── AlertaModel.php
│   │   └── LogAuditoriaModel.php
│   │
│   ├── Services/
│   │   ├── AuthService.php         # Hash bcrypt, bloqueo de cuenta, sesiones PHP
│   │   ├── FEFOService.php         # Descuento de stock por lote más próximo a vencer
│   │   ├── DomicilioService.php    # Cálculo de tarifa: base + distancia + volumen
│   │   ├── EmailService.php        # Wrapper de PHPMailer
│   │   ├── MercadoPagoService.php  # Wrapper del SDK de MercadoPago
│   │   └── AlertaService.php       # Generación automática de alertas de inventario
│   │
│   ├── Middleware/
│   │   ├── AuthMiddleware.php      # Verifica que haya sesión PHP activa
│   │   └── RolMiddleware.php       # Verifica que el rol tenga acceso a la ruta
│   │
│   └── Routes/
│       ├── auth.php                # POST /login, POST /logout, POST /recuperar
│       ├── admin.php               # Rutas exclusivas del Administrador
│       ├── gerente.php             # Rutas del Gerente
│       ├── inventario.php          # Productos, lotes, alertas, proveedores
│       ├── ventas.php              # POS presencial
│       ├── pedidos.php             # Pedidos en línea
│       ├── tienda.php              # Catálogo y checkout cliente
│       └── webhooks.php            # Webhooks externos (MercadoPago)
│
├── views/                          # Todo el frontend — vistas PHP renderizadas por el servidor
│   ├── layouts/
│   │   ├── base.php                # Layout principal: sidebar + topbar + área de contenido
│   │   ├── auth.php                # Layout para login y recuperación (sin sidebar)
│   │   ├── pos.php                 # Layout POS: pantalla completa sin sidebar
│   │   └── tienda.php              # Layout tienda: navbar superior + grid de productos
│   │
│   ├── auth/
│   │   ├── login.php
│   │   └── recuperar.php
│   │
│   ├── dashboard/
│   │   ├── admin.php               # Dashboard del Administrador
│   │   └── gerente.php             # Dashboard del Gerente
│   │
│   ├── clientes/
│   │   ├── lista.php
│   │   ├── perfil.php
│   │   └── form.php                # Crear/editar cliente
│   │
│   ├── inventario/
│   │   ├── productos.php           # Listado de productos
│   │   ├── producto_form.php       # Crear/editar producto
│   │   ├── ficha_producto.php      # Detalle del producto
│   │   ├── lotes.php               # Listado de lotes
│   │   ├── lote_form.php           # Registrar entrada de lote
│   │   ├── alertas.php             # Panel de alertas
│   │   └── proveedores.php         # Gestión de proveedores
│   │
│   ├── ventas/
│   │   ├── pos.php                 # Punto de venta presencial
│   │   └── comprobante.php         # Comprobante de venta
│   │
│   ├── pedidos/
│   │   ├── lista.php               # Listado de pedidos
│   │   └── detalle.php             # Detalle de un pedido
│   │
│   ├── tienda/                     # Vistas del cliente externo
│   │   ├── catalogo.php
│   │   ├── carrito.php
│   │   ├── checkout.php            # Checkout en 3 pasos
│   │   └── confirmacion.php        # Pedido confirmado post-pago
│   │
│   ├── repartidor/
│   │   ├── pedidos.php             # Lista de pedidos asignados
│   │   └── detalle.php             # Detalle de entrega
│   │
│   ├── reportes/
│   │   ├── ventas.php
│   │   └── inventario.php
│   │
│   └── admin/
│       ├── usuarios.php
│       └── configuracion.php
│
├── config/
│   ├── database.php                # Credenciales MySQL (carga desde .env)
│   ├── app.php                     # Variables globales: APP_URL, APP_NAME, versión
│   └── mail.php                    # Configuración SMTP para PHPMailer
│
├── database/
│   ├── farmaplus_schema.sql        # Script DDL completo — MySQL 8.0 compatible
│   └── farmaplus_seed.sql          # Datos iniciales: roles, configuración, usuario admin
│
├── mockups/                        # Prototipos HTML de alta fidelidad (referencia de UI)
│   ├── fp-01-login.html
│   ├── fp-02-recuperar.html
│   ├── fp-07-dashboard.html
│   ├── fp-08-productos.html
│   ├── fp-17-pedidos.html
│   ├── fp-18-detalle-pedido.html
│   ├── fp-19-pos.html
│   ├── fp-20-comprobante.html
│   ├── fp-21-registro-lote.html
│   ├── fp-22-alertas.html
│   ├── fp-23-tienda.html
│   ├── fp-24-checkout.html
│   ├── fp-25-repartidor.html
│   └── ... (25 mockups en total)
│
├── vendor/                         # Dependencias de Composer (no subir a git)
│
├── tailwind.config.js              # Paleta corporativa + tipografía en Tailwind
├── package.json                    # Tailwind CLI como devDependency
├── composer.json                   # Slim Framework, PHPMailer, MercadoPago SDK
├── .env                            # Variables de entorno (no subir a git)
├── .env.example                    # Plantilla de variables de entorno
├── .gitignore
└── README.md                       # Este archivo
```

---

## 15. Mockups diseñados

| Archivo | Pantalla | Estado | Notas |
|---|---|---|---|
| `fp-01-login.html` | Login | ✅ Listo | Acepta correo o cédula |
| `fp-02-recuperar.html` | Recuperar contraseña | ✅ Listo | Estado post-envío incluido |
| `fp-07-dashboard.html` | Dashboard Admin/Gerente | ✅ Listo | KPIs + alertas + últimas ventas |
| `fp-clientes.html` | Listado de clientes | ✅ Listo | Tabla con filtros y paginación |
| `fp-perfil-cliente.html` | Perfil de cliente | ✅ Listo | Historial de compras |
| `fp-08-productos.html` | Listado de productos | ✅ Listo | INVIMA, stock, alertas |
| `fp-ficha-producto.html` | Ficha de producto | ✅ Listo | Detalle con lotes |
| `fp-form-producto.html` | Formulario producto | ✅ Listo | INVIMA obligatorio |
| `fp-form-cliente.html` | Formulario cliente | ✅ Listo | Sin datos médicos |
| `fp-reportes.html` | Reportes de ventas | ✅ Listo | Exportación PDF/Excel |
| `fp-reportes-inventario.html` | Reportes de inventario | ✅ Listo | Alertas exportables |
| `fp-17-pedidos.html` | Listado de pedidos | ✅ Listo | Modal asignación repartidor |
| `fp-18-detalle-pedido.html` | Detalle de pedido | ✅ Listo | Timeline de estados |
| `fp-19-pos.html` | Punto de venta (POS) | ✅ Listo | Pantalla full, fórmula médica |
| `fp-20-comprobante.html` | Comprobante de venta | ✅ Listo | Imprimible, PDF, correo |
| `fp-21-registro-lote.html` | Registro de lote | ✅ Listo | Alerta vencimiento < 30 días |
| `fp-22-alertas.html` | Panel de alertas | ✅ Listo | Stock mínimo + vencimiento |
| `fp-23-tienda.html` | Catálogo tienda cliente | ✅ Listo | Sin control especial |
| `fp-24-checkout.html` | Carrito y checkout | ✅ Listo | 3 pasos, domicilio calculado |
| `fp-25-repartidor.html` | Panel repartidor | ✅ Listo | Solo pedidos propios |

**Sistema de diseño aplicado en todos los mockups:**

- **Paleta:** Azul primario `#1A6B8A`, verde salud `#2A9D8F`, sidebar `#1A3A4A`
- **Tipografía:** Inter (UI) + JetBrains Mono (códigos, números de referencia)
- **Iconos:** Lucide Icons (CDN)
- **Leyes UX:** Hick (máx. 5 ítems por sección), Fitts (botones ≥ 48px), Jakob (sidebar izquierdo), Miller (máx. 10 filas/página)

---

## 16. Sistema de diseño

### Paleta de colores

```css
--color-primary:        #1A6B8A;   /* Azul médico — principal */
--color-primary-dark:   #1A3A4A;   /* Azul noche — sidebar, headers */
--color-primary-light:  #4A9BB5;   /* Azul medio — hover, links */
--color-secondary:      #2A9D8F;   /* Verde salud — acciones, éxito */
--color-bg-main:        #F4F9FC;   /* Blanco clínico — fondo general */
--color-bg-card:        #E9F5F8;   /* Azul hielo — fondo de cards */
--color-success:        #27AE60;
--color-error:          #E74C3C;
--color-warning:        #F39C12;
--color-info:           #3498DB;
--color-text-primary:   #2C3E50;
--color-text-secondary: #7F8C8D;
--color-border:         #BDC3C7;
```

### Tipografía

| Uso | Fuente | Pesos |
|---|---|---|
| UI general | Inter | 400, 500, 600, 700 |
| Códigos (INVIMA, lotes, comprobantes, IDs) | JetBrains Mono | 400, 500 |

### Componentes base

- **Botón primario:** `#1A6B8A`, 48px alto, border-radius 8px
- **Inputs:** 48px alto, border 1.5px `#BDC3C7`, focus border `#1A6B8A`
- **Sidebar:** 240px, `#1A3A4A`, ítem activo `#2A9D8F`
- **Topbar:** 64px, fondo blanco, sombra sutil
- **Badges de estado:** border-radius full, padding 4px 10px, colores semánticos

---

## 17. Cronograma de desarrollo

| Semana | Fechas | Módulos | Entregable |
|---|---|---|---|
| **1** | 15–21 mar | Configuración del proyecto + Autenticación | Login funcional, roles activos, sesiones por rol |
| **2** | 22–28 mar | Inventario: productos, lotes, alertas | Auxiliar registra productos con INVIMA y lotes; alertas automáticas |
| **3** | 29 mar–4 abr | Ventas POS + Módulo de clientes | Vendedor registra ventas con FEFO; cliente se registra con consentimiento |
| **4** | 5–11 abr | E-commerce + MercadoPago + Domicilios | Flujo completo de compra en línea con pago sandbox |
| **5** | 12–16 abr | Dashboards + Reportes + Testing + Cierre | MVP funcional, datos de demo, documentación lista |

### Riesgos identificados

| Riesgo | Probabilidad | Mitigación |
|---|---|---|
| PHPMailer SMTP (configuración) | Media | Configurar el primer día de la semana 3 con Mailtrap |
| Webhooks MercadoPago (requiere URL pública) | Alta | Configurar ngrok el primer día de la semana 4 |
| Scope creep (querer hacer demasiado) | Alta | Si al miércoles de cada semana el entregable no está, no avanzar al siguiente módulo |

---

## 18. Configuración del entorno

### Requisitos previos

```
PHP 8.2+
MySQL 8.0
Composer 2.x
Node.js 18+ (solo para Tailwind CLI)
WampServer64 (recomendado para Windows) o XAMPP
```

### Instalación paso a paso

```bash
# 1. Clonar el repositorio
git clone https://github.com/tu-usuario/farmaplus-crm.git
cd farmaplus-crm

directorio recomendado: C:/wamp64/www/...

# 2. Instalar dependencias PHP
composer install

# 3. Instalar Tailwind CLI
npm install

# 4. Copiar y configurar variables de entorno
cp .env.example .env
# Editar .env con tus credenciales de MySQL, SMTP y MercadoPago

# 5. Crear la base de datos en MySQL Workbench
# Ejecutar: database/farmaplus_schema.sql
# Luego:    database/farmaplus_seed.sql

# 6. Compilar CSS con Tailwind (modo watch en desarrollo)
npx tailwindcss -i ./public/assets/css/app.css \
                -o ./public/assets/css/app.min.css \
                --watch

# 7. Configurar DocumentRoot en Wampserver64/Apache
# Apuntar al directorio: farmaplus-crm/public/
```

### Dependencias del `composer.json`

```json
{
    "require": {
        "php": "^8.2",
        "slim/slim": "^4.0",
        "slim/psr7": "^1.0",
        "phpmailer/phpmailer": "^6.0",
        "mercadopago/dx-php": "^3.0",
        "vlucas/phpdotenv": "^5.0"
    }
}
```

---

## 19. Variables de entorno

```env
# Aplicación
APP_NAME=FarmaPlus CRM
APP_URL=http://localhost
APP_ENV=development
APP_VERSION=1.0.0

# Base de datos
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=farmaplus_db
DB_USER=root
DB_PASS=

# Correo (desarrollo: usar Mailtrap)
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USER=tu_usuario_mailtrap
MAIL_PASS=tu_pass_mailtrap
MAIL_FROM=noreply@farmaplus.co
MAIL_FROM_NAME=FarmaPlus CRM

# MercadoPago (usar credenciales de sandbox para desarrollo)
MP_ACCESS_TOKEN=TEST-xxxxxxxxxxxx
MP_PUBLIC_KEY=TEST-xxxxxxxxxxxx
MP_WEBHOOK_SECRET=tu_webhook_secret

# Sesión
SESSION_LIFETIME=7200
SESSION_NAME=farmaplus_session
```

---

## 20. Convenciones del proyecto

### Nomenclatura

| Elemento | Convención | Ejemplo |
|---|---|---|
| Clases PHP | PascalCase | `ProductoController`, `FEFOService` |
| Métodos PHP | camelCase | `registrarLote()`, `calcularDomicilio()` |
| Variables PHP | camelCase | `$stockActual`, `$fechaVencimiento` |
| Tablas BD | snake_case, plural | `detalle_pedido`, `logs_auditoria` |
| Columnas BD | snake_case | `fecha_vencimiento`, `codigo_invima` |
| PKs | `{tabla_singular}_id` | `producto_id`, `lote_id` |
| Archivos de vista | snake_case | `registro_lote.php`, `lista_clientes.php` |
| Rutas URL | kebab-case | `/inventario/registro-lote`, `/tienda/checkout` |
| Números de comprobante | `FP-{AÑO}-{SECUENCIA}` | `FP-2026-0349` |
| Números de lote | `{LAB}-{AÑO}-{SEQ}` | `TQ-2024-089`, `GF-2025-031` |

### Estructura de un Controlador

```php
<?php
namespace App\Controllers;

use App\Database\Database;
use App\Models\ProductoModel;
use App\Services\AlertaService;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ProductoController
{
    private ProductoModel $productoModel;
    private AlertaService $alertaService;

    public function __construct()
    {
        $db = Database::getInstance()->getConnection();
        $this->productoModel = new ProductoModel($db);
        $this->alertaService = new AlertaService($db);
    }

    public function listar(Request $request, Response $response): Response
    {
        $productos = $this->productoModel->listarConStock();
        // pasar datos a la vista...
    }
}
```

### Estructura de un Modelo (PDO puro)

```php
<?php
namespace App\Models;

use PDO;

class LoteModel
{
    public function __construct(private PDO $db) {}

    public function obtenerLotesFEFO(int $productoId): array
    {
        $sql = "SELECT lote_id, cantidad_actual, fecha_vencimiento
                FROM lotes
                WHERE producto_id = :producto_id
                  AND cantidad_actual > 0
                  AND activo = 1
                ORDER BY fecha_vencimiento ASC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([':producto_id' => $productoId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
```

---
*Proyecto académico — Desarrollado por aprendices del sena!!! >— SENA · 2026*
