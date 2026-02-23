<script setup>
import { ref, onMounted, computed } from 'vue';
import api from '../services/api';
import Sidebar from './Sidebar.vue';
import Topbar from './Topbar.vue';

// ─── State ────────────────────────────────────────────────────────────────────
const activeSection = ref('dashboard'); // 'dashboard' | 'alumnos' | 'rutinas'

const students = ref([]);
const routines = ref([]);
const exercises = ref([]);
const dashStats = ref(null);

const loading = ref(true);
const error = ref(null);

const isDark = ref(false);

// ─── Modal ────────────────────────────────────────────────────────────────────
const showAssignModal = ref(false);
const selectedStudent = ref(null);
const selectedRoutineId = ref('');
const assigning = ref(false);
const assignmentSuccess = ref(false);

// ─── Theme ────────────────────────────────────────────────────────────────────
const toggleDarkMode = () => {
    isDark.value = !isDark.value;
    document.documentElement.classList.toggle('dark', isDark.value);
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
};

// ─── API Calls ────────────────────────────────────────────────────────────────
const fetchStats = async () => {
    try {
        const { data } = await api.get('/trainer/stats');
        dashStats.value = data;
    } catch (err) {
        console.error('Error cargando stats', err);
    }
};

const fetchStudents = async () => {
    try {
        const { data } = await api.get('/trainer/students');
        students.value = data.data;
    } catch (err) {
        error.value = 'No se pudo cargar la lista de alumnos.';
        console.error(err);
    }
};

const fetchRoutines = async () => {
    try {
        const { data } = await api.get('/trainer/routines');
        routines.value = data.data;
    } catch (err) {
        console.error('Error cargando rutinas', err);
    }
};

const fetchExercises = async () => {
    try {
        const { data } = await api.get('/trainer/exercises');
        exercises.value = data.data;
    } catch (err) {
        console.error('Error cargando ejercicios', err);
    }
};

const loadAll = async () => {
    loading.value = true;
    error.value = null;
    await Promise.all([fetchStats(), fetchStudents(), fetchRoutines(), fetchExercises()]);
    loading.value = false;
};

// ─── Helpers ──────────────────────────────────────────────────────────────────
const openWhatsApp = (phone) => window.open(`https://wa.me/${phone.replace(/\D/g, '')}`, '_blank');
const openMail = (email) => { window.location.href = `mailto:${email}`; };

const openAssignModal = (student) => {
    selectedStudent.value = student;
    selectedRoutineId.value = '';
    assignmentSuccess.value = false;
    showAssignModal.value = true;
};

const closeAssignModal = () => {
    showAssignModal.value = false;
    selectedStudent.value = null;
};

const assignRoutine = async () => {
    if (!selectedRoutineId.value) return;
    assigning.value = true;
    try {
        await api.post('/trainer/routines/assign', {
            routine_id: selectedRoutineId.value,
            student_id: selectedStudent.value.id,
        });
        assignmentSuccess.value = true;
        setTimeout(closeAssignModal, 2000);
    } catch (err) {
        console.error('Error asignando rutina', err);
        alert('Hubo un error al asignar la rutina.');
    } finally {
        assigning.value = false;
    }
};

// ─── Difficulty badge colours ─────────────────────────────────────────────────
const difficultyColor = {
    'Básico': 'bg-green-500/10 text-green-500 border-green-500/20',
    'Intermedio': 'bg-yellow-500/10 text-yellow-500 border-yellow-500/20',
    'Avanzado': 'bg-orange-500/10 text-orange-500 border-orange-500/20',
    'Elite': 'bg-red-500/10 text-red-500 border-red-500/20',
};

// ─── Lifecycle ────────────────────────────────────────────────────────────────
onMounted(() => {
    const saved = localStorage.getItem('theme');
    isDark.value = saved === 'dark' || (!saved && window.matchMedia('(prefers-color-scheme: dark)').matches);
    document.documentElement.classList.toggle('dark', isDark.value);
    loadAll();
});
</script>

<template>
    <div
        class="min-h-screen bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-200 font-sans transition-colors duration-500 flex overflow-x-hidden">

        <!-- Sidebar -->
        <Sidebar :is-dark="isDark" @toggle-dark="toggleDarkMode" />

        <!-- Main Area -->
        <div class="ml-72 flex-1 flex flex-col min-h-screen">

            <!-- Topbar -->
            <Topbar :is-dark="isDark" :active-section="activeSection" @toggle-dark="toggleDarkMode"
                @section-change="activeSection = $event" />

            <!-- Main Content (offset for Topbar height only) -->
            <main class="mt-20 p-4 md:p-8 lg:p-12 flex-1 relative z-10">

                <!-- Background Glow -->
                <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
                    <div
                        class="absolute -top-[10%] -left-[10%] opacity-20 dark:opacity-30 blur-[120px] w-[50%] h-[50%] bg-primary rounded-full">
                    </div>
                    <div
                        class="absolute top-[60%] -right-[5%] opacity-10 dark:opacity-20 blur-[100px] w-[40%] h-[40%] bg-aqua-400 rounded-full">
                    </div>
                </div>

                <!-- Loading -->
                <div v-if="loading" class="flex flex-col items-center justify-center py-32 gap-6">
                    <div class="w-16 h-16 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
                    <p class="text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest text-sm italic">
                        Sincronizando datos...</p>
                </div>

                <div v-else class="max-w-7xl mx-auto">

                    <!-- ═══════════════════════════════════════════════════════ -->
                    <!-- SECTION: DASHBOARD                                     -->
                    <!-- ═══════════════════════════════════════════════════════ -->
                    <template v-if="activeSection === 'dashboard'">

                        <!-- Header -->
                        <header class="mb-10 flex flex-col md:flex-row md:items-end justify-between gap-6">
                            <div>
                                <h1
                                    class="text-5xl font-display font-black text-primary-dark dark:text-white tracking-tight uppercase italic">
                                    <span class="text-primary not-italic">Fox</span>Fit
                                    <span
                                        class="font-light not-italic text-slate-400 dark:text-slate-500 text-3xl ml-2">Personal
                                        Trainer</span>
                                </h1>
                                <p class="text-slate-500 dark:text-slate-400 mt-2 flex items-center gap-2 font-medium">
                                    <span
                                        class="w-2.5 h-2.5 rounded-full bg-primary animate-pulse shadow-[0_0_10px_rgba(255,85,0,0.8)]"></span>
                                    Gestión estratégica de alumnos y rendimiento
                                </p>
                            </div>
                        </header>

                        <!-- KPI Cards -->
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mb-12">
                            <!-- Alumnos -->
                            <button @click="activeSection = 'alumnos'"
                                class="glass-panel p-8 rounded-[2rem] border border-slate-200 dark:border-white/5 shadow-sm group hover:border-primary/30 transition-all text-left">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="material-icons text-primary text-3xl">groups</span>
                                    <span
                                        class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Total</span>
                                </div>
                                <p
                                    class="text-4xl font-display font-black text-primary-dark dark:text-white tracking-tighter">
                                    {{ dashStats?.stats?.total_students ?? students.length }}
                                </p>
                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-2">Alumnos
                                </p>
                            </button>

                            <!-- Rutinas -->
                            <button @click="activeSection = 'rutinas'"
                                class="glass-panel p-8 rounded-[2rem] border border-slate-200 dark:border-white/5 shadow-sm group hover:border-primary/30 transition-all text-left">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="material-icons text-aqua-400 text-3xl">fitness_center</span>
                                    <span
                                        class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Creadas</span>
                                </div>
                                <p
                                    class="text-4xl font-display font-black text-primary-dark dark:text-white tracking-tighter">
                                    {{ dashStats?.stats?.total_routines ?? routines.length }}
                                </p>
                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-2">Rutinas
                                </p>
                            </button>

                            <!-- Asignadas -->
                            <div
                                class="glass-panel p-8 rounded-[2rem] border border-slate-200 dark:border-white/5 shadow-sm">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="material-icons text-primary text-3xl">assignment_turned_in</span>
                                    <span
                                        class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Activas</span>
                                </div>
                                <p
                                    class="text-4xl font-display font-black text-primary-dark dark:text-white tracking-tighter">
                                    {{ dashStats?.stats?.assigned_routines ?? 0 }}
                                </p>
                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-2">Rutinas
                                    Asignadas</p>
                            </div>

                            <!-- Ejercicios -->
                            <div
                                class="glass-panel p-8 rounded-[2rem] border border-slate-200 dark:border-white/5 shadow-sm">
                                <div class="flex items-center justify-between mb-4">
                                    <span class="material-icons text-aqua-400 text-3xl">sports_gymnastics</span>
                                    <span
                                        class="text-[9px] font-black text-slate-400 uppercase tracking-widest">Catálogo</span>
                                </div>
                                <p
                                    class="text-4xl font-display font-black text-primary-dark dark:text-white tracking-tighter">
                                    {{ dashStats?.stats?.total_exercises ?? exercises.length }}
                                </p>
                                <p class="text-[10px] text-slate-400 font-black uppercase tracking-widest mt-2">
                                    Ejercicios
                                </p>
                            </div>
                        </div>

                        <!-- OBS Studio Panel -->
                        <div v-if="dashStats?.channel"
                            class="mb-12 glass-panel rounded-[3rem] border border-slate-200 dark:border-white/5 overflow-hidden shadow-2xl relative">
                            <!-- Background elements -->
                            <div
                                class="absolute inset-0 bg-gradient-to-r from-red-600/10 to-transparent pointer-events-none">
                            </div>

                            <div
                                class="px-8 md:px-12 py-10 relative z-10 flex flex-col md:flex-row items-center justify-between gap-8">
                                <div class="flex-1">
                                    <div class="flex items-center gap-3 mb-4">
                                        <span
                                            class="px-3 py-1 bg-red-500 text-white text-[10px] font-black tracking-widest uppercase rounded-full flex items-center gap-2 shadow-lg shadow-red-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span>
                                            Transmitir
                                        </span>
                                        <h2
                                            class="text-3xl font-display font-black text-primary-dark dark:text-white uppercase tracking-tight italic">
                                            Mi Estudio <span class="text-primary not-italic">En Vivo</span>
                                        </h2>
                                    </div>
                                    <p class="text-slate-500 dark:text-slate-400 font-medium max-w-xl">
                                        Configura tu software de transmisión (ej. OBS Studio) con estos datos para
                                        empezar a emitir clases en directo a tus alumnos en FoxFit TV.
                                    </p>
                                </div>

                                <div
                                    class="w-full md:w-auto bg-slate-100 dark:bg-slate-900/50 p-6 rounded-3xl border border-slate-200 dark:border-white/10 space-y-4">
                                    <div>
                                        <span
                                            class="block text-[10px] font-black uppercase tracking-widest text-slate-400 mb-1">Tu
                                            Clave de Transmisión</span>
                                        <div
                                            class="flex items-center gap-2 bg-white dark:bg-slate-800 px-4 py-2.5 rounded-xl border border-slate-200 dark:border-white/5">
                                            <code
                                                class="text-primary-dark dark:text-white font-bold text-sm flex-1 truncate max-w-[150px]">{{ dashStats.channel.stream_key || 'No disponible' }}</code>
                                            <button @click="navigator.clipboard.writeText(dashStats.channel.stream_key)"
                                                class="text-primary hover:text-primary-dark transition-colors px-2"
                                                title="Copiar Clave">
                                                <span class="material-icons text-lg">content_copy</span>
                                            </button>
                                        </div>
                                    </div>

                                    <a href="/trainer/studio"
                                        class="w-full shrink-0 bg-red-500 text-white hover:bg-red-600 transition-colors px-6 py-3 rounded-xl shadow-lg shadow-red-500/20 font-bold uppercase tracking-wide flex items-center justify-center gap-2 hover:scale-105 duration-300">
                                        Entrar al Estudio
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Bottom Grid: Recent Students + Recent Routines -->
                        <div class="grid lg:grid-cols-2 gap-8">

                            <!-- Recent Students -->
                            <div
                                class="glass-panel rounded-[2.5rem] border border-slate-200 dark:border-white/5 overflow-hidden">
                                <div
                                    class="px-8 py-6 border-b border-slate-100 dark:border-white/5 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="material-icons text-primary">person_pin</span>
                                        <h2
                                            class="text-base font-display font-black text-primary-dark dark:text-white uppercase tracking-tight italic">
                                            Alumnos Recientes
                                        </h2>
                                    </div>
                                    <button @click="activeSection = 'alumnos'"
                                        class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-primary transition-colors">
                                        Ver todos →
                                    </button>
                                </div>
                                <div v-if="!dashStats?.recent_students?.length"
                                    class="p-8 text-center text-slate-400 text-sm">
                                    Sin alumnos aún.
                                </div>
                                <ul v-else class="divide-y divide-slate-100 dark:divide-white/5">
                                    <li v-for="s in dashStats.recent_students.slice(0, 5)" :key="s.id"
                                        class="flex items-center gap-4 px-8 py-4 hover:bg-primary/[0.03] dark:hover:bg-primary/[0.05] transition-all">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-gradient-to-tr from-primary-dark to-slate-700 dark:from-primary dark:to-aqua-300 flex items-center justify-center text-white dark:text-primary-dark font-black text-lg shadow-md shrink-0">
                                            {{ s.name?.charAt(0) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-primary-dark dark:text-white truncate">{{
                                                s.name }}</p>
                                            <p class="text-xs text-slate-400 truncate">{{ s.email }}</p>
                                        </div>
                                        <span class="text-[10px] text-slate-400 font-black uppercase">{{ s.last_activity
                                            }}</span>
                                    </li>
                                </ul>
                            </div>

                            <!-- Recent Routines -->
                            <div
                                class="glass-panel rounded-[2.5rem] border border-slate-200 dark:border-white/5 overflow-hidden">
                                <div
                                    class="px-8 py-6 border-b border-slate-100 dark:border-white/5 flex items-center justify-between">
                                    <div class="flex items-center gap-3">
                                        <span class="material-icons text-aqua-400">star</span>
                                        <h2
                                            class="text-base font-display font-black text-primary-dark dark:text-white uppercase tracking-tight italic">
                                            Últimas Rutinas
                                        </h2>
                                    </div>
                                    <button @click="activeSection = 'rutinas'"
                                        class="text-[10px] font-black uppercase tracking-widest text-slate-400 hover:text-primary transition-colors">
                                        Ver todas →
                                    </button>
                                </div>
                                <div v-if="!dashStats?.recent_routines?.length"
                                    class="p-8 text-center text-slate-400 text-sm">
                                    Aún no hay rutinas creadas.
                                </div>
                                <ul v-else class="divide-y divide-slate-100 dark:divide-white/5">
                                    <li v-for="r in dashStats.recent_routines.slice(0, 5)" :key="r.id"
                                        class="flex items-center gap-4 px-8 py-4 hover:bg-primary/[0.03] dark:hover:bg-primary/[0.05] transition-all">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-aqua-400/10 flex items-center justify-center shrink-0">
                                            <span class="material-icons text-aqua-400 text-xl">bolt</span>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-bold text-primary-dark dark:text-white truncate">{{
                                                r.name }}</p>
                                            <p class="text-xs text-slate-400">{{ r.exercises_count }} ejercicios · {{
                                                r.created_at }}</p>
                                        </div>
                                        <span
                                            :class="['text-[10px] font-black uppercase px-2.5 py-1 rounded-full border', difficultyColor[r.difficulty] || 'bg-slate-100 text-slate-400 border-slate-200']">
                                            {{ r.difficulty }}
                                        </span>
                                    </li>
                                </ul>
                            </div>

                        </div>
                    </template>

                    <!-- ═══════════════════════════════════════════════════════ -->
                    <!-- SECTION: ALUMNOS                                       -->
                    <!-- ═══════════════════════════════════════════════════════ -->
                    <template v-else-if="activeSection === 'alumnos'">

                        <header class="mb-8 flex items-center justify-between">
                            <div>
                                <h2
                                    class="text-4xl font-display font-black text-primary-dark dark:text-white tracking-tight uppercase italic">
                                    Mis <span class="text-primary not-italic">Alumnos</span>
                                </h2>
                                <p class="text-slate-400 text-sm mt-1">{{ students.length }} alumnos registrados</p>
                            </div>
                            <div
                                class="flex items-center gap-3 bg-white dark:bg-slate-800/50 backdrop-blur-xl px-6 py-4 rounded-2xl border border-slate-200 dark:border-white/10 shadow-xl">
                                <span class="material-icons text-primary text-3xl">groups</span>
                                <p class="text-3xl font-display font-bold text-primary-dark dark:text-white">{{
                                    students.length }}</p>
                            </div>
                        </header>

                        <!-- Error state -->
                        <div v-if="error"
                            class="bg-red-500/10 border border-red-500/20 p-8 rounded-[2rem] text-center backdrop-blur-md">
                            <span class="material-icons text-red-500 text-5xl mb-3">gpp_maybe</span>
                            <p class="text-red-800 dark:text-red-200 font-bold text-lg">{{ error }}</p>
                        </div>

                        <!-- Empty state -->
                        <div v-else-if="students.length === 0"
                            class="glass-panel p-24 text-center rounded-[3rem] border border-slate-200 dark:border-white/5 shadow-2xl">
                            <span
                                class="material-icons text-slate-300 dark:text-slate-700 text-5xl mb-4">person_search</span>
                            <h3 class="text-2xl font-display font-bold text-primary-dark dark:text-white mb-2">Sin
                                alumnos aún</h3>
                            <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto text-sm">Empieza a vincular
                                alumnos para gestionar sus rutinas y ver su progreso.</p>
                        </div>

                        <!-- CRM Table -->
                        <div v-else
                            class="glass-panel overflow-hidden rounded-[3rem] border border-slate-200 dark:border-white/5 shadow-2xl shadow-slate-200/50 dark:shadow-none">
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr
                                            class="bg-slate-50 dark:bg-white/5 border-b border-slate-200 dark:border-white/5">
                                            <th
                                                class="px-8 py-6 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                                Alumno</th>
                                            <th
                                                class="px-8 py-6 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                                Estado</th>
                                            <th
                                                class="px-8 py-6 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                                Actividad</th>
                                            <th
                                                class="px-8 py-6 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 text-right">
                                                Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                                        <tr v-for="student in students" :key="student.id"
                                            class="group hover:bg-primary/[0.03] dark:hover:bg-primary/[0.05] transition-all duration-300">
                                            <td class="px-8 py-6">
                                                <div class="flex items-center gap-4">
                                                    <div
                                                        class="w-12 h-12 rounded-2xl bg-gradient-to-tr from-primary-dark to-slate-800 dark:from-primary dark:to-aqua-300 flex items-center justify-center text-white dark:text-primary-dark font-black text-xl shadow-lg group-hover:scale-110 transition-transform duration-500">
                                                        {{ student.name.charAt(0) }}
                                                    </div>
                                                    <div>
                                                        <p
                                                            class="text-primary-dark dark:text-white font-bold font-display">
                                                            {{ student.name }}</p>
                                                        <p class="text-slate-400 text-xs">{{ student.email }}</p>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-8 py-6">
                                                <span
                                                    class="inline-flex items-center px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest bg-primary/10 text-primary border border-primary/20">
                                                    {{ student.subscription_status || 'Activo' }}
                                                </span>
                                            </td>
                                            <td class="px-8 py-6">
                                                <p
                                                    class="text-slate-500 dark:text-slate-400 text-sm font-medium flex items-center gap-2">
                                                    <span
                                                        class="material-icons text-primary text-base">monitoring</span>
                                                    {{ student.last_activity }}
                                                </p>
                                            </td>
                                            <td class="px-8 py-6 text-right">
                                                <div
                                                    class="flex items-center justify-end gap-2 translate-x-4 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-500">
                                                    <button @click="openAssignModal(student)"
                                                        class="px-4 py-2.5 rounded-xl bg-primary-dark dark:bg-primary text-white dark:text-primary-dark font-black text-[10px] uppercase tracking-widest hover:scale-105 active:scale-95 transition-all shadow-lg shadow-primary/20">
                                                        Asignar Rutina
                                                    </button>
                                                    <button @click="openWhatsApp(student.phone || '56900000000')"
                                                        class="w-10 h-10 rounded-xl bg-green-500/10 text-green-500 hover:bg-green-500 hover:text-white transition-all flex items-center justify-center border border-green-500/20">
                                                        <svg viewBox="0 0 24 24" class="w-5 h-5 fill-current">
                                                            <path
                                                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                                        </svg>
                                                    </button>
                                                    <button @click="openMail(student.email)"
                                                        class="w-10 h-10 rounded-xl bg-aqua-500/10 text-aqua-600 hover:bg-aqua-500 hover:text-white transition-all flex items-center justify-center border border-aqua-500/20">
                                                        <span class="material-icons text-lg">alternate_email</span>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </template>

                    <!-- ═══════════════════════════════════════════════════════ -->
                    <!-- SECTION: RUTINAS                                       -->
                    <!-- ═══════════════════════════════════════════════════════ -->
                    <template v-else-if="activeSection === 'rutinas'">

                        <header class="mb-8 flex items-center justify-between">
                            <div>
                                <h2
                                    class="text-4xl font-display font-black text-primary-dark dark:text-white tracking-tight uppercase italic">
                                    Mis <span class="text-primary not-italic">Rutinas</span>
                                </h2>
                                <p class="text-slate-400 text-sm mt-1">{{ routines.length }} rutinas creadas</p>
                            </div>
                        </header>

                        <div v-if="routines.length === 0"
                            class="glass-panel p-24 text-center rounded-[3rem] border border-slate-200 dark:border-white/5">
                            <span
                                class="material-icons text-slate-300 dark:text-slate-700 text-5xl mb-4">fitness_center</span>
                            <h3 class="text-2xl font-display font-bold text-primary-dark dark:text-white mb-2">Sin
                                rutinas aún</h3>
                            <p class="text-slate-500 dark:text-slate-400 text-sm">Crea tu primera rutina para empezar a
                                asignarla a tus alumnos.</p>
                        </div>

                        <div v-else class="grid sm:grid-cols-2 xl:grid-cols-3 gap-6">
                            <div v-for="r in routines" :key="r.id"
                                class="glass-panel rounded-[2rem] border border-slate-200 dark:border-white/5 p-8 hover:border-primary/30 transition-all group shadow-sm hover:shadow-xl hover:shadow-primary/5">
                                <!-- Header -->
                                <div class="flex items-start justify-between mb-6">
                                    <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center">
                                        <span class="material-icons text-primary text-2xl">bolt</span>
                                    </div>
                                    <span
                                        :class="['text-[10px] font-black uppercase px-3 py-1.5 rounded-full border', difficultyColor[r.difficulty] || 'bg-slate-100 text-slate-400 border-slate-200']">
                                        {{ r.difficulty }}
                                    </span>
                                </div>
                                <!-- Name -->
                                <h3
                                    class="text-xl font-display font-black text-primary-dark dark:text-white mb-2 group-hover:text-primary transition-colors">
                                    {{ r.name }}
                                </h3>
                                <p v-if="r.description" class="text-slate-400 text-sm mb-6 line-clamp-2">{{
                                    r.description }}</p>
                                <!-- Stats row -->
                                <div
                                    class="flex items-center gap-6 mt-auto text-[10px] font-black uppercase tracking-widest text-slate-400">
                                    <span class="flex items-center gap-1.5">
                                        <span class="material-icons text-sm text-primary">sports_gymnastics</span>
                                        {{ r.exercises?.length ?? 0 }} ejercicios
                                    </span>
                                    <span class="flex items-center gap-1.5">
                                        <span class="material-icons text-sm text-aqua-400">group</span>
                                        {{ r.students_count ?? 0 }} alumnos
                                    </span>
                                </div>
                            </div>
                        </div>
                    </template>

                </div><!-- /max-w-7xl -->
            </main><!-- /main -->
        </div><!-- /right panel -->
    </div>

    <!-- ═════════════════════════════════════════════════════════════════════ -->
    <!-- ASSIGN ROUTINE MODAL                                                  -->
    <!-- ═════════════════════════════════════════════════════════════════════ -->
    <div v-if="showAssignModal"
        class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-primary-dark/40 dark:bg-slate-950/80 backdrop-blur-xl">
        <div
            class="max-w-md w-full bg-white dark:bg-slate-900 rounded-[3rem] overflow-hidden border border-slate-200 dark:border-white/10 shadow-2xl p-10">
            <div class="flex justify-between items-start mb-8">
                <div>
                    <h2
                        class="text-3xl font-display font-black text-primary-dark dark:text-white uppercase tracking-tight italic">
                        Nueva <span class="text-primary not-italic">Misión</span>
                    </h2>
                    <p class="text-slate-500 dark:text-slate-400 text-sm mt-2 font-medium">
                        Para <span class="text-primary-dark dark:text-white font-bold">{{ selectedStudent?.name
                            }}</span>
                    </p>
                </div>
                <button @click="closeAssignModal"
                    class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-white/5 text-slate-400 transition-colors">
                    <span class="material-icons">close</span>
                </button>
            </div>

            <div v-if="assignmentSuccess" class="py-12 text-center">
                <div class="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                    <span class="material-icons text-primary text-5xl animate-bounce">check_circle</span>
                </div>
                <p class="text-primary-dark dark:text-white font-black text-2xl font-display italic uppercase">Objetivo
                    Asignado</p>
                <p class="text-slate-500 dark:text-slate-400 mt-2 text-sm">La rutina se ha vinculado exitosamente.</p>
            </div>

            <div v-else class="space-y-6">
                <div>
                    <label
                        class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 mb-3">
                        Seleccionar Rutina
                    </label>
                    <select v-model="selectedRoutineId"
                        class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-2xl px-5 py-4 text-primary-dark dark:text-white font-bold focus:outline-none focus:ring-2 focus:ring-primary/50 appearance-none transition-all">
                        <option value="" disabled>Seleccionar rutina...</option>
                        <option v-for="r in routines" :key="r.id" :value="r.id">
                            {{ r.name }} ({{ r.difficulty }})
                        </option>
                    </select>
                    <p v-if="routines.length === 0"
                        class="text-xs text-amber-500 mt-2 flex items-center gap-1 font-bold">
                        <span class="material-icons text-sm">warning</span> No hay rutinas creadas aún.
                    </p>
                </div>
                <div class="flex gap-3">
                    <button @click="closeAssignModal"
                        class="flex-1 py-4 bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 text-primary-dark dark:text-white font-black rounded-2xl transition-all uppercase tracking-widest text-[10px]">
                        Cancelar
                    </button>
                    <button @click="assignRoutine" :disabled="!selectedRoutineId || assigning"
                        class="flex-1 py-4 bg-primary hover:bg-primary/90 text-primary-dark font-black rounded-2xl shadow-xl shadow-primary/20 transition-all disabled:opacity-30 disabled:cursor-not-allowed uppercase tracking-widest text-[10px]">
                        <span v-if="assigning" class="flex items-center justify-center gap-2">
                            <span
                                class="w-4 h-4 border-2 border-primary-dark/30 border-t-primary-dark rounded-full animate-spin"></span>
                            Asignando...
                        </span>
                        <span v-else>Asignar</span>
                    </button>
                </div>
            </div>
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
    border-color: rgba(255, 255, 255, 0.03);
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
    background: rgba(255, 85, 0, 0.15);
    border-radius: 20px;
}

::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 85, 0, 0.25);
}
</style>
