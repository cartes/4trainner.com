<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../stores/auth';

const authStore = useAuthStore();

const form = ref({
    email: '',
    password: '',
});

const errors = ref({});
const loading = ref(false);

const handleLogin = async () => {
    errors.value = {};
    loading.value = true;

    try {
        await authStore.login(form.value);
        // Redirect to dashboard
        window.location.href = '/dashboard';
    } catch (error) {
        if (error.response?.data?.errors) {
            errors.value = error.response.data.errors;
        } else {
            errors.value.general = error.response?.data?.message || 'Error al iniciar sesión';
        }
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <div class="min-h-screen flex items-center justify-center bg-slate-50 dark:bg-slate-900 py-12 px-4 sm:px-6 lg:px-8">
        <div class="max-w-md w-full space-y-8">
            <div>
                <h2 class="mt-6 text-center text-3xl font-display font-bold text-slate-900 dark:text-white">
                    Inicia sesión en tu cuenta
                </h2>
                <p class="mt-2 text-center text-sm text-slate-600 dark:text-slate-400">
                    O
                    <a href="/register" class="font-medium text-primary hover:text-primary-dark">
                        crea una cuenta nueva
                    </a>
                </p>
            </div>

            <form class="mt-8 space-y-6" @submit.prevent="handleLogin">
                <!-- Error general -->
                <div v-if="errors.general" class="rounded-md bg-red-50 dark:bg-red-900/20 p-4">
                    <div class="flex">
                        <div class="ml-3">
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                {{ errors.general }}
                            </h3>
                        </div>
                    </div>
                </div>

                <div class="rounded-md shadow-sm space-y-4">
                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Email
                        </label>
                        <input
                            id="email"
                            v-model="form.email"
                            name="email"
                            type="email"
                            autocomplete="email"
                            required
                            class="appearance-none relative block w-full px-3 py-2 border border-slate-300 dark:border-slate-700 placeholder-slate-500 dark:placeholder-slate-400 text-slate-900 dark:text-white rounded-lg focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm bg-white dark:bg-slate-800"
                            placeholder="tu@email.com"
                        />
                        <p v-if="errors.email" class="mt-2 text-sm text-red-600 dark:text-red-400">
                            {{ errors.email[0] }}
                        </p>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                            Contraseña
                        </label>
                        <input
                            id="password"
                            v-model="form.password"
                            name="password"
                            type="password"
                            autocomplete="current-password"
                            required
                            class="appearance-none relative block w-full px-3 py-2 border border-slate-300 dark:border-slate-700 placeholder-slate-500 dark:placeholder-slate-400 text-slate-900 dark:text-white rounded-lg focus:outline-none focus:ring-primary focus:border-primary focus:z-10 sm:text-sm bg-white dark:bg-slate-800"
                            placeholder="••••••••"
                        />
                        <p v-if="errors.password" class="mt-2 text-sm text-red-600 dark:text-red-400">
                            {{ errors.password[0] }}
                        </p>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center">
                        <input
                            id="remember-me"
                            name="remember-me"
                            type="checkbox"
                            class="h-4 w-4 text-primary focus:ring-primary border-slate-300 rounded"
                        />
                        <label for="remember-me" class="ml-2 block text-sm text-slate-900 dark:text-slate-300">
                            Recuérdame
                        </label>
                    </div>

                    <div class="text-sm">
                        <a href="/forgot-password" class="font-medium text-primary hover:text-primary-dark">
                            ¿Olvidaste tu contraseña?
                        </a>
                    </div>
                </div>

                <div>
                    <button
                        type="submit"
                        :disabled="loading"
                        class="group relative w-full flex justify-center py-3 px-4 border border-transparent text-sm font-medium rounded-lg text-primary-dark bg-primary hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary disabled:opacity-50 disabled:cursor-not-allowed transition-all"
                    >
                        <span v-if="loading">Iniciando sesión...</span>
                        <span v-else>Iniciar sesión</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
