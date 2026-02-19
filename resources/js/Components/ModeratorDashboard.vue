<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';
import Sidebar from './Sidebar.vue';
import Topbar from './Topbar.vue';

const dashboardData = ref(null);
const loading = ref(true);
const error = ref(null);

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

const fetchDashboard = async () => {
    try {
        const response = await api.get('/moderator/dashboard');
        dashboardData.value = response.data;
    } catch (err) {
        error.value = 'No se pudo cargar el panel de moderación.';
        console.error(err);
    } finally {
        loading.value = false;
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
    fetchDashboard();
});
</script>

<template>
    <div
        class="min-h-screen bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-200 font-sans transition-colors duration-500 relative flex overflow-x-hidden">
        <!-- Sidebar Integration -->
        <Sidebar :is-dark="isDark" @toggle-dark="toggleDarkMode" />

        <!-- Main Content Area -->
        <div class="ml-72 flex-1 flex flex-col min-h-screen relative">
            <!-- Topbar Integration -->
            <Topbar :is-dark="isDark" @toggle-dark="toggleDarkMode" />

            <!-- Main Content -->
            <main class="mt-24 p-4 md:p-8 lg:p-12 flex-1 relative z-10">
                <!-- Background Glow -->
                <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
                    <div
                        class="absolute -top-[10%] -left-[10%] opacity-20 dark:opacity-30 blur-[120px] w-[50%] h-[50%] bg-primary rounded-full">
                    </div>
                    <div
                        class="absolute top-[60%] -right-[5%] opacity-10 dark:opacity-20 blur-[100px] w-[40%] h-[40%] bg-aqua-400 rounded-full">
                    </div>
                </div>



                <!-- Header -->
                <header class="max-w-7xl mx-auto mb-12 flex flex-col md:flex-row md:items-center justify-between gap-8">
                    <div>
                        <h1
                            class="text-5xl font-display font-black text-primary-dark dark:text-white tracking-tight uppercase italic">
                            <span class="text-primary not-italic">Fox</span>Control <span
                                class="font-light not-italic text-slate-400 dark:text-slate-500 text-3xl ml-2">Moderator</span>
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 mt-2 flex items-center gap-2 font-medium">
                            <span
                                class="w-3 h-3 rounded-full bg-primary animate-pulse shadow-[0_0_10px_rgba(212,255,55,0.8)]"></span>
                            Supervisión y control de la infraestructura humana
                        </p>
                    </div>

                    <div v-if="dashboardData"
                        class="flex items-center gap-6 bg-white dark:bg-slate-800/50 backdrop-blur-xl px-8 py-5 rounded-[2rem] border border-slate-200 dark:border-white/10 shadow-xl shadow-slate-200/50 dark:shadow-none">
                        <div class="text-right">
                            <p
                                class="text-[10px] text-slate-400 dark:text-slate-500 font-black uppercase tracking-[0.2em]">
                                Total Usuarios</p>
                            <p class="text-3xl font-display font-bold text-primary-dark dark:text-white">{{
                                dashboardData.stats.total_users }}</p>
                        </div>
                        <div class="w-px h-12 bg-slate-200 dark:bg-white/10"></div>
                        <div
                            class="w-14 h-14 rounded-2xl bg-primary flex items-center justify-center shadow-lg shadow-primary/20">
                            <span class="material-icons text-primary-dark text-3xl">manage_accounts</span>
                        </div>
                    </div>
                </header>

                <div class="max-w-7xl mx-auto">
                    <!-- Loading -->
                    <div v-if="loading" class="flex flex-col items-center justify-center py-32 gap-6">
                        <div class="w-16 h-16 border-4 border-primary/20 border-t-primary rounded-full animate-spin">
                        </div>
                        <p
                            class="text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest text-sm italic">
                            Cargando protocolos...</p>
                    </div>

                    <!-- Error -->
                    <div v-else-if="error"
                        class="bg-red-500/10 border border-red-500/20 p-8 rounded-[2rem] text-center backdrop-blur-md">
                        <span class="material-icons text-red-500 text-5xl mb-3">gpp_maybe</span>
                        <p class="text-red-800 dark:text-red-200 font-bold text-lg">{{ error }}</p>
                    </div>

                    <div v-else class="space-y-12">
                        <!-- Stats Row -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                            <div
                                class="glass-panel p-8 rounded-[2rem] border border-slate-200 dark:border-white/5 shadow-sm">
                                <span class="material-icons text-primary text-3xl mb-4">group</span>
                                <p
                                    class="text-4xl font-display font-black text-primary-dark dark:text-white tracking-tighter">
                                    {{ dashboardData.stats.total_users }}</p>
                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-2">
                                    Usuarios
                                    Registrados</p>
                            </div>
                            <div
                                class="glass-panel p-8 rounded-[2rem] border border-slate-200 dark:border-white/5 shadow-sm">
                                <span class="material-icons text-aqua-400 text-3xl mb-4">sports</span>
                                <p
                                    class="text-4xl font-display font-black text-primary-dark dark:text-white tracking-tighter">
                                    {{ dashboardData.stats.total_profesores }}</p>
                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-2">Elite
                                    Trainers
                                </p>
                            </div>
                            <div
                                class="glass-panel p-8 rounded-[2rem] border border-slate-200 dark:border-white/5 shadow-sm">
                                <span class="material-icons text-primary text-3xl mb-4">school</span>
                                <p
                                    class="text-4xl font-display font-black text-primary-dark dark:text-white tracking-tighter">
                                    {{ dashboardData.stats.total_alumnos }}</p>
                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-2">Atletas
                                    Activos
                                </p>
                            </div>
                            <div
                                class="glass-panel p-8 rounded-[2rem] border border-slate-200 dark:border-white/5 shadow-sm">
                                <span class="material-icons text-aqua-400 text-3xl mb-4">shield</span>
                                <p
                                    class="text-4xl font-display font-black text-primary-dark dark:text-white tracking-tighter">
                                    {{ dashboardData.stats.total_moderators }}</p>
                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-2">Security
                                    Team
                                </p>
                            </div>
                        </div>

                        <!-- Recent Users Table -->
                        <div
                            class="glass-panel overflow-hidden rounded-[3rem] border border-slate-200 dark:border-white/5 shadow-2xl shadow-slate-200/50 dark:shadow-none">
                            <div
                                class="px-10 py-8 border-b border-slate-100 dark:border-white/5 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="material-icons text-primary">history</span>
                                    <h2
                                        class="text-xl font-display font-black text-primary-dark dark:text-white uppercase tracking-tight italic">
                                        Audit de <span class="text-primary not-italic">Nuevos Atletas</span></h2>
                                </div>
                                <button
                                    class="px-5 py-2 rounded-xl bg-slate-100 dark:bg-white/5 text-[10px] font-black uppercase tracking-widest hover:bg-primary hover:text-primary-dark transition-all">Exportar
                                    Logs</button>
                            </div>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr
                                            class="bg-slate-50 dark:bg-white/5 border-b border-slate-200 dark:border-white/5">
                                            <th
                                                class="px-10 py-8 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                                Perfil de Usuario</th>
                                            <th
                                                class="px-10 py-8 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                                Privilegios</th>
                                            <th
                                                class="px-10 py-8 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 font-right">
                                                Timestamp</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                                        <tr v-for="user in dashboardData.recent_users" :key="user.id"
                                            class="group hover:bg-primary/[0.03] dark:hover:bg-primary/[0.05] transition-all duration-300">
                                            <td class="px-10 py-8">
                                                <div class="flex items-center gap-5">
                                                    <div
                                                        class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-primary-dark to-slate-800 dark:from-primary dark:to-aqua-300 flex items-center justify-center text-white dark:text-primary-dark font-black text-xl shadow-lg group-hover:scale-110 transition-transform">
                                                        {{ user.name.charAt(0) }}
                                                    </div>
                                                    <div>
                                                        <p
                                                            class="text-primary-dark dark:text-white font-bold text-lg font-display">
                                                            {{ user.name }}</p>
                                                        <p
                                                            class="text-slate-400 dark:text-slate-500 text-xs font-medium">
                                                            {{
                                                                user.email }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-10 py-8">
                                                <span v-for="role in user.roles" :key="role"
                                                    class="inline-flex items-center px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-primary/10 text-primary border border-primary/20 mr-2">
                                                    {{ role }}
                                                </span>
                                            </td>
                                            <td class="px-10 py-8">
                                                <p
                                                    class="text-slate-500 dark:text-slate-400 text-sm font-medium flex items-center gap-2">
                                                    <span
                                                        class="material-icons text-primary text-base">event_available</span>
                                                    {{ user.created_at }}
                                                </p>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            <div class="p-8 bg-slate-50/50 dark:bg-white/5 text-center">
                                <button
                                    class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 hover:text-primary transition-all">Ver
                                    todos los registros corporativos</button>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
.glass-panel {
    background: rgba(255, 255, 255, 0.7);
    backdrop-filter: blur(24px);
    -webkit-backdrop-filter: blur(24px);
}

.dark .glass-panel {
    background: rgba(30, 41, 59, 0.4);
    border: 1px solid rgba(255, 255, 255, 0.03);
}

.font-display {
    font-family: 'Outfit', sans-serif;
}

::-webkit-scrollbar {
    width: 8px;
    height: 8px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: rgba(212, 255, 55, 0.1);
    border-radius: 20px;
}

::-webkit-scrollbar-thumb:hover {
    background: rgba(212, 255, 55, 0.2);
}

.shadow-xl {
    box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.1);
}

.dark .shadow-xl {
    box-shadow: none;
}
</style>
