// File: frontend/src/services/authService.js
// Location: frontend/src/services/authService.js
// COMPLETE VERSION WITH PROPER CSRF TOKEN HANDLING

import api from "./api";

export const authService = {
  /**
   * Get CSRF token from Laravel Sanctum
   * This must be called before any state-changing requests (POST, PUT, DELETE)
   */
  async getCsrfToken() {
    try {
      await api.get("/sanctum/csrf-cookie");
      console.log("CSRF token refreshed successfully");
    } catch (error) {
      console.error("Failed to get CSRF token:", error);
      throw error;
    }
  },

  /**
   * Login user with credentials
   * @param {Object} credentials - {email, password}
   * @returns {Object} Response data with user info and token
   */
  async login(credentials) {
    try {
      // STEP 1: Get CSRF token BEFORE making login request
      await this.getCsrfToken();

      // STEP 2: Make login request with credentials
      const response = await api.post("/api/auth/login", credentials, {
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        withCredentials: true, // Critical for CSRF cookies
      });

      // STEP 3: Store auth data if login successful
      if (response.data.token) {
        localStorage.setItem("auth_token", response.data.token);
      }
      if (response.data.user) {
        localStorage.setItem("user", JSON.stringify(response.data.user));
      }

      console.log("Login successful:", response.data);
      return response.data;
    } catch (error) {
      console.error("AuthService login error:", error.response?.data || error.message);
      // Clear any partial auth data on login failure
      localStorage.removeItem("auth_token");
      localStorage.removeItem("user");
      throw error;
    }
  },

  /**
   * Register new user
   * @param {Object} userData - User registration data
   * @returns {Object} Response data with user info and token
   */
  async register(userData) {
    try {
      // STEP 1: Get CSRF token BEFORE making register request
      await this.getCsrfToken();

      // STEP 2: Make registration request
      const response = await api.post("/api/auth/register", userData, {
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        withCredentials: true,
      });

      // STEP 3: Store auth data if registration successful
      if (response.data.token) {
        localStorage.setItem("auth_token", response.data.token);
      }
      if (response.data.user) {
        localStorage.setItem("user", JSON.stringify(response.data.user));
      }

      console.log("Registration successful:", response.data);
      return response.data;
    } catch (error) {
      console.error("AuthService register error:", error.response?.data || error.message);
      throw error;
    }
  },

  /**
   * Logout current user
   * @returns {Object} Logout response data
   */
  async logout() {
    try {
      // STEP 1: Get CSRF token BEFORE making logout request
      await this.getCsrfToken();

      // STEP 2: Make logout request to server
      const response = await api.post(
        "/api/auth/logout",
        {},
        {
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          withCredentials: true,
        }
      );

      // STEP 3: Clear local storage regardless of server response
      localStorage.removeItem("auth_token");
      localStorage.removeItem("user");

      console.log("Logout successful");
      return response.data;
    } catch (error) {
      console.error("AuthService logout error:", error.response?.data || error.message);

      // IMPORTANT: Clear local storage even if server logout fails
      localStorage.removeItem("auth_token");
      localStorage.removeItem("user");

      throw error;
    }
  },

  /**
   * Get current authenticated user data from server
   * @returns {Object} Current user data
   */
  async getUser() {
    try {
      const response = await api.get("/api/auth/user", {
        headers: {
          Accept: "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        withCredentials: true,
      });

      // Update local storage with fresh user data
      if (response.data.user) {
        localStorage.setItem("user", JSON.stringify(response.data.user));
      }

      return response.data;
    } catch (error) {
      console.error("AuthService getUser error:", error.response?.data || error.message);

      // If unauthorized (401), clear local auth data
      if (error.response?.status === 401) {
        localStorage.removeItem("auth_token");
        localStorage.removeItem("user");
      }

      throw error;
    }
  },

  /**
   * Check if user is currently authenticated (has valid token)
   * @returns {boolean} Authentication status
   */
  isAuthenticated() {
    const token = localStorage.getItem("auth_token");
    return !!token;
  },

  /**
   * Get current user's role from local storage
   * @returns {string|null} User role or null if not found
   */
  getUserRole() {
    try {
      const userString = localStorage.getItem("user");
      if (!userString) return null;

      const user = JSON.parse(userString);
      return user.role || null;
    } catch (error) {
      console.error("Error parsing user data from localStorage:", error);
      return null;
    }
  },

  /**
   * Get current user data from local storage
   * @returns {Object|null} User data or null if not found
   */
  getCurrentUser() {
    try {
      const userString = localStorage.getItem("user");
      if (!userString) return null;

      return JSON.parse(userString);
    } catch (error) {
      console.error("Error parsing user data from localStorage:", error);
      return null;
    }
  },

  /**
   * Get current auth token from local storage
   * @returns {string|null} Auth token or null if not found
   */
  getAuthToken() {
    return localStorage.getItem("auth_token");
  },

  /**
   * Refresh CSRF token manually (utility method)
   * @returns {boolean} Success status
   */
  async refreshCsrfToken() {
    try {
      await this.getCsrfToken();
      return true;
    } catch (error) {
      console.error("Failed to refresh CSRF token:", error);
      return false;
    }
  },

  /**
   * Clear all authentication data from local storage
   * Useful for forced logouts or error recovery
   */
  clearAuthData() {
    localStorage.removeItem("auth_token");
    localStorage.removeItem("user");
    console.log("Authentication data cleared");
  },

  /**
   * Validate stored token by making a test request
   * @returns {boolean} Token validity status
   */
  async validateToken() {
    if (!this.isAuthenticated()) {
      return false;
    }

    try {
      await this.getUser();
      return true;
    } catch (error) {
      console.log("Token validation failed:", error.message);
      this.clearAuthData();
      return false;
    }
  },
};
