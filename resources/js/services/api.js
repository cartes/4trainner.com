import axios from "axios";

const api = axios.create({
    baseURL: import.meta.env.VITE_API_URL || "http://localhost:8000/api/v1",
    withCredentials: false,
    headers: {
        Accept: "application/json",
        "Content-Type": "application/json",
    },
});

// Request interceptor - Add token to requests
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
    },
);

// Response interceptor - Handle errors globally
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response) {
            // Handle 401 Unauthorized
            if (error.response.status === 401) {
                localStorage.removeItem("auth_token");
                localStorage.removeItem("user");
                window.location.href = "/login";
            }

            // Handle 403 Forbidden
            if (error.response.status === 403) {
                console.error(
                    "Forbidden: You don't have permission to access this resource",
                );
            }

            // Handle 422 Validation errors
            if (error.response.status === 422) {
                console.error("Validation errors:", error.response.data.errors);
            }
        }
        return Promise.reject(error);
    },
);

export default api;
