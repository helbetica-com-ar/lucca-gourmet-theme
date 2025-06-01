/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './*.php',
    './templates/**/*.php',
    './woocommerce/**/*.php',
    './js/**/*.js',
    './src/**/*.{html,js,php}',
    // Include safelist for classes that might not be detected
  ],
  safelist: [
    'bg-lucca-dark',
    'bg-lucca-gray',
    'bg-lucca-orange',
    'text-lucca-light-gray',
    'border-gray-700',
    'border-gray-800',
    'bg-gray-800',
    'bg-gray-700',
    'rounded-button',
    'rounded-lg',
    'lucca-card',
    'lucca-button',
    'link-button',
    'hover:bg-gray-700',
    'hover:shadow-lg',
    'hover:-translate-y-1',
    'hover:-translate-y-0.5',
    'shadow-sm',
    'transition-all',
    'duration-200',
    'bg-gradient-lucca',
    // Complete color palette
    'text-white',
    'text-gray-400',
    'backdrop-blur-sm',
    // Specific responsive classes
    'sm:flex-row',
    'flex-col'
  ],
  theme: {
    extend: {
      colors: {
        primary: "#4f46e5",
        secondary: "#818cf8",
        'lucca-orange': "#f39c12",
        'lucca-dark': "#131314",
        'lucca-gray': "#1a1a1b",
        'lucca-light-gray': "#dad0d6"
      },
      borderRadius: {
        none: "0px",
        sm: "4px",
        DEFAULT: "8px",
        md: "12px",
        lg: "16px",
        xl: "20px",
        "2xl": "24px",
        "3xl": "32px",
        full: "9999px",
        button: "8px",
      },
      fontFamily: {
        'playfair': ['Playfair Display', 'serif'],
        'pacifico': ['Pacifico', 'cursive'],
        'helvetica': ['Helvetica Neue', 'Helvetica', 'Arial', 'sans-serif']
      },
      animation: {
        'fade-in': 'fadeIn 0.5s ease-in-out',
        'slide-up': 'slideUp 0.3s ease-out',
        'hover-lift': 'hoverLift 0.2s ease-out'
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' }
        },
        slideUp: {
          '0%': { transform: 'translateY(10px)', opacity: '0' },
          '100%': { transform: 'translateY(0)', opacity: '1' }
        },
        hoverLift: {
          '0%': { transform: 'translateY(0)' },
          '100%': { transform: 'translateY(-2px)' }
        }
      }
    },
  },
  plugins: [],
}