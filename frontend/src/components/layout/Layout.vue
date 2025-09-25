<!-- File: frontend/src/components/layout/Layout.vue -->
<!-- Location: frontend/src/components/layout/Layout.vue -->
<!-- UPDATED WITH THEME SUPPORT -->

<template>
  <div :class="['min-h-screen transition-colors', themeStore.themeClasses.bg]">
    <!-- Header -->
    <Header />
    
    <!-- Main content area -->
    <div class="flex">
      <!-- Sidebar (only show if authenticated) -->
      <Sidebar v-if="authStore.isAuthenticated" />
      
      <!-- Main content -->
      <main 
        :class="[
          'flex-1 transition-all duration-300',
          authStore.isAuthenticated ? 'ml-64' : ''
        ]"
      >
        <div class="p-6">
          <!-- Page content -->
          <router-view />
        </div>
      </main>
    </div>

    <!-- Global loading spinner -->
    <LoadingSpinner v-if="isLoading" />
    
    <!-- Global notifications -->
    <div 
      v-if="notification" 
      class="fixed top-4 right-4 z-50 max-w-sm animate-slide-up"
    >
      <div 
        :class="[
          'p-4 rounded-lg shadow-lg border transition-all',
          notification.type === 'success' ? 'notification-success' : '',
          notification.type === 'error' ? 'notification-error' : '',
          notification.type === 'info' ? 'notification-info' : ''
        ]"
      >
        <div class="flex items-center justify-between">
          <div class="flex items-center">
            <!-- Success Icon -->
            <svg v-if="notification.type === 'success'" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            
            <!-- Error Icon -->
            <svg v-else-if="notification.type === 'error'" class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            
            <!-- Info Icon -->
            <svg v-else class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            
            <span class="font-medium">{{ notification.message }}</span>
          </div>
          
          <button 
            @click="clearNotification"
            :class="['ml-4 text-current opacity-70 hover:opacity-100 transition-opacity']"
          >
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>
      </div>
    </div>

    <!-- Theme transition overlay (subtle) -->
    <div 
      v-if="themeTransitioning"
      class="fixed inset-0 bg-black/20 z-50 pointer-events-none transition-opacity duration-300"
    ></div>
  </div>
</template>

<script setup>
import { ref, computed, watch, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useThemeStore } from '@/stores/theme'
import Header from './Header.vue'
import Sidebar from './Sidebar.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'

const authStore = useAuthStore()
const themeStore = useThemeStore()

// Global loading state
const isLoading = computed(() => {
  return authStore.loading
})

// Global notifications
const notification = ref(null)
const themeTransitioning = ref(false)

// Watch for theme changes to show transition effect
watch(() => themeStore.isDarkMode, () => {
  themeTransitioning.value = true
  setTimeout(() => {
    themeTransitioning.value = false
  }, 300)
})

const showNotification = (message, type = 'info') => {
  // Clear existing notification
  if (notification.value) {
    clearNotification()
  }
  
  // Show new notification after a brief delay
  setTimeout(() => {
    notification.value = { message, type }
    
    // Auto-dismiss after 5 seconds
    setTimeout(() => {
      if (notification.value?.message === message) {
        clearNotification()
      }
    }, 5000)
  }, 100)
}

const clearNotification = () => {
  if (notification.value) {
    // Fade out animation
    const notificationEl = document.querySelector('.animate-slide-up')
    if (notificationEl) {
      notificationEl.classList.add('opacity-0', 'translate-y-2')
      setTimeout(() => {
        notification.value = null
      }, 300)
    } else {
      notification.value = null
    }
  }
}

// Make notification functions available globally
onMounted(() => {
  window.showNotification = showNotification
})
</script>

<style scoped>
/* Enhanced animations for theme transitions */
@keyframes slideUp {
  from {
    opacity: 0;
    transform: translateY(10px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

.animate-slide-up {
  animation: slideUp 0.3s ease-out;
}

/* Smooth theme transitions */
* {
  transition: background-color 0.2s ease, color 0.2s ease, border-color 0.2s ease, box-shadow 0.2s ease;
}
</style>