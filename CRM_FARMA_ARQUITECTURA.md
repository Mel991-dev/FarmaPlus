# CRM FARMA+ — Documento de Arquitectura de Software
**Versión:** 1.0.0  
**Fecha:** 22 de febrero de 2026  
**Estado:** Modelado completo — Listo para desarrollo  
**Proyecto:** CRM interno para cadena de droguerías colombiana  
**Contexto académico:** Aprendiz SENA — Etapa de Aprendizaje  

---

## Tabla de Contenido

1. [Contexto y Alcance del Proyecto](#1-contexto-y-alcance-del-proyecto)
2. [Actores del Sistema](#2-actores-del-sistema)
3. [Requerimientos Funcionales](#3-requerimientos-funcionales)
4. [Requerimientos No Funcionales](#4-requerimientos-no-funcionales)
5. [Reglas de Negocio](#5-reglas-de-negocio)
6. [Casos de Uso](#6-casos-de-uso)
7. [Historias de Usuario y Criterios de Aceptación](#7-historias-de-usuario-y-criterios-de-aceptación)
8. [Modelo de Datos](#8-modelo-de-datos)
9. [Arquitectura del Sistema](#9-arquitectura-del-sistema)
10. [Stack Tecnológico](#10-stack-tecnológico)
11. [Estructura de Carpetas](#11-estructura-de-carpetas)
12. [API REST — Endpoints](#12-api-rest--endpoints)
13. [Sistema de Diseño UX/UI](#13-sistema-de-diseño-uxui)
14. [Plan de Desarrollo](#14-plan-de-desarrollo)
15. [Base de Datos — Consideraciones de Implementación](#15-base-de-datos--consideraciones-de-implementación)

---

## 1. Contexto y Alcance del Proyecto

### ¿Qué es CRM FARMA+?

CRM FARMA+ es un sistema de gestión de relaciones con clientes desarrollado para una **cadena de droguerías pequeña** (menos de 20 empleados) en Colombia. Opera exclusivamente en modo interno — solo los empleados de la empresa acceden a él. No es un portal de clientes ni un punto de venta.

### Propósito

Centralizar la gestión de dos tipos de clientes diferenciados (médicos y pacientes), mantener un catálogo farmacéutico con control básico de inventario, y generar reportes gerenciales para la toma de decisiones comerciales.

### Módulos del MVP

| # | Módulo | Descripción |
|---|--------|-------------|
| 0 | Autenticación y Roles | Login, sesiones JWT, recuperación de contraseña, gestión de usuarios |
| 1 | Gestión de Clientes | Perfiles diferenciados para Médicos y Pacientes con historial de interacciones |
| 2 | Gestión de Productos | Catálogo farmacéutico con stock, alertas y trazabilidad de ajustes |
| 3 | Reportes y Dashboard | KPIs en tiempo real, reportes filtrados, exportación PDF y Excel |

### Exclusiones del MVP (fuera de alcance)

- Punto de venta (POS)
- Portal externo para clientes o pacientes
- Facturación electrónica
- Integración con RIPS o historia clínica oficial
- Módulo de visitas médicas con seguimiento de oportunidades comerciales
- App móvil nativa

---

## 2. Actores del Sistema

### Usuarios internos (acceden al sistema)

| Actor | Descripción | Permisos |
|-------|-------------|----------|
| **Administrador** | Control total del sistema | Todos los módulos + gestión de usuarios |
| **Vendedor** | Gestión comercial diaria | Clientes, productos, interacciones, reportes |
| **Operativo** | Consulta y registro básico | Solo consulta + registro de interacciones |

### Entidades externas (registros en el sistema, no acceden)

| Entidad | Descripción |
|---------|-------------|
| **Médico** | Profesional de salud con datos RETHUS, tarjeta profesional y licencia |
| **Paciente** | Cliente final con historial, médico tratante opcional, datos clínicos generales |

### Actor secundario

| Actor | Descripción |
|-------|-------------|
| **Sistema** | Ejecuta procesos automáticos: alertas de stock bajo, expiración de sesión, logs de auditoría |

---

## 3. Requerimientos Funcionales

### Módulo 0 — Autenticación y Roles

| ID | Requerimiento |
|----|---------------|
| RF-01 | El sistema debe permitir inicio de sesión con correo electrónico y contraseña |
| RF-02 | El sistema debe gestionar tres roles: Administrador, Vendedor, Operativo |
| RF-03 | El sistema debe restringir el acceso a módulos según el rol del usuario autenticado |
| RF-04 | El sistema debe permitir al Administrador crear, editar y desactivar usuarios |
| RF-05 | El sistema debe registrar un log de auditoría con fecha, usuario y acción realizada |
| RF-06 | El sistema debe permitir recuperar la contraseña mediante correo electrónico |

### Módulo 1 — Gestión de Clientes

| ID | Requerimiento |
|----|---------------|
| RF-07 | El sistema debe permitir registrar médicos con: nombre, especialidad, consultorio, teléfono, correo, ciudad, registro RETHUS y tarjeta profesional |
| RF-08 | El sistema debe permitir registrar pacientes con: nombre, documento, fecha de nacimiento, teléfono, correo, EPS y médico tratante |
| RF-09 | El sistema debe permitir buscar clientes por nombre, documento o ciudad |
| RF-10 | El sistema debe permitir editar y desactivar registros de clientes |
| RF-11 | El sistema debe mostrar el perfil completo de un cliente con su historial de interacciones |
| RF-12 | El sistema debe permitir registrar interacciones con clientes (Llamada, Visita, Correo, WhatsApp) con fecha, tipo y descripción |

### Módulo 2 — Gestión de Productos

| ID | Requerimiento |
|----|---------------|
| RF-13 | El sistema debe permitir registrar productos con: nombre comercial, principio activo, laboratorio, categoría, forma farmacéutica, código INVIMA, precio y stock |
| RF-14 | El sistema debe permitir actualizar el stock de un producto manualmente con registro del motivo |
| RF-15 | El sistema debe generar alertas cuando el stock esté por debajo del umbral configurable por producto |
| RF-16 | El sistema debe permitir buscar productos por nombre, categoría o laboratorio |
| RF-17 | El sistema debe permitir activar o desactivar productos del catálogo |

### Módulo 3 — Reportes y Dashboard

| ID | Requerimiento |
|----|---------------|
| RF-18 | El sistema debe mostrar un dashboard con métricas en tiempo real: total de clientes activos, productos con stock bajo, interacciones del mes |
| RF-19 | El sistema debe generar un reporte de clientes registrados con filtros por tipo y fecha |
| RF-20 | El sistema debe generar un reporte de productos con stock actual y alertas activas |
| RF-21 | El sistema debe permitir exportar reportes en formato PDF y Excel |

---

## 4. Requerimientos No Funcionales

Clasificados según el estándar **ISO/IEC 25010**.

| ID | Categoría | Requerimiento |
|----|-----------|---------------|
| RNF-01 | Seguridad | Las contraseñas deben almacenarse con hash **bcrypt cost factor 12**, nunca en texto plano |
| RNF-02 | Seguridad | Las sesiones JWT expiran a las **8 horas**. La inactividad de 30 minutos cierra la sesión en el frontend |
| RNF-03 | Seguridad | El sistema debe usar **HTTPS** en producción |
| RNF-04 | Rendimiento | Las consultas de búsqueda deben responder en menos de **2 segundos** con hasta 10.000 registros |
| RNF-05 | Usabilidad | La interfaz debe ser **responsiva**: mobile, tablet y desktop |
| RNF-06 | Usabilidad | El sistema debe mostrar **mensajes de error claros y accionables** al usuario |
| RNF-07 | Disponibilidad | Disponibilidad mínima del **95%** en horario laboral |
| RNF-08 | Mantenibilidad | Código con convenciones de nomenclatura consistentes y comentarios en funciones críticas |
| RNF-09 | Portabilidad | Funcionar en **Chrome, Firefox y Edge** en versiones actuales |
| RNF-10 | Escalabilidad | La arquitectura debe permitir agregar nuevos módulos sin reestructurar el núcleo |

---

## 5. Reglas de Negocio

Las reglas de negocio son restricciones del dominio real que el sistema debe respetar obligatoriamente. Se implementan en la **capa de servicios** del backend.

### Autenticación y Roles

| ID | Regla |
|----|-------|
| RN-01 | Un usuario solo puede tener un rol activo a la vez |
| RN-02 | Solo el Administrador puede crear, editar o desactivar usuarios |
| RN-03 | Un usuario desactivado no puede iniciar sesión, pero sus registros se conservan |
| RN-04 | El correo electrónico de un usuario debe ser único en el sistema |
| RN-05 | La contraseña debe tener mínimo 8 caracteres, al menos una mayúscula y un número |

### Gestión de Clientes

| ID | Regla |
|----|-------|
| RN-06 | El número de documento de un paciente debe ser **único** en el sistema |
| RN-07 | Un paciente puede tener asociado **máximo un médico tratante** a la vez |
| RN-08 | Un médico con pacientes activos asociados **solo puede desactivarse**, no eliminarse |
| RN-09 | Un paciente con interacciones registradas **solo puede desactivarse**, no eliminarse |
| RN-10 | Toda interacción debe tener obligatoriamente: tipo, fecha, usuario que la registró y descripción |
| RN-11 | Una interacción guardada es **inmutable** — no puede editarse ni eliminarse. Solo se pueden agregar notas aclaratorias posteriores |

### Gestión de Productos

| ID | Regla |
|----|-------|
| RN-12 | El stock de un producto **nunca puede ser negativo** (`CHECK stock_actual >= 0`) |
| RN-13 | El umbral de alerta de stock debe ser un entero mayor a cero, configurable por producto |
| RN-14 | Un producto **no puede eliminarse** del sistema, solo desactivarse |
| RN-15 | El nombre comercial del producto debe ser **único** en el catálogo |
| RN-16 | Todo ajuste de stock debe registrar: cantidad anterior, cantidad nueva, motivo y usuario |

### Reportes

| ID | Regla |
|----|-------|
| RN-17 | Solo Administrador y Vendedor pueden acceder al módulo de reportes |
| RN-18 | Los reportes exportados deben incluir: fecha de generación, usuario que lo generó y filtros aplicados |
| RN-19 | El dashboard refleja datos en **tiempo real**, no datos cacheados |

---

## 6. Casos de Uso

### Matriz de acceso por rol

| ID | Caso de Uso | Módulo | Admin | Vendedor | Operativo |
|----|-------------|--------|-------|----------|-----------|
| UC-01 | Iniciar sesión | Auth | ✓ | ✓ | ✓ |
| UC-02 | Cerrar sesión | Auth | ✓ | ✓ | ✓ |
| UC-03 | Recuperar contraseña | Auth | ✓ | ✓ | ✓ |
| UC-04 | Gestionar usuarios | Auth | ✓ | — | — |
| UC-05 | Registrar médico | Clientes | ✓ | ✓ | — |
| UC-06 | Registrar paciente | Clientes | ✓ | ✓ | — |
| UC-07 | Buscar cliente | Clientes | ✓ | ✓ | ✓ |
| UC-08 | Editar cliente | Clientes | ✓ | ✓ | — |
| UC-09 | Desactivar cliente | Clientes | ✓ | ✓ | — |
| UC-10 | Registrar interacción | Clientes | ✓ | ✓ | ✓ |
| UC-11 | Registrar producto | Productos | ✓ | ✓ | — |
| UC-12 | Actualizar stock | Productos | ✓ | ✓ | — |
| UC-13 | Buscar producto | Productos | ✓ | ✓ | ✓ |
| UC-14 | Activar/Desactivar producto | Productos | ✓ | ✓ | — |
| UC-15 | Alerta stock bajo (automático) | Productos | Auto | Auto | — |
| UC-16 | Ver dashboard | Reportes | ✓ | ✓ | — |
| UC-17 | Generar reporte clientes | Reportes | ✓ | ✓ | — |
| UC-18 | Generar reporte productos | Reportes | ✓ | ✓ | — |
| UC-19 | Exportar reporte | Reportes | ✓ | ✓ | — |

---

## 7. Historias de Usuario y Criterios de Aceptación

Formato: `Como [rol], quiero [acción], para [beneficio]`  
Criterios en formato **Given/When/Then** (BDD — Behavior Driven Development)

---

### HU-01 — Iniciar sesión

**Como** usuario del sistema, **quiero** iniciar sesión con mi correo y contraseña, **para** acceder a las funcionalidades según mi rol.

**Escenario 1 — Login exitoso**
```
Given que soy un usuario activo con credenciales válidas
When ingreso mi correo y contraseña correctos
Then el sistema genera un JWT, lo devuelve y redirige al dashboard
And registra el evento en logs_auditoria
```

**Escenario 2 — Credenciales incorrectas**
```
Given que intento iniciar sesión
When ingreso una contraseña incorrecta
Then el sistema muestra "Correo o contraseña incorrectos"
And NO especifica cuál de los dos es incorrecto (seguridad)
```

**Escenario 3 — Usuario desactivado**
```
Given que mi cuenta fue desactivada por el Administrador
When intento iniciar sesión con mis credenciales
Then el sistema muestra "Tu cuenta está inactiva. Contacta al administrador."
```

**Escenario 4 — Inactividad de sesión**
```
Given que tengo sesión activa
When no realizo ninguna acción durante 30 minutos
Then el frontend invalida el token localmente
And redirige al login con mensaje "Sesión expirada por inactividad"
```

---

### HU-02 — Recuperar contraseña

**Como** usuario, **quiero** recuperar mi contraseña por correo, **para** poder acceder si la olvidé.

**Escenario 1 — Correo registrado**
```
Given que ingreso un correo que existe en el sistema
When solicito recuperación de contraseña
Then el sistema envía un correo con enlace de recuperación
And el enlace expira en 30 minutos
```

**Escenario 2 — Correo no registrado**
```
Given que ingreso un correo que NO existe
When solicito recuperación
Then el sistema muestra el mismo mensaje de éxito (no revela si el correo existe)
```

---

### HU-03 — Gestionar usuarios (Admin)

**Como** Administrador, **quiero** crear y gestionar usuarios del sistema, **para** controlar quién accede y con qué permisos.

**Escenario 1 — Crear usuario exitoso**
```
Given que soy Administrador
When creo un usuario con correo único, nombre y rol
Then el sistema crea el usuario con estado activo
And envía correo con credenciales temporales
And registra la acción en logs_auditoria
```

**Escenario 2 — Correo duplicado**
```
Given que intento crear un usuario
When ingreso un correo que ya existe en el sistema
Then el sistema muestra "Este correo ya está registrado"
And no crea el registro
```

---

### HU-04 — Registrar médico

**Como** Vendedor, **quiero** registrar médicos con su información profesional, **para** mantener un directorio actualizado de clientes.

**Escenario 1 — Registro exitoso**
```
Given que soy Vendedor o Administrador
When completo los campos obligatorios: nombre, especialidad, registro médico, tarjeta profesional y ciudad
Then el sistema crea el registro del médico con estado activo
And registra la acción en logs_auditoria
```

**Escenario 2 — Registro médico duplicado**
```
Given que intento registrar un médico
When ingreso un número de registro médico (RETHUS) que ya existe
Then el sistema muestra "Este número de registro médico ya está registrado"
And no crea el registro
```

---

### HU-05 — Registrar paciente

**Como** Vendedor, **quiero** registrar pacientes con sus datos personales y médico tratante, **para** gestionar la relación con clientes finales.

**Escenario 1 — Registro exitoso**
```
Given que soy Vendedor o Administrador
When completo los campos obligatorios: nombre, tipo documento, número documento, fecha nacimiento, teléfono y ciudad
Then el sistema crea el registro del paciente con estado activo
```

**Escenario 2 — Documento duplicado (RN-06)**
```
Given que intento registrar un paciente
When ingreso un número de documento que ya existe
Then el sistema muestra "Ya existe un paciente registrado con este documento"
And no crea el registro
```

**Escenario 3 — Asignar médico tratante**
```
Given que registro un paciente
When selecciono un médico tratante del dropdown de búsqueda
Then el sistema vincula el paciente al médico seleccionado
And el médico aparece en el perfil del paciente
```

---

### HU-06 — Buscar clientes

**Como** usuario del sistema, **quiero** buscar clientes por nombre, documento o ciudad, **para** encontrar información rápidamente.

**Escenario 1 — Búsqueda con resultados**
```
Given que estoy en el listado de clientes
When ingreso un término de búsqueda con al menos 2 caracteres
Then el sistema filtra los resultados en tiempo real
And muestra nombre, especialidad/EPS, ciudad y estado de cada cliente
```

**Escenario 2 — Sin resultados**
```
Given que busco un término
When no hay coincidencias
Then el sistema muestra "No se encontraron clientes con ese criterio"
```

---

### HU-07 — Desactivar cliente (RN-08 y RN-09)

**Como** Vendedor, **quiero** desactivar clientes inactivos, **para** mantener el directorio limpio sin perder historial.

**Escenario 1 — Médico con pacientes activos**
```
Given que intento desactivar un médico
When tiene pacientes activos asociados
Then el sistema permite la desactivación con advertencia
And los pacientes quedan sin médico tratante (medico_id = NULL)
```

**Escenario 2 — Desactivación exitosa**
```
Given que desactivo un cliente sin dependencias críticas
When confirmo la acción en el modal de confirmación
Then el cliente queda con estado inactivo
And sus interacciones y datos históricos se conservan
```

---

### HU-08 — Registrar interacción (RN-10 y RN-11)

**Como** usuario Vendedor u Operativo, **quiero** registrar interacciones con clientes, **para** mantener un historial confiable de la relación comercial.

**Escenario 1 — Registro exitoso**
```
Given que estoy en el perfil de un cliente
When completo: tipo de interacción, fecha, asunto y descripción
Then el sistema guarda la interacción como registro inmutable
And aparece en el historial del cliente con el nombre del usuario que la registró
```

**Escenario 2 — Intento de edición (RN-11)**
```
Given que existe una interacción registrada
When intento modificarla directamente
Then el sistema no ofrece opción de editar la interacción
And permite agregar una nota aclaratoria adicional
```

---

### HU-09 — Registrar producto

**Como** Vendedor, **quiero** registrar productos en el catálogo, **para** tener un inventario actualizado y controlado.

**Escenario 1 — Registro exitoso**
```
Given que soy Vendedor o Administrador
When completo: nombre comercial, principio activo, laboratorio, categoría, forma farmacéutica, precio compra, precio venta, stock inicial y stock mínimo
Then el sistema crea el producto en el catálogo con estado activo
And si el stock inicial <= stock mínimo, genera una alerta inmediata
```

**Escenario 2 — Nombre comercial duplicado (RN-15)**
```
Given que intento registrar un producto
When ingreso un nombre comercial que ya existe
Then el sistema muestra "Ya existe un producto con ese nombre comercial"
```

---

### HU-10 — Actualizar stock (RN-12, RN-16)

**Como** Vendedor, **quiero** ajustar el stock de un producto con registro del motivo, **para** mantener trazabilidad completa del inventario.

**Escenario 1 — Ajuste exitoso**
```
Given que estoy en la ficha de un producto
When ingreso nueva cantidad, tipo de ajuste (Entrada/Salida/Corrección) y motivo obligatorio
Then el sistema actualiza el stock_actual del producto
And crea un registro en ajustes_stock con: stock_anterior, stock_nuevo, variación, motivo y usuario
```

**Escenario 2 — Stock negativo rechazado (RN-12)**
```
Given que intento hacer un ajuste de salida
When la nueva cantidad resultante sería negativa
Then el sistema muestra "El stock no puede ser negativo"
And no procesa el ajuste
```

**Escenario 3 — Alerta post-ajuste**
```
Given que realizo un ajuste de salida válido
When el stock_actual resultante es <= stock_minimo
Then el sistema muestra badge de alerta "Stock bajo" en la ficha del producto
And el producto aparece en la lista de alertas del dashboard
```

**Escenario 4 — Motivo obligatorio**
```
Given que intento guardar un ajuste
When el campo motivo está vacío
Then el sistema muestra "El motivo del ajuste es obligatorio"
And no procesa el ajuste
```

---

### HU-11 — Ver dashboard (RF-18, RN-19)

**Como** Administrador o Vendedor, **quiero** ver un dashboard con KPIs en tiempo real, **para** tomar decisiones informadas sin necesidad de generar reportes.

**Escenario 1 — Carga del dashboard**
```
Given que accedo al módulo de dashboard
When la página carga
Then el sistema muestra en tiempo real:
  - Total de médicos activos
  - Total de pacientes activos
  - Número de productos con stock bajo
  - Total de interacciones del mes actual
And muestra las últimas 5 interacciones registradas
And muestra la lista de productos con alerta de stock activa
```

---

### HU-12 — Generar y exportar reporte

**Como** Administrador o Vendedor, **quiero** generar reportes filtrados y exportarlos, **para** compartir información con la gerencia.

**Escenario 1 — Reporte de clientes con filtros**
```
Given que accedo al módulo de reportes
When aplico filtros: tipo cliente (Médico/Paciente), estado (activo/inactivo), rango de fechas
Then el sistema muestra la tabla filtrada con el conteo de resultados
And habilita los botones "Exportar PDF" y "Exportar Excel"
```

**Escenario 2 — Exportación**
```
Given que hay resultados en la tabla
When hago clic en "Exportar PDF"
Then el sistema genera el archivo con: fecha de generación, usuario, filtros aplicados y datos de la tabla
And descarga el archivo automáticamente
```

---

## 8. Modelo de Datos

### Principios de Normalización

El modelo cumple las tres primeras formas normales (**3FN**):

- **1FN:** Valores atómicos — nombres y apellidos en campos separados, géneros como ENUM
- **2FN:** Dependencia total de la PK — todas las tablas tienen PK simple (`INT AUTO_INCREMENT`)
- **3FN:** Sin dependencias transitivas — laboratorios, ciudades, especialidades son tablas independientes

### Catálogo de las 16 Tablas

#### Tablas de Catálogo (sin dependencias externas)

| Tabla | Propósito |
|-------|-----------|
| `roles` | Roles del sistema: Administrador, Vendedor, Operativo |
| `departamentos` | Departamentos de Colombia (código DANE) |
| `ciudades` | Ciudades vinculadas a departamento |
| `especialidades` | Especialidades médicas con código RETHUS |
| `tipos_documento_paciente` | CC, TI, RC, CE, PA, NV |
| `tipos_interaccion` | Llamada, Visita, Correo, WhatsApp |
| `categorias_producto` | Categorías farmacológicas |
| `laboratorios` | Laboratorios fabricantes con datos de contacto |

#### Tablas Principales

| Tabla | Campos críticos |
|-------|-----------------|
| `usuarios` | `rol_id FK`, `correo UNIQUE`, `contrasena_hash`, `activo`, `creado_por FK` |
| `medicos` | `especialidad_id FK`, `ciudad_id FK`, `numero_documento UNIQUE`, `registro_medico UNIQUE`, `tarjeta_profesional UNIQUE`, `licencia_vigente`, `fecha_vencimiento_licencia` |
| `pacientes` | `medico_id FK NULL`, `ciudad_id FK`, `tipo_doc_id FK`, `numero_documento UNIQUE`, `condicion_medica_general`, `alergias_conocidas` |
| `productos` | `categoria_id FK`, `laboratorio_id FK`, `nombre_comercial UNIQUE`, `codigo_invima UNIQUE`, `stock_actual CHECK >= 0`, `stock_minimo CHECK > 0`, `requiere_formula` |

#### Tablas de Soporte (inmutables por diseño)

| Tabla | Nota de diseño |
|-------|----------------|
| `interacciones` | **Sin `actualizado_en`** — inmutable por RN-11. Columnas: `tipo_interaccion_id FK`, `usuario_id FK`, `medico_id FK NULL`, `paciente_id FK NULL` |
| `notas_interaccion` | Permite aclaraciones sin modificar la interacción original — `interaccion_id FK` |
| `ajustes_stock` | **Sin `actualizado_en`** — inmutable por RN-16. Columnas: `stock_anterior`, `stock_nuevo`, `variacion`, `tipo_ajuste ENUM`, `motivo NOT NULL` |
| `logs_auditoria` | **Sin `actualizado_en`** — registro permanente. `usuario_id FK NULL` (permite logs antes del login) |

### Índices Definidos (RNF-04)

Se definen **21 índices** sobre las columnas más consultadas:

```sql
-- Médicos
idx_medicos_nombres, idx_medicos_ciudad, idx_medicos_especialidad, idx_medicos_activo, idx_medicos_licencia

-- Pacientes
idx_pacientes_nombres, idx_pacientes_ciudad, idx_pacientes_medico, idx_pacientes_activo

-- Productos
idx_productos_nombre, idx_productos_categoria, idx_productos_laboratorio, idx_productos_activo, idx_productos_stock_alerta

-- Interacciones
idx_interacciones_medico, idx_interacciones_paciente, idx_interacciones_usuario, idx_interacciones_fecha

-- Ajustes
idx_ajustes_producto

-- Logs
idx_logs_usuario, idx_logs_modulo
```

### Vistas SQL Definidas

| Vista | Propósito |
|-------|-----------|
| `v_productos_stock_bajo` | Lista directa de productos con alerta activa para el dashboard |
| `v_medicos_licencia_alerta` | Médicos con licencia vencida o por vencer en 90 días |
| `v_dashboard_metricas` | Una sola query devuelve todos los KPIs del dashboard |
| `v_interacciones_completo` | Historial con nombres de cliente y usuario ya resueltos (sin JOINs adicionales) |

### Nota crítica sobre MySQL 8 y CHECK constraints

MySQL 8 **no permite** usar en un `CHECK` constraint una columna que simultáneamente participa en una FK con acciones referenciales (`ON DELETE`/`ON UPDATE`). Esta restricción afecta la validación de negocio `"medico_id IS NOT NULL OR paciente_id IS NOT NULL"` en la tabla `interacciones`.

**Solución implementada:** esa validación **vive en `interacciones.service.js`**, no en la base de datos:

```javascript
// interacciones.service.js
if (!datos.medico_id && !datos.paciente_id) {
  throw new Error('La interacción debe estar asociada a un médico o paciente');
}
```

---

## 9. Arquitectura del Sistema

### Patrón: Monolito en Capas (MVC extendido)

**Justificación:** Empresa pequeña, baja concurrencia, equipo de desarrollo de una persona, 17 días disponibles. Los microservicios o arquitecturas serverless serían sobre-ingeniería para este contexto.

### Las 5 Capas

```
┌─────────────────────────────────────────┐
│         CAPA DE PRESENTACIÓN            │
│   React 18 — Componentes por módulo     │
│   CSS puro — Sistema de diseño propio   │
└────────────────┬────────────────────────┘
                 │ HTTP/HTTPS — JSON — JWT
┌────────────────▼────────────────────────┐
│         CAPA DE CONTROLADORES           │
│   Express 4 — Recibe requests HTTP      │
│   Valida inputs — Delega a servicios    │
└────────────────┬────────────────────────┘
                 │
┌────────────────▼────────────────────────┐
│      CAPA DE SERVICIOS / DOMINIO        │
│   Lógica de negocio — Reglas de negocio │
│   Validaciones de dominio (RN-01..19)   │
└────────────────┬────────────────────────┘
                 │
┌────────────────▼────────────────────────┐
│         CAPA DE REPOSITORIOS            │
│   SQL puro con mysql2                   │
│   Prepared Statements obligatorios      │
│   Única capa que toca la BD             │
└────────────────┬────────────────────────┘
                 │
┌────────────────▼────────────────────────┐
│           BASE DE DATOS                 │
│   MySQL 8 — 16 tablas en 3FN            │
└─────────────────────────────────────────┘
```

### Seguridad por capas (Defense in Depth)

| Capa | Medida | Implementación |
|------|--------|----------------|
| Transporte | HTTPS | Obligatorio en producción |
| Autenticación | JWT con expiración 8h | `jsonwebtoken` — stateless |
| Contraseñas | Hash bcrypt cost 12 | `bcrypt` — nunca texto plano |
| Sesión frontend | 30 min inactividad | Interceptor axios + timer |
| Autorización | Middleware por ruta | `roleMiddleware` verifica rol antes de ejecutar |
| Queries | Prepared Statements | `mysql2` — previene SQL injection al 100% |
| Auditoría | Log inmutable | Tabla `logs_auditoria` — RF-05 |

### Middlewares del servidor

```
authMiddleware    → verifica que el JWT sea válido y no haya expirado
roleMiddleware    → verifica que el rol del usuario tenga acceso a esa ruta
errorHandler      → captura errores globales y devuelve respuesta JSON estandarizada
morgan            → logger de requests HTTP en desarrollo
helmet            → headers de seguridad HTTP automáticos
cors              → permite peticiones desde el frontend React
```

---

## 10. Stack Tecnológico

### Decisiones definitivas (confirmadas)

| Capa | Tecnología | Versión | Justificación |
|------|-----------|---------|---------------|
| Frontend | **React** | 18.x | Más demandado laboralmente. Componentes reutilizables por módulo |
| Estilos | **CSS puro + Variables** | CSS3 | Sistema de diseño ya definido con tokens. Sin dependencias adicionales |
| Backend | **Node.js** | 20.x LTS | Un solo lenguaje en todo el stack (JavaScript fullstack) |
| Framework API | **Express** | 4.x | Minimalista. Máximo control sobre la arquitectura en capas |
| Autenticación | **JWT** (`jsonwebtoken`) | 9.x | Stateless — estándar para APIs REST modernas |
| Base de datos | **MySQL** | 8.x | El más documentado en Colombia. Soporta todas las restricciones del modelo |
| Driver BD | **mysql2** | 3.x | Prepared Statements nativos — **sin ORM, SQL puro** |
| Hashing | **bcrypt** | 5.x | Estándar para contraseñas — RNF-01 |

> **Decisión importante:** Se usa **SQL puro con mysql2** — sin ORM (no Sequelize, no Prisma, no TypeORM). Cada repositorio escribe sus propias queries con Prepared Statements. La carpeta `database/schema.sql` es la **fuente de verdad absoluta** del modelo de datos.

### Librerías de backend

| Librería | Uso |
|----------|-----|
| `dotenv` | Variables de entorno (DB credentials, JWT secret, puerto) |
| `cors` | Peticiones cross-origin desde React |
| `express-validator` | Validación de inputs en endpoints |
| `morgan` | Logger de requests HTTP |
| `helmet` | Headers de seguridad automáticos |
| `nodemon` | Reinicio automático en desarrollo |

### Librerías de frontend

| Librería | Uso |
|----------|-----|
| `react-router-dom` v6 | Navegación con rutas protegidas por rol |
| `axios` | Cliente HTTP con interceptores para JWT |
| `lucide-react` | Iconografía del sistema de diseño |
| `react-hot-toast` | Toasts de notificación |
| `jspdf` | Generación de PDFs para exportación |
| `xlsx` | Generación de Excel para exportación |

---

## 11. Estructura de Carpetas

```
crm-farmacia/
│
├── client/                              # Frontend React
│   ├── public/
│   │   └── index.html
│   └── src/
│       ├── assets/
│       │   └── css/
│       │       ├── variables.css        # Tokens del sistema de diseño
│       │       ├── global.css           # Reset + estilos base
│       │       └── components.css       # Botones, inputs, badges, etc.
│       │
│       ├── components/
│       │   ├── layout/
│       │   │   ├── Sidebar.jsx
│       │   │   ├── Topbar.jsx
│       │   │   └── Layout.jsx
│       │   ├── ui/
│       │   │   ├── Button.jsx
│       │   │   ├── Input.jsx
│       │   │   ├── Badge.jsx
│       │   │   ├── Card.jsx
│       │   │   ├── Table.jsx
│       │   │   ├── Modal.jsx
│       │   │   └── Toast.jsx
│       │   └── shared/
│       │       ├── SearchBar.jsx
│       │       ├── Pagination.jsx
│       │       └── InteractionHistory.jsx
│       │
│       ├── pages/
│       │   ├── auth/
│       │   │   ├── Login.jsx
│       │   │   └── RecuperarPassword.jsx
│       │   ├── dashboard/
│       │   │   └── Dashboard.jsx
│       │   ├── medicos/
│       │   │   ├── ListadoMedicos.jsx
│       │   │   ├── PerfilMedico.jsx
│       │   │   └── FormMedico.jsx
│       │   ├── pacientes/
│       │   │   ├── ListadoPacientes.jsx
│       │   │   ├── PerfilPaciente.jsx
│       │   │   └── FormPaciente.jsx
│       │   ├── productos/
│       │   │   ├── CatalogoProductos.jsx
│       │   │   ├── FichaProducto.jsx
│       │   │   └── FormProducto.jsx
│       │   ├── reportes/
│       │   │   ├── ReporteClientes.jsx
│       │   │   └── ReporteProductos.jsx
│       │   └── admin/
│       │       └── GestionUsuarios.jsx
│       │
│       ├── context/
│       │   └── AuthContext.jsx          # Usuario logueado + rol (estado global)
│       │
│       ├── hooks/
│       │   ├── useAuth.js               # Hook para acceder al contexto de auth
│       │   └── useFetch.js              # Hook genérico para llamadas a la API
│       │
│       ├── services/                    # Llamadas HTTP a la API
│       │   ├── auth.service.js
│       │   ├── medicos.service.js
│       │   ├── pacientes.service.js
│       │   ├── productos.service.js
│       │   └── reportes.service.js
│       │
│       ├── utils/
│       │   ├── formatters.js            # Fechas, moneda COP, documentos colombianos
│       │   └── validators.js            # Validaciones del frontend
│       │
│       ├── router/
│       │   └── AppRouter.jsx            # Rutas protegidas por rol
│       │
│       └── App.jsx
│
├── server/                              # Backend Node.js + Express
│   ├── src/
│   │   ├── config/
│   │   │   ├── database.js              # Pool de conexiones mysql2
│   │   │   └── env.js                   # Carga y valida variables de entorno
│   │   │
│   │   ├── controllers/                 # Reciben requests, validan, delegan
│   │   │   ├── auth.controller.js
│   │   │   ├── medicos.controller.js
│   │   │   ├── pacientes.controller.js
│   │   │   ├── productos.controller.js
│   │   │   ├── reportes.controller.js
│   │   │   └── usuarios.controller.js
│   │   │
│   │   ├── services/                    # Lógica de negocio y reglas RN-01..19
│   │   │   ├── auth.service.js
│   │   │   ├── medicos.service.js
│   │   │   ├── pacientes.service.js
│   │   │   ├── productos.service.js
│   │   │   ├── reportes.service.js
│   │   │   └── audit.service.js         # Servicio transversal de auditoría
│   │   │
│   │   ├── repositories/                # SQL puro con mysql2 — única capa que toca la BD
│   │   │   ├── medicos.repository.js
│   │   │   ├── pacientes.repository.js
│   │   │   ├── productos.repository.js
│   │   │   ├── usuarios.repository.js
│   │   │   ├── interacciones.repository.js
│   │   │   └── logs.repository.js
│   │   │
│   │   ├── middlewares/
│   │   │   ├── auth.middleware.js        # Verifica JWT en cada request protegido
│   │   │   ├── role.middleware.js        # Verifica que el rol tenga acceso a la ruta
│   │   │   └── error.middleware.js       # Manejo global de errores — respuesta JSON
│   │   │
│   │   ├── routes/
│   │   │   ├── auth.routes.js
│   │   │   ├── medicos.routes.js
│   │   │   ├── pacientes.routes.js
│   │   │   ├── productos.routes.js
│   │   │   ├── reportes.routes.js
│   │   │   └── usuarios.routes.js
│   │   │
│   │   ├── utils/
│   │   │   └── response.helper.js       # Formato estándar de respuestas JSON
│   │   │
│   │   └── app.js                       # Configuración Express — middlewares globales
│   │
│   ├── .env.example
│   └── package.json
│
└── database/                            # Fuente de verdad del modelo de datos
    ├── crm_farma.sql                    # Script completo: schema + seeders
    ├── seeders/
    │   ├── 01_roles.sql
    │   ├── 02_especialidades.sql
    │   ├── 03_departamentos_ciudades.sql
    │   ├── 04_catalogos.sql
    │   └── 05_usuario_admin.sql
    └── migrations/
        └── 001_initial_schema.sql
```

---

## 12. API REST — Endpoints

### Formato de respuesta estándar

Todas las respuestas de la API siguen este formato:

```json
// Éxito
{
  "success": true,
  "data": { },
  "message": "Operación exitosa"
}

// Error
{
  "success": false,
  "error": "Mensaje descriptivo del error",
  "code": "ERROR_CODE"
}
```

### Endpoints por módulo

#### Autenticación (público)

| Método | Endpoint | Descripción | Rol mínimo |
|--------|----------|-------------|------------|
| `POST` | `/api/auth/login` | Iniciar sesión | Público |
| `POST` | `/api/auth/logout` | Cerrar sesión | Autenticado |
| `POST` | `/api/auth/recuperar` | Solicitar recuperación de contraseña | Público |
| `POST` | `/api/auth/reset-password` | Establecer nueva contraseña con token | Público |

#### Usuarios (solo Admin)

| Método | Endpoint | Descripción | Rol mínimo |
|--------|----------|-------------|------------|
| `GET` | `/api/usuarios` | Listar usuarios | Admin |
| `POST` | `/api/usuarios` | Crear usuario | Admin |
| `PUT` | `/api/usuarios/:id` | Editar usuario | Admin |
| `PATCH` | `/api/usuarios/:id/estado` | Activar / Desactivar | Admin |

#### Médicos

| Método | Endpoint | Descripción | Rol mínimo |
|--------|----------|-------------|------------|
| `GET` | `/api/medicos` | Listar / buscar médicos (`?search=&ciudad=&especialidad=`) | Operativo |
| `POST` | `/api/medicos` | Registrar médico | Vendedor |
| `GET` | `/api/medicos/:id` | Ver perfil completo | Operativo |
| `PUT` | `/api/medicos/:id` | Editar médico | Vendedor |
| `PATCH` | `/api/medicos/:id/estado` | Activar / Desactivar | Vendedor |

#### Pacientes

| Método | Endpoint | Descripción | Rol mínimo |
|--------|----------|-------------|------------|
| `GET` | `/api/pacientes` | Listar / buscar pacientes | Operativo |
| `POST` | `/api/pacientes` | Registrar paciente | Vendedor |
| `GET` | `/api/pacientes/:id` | Ver perfil completo | Operativo |
| `PUT` | `/api/pacientes/:id` | Editar paciente | Vendedor |
| `PATCH` | `/api/pacientes/:id/estado` | Activar / Desactivar | Vendedor |

#### Interacciones

| Método | Endpoint | Descripción | Rol mínimo |
|--------|----------|-------------|------------|
| `GET` | `/api/clientes/:tipo/:id/interacciones` | Historial del cliente | Operativo |
| `POST` | `/api/clientes/:tipo/:id/interacciones` | Registrar interacción | Operativo |
| `POST` | `/api/interacciones/:id/notas` | Agregar nota aclaratoria | Operativo |

#### Productos

| Método | Endpoint | Descripción | Rol mínimo |
|--------|----------|-------------|------------|
| `GET` | `/api/productos` | Listar / buscar productos (`?search=&categoria=&laboratorio=&alerta=`) | Operativo |
| `POST` | `/api/productos` | Registrar producto | Vendedor |
| `GET` | `/api/productos/:id` | Ver ficha de producto | Operativo |
| `PUT` | `/api/productos/:id` | Editar producto | Vendedor |
| `PATCH` | `/api/productos/:id/estado` | Activar / Desactivar | Vendedor |
| `POST` | `/api/productos/:id/stock` | Ajustar stock | Vendedor |
| `GET` | `/api/productos/:id/ajustes` | Historial de ajustes | Vendedor |

#### Reportes y Dashboard

| Método | Endpoint | Descripción | Rol mínimo |
|--------|----------|-------------|------------|
| `GET` | `/api/reportes/dashboard` | Métricas KPI en tiempo real | Vendedor |
| `GET` | `/api/reportes/clientes` | Reporte de clientes con filtros | Vendedor |
| `GET` | `/api/reportes/productos` | Reporte de productos con filtros | Vendedor |
| `POST` | `/api/reportes/exportar` | Exportar reporte (PDF o Excel) | Vendedor |

### Ejemplo: Repositorio con SQL puro

```javascript
// medicos.repository.js
const pool = require('../config/database');

const findAll = async ({ search = '', ciudad_id, especialidad_id, activo = 1, limit = 10, offset = 0 }) => {
  const [rows] = await pool.execute(
    `SELECT
       m.medico_id,
       CONCAT(m.nombres, ' ', m.apellidos) AS nombre_completo,
       e.nombre   AS especialidad,
       c.nombre   AS ciudad,
       m.licencia_vigente,
       m.fecha_vencimiento_licencia,
       m.acepta_visitas,
       m.activo
     FROM medicos m
     INNER JOIN especialidades e ON m.especialidad_id = e.especialidad_id
     INNER JOIN ciudades c       ON m.ciudad_id = c.ciudad_id
     WHERE m.activo = ?
       AND (m.nombres LIKE ? OR m.apellidos LIKE ? OR m.numero_documento LIKE ?)
     ORDER BY m.apellidos ASC
     LIMIT ? OFFSET ?`,
    [activo, `%${search}%`, `%${search}%`, `%${search}%`, limit, offset]
  );
  return rows;
};

module.exports = { findAll };
```

---

## 13. Sistema de Diseño UX/UI

### Leyes UX aplicadas

| Ley | Aplicación |
|-----|------------|
| **Ley de Hick** | Máximo 5 ítems en menú principal. Formularios sin campos innecesarios visibles |
| **Ley de Fitts** | Botones primarios mínimo 48px de alto. Posición predecible en cada pantalla |
| **Ley de Jakob** | Sidebar izquierdo, tabla con acciones a la derecha, formularios top-down |
| **Gestalt (Proximidad)** | Campos del formulario agrupados por secciones con separadores visuales |
| **Ley de Miller** | Máximo 10 filas por tabla con paginación |
| **Visibilidad del estado** | Badges de estado, toasts de feedback, spinners de carga |

### Paleta de Colores (tokens CSS)

```css
:root {
  --color-primary:        #1A6B8A;   /* Azul médico — principal */
  --color-primary-dark:   #1A3A4A;   /* Azul noche — sidebar */
  --color-primary-light:  #4A9BB5;   /* Azul medio — hover */
  --color-secondary:      #2A9D8F;   /* Verde salud — ítem activo */
  --color-bg-main:        #F4F9FC;   /* Blanco clínico — fondo */
  --color-bg-card:        #E9F5F8;   /* Azul hielo — fondo cards */
  --color-success:        #27AE60;
  --color-error:          #E74C3C;
  --color-warning:        #F39C12;   /* Alertas de stock bajo */
  --color-info:           #3498DB;
  --color-success-soft:   #EAFAF1;
  --color-error-soft:     #FDEDEC;
  --color-warning-soft:   #FEF9E7;
  --color-text-primary:   #2C3E50;
  --color-text-secondary: #7F8C8D;
  --color-border:         #BDC3C7;
  --color-sidebar-bg:     #1A3A4A;
  --color-sidebar-text:   #ECF0F1;
  --color-sidebar-active: #2A9D8F;
}
```

### Tipografía

- **Principal:** `Inter` (Google Fonts) — pesos 400, 500, 600, 700
- **Monospace:** `JetBrains Mono` — para códigos INVIMA, registros RETHUS, tarjetas profesionales

### Iconografía

**Lucide Icons** exclusivamente. Importar con `lucide-react`.

| Sección | Ícono |
|---------|-------|
| Dashboard | `LayoutDashboard` |
| Médicos | `Stethoscope` |
| Pacientes | `Users` |
| Productos | `Pill` |
| Reportes | `BarChart2` |
| Admin/Usuarios | `ShieldCheck` |
| Alerta stock | `AlertTriangle` |
| Licencia vigente | `BadgeCheck` |
| Licencia vencida | `BadgeX` |

### Layout Responsivo

| Breakpoint | Layout |
|------------|--------|
| Desktop ≥ 1024px | Sidebar fijo 240px + Topbar 64px + contenido flex |
| Tablet 768–1023px | Sidebar colapsable con toggle ☰ |
| Mobile < 768px | Bottom navigation bar + sidebar como drawer |

### Pantallas diseñadas (16 en total)

1. Login
2. Recuperación de contraseña
3. Listado de médicos
4. Perfil de médico (tabs: datos, pacientes, historial)
5. Listado de pacientes
6. Perfil de paciente (card clínica ámbar con alergias)
7. Dashboard con KPIs
8. Catálogo de productos
9. Ficha de producto + formulario ajuste de stock inline
10. Reporte de clientes
11. Reporte de productos
12. Gestión de usuarios (solo Admin)
13. Formulario nuevo médico (4 secciones)
14. Formulario nuevo paciente (3 secciones)
15. Formulario nuevo producto (3 secciones)
16. Modal nueva interacción (chips de tipo + campos de seguimiento)

---

## 14. Plan de Desarrollo

Fecha inicio: 22 febrero 2026 — Fecha límite: 9 marzo 2026

| Días | Bloque | Tareas | Entregable |
|------|--------|--------|------------|
| 1–2 | **Setup** | Crear repos, instalar dependencias, ejecutar `crm_farma.sql`, configurar `.env`, estructura de carpetas | Proyecto corriendo en local |
| 3–4 | **Autenticación** | Endpoints auth, JWT, middlewares `authMiddleware` y `roleMiddleware`, `Login.jsx` | Login funcional completo |
| 5–7 | **Módulo Clientes** | CRUD médicos y pacientes, endpoints REST, vistas React, interacciones | Módulo clientes funcional |
| 8–10 | **Módulo Productos** | CRUD productos, ajuste stock con trazabilidad, alertas, vistas React | Módulo productos funcional |
| 11–12 | **Reportes** | Dashboard KPIs usando `v_dashboard_metricas`, reportes con filtros, exportación PDF/Excel | Módulo reportes funcional |
| 13–14 | **Admin + Auditoría** | Gestión usuarios, logs de auditoría, vistas admin | Módulo admin funcional |
| 15–16 | **Pulido y pruebas** | Corrección de bugs, validaciones faltantes, responsividad, pruebas por rol | MVP estable |
| 17 | **Documentación** | Manual técnico y de usuario, revisión final | Entrega completa |

---

## 15. Base de Datos — Consideraciones de Implementación

### Credenciales de prueba (seeders incluidos)

El script `crm_farma.sql` incluye los siguientes datos de ejemplo:

**Usuario administrador inicial**
```
Correo:     admin@crmfarma.com
Contraseña: Admin2026*
Rol:        Administrador
```
> ⚠️ Cambiar en el primer login. El hash bcrypt incluido corresponde a esa contraseña.

**Otros usuarios de prueba** (misma contraseña: `Admin2026*`)
```
juan.vendedor@crmfarma.com   → Vendedor
maria.vendedor@crmfarma.com  → Vendedor
carlos.operativo@crmfarma.com → Operativo
```

### Variables de entorno requeridas

Crear archivo `server/.env` basado en `server/.env.example`:

```env
# Servidor
PORT=3001
NODE_ENV=development

# Base de datos
DB_HOST=localhost
DB_PORT=3306
DB_USER=root
DB_PASSWORD=tu_password
DB_NAME=crm_farma

# JWT
JWT_SECRET=tu_clave_secreta_muy_larga_y_segura
JWT_EXPIRES_IN=8h

# Correo (recuperación de contraseña)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USER=tu_correo@gmail.com
MAIL_PASS=tu_app_password
```

### Configuración del pool de conexiones

```javascript
// server/src/config/database.js
const mysql = require('mysql2/promise');

const pool = mysql.createPool({
  host:            process.env.DB_HOST,
  port:            process.env.DB_PORT,
  user:            process.env.DB_USER,
  password:        process.env.DB_PASSWORD,
  database:        process.env.DB_NAME,
  waitForConnections: true,
  connectionLimit:    10,
  queueLimit:         0,
  charset:            'utf8mb4',
});

module.exports = pool;
```

### Pasos de setup inicial

```bash
# 1. Clonar repositorio
git clone <repo-url>
cd crm-farmacia

# 2. Instalar dependencias del backend
cd server && npm install

# 3. Instalar dependencias del frontend
cd ../client && npm install

# 4. Configurar variables de entorno
cp server/.env.example server/.env
# Editar server/.env con las credenciales reales

# 5. Ejecutar el script de base de datos
# En MySQL Workbench: abrir y ejecutar database/crm_farma.sql
# O desde terminal:
mysql -u root -p < database/crm_farma.sql

# 6. Iniciar backend (desarrollo)
cd server && npm run dev      # nodemon en puerto 3001

# 7. Iniciar frontend (desarrollo)
cd client && npm start        # React en puerto 3000
```

---

## Glosario Técnico

| Término | Definición |
|---------|------------|
| **JWT** | JSON Web Token. Token firmado que contiene `usuario_id` y `rol`. Stateless — el servidor no guarda estado de sesión |
| **bcrypt** | Algoritmo de hashing con factor de costo configurable. Hace deliberadamente lento el proceso para resistir ataques de fuerza bruta |
| **Prepared Statements** | Mecanismo SQL que separa el código de los datos. Previene inyección SQL al 100% |
| **3FN** | Tercera Forma Normal. Elimina redundancia y dependencias transitivas en el modelo relacional |
| **RETHUS** | Registro Único Nacional del Talento Humano en Salud — sistema colombiano de registro de profesionales |
| **INVIMA** | Instituto Nacional de Vigilancia de Medicamentos y Alimentos — emite registros sanitarios en Colombia |
| **MVC** | Model-View-Controller — patrón que separa lógica de negocio, interfaz y flujo de control |
| **SPA** | Single Page Application — React carga una sola página HTML y actualiza el contenido dinámicamente |
| **Pool de conexiones** | Conjunto de conexiones a la BD reutilizables — evita abrir/cerrar una conexión por cada request |
| **Middleware** | Función que intercepta el request antes de llegar al controlador — autenticación, roles, logging |
| **Immutable record** | Registro que no puede modificarse una vez guardado — `interacciones`, `ajustes_stock`, `logs_auditoria` |

---

*CRM FARMA+ v1.0.0 — Documento generado el 22 de febrero de 2026*  
*Proyecto académico SENA — Etapa de Aprendizaje*
