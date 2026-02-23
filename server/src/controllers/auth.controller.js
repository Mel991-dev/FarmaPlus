/**
 * CRM FarmaPlus — Controlador de Autenticación
 * Recibe requests HTTP, valida inputs con express-validator,
 * delega al servicio y devuelve respuesta JSON estandarizada.
 * NO contiene lógica de negocio.
 */

'use strict';

const { validationResult } = require('express-validator');
const authService = require('../services/auth.service');

const authController = {
    /**
     * POST /api/auth/login
     * RF-01, HU-01 — Iniciar sesión con correo y contraseña.
     */
    async login(req, res, next) {
        try {
            // Validar inputs de express-validator
            const errors = validationResult(req);
            if (!errors.isEmpty()) {
                return res.status(422).json({
                    success: false,
                    message: 'Datos de entrada inválidos.',
                    errors: errors.array(),
                    code: 'VALIDATION_ERROR',
                });
            }

            const { correo, contrasena } = req.body;
            const ipOrigen = req.ip || req.headers['x-forwarded-for'] || null;

            const resultado = await authService.login(correo, contrasena, ipOrigen);

            return res.status(200).json({
                success: true,
                message: 'Sesión iniciada correctamente.',
                data: resultado,
            });
        } catch (error) {
            next(error);
        }
    },

    /**
     * POST /api/auth/recuperar-password
     * RF-06, HU-02 — Solicitar recuperación de contraseña.
     * Siempre devuelve 200 por seguridad (no revela si el correo existe).
     */
    async recuperarPassword(req, res, next) {
        try {
            const errors = validationResult(req);
            if (!errors.isEmpty()) {
                return res.status(422).json({
                    success: false,
                    message: 'Ingresa un correo electrónico válido.',
                    errors: errors.array(),
                    code: 'VALIDATION_ERROR',
                });
            }

            const { correo } = req.body;
            const ipOrigen = req.ip || req.headers['x-forwarded-for'] || null;

            const resultado = await authService.recuperarPassword(correo, ipOrigen);

            // Siempre 200 — HU-02 Escenario 2 (seguridad)
            return res.status(200).json({
                success: true,
                message: resultado.message,
            });
        } catch (error) {
            next(error);
        }
    },

    /**
     * POST /api/auth/logout
     * Registra el cierre de sesión en auditoría.
     * Requiere JWT válido (authMiddleware).
     */
    async logout(req, res, next) {
        try {
            const ipOrigen = req.ip || req.headers['x-forwarded-for'] || null;
            await authService.logout(req.usuario.sub, ipOrigen);

            return res.status(200).json({
                success: true,
                message: 'Sesión cerrada correctamente.',
            });
        } catch (error) {
            next(error);
        }
    },

    /**
     * POST /api/auth/verificar-codigo
     * Valida el código de recuperación ingresado.
     */
    async verificarCodigo(req, res, next) {
        try {
            const errors = validationResult(req);
            if (!errors.isEmpty()) {
                return res.status(422).json({
                    success: false,
                    message: 'Ingresa un código válido.',
                    errors: errors.array(),
                    code: 'VALIDATION_ERROR',
                });
            }

            const { correo, codigo } = req.body;
            const resultado = await authService.verificarCodigo(correo, codigo);

            return res.status(200).json(resultado);
        } catch (error) {
            next(error);
        }
    },

    /**
     * POST /api/auth/reset-password
     * Establece la nueva contraseña usando el código verificado.
     */
    async resetPassword(req, res, next) {
        try {
            const errors = validationResult(req);
            if (!errors.isEmpty()) {
                return res.status(422).json({
                    success: false,
                    message: 'Datos de entrada inválidos.',
                    errors: errors.array(),
                    code: 'VALIDATION_ERROR',
                });
            }

            const { correo, codigo, nuevaContrasena } = req.body;
            const ipOrigen = req.ip || req.headers['x-forwarded-for'] || null;

            const resultado = await authService.resetPassword(correo, codigo, nuevaContrasena, ipOrigen);

            return res.status(200).json(resultado);
        } catch (error) {
            next(error);
        }
    },

    /**
     * GET /api/auth/me
     * Devuelve los datos del usuario autenticado desde el JWT.
     * No consulta la BD — solo decodifica el token ya verificado.
     */
    async me(req, res) {
        return res.status(200).json({
            success: true,
            data: req.usuario,
        });
    },
};

module.exports = authController;
