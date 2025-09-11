import api from "./api";

export const userService = {
  // Get all users (admin only)
  async getUsers(params = {}) {
    const response = await api.get("/api/users", { params });
    return response.data;
  },

  // Get single user
  async getUser(userId) {
    const response = await api.get(`/api/users/${userId}`);
    return response.data;
  },

  // Update user
  async updateUser(userId, userData) {
    const response = await api.put(`/api/users/${userId}`, userData);
    return response.data;
  },

  // Delete user
  async deleteUser(userId) {
    const response = await api.delete(`/api/users/${userId}`);
    return response.data;
  },

  // Update user status
  async updateUserStatus(userId, status) {
    const response = await api.patch(`/api/users/${userId}/status`, { status });
    return response.data;
  },
};
