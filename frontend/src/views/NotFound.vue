
<template>
  <div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-md w-full space-y-8 text-center">
      <div>
        <h2 class="text-6xl font-bold text-gray-900">404</h2>
        <h3 class="mt-4 text-2xl font-bold text-gray-900">Page not found</h3>
        <p class="mt-2 text-gray-600">
          Sorry, we couldn't find the page you're looking for.
        </p>
      </div>
      
      <div class="space-y-4">
        <router-link to="/dashboard" class="btn-primary w-full">
          Go to Dashboard
        </router-link>
        
        <button @click="$router.go(-1)" class="btn-secondary w-full">
          Go Back
        </button>
      </div>
    </div>
  </div>
</template>


<script setup>
// This is a simple 404 page component
</script>