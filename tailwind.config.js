/** @type {import('tailwindcss').Config} */
const colors = require('tailwindcss/colors')

export default {
  darkMode: 'class',
  presets: [
    require("./vendor/wireui/wireui/tailwind.config.js"),
    require("./vendor/power-components/livewire-powergrid/tailwind.config.js"), 
  ],
  content: [
    // "./resources/css/app.css",
    // "./resources/js/app.js",
    "./vendor/wireui/wireui/src/*.php",
    "./vendor/wireui/wireui/ts/**/*.ts",
    "./vendor/wireui/wireui/src/WireUi/**/*.php",
    "./vendor/wireui/wireui/src/Components/**/*.php",
    './app/Livewire/**/*Table.php',
    './vendor/power-components/livewire-powergrid/resources/views/**/*.php',
    './vendor/power-components/livewire-powergrid/src/Themes/Tailwind.php'
  ],
  theme: {
    extend: {
      colors: {
        primary: colors.indigo,
        secondary: colors.gray,
        positive: colors.emerald,
        negative: colors.red,
        warning: colors.amber,
        info: colors.blue,
        "pg-primary": colors.gray, 
      },
    },
  },
  plugins: [
    require('@tailwindcss/forms'),
  ],
}

