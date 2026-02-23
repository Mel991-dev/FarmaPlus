/**
 * CRM FarmaPlus — Router de la Aplicación
 * Rutas protegidas por autenticación y rol.
 * RF-03: Acceso restringido según rol del usuario.
 *
 * Ruta protegida  → usuario no autenticado → redirect /login
 * Ruta de admin   → usuario sin rol Admin  → redirect /dashboard (403)
 */

import { BrowserRouter, Routes, Route, Navigate } from 'react-router-dom';
import { useAuth } from '../context/AuthContext';

// ── Páginas de autenticación (carga inmediata — siempre públicas)
import Login           from '../pages/auth/Login';
import RecuperarPassword from '../pages/auth/RecuperarPassword';

// ── Páginas principales (lazy carga futura)
// import Dashboard from '../pages/dashboard/Dashboard';

// ────────────────────────────────────────────────────────────
// Guard: ruta privada — redirige a /login si no hay sesión
// ────────────────────────────────────────────────────────────
function PrivateRoute({ children }) {
  const { usuario, loading } = useAuth();

  if (loading) {
    return (
      <div style={{ display:'flex', alignItems:'center', justifyContent:'center', height:'100vh', background:'#F4F9FC' }}>
        <div style={{ width:32, height:32, border:'3px solid #BDC3C7', borderTopColor:'#1A6B8A', borderRadius:'50%', animation:'spin 0.7s linear infinite' }} />
      </div>
    );
  }

  return usuario ? children : <Navigate to="/login" replace />;
}

// ────────────────────────────────────────────────────────────
// Guard: ruta de administrador — redirige al dashboard si no es Admin
// ────────────────────────────────────────────────────────────
function AdminRoute({ children }) {
  const { isAdmin, loading } = useAuth();
  if (loading) return null;
  return isAdmin() ? children : <Navigate to="/dashboard" replace />;
}

// ────────────────────────────────────────────────────────────
// Guard: usuario ya autenticado — redirige al dashboard
// ────────────────────────────────────────────────────────────
function PublicRoute({ children }) {
  const { usuario, loading } = useAuth();
  if (loading) return null;
  return !usuario ? children : <Navigate to="/dashboard" replace />;
}

// ────────────────────────────────────────────────────────────
// Router principal
// ────────────────────────────────────────────────────────────
export default function AppRouter() {
  return (
    <BrowserRouter>
      <Routes>
        {/* ── Rutas públicas (solo para no autenticados) ── */}
        <Route path="/login"             element={<PublicRoute><Login /></PublicRoute>} />
        <Route path="/recuperar-password" element={<PublicRoute><RecuperarPassword /></PublicRoute>} />

        {/* ── Raíz: redirige al dashboard o login según sesión ── */}
        <Route path="/" element={<Navigate to="/dashboard" replace />} />

        {/* ── Rutas privadas (requieren autenticación) ── */}
        <Route path="/dashboard" element={<PrivateRoute><div>Dashboard — próximamente</div></PrivateRoute>} />

        {/* ── 404 ── */}
        <Route path="*" element={<Navigate to="/" replace />} />
      </Routes>
    </BrowserRouter>
  );
}
