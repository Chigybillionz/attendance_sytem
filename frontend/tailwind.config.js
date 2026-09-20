// File: frontend/tailwind.config.js
// Location: frontend/tailwind.config.js
// UPDATED WITH DARK MODE SUPPORT

// /** @type {import('tailwindcss').Config} */
// export default {
//   content: ["./index.html", "./src/**/*.{vue,js,ts,jsx,tsx}"],
//   // Enable dark mode with class strategy
//   darkMode: "class",
//   theme: {
//     extend: {},
//   },
//   plugins: [],
// };
/** @type {import('tailwindcss').Config} */
export default {
  content: ["./index.html", "./src/**/*.{vue,js,ts,jsx,tsx}"],
  darkMode: "class", // ← Add this line
  theme: {
    extend: {},
  },
  plugins: [],
};
