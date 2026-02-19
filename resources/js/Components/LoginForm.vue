<script setup>
import { ref } from 'vue';
import { useAuthStore } from '../stores/auth';

const emit = defineEmits(['close']);
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

const close = () => {
    emit('close');
};
</script>

<template>
    <div class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-slate-900/40 backdrop-blur-sm transition-all duration-300"
        @click.self="close">
        <div
            class="max-w-md w-full glass-modal rounded-3xl shadow-2xl overflow-hidden transform transition-all scale-100 opacity-100 animate-in fade-in zoom-in duration-300 relative">
            <!-- Aqua Top Bar -->
            <div class="h-1.5 w-full bg-gradient-to-r from-aqua-300 via-aqua-500 to-primary"></div>

            <!-- Modal Header -->
            <div class="px-8 pt-8 flex justify-between items-start">
                <div>
                    <h2 class="text-3xl font-display font-bold text-slate-900 dark:text-white">
                        Bienvenido
                    </h2>
                    <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">
                        Inicia sesión para continuar
                    </p>
                </div>
                <button @click="close"
                    class="p-2 rounded-full hover:bg-aqua-100/50 dark:hover:bg-aqua-900/30 text-slate-400 hover:text-aqua-600 transition-colors">
                    <span class="material-icons">close</span>
                </button>
            </div>

            <div class="px-8 pb-8">
                <form class="mt-6 space-y-6" @submit.prevent="handleLogin">
                    <!-- Error general -->
                    <div v-if="errors.general"
                        class="rounded-xl bg-red-50/80 dark:bg-red-900/20 p-4 border border-red-100 dark:border-red-900/30">
                        <div class="flex">
                            <span class="material-icons text-red-500 text-sm mr-2">error</span>
                            <h3 class="text-sm font-medium text-red-800 dark:text-red-200">
                                {{ errors.general }}
                            </h3>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <!-- Email -->
                        <div>
                            <label for="email"
                                class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Email
                            </label>
                            <input id="email" v-model="form.email" name="email" type="email" autocomplete="email"
                                required
                                class="appearance-none block w-full px-4 py-3 border border-slate-200 dark:border-slate-700 placeholder-slate-400 text-slate-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-aqua-500/20 focus:border-aqua-500 transition-all bg-white/50 dark:bg-slate-800/40"
                                placeholder="tu@email.com" />
                            <p v-if="errors.email" class="mt-1 text-xs text-red-500">
                                {{ errors.email[0] }}
                            </p>
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="password"
                                class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-2">
                                Contraseña
                            </label>
                            <input id="password" v-model="form.password" name="password" type="password"
                                autocomplete="current-password" required
                                class="appearance-none block w-full px-4 py-3 border border-slate-200 dark:border-slate-700 placeholder-slate-400 text-slate-900 dark:text-white rounded-xl focus:outline-none focus:ring-2 focus:ring-aqua-500/20 focus:border-aqua-500 transition-all bg-white/50 dark:bg-slate-800/40"
                                placeholder="••••••••" />
                            <p v-if="errors.password" class="mt-1 text-xs text-red-500">
                                {{ errors.password[0] }}
                            </p>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <input id="remember-me" name="remember-me" type="checkbox"
                                class="h-4 w-4 text-aqua-500 focus:ring-aqua-500 border-slate-300 rounded-md" />
                            <label for="remember-me" class="ml-2 block text-sm text-slate-600 dark:text-slate-400">
                                Recuérdame
                            </label>
                        </div>

                        <div class="text-sm">
                            <a href="/forgot-password" class="nav-link-aqua text-sm py-1">
                                ¿Olvidaste la clave?
                            </a>
                        </div>
                    </div>

                    <div class="pt-2">
                        <button type="submit" :disabled="loading"
                            class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-sm font-bold rounded-xl text-white bg-gradient-to-r from-aqua-400 to-aqua-600 hover:from-aqua-500 hover:to-aqua-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-aqua-500 shadow-lg shadow-aqua-500/20 transition-all active:scale-[0.98] disabled:opacity-50">
                            <span v-if="loading" class="flex items-center gap-2">
                                <span
                                    class="w-4 h-4 border-2 border-white/30 border-t-white rounded-full animate-spin"></span>
                                Conectando...
                            </span>
                            <span v-else>Entrar a FoxFit</span>
                        </button>
                    </div>

                    <p class="text-center text-sm text-slate-500 dark:text-slate-400 mt-6">
                        ¿No tienes cuenta?
                        <a href="/register" class="nav-link-aqua font-bold py-1 ml-1">
                            Únete ahora
                        </a>
                    </p>
                </form>
            </div>
        </div>
    </div>
</template>

<style scoped>
/* Aqua Glass Effect for Modal */
.glass-modal {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
    border: 1px solid rgba(146, 203, 212, 0.3);
}

.dark .glass-modal {
    background: rgba(15, 23, 42, 0.8);
    border: 1px solid rgba(95, 169, 184, 0.2);
}

/* Aqua Hover Effect inherited from Welcome or defined here for local specificity */
.nav-link-aqua {
    position: relative;
    display: inline-block;
    color: #438d9c;
    text-decoration: none;
    transition: color 0.3s ease;
}

.nav-link-aqua::after {
    content: '';
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, #5fa9b8, #92cbd4, #D4FF37);
    transition: width 0.3s ease;
}

.nav-link-aqua:hover::after {
    width: 100%;
}

.font-display {
    font-family: 'Outfit', sans-serif;
}
</style>
