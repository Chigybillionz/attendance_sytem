
<template>
  <header class="bg-white shadow-sm border-b border-gray-200 fixed w-full top-0 z-40">
    <div class="px-6 py-4">
      <div class="flex items-center justify-between">
        <!-- Left side - Logo and title -->
        <div class="flex items-center space-x-4">
          <div class="flex items-center space-x-2">
            <div class="w-8 h-8 bg-primary-600 rounded-lg flex items-center justify-center">
              <span class="text-white font-bold text-sm">A</span>
            </div>
            <h1 class="text-xl font-semibold text-gray-900">
              Attendance System
            </h1>
          </div>
        </div>

        <!-- Right side - User menu -->
        <div v-if="authStore.isAuthenticated" class="flex items-center space-x-4">
          <!-- Current time -->
          <div class="text-sm text-gray-500">
            {{ currentTime }}
          </div>

          <!-- Greeting -->
          <div class="text-sm text-gray-700">
            {{ greeting }}, {{ authStore.userName }}
          </div>

          <!-- User dropdown -->
          <div class="relative" ref="dropdownRef">
            <button
              @click="toggleDropdown"
              class="flex items-center space-x-2 bg-gray-50 hover:bg-gray-100 px-3 py-2 rounded-lg transition-colors"
            >
              <div class="w-8 h-8 bg-primary-600 rounded-full flex items-center justify-center">
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
              class="absolute right-0 mt-2 w-48 bg-white rounded-lg shadow-lg border border-gray-200 py-1 z-50"
            >
              <div class="px-4 py-2 border-b border-gray-100">
                <p class="text-sm font-medium text-gray-900">{{ authStore.userName }}</p>
                <p class="text-xs text-gray-500">{{ authStore.userEmail }}</p>
                <p class="text-xs text-primary-600 font-medium">{{ authStore.user?.role?.toUpperCase() }}</p>
              </div>
              
              <router-link
                to="/profile"
                class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50"
                @click="closeDropdown"
              >
                My Profile
              </router-link>
              
              <button
                @click="handleLogout"
                class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50"
              >
                Sign Out
              </button>
            </div>
          </div>
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

// Dropdown state
const showDropdown = ref(false)
const dropdownRef = ref(null)

// Current time
const currentTime = ref('')

// Greeting
const greeting = computed(() => getGreeting())

// Toggle dropdown
const toggleDropdown = () => {
  showDropdown.value = !showDropdown.value
}

const closeDropdown = () => {
  showDropdown.value = false
}

// Handle logout
const handleLogout = async () => {
  try {
    await authStore.logout()
    router.push('/login')
    window.showNotification?.('Logged out successfully', 'success')
  } catch (error) {
    window.showNotification?.('Logout failed', 'error')
  }
  closeDropdown()
}

// Update current time
const updateTime = () => {
  currentTime.value = new Date().toLocaleTimeString()
}

// Close dropdown when clicking outside
const handleClickOutside = (event) => {
  if (dropdownRef.value && !dropdownRef.value.contains(event.target)) {
    closeDropdown()
  }
}

onMounted(() => {
  updateTime()
  setInterval(updateTime, 1000)
  document.addEventListener('click', handleClickOutside)
})

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside)
})
</script>