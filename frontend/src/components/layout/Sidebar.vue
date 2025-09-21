<!-- File: frontend/src/components/layout/Sidebar.vue -->
<!-- Location: frontend/src/components/layout/Sidebar.vue -->
<!-- UPDATED WITH LOGOUT BUTTON -->

<template>
  <aside class="bg-white shadow-lg border-r border-gray-200 w-64 fixed left-0 top-20 h-full overflow-y-auto z-30">
    <nav class="p-4">
      <!-- Admin Navigation -->
      <div v-if="authStore.isAdmin">
        <div class="mb-6">
          <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">
            Admin Panel
          </h3>
          
          <ul class="space-y-2">
            <li>
              <router-link
                to="/admin/dashboard"
                class="nav-link"
                :class="{ 'nav-link-active': isActiveRoute('/admin/dashboard') }"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z" />
                </svg>
                Dashboard
              </router-link>
            </li>
            
            <li>
              <router-link
                to="/admin/users"
                class="nav-link"
                :class="{ 'nav-link-active': isActiveRoute('/admin/users') }"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z" />
                </svg>
                Manage Users
              </router-link>
            </li>
            
            <li>
              <router-link
                to="/admin/reports"
                class="nav-link"
                :class="{ 'nav-link-active': isActiveRoute('/admin/reports') }"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                </svg>
                Reports
              </router-link>
            </li>
          </ul>
        </div>
      </div>

      <!-- Worker Navigation -->
      <div v-if="authStore.isWorker">
        <div class="mb-6">
          <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">
            My Workspace
          </h3>
          
          <ul class="space-y-2">
            <li>
              <router-link
                to="/worker/dashboard"
                class="nav-link"
                :class="{ 'nav-link-active': isActiveRoute('/worker/dashboard') }"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2H5a2 2 0 00-2-2z" />
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5a2 2 0 012-2h4a2 2 0 012 2v6H8V5z" />
                </svg>
                My Dashboard
              </router-link>
            </li>
            
            <li>
              <router-link
                to="/worker/attendance"
                class="nav-link"
                :class="{ 'nav-link-active': isActiveRoute('/worker/attendance') }"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Clock In/Out
              </router-link>
            </li>
            
            <li>
              <router-link
                to="/worker/history"
                class="nav-link"
                :class="{ 'nav-link-active': isActiveRoute('/worker/history') }"
              >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                  <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                </svg>
                My History
              </router-link>
            </li>
          </ul>
        </div>
      </div>

      <!-- Common Navigation -->
      <div class="border-t border-gray-200 pt-4 mb-6">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">
          Account
        </h3>
        
        <ul class="space-y-2">
          <li>
            <router-link
              to="/profile"
              class="nav-link"
              :class="{ 'nav-link-active': isActiveRoute('/profile') }"
            >
              <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              My Profile
            </router-link>
          </li>
          
          <!-- LOGOUT BUTTON IN SIDEBAR -->
          <li>
            <button
              @click="handleLogout"
              :disabled="loggingOut"
              class="nav-link w-full text-left text-red-600 hover:bg-red-50 hover:text-red-700 disabled:opacity-50"
            >
              <svg v-if="!loggingOut" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
              <svg v-else class="animate-spin w-5 h-5" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ loggingOut ? 'Signing out...' : 'Sign Out' }}
            </button>
          </li>
        </ul>
      </div>

      <!-- Quick Stats (for workers) -->
      <div v-if="authStore.isWorker && dashboardStore.workerDashboard" class="pt-4 border-t border-gray-200">
        <h3 class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">
          Quick Stats
        </h3>
        
        <div class="space-y-3">
          <div class="bg-blue-50 rounded-lg p-3">
            <div class="text-sm text-blue-600 font-medium">This Month</div>
            <div class="text-lg font-bold text-blue-900">
              {{ dashboardStore.workerMonthlyStats?.days_worked || 0 }} days
            </div>
          </div>
          
          <div class="bg-green-50 rounded-lg p-3">
            <div class="text-sm text-green-600 font-medium">Total Hours</div>
            <div class="text-lg font-bold text-green-900">
              {{ Math.round(dashboardStore.workerMonthlyStats?.total_hours || 0) }}h
            </div>
          </div>
        </div>
      </div>
    </nav>
  </aside>
</template>

<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useDashboardStore } from '@/stores/dashboard'

const route = useRoute()
const router = useRouter()
const authStore = useAuthStore()
const dashboardStore = useDashboardStore()

// Component state
const loggingOut = ref(false)

// Check if route is active
const isActiveRoute = (path) => {
  return route.path === path || route.path.startsWith(path + '/')
}

// Handle logout
const handleLogout = async () => {
  if (loggingOut.value) return
  
  // Confirm logout
  if (!confirm('Are you sure you want to sign out?')) {
    return
  }

  loggingOut.value = true
  
  try {
    await authStore.logout()
    
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

onMounted(async () => {
  if (authStore.isWorker && !dashboardStore.workerDashboard) {
    await dashboardStore.fetchWorkerDashboard()
  }
})
</script>