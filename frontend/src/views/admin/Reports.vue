!-- ======================================= -->

<!-- File: frontend/src/views/admin/Reports.vue -->
<!-- Location: frontend/src/views/admin/Reports.vue -->

<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="rounded-lg shadow-sm border border-gray-200 p-6 bg-green-200">
      <h1 class="text-2xl font-bold text-gray-900">Attendance Reports</h1>
      <p class="text-gray-600 mt-1 bg-green-100">Detailed attendance analysis and reports</p>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Employee</label>
          <select v-model="filters.user_id" class="input-field" @change="fetchReports">
            <option value="">All Employees</option>
            <option v-for="user in users" :key="user.id" :value="user.id">
              {{ user.name }} ({{ user.employee_id }})
            </option>
          </select>
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
          <input
            v-model="filters.start_date"
            type="date"
            class="input-field"
            @change="fetchReports"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
          <input
            v-model="filters.end_date"
            type="date"
            class="input-field"
            @change="fetchReports"
          >
        </div>
        <div>
          <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
          <select v-model="filters.status" class="input-field" @change="fetchReports">
            <option value="">All Status</option>
            <option value="present">Present</option>
            <option value="late">Late</option>
            <option value="absent">Absent</option>
          </select>
        </div>
        <div class="flex items-end">
          <button @click="exportReport" class="btn-secondary w-full">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            Export
          </button>
        </div>
      </div>
    </div>

    <!-- Summary Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
      <div class="stat-card">
        <div class="flex items-center">
          <div class="p-3 bg-blue-100 rounded-lg">
            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-600">Total Records</p>
            <p class="text-2xl font-bold text-gray-900">{{ reportStats.total || 0 }}</p>
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
            <p class="text-sm text-gray-600">Present</p>
            <p class="text-2xl font-bold text-gray-900">{{ reportStats.present || 0 }}</p>
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
            <p class="text-sm text-gray-600">Late</p>
            <p class="text-2xl font-bold text-gray-900">{{ reportStats.late || 0 }}</p>
          </div>
        </div>
      </div>

      <div class="stat-card">
        <div class="flex items-center">
          <div class="p-3 bg-purple-100 rounded-lg">
            <svg class="w-8 h-8 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
          </div>
          <div class="ml-4">
            <p class="text-sm text-gray-600">Total Hours</p>
            <p class="text-2xl font-bold text-gray-900">{{ Math.round(reportStats.total_hours || 0) }}h</p>
          </div>
        </div>
      </div>
    </div>

    <!-- Attendance Records -->
    <div class="bg-green-200 rounded-lg shadow-sm border border-gray-200">
      <div class="p-6 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900 bg-green-100">Attendance Records</h2>
      </div>

      <div v-if="loading" class="p-6 text-center bg-green-200 ">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
        <p class="mt-2 text-gray-500">Loading...</p>
      </div>

      <div v-else-if="attendanceRecords.length" class="table-container">
        <table class="table">
          <thead>
            <tr>
              <th>Employee</th>
              <th>Date</th>
              <th>Clock In</th>
              <th>Clock Out</th>
              <th>Total Hours</th>
              <th>Status</th>
              <th>Notes</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="record in attendanceRecords" :key="record.id">
              <td>
                <div>
                  <p class="font-medium text-gray-900">{{ record.user.name }}</p>
                  <p class="text-sm text-gray-500">{{ record.user.employee_id }}</p>
                </div>
              </td>
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
              :class="{ 'opacity-50 cursor-not-allowed': pagination.current_page >= pagination.last_page }">
              Next
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted } from 'vue'
import { useAttendanceStore } from '@/stores/attendance'
import { useUsersStore } from '@/stores/users'
import { formatDate, formatTime, getStatusColor, capitalize } from '@/utils/helpers'

const attendanceStore = useAttendanceStore()
const usersStore = useUsersStore()

const loading = ref(false)

const filters = reactive({
  user_id: '',
  start_date: '',
  end_date: '',
  status: ''
})

// Computed properties
const attendanceRecords = computed(() => attendanceStore.allAttendance)
const users = computed(() => usersStore.users)
const pagination = computed(() => attendanceStore.pagination)

const reportStats = computed(() => {
  const records = attendanceRecords.value
  return {
    total: records.length,
    present: records.filter(r => r.status === 'present').length,
    late: records.filter(r => r.status === 'late').length,
    total_hours: records.reduce((sum, r) => sum + (r.total_hours || 0), 0)
  }
})

const fetchReports = async () => {
  loading.value = true
  try {
    const params = {}
    if (filters.user_id) params.user_id = filters.user_id
    if (filters.start_date) params.start_date = filters.start_date
    if (filters.end_date) params.end_date = filters.end_date
    if (filters.status) params.status = filters.status

    await attendanceStore.fetchAllAttendance(params)
  } catch (error) {
    console.error('Failed to fetch reports:', error)
  } finally {
    loading.value = false
  }
}

const exportReport = () => {
  // Simple CSV export
  const headers = ['Employee', 'Employee ID', 'Date', 'Clock In', 'Clock Out', 'Total Hours', 'Status', 'Notes']
  const csvContent = [
    headers.join(','),
    ...attendanceRecords.value.map(record => [
      record.user.name,
      record.user.employee_id,
      record.date,
      record.clock_in_time || '',
      record.clock_out_time || '',
      record.total_hours || 0,
      record.status,
      record.notes || ''
    ].join(','))
  ].join('\n')

  const blob = new Blob([csvContent], { type: 'text/csv' })
  const url = window.URL.createObjectURL(blob)
  const a = document.createElement('a')
  a.href = url
  a.download = `attendance-report-${new Date().toISOString().split('T')[0]}.csv`
  document.body.appendChild(a)
  a.click()
  document.body.removeChild(a)
  window.URL.revokeObjectURL(url)
}

const changePage = (page) => {
  fetchReports()
}

onMounted(async () => {
  await fetchReports()
  await usersStore.fetchUsers()
})
</script>