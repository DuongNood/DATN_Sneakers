// tailwind.config.js
/** @type {import('tailwindcss').Config} */
export default {
  content: ['./index.html', './src/**/*.{js,ts,jsx,tsx}'],
  theme: {
    extend: {
      animation: {
        'blink-glow': 'blink-glow-animation 2s ease-in-out infinite'
      },
      keyframes: {
        'blink-glow-animation': {
          '0%': { textShadow: '0 0 5px rgba(0, 255, 255, 0.5), 0 0 15px rgba(0, 255, 255, 0.5)' },
          '50%': { textShadow: '0 0 10px rgba(0, 0, 255, 0.5), 0 0 20px rgba(0, 0, 255, 0.5)' },
          '100%': { textShadow: '0 0 5px rgba(0, 255, 255, 0.5), 0 0 15px rgba(0, 255, 255, 0.5)' }
        }
      }
    }
  },
  plugins: []
}
