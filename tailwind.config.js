/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    "./resources/**/*.blade.php",
    "./resources/**/*.js",
    "./resources/**/*.vue",
  ],
  darkMode: 'class', // Enable dark mode with class strategy
  theme: {
    extend: {
      colors: {
        primary: {
          900: '#0A0E27',
          700: '#1A1F4D',
          500: '#2D3FE6',
          300: '#6B7AFF',
          100: '#E5E8FF',
        },
        accent: {
          700: '#C41E3A',
          500: '#FF2D55',
          300: '#FF6B8A',
          100: '#FFE5EB',
        },
        success: {
          700: '#107C10',
          500: '#0F9D58',
          100: '#E7F6E9',
        },
        warning: {
          700: '#C87400',
          500: '#F9AB00',
          100: '#FFF4E0',
        },
        error: {
          700: '#C41E3A',
          500: '#F44336',
          100: '#FDECEA',
        },
        // Light mode
        'bg-primary': '#FFFFFF',
        'bg-secondary': '#F7F8FA',
        'bg-tertiary': '#EBEDF0',
        'text-primary': '#1C1E21',
        'text-secondary': '#65676B',
        'text-tertiary': '#B0B3B8',
        'border-light': '#E4E6EB',
        'border-medium': '#CCD0D5',
        'border-dark': '#8A8D91',
      },
      fontFamily: {
        sans: ['Inter', 'system-ui', 'sans-serif'],
        mono: ['SF Mono', 'Consolas', 'Monaco', 'monospace'],
        arabic: ['Cairo', 'Tajawal', 'sans-serif'],
      },
      fontSize: {
        'display-lg': ['57px', '64px'],
        'display-md': ['45px', '52px'],
        'display-sm': ['36px', '44px'],
        'h1': ['32px', '40px'],
        'h2': ['24px', '32px'],
        'h3': ['20px', '28px'],
        'h4': ['18px', '24px'],
        'body-lg': ['17px', '24px'],
        'body-md': ['15px', '22px'],
        'body-sm': ['13px', '18px'],
        'label-lg': ['16px', '20px'],
        'label-md': ['14px', '20px'],
        'label-sm': ['12px', '16px'],
        'caption': ['12px', '16px'],
        'overline': ['11px', '16px'],
      },
      spacing: {
        'xxs': '4px',
        'xs': '8px',
        'sm': '12px',
        'md': '16px',
        'lg': '24px',
        'xl': '32px',
        'xxl': '48px',
        'xxxl': '64px',
      },
      borderRadius: {
        'xs': '4px',
        'sm': '8px',
        'md': '12px',
        'lg': '16px',
        'xl': '24px',
      },
      boxShadow: {
        'level-1': '0 1px 2px rgba(0, 0, 0, 0.04)',
        'level-2': '0 2px 8px rgba(0, 0, 0, 0.08)',
        'level-3': '0 4px 16px rgba(0, 0, 0, 0.12)',
        'level-4': '0 8px 24px rgba(0, 0, 0, 0.16)',
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography'),
    require('@tailwindcss/aspect-ratio'),
  ],
}
