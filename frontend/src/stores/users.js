import { defineStore } from "pinia";
import { userService } from "@/services/userService";

export const useUsersStore = defineStore("users", {
  state: () => ({
    users: [],
    currentUser: null,
    departments: [],
    loading: false,
    error: null,
    pagination: {
      current_page: 1,
      last_page: 1,
      per_page: 15,
      total: 0,
    },
  }),

  getters: {
    activeUsers: (state) => state.users.filter((user) => user.status === "active"),
    inactiveUsers: (state) => state.users.filter((user) => user.status === "inactive"),
    workerUsers: (state) => state.users.filter((user) => user.role === "worker"),
    adminUsers: (state) => state.users.filter((user) => user.role === "admin"),
    totalUsers: (state) => state.users.length,

    // Get users by department
    usersByDepartment: (state) => {
      return state.users.reduce((acc, user) => {
        const dept = user.department || "No Department";
        if (!acc[dept]) {
          acc[dept] = [];
        }
        acc[dept].push(user);
        return acc;
      }, {});
    },

    // Get user statistics
    userStats: (state) => ({
      total: state.users.length,
      active: state.users.filter((user) => user.status === "active").length,
      inactive: state.users.filter((user) => user.status === "inactive").length,
      admins: state.users.filter((user) => user.role === "admin").length,
      workers: state.users.filter((user) => user.role === "worker").length,
    }),
  },

  actions: {
    async fetchUsers(params = {}) {
      this.loading = true;
      this.error = null;

      try {
        const response = await userService.getUsers(params);
        this.users = response.data;
        this.pagination = {
          current_page: response.current_page,
          last_page: response.last_page,
          per_page: response.per_page,
          total: response.total,
        };

        return response;
      } catch (error) {
        console.error("Failed to fetch users:", error);
        this.error = error.response?.data?.message || "Failed to fetch users";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async fetchUser(userId) {
      this.loading = true;
      this.error = null;

      try {
        const response = await userService.getUser(userId);
        this.currentUser = response.user;

        return response;
      } catch (error) {
        console.error("Failed to fetch user:", error);
        this.error = error.response?.data?.message || "Failed to fetch user";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async createUser(userData) {
      this.loading = true;
      this.error = null;

      try {
        const response = await userService.createUser(userData);

        // Add new user to the list
        this.users.unshift(response.user);

        return response;
      } catch (error) {
        this.error = error.response?.data?.message || "Failed to create user";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async updateUser(userId, userData) {
      this.loading = true;
      this.error = null;

      try {
        const response = await userService.updateUser(userId, userData);

        // Update user in the list if it exists
        const index = this.users.findIndex((user) => user.id === userId);
        if (index !== -1) {
          this.users[index] = { ...this.users[index], ...response.user };
        }

        // Update current user if it's the same
        if (this.currentUser && this.currentUser.id === userId) {
          this.currentUser = { ...this.currentUser, ...response.user };
        }

        return response;
      } catch (error) {
        this.error = error.response?.data?.message || "Failed to update user";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async deleteUser(userId) {
      this.loading = true;
      this.error = null;

      try {
        await userService.deleteUser(userId);

        // Remove user from list
        this.users = this.users.filter((user) => user.id !== userId);

        // Clear current user if it was deleted
        if (this.currentUser && this.currentUser.id === userId) {
          this.currentUser = null;
        }

        return true;
      } catch (error) {
        this.error = error.response?.data?.message || "Failed to delete user";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async updateUserStatus(userId, status) {
      this.loading = true;
      this.error = null;

      try {
        const response = await userService.updateUserStatus(userId, status);

        // Update user status in the list
        const index = this.users.findIndex((user) => user.id === userId);
        if (index !== -1) {
          this.users[index].status = status;
        }

        // Update current user status if it's the same
        if (this.currentUser && this.currentUser.id === userId) {
          this.currentUser.status = status;
        }

        return response;
      } catch (error) {
        this.error = error.response?.data?.message || "Failed to update user status";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async fetchDepartments() {
      try {
        const response = await userService.getDepartments();
        this.departments = response.departments || [];

        return response;
      } catch (error) {
        console.error("Failed to fetch departments:", error);
        // Don't set error for departments as it's not critical
      }
    },

    // Search users locally (for quick filtering)
    searchUsers(query) {
      if (!query.trim()) {
        return this.users;
      }

      const searchTerm = query.toLowerCase();
      return this.users.filter(
        (user) =>
          user.name.toLowerCase().includes(searchTerm) ||
          user.email.toLowerCase().includes(searchTerm) ||
          user.employee_id.toLowerCase().includes(searchTerm) ||
          (user.department && user.department.toLowerCase().includes(searchTerm))
      );
    },

    // Filter users by criteria
    filterUsers(criteria) {
      let filtered = [...this.users];

      if (criteria.role) {
        filtered = filtered.filter((user) => user.role === criteria.role);
      }

      if (criteria.status) {
        filtered = filtered.filter((user) => user.status === criteria.status);
      }

      if (criteria.department) {
        filtered = filtered.filter((user) => user.department === criteria.department);
      }

      return filtered;
    },

    // Clear store data
    clearUsers() {
      this.users = [];
      this.currentUser = null;
      this.departments = [];
      this.error = null;
    },

    clearCurrentUser() {
      this.currentUser = null;
    },

    clearError() {
      this.error = null;
    },
  },
});
