<script setup>
import { ref, computed, onMounted } from "vue";
import { RouterLink, RouterView, useRoute } from "vue-router";
import { Home, Info, Menu, X, Mail, MessageSquare, Sun, Moon } from "lucide-vue-next";
import { useActivityTracker } from "@/composables/useActivityTracker";
import InactivityWarning from "@/components/InactivityWarning.vue";
import { useAuthStore } from "@/stores/auth";

const route = useRoute();
const authStore = useAuthStore();
const isSidebarOpen = ref(false);

// Dark mode state - Load from localStorage
const isDarkMode = ref(localStorage.getItem("darkMode") === "true");

// Activity tracking
const { showWarning, countdown, stayLoggedIn, handleAutoLogout } = useActivityTracker();

// Check if current route should hide navigation
const shouldShowNavigation = computed(() => {
  return route.meta.layout !== "auth";
});

// Check if user is admin
const isAdmin = computed(() => {
  return authStore.user?.role === "admin" || authStore.user?.is_admin === true;
});

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};

const closeSidebar = () => {
  isSidebarOpen.value = false;
};

// Toggle dark mode
const toggleDarkMode = () => {
  isDarkMode.value = !isDarkMode.value;
  localStorage.setItem("darkMode", isDarkMode.value);
  applyDarkMode();
};

// Apply dark mode to document
const applyDarkMode = () => {
  if (isDarkMode.value) {
    document.documentElement.classList.add("dark");
  } else {
    document.documentElement.classList.remove("dark");
  }
};

// Apply dark mode on mount
onMounted(() => {
  applyDarkMode();
});
</script>

<template>
  <div class="app-container">
    <!-- Inactivity Warning Dialog -->
    <InactivityWarning
      v-if="authStore.isAuthenticated"
      :show-warning="showWarning"
      :countdown="countdown"
      @stay-logged-in="stayLoggedIn"
      @logout="handleAutoLogout"
    />

    <!-- Dark Mode Toggle Button (Floating) -->
    <button
      v-if="shouldShowNavigation"
      @click="toggleDarkMode"
      class="fixed bottom-4 right-4 z-50 bg-blue-600 dark:bg-gray-700 text-white p-3 rounded-full shadow-lg hover:bg-blue-700 dark:hover:bg-gray-600 transition-all duration-300"
      :title="isDarkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
    >
      <Sun v-if="isDarkMode" :size="24" />
      <Moon v-else :size="24" />
    </button>

    <!-- Only show navigation if not on auth pages -->
    <template v-if="shouldShowNavigation">
      <!-- Mobile Hamburger Button -->
      <button
        @click="toggleSidebar"
        class="fixed top-4 left-4 z-50 lg:hidden bg-blue-600 text-white p-3 rounded-lg shadow-lg hover:bg-blue-700 transition"
      >
        <Menu v-if="!isSidebarOpen" :size="24" />
        <X v-if="isSidebarOpen" :size="24" />
      </button>

      <!-- Overlay for mobile -->
      <div
        v-if="isSidebarOpen"
        @click="closeSidebar"
        class="fixed inset-0 bg-black bg-opacity-50 z-30 lg:hidden"
      ></div>

      <!-- Sidebar Navigation -->
      <nav
        :class="[
          'fixed top-0 left-0 h-full bg-gradient-to-b from-blue-600 to-blue-600 dark:from-gray-800 dark:to-gray-900 text-white shadow-2xl z-40 transition-all duration-300 ease-in-out overflow-y-auto',
          isSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
          'w-64',
        ]"
      >
        <div class="p-6">
          <div class="flex items-center gap-3 mb-8">
            <h1 class="text-3xl font-bold text-blue-400 dark:text-blue-300">
              welcome to InfoAssure
            </h1>
            <img
              src="/image.png"
              alt=""
              style="height: 30px; width: 30px; border-radius: 50%; object-fit: cover"
            />
          </div>

          <h2 class="text-2xl font-bold mb-8 text-blue-100 dark:text-gray-200">Dashboard</h2>

          <div class="space-y-2">
            <RouterLink
              to="/"
              @click="closeSidebar"
              class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-blue-700 dark:hover:bg-gray-700 hover:translate-x-1"
            >
              <Home :size="20" />
              <span class="font-medium">Home</span>
            </RouterLink>

            <RouterLink
              to="/about"
              @click="closeSidebar"
              class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-blue-700 dark:hover:bg-gray-700 hover:translate-x-1"
            >
              <Info :size="20" />
              <span class="font-medium">About</span>
            </RouterLink>

            <RouterLink
              to="/help"
              @click="closeSidebar"
              class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-blue-700 dark:hover:bg-gray-700 hover:translate-x-1"
            >
              <Mail :size="20" />
              <span class="font-medium">Need Help?</span>
            </RouterLink>

            <!-- Admin Only: View Feedbacks -->
            <RouterLink
              v-if="isAdmin"
              to="/admin/feedbacks"
              @click="closeSidebar"
              class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-blue-700 dark:hover:bg-gray-700 hover:translate-x-1 border-t border-blue-500 dark:border-gray-600 mt-4 pt-4"
            >
              <MessageSquare :size="20" />
              <span class="font-medium">View Feedbacks</span>
            </RouterLink>
          </div>
        </div>
      </nav>
    </template>

    <!-- Main Content Area -->
    <main
      :class="[
        shouldShowNavigation ? 'lg:ml-64' : '',
        'min-h-screen bg-gray-50 dark:bg-gray-900 dark:text-gray-100 transition-colors duration-300',
      ]"
    >
      <RouterView />
    </main>
  </div>
</template>

<style scoped>
.router-link-active {
  background-color: rgba(59, 130, 246, 0.5);
  border-left: 4px solid #60a5fa;
}

.router-link-exact-active {
  background-color: rgba(37, 99, 235, 0.8);
  border-left: 4px solid #3b82f6;
}
</style>
