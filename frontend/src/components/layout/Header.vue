<!-- File: frontend/src/components/layout/Header.vue -->
<!-- Location: frontend/src/components/layout/Header.vue -->
<!-- UPDATED WITH LOGOUT FUNCTIONALITY -->

<template>
  <header class="bg-white shadow-sm border-b border-gray-200 fixed w-full top-0 z-40">
    <div class="px-6 py-4">
      <div class="flex items-center justify-between">
        <!-- Left side - Logo and title -->
        <div class="flex items-center space-x-4">
          <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
              <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
              </svg>
            </div>
            <h1 class="text-xl font-semibold text-gray-900">
              Attendance System
            </h1>
          </div>
        </div>

        <!-- Right side - User menu -->
        <div v-if="authStore.isAuthenticated" class="flex items-center space-x-4">
          <!-- Current time -->
          <div class="hidden md:block text-sm text-gray-500">
            {{ currentTime }}
          </div>

          <!-- Greeting -->
          <div class="hidden sm:block text-sm text-gray-700">
            {{ greeting }}, {{ authStore.userName }}!
          </div>

          <!-- User dropdown -->
          <div class="relative" ref="dropdownRef">
            <button
              @click="toggleDropdown"
              class="flex items-center space-x-2 bg-gray-50 hover:bg-gray-100 px-3 py-2 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
              <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                <span class="text-white text-sm font-medium">
                  {{ authStore.userName.charAt(0).toUpperCase() }}
                </span>
              </div>
              <svg class="w-4 h-4 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- Dropdown menu -->
            <div
              v-show="showDropdown"
              class="absolute right-0 mt-2 w-56 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
            >
              <!-- User info -->
              <div class="px-4 py-3 border-b border-gray-100">
                <p class="text-sm font-medium text-gray-900">{{ authStore.userName }}</p>
                <p class="text-xs text-gray-500">{{ authStore.userEmail }}</p>
                <p class="text-xs text-blue-600 font-medium mt-1">
                  {{ authStore.user?.role?.toUpperCase() }} | {{ authStore.user?.employee_id }}
                </p>
              </div>
              
              <!-- Menu items -->
              <router-link
                to="/profile"
                @click="closeDropdown"
                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
              >
                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                My Profile
              </router-link>
              
              <!-- Admin-only link -->
              <router-link
                v-if="authStore.isAdmin"
                to="/admin/users"
                @click="closeDropdown"
                class="flex items-center px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors"
              >
                <svg class="w-4 h-4 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
                Manage Users
              </router-link>

              <div class="border-t border-gray-100 my-1"></div>
              
              <!-- Logout button -->
              <button
                @click="handleLogout"
                :disabled="loggingOut"
                class="flex items-center w-full px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition-colors disabled:opacity-50"
              >
                <svg v-if="!loggingOut" class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                </svg>
                <svg v-else class="animate-spin w-4 h-4 mr-3" fill="none" viewBox="0 0 24 24">
                  <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                  <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                {{ loggingOut ? 'Signing out...' : 'Sign Out' }}
              </button>
            </div>
          </div>

          <!-- Mobile logout button (visible on small screens) -->
          <button
            @click="handleLogout"
            :disabled="loggingOut"
            class="md:hidden flex items-center px-3 py-2 text-sm text-red-600 bg-red-50 hover:bg-red-100 rounded-lg transition-colors disabled:opacity-50"
          >
            <svg v-if="!loggingOut" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013 3v1" />
            </svg>
            <svg v-else class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ loggingOut ? 'Out' : 'Logout' }}
          </button>
        </div>

        <!-- Login button for guests -->
        <div v-else class="flex items-center space-x-2">
          <router-link
            to="/login"
            class="btn-primary"
          >
            Sign In
          </router-link>
        </div>
      </div>
    </div>
  </header>

  <!-- Spacer to prevent content from going under fixed header -->
  <div class="h-20"></div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { getGreeting } from '@/utils/helpers'

const authStore = useAuthStore()
const router = useRouter()

// Component state
const showDropdown = ref(false)
const dropdownRef = ref(null)
const currentTime = ref('')
const loggingOut = ref(false)

// Computed properties
const greeting = computed(() => getGreeting())

// Methods
const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value
}

const closeDropdown = () => {
  showDropdown.value = false
}

const handleLogout = async () => {
  if (loggingOut.value) return
  
  // Confirm logout
  if (!confirm('Are you sure you want to sign out?')) {
    closeDropdown()
    return
  }

  loggingOut.value = true
  
  try {
    await authStore.logout()
    closeDropdown()
    
    // Show success message
    window.showNotification?.('Logged out successfully', 'success')
    
    // Redirect to login page
    router.push('/login')
  } catch (error) {
    console.error('Logout error:', error)
    window.showNotification?.('Logout failed, but you have been signed out locally', 'error')
    
    // Still redirect to login even if server logout fails
    router.push('/login')
  } finally {
    loggingOut.value = false
  }
}

const updateTime = () => {
  currentTime.value = new Date().toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit'
  })
}

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    closeDropdown()
  }
}

// Lifecycle
onMounted(() => {
  updateTime()
  const timeInterval = setInterval(updateTime, 1000)
  document.addEventListener('click', handleClickOutside)
  
  // Clean up interval on unmount
  onUnmounted(() => {
    clearInterval(timeInterval)
    document.removeEventListener('click', handleClickOutside)
  })
})
</script>