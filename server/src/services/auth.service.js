/**
 * CRM FarmaPlus — Servicio de Autenticación
 * Capa de dominio — Contiene TODA la lógica de negocio del módulo 0
 *
 * Reglas de negocio implementadas:
 *   RN-01: Un usuario solo puede tener un rol activo
 *   RN-03: Usuario desactivado no puede iniciar sesión
 *   RN-04: Correo electrónico único (validado en repositorio)
 *   HU-01: Login con correo + contraseña → JWT
 *   HU-02: Recuperación de contraseña (respuesta genérica por seguridad)
 *   RF-05: Registro en logs_auditoria de cada acción crítica
 *   RNF-01: Contraseñas con bcrypt cost 12
 *   RNF-02: JWT expira en 8h
 */

'use strict';

const bcrypt = require('bcrypt');
const jwt = require('jsonwebtoken');
const authRepository = require('../repositories/auth.repository');
const emailService = require('./email.service');

const JWT_SECRET = process.env.JWT_SECRET;
const JWT_EXPIRES = process.env.JWT_EXPIRES_IN || '8h';

const authService = {
    /**
     * HU-01 — Iniciar sesión
     * Valida credenciales, retorna JWT + datos del usuario sin datos sensibles.
     *
     * Seguridad: el mensaje de error es idéntico para correo incorrecto y
     * contraseña incorrecta — nunca revela cuál fue el fallo (HU-01 Escenario 2).
     *
     * @param {string} correo
     * @param {string} contrasena
     * @param {string} ipOrigen
     * @returns {{ token: string, usuario: Object }}
     */
    async login(correo, contrasena, ipOrigen) {
        // 1. Buscar usuario en la BD
        const usuario = await authRepository.findByEmail(correo);

        // 2. RN-03: Si no existe o está inactivo, mensaje genérico (HU-01 Esc. 2)
        if (!usuario) {
            await authRepository.logAuditoria({
                modulo: 'AUTH',
                accion: 'LOGIN_FALLIDO',
                detalle: `Intento con correo no registrado: ${correo}`,
                ipOrigen,
            });
            const err = new Error('Correo o contraseña incorrectos.');
            err.statusCode = 401;
            err.code = 'INVALID_CREDENTIALS';
            throw err;
        }

        // 3. Usuario desactivado (HU-01 Escenario 3 — mensaje específico en este caso)
        if (!usuario.activo) {
            await authRepository.logAuditoria({
                usuarioId: usuario.usuario_id,
                modulo: 'AUTH',
                accion: 'LOGIN_CUENTA_INACTIVA',
                ipOrigen,
            });
            const err = new Error('Tu cuenta está inactiva. Contacta al administrador.');
            err.statusCode = 403;
            err.code = 'ACCOUNT_INACTIVE';
            throw err;
        }

        // 4. Verificar contraseña con bcrypt (RNF-01)
        const passwordValida = await bcrypt.compare(contrasena, usuario.contrasena_hash);
        if (!passwordValida) {
            await authRepository.logAuditoria({
                usuarioId: usuario.usuario_id,
                modulo: 'AUTH',
                accion: 'LOGIN_FALLIDO',
                detalle: 'Contraseña incorrecta',
                ipOrigen,
            });
            const err = new Error('Correo o contraseña incorrectos.');
            err.statusCode = 401;
            err.code = 'INVALID_CREDENTIALS';
            throw err;
        }

        // 5. Generar JWT (RNF-02 — expira en 8h)
        const payload = {
            sub: usuario.usuario_id,
            rol: usuario.rol,
            nombre: `${usuario.nombres} ${usuario.apellidos}`,
        };
        const token = jwt.sign(payload, JWT_SECRET, { expiresIn: JWT_EXPIRES });

        // 6. Actualizar último acceso + auditoría
        await authRepository.updateLastAccess(usuario.usuario_id);
        await authRepository.logAuditoria({
            usuarioId: usuario.usuario_id,
            modulo: 'AUTH',
            accion: 'LOGIN_EXITOSO',
            ipOrigen,
        });

        // 7. Retornar token y datos del usuario (SIN contrasena_hash)
        return {
            token,
            usuario: {
                usuario_id: usuario.usuario_id,
                nombres: usuario.nombres,
                apellidos: usuario.apellidos,
                correo: usuario.correo,
                rol: usuario.rol,
                rol_id: usuario.rol_id,
            },
        };
    },

    /**
     * HU-02 — Recuperar contraseña (Paso 1: Solicitar código)
     * Genera un código de 6 dígitos que expira en 10 minutos.
     *
     * Seguridad: siempre responde éxito para evitar enumeración.
     *
     * @param {string} correo
     * @param {string} ipOrigen
     */
    async recuperarPassword(correo, ipOrigen) {
        const usuario = await authRepository.findByEmail(correo);

        // Generamos código de 6 dígitos (100000 - 999999)
        const codigo = Math.floor(100000 + Math.random() * 900000).toString();
        const expiracion = new Date(Date.now() + 10 * 60 * 1000); // 10 minutos

        // Log intent (siempre, por seguridad)
        await authRepository.logAuditoria({
            modulo: 'Auth',
            accion: 'Solicitud recuperación contraseña',
            tabla_afectada: 'usuarios',
            registro_id: usuario ? usuario.usuario_id : null,
            detalle: `Solicitud de recuperación para: ${correo}. Código generado: ${codigo}`,
            ip_origen: ipOrigen,
        });

        if (usuario && usuario.activo) {
            // Guardar en BD
            await authRepository.saveRecoveryCode(usuario.usuario_id, codigo, expiracion);

            // Enviar Email real
            try {
                await emailService.sendRecoveryEmail(correo, codigo);
                console.log(`[DEV] Email enviado a: ${correo} con código: ${codigo}`);
            } catch (emailError) {
                // Si falla el envío de email, no detenemos el flujo para evitar enumeración,
                // pero el código sigue disponible en logs para el dev.
                console.error(`[ERROR] Falló envío de email a ${correo}:`, emailError.message);
            }
        }

        // Siempre retornamos éxito genérico por seguridad (RN-03)
        return { message: 'Si el correo está registrado, recibirás un código de recuperación en breve.' };
    },

    /**
     * HU-02 — Verificar código (Paso 2)
     * Valida si el código ingresado coincide y no ha expirado.
     *
     * @param {string} correo
     * @param {string} codigo
     */
    async verificarCodigo(correo, codigo) {
        const usuario = await authRepository.findByRecoveryCode(correo, codigo);
        if (!usuario) {
            const err = new Error('Código inválido o expirado.');
            err.statusCode = 400;
            err.code = 'INVALID_CODE';
            throw err;
        }
        return { success: true, message: 'Código verificado correctamente.' };
    },

    /**
     * HU-02 — Resetear contraseña (Paso 3)
     * Aplica la nueva contraseña si el código es válido.
     *
     * @param {string} correo
     * @param {string} codigo
     * @param {string} nuevaContrasena
     * @param {string} ipOrigen
     */
    async resetPassword(correo, codigo, nuevaContrasena, ipOrigen) {
        const usuario = await authRepository.findByRecoveryCode(correo, codigo);
        if (!usuario) {
            const err = new Error('Código inválido o expirado.');
            err.statusCode = 400;
            err.code = 'INVALID_CODE';
            throw err;
        }

        const hash = await bcrypt.hash(nuevaContrasena, 12);
        await authRepository.updatePasswordAndClearCode(usuario.usuario_id, hash);

        await authRepository.logAuditoria({
            usuarioId: usuario.usuario_id,
            modulo: 'AUTH',
            accion: 'PASSWORD_RESET_EXITOSO',
            ipOrigen,
        });

        return { success: true, message: 'Contraseña actualizada correctamente.' };
    },

    /**
     * Registrar cierre de sesión en auditoría.
     * El JWT es stateless — la invalidación real ocurre en el frontend
     * eliminando el token del almacenamiento local.
     *
     * @param {number} usuarioId
     * @param {string} ipOrigen
     */
    async logout(usuarioId, ipOrigen) {
        await authRepository.logAuditoria({
            usuarioId,
            modulo: 'AUTH',
            accion: 'LOGOUT',
            ipOrigen,
        });
    },
};

module.exports = authService;
