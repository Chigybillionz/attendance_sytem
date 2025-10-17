<template>
  <div class="min-h-screen bg-blue-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-4xl mx-auto">
      <!-- Header Section -->
      <div class="text-center mb-12">
        <h1 class="text-4xl font-bold text-gray-900 mb-4">About Our Attendance System</h1>
        <p class="text-xl text-gray-600">
          Simplifying attendance tracking for modern organizations
        </p>
      </div>

      <!-- Main Content -->
      <div class="bg-white rounded-lg shadow-md p-8 mb-8">
        <div class="relative py-8">
          <!-- Mission Section (Icon on Right) -->
          <div class="relative flex items-start gap-8 py-8 mb-12">
            <!-- Vertical line for Mission (on the right side) -->
            <div class="absolute right-[64px] top-0 h-[calc(100%+6rem)] w-0.5 bg-blue-600"></div>

            <!-- Content -->
            <div class="flex-1 pt-8 text-right pr-8">
              <h2 class="text-4xl font-bold text-blue-600 mb-6">Our Mission</h2>
              <p class="text-gray-900 text-lg leading-relaxed">
                To help in securing organizations assets using best practice standards and
                frameworks.
              </p>
            </div>

            <!-- Icon circle -->
            <div class="relative z-10 flex-shrink-0">
              <div
                class="w-32 h-32 rounded-full bg-blue-100 flex items-center justify-center shadow-lg"
              >
                <img src="/mission.png" alt="Mission Icon" class="w-20 h-20" />
              </div>
            </div>
          </div>

          <!-- Vision Section (Icon on Left) -->
          <div class="relative flex items-start gap-8 py-8">
            <!-- Vertical line for Vision (on the left side) -->
            <div class="absolute left-[64px] top-0 bottom-0 w-0.5 bg-blue-600"></div>

            <!-- Icon circle -->
            <div class="relative z-10 flex-shrink-0">
              <div
                class="w-32 h-32 rounded-full bg-blue-100 flex items-center justify-center shadow-lg"
              >
                <img src="/vison.png" alt="Vision Icon" class="w-20 h-20" />
              </div>
            </div>

            <!-- Content -->
            <div class="flex-1 pt-8 pl-8">
              <h2 class="text-4xl font-bold text-blue-600 mb-6">Our Vision</h2>
              <p class="text-gray-900 text-lg leading-relaxed">
                To be the most respected Information Technology assurance and GRC firm of African
                Origin
              </p>
            </div>
          </div>
        </div>

        <h2 class="text-2xl font-semibold text-gray-900 mb-4 mt-12">Key Features</h2>
        <div class="grid md:grid-cols-2 gap-6 mb-6">
          <div class="border-l-4 border-blue-500 pl-4">
            <h3 class="font-semibold text-gray-900 mb-2 border-b-2 border-blue-600 pb-1 w-fit">
              Instant Visibility
            </h3>
            <p class="text-gray-600">
              Managers can monitor attendance as it happens, allowing quick responses to issues like 
              tardiness or unexpected absences.
            </p>
          </div>
          
          <div class="border-l-4 border-blue-500 pl-4">
            <h3 class="font-semibold text-gray-900 mb-2 border-b-2 border-blue-600 pb-1 w-fit">
              Location Verification
            </h3>
            <p class="text-gray-600">
              Integrates GPS, geofencing, or Wi-Fi to confirm employees are at approved work sites, 
              reducing time theft (e.g., buddy punching).
            </p>
          </div>
          
          <div class="border-l-4 border-blue-500 pl-4">
            <h3 class="font-semibold text-gray-900 mb-2 border-b-2 border-blue-600 pb-1 w-fit">
              Proactive Management
            </h3>
            <p class="text-gray-600">
              Provides alerts for approaching overtime or low staffing, helping optimize labor costs 
              and scheduling in real time.
            </p>
          </div>

          <div class="border-l-4 border-green-500 pl-4">
            <h3 class="font-semibold text-gray-900 mb-2 border-b-2 border-green-500 pb-1 w-fit">
              Comprehensive Reports
            </h3>
            <p class="text-gray-600">
              Generate detailed reports for better decision-making and compliance.
            </p>
          </div>
          
          <div class="border-l-4 border-purple-500 pl-4">
            <h3 class="font-semibold text-gray-900 mb-2 border-b-2 border-purple-500 pb-1 w-fit">
              User-Friendly Interface
            </h3>
            <p class="text-gray-600">
              Intuitive design that requires minimal training to get started.
            </p>
          </div>
          
          <div class="border-l-4 border-orange-500 pl-4">
            <h3 class="font-semibold text-gray-900 mb-2 border-b-2 border-orange-500 pb-1 w-fit">
              Secure & Reliable
            </h3>
            <p class="text-gray-600">
              Enterprise-grade security to protect your sensitive attendance data.
            </p>
          </div>
        </div>

        <h2 class="text-2xl font-semibold text-gray-900 mb-4 mt-8">Technology Stack</h2>
        <p class="text-gray-700 mb-4 leading-relaxed">
          Built with modern web technologies including Vue.js for a responsive frontend, ensuring
          fast performance and an excellent user experience across all devices.
        </p>
      </div>

      <!-- Contact/Support Section -->
      <div class="bg-blue-50 rounded-lg p-8">
        <h2 class="text-2xl font-semibold text-gray-900 mb-4 text-center">Need Help?</h2>
        <p class="text-gray-700 mb-6 text-center">
          Have questions or feedback? We're here to help you get the most out of our system.
        </p>

        <!-- Support Form -->
        <div class="max-w-2xl mx-auto mb-6">
          <form @submit.prevent="handleSubmit" class="space-y-4">
            <!-- Name Input -->
            <div>
              <label for="name" class="block text-sm font-medium text-gray-700 mb-2">
                Your Name
              </label>
              <input
                type="text"
                id="name"
                v-model="form.name"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="Enter your name"
                required
              />
            </div>

            <!-- Email Input -->
            <div>
              <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                Email Address
              </label>
              <input
                type="email"
                id="email"
                v-model="form.email"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                placeholder="your.email@example.com"
                required
              />
            </div>

            <!-- Subject/Issue Type -->
            <div>
              <label for="subject" class="block text-sm font-medium text-gray-700 mb-2">
                Subject
              </label>
              <select
                id="subject"
                v-model="form.subject"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                required
              >
                <option value="">Select a topic</option>
                <option value="bug">Bug Report</option>
                <option value="feature">Feature Request</option>
                <option value="feedback">General Feedback</option>
                <option value="support">Technical Support</option>
                <option value="other">Other</option>
              </select>
            </div>

            <!-- Message Textarea -->
            <div>
              <label for="message" class="block text-sm font-medium text-gray-700 mb-2">
                Your Message
              </label>
              <textarea
                id="message"
                v-model="form.message"
                rows="5"
                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent resize-none"
                placeholder="Describe your issue, observation, or feedback here..."
                required
              ></textarea>
            </div>

            <!-- Submit Button -->
            <div class="flex gap-4 justify-center pt-2">
              <button
                type="submit"
                class="bg-blue-600 text-white px-6 py-3 rounded-md hover:bg-blue-700 transition duration-200"
                :disabled="isSubmitting"
              >
                {{ isSubmitting ? 'Sending...' : 'Send Message' }}
              </button>
              <button
                type="button"
                @click="$router.go(-1)"
                class="bg-gray-200 text-gray-800 px-6 py-3 rounded-md hover:bg-gray-300 transition duration-200"
              >
                Go Back
              </button>
            </div>
          </form>

          <!-- Success Message -->
          <div
            v-if="showSuccess"
            class="mt-4 p-4 bg-green-100 border border-green-400 text-green-700 rounded-md"
          >
            {{ successMessage }}
          </div>

          <!-- Error Message -->
          <div
            v-if="errorMessage"
            class="mt-4 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md"
          >
            {{ errorMessage }}
          </div>
        </div>
      </div>

    </div>
  </div>
</template>

<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '@/stores/auth';
import api from '@/services/api';

const authStore = useAuthStore();

const form = ref({
  name: '',
  email: '',
  subject: '',
  message: ''
});

const isSubmitting = ref(false);
const showSuccess = ref(false);
const successMessage = ref('');
const errorMessage = ref('');

// Auto-fill user data if logged in
onMounted(() => {
  if (authStore.isAuthenticated && authStore.user) {
    form.value.name = authStore.userName || authStore.user.name || '';
    form.value.email = authStore.user.email || '';
  }
});

const handleSubmit = async () => {
  isSubmitting.value = true;
  errorMessage.value = '';
  showSuccess.value = false;

  try {
    // Send data to backend using your existing api service
    const response = await api.post('/api/contact', {
      name: form.value.name,
      email: form.value.email,
      message: form.value.message
      // Note: subject is stored but not sent to backend as per your requirement
    });
    
    // Show success message from backend
    showSuccess.value = true;
    successMessage.value = response.data.message || 'Thank you for your message! We\'ll get back to you soon.';
    
    // Reset form
    form.value = {
      name: '',
      email: '',
      subject: '',
      message: ''
    };
    
    // Hide success message after 5 seconds
    setTimeout(() => {
      showSuccess.value = false;
      successMessage.value = '';
    }, 5000);
    
  } catch (error) {
    console.error('Error submitting form:', error);
    
    // Handle different error scenarios
    if (error.response) {
      // Server responded with error status
      if (error.response.data.errors) {
        // Validation errors
        const errors = Object.values(error.response.data.errors).flat();
        errorMessage.value = errors.join(', ');
      } else {
        errorMessage.value = error.response.data.message || 'There was an error sending your message. Please try again.';
      }
    } else if (error.request) {
      // Request made but no response received
      errorMessage.value = 'Unable to reach the server. Please check your connection and try again.';
    } else {
      // Something else happened
      errorMessage.value = 'An unexpected error occurred. Please try again.';
    }
    
    // Hide error message after 7 seconds
    setTimeout(() => {
      errorMessage.value = '';
    }, 7000);
  } finally {
    isSubmitting.value = false;
  }
};
</script>

<style scoped>
/* Add custom styles if needed */
</style>