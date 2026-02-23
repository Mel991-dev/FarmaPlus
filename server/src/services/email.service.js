/**
 * CRM FarmaPlus — Email Service
 * Gestión de envíos de correo electrónico mediante Nodemailer.
 * Incluye plantillas HTML premium con estética moderna.
 */

'use strict';

const nodemailer = require('nodemailer');
require('dotenv').config();

// ─── Configuración del Transportador ──────────────────────────────────────────
let transporter;

async function getTransporter() {
    if (transporter) return transporter;

    // Si hay credenciales en el .env, las usamos
    if (process.env.SMTP_USER && process.env.SMTP_USER !== 'test@example.com') {
        console.log(`[EMAIL] Usando servidor SMTP real: ${process.env.SMTP_HOST} (${process.env.SMTP_USER})`);
        transporter = nodemailer.createTransport({
            host: process.env.SMTP_HOST,
            port: parseInt(process.env.SMTP_PORT),
            secure: process.env.SMTP_SECURE === 'true',
            auth: {
                user: process.env.SMTP_USER,
                pass: process.env.SMTP_PASS,
            },
        });
    } else {
        // En desarrollo, si no hay credenciales, creamos una cuenta de prueba de Ethereal
        console.log('[EMAIL] Generando cuenta de prueba Ethereal automática...');
        const testAccount = await nodemailer.createTestAccount();
        transporter = nodemailer.createTransport({
            host: 'smtp.ethereal.email',
            port: 587,
            secure: false,
            auth: {
                user: testAccount.user,
                pass: testAccount.pass,
            },
        });
    }
    return transporter;
}

/**
 * Genera la plantilla HTML para el código de recuperación.
 * @param {string} code - Código de 6 dígitos
 * @returns {string} HTML estilizado
 */
function getRecoveryTemplate(code) {
    return `
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Recuperación de Contraseña — FarmaPlus</title>
        <style>
            body { font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f4f7f6; margin: 0; padding: 0; color: #333; }
            .container { max-width: 600px; margin: 40px auto; background: #ffffff; border-radius: 12px; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.1); }
            .header { background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); padding: 30px; text-align: center; color: white; }
            .logo-text { font-size: 24px; font-weight: bold; letter-spacing: 1px; }
            .logo-text span { color: #27ae60; }
            .content { padding: 40px; text-align: center; }
            .content h1 { font-size: 22px; color: #2c3e50; margin-bottom: 20px; }
            .content p { font-size: 16px; line-height: 1.6; color: #666; margin-bottom: 30px; }
            .code-box { background: #f8f9fa; border: 2px dashed #27ae60; border-radius: 8px; padding: 20px; font-size: 32px; font-weight: bold; color: #27ae60; letter-spacing: 10px; display: inline-block; margin-bottom: 30px; }
            .footer { background: #f8f9fa; padding: 20px; text-align: center; font-size: 12px; color: #999; border-top: 1px solid #eee; }
            .btn { display: inline-block; padding: 12px 24px; background-color: #27ae60; color: white; text-decoration: none; border-radius: 6px; font-weight: bold; transition: background 0.3s; }
            .btn:hover { background-color: #219150; }
            .warning { font-size: 13px; color: #e74c3c; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="header">
                <div class="logo-text">CRM Farma<span>+</span></div>
                <div style="font-size: 12px; opacity: 0.8; margin-top: 5px;">Tu plataforma de confianza</div>
            </div>
            <div class="content">
                <h1>Restablecer tu contraseña</h1>
                <p>Hola, hemos recibido una solicitud para restablecer la contraseña de tu cuenta en <strong>FarmaPlus</strong>.</p>
                <p>Usa el siguiente código de verificación para continuar:</p>
                <div class="code-box">${code}</div>
                <p>Este código es de un solo uso y <strong>expira en 10 minutos</strong>.</p>
                <div class="warning">Si no solicitaste este cambio, puedes ignorar este correo de forma segura.</div>
            </div>
            <div class="footer">
                &copy; 2026 FarmaPlus CRM. Todos los derechos reservados.<br>
                Este es un mensaje automático, por favor no respondas a este correo.
            </div>
        </div>
    </body>
    </html>
    `;
}

/**
 * Envía el correo de recuperación con el código.
 * @param {string} to - Correo del destinatario
 * @param {string} code - Código de 6 dígitos
 */
async function sendRecoveryEmail(to, code) {
    const transport = await getTransporter();

    const mailOptions = {
        from: `"FarmaPlus CRM" <${process.env.SMTP_FROM || 'noreply@crmfarma.com'}>`,
        to: to,
        subject: '🔐 Código de recuperación — FarmaPlus',
        html: getRecoveryTemplate(code),
    };

    try {
        const info = await transport.sendMail(mailOptions);
        if (process.env.NODE_ENV === 'development') {
            const testUrl = nodemailer.getTestMessageUrl(info);
            if (testUrl) {
                console.log(`[EMAIL] Vista previa disponible en: ${testUrl}`);
            }
        }
        return info;
    } catch (error) {
        console.error('❌ Error al enviar email:', error);
        throw new Error('No se pudo enviar el correo de recuperación.');
    }
}

module.exports = {
    sendRecoveryEmail,
};
