import { defineStore } from "pinia";
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
                // Initialize CSRF for Sanctum stateful auth
                await api.get("/../../sanctum/csrf-cookie");

                const { data } = await api.post("/login", credentials);
                this.setAuth(data.user, data.token);
                return data;
            } catch (error) {
                this.error =
                    error.response?.data?.message || "Error al iniciar sesión";
                throw error;
            } finally {
                this.loading = false;
            }
        },

        async logout() {
            this.loading = true;

            try {
                // Use the WEB logout route to ensure the session is destroyed
                await api.post("/../../logout");
            } catch (error) {
                console.error("Error during logout:", error);
            } finally {
                this.clearAuth();
                this.loading = false;
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
