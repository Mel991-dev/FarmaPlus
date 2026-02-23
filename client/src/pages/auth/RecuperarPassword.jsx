/**
 * CRM FarmaPlus — Recuperar Contraseña
 * HU-02: Flujo de 3 pasos con código de verificación.
 * Replicado fiel del mockup: pantalla-02-recuperar.html
 */

import { useState } from 'react';
import { Link } from 'react-router-dom';
import { 
  recuperarPasswordService, 
  verificarCodigoService, 
  resetPasswordService 
} from '../../services/auth.service';
import './RecuperarPassword.css';

export default function RecuperarPassword() {
  // ── Estados de flujo ──
  const [step, setStep] = useState(1); // 1: Email, 2: Código, 3: Nueva Clave
  const [loading, setLoading] = useState(false);
  const [errorLocal, setErrorLocal] = useState('');
  const [successMsg, setSuccessMsg] = useState('');

  // ── Datos del formulario ──
  const [correo, setCorreo] = useState('');
  const [codigo, setCodigo] = useState('');
  const [nuevaContrasena, setNuevaContrasena] = useState('');
  const [confirmarContrasena, setConfirmarContrasena] = useState('');

  // ── Manejadores de pasos ──

  // Paso 1: Solicitar código
  async function handleSolicitar(e) {
    e.preventDefault();
    if (!correo.trim() || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(correo)) {
      setErrorLocal('Ingresa un correo electrónico válido.');
      return;
    }

    setLoading(true);
    setErrorLocal('');
    try {
      await recuperarPasswordService(correo);
      setStep(2);
    } catch (err) {
      // Por seguridad, avanzamos o mostramos éxito genérico
      setStep(2);
    } finally {
      setLoading(false);
    }
  }

  // Paso 2: Verificar código
  async function handleVerificar(e) {
    e.preventDefault();
    if (codigo.length !== 6) {
      setErrorLocal('El código debe tener 6 dígitos.');
      return;
    }

    setLoading(true);
    setErrorLocal('');
    try {
      await verificarCodigoService(correo, codigo);
      setStep(3);
    } catch (err) {
      setErrorLocal(err.response?.data?.message || 'Código inválido o expirado.');
    } finally {
      setLoading(false);
    }
  }

  // Paso 3: Cambiar contraseña
  async function handleReset(e) {
    e.preventDefault();
    if (nuevaContrasena.length < 6) {
      setErrorLocal('La contraseña debe tener al menos 6 caracteres.');
      return;
    }
    if (nuevaContrasena !== confirmarContrasena) {
      setErrorLocal('Las contraseñas no coinciden.');
      return;
    }

    setLoading(true);
    setErrorLocal('');
    try {
      await resetPasswordService({ correo, codigo, nuevaContrasena });
      setSuccessMsg('Contraseña actualizada con éxito.');
      setStep(4); // Pantalla de éxito final
    } catch (err) {
      setErrorLocal(err.response?.data?.message || 'Error al actualizar la contraseña.');
    } finally {
      setLoading(false);
    }
  }

  return (
    <div className="auth-wrapper">
      {/* ─── Panel izquierdo — Marca ─────────────────────── */}
      <aside className="brand-panel">
        <div className="deco-circle" />
        <div className="brand-content">
          <div className="logo-wrap">
            <div className="logo-icon">
              <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2"><path d="M10.5 3.5a2 2 0 0 1 3 0l1.5 1.5H18a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h3L10.5 3.5z"/><rect x="9" y="10" width="6" height="5" rx="0.5"/><line x1="12" y1="10" x2="12" y2="15"/></svg>
            </div>
            <div className="logo-text">
              <span className="logo-name">CRM Farma<span>+</span></span>
              <span className="logo-sub">Plataforma de gestión</span>
            </div>
          </div>
          <p className="brand-tagline">Recupera el acceso<br/>a tu cuenta</p>
          <p className="brand-desc">Sigue los pasos para restablecer tu<br/>contraseña de forma segura.</p>
          <div className="brand-divider" />
          <div className="feature-list">
            <div className="feature-item"><span>Código de 6 dígitos de un solo uso</span></div>
            <div className="feature-item"><span>Expiración de seguridad (10 min)</span></div>
            <div className="feature-item"><span>Cifrado de grado bancario</span></div>
          </div>
        </div>
      </aside>

      {/* ─── Panel derecho — Formulario ──────────────────── */}
      <main className="form-panel">
        <div className="form-container">
          <Link to="/login" className="back-link">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" width="15"><polyline points="15 18 9 12 15 6"/></svg>
            Volver al inicio de sesión
          </Link>

          {/* Stepper */}
          <div className="steps-wrap">
            <div className={`step-dot ${step === 1 ? 'step-active' : step > 1 ? 'step-done' : ''}`}>{step > 1 ? '✓' : '1'}</div>
            <span className={`step-label ${step === 1 ? 'step-label-active' : ''}`}>Solicitar</span>
            <div className="step-connector"><div className={`step-connector-fill ${step > 1 ? 'filled' : ''}`} /></div>
            
            <div className={`step-dot ${step === 2 ? 'step-active' : step > 2 ? 'step-done' : ''}`}>{step > 2 ? '✓' : '2'}</div>
            <span className={`step-label ${step === 2 ? 'step-label-active' : ''}`}>Código</span>
            <div className="step-connector"><div className={`step-connector-fill ${step > 2 ? 'filled' : ''}`} /></div>

            <div className={`step-dot ${step === 3 ? 'step-active' : step > 3 ? 'step-done' : ''}`}>{step > 3 ? '✓' : '3'}</div>
            <span className={`step-label ${step === 3 ? 'step-label-active' : ''}`}>Cambio</span>
          </div>

          {step === 1 && (
            <form onSubmit={handleSolicitar}>
              <h1 className="form-title">¿Olvidaste tu contraseña?</h1>
              <p className="form-subtitle">Ingresa tu correo y te enviaremos un código de 6 dígitos.</p>
              <div className="form-group">
                <label className="form-label">Correo electrónico</label>
                <input 
                  type="email" className="form-input" placeholder="correo@drogueria.com"
                  value={correo} onChange={e => setCorreo(e.target.value)} required
                />
                {errorLocal && <span className="field-error visible">{errorLocal}</span>}
              </div>
              <button type="submit" className={`btn btn-primary btn-full ${loading ? 'btn-loading' : ''}`} disabled={loading}>
                Enviar código
              </button>
            </form>
          )}

          {step === 2 && (
            <form onSubmit={handleVerificar}>
              <h1 className="form-title">Ingresa el código</h1>
              <p className="form-subtitle">Hemos enviado un código de 6 dígitos a <strong>{correo}</strong>.</p>
              <div className="form-group">
                <label className="form-label">Código de verificación</label>
                <input 
                  type="text" className="form-input" placeholder="000000" maxLength="6"
                  value={codigo} onChange={e => setCodigo(e.target.value.replace(/\D/g,''))} required
                />
                {errorLocal && <span className="field-error visible">{errorLocal}</span>}
                <p className="field-hint">Revisa tu bandeja de entrada o spam. El código expira en 10 min.</p>
              </div>
              <button type="submit" className={`btn btn-primary btn-full ${loading ? 'btn-loading' : ''}`} disabled={loading}>
                Verificar código
              </button>
              <button type="button" className="btn btn-secondary btn-full" style={{marginTop:'1rem'}} onClick={() => setStep(1)}>
                Atrás
              </button>
            </form>
          )}

          {step === 3 && (
            <form onSubmit={handleReset}>
              <h1 className="form-title">Nueva contraseña</h1>
              <p className="form-subtitle">Crea una clave segura para tu cuenta.</p>
              <div className="form-group">
                <label className="form-label">Nueva contraseña</label>
                <input 
                  type="password" className="form-input" placeholder="••••••••"
                  value={nuevaContrasena} onChange={e => setNuevaContrasena(e.target.value)} required
                />
              </div>
              <div className="form-group">
                <label className="form-label">Confirmar contraseña</label>
                <input 
                  type="password" className="form-input" placeholder="••••••••"
                  value={confirmarContrasena} onChange={e => setConfirmarContrasena(e.target.value)} required
                />
                {errorLocal && <span className="field-error visible">{errorLocal}</span>}
              </div>
              <button type="submit" className={`btn btn-primary btn-full ${loading ? 'btn-loading' : ''}`} disabled={loading}>
                Cambiar contraseña
              </button>
            </form>
          )}

          {step === 4 && (
            <div className="success-card">
              <div className="success-icon-wrap">✓</div>
              <h2 className="success-title">¡Listo!</h2>
              <p className="success-desc">{successMsg}</p>
              <Link to="/login" className="btn btn-primary btn-full">Ir al login</Link>
            </div>
          )}

          <footer className="form-footer">
            <p>Plataforma para uso exclusivo del personal de FarmaPlus.</p>
            <p className="version-tag">v1.0.0 · 2026</p>
          </footer>
        </div>
      </main>
    </div>
  );
}
