<!-- File: frontend/src/views/Profile.vue -->
<!-- Location: frontend/src/views/Profile.vue -->

<template>
  <div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <h1 class="text-2xl font-bold text-gray-900">My Profile</h1>
      <p class="text-gray-600 mt-1">Manage your personal information</p>
    </div>

    <!-- Profile Form -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <form @submit.prevent="updateProfile">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Full Name</label>
            <input
              v-model="form.name"
              type="text"
              required
              class="input-field"
              :class="{ 'border-red-300': errors.name }"
            >
            <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
            <input
              v-model="form.email"
              type="email"
              required
              class="input-field"
              :class="{ 'border-red-300': errors.email }"
            >
            <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Employee ID</label>
            <input
              v-model="form.employee_id"
              type="text"
              required
              class="input-field"
              :class="{ 'border-red-300': errors.employee_id }"
            >
            <p v-if="errors.employee_id" class="mt-1 text-sm text-red-600">{{ errors.employee_id }}</p>
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Department</label>
            <input
              v-model="form.department"
              type="text"
              class="input-field"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
            <input
              v-model="form.phone"
              type="tel"
              class="input-field"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Role</label>
            <input
              :value="authStore.user?.role"
              type="text"
              disabled
              class="input-field bg-gray-50 text-gray-500"
            >
          </div>
        </div>

        <div v-if="error" class="mt-6 bg-red-50 border border-red-200 rounded-lg p-4">
          <p class="text-sm text-red-600">{{ error }}</p>
        </div>

        <div class="mt-6">
          <button
            type="submit"
            :disabled="loading"
            class="btn-primary"
            :class="{ 'opacity-50 cursor-not-allowed': loading }"
          >
            {{ loading ? 'Updating...' : 'Update Profile' }}
          </button>
        </div>
      </form>
    </div>

    <!-- Change Password -->
    <div class="bg-white rounded-lg shadow-sm border border-gray-200 p-6">
      <h2 class="text-lg font-semibold text-gray-900 mb-4">Change Password</h2>
      
      <form @submit.prevent="changePassword">
        <div class="space-y-4 max-w-md">
          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Current Password</label>
            <input
              v-model="passwordForm.current_password"
              type="password"
              required
              class="input-field"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
            <input
              v-model="passwordForm.password"
              type="password"
              required
              minlength="6"
              class="input-field"
            >
          </div>

          <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Confirm New Password</label>
            <input
              v-model="passwordForm.password_confirmation"
              type="password"
              required
              class="input-field"
            >
          </div>
        </div>

        <div v-if="passwordError" class="mt-4 bg-red-50 border border-red-200 rounded-lg p-4">
          <p class="text-sm text-red-600">{{ passwordError }}</p>
        </div>

        <div class="mt-6">
          <button
            type="submit"
            :disabled="passwordLoading"
            class="btn-primary"
            :class="{ 'opacity-50 cursor-not-allowed': passwordLoading }"
          >
            {{ passwordLoading ? 'Changing...' : 'Change Password' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useAuthStore } from '@/stores/auth'
import { useUsersStore } from '@/stores/users'

const authStore = useAuthStore()
const usersStore = useUsersStore()

const loading = ref(false)
const passwordLoading = ref(false)
const error = ref('')
const passwordError = ref('')

const form = reactive({
  name: '',
  email: '',
  employee_id: '',
  department: '',
  phone: ''
})

const passwordForm = reactive({
  current_password: '',
  password: '',
  password_confirmation: ''
})

const errors = reactive({
  name: '',
  email: '',
  employee_id: ''
})

const updateProfile = async () => {
  loading.value = true
  error.value = ''
  
  try {
    await usersStore.updateUser(authStore.user.id, form)
    await authStore.fetchUser() // Refresh user data
    window.showNotification?.('Profile updated successfully!', 'success')
  } catch (err) {
    error.value = err.response?.data?.message || 'Failed to update profile'
  } finally {
    loading.value = false
  }
}

const changePassword = async () => {
  if (passwordForm.password !== passwordForm.password_confirmation) {
    passwordError.value = 'Passwords do not match'
    return
  }

  passwordLoading.value = true
  passwordError.value = ''
  
  try {
    await usersStore.updateUser(authStore.user.id, {
      password: passwordForm.password,
      password_confirmation: passwordForm.password_confirmation
    })
    
    // Clear form
    Object.keys(passwordForm).forEach(key => {
      passwordForm[key] = ''
    })
    
    window.showNotification?.('Password changed successfully!', 'success')
  } catch (err) {
    passwordError.value = err.response?.data?.message || 'Failed to change password'
  } finally {
    passwordLoading.value = false
  }
}

onMounted(() => {
  const user = authStore.user
  if (user) {
    form.name = user.name || ''
    form.email = user.email || ''
    form.employee_id = user.employee_id || ''
    form.department = user.department || ''
    form.phone = user.phone || ''
  }
})
</script>
