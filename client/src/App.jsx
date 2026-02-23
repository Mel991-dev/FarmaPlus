/**
 * CRM FarmaPlus — App.jsx
 * Raíz de la aplicación:
 *   - AuthProvider: contexto global de sesión
 *   - AppRouter: rutas con guards
 */

import { AuthProvider } from './context/AuthContext';
import AppRouter from './router/AppRouter';

export default function App() {
  return (
    <AuthProvider>
      <AppRouter />
    </AuthProvider>
  );
}
