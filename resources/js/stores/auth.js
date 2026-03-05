import { defineStore } from "pinia";
import axios from "axios";
import api from "../services/api";

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: JSON.parse(localStorage.getItem("user")) || null,
        token: localStorage.getItem("auth_token") || null,
        isAuthenticated: !!localStorage.getItem("auth_token"),
        loading: false,
        error: null,
    }),

    getters: {
        currentUser: (state) => state.user,
        isLoggedIn: (state) => state.isAuthenticated,
        userRoles: (state) => state.user?.roles || [],
        hasRole: (state) => (role) =>
            state.user?.roles?.includes(role) || false,
        isAdmin: (state) => state.user?.roles?.includes("super-admin") || false,
        isTrainer: (state) => state.user?.roles?.includes("profesor") || false,
        isStudent: (state) =>
            state.user?.roles?.includes("alumno") ||
            state.user?.roles?.includes("student") ||
            false,
        isModerator: (state) =>
            state.user?.roles?.includes("moderador") || false,
    },

    actions: {
        async register(credentials) {
            this.loading = true;
            this.error = null;

            try {
                const { data } = await api.post("/register", credentials);
                this.setAuth(data.user, data.token);
                return data;
            } catch (error) {
                this.error =
                    error.response?.data?.message ||
                    "Error al registrar usuario";
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async login(credentials) {
            this.loading = true;
            this.error = null;

            try {
                // Get CSRF cookie first (web session auth)
                await axios.get('/sanctum/csrf-cookie', { withCredentials: true });

                // POST to web login route to create a real session
                await axios.post('/login', credentials, {
                    withCredentials: true,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });

                // Session is created — page will fully reload to /dashboard
                this.isAuthenticated = true;
                return true;
            } catch (error) {
                this.error = error.response?.data?.message || 'Error al iniciar sesión';
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            this.loading = true;

            try {
                await axios.post('/logout', {}, {
                    withCredentials: true,
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                });
            } catch (error) {
                console.error('Error during logout:', error);
            } finally {
                this.clearAuth();
                this.loading = false;
                window.location.href = '/';
            }
        },

        async fetchUser() {
            if (!this.token) return;

            this.loading = true;

            try {
                const { data } = await api.get("/user");
                this.user = data.user;
                localStorage.setItem("user", JSON.stringify(data.user));
            } catch (error) {
                console.error("Error fetching user:", error);
                this.clearAuth();
            } finally {
                this.loading = false;
            }
        },

        setAuth(user, token) {
            this.user = user;
            this.token = token;
            this.isAuthenticated = true;
            localStorage.setItem("user", JSON.stringify(user));
            localStorage.setItem("auth_token", token);
        },

        clearAuth() {
            this.user = null;
            this.token = null;
            this.isAuthenticated = false;
            localStorage.removeItem("user");
            localStorage.removeItem("auth_token");
        },
    },
});
