<!-- File: frontend/src/components/common/LogoutButton.vue -->
<!-- Location: frontend/src/components/common/LogoutButton.vue -->
<!-- STANDALONE LOGOUT COMPONENT (CAN BE USED ANYWHERE) -->

<template>
  <button
    @click="handleLogout"
    :disabled="loading"
    :class="buttonClasses"
    class="inline-flex items-center justify-center transition-colors focus:outline-none focus:ring-2 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed"
  >
    <svg v-if="!loading" :class="iconClasses" fill="none" stroke="currentColor" viewBox="0 0 24 24">
      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
    </svg>
    <svg v-else :class="iconClasses + ' animate-spin'" fill="none" viewBox="0 0 24 24">
      <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
      <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
    </svg>
    
    <span v-if="showText" :class="textClasses">
      {{ loading ? loadingText : 'Sign Out' }}
    </span>
  </button>
</template>

<script setup>
import { ref, computed } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

// Props
const props = defineProps({
  variant: {
    type: String,
    default: 'default', // 'default', 'danger', 'minimal'
    validator: (value) => ['default', 'danger', 'minimal'].includes(value)
  },
  size: {
    type: String,
    default: 'md', // 'sm', 'md', 'lg'
    validator: (value) => ['sm', 'md', 'lg'].includes(value)
  },
  showText: {
    type: Boolean,
    default: true
  },
  confirmLogout: {
    type: Boolean,
    default: true
  },
  loadingText: {
    type: String,
    default: 'Signing out...'
  }
})

// Emits
const emit = defineEmits(['logout-start', 'logout-success', 'logout-error'])

const router = useRouter()
const authStore = useAuthStore()
const loading = ref(false)

// Computed classes
const buttonClasses = computed(() => {
  const base = 'font-medium rounded-lg focus:ring-red-500'
  const variants = {
    default: 'bg-gray-100 text-gray-700 hover:bg-gray-200',
    danger: 'bg-red-600 text-white hover:bg-red-700',
    minimal: 'text-red-600 hover:text-red-700 hover:bg-red-50'
  }
  const sizes = {
    sm: 'px-2 py-1 text-sm',
    md: 'px-3 py-2 text-sm',
    lg: 'px-4 py-3 text-base'
  }
  
  return [base, variants[props.variant], sizes[props.size]]
})

const iconClasses = computed(() => {
  const sizes = {
    sm: 'w-4 h-4',
    md: 'w-4 h-4',
    lg: 'w-5 h-5'
  }
  
  return sizes[props.size] + (props.showText ? ' mr-2' : '')
})

const textClasses = computed(() => {
  return props.size === 'sm' ? 'text-xs' : ''
})

// Handle logout
const handleLogout = async () => {
  if (loading.value) return
  
  // Confirm logout if enabled
  if (props.confirmLogout) {
    if (!confirm('Are you sure you want to sign out?')) {
      return
    }
  }

  loading.value = true
  emit('logout-start')
  
  try {
    await authStore.logout()
    
    emit('logout-success')
    
    // Show success message
    window.showNotification?.('Logged out successfully', 'success')
    
    // Redirect to login page
    router.push('/login')
  } catch (error) {
    console.error('Logout error:', error)
    emit('logout-error', error)
    
    window.showNotification?.('Logout failed, but you have been signed out locally', 'error')
    
    // Still redirect to login even if server logout fails
    router.push('/login')
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/* Additional hover effects */
button:hover:not(:disabled) {
  transform: translateY(-1px);
}

button:active:not(:disabled) {
  transform: translateY(0);
}
</style>

  Basic usage:
  <LogoutButton />
  
  Danger style:
  <LogoutButton variant="danger" />
  
  Small icon only:
  <LogoutButton size="sm" :show-text="false" />
  
  Without confirmation:
  <LogoutButton :confirm-logout="false" />
  
  With event handlers:
  <LogoutButton 
    @logout-start="onLogoutStart"
    @logout-success="onLogoutSuccess" 
    @logout-error="onLogoutError"
  />