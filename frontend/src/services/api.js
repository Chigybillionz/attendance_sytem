import axios from "axios";

const resolveApiUrl = () => {
  const configuredApiUrl = import.meta.env.VITE_API_URL;
  if (configuredApiUrl) {
    return configuredApiUrl.replace(/\/$/, "").replace(/\/api$/, "");
  }

  if (typeof window !== "undefined") {
    const { origin, hostname } = window.location;

    // Keep local development pointed at the Laravel dev server.
    if (hostname === "localhost" || hostname === "127.0.0.1") {
      return "http://localhost:8000";
    }

    // In deployed environments, use the current site origin so API calls
    // follow the same Vercel domain instead of falling back to localhost.
    return origin.replace(/\/$/, "");
  }

  return "http://localhost:8000";
};

const API_URL = resolveApiUrl();

// Fetch CSRF token from meta tag
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute("content");
if (!csrfToken) {
  console.error("CSRF token not found in meta tag.");
}

// const api = axios.create({
//   baseURL: "http://localhost:8000",
//   withCredentials: true,
//   headers: {
//     "Content-Type": "application/json",
//     Accept: "application/json",
//     "X-Requested-With": "XMLHttpRequest",
//     "X-CSRF-TOKEN": csrfToken, // Ensure this is added
//   },
// });

const api = axios.create({
  baseURL: API_URL,
  withCredentials: true,
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
