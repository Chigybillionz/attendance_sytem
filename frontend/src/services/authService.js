

import api from "./api";

export const authService = {
  // Get CSRF token
  async getCsrfToken() {
    return await api.get("/sanctum/csrf-cookie");
  },

  // Login user
  async login(credentials) {
    await this.getCsrfToken();
    const response = await api.post("/api/auth/login", credentials);
    return response.data;
  },

  // Register user
  async register(userData) {
    await this.getCsrfToken();
    const response = await api.post("/api/auth/register", userData);
    return response.data;
  },

  // Logout user
  async logout() {
    const response = await api.post("/api/auth/logout");
    localStorage.removeItem("auth_token");
    localStorage.removeItem("user");
    return response.data;
  },

  // Get current user
  async getUser() {
    const response = await api.get("/api/auth/user");
    return response.data;
  },

  // Check if user is authenticated
  isAuthenticated() {
    return !!localStorage.getItem("auth_token");
  },

  // Get user role
  getUserRole() {
    const user = JSON.parse(localStorage.getItem("user") || "{}");
    return user.role || null;
  },
};
