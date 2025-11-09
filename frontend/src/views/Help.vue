<template>
  <div class="min-h-screen bg-blue-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
      <!-- Contact/Support Section -->
      <div class="bg-blue-50 rounded-lg p-8">
        <h2 class="text-2xl font-semibold text-gray-900 mb-4 text-center">Need Help?</h2>
        <p class="text-gray-700 mb-6 text-center">
          Have questions or feedback? We're here to help you get the most out of our system.
        </p>

        <!-- Support Form -->
        <div class="mb-6">
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
                {{ isSubmitting ? "Sending..." : "Send Message" }}
              </button>
              <button
                type="button"
                @click="$router.push('/')"
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
import { ref, onMounted } from "vue";
import { useAuthStore } from "@/stores/auth";
import api from "@/services/api";

const authStore = useAuthStore();

const form = ref({
  name: "",
  email: "",
  subject: "",
  message: "",
});

const isSubmitting = ref(false);
const showSuccess = ref(false);
const successMessage = ref("");
const errorMessage = ref("");

// Auto-fill user data if logged in
onMounted(() => {
  if (authStore.isAuthenticated && authStore.user) {
    form.value.name = authStore.userName || authStore.user.name || "";
    form.value.email = authStore.user.email || "";
  }
});

const handleSubmit = async () => {
  isSubmitting.value = true;
  errorMessage.value = "";
  showSuccess.value = false;

  try {
    const response = await api.post("/api/contact", {
      name: form.value.name,
      email: form.value.email,
      message: form.value.message,
    });

    showSuccess.value = true;
    successMessage.value =
      response.data.message || "Thank you for your message! We'll get back to you soon.";

    // Reset form
    form.value = {
      name: "",
      email: "",
      subject: "",
      message: "",
    };

    setTimeout(() => {
      showSuccess.value = false;
      successMessage.value = "";
    }, 5000);
  } catch (error) {
    console.error("Error submitting form:", error);

    if (error.response) {
      if (error.response.data.errors) {
        const errors = Object.values(error.response.data.errors).flat();
        errorMessage.value = errors.join(", ");
      } else {
        errorMessage.value =
          error.response.data.message ||
          "There was an error sending your message. Please try again.";
      }
    } else if (error.request) {
      errorMessage.value =
        "Unable to reach the server. Please check your connection and try again.";
    } else {
      errorMessage.value = "An unexpected error occurred. Please try again.";
    }

    setTimeout(() => {
      errorMessage.value = "";
    }, 7000);
  } finally {
    isSubmitting.value = false;
  }
};
</script>
