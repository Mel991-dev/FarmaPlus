/**
 * CRM FarmaPlus — AuthContext
 * Estado global de autenticación: usuario logueado, rol, token.
 * Cumple: RNF-02 (inactividad 30 min), HU-01/HU-02.
 *
 * Persiste sesión en localStorage. Al recargar, restaura el usuario
 * sin pedir login de nuevo (dentro del período de 8h del JWT).
 */

import { createContext, useContext, useState, useEffect, useCallback, useRef } from 'react';
import { loginService, logoutService } from '../services/auth.service';

const AuthContext = createContext(null);

// Tiempo de inactividad en ms antes de cerrar sesión (RNF-02: 30 min)
const INACTIVITY_TIMEOUT = 30 * 60 * 1000;

export function AuthProvider({ children }) {
  const [usuario, setUsuario]   = useState(null);
  const [loading, setLoading]   = useState(true); // mientras restauramos sesión
  const inactivityTimer = useRef(null);

  // ── Restaurar sesión al recargar la página ──────────────────
  useEffect(() => {
    const token     = localStorage.getItem('farmaplus_token');
    const userStr   = localStorage.getItem('farmaplus_user');
    if (token && userStr) {
      try {
        const user = JSON.parse(userStr);
        setUsuario(user);
      } catch {
        clearSession();
      }
    }
    setLoading(false);
  }, []);

  // ── Timer de inactividad (RNF-02: 30 min) ──────────────────
  const resetInactivityTimer = useCallback(() => {
    clearTimeout(inactivityTimer.current);
    inactivityTimer.current = setTimeout(() => {
      clearSession();
      // La redirección al login ocurre via el interceptor axios (401)
      // o desde AppRouter al detectar usuario null
      window.location.href = '/login?sesion=expirada';
    }, INACTIVITY_TIMEOUT);
  }, []);

  useEffect(() => {
    if (!usuario) return;
    // Eventos que resetean el timer
    const eventos = ['mousemove', 'keydown', 'click', 'scroll', 'touchstart'];
    eventos.forEach((e) => window.addEventListener(e, resetInactivityTimer));
    resetInactivityTimer();
    return () => {
      eventos.forEach((e) => window.removeEventListener(e, resetInactivityTimer));
      clearTimeout(inactivityTimer.current);
    };
  }, [usuario, resetInactivityTimer]);

  // ── Limpiar sesión local ────────────────────────────────────
  function clearSession() {
    localStorage.removeItem('farmaplus_token');
    localStorage.removeItem('farmaplus_user');
    setUsuario(null);
  }

  // ── HU-01: Login ────────────────────────────────────────────
  async function login(correo, contrasena) {
    const resultado = await loginService({ correo, contrasena });
    localStorage.setItem('farmaplus_token', resultado.token);
    localStorage.setItem('farmaplus_user', JSON.stringify(resultado.usuario));
    setUsuario(resultado.usuario);
    return resultado.usuario;
  }

  // ── UC-02: Logout ───────────────────────────────────────────
  async function logout() {
    try { await logoutService(); } catch { /* ignorar error de red */ }
    clearSession();
  }

  // ── Helper de permisos por rol ──────────────────────────────
  function tieneRol(...roles) {
    return usuario ? roles.includes(usuario.rol) : false;
  }

  const value = {
    usuario,
    loading,
    login,
    logout,
    tieneRol,
    isAdmin:    () => tieneRol('Administrador'),
    isVendedor: () => tieneRol('Administrador', 'Vendedor'),
  };

  return <AuthContext.Provider value={value}>{children}</AuthContext.Provider>;
}

// Hook para consumir el contexto
export function useAuth() {
  const ctx = useContext(AuthContext);
  if (!ctx) throw new Error('useAuth debe usarse dentro de <AuthProvider>');
  return ctx;
}
