<!-- File: frontend/src/components/common/ThemeToggle.vue -->
<!-- Location: frontend/src/components/common/ThemeToggle.vue -->

<template>
  <div class="relative">
    <!-- Theme Toggle Button -->
    <button
      @click="toggleDropdown"
      :class="[
        'flex items-center px-3 py-2 rounded-lg transition-all duration-200',
        'focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2',
        themeStore.themeClasses.bgSecondary,
        themeStore.themeClasses.text,
        themeStore.themeClasses.hover
      ]"
      :title="`Current theme: ${themeStore.currentTheme}`"
    >
      <!-- Sun Icon (Light Mode) -->
      <svg
        v-if="!themeStore.isDarkMode"
        class="w-5 h-5 text-yellow-500"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"
        />
      </svg>
      
      <!-- Moon Icon (Dark Mode) -->
      <svg
        v-else
        class="w-5 h-5 text-blue-400"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"
        />
      </svg>
      
      <span v-if="showLabel" class="ml-2 text-sm font-medium">
        {{ themeStore.isDarkMode ? 'Dark' : 'Light' }}
      </span>
      
      <svg v-if="showDropdown" class="w-4 h-4 ml-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
      </svg>
    </button>

    <!-- Dropdown Menu -->
    <div
      v-show="dropdownOpen && showDropdown"
      :class="[
        'absolute right-0 mt-2 w-48 py-1 rounded-lg shadow-lg border z-50',
        themeStore.themeClasses.bgCard,
        themeStore.themeClasses.border,
        themeStore.themeClasses.shadowLg
      ]"
    >
      <!-- Light Mode Option -->
      <button
        @click="setTheme('light')"
        :class="[
          'flex items-center w-full px-4 py-2 text-left text-sm transition-colors',
          themeStore.themeClasses.text,
          themeStore.themeClasses.hover,
          !themeStore.isDarkMode ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300' : ''
        ]"
      >
        <svg class="w-4 h-4 mr-3 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
        </svg>
        Light Mode
        <svg v-if="!themeStore.isDarkMode" class="w-4 h-4 ml-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
      </button>

      <!-- Dark Mode Option -->
      <button
        @click="setTheme('dark')"
        :class="[
          'flex items-center w-full px-4 py-2 text-left text-sm transition-colors',
          themeStore.themeClasses.text,
          themeStore.themeClasses.hover,
          themeStore.isDarkMode ? 'bg-blue-50 text-blue-700 dark:bg-blue-900/20 dark:text-blue-300' : ''
        ]"
      >
        <svg class="w-4 h-4 mr-3 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
        </svg>
        Dark Mode
        <svg v-if="themeStore.isDarkMode" class="w-4 h-4 ml-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
      </button>

      <!-- System Preference Option -->
      <div class="border-t border-gray-200 dark:border-gray-700 my-1"></div>
      <button
        @click="resetToSystem"
        :class="[
          'flex items-center w-full px-4 py-2 text-left text-sm transition-colors',
          themeStore.themeClasses.textSecondary,
          themeStore.themeClasses.hover
        ]"
      >
        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
        </svg>
        System Preference
      </button>
    </div>

    <!-- Click outside to close -->
    <div
      v-if="dropdownOpen"
      @click="closeDropdown"
      class="fixed inset-0 z-40"
    ></div>
  </div>
</template>

<script setup>
import { ref, onMounted, onUnmounted } from 'vue'
import { useThemeStore } from '@/stores/theme'

// Props
defineProps({
  showLabel: {
    type: Boolean,
    default: false
  },
  showDropdown: {
    type: Boolean,
    default: true
  }
})

const themeStore = useThemeStore()
const dropdownOpen = ref(false)

// Methods
const toggleDropdown = () => {
  if (props.showDropdown) {
    dropdownOpen.value = !dropdownOpen.value
  } else {
    themeStore.toggleTheme()
  }
}

const closeDropdown = () => {
  dropdownOpen.value = false
}

const setTheme = (theme) => {
  themeStore.setTheme(theme)
  closeDropdown()
  
  // Show notification
  window.showNotification?.(
    `Switched to ${theme} mode`, 
    'success'
  )
}

const resetToSystem = () => {
  themeStore.resetToSystem()
  closeDropdown()
  
  window.showNotification?.(
    `Using system preference (${themeStore.systemPreference} mode)`, 
    'info'
  )
}

// Handle keyboard navigation
const handleKeydown = (event) => {
  if (event.key === 'Escape' && dropdownOpen.value) {
    closeDropdown()
  }
}

onMounted(() => {
  document.addEventListener('keydown', handleKeydown)
})

onUnmounted(() => {
  document.removeEventListener('keydown', handleKeydown)
})
</script>

<style scoped>
/* Smooth theme transition */
* {
  transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease;
}

/* Custom focus styles for dark mode */
.dark button:focus {
  @apply ring-blue-400 ring-offset-gray-800;
}
</style>