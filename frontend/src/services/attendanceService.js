import api from "./api";

export const attendanceService = {
  // Clock in
  async clockIn() {
    const response = await api.post("/api/attendance/clock-in");
    return response.data;
  },

  // Clock out
  async clockOut() {
    const response = await api.post("/api/attendance/clock-out");
    return response.data;
  },

  // Get today's attendance
  async getTodayAttendance() {
    const response = await api.get("/api/attendance/today");
    return response.data;
  },

  // Get attendance history
  async getAttendanceHistory(params = {}) {
    const response = await api.get("/api/attendance/history", { params });
    return response.data;
  },

  // Get user attendance (for admin)
  async getUserAttendance(userId, params = {}) {
    const response = await api.get(`/api/attendance/user/${userId}`, { params });
    return response.data;
  },

  // Get all attendance records (admin only)
  async getAllAttendance(params = {}) {
    const response = await api.get("/api/attendance/all", { params });
    return response.data;
  },
};
