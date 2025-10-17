<script setup>
import { ref, computed } from "vue";
import { RouterLink, RouterView, useRoute } from "vue-router";
import { Home, Info, Menu, X } from "lucide-vue-next";

const route = useRoute();
const isSidebarOpen = ref(false);

// Check if current route should hide navigation
const shouldShowNavigation = computed(() => {
  // Hide navigation on login and register pages
  return route.meta.layout !== 'auth';
});

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};

const closeSidebar = () => {
  isSidebarOpen.value = false;
};
</script>

<template>
  <div class="app-container">
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
          'fixed top-0 left-0 h-full bg-gradient-to-b from-blue-600 to-blue-600 text-white shadow-2xl z-40 transition-transform duration-300 ease-in-out',
          isSidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
          'w-64',
        ]"
      >
        <div class="p-6">
          <div class="flex items-center gap-3 mb-8">
            <h1 class="text-3xl font-bold text-blue-400">welcome to InfoAssure</h1>
            <img
              src="/image.png"
              alt=""
              style="height: 30px; width: 30px; border-radius: 50%; object-fit: cover"
            />
          </div>

          <h2 class="text-2xl font-bold mb-8 text-blue-100">Dashboard</h2>

          <div class="space-y-2">
            <RouterLink
              to="/"
              @click="closeSidebar"
              class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-blue-700 hover:translate-x-1"
            >
              <Home :size="20" />
              <span class="font-medium">Home</span>
            </RouterLink>

            <RouterLink
              to="/about"
              @click="closeSidebar"
              class="flex items-center gap-3 px-4 py-3 rounded-lg transition-all duration-200 hover:bg-blue-700 hover:translate-x-1"
            >
              <Info :size="20" />
              <span class="font-medium">About</span>
            </RouterLink>
          </div>
        </div>
      </nav>
    </template>

    <!-- Main Content Area -->
    <main :class="shouldShowNavigation ? 'lg:ml-64 min-h-screen bg-gray-50' : 'min-h-screen bg-gray-50'">
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