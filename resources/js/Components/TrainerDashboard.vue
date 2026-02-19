<script setup>
import { ref, onMounted } from 'vue';
import api from '../services/api';
import Sidebar from './Sidebar.vue';
import Topbar from './Topbar.vue';

const students = ref([]);
const routines = ref([]);
const exercises = ref([]);
const loading = ref(true);
const error = ref(null);

// Theme state
const isDark = ref(false);

// Modal states
const showAssignModal = ref(false);
const selectedStudent = ref(null);
const selectedRoutineId = ref('');
const assigning = ref(false);
const assignmentSuccess = ref(false);

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

const fetchStudents = async () => {
    try {
        const response = await api.get('/trainer/students');
        students.value = response.data.data;
    } catch (err) {
        error.value = 'No se pudo cargar la lista de alumnos.';
        console.error(err);
    }
};

const fetchRoutines = async () => {
    try {
        const response = await api.get('/trainer/routines');
        routines.value = response.data.data;
    } catch (err) {
        console.error('Error cargando rutinas', err);
    }
};

const fetchExercises = async () => {
    try {
        const response = await api.get('/trainer/exercises');
        exercises.value = response.data.data;
    } catch (err) {
        console.error('Error cargando ejercicios', err);
    }
};

const loadDashboardData = async () => {
    loading.value = true;
    await Promise.all([fetchStudents(), fetchRoutines(), fetchExercises()]);
    loading.value = false;
};

const cleanPhoneNumber = (phone) => {
    return phone.replace(/\D/g, '');
};

const openWhatsApp = (phone) => {
    const cleanNumber = cleanPhoneNumber(phone);
    window.open(`https://wa.me/${cleanNumber}`, '_blank');
};

const openMail = (email) => {
    window.location.href = `mailto:${email}`;
};

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
            student_id: selectedStudent.value.id
        });
        assignmentSuccess.value = true;
        setTimeout(() => {
            closeAssignModal();
        }, 2000);
    } catch (err) {
        console.error('Error asignando rutina', err);
        alert('Hubo un error al asignar la rutina.');
    } finally {
        assigning.value = false;
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
    loadDashboardData();
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



                <!-- Dashboard Header -->
                <header class="max-w-7xl mx-auto mb-12 flex flex-col md:flex-row md:items-center justify-between gap-8">
                    <div>
                        <h1
                            class="text-5xl font-display font-black text-primary-dark dark:text-white tracking-tight uppercase italic">
                            <span class="text-primary not-italic">Fox</span>Fit <span
                                class="font-light not-italic text-slate-400 dark:text-slate-500 text-3xl ml-2">Trainer</span>
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 mt-2 flex items-center gap-2 font-medium">
                            <span
                                class="w-3 h-3 rounded-full bg-primary animate-pulse shadow-[0_0_10px_rgba(212,255,55,0.8)]"></span>
                            Gestión estratégica de alumnos y rendimiento
                        </p>
                    </div>

                    <div
                        class="flex items-center gap-6 bg-white dark:bg-slate-800/50 backdrop-blur-xl px-8 py-5 rounded-[2rem] border border-slate-200 dark:border-white/10 shadow-xl shadow-slate-200/50 dark:shadow-none">
                        <div class="text-right">
                            <p
                                class="text-[10px] text-slate-400 dark:text-slate-500 font-black uppercase tracking-[0.2em]">
                                Comunidad Activa</p>
                            <p class="text-3xl font-display font-bold text-primary-dark dark:text-white">{{
                                students.length
                            }}
                            </p>
                        </div>
                        <div class="w-px h-12 bg-slate-200 dark:bg-white/10"></div>
                        <div
                            class="w-14 h-14 rounded-2xl bg-primary flex items-center justify-center shadow-lg shadow-primary/20">
                            <span class="material-icons text-primary-dark text-3xl">groups</span>
                        </div>
                    </div>
                </header>

                <main class="max-w-7xl mx-auto">
                    <!-- Loading State -->
                    <div v-if="loading" class="flex flex-col items-center justify-center py-32 gap-6">
                        <div class="w-16 h-16 border-4 border-primary/20 border-t-primary rounded-full animate-spin">
                        </div>
                        <p
                            class="text-slate-500 dark:text-slate-400 font-bold uppercase tracking-widest text-sm italic">
                            Sincronizando datos...</p>
                    </div>

                    <!-- Error State -->
                    <div v-else-if="error"
                        class="bg-red-500/10 border border-red-500/20 p-8 rounded-[2rem] text-center backdrop-blur-md">
                        <span class="material-icons text-red-500 text-5xl mb-3">gpp_maybe</span>
                        <p class="text-red-800 dark:text-red-200 font-bold text-lg">{{ error }}</p>
                    </div>

                    <!-- Empty State -->
                    <div v-else-if="students.length === 0"
                        class="glass-panel p-32 text-center rounded-[3rem] border border-slate-200 dark:border-white/5 shadow-2xl">
                        <div
                            class="w-20 h-20 bg-slate-100 dark:bg-white/5 rounded-full flex items-center justify-center mx-auto mb-6">
                            <span
                                class="material-icons text-slate-300 dark:text-slate-700 text-5xl">person_search</span>
                        </div>
                        <h2 class="text-3xl font-display font-bold text-primary-dark dark:text-white mb-3">Tu equipo
                            está
                            listo
                        </h2>
                        <p class="text-slate-500 dark:text-slate-400 max-w-sm mx-auto">Empieza a vincular alumnos para
                            gestionar
                            sus rutinas y ver su progreso en tiempo real.</p>
                    </div>

                    <!-- Students List (CRM View) -->
                    <div v-else
                        class="glass-panel overflow-hidden rounded-[3rem] border border-slate-200 dark:border-white/5 shadow-2xl shadow-slate-200/50 dark:shadow-none">
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr
                                        class="bg-slate-50 dark:bg-white/5 border-b border-slate-200 dark:border-white/5">
                                        <th
                                            class="px-10 py-8 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                            Alumno Profesional</th>
                                        <th
                                            class="px-10 py-8 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                            Plan de Acceso</th>
                                        <th
                                            class="px-10 py-8 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500">
                                            Última Sesión</th>
                                        <th
                                            class="px-10 py-8 text-[11px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 text-right">
                                            Estrategia</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                                    <tr v-for="student in students" :key="student.id"
                                        class="group hover:bg-primary/[0.03] dark:hover:bg-primary/[0.05] transition-all duration-300">
                                        <td class="px-10 py-8">
                                            <div class="flex items-center gap-5">
                                                <div
                                                    class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-primary-dark to-slate-800 dark:from-primary dark:to-aqua-300 flex items-center justify-center text-white dark:text-primary-dark font-black text-2xl shadow-lg group-hover:scale-110 transition-transform duration-500">
                                                    {{ student.name.charAt(0) }}
                                                </div>
                                                <div>
                                                    <p
                                                        class="text-primary-dark dark:text-white font-bold text-xl font-display">
                                                        {{ student.name }}</p>
                                                    <p class="text-slate-400 dark:text-slate-500 text-xs font-medium">{{
                                                        student.email }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-10 py-8">
                                            <span
                                                class="inline-flex items-center px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-primary/10 text-primary border border-primary/20">
                                                {{ student.subscription_status || 'Premium' }}
                                            </span>
                                        </td>
                                        <td class="px-10 py-8">
                                            <p
                                                class="text-slate-500 dark:text-slate-400 text-sm font-medium flex items-center gap-2">
                                                <span class="material-icons text-primary text-base">monitoring</span>
                                                {{ student.last_activity || 'Hoy, 10:45 AM' }}
                                            </p>
                                        </td>
                                        <td class="px-10 py-8 text-right">
                                            <div
                                                class="flex items-center justify-end gap-3 translate-x-4 opacity-0 group-hover:translate-x-0 group-hover:opacity-100 transition-all duration-500">
                                                <!-- Open Dialog Button -->
                                                <button @click="openAssignModal(student)"
                                                    class="px-6 py-3 rounded-2xl bg-primary-dark dark:bg-primary text-white dark:text-primary-dark font-black text-[11px] uppercase tracking-widest hover:scale-105 active:scale-95 transition-all shadow-lg shadow-primary/20">
                                                    Asignar Rutina
                                                </button>

                                                <!-- Contact Options -->
                                                <div class="flex gap-2">
                                                    <button @click="openWhatsApp(student.phone || '56912345678')"
                                                        class="w-12 h-12 rounded-2xl bg-green-500/10 text-green-500 hover:bg-green-500 hover:text-white transition-all flex items-center justify-center border border-green-500/20">
                                                        <svg viewBox="0 0 24 24" class="w-6 h-6 fill-current">
                                                            <path
                                                                d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z" />
                                                        </svg>
                                                    </button>
                                                    <button @click="openMail(student.email)"
                                                        class="w-12 h-12 rounded-2xl bg-aqua-500/10 text-aqua-600 hover:bg-aqua-500 hover:text-white transition-all flex items-center justify-center border border-aqua-500/20">
                                                        <span class="material-icons text-xl">alternate_email</span>
                                                    </button>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </main>

                <!-- Assign Routine Modal -->
                <div v-if="showAssignModal"
                    class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-primary-dark/40 dark:bg-slate-950/80 backdrop-blur-xl">
                    <div
                        class="max-w-md w-full glass-modal rounded-[3rem] overflow-hidden border border-slate-200 dark:border-white/10 shadow-2xl p-10 transform transition-all animate-in fade-in zoom-in duration-500">
                        <div class="flex justify-between items-start mb-8">
                            <div>
                                <h2
                                    class="text-3xl font-display font-black text-primary-dark dark:text-white uppercase tracking-tight italic">
                                    Nueva <span class="text-primary not-italic">Misión</span>
                                </h2>
                                <p class="text-slate-500 dark:text-slate-400 text-sm mt-2 font-medium">
                                    Planificando entrenamiento para <span
                                        class="text-primary-dark dark:text-white font-bold">{{
                                            selectedStudent?.name }}</span>
                                </p>
                            </div>
                            <button @click="closeAssignModal"
                                class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-white/5 text-slate-400 hover:text-primary-dark dark:hover:text-white transition-colors">
                                <span class="material-icons">close</span>
                            </button>
                        </div>

                        <div v-if="assignmentSuccess" class="py-12 text-center">
                            <div
                                class="w-24 h-24 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6">
                                <span class="material-icons text-primary text-6xl animate-bounce">check_circle</span>
                            </div>
                            <p
                                class="text-primary-dark dark:text-white font-black text-2xl font-display italic uppercase">
                                Objetivo Asignado</p>
                            <p class="text-slate-500 dark:text-slate-400 mt-2">La rutina se ha vinculado exitosamente.
                            </p>
                        </div>

                        <div v-else class="space-y-8">
                            <div>
                                <label
                                    class="block text-[10px] font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 mb-4">Seleccionar
                                    Arsenal de Rutina</label>
                                <select v-model="selectedRoutineId"
                                    class="w-full bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 rounded-2xl px-6 py-5 text-primary-dark dark:text-white font-bold focus:outline-none focus:ring-2 focus:ring-primary/50 appearance-none transition-all">
                                    <option value="" disabled>Explorar rutinas disponibles...</option>
                                    <option v-for="routine in routines" :key="routine.id" :value="routine.id"
                                        class="bg-white dark:bg-slate-900 capitalize py-3">
                                        {{ routine.name }} ({{ routine.difficulty }})
                                    </option>
                                </select>
                                <p v-if="routines.length === 0"
                                    class="text-xs text-amber-500 mt-3 flex items-center gap-2 font-bold px-2">
                                    <span class="material-icons text-sm">precision_manufacturing</span>
                                    El arsenal de rutinas está vacío.
                                </p>
                            </div>

                            <div class="flex gap-4">
                                <button @click="closeAssignModal"
                                    class="flex-1 py-5 bg-slate-100 dark:bg-white/5 hover:bg-slate-200 dark:hover:bg-white/10 text-primary-dark dark:text-white font-black rounded-2xl transition-all uppercase tracking-widest text-[11px]">
                                    Descartar
                                </button>
                                <button @click="assignRoutine" :disabled="!selectedRoutineId || assigning"
                                    class="flex-1 py-5 bg-primary hover:bg-primary/90 text-primary-dark font-black rounded-2xl shadow-xl shadow-primary/20 transition-all disabled:opacity-30 disabled:cursor-not-allowed uppercase tracking-widest text-[11px]">
                                    <span v-if="assigning" class="flex items-center justify-center gap-2">
                                        <span
                                            class="w-4 h-4 border-2 border-primary-dark/30 border-t-primary-dark rounded-full animate-spin"></span>
                                        Vinculando...
                                    </span>
                                    <span v-else>Asignar ahora</span>
                                </button>
                            </div>

                            <button
                                class="w-full py-4 text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] hover:text-primary transition-colors border-t border-slate-100 dark:border-white/5 pt-6">
                                + Nueva Construcción Custom
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

.glass-modal {
    background: rgba(250, 251, 252, 0.95);
    backdrop-filter: blur(40px);
    -webkit-backdrop-filter: blur(40px);
}

.dark .glass-modal {
    background: rgba(15, 23, 42, 0.9);
}

.font-display {
    font-family: 'Outfit', sans-serif;
}

/* Custom Scrollbar */
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
