<!-- File: frontend/src/views/admin/Dashboard.vue -->
<!-- Location: frontend/src/views/admin/Dashboard.vue -->
<!-- UPDATED WITH LOGOUT BUTTON -->

<template>
  <div class="space-y-6">
    <!-- Welcome Header with Logout -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">Admin Dashboard</h1>
          <p class="text-gray-600 mt-1">Overview of attendance system</p>
        </div>
        <div class="flex items-center space-x-4">
          <!-- Current Date -->
          <div class="text-right hidden sm:block">
            <p class="text-sm text-gray-500">Today's Date</p>
            <p class="text-lg font-semibold text-gray-900">{{ todayDate }}</p>
          </div>
          
          <!-- Quick Logout Button -->
          <div class="flex items-center space-x-3">
            <div class="text-right">
              <p class="text-sm text-gray-500">Logged in as</p>
              <p class="text-sm font-semibold text-blue-600">{{ authStore.userName }} (Admin)</p>
            </div>
            
            <button
              @click="handleLogout"
              :disabled="loggingOut"
              class="flex items-center px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 disabled:opacity-50"
            >
              <svg v-if="!loggingOut" class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
              </svg>
              <svg v-else class="animate-spin w-4 h-4 mr-2" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
              </svg>
              {{ loggingOut ? 'Signing out...' : 'Switch Account' }}
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Quick Account Switcher -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
      <div class="flex items-center justify-between">
        <div class="flex items-center space-x-3">
          <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <div>
            <p class="text-sm font-medium text-blue-800">Testing Mode</p>
            <p class="text-xs text-blue-700">Quick switch to test worker account</p>
          </div>
        </div>
        
        <button
          @click="quickSwitchToWorker"
          :disabled="switchingAccount"
          class="px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50"
        >
          {{ switchingAccount ? 'Switching...' : 'Test Worker Account' }}
        </button>
      </div>
    </div>

    <!-- Overview Stats -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
      <div class="stat-card">
        <div class="flex items-center">
          <div class="p-3 bg-blue-100 rounded-lg">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-600">Total Employees</p>
            <p class="text-2xl font-bold text-gray-900">{{ overview.total_employees || 0 }}</p>
          </div>
        </div>
      </div>

      <div class="stat-card">
        <div class="flex items-center">
          <div class="p-3 bg-green-100 rounded-lg">
            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-600">Present Today</p>
            <p class="text-2xl font-bold text-gray-900">{{ overview.today_present || 0 }}</p>
          </div>
        </div>
      </div>

      <div class="stat-card">
        <div class="flex items-center">
          <div class="p-3 bg-yellow-100 rounded-lg">
            <svg class="w-8 h-8 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.996-.833-2.464 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-600">Late Today</p>
            <p class="text-2xl font-bold text-gray-900">{{ overview.today_late || 0 }}</p>
          </div>
        </div>
      </div>

      <div class="stat-card">
        <div class="flex items-center">
          <div class="p-3 bg-red-100 rounded-lg">
            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-600">Absent Today</p>
            <p class="text-2xl font-bold text-gray-900">{{ overview.today_absent || 0 }}</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Charts and Recent Activity -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Weekly Chart -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Weekly Attendance</h2>
        
        <div v-if="weeklyChart && weeklyChart.length" class="space-y-3">
          <div 
            v-for="day in weeklyChart" 
            :key="day.date"
            class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-gray-50"
          >
            <div class="flex items-center space-x-3">
              <div class="w-16 text-sm font-medium text-gray-600">{{ day.day }}</div>
              <div class="text-sm text-gray-500">{{ day.date }}</div>
            </div>
            <div class="flex items-center space-x-3">
              <div class="w-32 bg-gray-200 rounded-full h-2">
                <div 
                  class="bg-blue-600 h-2 rounded-full transition-all duration-300"
                  :style="`width: ${Math.min((day.attendance / overview.total_employees) * 100, 100)}%`"
                ></div>
              </div>
              <div class="w-8 text-sm font-semibold text-gray-900">{{ day.attendance }}</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Recent Attendance -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Activity</h2>
        
        <div v-if="recentAttendance && recentAttendance.length" class="space-y-3">
          <div 
            v-for="record in recentAttendance.slice(0, 5)" 
            :key="record.id"
            class="flex items-center justify-between py-2 px-3 rounded-lg hover:bg-gray-50"
          >
            <div>
              <p class="text-sm font-medium text-gray-900">{{ record.user.name }}</p>
              <p class="text-xs text-gray-500">{{ formatDate(record.date) }}</p>
            </div>
            <div class="text-right">
              <p class="text-sm text-gray-900">{{ formatTime(record.clock_in_time) }}</p>
              <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" 
                    :class="getStatusColor(record.status)">
                {{ capitalize(record.status) }}
              </span>
            </div>
          </div>
        </div>
        
        <div class="mt-4">
          <router-link to="/admin/reports" class="text-sm text-blue-600 hover:text-blue-500">
            View all reports →
          </router-link>
        </div>
      </div>
    </div>

    <!-- Department Stats -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Department Attendance Today</h2>
      
      <div v-if="departmentStats && departmentStats.length" class="overflow-x-auto">
        <table class="table">
          <thead>
            <tr>
              <th>Department</th>
              <th>Total Employees</th>
              <th>Present</th>
              <th>Absent</th>
              <th>Attendance Rate</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="dept in departmentStats" :key="dept.department">
              <td class="font-medium">{{ dept.department }}</td>
              <td>{{ dept.total_employees }}</td>
              <td class="text-green-600">{{ dept.present_today }}</td>
              <td class="text-red-600">{{ dept.absent_today }}</td>
              <td>
                <div class="flex items-center space-x-2">
                  <div class="flex-1 bg-gray-200 rounded-full h-2">
                    <div 
                      class="bg-green-500 h-2 rounded-full"
                      :style="`width: ${dept.attendance_rate}%`"
                    ></div>
                  </div>
                  <span class="text-sm font-medium">{{ dept.attendance_rate }}%</span>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'
import { useDashboardStore } from '@/stores/dashboard'
import { formatDate, formatTime, getStatusColor, capitalize } from '@/utils/helpers'

const router = useRouter()
const authStore = useAuthStore()
const dashboardStore = useDashboardStore()

// Component state
const loggingOut = ref(false)
const switchingAccount = ref(false)

// Computed properties
const todayDate = computed(() => formatDate(new Date()))
const overview = computed(() => dashboardStore.adminDashboard?.overview || {})
const weeklyChart = computed(() => dashboardStore.weeklyChart)
const recentAttendance = computed(() => dashboardStore.adminDashboard?.recent_attendance)
const departmentStats = computed(() => dashboardStore.departmentStats)

// Handle logout
const handleLogout = async () => {
  if (loggingOut.value) return
  
  if (!confirm('Are you sure you want to sign out?')) {
    return
  }

  loggingOut.value = true
  
  try {
    await authStore.logout()
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

// Quick switch to worker account (for testing)
const quickSwitchToWorker = async () => {
  if (switchingAccount.value) return
  
  if (!confirm('Switch to worker account for testing?\n\nCredentials:\nEmail: john@attendance.com\nPassword: worker123')) {
    return
  }

  switchingAccount.value = true
  
  try {
    // Logout current user
    await authStore.logout()
    
    // Auto-fill worker credentials and redirect to login
    localStorage.setItem('test_email', 'john@attendance.com')
    localStorage.setItem('test_password', 'worker123')
    
    window.showNotification?.('Switching to worker account...', 'info')
    router.push('/login')
  } catch (error) {
    console.error('Account switch error:', error)
    window.showNotification?.('Failed to switch account', 'error')
    router.push('/login')
  } finally {
    switchingAccount.value = false
  }
}

onMounted(async () => {
  await dashboardStore.fetchAdminDashboard()
})
</script>