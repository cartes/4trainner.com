<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../stores/auth';
import Sidebar from './Sidebar.vue';
import Topbar from './Topbar.vue';

// Props passed from the blade
const props = defineProps({
    dashboardData: { type: String, default: null },
    authUser: { type: String, default: null },
});

const authStore = useAuthStore();
const dashboardData = ref(null);
const loading = ref(true);
const error = ref(null);

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
    // Theme
    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
    }

    // Hydrate authStore with server-side user data (web session login)
    if (props.authUser && !authStore.user) {
        try {
            const user = JSON.parse(props.authUser);
            authStore.user = user;
            authStore.isAuthenticated = true;
        } catch (e) {
            console.error('Error parsing authUser:', e);
        }
    }

    // Load dashboard data from blade prop
    try {
        if (props.dashboardData) {
            dashboardData.value = JSON.parse(props.dashboardData);
        } else {
            error.value = 'No se recibieron datos del servidor.';
        }
    } catch (e) {
        error.value = 'Error al procesar los datos del panel.';
        console.error('Error parsing dashboardData:', e);
    } finally {
        loading.value = false;
    }
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
                            <span class="text-primary not-italic">Fox</span>Core <span
                                class="font-light not-italic text-slate-400 dark:text-slate-500 text-3xl ml-2">Global
                                Admin</span>
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 mt-2 flex items-center gap-2 font-medium">
                            <span
                                class="w-3 h-3 rounded-full bg-primary animate-pulse shadow-[0_0_10px_rgba(255,85,0,0.8)]"></span>
                            Inteligencia operativa y métricas de plataforma
                        </p>
                    </div>

                    <div v-if="dashboardData"
                        class="flex items-center gap-6 bg-white dark:bg-slate-800/50 backdrop-blur-xl px-8 py-5 rounded-[2rem] border border-slate-200 dark:border-white/10 shadow-xl shadow-slate-200/50 dark:shadow-none">
                        <div class="text-right">
                            <p
                                class="text-[10px] text-slate-400 dark:text-slate-500 font-black uppercase tracking-[0.2em]">
                                Crecimiento 24h</p>
                            <p class="text-3xl font-display font-bold text-primary-dark dark:text-white">+{{
                                dashboardData.new_users_per_day[0]?.count || 0 }}</p>
                        </div>
                        <div class="w-px h-12 bg-slate-200 dark:bg-white/10"></div>
                        <div
                            class="w-14 h-14 rounded-2xl bg-primary flex items-center justify-center shadow-lg shadow-primary/20">
                            <span class="material-icons text-primary-dark text-3xl">terminal</span>
                        </div>
                    </div>
                </header>

                <!-- Loading -->
                <div v-if="loading" class="flex flex-col items-center justify-center py-32 gap-6">
                    <div class="w-16 h-16 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
                    <p class="text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest text-sm italic">
                        Accediendo al núcleo...</p>
                </div>

                <!-- Error -->
                <div v-else-if="error"
                    class="bg-red-500/10 border border-red-500/20 p-8 rounded-[2rem] text-center backdrop-blur-md">
                    <span class="material-icons text-red-500 text-5xl mb-3">gpp_maybe</span>
                    <p class="text-red-800 dark:text-red-200 font-bold text-lg">{{ error }}</p>
                </div>

                <div v-else class="space-y-12">
                    <!-- KPI Stats -->
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div
                            class="glass-panel p-8 rounded-[2rem] border border-slate-200 dark:border-white/5 shadow-sm group hover:border-primary/20 transition-all">
                            <div class="flex items-center justify-between mb-4">
                                <span class="material-icons text-primary text-3xl">all_inclusive</span>
                                <span
                                    class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Global</span>
                            </div>
                            <p
                                class="text-4xl font-display font-black text-primary-dark dark:text-white tracking-tighter">
                                {{ dashboardData.stats.total_users }}</p>
                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-2">Usuarios
                                Totales
                            </p>
                        </div>
                        <div
                            class="glass-panel p-8 rounded-[2rem] border border-slate-200 dark:border-white/5 shadow-sm group hover:border-aqua-400/20 transition-all">
                            <div class="flex items-center justify-between mb-4">
                                <span class="material-icons text-aqua-400 text-3xl">sports</span>
                                <span
                                    class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Coaches</span>
                            </div>
                            <p
                                class="text-4xl font-display font-black text-primary-dark dark:text-white tracking-tighter">
                                {{ dashboardData.stats.total_profesores }}</p>
                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-2">Personal
                                Trainers
                            </p>
                        </div>
                        <div
                            class="glass-panel p-8 rounded-[2rem] border border-slate-200 dark:border-white/5 shadow-sm group hover:border-primary/20 transition-all">
                            <div class="flex items-center justify-between mb-4">
                                <span class="material-icons text-primary text-3xl">school</span>
                                <span
                                    class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Atletas</span>
                            </div>
                            <p
                                class="text-4xl font-display font-black text-primary-dark dark:text-white tracking-tighter">
                                {{ dashboardData.stats.total_alumnos }}</p>
                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-2">Alumnos</p>
                        </div>
                        <div
                            class="glass-panel p-8 rounded-[2rem] border border-slate-200 dark:border-white/5 shadow-sm group hover:border-aqua-400/20 transition-all">
                            <div class="flex items-center justify-between mb-4">
                                <span class="material-icons text-aqua-400 text-3xl">security</span>
                                <span
                                    class="text-[9px] font-black text-slate-400 uppercase tracking-widest">System</span>
                            </div>
                            <p
                                class="text-4xl font-display font-black text-primary-dark dark:text-white tracking-tighter">
                                {{ dashboardData.stats.total_moderators }}</p>
                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-2">Moderadores
                            </p>
                        </div>
                    </div>

                    <!-- Main Content -->
                    <div class="grid lg:grid-cols-2 gap-12">
                        <!-- Growth Metrics -->
                        <div
                            class="glass-panel rounded-[3rem] border border-slate-200 dark:border-white/5 overflow-hidden flex flex-col">
                            <div
                                class="px-10 py-8 border-b border-slate-100 dark:border-white/5 flex items-center gap-3">
                                <span class="material-icons text-primary">analytics</span>
                                <h2
                                    class="text-xl font-display font-black text-primary-dark dark:text-white uppercase tracking-tight italic">
                                    Evolución de <span class="text-primary not-italic">Registro</span></h2>
                            </div>
                            <div class="p-10 flex-1 flex flex-col justify-center">
                                <div v-if="dashboardData.new_users_per_day.length === 0" class="text-center py-20">
                                    <span
                                        class="material-icons text-slate-200 dark:text-slate-800 text-6xl mb-4">show_chart</span>
                                    <p class="text-slate-400 font-bold uppercase tracking-widest text-xs italic">Aún no
                                        hay
                                        datos de crecimiento</p>
                                </div>
                                <div v-else class="space-y-6">
                                    <div v-for="day in dashboardData.new_users_per_day" :key="day.date"
                                        class="flex items-center gap-6 group">
                                        <p
                                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest w-32 shrink-0">
                                            {{ day.date }}</p>
                                        <div
                                            class="flex-1 bg-slate-50 dark:bg-white/5 rounded-full h-3 overflow-hidden border border-slate-100 dark:border-white/5">
                                            <div class="h-full bg-primary rounded-full transition-all duration-1000 shadow-[0_0_15px_rgba(255,85,0,0.3)]"
                                                :style="{ width: `${Math.min((day.count / Math.max(...dashboardData.new_users_per_day.map(d => d.count))) * 100, 100)}%` }">
                                            </div>
                                        </div>
                                        <p
                                            class="text-primary-dark dark:text-white font-display font-black text-lg w-8 text-right italic">
                                            {{ day.count }}</p>
                                    </div>
                                </div>
                            </div>
                            <div
                                class="px-10 py-6 bg-slate-50/50 dark:bg-white/5 border-t border-slate-100 dark:border-white/5">
                                <p class="text-[10px] font-black text-slate-400 uppercase tracking-widest text-center">
                                    Datos
                                    sincronizados en tiempo real con la base de datos central</p>
                            </div>
                        </div>

                        <!-- Recent Activity Table -->
                        <div
                            class="glass-panel rounded-[3rem] border border-slate-200 dark:border-white/5 overflow-hidden">
                            <div
                                class="px-10 py-8 border-b border-slate-100 dark:border-white/5 flex items-center justify-between">
                                <div class="flex items-center gap-3">
                                    <span class="material-icons text-aqua-400">history</span>
                                    <h2
                                        class="text-xl font-display font-black text-primary-dark dark:text-white uppercase tracking-tight italic">
                                        Últimas <span class="text-aqua-400 not-italic">Vitulaciones</span></h2>
                                </div>
                            </div>
                            <div class="divide-y divide-slate-100 dark:divide-white/5">
                                <div v-for="user in dashboardData.recent_users" :key="user.id"
                                    class="px-10 py-6 flex items-center gap-6 group hover:bg-primary/[0.03] dark:hover:bg-primary/[0.05] transition-all duration-300">
                                    <div
                                        class="w-12 h-12 rounded-2xl bg-primary-dark dark:bg-primary flex items-center justify-center text-white dark:text-primary-dark font-black text-lg shadow-lg group-hover:rotate-12 transition-transform duration-500 shrink-0">
                                        {{ user.name.charAt(0) }}
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <p
                                            class="text-primary-dark dark:text-white font-bold text-lg font-display truncate">
                                            {{ user.name }}</p>
                                        <p class="text-slate-400 text-xs font-medium truncate">{{ user.email }}</p>
                                    </div>
                                    <div class="text-right shrink-0">
                                        <div class="flex gap-1 justify-end mb-1">
                                            <span v-for="role in user.roles" :key="role"
                                                class="inline-flex px-2 py-0.5 rounded-full text-[9px] font-black uppercase tracking-tighter bg-aqua-400/10 text-aqua-400 border border-aqua-400/20 italic">
                                                {{ role }}
                                            </span>
                                        </div>
                                        <p class="text-slate-400 dark:text-slate-500 text-[10px] font-medium">{{
                                            user.created_at }}</p>
                                    </div>
                                    <button
                                        class="p-2 text-slate-300 hover:text-primary transition-all opacity-0 group-hover:opacity-100">
                                        <span class="material-icons">more_vert</span>
                                    </button>
                                </div>
                            </div>
                            <div class="p-8 bg-slate-50/50 dark:bg-white/5 text-center">
                                <button
                                    class="text-[10px] font-black uppercase tracking-[0.3em] text-slate-400 hover:text-primary transition-all">Explorar
                                    clúster completo de usuarios</button>
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

.animate-in {
    animation-duration: 0.5s;
}
</style>
