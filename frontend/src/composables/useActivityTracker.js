// frontend/src/composables/useActivityTracker.js
// FAST TEST VERSION - Shows warning after 5 seconds, logout after 10 seconds
import { ref, onMounted, onUnmounted } from "vue";
import { useAuthStore } from "@/stores/auth";
import { useRouter } from "vue-router";
import { authService } from "@/services/authService";

export function useActivityTracker() {
  const authStore = useAuthStore();
  const router = useRouter();

  const lastActivity = ref(Date.now());
  const showWarning = ref(false);
  const countdown = ref(5);

  // PRODUCTION TIMINGS - 2 minute inactivity timeout
  const INACTIVITY_TIMEOUT = 2 * 60 * 1000; // 2 minutes in milliseconds
  const WARNING_TIME = 1.5 * 60 * 1000; // Show warning at 1:30 (1.5 minutes)
  const WARNING_DURATION = 30; // 30 seconds warning countdown
  const REFRESH_INTERVAL = 1 * 60 * 1000; // Refresh token every 1 minute

  let activityCheckInterval = null;
  let tokenRefreshInterval = null;
  let countdownInterval = null;

  // Track user activity
  const updateActivity = () => {
    console.log("👆 User activity detected");
    const now = Date.now();
    lastActivity.value = now;
    // Save to localStorage
    localStorage.setItem("last_activity", now.toString());

    if (showWarning.value) {
      console.log("✅ Warning dismissed - user is active");
      showWarning.value = false;
      countdown.value = WARNING_DURATION;
      if (countdownInterval) {
        clearInterval(countdownInterval);
        countdownInterval = null;
      }
    }
  };

  // Events to track - ONLY intentional user actions
  const events = ["click", "keydown", "touchstart", "submit"];

  // Check for inactivity
  const checkInactivity = () => {
    if (!authStore.isAuthenticated) return;

    const now = Date.now();
    const timeSinceLastActivity = now - lastActivity.value;

    // Show warning at WARNING_TIME
    if (timeSinceLastActivity >= WARNING_TIME && !showWarning.value) {
      console.log("⚠️ Showing inactivity warning - user has been idle for 1.5 minutes");
      showWarning.value = true;
      countdown.value = WARNING_DURATION;
      startCountdown();
    }

    // Auto logout at INACTIVITY_TIMEOUT
    if (timeSinceLastActivity >= INACTIVITY_TIMEOUT) {
      console.log("🚪 Auto-logout triggered - 2 minutes of inactivity");
      handleAutoLogout();
    }
  };

  // Refresh token periodically
  const refreshToken = async () => {
    if (!authStore.isAuthenticated) return;

    try {
      const response = await authService.refreshToken();
      authStore.token = response.token;
      localStorage.setItem("auth_token", response.token);
      console.log("🔄 Token refreshed successfully");
    } catch (error) {
      console.error("❌ Token refresh failed:", error);
      if (error.response?.status === 401) {
        handleAutoLogout();
      }
    }
  };

  // Start countdown timer
  const startCountdown = () => {
    console.log("⏱️ Countdown started - 30 seconds until auto-logout");
    if (countdownInterval) {
      clearInterval(countdownInterval);
    }

    countdownInterval = setInterval(() => {
      countdown.value--;
      console.log(`⏳ Countdown: ${countdown.value} seconds remaining`);

      if (countdown.value <= 0) {
        console.log("⏰ Countdown finished - logging out user");
        clearInterval(countdownInterval);
        countdownInterval = null;
        handleAutoLogout();
      }
    }, 1000);
  };

  // Handle auto logout
  const handleAutoLogout = async () => {
    console.log("🚪 Logging out user due to inactivity");
    showWarning.value = false;
    clearAllIntervals();

    // Clear activity tracking from localStorage
    localStorage.removeItem("last_activity");

    try {
      await authStore.logout();
    } catch (error) {
      console.error("Logout error:", error);
    } finally {
      // Always clear local data even if API call fails
      localStorage.removeItem("user");
      localStorage.removeItem("auth_token");
      router.push("/login");

      // Show alert after redirect
      setTimeout(() => {
        alert("You have been logged out due to 2 minutes of inactivity");
      }, 100);
    }
  };

  // Stay logged in (refresh token and reset activity)
  const stayLoggedIn = async () => {
    console.log('✅ User clicked "Stay Logged In" - refreshing token');
    updateActivity();
    showWarning.value = false;
    countdown.value = WARNING_DURATION;
    if (countdownInterval) {
      clearInterval(countdownInterval);
      countdownInterval = null;
    }
    await refreshToken();
  };

  // Clear all intervals
  const clearAllIntervals = () => {
    if (activityCheckInterval) clearInterval(activityCheckInterval);
    if (tokenRefreshInterval) clearInterval(tokenRefreshInterval);
    if (countdownInterval) clearInterval(countdownInterval);
  };

  // Initialize tracking
  const startTracking = () => {
    if (!authStore.isAuthenticated) {
      console.log("⏸️ User not authenticated - tracking not started");
      return;
    }

    console.log("▶️ Activity tracking started - 2 minute timeout with 30 second warning");

    // Add event listeners
    events.forEach((event) => {
      window.addEventListener(event, updateActivity, { passive: true });
    });

    // Check inactivity every 500ms for better accuracy
    activityCheckInterval = setInterval(checkInactivity, 500);

    // Refresh token every 3.5 minutes if user is active
    tokenRefreshInterval = setInterval(() => {
      const timeSinceLastActivity = Date.now() - lastActivity.value;
      // Only refresh if user was active in the last 3.5 minutes
      if (timeSinceLastActivity < REFRESH_INTERVAL) {
        console.log("🔄 Auto-refreshing token - user is active");
        refreshToken();
      }
    }, REFRESH_INTERVAL);
  };

  // Stop tracking
  const stopTracking = () => {
    console.log("⏹️ Activity tracking stopped");
    events.forEach((event) => {
      window.removeEventListener(event, updateActivity);
    });
    clearAllIntervals();
  };

  onMounted(() => {
    startTracking();
  });

  onUnmounted(() => {
    stopTracking();
  });

  return {
    showWarning,
    countdown,
    stayLoggedIn,
    handleAutoLogout,
    startTracking,
    stopTracking,
  };
}
