/**
 * FarmaPlus CRM — app.js
 * JavaScript global: helpers, utilidades, toasts y funciones compartidas.
 */

'use strict';

// ============================================================
// Toast / Notificaciones
// ============================================================
const Toast = {
    container: null,

    init() {
        this.container = document.getElementById('toast-container');
        if (!this.container) {
            this.container = document.createElement('div');
            this.container.id = 'toast-container';
            this.container.style.cssText = 'position:fixed;top:20px;right:20px;z-index:9999;display:flex;flex-direction:column;gap:8px;';
            document.body.appendChild(this.container);
        }
    },

    show(mensaje, tipo = 'info', duracion = 4000) {
        this.init();

        const colores = {
            success: '#27AE60',
            error:   '#E74C3C',
            warning: '#F39C12',
            info:    '#3498DB',
        };

        const toast = document.createElement('div');
        toast.style.cssText = `background:${colores[tipo] || '#3498DB'};color:#fff;padding:12px 20px;border-radius:8px;font-family:Inter,sans-serif;font-size:14px;font-weight:500;box-shadow:0 4px 12px rgba(0,0,0,0.15);min-width:280px;transition:opacity 0.3s;`;
        toast.textContent = mensaje;

        this.container.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            setTimeout(() => toast.remove(), 300);
        }, duracion);
    },

    success: (msg) => Toast.show(msg, 'success'),
    error:   (msg) => Toast.show(msg, 'error'),
    warning: (msg) => Toast.show(msg, 'warning'),
    info:    (msg) => Toast.show(msg, 'info'),
};

// ============================================================
// Helpers de formulario
// ============================================================

/**
 * Deshabilitar botón de submit al enviar formulario (evitar doble click).
 */
function protegerFormularios() {
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function () {
            const btn = this.querySelector('button[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.textContent = 'Procesando...';
            }
        });
    });
}

/**
 * Formatear número como moneda colombiana (COP).
 * @param {number} valor
 * @returns {string}
 */
function formatearCOP(valor) {
    return new Intl.NumberFormat('es-CO', {
        style: 'currency',
        currency: 'COP',
        minimumFractionDigits: 0,
    }).format(valor);
}

/**
 * Formatear fecha a formato local colombiano: dd/mm/yyyy
 * @param {string} fechaISO
 * @returns {string}
 */
function formatearFecha(fechaISO) {
    if (!fechaISO) return '—';
    const [anio, mes, dia] = fechaISO.split('-');
    return `${dia}/${mes}/${anio}`;
}

// ============================================================
// Inicialización
// ============================================================
document.addEventListener('DOMContentLoaded', () => {
    protegerFormularios();

    // Mostrar toasts desde atributos data (inyectados por el servidor PHP)
    const flashMsg = document.querySelector('[data-flash-msg]');
    if (flashMsg) {
        const tipo = flashMsg.dataset.flashTipo || 'info';
        Toast.show(flashMsg.dataset.flashMsg, tipo);
    }
});

// ============================================================
// Sidebar Toggle (Mobile)
// ============================================================
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar && overlay) {
        sidebar.classList.toggle('open');
        overlay.classList.toggle('visible');
    }
}

function openSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar && overlay) {
        sidebar.classList.add('open');
        overlay.classList.add('visible');
    }
}

function closeSidebar() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar && overlay) {
        sidebar.classList.remove('open');
        overlay.classList.remove('visible');
    }
}
