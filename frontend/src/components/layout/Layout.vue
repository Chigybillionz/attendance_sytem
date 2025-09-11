<!-- File: frontend/src/components/layout/Layout.vue -->
<!-- Location: frontend/src/components/layout/Layout.vue -->

<template>
  <div class="min-h-screen bg-gray-50">
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
      class="fixed top-4 right-4 z-50 max-w-sm"
    >
      <div 
        :class="[
          'p-4 rounded-lg shadow-lg',
          notification.type === 'success' ? 'bg-green-100 text-green-800 border border-green-200' : '',
          notification.type === 'error' ? 'bg-red-100 text-red-800 border border-red-200' : '',
          notification.type === 'info' ? 'bg-blue-100 text-blue-800 border border-blue-200' : ''
        ]"
      >
        <div class="flex items-center justify-between">
          <span>{{ notification.message }}</span>
          <button 
            @click="clearNotification"
            class="ml-2 text-gray-500 hover:text-gray-700"
          >
            ×
          </button>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { computed, ref } from 'vue'
import { useAuthStore } from '@/stores/auth'
import Header from './Header.vue'
import Sidebar from './Sidebar.vue'
import LoadingSpinner from '@/components/common/LoadingSpinner.vue'

const authStore = useAuthStore()

// Global loading state
const isLoading = computed(() => {
  return authStore.loading
})

// Global notifications
const notification = ref(null)

const showNotification = (message, type = 'info') => {
  notification.value = { message, type }
  setTimeout(() => {
    clearNotification()
  }, 5000)
}

const clearNotification = () => {
  notification.value = null
}

// Make notification functions available globally
window.showNotification = showNotification
</script>
