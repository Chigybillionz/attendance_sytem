<template>
  <div class="space-y-6">
    <!-- Welcome Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <div class="flex items-center justify-between">
        <div>
          <h1 class="text-2xl font-bold text-gray-900">
            {{ greeting }}, {{ authStore.userName }}!
          </h1>
          <p class="text-gray-600 mt-1">
            Employee ID: {{ authStore.user?.employee_id }} | {{ authStore.user?.department }}
          </p>
        </div>
        <div class="text-right">
          <p class="text-sm text-gray-500">Today's Date</p>
          <p class="text-lg font-semibold text-gray-900">{{ todayDate }}</p>
        </div>
      </div>
    </div>

    <!-- Quick Actions & Today's Status -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
      <!-- Clock In/Out Card -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Today's Attendance</h2>
        
        <div v-if="todayAttendance" class="space-y-4">
          <!-- Status Display -->
          <div class="flex items-center space-x-4">
            <div class="flex items-center space-x-2">
              <div class="w-3 h-3 rounded-full" :class="{
                'bg-green-500': todayAttendance.status === 'present',
                'bg-yellow-500': todayAttendance.status === 'late',
                'bg-red-500': todayAttendance.status === 'absent'
              }"></div>
              <span class="text-sm font-medium capitalize">{{ todayAttendance.status }}</span>
            </div>
          </div>

          <!-- Time Display -->
          <div class="grid grid-cols-2 gap-4">
            <div>
              <p class="text-sm text-gray-500">Clock In</p>
              <p class="text-lg font-semibold text-gray-900">
                {{ formatTime(todayAttendance.clock_in_time) }}
              </p>
            </div>
            <div>
              <p class="text-sm text-gray-500">Clock Out</p>
              <p class="text-lg font-semibold text-gray-900">
                {{ formatTime(todayAttendance.clock_out_time) }}
              </p>
            </div>
          </div>

          <!-- Total Hours -->
          <div v-if="todayAttendance.total_hours" class="bg-blue-50 rounded-lg p-3">
            <p class="text-sm text-blue-600">Total Hours Today</p>
            <p class="text-xl font-bold text-blue-900">{{ todayAttendance.total_hours }}h</p>
          </div>
        </div>

        <div v-else class="text-center py-8">
          <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="text-gray-500 mb-4">No attendance record for today</p>
        </div>

        <!-- Clock In/Out Buttons -->
        <div class="mt-6 space-y-3">
          <button
            v-if="canClockIn"
            @click="clockIn"
            :disabled="loading"
            class="w-full btn-primary"
            :class="{ 'opacity-50 cursor-not-allowed': loading }"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Clock In
          </button>
          
          <button
            v-if="canClockOut"
            @click="clockOut"
            :disabled="loading"
            class="w-full bg-red-600 hover:bg-red-700 text-white font-medium py-2 px-4 rounded-lg transition-colors"
            :class="{ 'opacity-50 cursor-not-allowed': loading }"
          >
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Clock Out
          </button>
        </div>
      </div>

      <!-- Monthly Stats -->
      <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">This Month's Summary</h2>
        
        <div v-if="monthlyStats" class="space-y-4">
          <div class="grid grid-cols-2 gap-4">
            <div class="text-center">
              <p class="text-2xl font-bold text-blue-600">{{ monthlyStats.days_worked }}</p>
              <p class="text-sm text-gray-500">Days Worked</p>
            </div>
            <div class="text-center">
              <p class="text-2xl font-bold text-green-600">{{ Math.round(monthlyStats.total_hours) }}h</p>
              <p class="text-sm text-gray-500">Total Hours</p>
            </div>
            <div class="text-center">
              <p class="text-2xl font-bold text-yellow-600">{{ monthlyStats.late_days }}</p>
              <p class="text-sm text-gray-500">Late Days</p>
            </div>
            <div class="text-center">
              <p class="text-2xl font-bold text-purple-600">{{ Math.round(monthlyStats.average_hours) }}h</p>
              <p class="text-sm text-gray-500">Avg/Day</p>
            </div>
          </div>
        </div>
        
        <div v-else class="text-center py-8 text-gray-500">
          No data for this month
        </div>
      </div>
    </div>

    <!-- Weekly Hours Chart -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Weekly Hours</h2>
      
      <div v-if="weeklyHours && weeklyHours.length" class="space-y-2">
        <div 
          v-for="day in weeklyHours" 
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
                :style="`width: ${Math.min((day.hours / 8) * 100, 100)}%`"
              ></div>
            </div>
            <div class="w-12 text-sm font-semibold text-gray-900">{{ day.hours }}h</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Recent Attendance -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Attendance</h2>
      
      <div v-if="recentAttendance && recentAttendance.length" class="overflow-x-auto">
        <table class="table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Clock In</th>
              <th>Clock Out</th>
              <th>Hours</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="record in recentAttendance" :key="record.id">
              <td>{{ formatDate(record.date) }}</td>
              <td>{{ formatTime(record.clock_in_time) }}</td>
              <td>{{ formatTime(record.clock_out_time) }}</td>
              <td>{{ record.total_hours || 0 }}h</td>
              <td>
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" 
                      :class="getStatusColor(record.status)">
                  {{ record.status }}
                </span>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <div v-else class="text-center py-8 text-gray-500">
        No attendance records found
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useAttendanceStore } from '@/stores/attendance'
import { useDashboardStore } from '@/stores/dashboard'
import { formatDate, formatTime, getGreeting, getStatusColor } from '@/utils/helpers'

const authStore = useAuthStore()
const attendanceStore = useAttendanceStore()
const dashboardStore = useDashboardStore()

const loading = ref(false)

// Computed properties
const greeting = computed(() => getGreeting())
const todayDate = computed(() => formatDate(new Date()))
const todayAttendance = computed(() => dashboardStore.workerTodayAttendance)
const monthlyStats = computed(() => dashboardStore.workerMonthlyStats)
const weeklyHours = computed(() => dashboardStore.workerWeeklyHours)
const recentAttendance = computed(() => dashboardStore.workerDashboard?.recent_attendance)
const canClockIn = computed(() => dashboardStore.canClockIn)
const canClockOut = computed(() => dashboardStore.canClockOut)

const clockIn = async () => {
  loading.value = true
  try {
    await attendanceStore.clockIn()
    await dashboardStore.fetchWorkerDashboard()
    window.showNotification?.('Clocked in successfully!', 'success')
  } catch (error) {
    window.showNotification?.('Failed to clock in', 'error')
  } finally {
    loading.value = false
  }
}

const clockOut = async () => {
  loading.value = true
  try {
    await attendanceStore.clockOut()
    await dashboardStore.fetchWorkerDashboard()
    window.showNotification?.('Clocked out successfully!', 'success')
  } catch (error) {
    window.showNotification?.('Failed to clock out', 'error')
  } finally {
    loading.value = false
  }
}

onMounted(async () => {
  await dashboardStore.fetchWorkerDashboard()
})
</script>
