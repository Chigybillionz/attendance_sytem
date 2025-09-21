import { defineStore } from "pinia";
import { authService } from "@/services/authService";

export const useAuthStore = defineStore("auth", {
  state: () => ({
    user: JSON.parse(localStorage.getItem("user")) || null,
    token: localStorage.getItem("auth_token") || null,
    loading: false,
    error: null,
  }),

  getters: {
    isAuthenticated: (state) => !!state.token && !!state.user,
    isAdmin: (state) => state.user?.role === "admin",
    isWorker: (state) => state.user?.role === "worker",
    userName: (state) => state.user?.name || "",
    userEmail: (state) => state.user?.email || "",
    employeeId: (state) => state.user?.employee_id || "",
  },

  actions: {
    async login(credentials) {
      this.loading = true;
      this.error = null;

      try {
        const response = await authService.login(credentials);

        this.user = response.user;
        this.token = response.token;

        // Store in localStorage
        localStorage.setItem("user", JSON.stringify(response.user));
        localStorage.setItem("auth_token", response.token);

        return response;
      } catch (error) {
        this.error = error.response?.data?.message || "Login failed";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async register(userData) {
      this.loading = true;
      this.error = null;

      try {
        const response = await authService.register(userData);

        this.user = response.user;
        this.token = response.token;

        // Store in localStorage
        localStorage.setItem("user", JSON.stringify(response.user));
        localStorage.setItem("auth_token", response.token);

        return response;
      } catch (error) {
        this.error = error.response?.data?.message || "Registration failed";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async logout() {
      this.loading = true;

      try {
          await authService.logout();
      } catch (error) {
        console.error("Logout error:", error);
      } finally {
        this.user = null;
        this.token = null;
        this.error = null;
        this.loading = false;

        // Clear localStorage
        localStorage.removeItem("user");
        localStorage.removeItem("auth_token");
      }
    },

    async fetchUser() {
      if (!this.token) return;

      try {
        const response = await authService.getUser();
        this.user = response.user;
        localStorage.setItem("user", JSON.stringify(response.user));
      } catch (error) {
        console.error("Failed to fetch user:", error);
        this.logout();
      }
    },

    clearError() {
      this.error = null;
    },
  },
});
