export const API_ENDPOINTS = {
  AUTH: {
    LOGIN: "/api/auth/login",
    REGISTER: "/api/auth/register",
    LOGOUT: "/api/auth/logout",
    USER: "/api/auth/user",
  },
  ATTENDANCE: {
    CLOCK_IN: "/api/attendance/clock-in",
    CLOCK_OUT: "/api/attendance/clock-out",
    TODAY: "/api/attendance/today",
    HISTORY: "/api/attendance/history",
    ALL: "/api/attendance/all",
    USER: (userId) => `/api/attendance/user/${userId}`,
  },
  DASHBOARD: {
    ADMIN: "/api/dashboard/admin",
    WORKER: "/api/dashboard/worker",
  },
  USERS: {
    LIST: "/api/users",
    SHOW: (id) => `/api/users/${id}`,
    UPDATE: (id) => `/api/users/${id}`,
    DELETE: (id) => `/api/users/${id}`,
    STATUS: (id) => `/api/users/${id}/status`,
    DEPARTMENTS: "/api/users/departments",
  },
};

export const USER_ROLES = {
  ADMIN: "admin",
  WORKER: "worker",
};

export const USER_STATUS = {
  ACTIVE: "active",
  INACTIVE: "inactive",
};

export const ATTENDANCE_STATUS = {
  PRESENT: "present",
  LATE: "late",
  ABSENT: "absent",
  EARLY_DEPARTURE: "early_departure",
};

export const ATTENDANCE_STATUS_LABELS = {
  [ATTENDANCE_STATUS.PRESENT]: "Present",
  [ATTENDANCE_STATUS.LATE]: "Late",
  [ATTENDANCE_STATUS.ABSENT]: "Absent",
  [ATTENDANCE_STATUS.EARLY_DEPARTURE]: "Early Departure",
};

export const DAYS_OF_WEEK = [
  "Sunday",
  "Monday",
  "Tuesday",
  "Wednesday",
  "Thursday",
  "Friday",
  "Saturday",
];

export const MONTHS = [
  "January",
  "February",
  "March",
  "April",
  "May",
  "June",
  "July",
  "August",
  "September",
  "October",
  "November",
  "December",
];

export const PAGINATION = {
  DEFAULT_PER_PAGE: 15,
  ITEMS_PER_PAGE_OPTIONS: [10, 15, 25, 50],
};
