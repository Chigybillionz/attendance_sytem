// File: frontend/src/stores/theme.js
// Location: frontend/src/stores/theme.js
// THEME MANAGEMENT STORE

import { defineStore } from "pinia";

export const useThemeStore = defineStore("theme", {
  state: () => ({
    isDarkMode: false,
    systemPreference: "light",
  }),

  getters: {
    currentTheme: (state) => (state.isDarkMode ? "dark" : "light"),

    // Theme-specific classes
    themeClasses: (state) => ({
      // Background classes
      bg: state.isDarkMode ? "bg-gray-900" : "bg-gray-50",
      bgPrimary: state.isDarkMode ? "bg-gray-800" : "bg-white",
      bgSecondary: state.isDarkMode ? "bg-gray-700" : "bg-gray-100",
      bgCard: state.isDarkMode ? "bg-gray-800" : "bg-white",

      // Text classes
      text: state.isDarkMode ? "text-gray-100" : "text-gray-900",
      textSecondary: state.isDarkMode ? "text-gray-300" : "text-gray-600",
      textMuted: state.isDarkMode ? "text-gray-400" : "text-gray-500",

      // Border classes
      border: state.isDarkMode ? "border-gray-600" : "border-gray-200",
      borderLight: state.isDarkMode ? "border-gray-700" : "border-gray-100",

      // Hover states
      hover: state.isDarkMode ? "hover:bg-gray-700" : "hover:bg-gray-50",
      hoverCard: state.isDarkMode ? "hover:bg-gray-700" : "hover:bg-gray-100",

      // Input classes
      input: state.isDarkMode
        ? "bg-gray-700 border-gray-600 text-gray-100 placeholder-gray-400 focus:ring-blue-500 focus:border-blue-500"
        : "bg-white border-gray-300 text-gray-900 placeholder-gray-500 focus:ring-blue-500 focus:border-blue-500",

      // Shadow classes
      shadow: state.isDarkMode ? "shadow-gray-900/20" : "shadow-gray-200",
      shadowLg: state.isDarkMode ? "shadow-xl shadow-gray-900/30" : "shadow-lg shadow-gray-200/50",
    }),
  },

  actions: {
    // Initialize theme from localStorage or system preference
    initializeTheme() {
      const savedTheme = localStorage.getItem("theme");

      if (savedTheme) {
        this.isDarkMode = savedTheme === "dark";
      } else {
        // Check system preference
        this.detectSystemPreference();
        this.isDarkMode = this.systemPreference === "dark";
      }

      this.applyTheme();
      this.watchSystemPreference();
    },

    // Toggle between light and dark mode
    toggleTheme() {
      this.isDarkMode = !this.isDarkMode;
      this.saveTheme();
      this.applyTheme();
    },

    // Set specific theme
    setTheme(theme) {
      this.isDarkMode = theme === "dark";
      this.saveTheme();
      this.applyTheme();
    },

    // Apply theme to document
    applyTheme() {
      const html = document.documentElement;

      if (this.isDarkMode) {
        html.classList.add("dark");
        html.classList.remove("light");
      } else {
        html.classList.add("light");
        html.classList.remove("dark");
      }
    },

    // Save theme preference to localStorage
    saveTheme() {
      localStorage.setItem("theme", this.isDarkMode ? "dark" : "light");
    },

    // Detect system color scheme preference
    detectSystemPreference() {
      if (typeof window !== "undefined" && window.matchMedia) {
        this.systemPreference = window.matchMedia("(prefers-color-scheme: dark)").matches
          ? "dark"
          : "light";
      }
    },

    // Watch for system preference changes
    watchSystemPreference() {
      if (typeof window !== "undefined" && window.matchMedia) {
        const mediaQuery = window.matchMedia("(prefers-color-scheme: dark)");

        mediaQuery.addEventListener("change", (e) => {
          this.systemPreference = e.matches ? "dark" : "light";

          // Only auto-switch if user hasn't set a preference
          const savedTheme = localStorage.getItem("theme");
          if (!savedTheme) {
            this.isDarkMode = e.matches;
            this.applyTheme();
          }
        });
      }
    },

    // Reset to system preference
    resetToSystem() {
      localStorage.removeItem("theme");
      this.detectSystemPreference();
      this.isDarkMode = this.systemPreference === "dark";
      this.applyTheme();
    },
  },
});
