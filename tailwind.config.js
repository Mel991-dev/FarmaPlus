/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./views/**/*.php",
    "./public/assets/js/**/*.js"
  ],
  theme: {
    extend: {
      colors: {
        'fp-primary':      '#1A6B8A',
        'fp-primary-dark': '#1A3A4A',
        'fp-primary-light':'#4A9BB5',
        'fp-secondary':    '#2A9D8F',
        'fp-bg-main':      '#F4F9FC',
        'fp-bg-card':      '#E9F5F8',
        'fp-success':      '#27AE60',
        'fp-error':        '#E74C3C',
        'fp-warning':      '#F39C12',
        'fp-info':         '#3498DB',
        'fp-text':         '#2C3E50',
        'fp-muted':        '#7F8C8D',
        'fp-border':       '#BDC3C7',
      },
      fontFamily: {
        'sans':  ['Inter', 'sans-serif'],
        'mono':  ['JetBrains Mono', 'monospace'],
      },
      borderRadius: {
        'fp': '8px',
      }
    }
  },
  plugins: []
}
