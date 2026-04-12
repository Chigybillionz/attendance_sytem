<template>
  <div class="min-h-screen flex items-center justify-center bg-[#0051ff] py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    
    <div class="auth-background-logo">
      <img src="/image.png" alt="Company Logo" class="floating-logo-vertical" />
    </div>

    <div class="max-w-md w-full space-y-8 relative z-10">
      <div class="text-center">
        <div class="mx-auto h-16 w-16 bg-blue-600 rounded-full flex items-center justify-center mb-6">
          <!-- <svg class="h-8 w-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z" />
          </svg> -->
          <div>
            <img src="/image.png" alt="" style="height: 48px; width: 48px; border-radius: 50%; object-fit: cover;">
          </div>
        </div>
        <h2 class="text-3xl font-bold text-gray-900">Create Account</h2>
        <p class="mt-2 text-sm text-white">Join the attendance system</p>
      </div>

      <form class="mt-8 space-y-6 bg-white p-10 rounded-xl shadow-lg" @submit.prevent="handleRegister">
        <div class="space-y-4">
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
        </div>

        <div v-if="authStore.error" class="bg-red-50 border border-red-200 rounded-lg p-4">
          <p class="text-sm text-red-600">{{ authStore.error }}</p>
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

        <div class="text-center">
          <p class="text-sm text-gray-600">
            Already have an account?
            <router-link to="/login" class="font-medium text-blue-600 hover:text-blue-500">
              Sign in here
            </router-link>
          </p>
        </div>
      </form>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const loading = ref(false)
const form = reactive({
  name: '',
  email: '',
  employee_id: '',
  department: '',
  phone: '',
  password: '',
  password_confirmation: ''
})

const errors = reactive({
  name: '',
  email: '',
  employee_id: '',
  password: '',
  password_confirmation: ''
})

const clearErrors = () => {
  Object.keys(errors).forEach(key => {
    errors[key] = ''
  })
  authStore.clearError()
}

const validateForm = () => {
  clearErrors()
  let isValid = true

  if (!form.name) {
    errors.name = 'Name is required'
    isValid = false
  }

  if (!form.email) {
    errors.email = 'Email is required'
    isValid = false
  } else if (!/\S+@\S+\.\S+/.test(form.email)) {
    errors.email = 'Email is invalid'
    isValid = false
  }

  if (!form.employee_id) {
    errors.employee_id = 'Employee ID is required'
    isValid = false
  }

  if (!form.password) {
    errors.password = 'Password is required'
    isValid = false
  } else if (form.password.length < 6) {
    errors.password = 'Password must be at least 6 characters'
    isValid = false
  }

  if (!form.password_confirmation) {
    errors.password_confirmation = 'Password confirmation is required'
    isValid = false
  } else if (form.password !== form.password_confirmation) {
    errors.password_confirmation = 'Passwords do not match'
    isValid = false
  }

  return isValid
}

const handleRegister = async () => {
  if (!validateForm()) return

  loading.value = true

  try {
    await authStore.register(form)
    
    // Redirect to worker dashboard (new users are workers by default)
    router.push('/worker/dashboard')
    
    // Show success message
    window.showNotification?.('Account created successfully! Welcome to the attendance system.', 'success')
  } catch (error) {
    console.error('Registration failed:', error)
  } finally {
    loading.value = false
  }
}
</script>

<style scoped>
/*
  MODIFIED: Centered the logo horizontally for vertical movement
*/
.auth-background-logo {
  position: fixed;
  top: 0;
  left: 50%; /* Center horizontally */
  transform: translateX(-50%); /* Correct horizontal centering */
  z-index: 0;
  pointer-events: none;
  width: 100%;
  height: 100vh;
  display: flex;
  align-items: flex-start; /* Start at the top */
  justify-content: center;
  padding-top: 5vh;
}

/*
  MODIFIED: Changed class name to floating-logo-vertical
  MODIFIED: Applied the new vertical animation
*/
.floating-logo-vertical {
  width: 600px;
  height: 600px;
  opacity: 0.15;
  animation: floatTopToBottom 8s ease-in-out infinite;
  object-fit: contain;
}

/* |--------------------------------------------------------------------------
| Vertical Floating Animation (Up and Down)
|--------------------------------------------------------------------------
*/
@keyframes floatTopToBottom {
  0% {
    transform: translateY(-20vh); /* Starts off-screen to the top */
    opacity: 0.1;
  }
  25% {
    opacity: 0.15;
  }
  50% {
    transform: translateY(40vh); /* Moves down towards the bottom */
    opacity: 0.2;
  }
  75% {
    opacity: 0.15;
  }
  100% {
    transform: translateY(-20vh); /* Returns to the top */
    opacity: 0.1;
  }
}

/* Responsive sizing for mobile */
@media (max-width: 768px) {
  .floating-logo-vertical {
    width: 400px;
    height: 400px;
    animation: floatTopToBottomMobile 8s ease-in-out infinite;
  }
  
  @keyframes floatTopToBottomMobile {
    0% {
      transform: translateY(-10vh);
      opacity: 0.1;
    }
    50% {
      transform: translateY(30vh);
      opacity: 0.18;
    }
    100% {
      transform: translateY(-10vh);
      opacity: 0.1;
    }
  }
}

@media (max-width: 480px) {
  .floating-logo-vertical {
    width: 300px;
    height: 300px;
  }
}
</style>