<!-- File: frontend/src/components/admin/UserManagement.vue -->
<!-- Location: frontend/src/components/admin/UserManagement.vue -->

<template>
  <div class="space-y-6">
    <!-- Header with Actions -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <div class="flex items-center justify-between">
        <div>
          <h2 class="text-xl font-bold text-gray-900">User Management</h2>
          <p class="text-gray-600 mt-1">Manage system users and their permissions</p>
        </div>
        <div class="flex items-center space-x-3">
          <button
            @click="refreshUsers"
            :disabled="loading"
            class="btn-secondary"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
            </svg>
            {{ loading ? 'Refreshing!!!' : 'Refresh' }}
          </button>
          <button
            @click="showCreateModal = true"
            class="btn-primary"
          >
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
            </svg>
            Create User..
          </button>
        </div>
      </div>

      <!-- Search and Filters -->
      <div class="mt-6 grid grid-cols-1 md:grid-cols-4 gap-4">
        <div>
          <input
            v-model="searchTerm"
            @input="debouncedSearch"
            type="text"
            placeholder="Search users..."
            class="input-field"
          />
        </div>
        <div>
          <select v-model="filters.role" @change="fetchUsers" class="input-field">
            <option value="">All Roles</option>
            <option value="admin">Admin</option>
            <option value="worker">Worker</option>
          </select>
        </div>
        <div>
          <select v-model="filters.status" @change="fetchUsers" class="input-field">
            <option value="">All Status</option>
            <option value="active">Active</option>
            <option value="inactive">Inactive</option>
          </select>
        </div>
        <div>
          <select v-model="filters.department" @change="fetchUsers" class="input-field">
            <option value="">All Departments</option>
            <option v-for="dept in departments" :key="dept" :value="dept">
              {{ dept }}
            </option>
          </select>
        </div>
      </div>
    </div>

    <!-- Users Table -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200">
      <div class="p-6 border-b border-gray-200">
        <div class="flex items-center justify-between">
          <h3 class="text-lg font-semibold text-gray-900">
            Users ({{ users.total || 0 }})
          </h3>
          
          <!-- Bulk Actions -->
          <div v-if="selectedUsers.length > 0" class="flex items-center space-x-3">
            <span class="text-sm text-gray-600">{{ selectedUsers.length }} selected</span>
            <div class="flex space-x-2">
              <button
                @click="bulkAction('activate')"
                :disabled="bulkLoading"
                class="btn-secondary text-sm"
              >
                Activate
              </button>
              <button
                @click="bulkAction('deactivate')"
                :disabled="bulkLoading"
                class="btn-secondary text-sm"
              >
                Deactivate
              </button>
              <button
                @click="bulkAction('delete')"
                :disabled="bulkLoading"
                class="text-red-600 hover:text-red-800 text-sm font-medium"
              >
                Delete
              </button>
            </div>
          </div>
        </div>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="p-8 text-center">
        <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-600 mx-auto"></div>
        <p class="text-gray-600 mt-2">Loading users...</p>
      </div>

      <!-- Users Table -->
      <div v-else-if="users.data && users.data.length" class="overflow-x-auto">
        <table class="table">
          <thead>
            <tr>
              <th class="w-12">
                <input
                  type="checkbox"
                  @change="toggleAllUsers"
                  :checked="allUsersSelected"
                  class="rounded"
                />
              </th>
              <th>User</th>
              <th>Role</th>
              <th>Department</th>
              <th>Status</th>
              <th>Joined</th>
              <th class="text-right">Actions</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="user in users.data" :key="user.id">
              <td>
                <input
                  type="checkbox"
                  :value="user.id"
                  v-model="selectedUsers"
                  class="rounded"
                />
              </td>
              <td>
                <div class="flex items-center space-x-3">
                  <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center">
                    <span class="text-blue-600 font-semibold text-sm">
                      {{ user.name.charAt(0).toUpperCase() }}
                    </span>
                  </div>
                  <div>
                    <p class="font-medium text-gray-900">{{ user.name }}</p>
                    <p class="text-sm text-gray-500">{{ user.email }}</p>
                    <p class="text-xs text-gray-400">ID: {{ user.employee_id }}</p>
                  </div>
                </div>
              </td>
              <td>
                <span 
                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                  :class="user.role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'"
                >
                  {{ capitalize(user.role) }}
                </span>
              </td>
              <td>
                <span class="text-gray-900">{{ user.department || 'N/A' }}</span>
              </td>
              <td>
                <span 
                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                  :class="user.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                >
                  {{ capitalize(user.status) }}
                </span>
              </td>
              <td class="text-gray-600">{{ formatDate(user.created_at) }}</td>
              <td class="text-right">
                <div class="flex items-center justify-end space-x-2">
                  <button
                    @click="viewUserDetails(user)"
                    class="text-blue-600 hover:text-blue-800 font-medium text-sm"
                  >
                    View Details
                  </button>
                  <button
                    @click="editUser(user)"
                    class="text-gray-600 hover:text-gray-800 font-medium text-sm"
                  >
                    Edit
                  </button>
                  <button
                    @click="deleteUser(user)"
                    :disabled="user.id === authStore.user.id"
                    class="text-red-600 hover:text-red-800 font-medium text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                  >
                    Delete
                  </button>
                </div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>

      <!-- Empty State -->
      <div v-else class="p-8 text-center">
        <svg class="w-12 h-12 text-gray-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <p class="text-gray-600">No users found</p>
        <p class="text-gray-500 text-sm mt-1">Try adjusting your search criteria</p>
      </div>

      <!-- Pagination -->
      <div v-if="users.data && users.data.length && users.last_page > 1" class="p-6 border-t border-gray-200">
        <div class="flex items-center justify-between">
          <div class="text-sm text-gray-700">
            Showing {{ users.from || 0 }} to {{ users.to || 0 }} of {{ users.total || 0 }} results
          </div>
          <div class="flex space-x-2">
            <button
              @click="changePage(users.current_page - 1)"
              :disabled="!users.prev_page_url"
              class="btn-secondary text-sm disabled:opacity-50"
            >
              Previous
            </button>
            <span class="px-3 py-2 text-sm text-gray-700">
              Page {{ users.current_page || 1 }} of {{ users.last_page || 1 }}
            </span>
            <button
              @click="changePage(users.current_page + 1)"
              :disabled="!users.next_page_url"
              class="btn-secondary text-sm disabled:opacity-50"
            >
              Next
            </button>
          </div>
        </div>
      </div>
    </div>

    <!-- Create User Modal -->
    <div v-if="showCreateModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-xl font-semibold">Create New User</h3>
          <button @click="closeCreateModal" class="text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Use your existing RegisterForm component -->
        <RegisterForm
          :loading="createLoading"
          :submitError="createError"
          @submit="handleCreateUser"
          ref="createFormRef"
        />
      </div>
    </div>

    <!-- Edit User Modal -->
    <div v-if="showEditModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg p-6 w-full max-w-md max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-6">
          <h3 class="text-xl font-semibold">Edit User</h3>
          <button @click="closeEditModal" class="text-gray-500 hover:text-gray-700">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
          </button>
        </div>

        <!-- Edit Form -->
        <form @submit.prevent="handleUpdateUser" class="space-y-4">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
            <input
              v-model="editForm.name"
              type="text"
              required
              class="input-field"
              placeholder="Enter full name"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input
              v-model="editForm.email"
              type="email"
              required
              class="input-field"
              placeholder="Enter email"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Employee ID</label>
            <input
              v-model="editForm.employee_id"
              type="text"
              required
              class="input-field"
              placeholder="Enter employee ID"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
            <select v-model="editForm.role" required class="input-field">
              <option value="worker">Worker</option>
              <option value="admin">Admin</option>
            </select>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
            <input
              v-model="editForm.department"
              type="text"
              class="input-field"
              placeholder="Enter department"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
            <input
              v-model="editForm.phone"
              type="tel"
              class="input-field"
              placeholder="Enter phone number"
            />
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
            <select v-model="editForm.status" required class="input-field">
              <option value="active">Active</option>
              <option value="inactive">Inactive</option>
            </select>
          </div>

          <div class="border-t pt-4">
            <h4 class="font-medium text-gray-900 mb-3">Change Password (Optional)</h4>
            <div class="space-y-3">
              <div>
                <input
                  v-model="editForm.password"
                  type="password"
                  class="input-field"
                  placeholder="New password (leave empty to keep current)"
                />
              </div>
              <div v-if="editForm.password">
                <input
                  v-model="editForm.password_confirmation"
                  type="password"
                  class="input-field"
                  placeholder="Confirm new password"
                />
              </div>
            </div>
          </div>

          <div v-if="updateError" class="bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="text-sm text-red-600">{{ updateError }}</p>
          </div>

          <div class="flex space-x-3">
            <button
              type="button"
              @click="closeEditModal"
              class="flex-1 btn-secondary"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="updateLoading"
              class="flex-1 btn-primary"
            >
              {{ updateLoading ? 'Updating...' : 'Update User' }}
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- User Details Modal -->
    <div v-if="showDetailsModal && selectedUserDetails" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50">
      <div class="bg-white rounded-lg w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="p-6 border-b border-gray-200">
          <div class="flex justify-between items-center">
            <h3 class="text-xl font-semibold">User Details</h3>
            <button @click="closeDetailsModal" class="text-gray-500 hover:text-gray-700">
              <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
              </svg>
            </button>
          </div>
        </div>

        <div class="p-6">
          <!-- User Info -->
          <div class="mb-6">
            <div class="flex items-center space-x-4 mb-4">
              <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center">
                <span class="text-blue-600 font-bold text-xl">
                  {{ selectedUserDetails.user.name.charAt(0).toUpperCase() }}
                </span>
              </div>
              <div>
                <h4 class="text-lg font-semibold text-gray-900">{{ selectedUserDetails.user.name }}</h4>
                <p class="text-gray-600">{{ selectedUserDetails.user.email }}</p>
                <p class="text-sm text-gray-500">Employee ID: {{ selectedUserDetails.user.employee_id }}</p>
              </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
              <div>
                <p class="text-sm font-medium text-gray-500">Role</p>
                <p class="text-gray-900">{{ capitalize(selectedUserDetails.user.role) }}</p>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-500">Status</p>
                <span 
                  class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                  :class="selectedUserDetails.user.status === 'active' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'"
                >
                  {{ capitalize(selectedUserDetails.user.status) }}
                </span>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-500">Department</p>
                <p class="text-gray-900">{{ selectedUserDetails.user.department || 'N/A' }}</p>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-500">Phone</p>
                <p class="text-gray-900">{{ selectedUserDetails.user.phone || 'N/A' }}</p>
              </div>
              <div>
                <p class="text-sm font-medium text-gray-500">Joined</p>
                <p class="text-gray-900">{{ formatDate(selectedUserDetails.user.created_at) }}</p>
              </div>
            </div>
          </div>

          <!-- Attendance Statistics -->
          <div class="mb-6">
            <h5 class="font-medium text-gray-900 mb-3">Attendance Statistics</h5>
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
              <div class="bg-blue-50 p-3 rounded-lg text-center">
                <p class="text-2xl font-bold text-blue-600">{{ selectedUserDetails.stats.total_attendance_days }}</p>
                <p class="text-sm text-blue-700">Total Days</p>
              </div>
              <div class="bg-green-50 p-3 rounded-lg text-center">
                <p class="text-2xl font-bold text-green-600">{{ selectedUserDetails.stats.present_days }}</p>
                <p class="text-sm text-green-700">Present</p>
              </div>
              <div class="bg-yellow-50 p-3 rounded-lg text-center">
                <p class="text-2xl font-bold text-yellow-600">{{ selectedUserDetails.stats.late_days }}</p>
                <p class="text-sm text-yellow-700">Late</p>
              </div>
              <div class="bg-red-50 p-3 rounded-lg text-center">
                <p class="text-2xl font-bold text-red-600">{{ selectedUserDetails.stats.absent_days }}</p>
                <p class="text-sm text-red-700">Absent</p>
              </div>
            </div>
          </div>

          <!-- Recent Attendance -->
          <div>
            <h5 class="font-medium text-gray-900 mb-3">Recent Attendance</h5>
            <div class="space-y-2">
              <div v-if="selectedUserDetails.user.attendances && selectedUserDetails.user.attendances.length">
                <div 
                  v-for="attendance in selectedUserDetails.user.attendances.slice(0, 5)" 
                  :key="attendance.id"
                  class="flex items-center justify-between py-2 px-3 bg-gray-50 rounded"
                >
                  <div>
                    <p class="text-sm font-medium">{{ formatDate(attendance.date) }}</p>
                    <p class="text-xs text-gray-500">
                      In: {{ formatTime(attendance.clock_in_time) }}
                      <span v-if="attendance.clock_out_time">
                        | Out: {{ formatTime(attendance.clock_out_time) }}
                      </span>
                    </p>
                  </div>
                  <span 
                    class="inline-flex px-2 py-1 text-xs font-semibold rounded-full"
                    :class="getStatusColor(attendance.status)"
                  >
                    {{ capitalize(attendance.status) }}
                  </span>
                </div>
              </div>
              <div v-else class="text-center py-4 text-gray-500">
                <p>No attendance records found</p>
              </div>
            </div>
          </div>

          <!-- Action Buttons -->
          <div class="mt-6 flex space-x-3">
            <button
              @click="editUser(selectedUserDetails.user)"
              class="btn-primary"
            >
              Edit User
            </button>
            <button
              @click="toggleUserStatus(selectedUserDetails.user)"
              :class="selectedUserDetails.user.status === 'active' ? 'btn-secondary' : 'btn-primary'"
            >
              {{ selectedUserDetails.user.status === 'active' ? 'Deactivate' : 'Activate' }}
            </button>
            <button
              v-if="selectedUserDetails.user.id !== authStore.user.id"
              @click="deleteUser(selectedUserDetails.user)"
              class="text-red-600 hover:text-red-800 font-medium"
            >
              Delete User
            </button>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, computed, onMounted, watch } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { userService } from '@/services/userService'
import { formatDate, formatTime, getStatusColor, capitalize } from '@/utils/helpers'
import RegisterForm from '@/components/forms/RegisterForm.vue'
import { debounce } from 'lodash-es'


const authStore = useAuthStore()

// State
const loading = ref(false)
const createLoading = ref(false)
const updateLoading = ref(false)
const bulkLoading = ref(false)
const users = ref({})
const departments = ref([])
const selectedUsers = ref([])
const selectedUserDetails = ref(null)

// Modals
const showCreateModal = ref(false)
const showEditModal = ref(false)
const showDetailsModal = ref(false)

// Forms
const createError = ref('')
const updateError = ref('')
const searchTerm = ref('')
const currentPage = ref(1)

// Filters
const filters = reactive({
  role: '',
  status: '',
  department: ''
})

// Edit form
const editForm = reactive({
  name: '',
  email: '',
  employee_id: '',
  role: 'worker',
  department: '',
  phone: '',
  status: 'active',
  password: '',
  password_confirmation: ''
})

const editingUserId = ref(null)
const createFormRef = ref(null)

// Computed
const allUsersSelected = computed(() => {
  return users.value.data && users.value.data.length > 0 && 
         selectedUsers.value.length === users.value.data.length
})

// Methods
const fetchUsers = async (page = 1) => {
  loading.value = true
  try {
    const params = {
      page,
      search: searchTerm.value,
      ...filters
    }

    const response = await userService.getUsers(params)
    users.value = response.data || response
    currentPage.value = page
    
    // Clear selected users when fetching new data
    selectedUsers.value = []
  } catch (error) {
    console.error('Failed to fetch users:', error)
    window.showNotification?.('Failed to load users', 'error')
  } finally {
    loading.value = false
  }
}

const refreshUsers = () => {
  fetchUsers(currentPage.value)
}

const changePage = (page) => {
  if (page >= 1 && page <= (users.value.last_page || 1)) {
    fetchUsers(page)
  }
}

// Debounced search
const debouncedSearch = debounce(() => {
  fetchUsers(1)
}, 500)

// User selection
const toggleAllUsers = () => {
  if (allUsersSelected.value) {
    selectedUsers.value = []
  } else {
    selectedUsers.value = users.value.data.map(user => user.id)
  }
}

// Create user
const handleCreateUser = async (userData) => {
  createLoading.value = true
  createError.value = ''

  try {
    // Add default role if not specified
    const userPayload = {
      ...userData,
      role: userData.role || 'worker'
    }

    await userService.createUser(userPayload)
    
    window.showNotification?.('User created successfully!', 'success')
    closeCreateModal()
    fetchUsers(currentPage.value)
  } catch (error) {
    console.error('Failed to create user:', error)
    createError.value = error.response?.data?.message || 'Failed to create user'
    
    if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat()
      createError.value = errors.join(', ')
    }
  } finally {
    createLoading.value = false
  }
}

const closeCreateModal = () => {
  showCreateModal.value = false
  createError.value = ''
  createFormRef.value?.clearForm()
}

// Edit user
const editUser = (user) => {
  editingUserId.value = user.id
  editForm.name = user.name
  editForm.email = user.email
  editForm.employee_id = user.employee_id
  editForm.role = user.role
  editForm.department = user.department || ''
  editForm.phone = user.phone || ''
  editForm.status = user.status
  editForm.password = ''
  editForm.password_confirmation = ''
  
  showEditModal.value = true
  showDetailsModal.value = false
}

const handleUpdateUser = async () => {
  if (editForm.password && editForm.password !== editForm.password_confirmation) {
    updateError.value = 'Password confirmation does not match'
    return
  }

  updateLoading.value = true
  updateError.value = ''

  try {
    const updateData = {
      name: editForm.name,
      email: editForm.email,
      employee_id: editForm.employee_id,
      role: editForm.role,
      department: editForm.department,
      phone: editForm.phone,
      status: editForm.status
    }

    // Include password only if provided
    if (editForm.password) {
      updateData.password = editForm.password
      updateData.password_confirmation = editForm.password_confirmation
    }

    await userService.updateUser(editingUserId.value, updateData)
    
    window.showNotification?.('User updated successfully!', 'success')
    closeEditModal()
    fetchUsers(currentPage.value)
  } catch (error) {
    console.error('Failed to update user:', error)
    updateError.value = error.response?.data?.message || 'Failed to update user'
    
    if (error.response?.data?.errors) {
      const errors = Object.values(error.response.data.errors).flat()
      updateError.value = errors.join(', ')
    }
  } finally {
    updateLoading.value = false
  }
}

const closeEditModal = () => {
  showEditModal.value = false
  updateError.value = ''
  editingUserId.value = null
}

// View user details
const viewUserDetails = async (user) => {
  try {
    const response = await userService.getUser(user.id)
    selectedUserDetails.value = response.data || response
    showDetailsModal.value = true
  } catch (error) {
    console.error('Failed to fetch user details:', error)
    window.showNotification?.('Failed to load user details', 'error')
  }
}

const closeDetailsModal = () => {
  showDetailsModal.value = false
  selectedUserDetails.value = null
}

// Delete user
const deleteUser = async (user) => {
  if (!confirm(`Are you sure you want to delete "${user.name}"? This action cannot be undone.`)) {
    return
  }

  try {
    await userService.deleteUser(user.id)
    window.showNotification?.('User deleted successfully!', 'success')
    fetchUsers(currentPage.value)
    closeDetailsModal()
  } catch (error) {
    console.error('Failed to delete user:', error)
    window.showNotification?.(error.response?.data?.message || 'Failed to delete user', 'error')
  }
}

// Toggle user status
const toggleUserStatus = async (user) => {
  const newStatus = user.status === 'active' ? 'inactive' : 'active'
  
  try {
    await userService.updateUserStatus(user.id, newStatus)
    window.showNotification?.(`User ${newStatus === 'active' ? 'activated' : 'deactivated'} successfully!`, 'success')
    fetchUsers(currentPage.value)
    
    // Update details modal if open
    if (selectedUserDetails.value && selectedUserDetails.value.user.id === user.id) {
      selectedUserDetails.value.user.status = newStatus
    }
  } catch (error) {
    console.error('Failed to update user status:', error)
    window.showNotification?.(error.response?.data?.message || 'Failed to update user status', 'error')
  }
}

// Bulk actions
const bulkAction = async (action) => {
  if (selectedUsers.value.length === 0) return

  let confirmMessage = ''
  switch (action) {
    case 'activate':
      confirmMessage = `Activate ${selectedUsers.value.length} selected users?`
      break
    case 'deactivate':
      confirmMessage = `Deactivate ${selectedUsers.value.length} selected users?`
      break
    case 'delete':
      confirmMessage = `Delete ${selectedUsers.value.length} selected users? This action cannot be undone.`
      break
  }

  if (!confirm(confirmMessage)) return

  bulkLoading.value = true
  try {
    await userService.bulkAction(action, selectedUsers.value)
    window.showNotification?.(`Bulk ${action} completed successfully!`, 'success')
    selectedUsers.value = []
    fetchUsers(currentPage.value)
  } catch (error) {
    console.error('Bulk action failed:', error)
    window.showNotification?.(error.response?.data?.message || 'Bulk action failed', 'error')
  } finally {
    bulkLoading.value = false
  }
}

// Load departments
const loadDepartments = async () => {
  try {
    const response = await userService.getDepartments()
    departments.value = response.data || response || []
  } catch (error) {
    console.error('Failed to load departments:', error)
  }
}

// Watch for filter changes
watch([() => filters.role, () => filters.status, () => filters.department], () => {
  fetchUsers(1)
})

// Initialize
onMounted(async () => {
  await Promise.all([
    fetchUsers(),
    loadDepartments()
  ])
})
</script>

<style scoped>
.btn-primary {
  @apply bg-blue-600 hover:bg-blue-700 text-white font-medium py-2 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed;
}

.btn-secondary {
  @apply bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium py-2 px-4 rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-gray-500 focus:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed;
}

.input-field {
  @apply w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent;
}

.table {
  @apply min-w-full divide-y divide-gray-200;
}

.table th {
  @apply px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider;
}

.table td {
  @apply px-6 py-4 whitespace-nowrap text-sm text-gray-900;
}

.table tbody tr:hover {
  @apply bg-gray-50;
}
</style>