<script setup>
import { ref, onMounted } from 'vue';
import { useAuthStore } from '../../stores/auth';
import Sidebar from '../Sidebar.vue';
import Topbar from '../Topbar.vue';
import axios from 'axios';

const props = defineProps({
    authUser: { type: String, default: null }
});

const authStore = useAuthStore();
const isDark = ref(false);

const metrics = ref({});
const metricsLoading = ref(true);

const logs = ref([]);
const logsLoading = ref(true);
const pagination = ref({ current_page: 1, last_page: 1, total: 0 });

const fetchMetrics = async () => {
    metricsLoading.value = true;
    try {
        const response = await axios.get('/admin/audit/metrics');
        metrics.value = response.data;
    } catch (e) {
        console.error("Error fetching metrics", e);
    } finally {
        metricsLoading.value = false;
    }
};

const fetchLogs = async (page = 1) => {
    logsLoading.value = true;
    try {
        const response = await axios.get(`/admin/audit/logs?page=${page}`);
        logs.value = response.data.data;
        pagination.value = {
            current_page: response.data.current_page,
            last_page: response.data.last_page,
            total: response.data.total
        };
    } catch (e) {
        console.error("Error fetching activity logs", e);
    } finally {
        logsLoading.value = false;
    }
};

const changePage = (page) => {
    if (page >= 1 && page <= pagination.value.last_page) {
        fetchLogs(page);
    }
};

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
    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
    }

    if (props.authUser && !authStore.user) {
        try {
            authStore.user = JSON.parse(props.authUser);
            authStore.isAuthenticated = true;
        } catch (e) { }
    }

    fetchMetrics();
    fetchLogs();
});
</script>

<template>
    <div
        class="min-h-screen bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-200 font-sans transition-colors duration-500 relative flex overflow-x-hidden">
        <Sidebar :is-dark="isDark" @toggle-dark="toggleDarkMode" />

        <div class="ml-72 flex-1 flex flex-col min-h-screen relative">
            <Topbar :is-dark="isDark" @toggle-dark="toggleDarkMode" />

            <main class="mt-24 p-4 md:p-8 lg:p-12 flex-1 relative z-10 w-full max-w-7xl mx-auto">
                <div class="mb-10">
                    <h1
                        class="text-4xl font-display font-black text-primary-dark dark:text-white uppercase tracking-tight italic">
                        Métricas Globales <span class="text-aqua-400 not-italic">&</span> Auditoría
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium">
                        Monitorea la salud del sistema, KPI's comerciales y la consola de auditoría de actividad para
                        máxima seguridad.
                    </p>
                </div>

                <!-- KEY METRICS CARDS -->
                <div v-if="metricsLoading" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    <div v-for="i in 4" :key="i"
                        class="h-32 glass-panel rounded-[2rem] animate-pulse bg-slate-200/50 dark:bg-white/5"></div>
                </div>

                <div v-else class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-10">
                    <!-- Card 1 -->
                    <div
                        class="glass-panel p-6 rounded-[2rem] border border-slate-200 dark:border-white/5 relative overflow-hidden group hover:-translate-y-1 transition-transform">
                        <div
                            class="absolute -right-4 -top-4 w-24 h-24 bg-primary/10 rounded-full blur-2xl group-hover:bg-primary/20 transition-colors">
                        </div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total
                                    Usuarios</p>
                                <h3 class="text-4xl font-display font-black text-slate-800 dark:text-white mt-1">{{
                                    metrics.total_users }}</h3>
                                <p class="text-xs font-bold mt-2 text-primary-dark dark:text-primary">
                                    +{{ metrics.new_users_today }} nuevos hoy
                                </p>
                            </div>
                            <div
                                class="w-12 h-12 rounded-xl bg-primary/10 text-primary-dark dark:text-primary flex items-center justify-center">
                                <span class="material-icons">people</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 2 -->
                    <div
                        class="glass-panel p-6 rounded-[2rem] border border-slate-200 dark:border-white/5 relative overflow-hidden group hover:-translate-y-1 transition-transform">
                        <div
                            class="absolute -right-4 -top-4 w-24 h-24 bg-blue-500/10 rounded-full blur-2xl group-hover:bg-blue-500/20 transition-colors">
                        </div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Total Rutinas
                                </p>
                                <h3 class="text-4xl font-display font-black text-slate-800 dark:text-white mt-1">{{
                                    metrics.total_routines }}</h3>
                                <p class="text-xs font-bold mt-2 text-blue-600 dark:text-blue-400">
                                    +{{ metrics.new_routines_today }} globales hoy
                                </p>
                            </div>
                            <div
                                class="w-12 h-12 rounded-xl bg-blue-500/10 text-blue-500 flex items-center justify-center">
                                <span class="material-icons">fitness_center</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 3 -->
                    <div
                        class="glass-panel p-6 rounded-[2rem] border border-slate-200 dark:border-white/5 relative overflow-hidden group hover:-translate-y-1 transition-transform">
                        <div
                            class="absolute -right-4 -top-4 w-24 h-24 bg-purple-500/10 rounded-full blur-2xl group-hover:bg-purple-500/20 transition-colors">
                        </div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">Auditoría /
                                    Trazas</p>
                                <h3 class="text-4xl font-display font-black text-slate-800 dark:text-white mt-1">{{
                                    metrics.total_actions }}</h3>
                                <p class="text-xs font-bold mt-2 text-purple-600 dark:text-purple-400">
                                    +{{ metrics.actions_today }} trazas hoy
                                </p>
                            </div>
                            <div
                                class="w-12 h-12 rounded-xl bg-purple-500/10 text-purple-500 flex items-center justify-center">
                                <span class="material-icons">history</span>
                            </div>
                        </div>
                    </div>

                    <!-- Card 4 -->
                    <div
                        class="glass-panel p-6 rounded-[2rem] border border-slate-200 dark:border-white/5 relative overflow-hidden group hover:-translate-y-1 transition-transform">
                        <div
                            class="absolute -right-4 -top-4 w-24 h-24 bg-emerald-500/10 rounded-full blur-2xl group-hover:bg-emerald-500/20 transition-colors">
                        </div>
                        <div class="flex justify-between items-start relative z-10">
                            <div>
                                <p class="text-[10px] font-black uppercase tracking-widest text-slate-400">DB Size
                                    (Est.)</p>
                                <h3 class="text-4xl font-display font-black text-slate-800 dark:text-white mt-1">{{
                                    metrics.db_size_mb }} <span class="text-lg">MB</span></h3>
                                <p class="text-xs font-bold mt-2 text-emerald-600 dark:text-emerald-400">
                                    Salud Base de Datos
                                </p>
                            </div>
                            <div
                                class="w-12 h-12 rounded-xl bg-emerald-500/10 text-emerald-500 flex items-center justify-center">
                                <span class="material-icons">storage</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- ACTIVITY LOG TABLE -->
                <div
                    class="glass-panel rounded-[2.5rem] border border-slate-200 dark:border-white/5 shadow-xl shadow-slate-200/50 dark:shadow-none overflow-hidden flex flex-col min-h-[500px]">
                    <div
                        class="px-8 py-6 border-b border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-white/5 flex items-center justify-between">
                        <div>
                            <h2
                                class="text-xl font-display font-black text-primary-dark dark:text-white uppercase tracking-tight italic">
                                Registro de <span class="text-aqua-400 not-italic">Actividades</span>
                            </h2>
                            <p class="text-xs text-slate-500 font-medium mt-1">Visor inmutable de la traza CUD en los
                                Modelos del sistema.</p>
                        </div>
                        <button @click="fetchLogs(pagination.current_page)"
                            class="w-10 h-10 rounded-xl bg-white dark:bg-slate-800 shadow flex items-center justify-center text-slate-500 hover:text-aqua-400 transition-colors">
                            <span class="material-icons text-sm" :class="{ 'animate-spin': logsLoading }">sync</span>
                        </button>
                    </div>

                    <div v-if="logsLoading && logs.length === 0" class="flex-1 flex items-center justify-center p-20">
                        <div class="w-10 h-10 border-4 border-aqua-400/20 border-t-aqua-400 rounded-full animate-spin">
                        </div>
                    </div>

                    <div v-else-if="logs.length === 0" class="flex-1 flex flex-col items-center justify-center py-20">
                        <span
                            class="material-icons text-slate-300 dark:text-slate-700 text-6xl mb-4">history_toggle_off</span>
                        <p class="text-slate-500 font-bold uppercase tracking-widest text-sm">No hay registros de
                            actividad aún</p>
                    </div>

                    <div v-else class="flex-1 overflow-x-auto">
                        <table class="w-full text-left">
                            <thead
                                class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-white/5 text-[10px] uppercase font-black tracking-widest text-slate-400">
                                <tr>
                                    <th class="px-6 py-4 whitespace-nowrap">Fecha / Hora</th>
                                    <th class="px-6 py-4">Actor Responsable</th>
                                    <th class="px-6 py-4">Acción / Evento</th>
                                    <th class="px-6 py-4">Modelo Auditado</th>
                                    <th class="px-6 py-4 whitespace-nowrap">Subject ID</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-white/5 text-sm">
                                <tr v-for="log in logs" :key="log.id"
                                    class="group hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                                    <td class="px-6 py-5 whitespace-nowrap text-slate-500 shrink-0">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-800 dark:text-slate-200">{{
                                                log.created_at_human }}</span>
                                            <span class="text-[10px]">{{ log.created_at }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex flex-col">
                                            <span class="font-bold text-slate-700 dark:text-white">{{ log.causer_name
                                                }}</span>
                                            <span class="text-xs text-slate-500">{{ log.causer_email }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <span
                                            class="inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border"
                                            :class="{
                                                'bg-green-500/10 text-green-600 border-green-500/20': log.description === 'created',
                                                'bg-blue-500/10 text-blue-600 border-blue-500/20': log.description === 'updated',
                                                'bg-red-500/10 text-red-600 border-red-500/20': log.description === 'deleted'
                                            }">
                                            {{ log.description }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-5 text-slate-600 dark:text-slate-300 font-bold">
                                        {{ log.log_name || 'Desconocido' }}
                                    </td>
                                    <td class="px-6 py-5 font-mono text-xs text-slate-400">
                                        #{{ log.subject_id || '-' }}
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="px-8 py-4 border-t border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-white/5 flex items-center justify-between"
                        v-if="pagination.total > 0">
                        <span class="text-xs font-bold text-slate-500">Total registros: <span
                                class="text-primary-dark dark:text-white">{{ pagination.total }}</span></span>

                        <div class="flex gap-2">
                            <button @click="changePage(pagination.current_page - 1)"
                                :disabled="pagination.current_page === 1"
                                class="w-8 h-8 rounded-lg border border-slate-200 dark:border-white/10 flex items-center justify-center hover:bg-white dark:hover:bg-slate-700 disabled:opacity-50 transition-colors">
                                <span class="material-icons text-sm">chevron_left</span>
                            </button>
                            <span class="text-xs font-bold w-12 text-center leading-8">
                                {{ pagination.current_page }} / {{ pagination.last_page }}
                            </span>
                            <button @click="changePage(pagination.current_page + 1)"
                                :disabled="pagination.current_page === pagination.last_page"
                                class="w-8 h-8 rounded-lg border border-slate-200 dark:border-white/10 flex items-center justify-center hover:bg-white dark:hover:bg-slate-700 disabled:opacity-50 transition-colors">
                                <span class="material-icons text-sm">chevron_right</span>
                            </button>
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
</style>
