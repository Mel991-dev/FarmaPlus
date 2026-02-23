/**
 * CRM FarmaPlus — Repositorio de Autenticación
 * Única capa que accede a la DB — SQL puro con Prepared Statements
 * Reglas de negocio: NO aquí. Solo queries.
 */

'use strict';

const db = require('../config/database');

const authRepository = {
    /**
     * Busca un usuario por correo electrónico.
     * Usado en login y recuperación de contraseña.
     * @param {string} correo
     * @returns {Object|null} usuario con su rol o null si no existe
     */
    async findByEmail(correo) {
        const rows = await db.query(
            `SELECT
         u.usuario_id,
         u.nombres,
         u.apellidos,
         u.correo,
         u.contrasena_hash,
         u.activo,
         u.ultimo_acceso,
         r.rol_id,
         r.nombre AS rol
       FROM usuarios u
       INNER JOIN roles r ON u.rol_id = r.rol_id
       WHERE u.correo = ?
       LIMIT 1`,
            [correo]
        );
        return rows[0] || null;
    },

    /**
     * Actualiza la fecha de último acceso del usuario.
     * Se llama después de un login exitoso.
     * @param {number} usuarioId
     */
    async updateLastAccess(usuarioId) {
        await db.query(
            `UPDATE usuarios SET ultimo_acceso = NOW() WHERE usuario_id = ?`,
            [usuarioId]
        );
    },

    /**
     * Registra un evento en la tabla de auditoría.
     * Llamado desde el servicio, no expuesto en la API.
     * @param {Object} params
     */
    async logAuditoria({ usuarioId = null, modulo, accion, tablaAfectada = null, registroId = null, detalle = null, ipOrigen = null }) {
        await db.query(
            `INSERT INTO logs_auditoria
         (usuario_id, modulo, accion, tabla_afectada, registro_id, detalle, ip_origen)
       VALUES (?, ?, ?, ?, ?, ?, ?)`,
            [usuarioId, modulo, accion, tablaAfectada, registroId, detalle, ipOrigen]
        );
    },

    /**
     * Guarda un código de recuperación para un usuario.
     * @param {number} usuarioId
     * @param {string} codigo
     * @param {Date} expira
     */
    async saveRecoveryCode(usuarioId, codigo, expira) {
        await db.query(
            `UPDATE usuarios SET recuperacion_codigo = ?, recuperacion_expira = ? WHERE usuario_id = ?`,
            [codigo, expira, usuarioId]
        );
    },

    /**
     * Busca un usuario por correo y código de recuperación vigente.
     * @param {string} correo
     * @param {string} codigo
     * @returns {Object|null}
     */
    async findByRecoveryCode(correo, codigo) {
        const rows = await db.query(
            `SELECT usuario_id, correo 
       FROM usuarios 
       WHERE correo = ? AND recuperacion_codigo = ? AND recuperacion_expira > NOW()
       LIMIT 1`,
            [correo, codigo]
        );
        return rows[0] || null;
    },

    /**
     * Actualiza la contraseña y limpia los datos de recuperación.
     * @param {number} usuarioId
     * @param {string} nuevoHash
     */
    async updatePasswordAndClearCode(usuarioId, nuevoHash) {
        await db.query(
            `UPDATE usuarios 
       SET contrasena_hash = ?, recuperacion_codigo = NULL, recuperacion_expira = NULL 
       WHERE usuario_id = ?`,
            [nuevoHash, usuarioId]
        );
    },
};

module.exports = authRepository;
