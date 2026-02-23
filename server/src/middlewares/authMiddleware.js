/**
 * CRM FarmaPlus — Middleware de Autenticación JWT
 * RF-03: Restringe el acceso según rol del usuario autenticado.
 * RNF-02: JWT expira en 8h — sesión por inactividad gestionada en frontend.
 *
 * Verifica que:
 *  1. El header Authorization contiene un Bearer token válido
 *  2. El token no ha expirado
 *  3. Adjunta `req.usuario` con { sub, rol, nombre } para uso posterior
 */

'use strict';

const jwt = require('jsonwebtoken');

/**
 * authMiddleware — Verifica JWT en cada request protegido.
 * Uso: router.get('/ruta', authMiddleware, controlador)
 */
function authMiddleware(req, res, next) {
    const authHeader = req.headers['authorization'];

    if (!authHeader || !authHeader.startsWith('Bearer ')) {
        return res.status(401).json({
            success: false,
            message: 'Acceso denegado. Token no proporcionado.',
            code: 'TOKEN_MISSING',
        });
    }

    const token = authHeader.split(' ')[1];

    try {
        const decoded = jwt.verify(token, process.env.JWT_SECRET);
        req.usuario = decoded; // { sub, rol, nombre, iat, exp }
        next();
    } catch (error) {
        // Los errores JWT son manejados por el errorHandler global
        next(error);
    }
}

module.exports = authMiddleware;
