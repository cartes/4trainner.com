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
        const response = await api.get('/student/dashboard');
        dashboardData.value = response.data;
    } catch (err) {
        error.value = 'No se pudo cargar tu panel.';
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
                            <span class="text-primary not-italic">Fox</span>Fit <span
                                class="font-light not-italic text-slate-400 dark:text-slate-500 text-3xl ml-2 text-right">Energy</span>
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 mt-2 flex items-center gap-2 font-medium">
                            <span
                                class="w-3 h-3 rounded-full bg-primary animate-pulse shadow-[0_0_10px_rgba(212,255,55,0.8)]"></span>
                            Tu entrenamiento estratégico está listo
                        </p>
                    </div>

                    <div v-if="dashboardData"
                        class="flex items-center gap-6 bg-white dark:bg-slate-800/50 backdrop-blur-xl px-8 py-5 rounded-[2rem] border border-slate-200 dark:border-white/10 shadow-xl shadow-slate-200/50 dark:shadow-none">
                        <div class="text-right">
                            <p
                                class="text-[10px] text-slate-400 dark:text-slate-500 font-black uppercase tracking-[0.2em]">
                                Sesiones Totales</p>
                            <p class="text-3xl font-display font-bold text-primary-dark dark:text-white">{{
                                dashboardData.stats.total_routines }}</p>
                        </div>
                        <div class="w-px h-12 bg-slate-200 dark:bg-white/10"></div>
                        <div
                            class="w-14 h-14 rounded-2xl bg-primary flex items-center justify-center shadow-lg shadow-primary/20">
                            <span class="material-icons text-primary-dark text-3xl">fitness_center</span>
                        </div>
                    </div>
                </header>

                <!-- The content that was previously inside the original <main> tag -->
                <!-- Loading -->
                <div v-if="loading" class="flex flex-col items-center justify-center py-32 gap-6">
                    <div class="w-16 h-16 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
                    <p class="text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest text-sm italic">
                        Sincronizando rendimiento...</p>
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
                            <span class="material-icons text-primary text-3xl mb-4">sports_score</span>
                            <p
                                class="text-4xl font-display font-black text-primary-dark dark:text-white tracking-tighter">
                                {{ dashboardData.stats.total_routines }}</p>
                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-2">Rutinas
                                Activas
                            </p>
                        </div>
                        <div
                            class="glass-panel p-8 rounded-[2rem] border border-slate-200 dark:border-white/5 shadow-sm">
                            <span class="material-icons text-aqua-400 text-3xl mb-4">badge</span>
                            <p
                                class="text-4xl font-display font-black text-primary-dark dark:text-white tracking-tighter">
                                {{ dashboardData.stats.total_trainers }}</p>
                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-2">Elite
                                Coaches
                            </p>
                        </div>
                        <div
                            class="glass-panel p-8 rounded-[2rem] border border-slate-200 dark:border-white/5 shadow-sm">
                            <span class="material-icons text-primary text-3xl mb-4">bolt</span>
                            <p
                                class="text-4xl font-display font-black text-primary-dark dark:text-white tracking-tighter">
                                0
                            </p>
                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-2">Streak
                                Actual
                            </p>
                        </div>
                        <div
                            class="glass-panel p-8 rounded-[2rem] border border-slate-200 dark:border-white/5 shadow-sm">
                            <span class="material-icons text-aqua-400 text-3xl mb-4">auto_graph</span>
                            <p
                                class="text-4xl font-display font-black text-primary-dark dark:text-white tracking-tighter">
                                0%</p>
                            <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-2">Power Growth
                            </p>
                        </div>
                    </div>

                    <!-- Main Content Layout -->
                    <div class="grid lg:grid-cols-3 gap-12">
                        <!-- Routines Section (Library Style) -->
                        <div class="lg:col-span-2 space-y-8">
                            <div class="flex items-center justify-between">
                                <h2
                                    class="text-3xl font-display font-black text-primary-dark dark:text-white uppercase tracking-tight italic">
                                    Tu <span class="text-primary not-italic">Librería</span> de Éxito
                                </h2>
                                <button
                                    class="text-xs font-black uppercase tracking-widest text-primary hover:scale-105 transition-all">Ver
                                    Historial</button>
                            </div>

                            <div v-if="dashboardData.routines.length === 0"
                                class="glass-panel p-20 text-center rounded-[3rem] border border-slate-200 dark:border-white/5">
                                <span
                                    class="material-icons text-slate-300 dark:text-slate-700 text-6xl mb-4">auto_fix_high</span>
                                <p class="text-slate-500 font-bold uppercase tracking-widest text-sm">Preparando tu
                                    próxima
                                    misión</p>
                                <p class="text-slate-400 text-sm mt-2">Contacta a tu entrenador para recibir tu primera
                                    rutina personalizada.</p>
                            </div>

                            <div v-else class="grid md:grid-cols-2 gap-8">
                                <div v-for="routine in dashboardData.routines" :key="routine.id"
                                    class="group relative bg-white dark:bg-slate-800/40 rounded-[2.5rem] overflow-hidden border border-slate-200 dark:border-white/5 shadow-lg shadow-slate-200/50 dark:shadow-none hover:translate-y-[-8px] transition-all duration-500">
                                    <!-- Card Image Placeholder (like FoxFit Library) -->
                                    <div class="aspect-[16/10] bg-slate-100 dark:bg-slate-900 overflow-hidden relative">
                                        <div
                                            class="absolute inset-0 bg-gradient-to-t from-primary-dark/80 to-transparent">
                                        </div>
                                        <div class="absolute bottom-6 left-8">
                                            <span
                                                class="inline-flex px-3 py-1 bg-primary text-primary-dark text-[10px] font-black uppercase tracking-tighter rounded-full mb-2 italic">
                                                {{ routine.difficulty || 'Fuerza Pro' }}
                                            </span>
                                            <h3
                                                class="text-white font-display font-black text-2xl uppercase italic tracking-tight">
                                                {{ routine.name }}</h3>
                                        </div>
                                        <!-- Play Icon Overlay -->
                                        <div
                                            class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-500">
                                            <div
                                                class="w-16 h-16 bg-primary rounded-full flex items-center justify-center shadow-2xl shadow-primary/40">
                                                <span
                                                    class="material-icons text-primary-dark text-4xl ml-1">play_arrow</span>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="p-8">
                                        <div class="flex items-center justify-between">
                                            <div class="flex items-center gap-4">
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Capacidad</span>
                                                    <span class="text-primary-dark dark:text-white font-bold">{{
                                                        routine.exercises?.length ?? 0 }} Ejercicios</span>
                                                </div>
                                                <div class="w-px h-8 bg-slate-200 dark:bg-white/10"></div>
                                                <div class="flex flex-col">
                                                    <span
                                                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Duración</span>
                                                    <span class="text-primary-dark dark:text-white font-bold">~45
                                                        min</span>
                                                </div>
                                            </div>
                                            <button
                                                class="w-12 h-12 rounded-2xl bg-slate-50 dark:bg-white/5 text-slate-400 hover:text-primary transition-all flex items-center justify-center border border-slate-200 dark:border-white/5">
                                                <span class="material-icons">east</span>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Side Section: Trainers -->
                        <div class="space-y-8">
                            <div v-if="dashboardData.trainers.length > 0"
                                class="glass-panel rounded-[3rem] border border-slate-200 dark:border-white/5 overflow-hidden">
                                <div class="px-8 py-8 border-b border-slate-100 dark:border-white/5">
                                    <h2
                                        class="text-xl font-display font-black text-primary-dark dark:text-white uppercase tracking-tight italic">
                                        Tus <span class="text-aqua-400 not-italic">Líderes</span>
                                    </h2>
                                </div>
                                <div class="p-4 space-y-4">
                                    <div v-for="trainer in dashboardData.trainers" :key="trainer.id"
                                        class="p-6 bg-white dark:bg-white/5 rounded-[2rem] border border-slate-100 dark:border-transparent hover:border-primary/20 transition-all flex items-center gap-5 group cursor-pointer">
                                        <div
                                            class="w-14 h-14 rounded-2xl bg-primary flex items-center justify-center text-primary-dark font-black text-2xl shadow-lg group-hover:scale-110 transition-transform">
                                            {{ trainer.name.charAt(0) }}
                                        </div>
                                        <div>
                                            <p class="text-primary-dark dark:text-white font-bold">{{ trainer.name }}
                                            </p>
                                            <p class="text-slate-400 text-xs font-medium">{{ trainer.email }}</p>
                                        </div>
                                        <button class="ml-auto p-2 text-slate-300 hover:text-primary transition-colors">
                                            <span class="material-icons text-xl">chat_bubble</span>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <!-- Progress Widget Placeholder -->
                            <div
                                class="glass-panel p-8 rounded-[3rem] border border-slate-200 dark:border-white/5 bg-gradient-to-br from-primary/5 to-transparent">
                                <h3
                                    class="text-sm font-black text-primary-dark dark:text-white uppercase tracking-widest mb-6 italic">
                                    Meta Mensual</h3>
                                <div class="relative pt-1">
                                    <div class="flex mb-2 items-center justify-between">
                                        <div>
                                            <span
                                                class="text-xs font-black inline-block py-1 px-2 uppercase rounded-full text-primary-dark bg-primary">
                                                En camino
                                            </span>
                                        </div>
                                        <div class="text-right">
                                            <span class="text-xs font-black inline-block text-primary">
                                                65%
                                            </span>
                                        </div>
                                    </div>
                                    <div
                                        class="overflow-hidden h-2.5 mb-4 text-xs flex rounded-full bg-slate-100 dark:bg-white/5 relative">
                                        <div style="width:65%"
                                            class="shadow-none flex flex-col text-center whitespace-nowrap text-white justify-center bg-primary rounded-full relative overflow-hidden">
                                            <div class="absolute inset-0 bg-white/20 animate-pulse"></div>
                                        </div>
                                    </div>
                                    <p class="text-xs text-slate-500 font-medium">Has completado 13 de tus 20 sesiones
                                        programadas.</p>
                                </div>
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
