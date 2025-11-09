<template>
  <Teleport to="body">
    <Transition name="fade">
      <div
        v-if="showWarning"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black bg-opacity-50"
      >
        <div class="bg-white rounded-lg shadow-2xl p-8 max-w-md w-full mx-4 animate-pulse-slow">
          <div class="flex flex-col items-center text-center">
            <!-- Warning Icon -->
            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mb-4">
              <svg
                class="w-10 h-10 text-yellow-600"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
              >
                <path
                  stroke-linecap="round"
                  stroke-linejoin="round"
                  stroke-width="2"
                  d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"
                />
              </svg>
            </div>

            <!-- Title -->
            <h3 class="text-2xl font-bold text-gray-900 mb-2">Session Expiring Soon</h3>

            <!-- Message -->
            <p class="text-gray-600 mb-6">
              You will be logged out in
              <span class="font-bold text-red-600 text-xl">{{ countdown }}</span> seconds due to
              inactivity.
            </p>

            <!-- Progress Bar -->
            <div class="w-full bg-gray-200 rounded-full h-2 mb-6 overflow-hidden">
              <div
                class="bg-red-600 h-2 transition-all duration-1000 ease-linear"
                :style="{ width: `${(countdown / 30) * 100}%` }"
              ></div>
            </div>

            <!-- Buttons -->
            <div class="flex gap-4 w-full">
              <button
                @click="handleStayLoggedIn"
                class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 px-6 rounded-lg transition-colors duration-200 shadow-lg"
              >
                Stay Logged In
              </button>
              <button
                @click="handleLogout"
                class="flex-1 bg-gray-300 hover:bg-gray-400 text-gray-700 font-semibold py-3 px-6 rounded-lg transition-colors duration-200"
              >
                Logout Now
              </button>
            </div>
          </div>
        </div>
      </div>
    </Transition>
  </Teleport>
</template>

<script setup>
import { defineProps, defineEmits } from "vue";

const props = defineProps({
  showWarning: {
    type: Boolean,
    required: true,
  },
  countdown: {
    type: Number,
    required: true,
  },
});

const emit = defineEmits(["stay-logged-in", "logout"]);

const handleStayLoggedIn = () => {
  emit("stay-logged-in");
};

const handleLogout = () => {
  emit("logout");
};
</script>

<style scoped>
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

@keyframes pulse-slow {
  0%,
  100% {
    transform: scale(1);
  }
  50% {
    transform: scale(1.02);
  }
}

.animate-pulse-slow {
  animation: pulse-slow 2s ease-in-out infinite;
}
</style>
