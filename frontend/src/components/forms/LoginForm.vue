<template>
  <form @submit.prevent="$emit('submit', form)" class="space-y-4">
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
      />
      <p v-if="errors.email" class="mt-1 text-sm text-red-600">{{ errors.email }}</p>
    </div>

    <div>
      <label for="password" class="block text-sm font-medium text-gray-700 mb-1"> Password </label>
      <input
        id="password"
        v-model="form.password"
        type="password"
        required
        class="input-field"
        :class="{ 'border-red-300 focus:ring-red-500': errors.password }"
        placeholder="Enter your password"
      />
      <p v-if="errors.password" class="mt-1 text-sm text-red-600">{{ errors.password }}</p>
    </div>

    <!-- Added hidden input field for CSRF token -->
    <input type="hidden" name="_token" :value="csrfToken" />

    <button
      type="submit"
      :disabled="loading"
      class="w-full btn-primary"
      :class="{ 'opacity-50 cursor-not-allowed': loading }"
    >
      <span v-if="loading" class="mr-2">
        <svg class="animate-spin h-4 w-4 text-white inline" fill="none" viewBox="0 0 24 24">
          <circle
            class="opacity-25"
            cx="12"
            cy="12"
            r="10"
            stroke="currentColor"
            stroke-width="4"
          ></circle>
          <path
            class="opacity-75"
            fill="currentColor"
            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
          ></path>
        </svg>
      </span>
      {{ loading ? "Signing in..." : "Sign In" }}
    </button>
  </form>
</template>

<script setup>
import { reactive, ref } from "vue";

defineProps({
  loading: {
    type: Boolean,
    default: false,
  },
  errors: {
    type: Object,
    default: () => ({}),
  },
});

defineEmits(["submit"]);

const form = reactive({
  email: "",
  password: "",
});

// Added code to fetch CSRF token from meta tag
const csrfToken = ref(document.querySelector('meta[name="csrf-token"]').getAttribute("content"));
</script>
