<!-- File: frontend/src/views/worker/History.vue -->
<!-- Location: frontend/src/views/worker/History.vue -->

<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <h1 class="text-2xl font-bold text-gray-900">My Attendance History</h1>
      <p class="text-gray-600 mt-1">View your attendance records and history</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
          <input
            v-model="filters.start_date"
            type="date"
            class="input-field"
            @change="fetchHistory"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
          <input
            v-model="filters.end_date"
            type="date"
            class="input-field"
            @change="fetchHistory"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Month</label>
          <select v-model="filters.month" class="input-field" @change="fetchHistory">
            <option value="">All Months</option>
            <option v-for="(month, index) in months" :key="index" :value="index + 1">
              {{ month }}
            </option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Year</label>
          <select v-model="filters.year" class="input-field" @change="fetchHistory">
            <option v-for="year in years" :key="year" :value="year">{{ year }}</option>
          </select>
        </div>
      </div>
      
      <div class="mt-4 flex justify-end">
        <button @click="clearFilters" class="btn-secondary mr-3">Clear Filters</button>
        <button @click="fetchHistory" class="btn-primary">Apply Filters</button>
      </div>
    </div>

    <!-- Statistics Summary -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="stat-card">
        <div class="flex items-center">
          <div class="p-2 bg-blue-100 rounded-lg">
            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3a4 4 0 118 0v4M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-600">Total Days</p>
            <p class="text-2xl font-bold text-gray-900">{{ totalDays }}</p>
          </div>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="flex items-center">
          <div class="p-2 bg-green-100 rounded-lg">
            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-600">Total Hours</p>
            <p class="text-2xl font-bold text-gray-900">{{ totalHours }}h</p>
          </div>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="flex items-center">
          <div class="p-2 bg-yellow-100 rounded-lg">
            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.996-.833-2.464 0L3.732 16c-.77.833.192 2.5 1.732 2.5z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-600">Late Days</p>
            <p class="text-2xl font-bold text-gray-900">{{ lateDays }}</p>
          </div>
        </div>
      </div>
      
      <div class="stat-card">
        <div class="flex items-center">
          <div class="p-2 bg-purple-100 rounded-lg">
            <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-600">Avg Hours</p>
            <p class="text-2xl font-bold text-gray-900">{{ averageHours }}h</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Attendance Records -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
      <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Attendance Records</h2>
      </div>
      
      <div v-if="loading" class="p-6 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-2 text-gray-500">Loading...</p>
      </div>
      
      <div v-else-if="attendanceHistory.length" class="table-container">
        <table class="table">
          <thead>
            <tr>
              <th>Date</th>
              <th>Clock In</th>
              <th>Clock Out</th>
              <th>Total Hours</th>
              <th>Status</th>
              <th>Notes</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="record in attendanceHistory" :key="record.id">
              <td>{{ formatDate(record.date) }}</td>
              <td>{{ formatTime(record.clock_in_time) }}</td>
              <td>{{ formatTime(record.clock_out_time) }}</td>
              <td>{{ record.total_hours || 0 }}h</td>
              <td>
                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full" 
                      :class="getStatusColor(record.status)">
                  {{ capitalize(record.status) }}
                </span>
              </td>
              <td class="text-sm text-gray-500">{{ record.notes || '-' }}</td>
            </tr>
          </tbody>
        </table>
      </div>
      
      <div v-else class="p-6 text-center">
        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
        </svg>
        <p class="text-gray-500">No attendance records found</p>
      </div>

      <!-- Pagination -->
      <div v-if="pagination.last_page > 1" class="p-6 border-t border-gray-200">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-700">
            Showing {{ (pagination.current_page - 1) * pagination.per_page + 1 }} to 
            {{ Math.min(pagination.current_page * pagination.per_page, pagination.total) }} of 
            {{ pagination.total }} results
          </div>
          <div class="flex space-x-2">
            <button
              @click="changePage(pagination.current_page - 1)"
              :disabled="pagination.current_page <= 1"
              class="btn-secondary text-sm"
              :class="{ 'opacity-50 cursor-not-allowed': pagination.current_page <= 1 }"
            >
              Previous
            </button>
            <button
              @click="changePage(pagination.current_page + 1)"
              :disabled="pagination.current_page >= pagination.last_page"
              class="btn-secondary text-sm"
              :class="{ 'opacity-50 cursor-not-allowed': pagination.current_page >= pagination.last_page }"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, reactive, onMounted } from 'vue'
import { useAttendanceStore } from '@/stores/attendance'
import { formatDate, formatTime, getStatusColor, capitalize } from '@/utils/helpers'
import { MONTHS } from '@/utils/constants'

const attendanceStore = useAttendanceStore()

const loading = ref(false)
const months = MONTHS

const filters = reactive({
  start_date: '',
  end_date: '',
  month: new Date().getMonth() + 1,
  year: new Date().getFullYear()
})

// Computed properties
const attendanceHistory = computed(() => attendanceStore.attendanceHistory)
const pagination = computed(() => attendanceStore.pagination)

const years = computed(() => {
  const currentYear = new Date().getFullYear()
  const years = []
  for (let i = currentYear; i >= currentYear - 5; i--) {
    years.push(i)
  }
  return years
})

const totalDays = computed(() => attendanceHistory.value.length)
const totalHours = computed(() => {
  return Math.round(attendanceHistory.value.reduce((sum, record) => sum + (record.total_hours || 0), 0))
})
const lateDays = computed(() => {
  return attendanceHistory.value.filter(record => record.status === 'late').length
})
const averageHours = computed(() => {
  return totalDays.value > 0 ? Math.round(totalHours.value / totalDays.value * 10) / 10 : 0
})

const fetchHistory = async () => {
  loading.value = true
  try {
    const params = {}
    if (filters.start_date) params.start_date = filters.start_date
    if (filters.end_date) params.end_date = filters.end_date
    if (filters.month) params.month = filters.month
    if (filters.year) params.year = filters.year
    
    await attendanceStore.fetchAttendanceHistory(params)
  } catch (error) {
    console.error('Failed to fetch attendance history:', error)
  } finally {
    loading.value = false
  }
}

const clearFilters = () => {
  filters.start_date = ''
  filters.end_date = ''
  filters.month = ''
  filters.year = new Date().getFullYear()
  fetchHistory()
}

const changePage = (page) => {
  if (page >= 1 && page <= pagination.value.last_page) {
    fetchHistory()
  }
}

onMounted(() => {
  fetchHistory()
})
</script>
