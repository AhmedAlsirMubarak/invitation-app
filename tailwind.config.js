/** @type {import('tailwindcss').Config} */
export default {
  content: [
    './resources/**/*.blade.php',
    './resources/**/*.js',
  ],
  theme: {
    extend: {
      fontFamily: {
        display: ['"Playfair Display"', 'serif'],
        sans: ['"Inter"', 'sans-serif'],
      },
      colors: {
        ink: {
          950: '#0F1B16',
          900: '#16261F',
          800: '#1F362C',
        },
        gold: {
          400: '#D8B45C',
          500: '#C79A3B',
          600: '#A87B24',
        },
      },
    },
  },
  plugins: [],
}
