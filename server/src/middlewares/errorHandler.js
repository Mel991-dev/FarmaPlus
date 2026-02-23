/**
 * CRM FarmaPlus — Middleware global de manejo de errores
 * Captura todos los errores no controlados del servidor
 * Devuelve respuesta JSON estandarizada: { success, message, code }
 */

'use strict';

// Códigos de error MySQL más comunes en este sistema
const MYSQL_ERROR_CODES = {
    ER_DUP_ENTRY: 'Ya existe un registro con ese dato único.',
    ER_NO_REFERENCED_ROW_2: 'El registro referenciado no existe.',
    ER_ROW_IS_REFERENCED_2: 'No se puede eliminar: otros registros dependen de este.',
    ER_CHECK_CONSTRAINT_VIOLATED: 'El valor no cumple las restricciones del sistema.',
};

/**
 * errorHandler — Middleware final de Express (4 parámetros obligatorios)
 * Debe registrarse DESPUÉS de todas las rutas en app.js
 */
function errorHandler(err, req, res, next) { // eslint-disable-line no-unused-vars
    // ── 1. Log en desarrollo ─────────────────────────────────────────────────
    if (process.env.NODE_ENV === 'development') {
        console.error(`[ERROR] ${req.method} ${req.originalUrl}`);
        console.error(err);
    }

    // ── 2. Errores de MySQL ──────────────────────────────────────────────────
    if (err.code && MYSQL_ERROR_CODES[err.code]) {
        return res.status(409).json({
            success: false,
            message: MYSQL_ERROR_CODES[err.code],
            code: err.code,
        });
    }

    // ── 3. Errores de JWT ────────────────────────────────────────────────────
    if (err.name === 'JsonWebTokenError') {
        return res.status(401).json({
            success: false,
            message: 'Token inválido. Inicia sesión nuevamente.',
            code: 'JWT_INVALID',
        });
    }

    if (err.name === 'TokenExpiredError') {
        return res.status(401).json({
            success: false,
            message: 'Tu sesión ha expirado. Inicia sesión nuevamente.',
            code: 'JWT_EXPIRED',
        });
    }

    // ── 4. Errores de validación de express-validator ────────────────────────
    if (err.type === 'validation') {
        return res.status(422).json({
            success: false,
            message: 'Datos de entrada inválidos.',
            errors: err.errors,
            code: 'VALIDATION_ERROR',
        });
    }

    // ── 5. Error personalizado con statusCode ────────────────────────────────
    if (err.statusCode) {
        return res.status(err.statusCode).json({
            success: false,
            message: err.message,
            code: err.code || 'APP_ERROR',
        });
    }

    // ── 6. Error genérico del servidor ──────────────────────────────────────
    const response = {
        success: false,
        message: 'Error interno del servidor. Contacta al administrador.',
        code: 'INTERNAL_SERVER_ERROR',
    };

    // En desarrollo, enviamos el error real para depurar sin tener que ver los logs del server
    if (process.env.NODE_ENV === 'development') {
        response.debug = {
            message: err.message,
            stack: err.stack,
            error: err
        };
    }

    return res.status(500).json(response);
}

module.exports = errorHandler;
