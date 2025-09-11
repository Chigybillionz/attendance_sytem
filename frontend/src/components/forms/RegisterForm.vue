<!-- File: frontend/src/components/forms/RegisterForm.vue -->
<!-- Location: frontend/src/components/forms/RegisterForm.vue -->

<template>
  <form @submit.prevent="handleSubmit" class="space-y-4">
    <div>
      <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
        Full Name
      </label>
      <input
        id="name"
        v-model="form.name"
        type="text"
        required
        class="input-field"
        :class="{ 'border-red-300 focus:ring-red-500': errors.name }"
        placeholder="Enter your full name"
      >
      <p v-if="errors.name" class="mt-1 text-sm text-red-600">{{ errors.name }}</p>
    </div>

    <div>
      <label for="email" class="block text-sm font-medium text-gray-700 mb-1">
        Email Address
      </label>
      <input
        id="email"
        v-model="form.email"
        type="email"
        required
        class="input-field"
        :class="{ 'border-red-300 focus:ring-red-500': errors.email }"
        placeholder="Enter your email"
      >
      <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
    </div>

    <div>
      <label for="employee_id" class="block text-sm font-medium text-gray-700 mb-1">
        Employee ID
      </label>
      <input
        id="employee_id"
        v-model="form.employee_id"
        type="text"
        required
        class="input-field"
        :class="{ 'border-red-300 focus:ring-red-500': errors.employee_id }"
        placeholder="Enter your employee ID"
      >
      <p v-if="errors.employee_id" class="mt-1 text-sm text-red-600">{{ errors.employee_id }}</p>
    </div>

    <div>
      <label for="department" class="block text-sm font-medium text-gray-700 mb-1">
        Department (Optional)
      </label>
      <input
        id="department"
        v-model="form.department"
        type="text"
        class="input-field"
        placeholder="Enter your department"
      >
    </div>

    <div>
      <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">
        Phone Number (Optional)
      </label>
      <input
        id="phone"
        v-model="form.phone"
        type="tel"
        class="input-field"
        placeholder="Enter your phone number"
      >
    </div>

    <div>
      <label for="password" class="block text-sm font-medium text-gray-700 mb-1">
        Password
      </label>
      <input
        id="password"
        v-model="form.password"
        type="password"
        required
        class="input-field"
        :class="{ 'border-red-300 focus:ring-red-500': errors.password }"
        placeholder="Create a password"
      >
      <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
    </div>

    <div>
      <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">
        Confirm Password
      </label>
      <input
        id="password_confirmation"
        v-model="form.password_confirmation"
        type="password"
        required
        class="input-field"
        :class="{ 'border-red-300 focus:ring-red-500': errors.password_confirmation }"
        placeholder="Confirm your password"
      >
      <p v-if="errors.password_confirmation" class="mt-1 text-sm text-red-600">{{ errors.password_confirmation }}</p>
    </div>

    <!-- Error Message -->
    <div v-if="submitError" class="bg-red-50 border border-red-200 rounded-lg p-4">
      <p class="text-sm text-red-600">{{ submitError }}</p>
    </div>

    <button
      type="submit"
      :disabled="loading"
      class="w-full btn-primary relative"
      :class="{ 'opacity-50 cursor-not-allowed': loading }"
    >
      <span v-if="loading" class="absolute left-4">
        <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
          <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
          <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
      </span>
      <span :class="{ 'ml-6': loading }">
        {{ loading ? 'Creating Account...' : 'Create Account' }}
      </span>
    </button>
  </form>
</template>

<script setup>
import { reactive, ref } from 'vue'

// Define props
defineProps({
  loading: {
    type: Boolean,
    default: false
  },
  submitError: {
    type: String,
    default: ''
  }
})

// Define emits
const emit = defineEmits(['submit'])

// Form data
const form = reactive({
  name: '',
  email: '',
  employee_id: '',
  department: '',
  phone: '',
  password: '',
  password_confirmation: ''
})

// Form errors
const errors = reactive({
  name: '',
  email: '',
  employee_id: '',
  password: '',
  password_confirmation: ''
})

// Clear errors function
const clearErrors = () => {
  Object.keys(errors).forEach(key => {
    errors[key] = ''
  })
}

// Form validation
const validateForm = () => {
  clearErrors()
  let isValid = true

  // Name validation
  if (!form.name.trim()) {
    errors.name = 'Full name is required'
    isValid = false
  } else if (form.name.trim().length < 2) {
    errors.name = 'Name must be at least 2 characters'
    isValid = false
  }

  // Email validation
  if (!form.email.trim()) {
    errors.email = 'Email is required'
    isValid = false
  } else if (!/\S+@\S+\.\S+/.test(form.email)) {
    errors.email = 'Please enter a valid email address'
    isValid = false
  }

  // Employee ID validation
  if (!form.employee_id.trim()) {
    errors.employee_id = 'Employee ID is required'
    isValid = false
  } else if (form.employee_id.trim().length < 3) {
    errors.employee_id = 'Employee ID must be at least 3 characters'
    isValid = false
  }

  // Password validation
  if (!form.password) {
    errors.password = 'Password is required'
    isValid = false
  } else if (form.password.length < 6) {
    errors.password = 'Password must be at least 6 characters'
    isValid = false
  }

  // Password confirmation validation
  if (!form.password_confirmation) {
    errors.password_confirmation = 'Please confirm your password'
    isValid = false
  } else if (form.password !== form.password_confirmation) {
    errors.password_confirmation = 'Passwords do not match'
    isValid = false
  }

  return isValid
}

// Handle form submission
const handleSubmit = () => {
  if (validateForm()) {
    emit('submit', { ...form })
  }
}

// Clear form
const clearForm = () => {
  Object.keys(form).forEach(key => {
    form[key] = ''
  })
  clearErrors()
}

// Expose methods for parent component
defineExpose({
  clearForm,
  clearErrors
})
</script>

<style scoped>
/* Additional styles if needed */
.input-field:focus {
  transform: translateY(-1px);
}

.btn-primary:hover:not(:disabled) {
  transform: translateY(-1px);
}

.btn-primary:active:not(:disabled) {
  transform: translateY(0);
}
</style>