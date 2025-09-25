<!-- File: frontend/src/components/layout/Header.vue -->
<!-- Location: frontend/src/components/layout/Header.vue -->
<!-- UPDATED WITH THEME SUPPORT -->

<template>
  <header :class="[
    'shadow-sm border-b fixed w-full top-0 z-40 transition-colors',
    themeStore.themeClasses.bgPrimary,
    themeStore.themeClasses.border
  ]">
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
            <h1 :class="['text-xl font-semibold', themeStore.themeClasses.text]">
              Attendance System
            </h1>
          </div>
        </div>

        <!-- Right side - Theme toggle and User menu -->
        <div v-if="authStore.isAuthenticated" class="flex items-center space-x-4">
          <!-- Theme Toggle -->
          <ThemeToggle />

          <!-- Current time -->
          <div :class="['hidden md:block text-sm', themeStore.themeClasses.textMuted]">
            {{ currentTime }}
          </div>

          <!-- Greeting -->
          <div :class="['hidden sm:block text-sm', themeStore.themeClasses.textSecondary]">
            {{ greeting }}, {{ authStore.userName }}!
          </div>

          <!-- User dropdown -->
          <div class="relative" ref="dropdownRef">
            <button
              @click="toggleDropdown"
              :class="[
                'flex items-center space-x-2 px-3 py-2 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500',
                themeStore.themeClasses.bgSecondary,
                themeStore.themeClasses.hover
              ]"
            >
              <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center">
                <span class="text-white text-sm font-medium">
                  {{ authStore.userName.charAt(0).toUpperCase() }}
                </span>
              </div>
              <svg :class="['w-4 h-4', themeStore.themeClasses.textMuted]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
              </svg>
            </button>

            <!-- Dropdown menu -->
            <div
              v-show="showDropdown"
              :class="[
                'absolute right-0 mt-2 w-56 rounded-lg shadow-lg border py-1 z-50',
                themeStore.themeClasses.bgCard,
                themeStore.themeClasses.border,
                themeStore.themeClasses.shadowLg
              ]"
            >
              <!-- User info -->
              <div :class="['px-4 py-3 border-b', themeStore.themeClasses.borderLight]">
                <p :class="['text-sm font-medium', themeStore.themeClasses.text]">{{ authStore.userName }}</p>
                <p :class="['text-xs', themeStore.themeClasses.textMuted]">{{ authStore.userEmail }}</p>
                <p class="text-xs text-blue-600 font-medium mt-1">
                  {{ authStore.user?.role?.toUpperCase() }} | {{ authStore.user?.employee_id }}
                </p>
              </div>
              
              <!-- Menu items -->
              <router-link
                to="/profile"
                @click="closeDropdown"
                :class="[
                  'flex items-center px-4 py-2 text-sm transition-colors',
                  themeStore.themeClasses.text,
                  themeStore.themeClasses.hover
                ]"
              >
                <svg :class="['w-4 h-4 mr-3', themeStore.themeClasses.textMuted]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                </svg>
                My Profile
              </router-link>
              
              <!-- Admin-only link -->
              <router-link
                v-if="authStore.isAdmin"
                to="/admin/users"
                @click="closeDropdown"
                :class="[
                  'flex items-center px-4 py-2 text-sm transition-colors',
                  themeStore.themeClasses.text,
                  themeStore.themeClasses.hover
                ]"
              >
                <svg :class="['w-4 h-4 mr-3', themeStore.themeClasses.textMuted]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
                Manage Users
              </router-link>

              <div :class="['border-t my-1', themeStore.themeClasses.borderLight]"></div>
              
              <!-- Logout button -->
              <button
                @click="handleLogout"
                :disabled="loggingOut"
                class="flex items-center w-full px-4 py-2 text-sm text-red-600 dark:text-red-400 hover:bg-red-50 dark:hover:bg-red-900/20 transition-colors disabled:opacity-50"
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

          <!-- Mobile logout button -->
          <button
            @click="handleLogout"
            :disabled="loggingOut"
            class="md:hidden flex items-center px-3 py-2 text-sm text-red-600 dark:text-red-400 bg-red-50 dark:bg-red-900/20 hover:bg-red-100 dark:hover:bg-red-900/40 rounded-lg transition-colors disabled:opacity-50"
          >
            <svg v-if="!loggingOut" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
            </svg>
            <svg v-else class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
              <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
              <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 714 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
            </svg>
            {{ loggingOut ? 'Out' : 'Logout' }}
          </button>
        </div>

        <!-- Login button for guests -->
        <div v-else class="flex items-center space-x-4">
          <!-- Theme toggle for guests -->
          <ThemeToggle />
          
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
import { useThemeStore } from '@/stores/theme'
import { getGreeting } from '@/utils/helpers'
import ThemeToggle from '@/components/common/ThemeToggle.vue'

const authStore = useAuthStore()
const themeStore = useThemeStore()
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
  
  if (!confirm('Are you sure you want to sign out?')) {
    closeDropdown()
    return
  }

  loggingOut.value = true
  
  try {
    await authStore.logout()
    closeDropdown()
    window.showNotification?.('Logged out successfully', 'success')
    router.push('/login')
  } catch (error) {
    console.error('Logout error:', error)
    window.showNotification?.('Logout failed, but you have been signed out locally', 'error')
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
  
  onUnmounted(() => {
    clearInterval(timeInterval)
    document.removeEventListener('click', handleClickOutside)
  })
})
</script>