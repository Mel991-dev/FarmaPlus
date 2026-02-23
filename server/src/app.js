/**
 * CRM FarmaPlus — Servidor principal Express
 * Arquitectura: Monolito en capas (RNF-08, RNF-09)
 * Middlewares de seguridad: helmet, cors, morgan
 */

'use strict';
const path = require('path');
require('dotenv').config({ path: path.resolve(__dirname, '../.env') });

const express = require('express');
const cors = require('cors');
const helmet = require('helmet');
const morgan = require('morgan');
const errorHandler = require('./middlewares/errorHandler');
const { testConnection } = require('./config/database');

const app = express();
const PORT = process.env.PORT || 3000;

// ─── Middlewares globales ─────────────────────────────────────────────────────

// Seguridad: headers HTTP seguros
app.use(helmet());

// CORS: permite peticiones desde el frontend React (Vite corre en :5173)
app.use(cors({
    origin: process.env.NODE_ENV === 'production'
        ? process.env.FRONTEND_URL
        : ['http://localhost:5173', 'http://localhost:5174', 'http://localhost:3000'],
    credentials: true,
    methods: ['GET', 'POST', 'PUT', 'PATCH', 'DELETE'],
}));

// Logger HTTP (solo en desarrollo)
if (process.env.NODE_ENV === 'development') {
    app.use(morgan('dev'));
}

// Parseo de JSON
app.use(express.json());
app.use(express.urlencoded({ extended: false }));

// ─── Health Check ─────────────────────────────────────────────────────────────
/**
 * GET /api/health
 * Endpoint para verificar que el servidor y la BD están operativos.
 * No requiere autenticación.
 */
app.get('/api/health', async (req, res) => {
    const { pool } = require('./config/database');
    try {
        const connection = await pool.getConnection();
        connection.release();
        return res.json({
            success: true,
            message: 'FarmaPlus API operativa',
            database: 'conectada',
            env: process.env.NODE_ENV,
            timestamp: new Date().toISOString(),
        });
    } catch (error) {
        return res.status(503).json({
            success: false,
            message: 'FarmaPlus API operativa pero la BD no responde',
            database: 'desconectada',
            error: error.message,
        });
    }
});

// ─── Rutas de la API ──────────────────────────────────────────────────────────
// TODO: se irán registrando aquí a medida que se implementen los módulos
app.use('/api/auth', require('./routes/auth.routes'));
// app.use('/api/medicos',    require('./routes/medicos.routes'));
// app.use('/api/pacientes',  require('./routes/pacientes.routes'));
// app.use('/api/productos',  require('./routes/productos.routes'));
// app.use('/api/reportes',   require('./routes/reportes.routes'));
// app.use('/api/usuarios',   require('./routes/usuarios.routes'));

// ─── Ruta 404 ─────────────────────────────────────────────────────────────────
app.use((req, res) => {
    res.status(404).json({
        success: false,
        message: `Ruta no encontrada: ${req.method} ${req.originalUrl}`,
        code: 'NOT_FOUND',
    });
});

// ─── Middleware global de errores (debe ir al final) ─────────────────────────
app.use(errorHandler);

// ─── Arranque del servidor ────────────────────────────────────────────────────
async function start() {
    await testConnection(); // Verifica la BD antes de escuchar peticiones
    app.listen(PORT, () => {
        console.log(`🚀 Servidor FarmaPlus corriendo en http://localhost:${PORT}`);
        console.log(`   Entorno: ${process.env.NODE_ENV}`);
        console.log(`   Health check: http://localhost:${PORT}/api/health`);
    });
}

start();

module.exports = app; // Para pruebas futuras
