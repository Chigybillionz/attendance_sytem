// frontend/src/services/authService.js
import api from "./api";

const ensureCsrfCookie = async () => {
  await api.get("/sanctum/csrf-cookie");
};

export const authService = {
  async login(credentials) {
    await ensureCsrfCookie();
    const response = await api.post("/api/auth/login", credentials);
    return response.data;
  },

  async register(userData) {
    await ensureCsrfCookie();
    const response = await api.post("/api/auth/register", userData);
    return response.data;
  },

  async logout() {
    const response = await api.post("/api/auth/logout");
    return response.data;
  },

  async getUser() {
    const response = await api.get("/api/auth/user");
    return response.data;
  },

  // NEW: Refresh token
  async refreshToken() {
    const response = await api.post("/api/auth/refresh-token");
    return response.data;
  },
};
