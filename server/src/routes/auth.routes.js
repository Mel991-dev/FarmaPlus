/**
 * CRM FarmaPlus — Rutas de Autenticación
 * RF-01, RF-06, HU-01, HU-02
 *
 * Rutas públicas  (sin authMiddleware): /login, /recuperar-password
 * Rutas privadas  (con authMiddleware): /logout, /me
 */

'use strict';

const { Router } = require('express');
const { body } = require('express-validator');
const authController = require('../controllers/auth.controller');
const authMiddleware = require('../middlewares/authMiddleware');

const router = Router();

// ────────────────────────────────────────────────────────────
// POST /api/auth/login
// RF-01 — Login con correo y contraseña
// ────────────────────────────────────────────────────────────
router.post(
    '/login',
    [
        body('correo')
            .trim()
            .isEmail().withMessage('Ingresa un correo electrónico válido.')
            .normalizeEmail(),
        body('contrasena')
            .notEmpty().withMessage('La contraseña no puede estar vacía.')
            .isLength({ min: 6 }).withMessage('La contraseña es demasiado corta.'),
    ],
    authController.login
);

// ────────────────────────────────────────────────────────────
// POST /api/auth/recuperar-password
// RF-06, HU-02 — Solicitar recuperación por correo
// ────────────────────────────────────────────────────────────
router.post(
    '/recuperar-password',
    [
        body('correo')
            .trim()
            .isEmail().withMessage('Ingresa un correo electrónico válido.')
            .normalizeEmail(),
    ],
    authController.recuperarPassword
);

// ────────────────────────────────────────────────────────────
// POST /api/auth/verificar-codigo
// Paso 2 de recuperación: Validar código de 6 dígitos
// ────────────────────────────────────────────────────────────
router.post(
    '/verificar-codigo',
    [
        body('correo')
            .trim()
            .isEmail().withMessage('Ingresa un correo electrónico válido.')
            .normalizeEmail(),
        body('codigo')
            .isLength({ min: 6, max: 6 }).withMessage('El código debe tener 6 dígitos.'),
    ],
    authController.verificarCodigo
);

// ────────────────────────────────────────────────────────────
// POST /api/auth/reset-password
// Paso 3 de recuperación: Establecer nueva contraseña
// ────────────────────────────────────────────────────────────
router.post(
    '/reset-password',
    [
        body('correo')
            .trim()
            .isEmail().withMessage('Ingresa un correo electrónico válido.')
            .normalizeEmail(),
        body('codigo')
            .isLength({ min: 6, max: 6 }).withMessage('El código debe tener 6 dígitos.'),
        body('nuevaContrasena')
            .notEmpty().withMessage('La contraseña no puede estar vacía.')
            .isLength({ min: 6 }).withMessage('La contraseña es demasiado corta.'),
    ],
    authController.resetPassword
);

// ────────────────────────────────────────────────────────────
// POST /api/auth/logout
// UC-02 — Cerrar sesión (requiere JWT válido)
// ────────────────────────────────────────────────────────────
router.post('/logout', authMiddleware, authController.logout);

// ────────────────────────────────────────────────────────────
// GET /api/auth/me
// Devuelve datos del usuario autenticado desde el token
// ────────────────────────────────────────────────────────────
router.get('/me', authMiddleware, authController.me);

module.exports = router;
