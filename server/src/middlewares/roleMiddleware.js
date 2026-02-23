/**
 * CRM FarmaPlus — Middleware de Autorización por Rol
 * RF-03: Solo usuarios con el rol correcto pueden ejecutar la acción.
 * UC-04: Solo Administrador gestiona usuarios.
 * UC-16..19: Solo Admin y Vendedor acceden a reportes (RN-17).
 *
 * Uso:
 *   router.post('/ruta', authMiddleware, roleMiddleware('Administrador'), controlador)
 *   router.get('/ruta',  authMiddleware, roleMiddleware('Administrador', 'Vendedor'), controlador)
 */

'use strict';

/**
 * Genera un middleware que valida que el usuario tenga uno de los roles permitidos.
 * @param {...string} rolesPermitidos - Nombres de rol: 'Administrador', 'Vendedor', 'Operativo'
 */
function roleMiddleware(...rolesPermitidos) {
    return (req, res, next) => {
        if (!req.usuario) {
            return res.status(401).json({
                success: false,
                message: 'No autenticado.',
                code: 'UNAUTHENTICATED',
            });
        }

        if (!rolesPermitidos.includes(req.usuario.rol)) {
            return res.status(403).json({
                success: false,
                message: `Acceso denegado. Se requiere rol: ${rolesPermitidos.join(' o ')}.`,
                code: 'FORBIDDEN',
            });
        }

        next();
    };
}

module.exports = roleMiddleware;
