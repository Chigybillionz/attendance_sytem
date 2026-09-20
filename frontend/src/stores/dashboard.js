import { defineStore } from "pinia";
import api from "@/services/api";

export const useDashboardStore = defineStore("dashboard", {
  state: () => ({
    adminDashboard: null,
    workerDashboard: null,
    loading: false,
    error: null,
  }),

  getters: {
    // Admin dashboard getters
    totalEmployees: (state) => state.adminDashboard?.overview?.total_employees || 0,
    todayAttendance: (state) => state.adminDashboard?.overview?.today_attendance || 0,
    todayPresent: (state) => state.adminDashboard?.overview?.today_present || 0,
    todayLate: (state) => state.adminDashboard?.overview?.today_late || 0,
    todayAbsent: (state) => state.adminDashboard?.overview?.today_absent || 0,
    monthlyHours: (state) => state.adminDashboard?.overview?.monthly_hours || 0,
    weeklyChart: (state) => state.adminDashboard?.weekly_chart || [],
    departmentStats: (state) => state.adminDashboard?.department_stats || [],
    recentAttendance: (state) => state.adminDashboard?.recent_attendance || [],

    // Admin extra getters
    totalRegisteredUsers: (state) => state.adminDashboard?.overview?.total_registered_users || 0,
    totalAdmins: (state) => state.adminDashboard?.overview?.total_admins || 0,

    // Worker dashboard getters
    workerTodayAttendance: (state) => state.workerDashboard?.today_attendance,
    workerMonthlyStats: (state) => state.workerDashboard?.monthly_stats,
    workerWeeklyHours: (state) => state.workerDashboard?.weekly_hours || [],
    workerRecentAttendance: (state) => state.workerDashboard?.recent_attendance || [],
    // canClockIn: true when dashboard loaded and either backend says true OR no attendance record exists yet (new user)
    canClockIn: (state) => {
      if (!state.workerDashboard) return false;
      // If the backend explicitly set can_clock_in, use it; otherwise default true for new users
      if (state.workerDashboard.can_clock_in === true) return true;
      if (state.workerDashboard.can_clock_in === false) return false;
      // Fallback: if no today_attendance record, user can clock in
      return !state.workerDashboard.today_attendance;
    },
    canClockOut: (state) => {
      if (!state.workerDashboard) return false;
      return state.workerDashboard.can_clock_out || false;
    },
    isNewUser: (state) => state.workerDashboard?.is_new_user || false,

    // Attendance summary
    attendanceSummary: (state) => state.adminDashboard?.attendance_summary || {},
  },

  actions: {
    async fetchAdminDashboard() {
      this.loading = true;
      this.error = null;

      try {
        const response = await api.get("/api/dashboard/admin");
        this.adminDashboard = response.data;

        return response.data;
      } catch (error) {
        console.error("Failed to fetch admin dashboard:", error);
        this.error = error.response?.data?.message || "Failed to load admin dashboard";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async fetchWorkerDashboard() {
      this.loading = true;
      this.error = null;

      try {
        const response = await api.get("/api/dashboard/worker");
        this.workerDashboard = response.data;

        return response.data;
      } catch (error) {
        console.error("Failed to fetch worker dashboard:", error);
        this.error = error.response?.data?.message || "Failed to load worker dashboard";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // Refresh dashboard data
    async refreshDashboard() {
      const authStore = useAuthStore();

      if (authStore.isAdmin) {
        await this.fetchAdminDashboard();
      } else {
        await this.fetchWorkerDashboard();
      }
    },

    // Clear dashboard data
    clearDashboard() {
      this.adminDashboard = null;
      this.workerDashboard = null;
      this.error = null;
    },

    clearError() {
      this.error = null;
    },
  },
});
