module.exports = {
    content: [
      "./app.vue",
      "./views/*.{vue,js,ts,jsx,tsx}",
    ],
    theme: {
      extend: {
        colors: {
          primary: {
            50: '#fff7ed',
            500: '#f97316', // naranja
            600: '#ea580c',
          }
        },
        animation: {
          fade: 'fadeIn 0.3s ease-in-out',
        },
        keyframes: {
          fadeIn: {
            '0%': { opacity: '0' },
            '100%': { opacity: '1' },
          },
        },
      },
    },
    plugins: [],
  }