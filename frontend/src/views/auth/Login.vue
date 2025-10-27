<!-- File: frontend/src/views/auth/Login.vue -->
<!-- Location: frontend/src/views/auth/Login.vue -->
<!-- ENHANCED WITH DYNAMIC FLOATING ANIMATION -->

<template>
  <div class="min-h-screen flex items-center justify-center bg-blue-600 py-12 px-4 sm:px-6 lg:px-8 relative overflow-hidden">
    <!-- Animated Background Logo - More Dynamic Movement -->
    <div class="auth-background-logo">
      <img src="/image.png" alt="InfoAssure Logo" class="floating-logo" />
    </div>

    <div class="max-w-md w-full space-y-8 relative z-10">
      <!-- Header -->
      <div class="text-center">
        <div class="mx-auto h-16 w-16 bg-blue-600 rounded-full flex items-center justify-center mb-6">
          <div>
            <img src="/image.png" alt="" style="height: 48px; width: 48px; border-radius: 50%; object-fit: cover;">
          </div>
        </div>
        <h2 class="text-3xl font-bold text-white">Attendance System</h2>
        <p class="mt-2 text-sm text-gray-100">Sign in to your account</p>
      </div>

      <!-- Login Form -->
      <form class="mt-8 space-y-6 bg-white bg-opacity-90 backdrop-blur-lg rounded-xl p-8 shadow-2xl" @submit.prevent="handleLogin">
        <div class="space-y-4">
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
              placeholder="Enter your password"
            >
            <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
          </div>
        </div>

        <!-- Error Message -->
        <div v-if="authStore.error" class="bg-red-50 border border-red-200 rounded-lg p-4">
          <p class="text-sm text-red-600">{{ authStore.error }}</p>
        </div>

        <!-- Submit Button -->
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
            {{ loading ? 'Signing in....' : 'Sign In' }}
          </span>
        </button>

        <!-- Register Link -->
        <div class="text-center">
          <p class="text-sm text-gray-600">
            Don't have an account?
            <router-link to="/register" class="font-medium text-blue-600 hover:text-blue-500">
              Register here
            </router-link>
          </p>
        </div>
      </form>

      <!-- Quick Login Buttons for Testing -->
      <div class="mt-8 space-y-4">
        <div class="bg-white bg-opacity-90 backdrop-blur-lg border border-blue-200 rounded-lg p-4 shadow-xl">
          <h3 class="text-sm font-medium text-blue-800 mb-3">Quick Login (Testing)</h3>
          <div class="space-y-2">
            <button
              @click="quickLoginAdmin"
              :disabled="loading"
              class="w-full flex items-center justify-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
              </svg>
              Login as Admin
            </button>
            
            <button
              @click="quickLoginWorker"
              :disabled="loading"
              class="w-full flex items-center justify-center px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg transition-colors disabled:opacity-50"
            >
              <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
              </svg>
              Login as Worker (John)
            </button>
          </div>
        </div>

        <!-- Demo Credentials Info -->
        <div class="bg-white bg-opacity-90 backdrop-blur-lg border border-gray-200 rounded-lg p-4 shadow-xl">
          <h3 class="text-sm font-medium text-gray-800 mb-2">Demo Credentials:</h3>
          <div class="space-y-1 text-xs text-gray-600">
            <p><strong>Admin:</strong> admin@attendance.com / admin123</p>
            <p><strong>Worker:</strong> john@attendance.com / worker123</p>
            <p><strong>Worker:</strong> jane@attendance.com / worker123</p>
            <p><strong>Worker:</strong> mike@attendance.com / worker123</p>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, reactive, onMounted } from 'vue'
import { useRouter } from 'vue-router'
import { useAuthStore } from '@/stores/auth'

const router = useRouter()
const authStore = useAuthStore()

const loading = ref(false)
const form = reactive({
  email: '',
  password: ''
})

const errors = reactive({
  email: '',
  password: ''
})

// Check for test credentials from account switching
onMounted(() => {
  const testEmail = localStorage.getItem('test_email')
  const testPassword = localStorage.getItem('test_password')
  
  if (testEmail && testPassword) {
    form.email = testEmail
    form.password = testPassword
    
    // Clear test credentials
    localStorage.removeItem('test_email')
    localStorage.removeItem('test_password')
    
    // Auto-login after short delay
    setTimeout(() => {
      handleLogin()
    }, 500)
  }
})

const clearErrors = () => {
  errors.email = ''
  errors.password = ''
  authStore.clearError()
}

const validateForm = () => {
  clearErrors()
  let isValid = true

  if (!form.email) {
    errors.email = 'Email is required'
    isValid = false
  } else if (!/\S+@\S+\.\S+/.test(form.email)) {
    errors.email = 'Email is invalid'
    isValid = false
  }

  if (!form.password) {
    errors.password = 'Password is required'
    isValid = false
  } else if (form.password.length < 6) {
    errors.password = 'Password must be at least 6 characters'
    isValid = false
  }

  return isValid
}

const handleLogin = async () => {
  if (!validateForm()) return

  loading.value = true

  try {
    await authStore.login({
      email: form.email,
      password: form.password
    })

    // Show success message
    window.showNotification?.(`Welcome ${authStore.userName}!`, 'success')

    // Redirect based on role
    if (authStore.isAdmin) {
      router.push('/admin/dashboard')
    } else {
      router.push('/worker/dashboard')
    }
  } catch (error) {
    console.error('Login failed:', error)
    // Error is handled by the auth store
  } finally {
    loading.value = false
  }
}

// Quick login functions for testing
const quickLoginAdmin = () => {
  form.email = 'admin@attendance.com'
  form.password = 'admin123'
  handleLogin()
}

const quickLoginWorker = () => {
  form.email = 'john@attendance.com'
  form.password = 'worker123'
  handleLogin()
}
</script>

<style scoped>
.auth-background-logo {
  position: fixed;
  top: 0;
  left: 50%;
  transform: translateX(-50%);
  z-index: 0;
  pointer-events: none;
  width: 100%;
  height: 100vh;
  display: flex;
  align-items: flex-start;
  justify-content: center;
  padding-top: 5vh;
}

.floating-logo {
  width: 600px;
  height: 600px;
  opacity: 0.15;
  animation: floatTopToBottom 8s ease-in-out infinite;
  object-fit: contain;
}

/* Dynamic animation from top to bottom */
@keyframes floatTopToBottom {
  0% {
    transform: translateY(-20vh);
    opacity: 0.1;
  }
  25% {
    opacity: 0.15;
  }
  50% {
    transform: translateY(40vh);
    opacity: 0.2;
  }
  75% {
    opacity: 0.15;
  }
  100% {
    transform: translateY(-20vh);
    opacity: 0.1;
  }
}

/* Responsive sizing for mobile */
@media (max-width: 768px) {
  .floating-logo {
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
  .floating-logo {
    width: 300px;
    height: 300px;
  }
}
</style>