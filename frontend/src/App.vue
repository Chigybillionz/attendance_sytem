<script setup>
import { ref } from 'vue';
import { RouterLink, RouterView } from "vue-router";
import { Home, Info, Menu, X } from 'lucide-vue-next';

const isSidebarOpen = ref(false);

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};

const closeSidebar = () => {
  isSidebarOpen.value = false;
};
</script>

<template>
  <div class="app-container">
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
        'w-64 ',
      ]"
    >
      <div class="p-6">
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

    <!-- Main Content Area -->
    <main class="lg:ml-64 min-h-screen bg-gray-50">
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
my old code 

<!-- <script setup>
import { RouterLink, RouterView } from "vue-router";
</script>

<template>
  <header>
    <img alt="Vue logo" class="logo" src="@/assets/logo.svg" width="125" height="125" />
    <div class="wrapper">
      <HelloWorld msg="You did it!" />
      <nav class="bg-blue-600 w-screen">
        <RouterLink to="/" class="home  bg-blue-600 border border-blue-600 rounded-lg p-4 hover:bg-white">Home</RouterLink>
        <RouterLink to="/about" class="about bg-blue-50 border border-blue-200 rounded-lg p-4  hover:bg-blue-600 text-white">About</RouterLink>
      </nav>
    </div>
  </header>
  this connect my whole appliaction to show on the web browser
  <RouterView />
</template>

<style scoped>
header {
  line-height: 2.5;
  max-height: 100vh;
}

.logo {
  display: block;
  margin: 0 auto 2rem;
}

nav {
  width: 100%;
  font-size: 20px;
  text-align: center;
  margin-top: 2rem;
}

nav .home {
  font-weight: bold;
  background: blue;
}

nav .about {
  font-weight: bold;
  background: rgb(36, 30, 80);
}

nav a.router-link-exact-active {
  color: var(--color-text);
}

nav a.router-link-exact-active:hover {
  background-color: transparent;
}

nav a {
  display: inline-block;
  padding: 0 1rem;
  border-left: 1px solid var(--color-border);
}

nav a:first-of-type {
  border: 0;
}

@media (min-width: 1024px) {
  header {
    display: flex;
    place-items: center;
    padding-right: calc(var(--section-gap) / 2);
  }

  .logo {
    margin: 0 2rem 0 0;
  }

  header .wrapper {
    display: flex;
    place-items: flex-start;
    flex-wrap: wrap;
  }

  nav {
    text-align: left;
    margin-left: -1rem;
    font-size: 1rem;
    padding: 1rem 0;
    margin-top: 1rem;
  }
}
</style> -->