import { defineStore } from "pinia";
import { attendanceService } from "@/services/attendanceService";

export const useAttendanceStore = defineStore("attendance", {
  state: () => ({
    todayAttendance: null,
    attendanceHistory: [],
    allAttendance: [],
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
    hasClockIn: (state) => state.todayAttendance?.clock_in_time != null,
    hasClockOut: (state) => state.todayAttendance?.clock_out_time != null,
    canClockIn: (state) => !state.todayAttendance?.clock_in_time,
    canClockOut: (state) =>
      state.todayAttendance?.clock_in_time && !state.todayAttendance?.clock_out_time,
    workingHours: (state) => state.todayAttendance?.total_hours || 0,
    attendanceStatus: (state) => state.todayAttendance?.status || "absent",

    // Additional useful getters
    isOnBreak: (state) =>
      state.todayAttendance?.break_start_time && !state.todayAttendance?.break_end_time,
    totalPages: (state) => state.pagination.last_page,
    hasNextPage: (state) => state.pagination.current_page < state.pagination.last_page,
    hasPreviousPage: (state) => state.pagination.current_page > 1,
    attendanceByDate: (state) => {
      return state.attendanceHistory.reduce((acc, record) => {
        const date = new Date(record.date).toDateString();
        if (!acc[date]) acc[date] = [];
        acc[date].push(record);
        return acc;
      }, {});
    },
  },

  actions: {
    async clockIn() {
      this.loading = true;
      this.error = null;
      try {
        const response = await attendanceService.clockIn();
        this.todayAttendance = response.attendance;
        return response;
      } catch (error) {
        this.error = error.response?.data?.message || "Clock in failed";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async clockOut() {
      this.loading = true;
      this.error = null;
      try {
        const response = await attendanceService.clockOut();
        this.todayAttendance = response.attendance;
        return response;
      } catch (error) {
        this.error = error.response?.data?.message || "Clock out failed";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async fetchTodayAttendance() {
      this.loading = true;
      try {
        const response = await attendanceService.getTodayAttendance();
        this.todayAttendance = response.attendance;
      } catch (error) {
        console.error("Failed to fetch today attendance:", error);
        this.error = error.response?.data?.message || "Failed to fetch attendance";
      } finally {
        this.loading = false;
      }
    },

    async fetchAttendanceHistory(params = {}) {
      this.loading = true;
      try {
        const response = await attendanceService.getAttendanceHistory(params);
        this.attendanceHistory = response.data;
        this.pagination = {
          current_page: response.current_page,
          last_page: response.last_page,
          per_page: response.per_page,
          total: response.total,
        };
      } catch (error) {
        console.error("Failed to fetch attendance history:", error);
        this.error = error.response?.data?.message || "Failed to fetch history";
      } finally {
        this.loading = false;
      }
    },

    // Additional useful actions
    async fetchAllAttendance(params = {}) {
      this.loading = true;
      try {
        const response = await attendanceService.getAllAttendance(params);
        this.allAttendance = response.data || response;
        return response;
      } catch (error) {
        console.error("Failed to fetch all attendance:", error);
        this.error = error.response?.data?.message || "Failed to fetch all attendance";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async startBreak() {
      this.loading = true;
      this.error = null;
      try {
        const response = await attendanceService.startBreak();
        this.todayAttendance = response.attendance;
        return response;
      } catch (error) {
        this.error = error.response?.data?.message || "Failed to start break";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async endBreak() {
      this.loading = true;
      this.error = null;
      try {
        const response = await attendanceService.endBreak();
        this.todayAttendance = response.attendance;
        return response;
      } catch (error) {
        this.error = error.response?.data?.message || "Failed to end break";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    async updateAttendance(id, data) {
      this.loading = true;
      this.error = null;
      try {
        const response = await attendanceService.updateAttendance(id, data);

        // Update the record in history if it exists
        const index = this.attendanceHistory.findIndex((record) => record.id === id);
        if (index !== -1) {
          this.attendanceHistory[index] = response.attendance || response.data;
        }

        // Update today's attendance if it's the same record
        if (this.todayAttendance?.id === id) {
          this.todayAttendance = response.attendance || response.data;
        }

        return response;
      } catch (error) {
        this.error = error.response?.data?.message || "Failed to update attendance";
        throw error;
      } finally {
        this.loading = false;
      }
    },

    // Pagination helpers
    async goToPage(page) {
      if (page >= 1 && page <= this.pagination.last_page) {
        await this.fetchAttendanceHistory({ page });
      }
    },

    async nextPage() {
      if (this.hasNextPage) {
        await this.goToPage(this.pagination.current_page + 1);
      }
    },

    async previousPage() {
      if (this.hasPreviousPage) {
        await this.goToPage(this.pagination.current_page - 1);
      }
    },

    // Utility actions
    clearError() {
      this.error = null;
    },

    resetAttendanceHistory() {
      this.attendanceHistory = [];
      this.pagination = {
        current_page: 1,
        last_page: 1,
        per_page: 15,
        total: 0,
      };
    },

    resetAllAttendance() {
      this.allAttendance = [];
    },

    // Filter and search helpers
    async searchAttendance(searchTerm, filters = {}) {
      const params = {
        search: searchTerm,
        ...filters,
      };
      await this.fetchAttendanceHistory(params);
    },

    async filterByDateRange(startDate, endDate, additionalParams = {}) {
      const params = {
        start_date: startDate,
        end_date: endDate,
        ...additionalParams,
      };
      await this.fetchAttendanceHistory(params);
    },

    async filterByStatus(status, additionalParams = {}) {
      const params = {
        status,
        ...additionalParams,
      };
      await this.fetchAttendanceHistory(params);
    },
  },
});
