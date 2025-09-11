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

    // Worker dashboard getters
    workerTodayAttendance: (state) => state.workerDashboard?.today_attendance,
    workerMonthlyStats: (state) => state.workerDashboard?.monthly_stats,
    workerWeeklyHours: (state) => state.workerDashboard?.weekly_hours || [],
    workerRecentAttendance: (state) => state.workerDashboard?.recent_attendance || [],
    canClockIn: (state) => state.workerDashboard?.can_clock_in || false,
    canClockOut: (state) => state.workerDashboard?.can_clock_out || false,

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
