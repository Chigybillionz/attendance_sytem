// File: frontend/src/services/userService.js
// Location: frontend/src/services/userService.js

import api from "./api";

export const userService = {
  /**
   * Get CSRF token before making requests
   */
  async getCsrfToken() {
    try {
      await api.get("/sanctum/csrf-cookie");
    } catch (error) {
      console.error("Failed to get CSRF token:", error);
      throw error;
    }
  },

  /**
   * Get all users with pagination and filters
   * @param {Object} params - Query parameters
   * @returns {Object} Paginated users data
   */
  async getUsers(params = {}) {
    try {
      const response = await api.get("/api/users", {
        params,
        headers: {
          Accept: "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        withCredentials: true,
      });

      return response.data;
    } catch (error) {
      console.error("UserService getUsers error:", error.response?.data || error.message);
      throw error;
    }
  },

  /**
   * Get specific user by ID
   * @param {number} userId - User ID
   * @returns {Object} User data with statistics
   */
  async getUser(userId) {
    try {
      const response = await api.get(`/api/users/${userId}`, {
        headers: {
          Accept: "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        withCredentials: true,
      });

      return response.data;
    } catch (error) {
      console.error("UserService getUser error:", error.response?.data || error.message);
      throw error;
    }
  },

  /**
   * Create new user
   * @param {Object} userData - User data
   * @returns {Object} Created user data
   */
  async createUser(userData) {
    try {
      // Get CSRF token before making POST request
      await this.getCsrfToken();

      const response = await api.post("/api/users", userData, {
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        withCredentials: true,
      });

      return response.data;
    } catch (error) {
      console.error("UserService createUser error:", error.response?.data || error.message);
      throw error;
    }
  },

  /**
   * Update existing user
   * @param {number} userId - User ID
   * @param {Object} userData - Updated user data
   * @returns {Object} Updated user data
   */
  async updateUser(userId, userData) {
    try {
      // Get CSRF token before making PUT request
      await this.getCsrfToken();

      const response = await api.put(`/api/users/${userId}`, userData, {
        headers: {
          Accept: "application/json",
          "Content-Type": "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        withCredentials: true,
      });

      return response.data;
    } catch (error) {
      console.error("UserService updateUser error:", error.response?.data || error.message);
      throw error;
    }
  },

  /**
   * Delete user
   * @param {number} userId - User ID
   * @returns {Object} Delete response
   */
  async deleteUser(userId) {
    try {
      // Get CSRF token before making DELETE request
      await this.getCsrfToken();

      const response = await api.delete(`/api/users/${userId}`, {
        headers: {
          Accept: "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        withCredentials: true,
      });

      return response.data;
    } catch (error) {
      console.error("UserService deleteUser error:", error.response?.data || error.message);
      throw error;
    }
  },

  /**
   * Update user status (activate/deactivate)
   * @param {number} userId - User ID
   * @param {string} status - New status ('active' or 'inactive')
   * @returns {Object} Updated user data
   */
  async updateUserStatus(userId, status) {
    try {
      // Get CSRF token before making PATCH request
      await this.getCsrfToken();

      const response = await api.patch(
        `/api/users/${userId}/status`,
        { status },
        {
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          withCredentials: true,
        }
      );

      return response.data;
    } catch (error) {
      console.error("UserService updateUserStatus error:", error.response?.data || error.message);
      throw error;
    }
  },

  /**
   * Get available departments
   * @returns {Array} List of departments
   */
  async getDepartments() {
    try {
      const response = await api.get("/api/users/departments", {
        headers: {
          Accept: "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        withCredentials: true,
      });

      return response.data;
    } catch (error) {
      console.error("UserService getDepartments error:", error.response?.data || error.message);
      throw error;
    }
  },

  /**
   * Get user statistics for dashboard
   * @returns {Object} User statistics
   */
  async getUserStats() {
    try {
      const response = await api.get("/api/users/stats", {
        headers: {
          Accept: "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        withCredentials: true,
      });

      return response.data;
    } catch (error) {
      console.error("UserService getUserStats error:", error.response?.data || error.message);
      throw error;
    }
  },

  /**
   * Bulk operations on users
   * @param {string} action - Action to perform ('activate', 'deactivate', 'delete')
   * @param {Array} userIds - Array of user IDs
   * @returns {Object} Bulk operation response
   */
  async bulkAction(action, userIds) {
    try {
      // Get CSRF token before making POST request
      await this.getCsrfToken();

      const response = await api.post(
        "/api/users/bulk-action",
        {
          action,
          user_ids: userIds,
        },
        {
          headers: {
            Accept: "application/json",
            "Content-Type": "application/json",
            "X-Requested-With": "XMLHttpRequest",
          },
          withCredentials: true,
        }
      );

      return response.data;
    } catch (error) {
      console.error("UserService bulkAction error:", error.response?.data || error.message);
      throw error;
    }
  },

  /**
   * Search users
   * @param {string} searchTerm - Search term
   * @param {Object} filters - Additional filters
   * @returns {Object} Search results
   */
  async searchUsers(searchTerm, filters = {}) {
    try {
      const params = {
        search: searchTerm,
        ...filters,
      };

      const response = await api.get("/api/users", {
        params,
        headers: {
          Accept: "application/json",
          "X-Requested-With": "XMLHttpRequest",
        },
        withCredentials: true,
      });

      return response.data;
    } catch (error) {
      console.error("UserService searchUsers error:", error.response?.data || error.message);
      throw error;
    }
  },

  /**
   * Export users data
   * @param {string} format - Export format ('csv' or 'excel')
   * @param {Object} filters - Export filters
   * @returns {Blob} Export file
   */
  async exportUsers(format = "csv", filters = {}) {
    try {
      const response = await api.get(`/api/users/export/${format}`, {
        params: filters,
        responseType: "blob",
        headers: {
          Accept:
            format === "csv"
              ? "text/csv"
              : "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
          "X-Requested-With": "XMLHttpRequest",
        },
        withCredentials: true,
      });

      return response.data;
    } catch (error) {
      console.error("UserService exportUsers error:", error.response?.data || error.message);
      throw error;
    }
  },

  /**
   * Validate user data before submission
   * @param {Object} userData - User data to validate
   * @param {boolean} isUpdate - Whether this is an update operation
   * @returns {Object} Validation result
   */
  validateUserData(userData, isUpdate = false) {
    const errors = {};

    // Name validation
    if (!userData.name || userData.name.trim().length < 2) {
      errors.name = "Name must be at least 2 characters long";
    }

    // Email validation
    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!userData.email || !emailRegex.test(userData.email)) {
      errors.email = "Please enter a valid email address";
    }

    // Employee ID validation
    if (!userData.employee_id || userData.employee_id.trim().length < 3) {
      errors.employee_id = "Employee ID must be at least 3 characters long";
    }

    // Password validation (only for create or when password is provided)
    if (!isUpdate && (!userData.password || userData.password.length < 6)) {
      errors.password = "Password must be at least 6 characters long";
    }

    if (!isUpdate && userData.password !== userData.password_confirmation) {
      errors.password_confirmation = "Password confirmation does not match";
    }

    // Role validation
    if (!userData.role || !["admin", "worker"].includes(userData.role)) {
      errors.role = "Please select a valid role";
    }

    return {
      isValid: Object.keys(errors).length === 0,
      errors,
    };
  },
};
