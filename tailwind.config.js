/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: "class",
  theme: {
    extend: {
      colors: {
        primary: "#1A73E8",
        "primary-hover": "#1557B0",
        "background-light": "#F3F4F6",
        "background-dark": "#0B1116",
        "surface-light": "#FFFFFF",
        "surface-dark": "#161B22",
        "input-bg-dark": "#161B22",
        "input-border-dark": "#30363D",
        "text-light": "#111418",
        "text-dark": "#F9FAFB",
        "subtle-light": "#6B7280",
        "subtle-dark": "#9CA3AF",
        "border-light": "#DBE0E6",
        "border-dark": "#2A3B4C"
      },
      fontFamily: {
        display: ["Inter", "sans-serif"],
        body: ["Inter", "sans-serif"]
      },
      borderRadius: {
        DEFAULT: "0.375rem",
        lg: "0.5rem",
        xl: "0.75rem",
        full: "9999px"
      }
    }
  },
  plugins: [
    require('@tailwindcss/forms'),
    require('@tailwindcss/typography')
  ]
}