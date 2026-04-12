<template>
  <div class="min-h-screen bg-blue-100 py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-6xl mx-auto">
      <!-- Header -->
      <div class="bg-white rounded-lg shadow-md p-6 mb-6">
        <div class="flex justify-between items-center">
          <div>
            <h1 class="text-3xl font-bold text-gray-900 mb-2">User Feedback</h1>
            <p class="text-gray-600">View and manage all user feedback submissions</p>
          </div>
          <button
            @click="fetchFeedbacks"
            class="bg-blue-600 text-white px-4 py-2 rounded-md hover:bg-blue-700 transition duration-200 flex items-center gap-2"
            :disabled="loading"
          >
            <svg
              v-if="loading"
              class="animate-spin h-5 w-5"
              xmlns="http://www.w3.org/2000/svg"
              fill="none"
              viewBox="0 0 24 24"
            >
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
            {{ loading ? "Loading..." : "Refresh" }}
          </button>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-gray-500 text-sm font-medium mb-2">Total Feedbacks</h3>
          <p class="text-3xl font-bold text-blue-600">{{ feedbacks.length }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-gray-500 text-sm font-medium mb-2">Bug Reports</h3>
          <p class="text-3xl font-bold text-red-600">{{ countBySubject("bug") }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-gray-500 text-sm font-medium mb-2">Feature Requests</h3>
          <p class="text-3xl font-bold text-green-600">{{ countBySubject("feature") }}</p>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
          <h3 class="text-gray-500 text-sm font-medium mb-2">Support Tickets</h3>
          <p class="text-3xl font-bold text-orange-600">{{ countBySubject("support") }}</p>
        </div>
      </div>

      <!-- Filters -->
      <div class="bg-white rounded-lg shadow-md p-4 mb-6">
        <div class="flex flex-wrap gap-4">
          <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-2">Filter by Subject</label>
            <select
              v-model="filterSubject"
              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            >
              <option value="">All Subjects</option>
              <option value="bug">Bug Report</option>
              <option value="feature">Feature Request</option>
              <option value="feedback">General Feedback</option>
              <option value="support">Technical Support</option>
              <option value="other">Other</option>
            </select>
          </div>
          <div class="flex-1 min-w-[200px]">
            <label class="block text-sm font-medium text-gray-700 mb-2">Search</label>
            <input
              v-model="searchQuery"
              type="text"
              placeholder="Search by name, email, or message..."
              class="w-full px-4 py-2 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-transparent"
            />
          </div>
        </div>
      </div>

      <!-- Error Message -->
      <div
        v-if="errorMessage"
        class="mb-6 p-4 bg-red-100 border border-red-400 text-red-700 rounded-md"
      >
        {{ errorMessage }}
      </div>

      <!-- Feedbacks List -->
      <div v-if="!loading && filteredFeedbacks.length > 0" class="space-y-4">
        <div
          v-for="feedback in filteredFeedbacks"
          :key="feedback.id"
          class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition-shadow duration-200"
        >
          <div class="flex justify-between items-start mb-4">
            <div>
              <h3 class="text-lg font-semibold text-gray-900">{{ feedback.name }}</h3>
              <p class="text-sm text-gray-600">{{ feedback.email }}</p>
            </div>
            <div class="text-right">
              <span
                :class="getSubjectBadgeClass(feedback.subject)"
                class="inline-block px-3 py-1 rounded-full text-xs font-medium mb-2"
              >
                {{ getSubjectLabel(feedback.subject) }}
              </span>
              <p class="text-xs text-gray-500">{{ formatDate(feedback.created_at) }}</p>
            </div>
          </div>

          <div class="border-t border-gray-200 pt-4">
            <p class="text-gray-700 whitespace-pre-wrap">{{ feedback.message }}</p>
          </div>

          <div class="flex justify-end gap-2 mt-4">
            <button
              @click="deleteFeedback(feedback.id)"
              class="text-red-600 hover:text-red-700 text-sm font-medium"
            >
              Delete
            </button>
          </div>
        </div>
      </div>

      <!-- Empty State -->
      <div
        v-else-if="!loading && filteredFeedbacks.length === 0"
        class="bg-white rounded-lg shadow-md p-12 text-center"
      >
        <svg
          class="mx-auto h-12 w-12 text-gray-400 mb-4"
          fill="none"
          viewBox="0 0 24 24"
          stroke="currentColor"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            stroke-width="2"
            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"
          />
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">No feedback found</h3>
        <p class="text-gray-600">
          {{
            searchQuery || filterSubject
              ? "Try adjusting your filters"
              : "No feedback submissions yet"
          }}
        </p>
      </div>

      <!-- Loading State -->
      <div v-if="loading" class="bg-white rounded-lg shadow-md p-12 text-center">
        <svg
          class="animate-spin h-12 w-12 text-blue-600 mx-auto mb-4"
          xmlns="http://www.w3.org/2000/svg"
          fill="none"
          viewBox="0 0 24 24"
        >
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
        <p class="text-gray-600">Loading feedbacks...</p>
      </div>
    </div>
  </div>
</template>

<script setup>
import { ref, computed, onMounted } from "vue";
import api from "@/services/api";

const feedbacks = ref([]);
const loading = ref(false);
const errorMessage = ref("");
const filterSubject = ref("");
const searchQuery = ref("");

// Fetch all feedbacks
const fetchFeedbacks = async () => {
  loading.value = true;
  errorMessage.value = "";

  try {
    const response = await api.get("/api/admin/feedbacks");
    feedbacks.value = response.data.feedbacks || response.data || [];
  } catch (error) {
    console.error("Error fetching feedbacks:", error);
    errorMessage.value = "Failed to load feedbacks. Please try again.";
  } finally {
    loading.value = false;
  }
};

// Delete feedback
const deleteFeedback = async (id) => {
  if (!confirm("Are you sure you want to delete this feedback?")) {
    return;
  }

  try {
    await api.delete(`/api/admin/feedbacks/${id}`);
    feedbacks.value = feedbacks.value.filter((f) => f.id !== id);
  } catch (error) {
    console.error("Error deleting feedback:", error);
    errorMessage.value = "Failed to delete feedback. Please try again.";
  }
};

// Filter feedbacks
const filteredFeedbacks = computed(() => {
  let filtered = feedbacks.value;

  // Filter by subject
  if (filterSubject.value) {
    filtered = filtered.filter((f) => f.subject === filterSubject.value);
  }

  // Filter by search query
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase();
    filtered = filtered.filter(
      (f) =>
        f.name.toLowerCase().includes(query) ||
        f.email.toLowerCase().includes(query) ||
        f.message.toLowerCase().includes(query)
    );
  }

  return filtered;
});

// Count feedbacks by subject
const countBySubject = (subject) => {
  return feedbacks.value.filter((f) => f.subject === subject).length;
};

// Get subject badge class
const getSubjectBadgeClass = (subject) => {
  const classes = {
    bug: "bg-red-100 text-red-800",
    feature: "bg-green-100 text-green-800",
    feedback: "bg-blue-100 text-blue-800",
    support: "bg-orange-100 text-orange-800",
    other: "bg-gray-100 text-gray-800",
  };
  return classes[subject] || classes.other;
};

// Get subject label
const getSubjectLabel = (subject) => {
  const labels = {
    bug: "Bug Report",
    feature: "Feature Request",
    feedback: "General Feedback",
    support: "Technical Support",
    other: "Other",
  };
  return labels[subject] || "Unknown";
};

// Format date
const formatDate = (dateString) => {
  const date = new Date(dateString);
  return date.toLocaleString("en-US", {
    year: "numeric",
    month: "short",
    day: "numeric",
    hour: "2-digit",
    minute: "2-digit",
  });
};

// Load feedbacks on mount
onMounted(() => {
  fetchFeedbacks();
});
</script>
