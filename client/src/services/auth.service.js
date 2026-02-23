/**
 * CRM FarmaPlus — Servicio HTTP de Autenticación (Frontend)
 * Todas las llamadas HTTP al backend de auth pasan por aquí.
 * Usa axios con interceptores configurados en axiosInstance.
 */

import axios from 'axios';

const API_BASE = import.meta.env.VITE_API_URL || 'http://localhost:3001/api';

// Instancia base de axios con token JWT automático
const api = axios.create({ baseURL: API_BASE });

// Interceptor: adjunta el JWT en cada request si existe
api.interceptors.request.use((config) => {
    const token = localStorage.getItem('farmaplus_token');
    if (token) config.headers.Authorization = `Bearer ${token}`;
    return config;
});

// Interceptor: si el backend responde 401, limpia la sesión local
// Evitamos redireccionar si ya estamos en login o si es el propio endpoint de login el que falla
api.interceptors.response.use(
    (response) => response,
    (error) => {
        const isLoginRequest = error.config?.url?.includes('/auth/login');
        const isAlreadyOnLoginPage = window.location.pathname === '/login';

        if (error.response?.status === 401 && !isLoginRequest && !isAlreadyOnLoginPage) {
            localStorage.removeItem('farmaplus_token');
            localStorage.removeItem('farmaplus_user');
            window.location.href = '/login';
        }
        return Promise.reject(error);
    }
);

export { api };

// ── Endpoints del módulo de auth ─────────────────────────────

/**
 * POST /api/auth/login
 * @param {{ correo: string, contrasena: string }} datos
 * @returns {{ token, usuario }}
 */
export async function loginService(datos) {
    const { data } = await api.post('/auth/login', datos);
    return data.data; // { token, usuario }
}

/**
 * POST /api/auth/recuperar-password
 * @param {string} correo
 * @returns {{ message: string }}
 */
export async function recuperarPasswordService(correo) {
    const { data } = await api.post('/auth/recuperar-password', { correo });
    return data;
}

/**
 * POST /api/auth/verificar-codigo
 * @param {string} correo
 * @param {string} codigo
 */
export async function verificarCodigoService(correo, codigo) {
    const { data } = await api.post('/auth/verificar-codigo', { correo, codigo });
    return data;
}

/**
 * POST /api/auth/reset-password
 * @param {{ correo: string, codigo: string, nuevaContrasena: string }} datos
 */
export async function resetPasswordService(datos) {
    const { data } = await api.post('/auth/reset-password', datos);
    return data;
}

/**
 * POST /api/auth/logout
 * Requiere JWT válido en el header.
 */
export async function logoutService() {
    await api.post('/auth/logout');
}

/**
 * GET /api/auth/me
 * Devuelve los datos del usuario autenticado.
 */
export async function getMeService() {
    const { data } = await api.get('/auth/me');
    return data.data;
}
