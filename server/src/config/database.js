/**
 * CRM FarmaPlus — Pool de conexiones MySQL
 * Driver: mysql2 con Prepared Statements (sin ORM)
 * Arquitectura: Capa de Repositorios — única capa que toca la BD
 */

'use strict';
const path = require('path');
const mysql = require('mysql2/promise');
require('dotenv').config({ path: path.resolve(__dirname, '../../.env') });

// ─── Configuración del pool ───────────────────────────────────────────────────
const pool = mysql.createPool({
    host: process.env.DB_HOST || 'localhost',
    port: parseInt(process.env.DB_PORT) || 3306,
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    database: process.env.DB_NAME || 'crm_farma',
    charset: 'utf8mb4',
    timezone: '-05:00',             // Zona horaria Colombia (UTC-5)
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0,
    enableKeepAlive: true,
    keepAliveInitialDelay: 0,
});

// ─── Función principal de consulta ───────────────────────────────────────────
/**
 * Ejecuta una query con Prepared Statements.
 * Todos los repositorios deben usar esta función exclusivamente.
 *
 * @param {string} sql   - Query SQL con placeholders (?)
 * @param {Array}  params - Parámetros para el Prepared Statement
 * @returns {Promise<Array>} Filas del resultado
 */
async function query(sql, params = []) {
    const [rows] = await pool.execute(sql, params);
    return rows;
}

// ─── Verificación de conexión al arrancar ─────────────────────────────────────
/**
 * Intenta obtener una conexión del pool para verificar que la BD responde.
 * Se llama una vez al iniciar el servidor.
 */
async function testConnection() {
    try {
        const connection = await pool.getConnection();
        connection.release();
        console.log('✅ Conexión a MySQL establecida correctamente');
        console.log(`   Base de datos: ${process.env.DB_NAME} | Host: ${process.env.DB_HOST}:${process.env.DB_PORT}`);
    } catch (error) {
        console.error('❌ Error al conectar con MySQL:', error.message);
        console.error('   Verifica que WAMP esté corriendo y las credenciales en .env sean correctas.');
        process.exit(1); // Detiene el servidor si no hay BD
    }
}

module.exports = { query, testConnection, pool };
