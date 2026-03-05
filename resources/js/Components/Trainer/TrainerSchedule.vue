<script setup>
import { ref, computed, onMounted } from 'vue';
import Sidebar from '@/Components/Sidebar.vue';
import Topbar from '@/Components/Topbar.vue';
import api from '@/services/api.js';

const props = defineProps({ userName: String });

// ── State ────────────────────────────────────────────────────────────────────
const classes     = ref([]);
const loading     = ref(true);
const saving      = ref(false);
const error       = ref(null);
const showModal   = ref(false);
const editingClass = ref(null);

const form = ref({
    title:            '',
    description:      '',
    scheduled_at:     '',
    duration_minutes: 60,
    max_students:     null,
});

// ── Computed ─────────────────────────────────────────────────────────────────
const upcoming = computed(() =>
    classes.value.filter(c => new Date(c.scheduled_at) >= new Date() && c.status !== 'cancelled')
        .sort((a, b) => new Date(a.scheduled_at) - new Date(b.scheduled_at))
);

const past = computed(() =>
    classes.value.filter(c => new Date(c.scheduled_at) < new Date() || c.status === 'ended')
        .sort((a, b) => new Date(b.scheduled_at) - new Date(a.scheduled_at))
        .slice(0, 10)
);

// ── Methods ───────────────────────────────────────────────────────────────────
async function fetchClasses() {
    loading.value = true;
    try {
        const { data } = await api.get('/trainer/schedule');
        classes.value = data;
    } catch (e) {
        error.value = 'No se pudieron cargar las clases.';
    } finally {
        loading.value = false;
    }
}

function openCreate() {
    editingClass.value = null;
    form.value = { title: '', description: '', scheduled_at: '', duration_minutes: 60, max_students: null };
    showModal.value = true;
}

function openEdit(cls) {
    editingClass.value = cls;
    form.value = {
        title:            cls.title,
        description:      cls.description ?? '',
        scheduled_at:     cls.scheduled_at.slice(0, 16),
        duration_minutes: cls.duration_minutes,
        max_students:     cls.max_students,
    };
    showModal.value = true;
}

async function saveClass() {
    saving.value = true;
    error.value  = null;
    try {
        if (editingClass.value) {
            const { data } = await api.patch(`/trainer/schedule/${editingClass.value.id}`, form.value);
            const idx = classes.value.findIndex(c => c.id === data.id);
            if (idx >= 0) classes.value[idx] = data;
        } else {
            const { data } = await api.post('/trainer/schedule', form.value);
            classes.value.push(data);
        }
        showModal.value = false;
    } catch (e) {
        const msg = e.response?.data?.errors
            ? Object.values(e.response.data.errors).flat().join(' ')
            : (e.response?.data?.message ?? 'Error al guardar.');
        error.value = msg;
    } finally {
        saving.value = false;
    }
}

async function cancelClass(cls) {
    if (!confirm(`¿Cancelar la clase "${cls.title}"?`)) return;
    try {
        await api.delete(`/trainer/schedule/${cls.id}`);
        const idx = classes.value.findIndex(c => c.id === cls.id);
        if (idx >= 0) classes.value[idx].status = 'cancelled';
    } catch {
        alert('No se pudo cancelar la clase.');
    }
}

function formatDate(dt) {
    return new Date(dt).toLocaleString('es-CL', {
        weekday: 'short', day: 'numeric', month: 'short',
        hour: '2-digit', minute: '2-digit'
    });
}

function statusColor(status) {
    return {
        scheduled: 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-300',
        live:      'bg-red-100 dark:bg-red-900/30 text-red-700 dark:text-red-300',
        ended:     'bg-slate-100 dark:bg-white/5 text-slate-500',
        cancelled: 'bg-yellow-100 dark:bg-yellow-900/20 text-yellow-700 dark:text-yellow-300',
    }[status] ?? '';
}

function statusLabel(status) {
    return { scheduled: 'Programada', live: 'En Vivo', ended: 'Finalizada', cancelled: 'Cancelada' }[status] ?? status;
}

// ── Minimum datetime for the input (now + 5 min) ─────────────────────────────
const minDateTime = computed(() => {
    const d = new Date(Date.now() + 5 * 60 * 1000);
    return d.toISOString().slice(0, 16);
});

onMounted(fetchClasses);
</script>

<template>
    <div class="flex min-h-screen bg-background-light dark:bg-background-dark">
        <Sidebar />

        <div class="flex-1 ml-72 flex flex-col min-h-screen">
            <Topbar :user-name="props.userName" />

            <main class="flex-1 p-8 space-y-8">

                <!-- Header -->
                <div class="flex items-center justify-between">
                    <div>
                        <h1 class="text-3xl font-black text-slate-800 dark:text-white tracking-tight">
                            Agendamiento de Clases
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm">
                            Programa tus clases en vivo y gestiona el calendario
                        </p>
                    </div>
                    <button @click="openCreate"
                        class="flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-6 py-3 rounded-2xl font-bold text-sm transition-all shadow-lg shadow-primary/25 active:scale-95">
                        <span class="material-icons text-lg">add</span>
                        Nueva Clase
                    </button>
                </div>

                <!-- Error banner -->
                <div v-if="error && !showModal"
                    class="p-4 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-2xl text-red-700 dark:text-red-300 text-sm flex items-center gap-2">
                    <span class="material-icons text-base">error_outline</span>
                    {{ error }}
                </div>

                <!-- Loading -->
                <div v-if="loading" class="flex justify-center py-20">
                    <div class="w-10 h-10 border-4 border-primary border-t-transparent rounded-full animate-spin"></div>
                </div>

                <template v-else>
                    <!-- ── Upcoming classes ───────────────────────────── -->
                    <section class="space-y-4">
                        <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 px-1">
                            Próximas Clases
                        </h2>

                        <div v-if="upcoming.length === 0"
                            class="p-12 bg-white dark:bg-slate-800/60 rounded-3xl border border-slate-100 dark:border-white/5 text-center">
                            <span class="material-icons text-5xl text-slate-300 dark:text-slate-600 block mb-3">event_available</span>
                            <p class="text-slate-500 dark:text-slate-400 text-sm font-medium">
                                No tienes clases programadas.<br>
                                ¡Agrega una con el botón <strong>Nueva Clase</strong>!
                            </p>
                        </div>

                        <div v-for="cls in upcoming" :key="cls.id"
                            class="flex items-center gap-5 p-5 bg-white dark:bg-slate-800/60 rounded-3xl border border-slate-100 dark:border-white/5 hover:shadow-md transition-shadow">
                            <!-- Icon -->
                            <div class="w-14 h-14 bg-primary/10 dark:bg-primary/5 rounded-2xl flex items-center justify-center flex-shrink-0">
                                <span class="material-icons text-primary text-2xl">event</span>
                            </div>

                            <!-- Info -->
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-slate-800 dark:text-white truncate">{{ cls.title }}</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                                    {{ formatDate(cls.scheduled_at) }} &bull; {{ cls.duration_minutes }} min
                                    <span v-if="cls.max_students"> &bull; Máx {{ cls.max_students }} alumnos</span>
                                </p>
                                <p v-if="cls.description" class="text-xs text-slate-400 dark:text-slate-500 mt-1 truncate">
                                    {{ cls.description }}
                                </p>
                            </div>

                            <!-- Status badge -->
                            <span :class="statusColor(cls.status)"
                                class="text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full flex-shrink-0">
                                {{ statusLabel(cls.status) }}
                            </span>

                            <!-- Actions -->
                            <div class="flex gap-2 flex-shrink-0">
                                <button @click="openEdit(cls)"
                                    class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-white/5 hover:bg-primary/10 dark:hover:bg-primary/10 text-slate-500 hover:text-primary transition-colors">
                                    <span class="material-icons text-base">edit</span>
                                </button>
                                <button @click="cancelClass(cls)"
                                    class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-white/5 hover:bg-red-50 dark:hover:bg-red-900/20 text-slate-500 hover:text-red-500 transition-colors">
                                    <span class="material-icons text-base">close</span>
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- ── Past classes ───────────────────────────────── -->
                    <section v-if="past.length > 0" class="space-y-4">
                        <h2 class="text-xs font-black uppercase tracking-[0.2em] text-slate-400 dark:text-slate-500 px-1">
                            Historial Reciente
                        </h2>
                        <div v-for="cls in past" :key="cls.id"
                            class="flex items-center gap-5 p-4 bg-white/50 dark:bg-slate-800/30 rounded-2xl border border-slate-100/50 dark:border-white/5 opacity-60 hover:opacity-80 transition-opacity">
                            <div class="w-10 h-10 bg-slate-100 dark:bg-white/5 rounded-xl flex items-center justify-center flex-shrink-0">
                                <span class="material-icons text-slate-400 text-lg">history</span>
                            </div>
                            <div class="flex-1 min-w-0">
                                <p class="font-bold text-slate-700 dark:text-slate-300 text-sm truncate">{{ cls.title }}</p>
                                <p class="text-xs text-slate-400 mt-0.5">{{ formatDate(cls.scheduled_at) }}</p>
                            </div>
                            <span :class="statusColor(cls.status)"
                                class="text-[10px] font-black uppercase tracking-widest px-3 py-1 rounded-full flex-shrink-0">
                                {{ statusLabel(cls.status) }}
                            </span>
                        </div>
                    </section>
                </template>
            </main>
        </div>

        <!-- ── Create / Edit Modal ────────────────────────────────────────── -->
        <Teleport to="body">
            <Transition name="fade">
                <div v-if="showModal"
                    class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
                    @click.self="showModal = false">
                    <div class="w-full max-w-lg bg-white dark:bg-slate-900 rounded-3xl shadow-2xl p-8 space-y-6">

                        <!-- Title -->
                        <div class="flex items-center justify-between">
                            <h2 class="text-xl font-black text-slate-800 dark:text-white">
                                {{ editingClass ? 'Editar Clase' : 'Nueva Clase en Vivo' }}
                            </h2>
                            <button @click="showModal = false"
                                class="w-9 h-9 flex items-center justify-center rounded-xl bg-slate-100 dark:bg-white/5 hover:bg-slate-200 text-slate-500 transition-colors">
                                <span class="material-icons text-base">close</span>
                            </button>
                        </div>

                        <!-- Error inside modal -->
                        <div v-if="error"
                            class="p-3 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl text-red-700 dark:text-red-300 text-xs">
                            {{ error }}
                        </div>

                        <!-- Form -->
                        <form @submit.prevent="saveClass" class="space-y-5">
                            <!-- Title -->
                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">
                                    Título *
                                </label>
                                <input v-model="form.title" type="text" required maxlength="255"
                                    placeholder="Ej: Yoga Matutino — Principiantes"
                                    class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary/40" />
                            </div>

                            <!-- Date + Duration (2 cols) -->
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">
                                        Fecha y Hora *
                                    </label>
                                    <input v-model="form.scheduled_at" type="datetime-local" required
                                        :min="minDateTime"
                                        class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary/40" />
                                </div>
                                <div>
                                    <label class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">
                                        Duración (min)
                                    </label>
                                    <input v-model.number="form.duration_minutes" type="number" min="15" max="480"
                                        class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary/40" />
                                </div>
                            </div>

                            <!-- Max students -->
                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">
                                    Máx. Alumnos <span class="text-slate-400 normal-case font-medium">(opcional — sin límite si vacío)</span>
                                </label>
                                <input v-model.number="form.max_students" type="number" min="1" max="500"
                                    placeholder="Sin límite"
                                    class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary/40" />
                            </div>

                            <!-- Description -->
                            <div>
                                <label class="block text-xs font-black uppercase tracking-widest text-slate-500 mb-2">
                                    Descripción <span class="text-slate-400 normal-case font-medium">(opcional)</span>
                                </label>
                                <textarea v-model="form.description" rows="3" maxlength="1000"
                                    placeholder="Qué van a trabajar, nivel requerido, equipo necesario..."
                                    class="w-full px-4 py-3 rounded-xl bg-slate-50 dark:bg-white/5 border border-slate-200 dark:border-white/10 text-slate-800 dark:text-white text-sm focus:outline-none focus:ring-2 focus:ring-primary/40 resize-none"></textarea>
                            </div>

                            <!-- Actions -->
                            <div class="flex gap-3 pt-2">
                                <button type="button" @click="showModal = false"
                                    class="flex-1 py-3 rounded-xl bg-slate-100 dark:bg-white/5 text-slate-600 dark:text-slate-300 font-bold text-sm hover:bg-slate-200 dark:hover:bg-white/10 transition-colors">
                                    Cancelar
                                </button>
                                <button type="submit" :disabled="saving"
                                    class="flex-1 py-3 rounded-xl bg-primary text-white font-bold text-sm hover:bg-primary/90 transition-all shadow-lg shadow-primary/25 disabled:opacity-50 flex items-center justify-center gap-2">
                                    <span v-if="saving" class="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></span>
                                    {{ editingClass ? 'Guardar Cambios' : 'Crear Clase' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </Transition>
        </Teleport>
    </div>
</template>

<style scoped>
.fade-enter-active, .fade-leave-active { transition: opacity 0.2s ease; }
.fade-enter-from, .fade-leave-to { opacity: 0; }
</style>
