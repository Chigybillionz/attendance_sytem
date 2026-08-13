import axios from "axios";

const resolveApiUrl = () => {
  let url = import.meta.env.VITE_API_URL || "";

  if (!url && typeof window !== "undefined") {
    url = window.location.origin;
  }

  // Remove trailing slashes and trailing /api (handles /api, /api/, /api/api, etc.)
  return url.replace(/\/+$/, "").replace(/\/api\/*$/i, "").replace(/\/+$/, "");
};

const API_URL = resolveApiUrl();

const api = axios.create({
  baseURL: API_URL,
  withCredentials: true,
  xsrfCookieName: "XSRF-TOKEN",
  xsrfHeaderName: "X-XSRF-TOKEN",
  headers: {
    Accept: "application/json",
    "Content-Type": "application/json",
    "X-Requested-With": "XMLHttpRequest",
  },
});

// Request interceptor to add auth token
api.interceptors.request.use(
  (config) => {
    const token = localStorage.getItem("auth_token");
    if (token) {
      config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
  },
  (error) => {
    return Promise.reject(error);
  }
);

// Response interceptor for error handling
api.interceptors.response.use(
  (response) => response,
  (error) => {
    if (error.response?.status === 401) {
      localStorage.removeItem("auth_token");
      localStorage.removeItem("user");
      window.location.href = "/login";
    }
    return Promise.reject(error);
  }
);

export default api;
export { API_URL };
