/**
 * CRM FarmaPlus — Página de Login
 * HU-01: Login con correo + contraseña → JWT → redirige al dashboard
 * Replicado fielmente del mockup: pantalla-01-login.html
 */

import { useState } from 'react';
import { useNavigate, Link } from 'react-router-dom';
import { useAuth } from '../../context/AuthContext';
import './Login.css';

export default function Login() {
  const navigate       = useNavigate();
  const { login }      = useAuth();

  const [correo, setCorreo]             = useState('');
  const [contrasena, setContrasena]     = useState('');
  const [mostrarPass, setMostrarPass]   = useState(false);
  const [loading, setLoading]           = useState(false);
  const [error, setError]               = useState('');
  const [errors, setErrors]             = useState({});

  // ── Validación local ──────────────────────────────────────
  function validate() {
    const e = {};
    if (!correo.trim())         e.correo     = 'El correo es obligatorio.';
    else if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo))
                                e.correo     = 'Ingresa un correo válido.';
    if (!contrasena)            e.contrasena = 'La contraseña es obligatoria.';
    else if (contrasena.length < 6) e.contrasena = 'Mínimo 6 caracteres.';
    setErrors(e);
    return Object.keys(e).length === 0;
  }

  // ── Submit ────────────────────────────────────────────────
  async function handleSubmit(e) {
    e.preventDefault();
    setError('');
    if (!validate()) return;

    setLoading(true);
    try {
      await login(correo, contrasena);
      navigate('/dashboard', { replace: true });
    } catch (err) {
      const msg = err.response?.data?.message || 'Ocurrió un error. Intenta de nuevo.';
      setError(msg);
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="auth-wrapper">

      {/* ─── Panel izquierdo — Marca ─────────────────────── */}
      <aside className="brand-panel" role="complementary" aria-label="Información de la plataforma">
        <div className="deco-circle" />
        <div className="brand-content">

          <div className="logo-wrap">
            <div className="logo-icon" aria-hidden="true">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round">
                <path d="M10.5 3.5a2 2 0 0 1 3 0l1.5 1.5H18a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h3L10.5 3.5z"/><rect x="9" y="10" width="6" height="5" rx="0.5"/><line x1="12" y1="10" x2="12" y2="15"/>
              </svg>
            </div>
            <div className="logo-text">
              <span className="logo-name">CRM Farma<span>+</span></span>
              <span className="logo-sub">Plataforma de gestión</span>
            </div>
          </div>

          <p className="brand-tagline">Conectamos médicos,<br/>pacientes y tu droguería.</p>
          <p className="brand-desc">Gestión integral de clientes, productos<br/>y reportes para tu cadena de droguerías.</p>

          <div className="brand-divider" />

          <div className="feature-list">
            <div className="feature-item">
              <div className="feature-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" width="16" height="16"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
              </div>
              <span>Clientes médicos y pacientes unificados</span>
            </div>
            <div className="feature-item">
              <div className="feature-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" width="16" height="16"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>
              </div>
              <span>Reportes gerenciales con métricas clave</span>
            </div>
            <div className="feature-item">
              <div className="feature-icon" aria-hidden="true">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" width="16" height="16"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
              </div>
              <span>Acceso seguro por rol y permisos</span>
            </div>
          </div>

          <div className="brand-footer">
            FarmaPlus © 2026 · v1.0.0
          </div>
        </div>
      </aside>

      {/* ─── Panel derecho — Formulario ──────────────────── */}
      <main className="form-panel" role="main">
        <div className="form-container">

          <div className="form-header">
            <p className="form-pretitle">BIENVENIDO DE NUEVO</p>
            <h1 className="form-title">Iniciar sesión</h1>
            <p className="form-subtitle">Ingresa tus credenciales para acceder al sistema.</p>
          </div>

          {/* Alerta de error global */}
          {error && (
            <div className="alert alert-error" role="alert">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
              <span>{error}</span>
            </div>
          )}

          <form onSubmit={handleSubmit} noValidate>

            {/* Campo correo */}
            <div className="form-group">
              <label className="form-label" htmlFor="correo">Correo electrónico</label>
              <div className="input-wrap">
                <svg className="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                <input
                  type="email"
                  id="correo"
                  name="correo"
                  className={`form-input${errors.correo ? ' input-error' : ''}`}
                  placeholder="usuario@farmaplus.com.co"
                  value={correo}
                  onChange={(e) => { setCorreo(e.target.value); setErrors(prev => ({ ...prev, correo: '' })); }}
                  autoComplete="email"
                  autoFocus
                  aria-describedby={errors.correo ? 'error-correo' : undefined}
                />
              </div>
              {errors.correo && (
                <span className="field-error visible" id="error-correo" role="alert">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                  {errors.correo}
                </span>
              )}
            </div>

            {/* Campo contraseña */}
            <div className="form-group">
              <label className="form-label" htmlFor="contrasena">Contraseña</label>
              <div className="input-wrap">
                <svg className="input-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true"><rect x="3" y="11" width="18" height="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
                <input
                  type={mostrarPass ? 'text' : 'password'}
                  id="contrasena"
                  name="contrasena"
                  className={`form-input${errors.contrasena ? ' input-error' : ''}`}
                  placeholder="••••••••"
                  value={contrasena}
                  onChange={(e) => { setContrasena(e.target.value); setErrors(prev => ({ ...prev, contrasena: '' })); }}
                  autoComplete="current-password"
                  aria-describedby={errors.contrasena ? 'error-contrasena' : undefined}
                />
                <button
                  type="button"
                  className="toggle-pass"
                  onClick={() => setMostrarPass(p => !p)}
                  aria-label={mostrarPass ? 'Ocultar contraseña' : 'Mostrar contraseña'}
                >
                  {mostrarPass ? (
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94"/><path d="M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                  ) : (
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                  )}
                </button>
              </div>
              {errors.contrasena && (
                <span className="field-error visible" id="error-contrasena" role="alert">
                  <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                  {errors.contrasena}
                </span>
              )}
            </div>

            {/* Recuperar contraseña */}
            <div className="forgot-wrap">
              <Link to="/recuperar-password" className="forgot-link">
                ¿Olvidaste tu contraseña?
              </Link>
            </div>

            {/* Botón submit */}
            <button
              type="submit"
              id="btn-login"
              className={`btn btn-primary btn-full${loading ? ' btn-loading' : ''}`}
              disabled={loading}
            >
              <div className="btn-spinner" />
              <span className="btn-content">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" strokeLinejoin="round" width="18" height="18"><path d="M15 3h4a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-4"/><polyline points="10 17 15 12 10 7"/><line x1="15" y1="12" x2="3" y2="12"/></svg>
                {loading ? 'Iniciando sesión…' : 'Iniciar sesión'}
              </span>
            </button>

          </form>

          <footer className="form-footer">
            <p>Sistema restringido — solo <strong>personal autorizado</strong></p>
            <p className="version-tag">v2.4.1 · CRM Farma+ · Colombia</p>
          </footer>
        </div>
      </main>
    </div>
  );
}
