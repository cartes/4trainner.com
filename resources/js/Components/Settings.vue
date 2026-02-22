<script setup>
import { ref, onMounted } from 'vue';
import Sidebar from './Sidebar.vue';
import Topbar from './Topbar.vue';

const props = defineProps({
    user: {
        type: [Object, String],
        required: true
    },
    status: String
});

const userObj = typeof props.user === 'string' ? JSON.parse(props.user) : props.user;
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// Theme state
const isDark = ref(false);

const toggleDarkMode = () => {
    isDark.value = !isDark.value;
    if (isDark.value) {
        document.documentElement.classList.add('dark');
        localStorage.setItem('theme', 'dark');
    } else {
        document.documentElement.classList.remove('dark');
        localStorage.setItem('theme', 'light');
    }
};

onMounted(() => {
    // Initialize theme
    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
    }
});
</script>

<template>
    <div class="min-h-screen bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-200 font-sans transition-colors duration-500 relative flex overflow-x-hidden">
        <!-- Sidebar Integration -->
        <Sidebar :is-dark="isDark" @toggle-dark="toggleDarkMode" />

        <!-- Main Content Area -->
        <div class="ml-72 flex-1 flex flex-col min-h-screen relative">
            <!-- Topbar Integration -->
            <Topbar :is-dark="isDark" @toggle-dark="toggleDarkMode" />

            <!-- Main Content -->
            <main class="mt-24 p-8 lg:p-12 flex-1 relative z-10">
                <h1 class="text-3xl font-display font-black text-primary-dark dark:text-white mb-8 uppercase italic tracking-tight">
                    Configuración <span class="text-primary not-italic">de Cuenta</span>
                </h1>

                <div v-if="status === 'profile-updated'" class="mb-6 p-4 bg-green-100 dark:bg-green-900/30 text-green-700 dark:text-green-300 rounded-2xl border border-green-200 dark:border-green-800 flex items-center gap-3">
                    <span class="material-icons">check_circle</span>
                    <span class="font-bold">Perfil actualizado correctamente.</span>
                </div>

                <div class="bg-white dark:bg-slate-800/50 backdrop-blur-xl rounded-[2rem] p-8 lg:p-12 shadow-xl shadow-slate-200/50 dark:shadow-none border border-slate-200 dark:border-white/5 max-w-3xl">
                    <form action="/settings" method="POST" class="space-y-8">
                        <input type="hidden" name="_token" :value="csrfToken">
                        <input type="hidden" name="_method" value="PATCH">

                        <div class="grid md:grid-cols-2 gap-8">
                            <div class="space-y-2">
                                <label class="text-xs font-black text-slate-400 uppercase tracking-widest pl-2">Nombre</label>
                                <input type="text" name="name" :value="userObj.name" required
                                    class="w-full bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 rounded-2xl px-6 py-4 text-slate-800 dark:text-white font-bold focus:outline-none focus:border-primary transition-colors placeholder-slate-400 dark:placeholder-slate-600">
                            </div>

                            <div class="space-y-2">
                                <label class="text-xs font-black text-slate-400 uppercase tracking-widest pl-2">Email</label>
                                <input type="email" name="email" :value="userObj.email" required
                                    class="w-full bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 rounded-2xl px-6 py-4 text-slate-800 dark:text-white font-bold focus:outline-none focus:border-primary transition-colors placeholder-slate-400 dark:placeholder-slate-600">
                            </div>
                        </div>

                        <div class="pt-8 border-t border-slate-200 dark:border-white/10 space-y-8">
                            <h3 class="text-xl font-display font-black text-primary-dark dark:text-white uppercase italic tracking-tight">
                                Seguridad
                            </h3>

                            <div class="space-y-6">
                                <div class="space-y-2">
                                    <label class="text-xs font-black text-slate-400 uppercase tracking-widest pl-2">Contraseña Actual</label>
                                    <input type="password" name="current_password" placeholder="Solo si deseas cambiarla"
                                        class="w-full bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 rounded-2xl px-6 py-4 text-slate-800 dark:text-white font-bold focus:outline-none focus:border-primary transition-colors placeholder-slate-400 dark:placeholder-slate-600">
                                </div>

                                <div class="grid md:grid-cols-2 gap-8">
                                    <div class="space-y-2">
                                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest pl-2">Nueva Contraseña</label>
                                        <input type="password" name="password"
                                            class="w-full bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 rounded-2xl px-6 py-4 text-slate-800 dark:text-white font-bold focus:outline-none focus:border-primary transition-colors placeholder-slate-400 dark:placeholder-slate-600">
                                    </div>

                                    <div class="space-y-2">
                                        <label class="text-xs font-black text-slate-400 uppercase tracking-widest pl-2">Confirmar Contraseña</label>
                                        <input type="password" name="password_confirmation"
                                            class="w-full bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 rounded-2xl px-6 py-4 text-slate-800 dark:text-white font-bold focus:outline-none focus:border-primary transition-colors placeholder-slate-400 dark:placeholder-slate-600">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4">
                            <button type="submit"
                                class="bg-primary hover:bg-primary-dark text-primary-dark font-black uppercase tracking-widest text-sm py-4 px-12 rounded-2xl shadow-lg shadow-primary/20 hover:shadow-primary/40 hover:-translate-y-1 transition-all duration-300">
                                Guardar Cambios
                            </button>
                        </div>
                    </form>
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
.font-display {
    font-family: 'Outfit', sans-serif;
}
</style>
