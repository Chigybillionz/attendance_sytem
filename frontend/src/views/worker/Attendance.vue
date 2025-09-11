
<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <h1 class="text-2xl font-bold text-gray-900">Clock In / Clock Out</h1>
      <p class="text-gray-600 mt-1">Manage your daily attendance</p>
    </div>

    <!-- Current Time & Status -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-8">
      <div class="text-center">
        <div class="mb-6">
          <p class="text-sm text-gray-500 mb-2">Current Time</p>
          <p class="text-4xl font-bold text-gray-900">{{ currentTime }}</p>
          <p class="text-lg text-gray-600 mt-2">{{ todayDate }}</p>
        </div>

        <!-- Today's Status -->
        <div v-if="todayAttendance" class="mb-8">
          <div class="inline-flex items-center space-x-2 bg-gray-100 rounded-full px-4 py-2">
            <div class="w-3 h-3 rounded-full" :class="{
              'bg-green-500': todayAttendance.status === 'present',
              'bg-yellow-500': todayAttendance.status === 'late',
              'bg-red-500': todayAttendance.status === 'absent'
            }"></div>
            <span class="text-sm font-medium capitalize">{{ todayAttendance.status }}</span>
          </div>
        </div>

        <!-- Action Buttons -->
        <div class="flex justify-center space-x-4">
          <button
            v-if="canClockIn"
            @click="clockIn"
            :disabled="loading"
            class="bg-green-600 hover:bg-green-700 text-white font-semibold py-4 px-8 rounded-lg text-lg transition-colors"
            :class="{ 'opacity-50 cursor-not-allowed': loading }"
          >
            <svg class="w-6 h-6 mr-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Clock In
          </button>
          
          <button
            v-if="canClockOut"
            @click="clockOut"
            :disabled="loading"
            class="bg-red-600 hover:bg-red-700 text-white font-semibold py-4 px-8 rounded-lg text-lg transition-colors"
            :class="{ 'opacity-50 cursor-not-allowed': loading }"
          >
            <svg class="w-6 h-6 mr-3 inline" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            Clock Out
          </button>
          
          <div v-if="!canClockIn && !canClockOut" class="text-center">
            <p class="text-gray-500 mb-4">You have completed attendance for today</p>
            <div class="bg-blue-50 rounded-lg p-4">
              <p class="text-sm text-blue-600">Total Hours: {{ todayAttendance?.total_hours || 0 }}h</p>
            </div>
          </div>
        </div>
      </div>
    </div>

    <!-- Today's Details -->
    <div v-if="todayAttendance" class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Today's Details</h2>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="text-center">
          <svg class="w-8 h-8 text-green-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="text-sm text-gray-500">Clock In Time</p>
          <p class="text-xl font-semibold text-gray-900">{{ formatTime(todayAttendance.clock_in_time) }}</p>
        </div>
        
        <div class="text-center">
          <svg class="w-8 h-8 text-red-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="text-sm text-gray-500">Clock Out Time</p>
          <p class="text-xl font-semibold text-gray-900">{{ formatTime(todayAttendance.clock_out_time) }}</p>
        </div>
        
        <div class="text-center">
          <svg class="w-8 h-8 text-blue-500 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
          </svg>
          <p class="text-sm text-gray-500">Total Hours</p>
          <p class="text-xl font-semibold text-gray-900">{{ todayAttendance.total_hours || 0 }}h</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted, onUnmounted } from 'vue'
import { useAttendanceStore } from '@/stores/attendance'
import { useDashboardStore } from '@/stores/dashboard'
import { formatDate, formatTime } from '@/utils/helpers'

const attendanceStore = useAttendanceStore()
const dashboardStore = useDashboardStore()

const loading = ref(false)
const currentTime = ref('')
let timeInterval

// Computed properties
const todayDate = computed(() => formatDate(new Date()))
const todayAttendance = computed(() => dashboardStore.workerTodayAttendance)
const canClockIn = computed(() => dashboardStore.canClockIn)
const canClockOut = computed(() => dashboardStore.canClockOut)

const updateTime = () => {
  currentTime.value = new Date().toLocaleTimeString('en-US', {
    hour: '2-digit',
    minute: '2-digit',
    second: '2-digit'
  })
}

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
  updateTime()
  timeInterval = setInterval(updateTime, 1000)
  await dashboardStore.fetchWorkerDashboard()
})

onUnmounted(() => {
  if (timeInterval) {
    clearInterval(timeInterval)
  }
})
</script>