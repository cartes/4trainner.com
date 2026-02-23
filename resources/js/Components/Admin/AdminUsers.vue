<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '../../stores/auth';
import Sidebar from '../Sidebar.vue';
import Topbar from '../Topbar.vue';
import axios from 'axios';

const props = defineProps({
    authUser: { type: String, default: null }
});

const authStore = useAuthStore();
const isDark = ref(false);
const users = ref([]);
const loading = ref(true);

// Modal state
const isModalOpen = ref(false);
const modalMode = ref('create'); // 'create' or 'edit'
const formLoading = ref(false);
const formError = ref(null);

const formData = ref({
    id: null,
    name: '',
    email: '',
    password: '',
    role: 'alumno',
    phone: ''
});

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

const fetchUsers = async () => {
    loading.value = true;
    try {
        const response = await axios.get('/admin/users/data');
        users.value = response.data.map(u => ({
            ...u,
            // Format roles cleanly
            displayRole: u.roles && u.roles.length > 0 ? u.roles[0] : 'Sin rol'
        }));
    } catch (e) {
        console.error("Error fetching users", e);
    } finally {
        loading.value = false;
    }
};

const openModal = (mode = 'create', user = null) => {
    modalMode.value = mode;
    formError.value = null;
    if (mode === 'edit' && user) {
        formData.value = {
            id: user.id,
            name: user.name,
            email: user.email,
            password: '', // Leave blank for edit unless they want to change it
            role: user.roles && user.roles.length > 0 ? user.roles[0] : 'alumno',
            phone: user.phone || ''
        };
    } else {
        formData.value = { id: null, name: '', email: '', password: '', role: 'alumno', phone: '' };
    }
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
};

const generatePassword = () => {
    const chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*';
    let pass = '';
    for (let i = 0; i < 12; i++) {
        pass += chars.charAt(Math.floor(Math.random() * chars.length));
    }
    formData.value.password = pass;
};

const submitForm = async () => {
    formLoading.value = true;
    formError.value = null;

    try {
        if (modalMode.value === 'create') {
            await axios.post('/admin/users/store', formData.value);
        } else {
            await axios.put(`/admin/users/${formData.value.id}`, formData.value);
        }
        await fetchUsers();
        closeModal();
    } catch (e) {
        console.error(e);
        formError.value = e.response?.data?.message || 'Ocurrió un error al procesar la solicitud.';
    } finally {
        formLoading.value = false;
    }
};

const deleteUser = async (id, name) => {
    if (confirm(`¿Estás seguro de eliminar a ${name}? Esta acción no se puede deshacer.`)) {
        try {
            await axios.delete(`/admin/users/${id}`);
            await fetchUsers();
        } catch (e) {
            console.error(e);
            alert("No se pudo eliminar al usuario.");
        }
    }
};

const getRoleClass = (role) => {
    if (role === 'super-admin') return 'bg-purple-500/10 text-purple-500 border-purple-500/20';
    if (role === 'profesor') return 'bg-aqua-400/10 text-aqua-400 border-aqua-400/20';
    if (role === 'alumno') return 'bg-primary/10 text-primary-dark dark:text-primary border-primary/20';
    return 'bg-slate-500/10 text-slate-500 border-slate-500/20';
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

    // Hydrate Auth Store
    if (props.authUser && !authStore.user) {
        try {
            authStore.user = JSON.parse(props.authUser);
            authStore.isAuthenticated = true;
        } catch (e) { }
    }

    fetchUsers();
});
</script>

<template>
    <div
        class="min-h-screen bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-200 font-sans transition-colors duration-500 relative flex overflow-x-hidden">
        <Sidebar :is-dark="isDark" @toggle-dark="toggleDarkMode" />

        <div class="ml-72 flex-1 flex flex-col min-h-screen relative">
            <Topbar :is-dark="isDark" @toggle-dark="toggleDarkMode" />

            <main class="mt-24 p-4 md:p-8 lg:p-12 flex-1 relative z-10 w-full max-w-7xl mx-auto">

                <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-10">
                    <div>
                        <h1
                            class="text-4xl font-display font-black text-primary-dark dark:text-white uppercase tracking-tight italic">
                            Gestión de <span class="text-aqua-400 not-italic">Usuarios</span>
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium">
                            Administra profesores y alumnos de la plataforma.
                        </p>
                    </div>
                    <button @click="openModal('create')"
                        class="bg-aqua-400 hover:bg-aqua-500 text-slate-900 font-black uppercase tracking-widest text-sm py-4 px-8 rounded-2xl shadow-lg shadow-aqua-400/20 hover:shadow-aqua-400/40 hover:-translate-y-1 transition-all duration-300 flex items-center gap-2">
                        <span class="material-icons">person_add</span>
                        CREAR USUARIO
                    </button>
                </div>

                <!-- Table Container -->
                <div
                    class="glass-panel rounded-[2.5rem] border border-slate-200 dark:border-white/5 shadow-xl shadow-slate-200/50 dark:shadow-none overflow-hidden">

                    <div v-if="loading" class="flex flex-col items-center justify-center py-20 gap-4">
                        <div class="w-12 h-12 border-4 border-aqua-400/20 border-t-aqua-400 rounded-full animate-spin">
                        </div>
                        <p class="text-sm font-bold uppercase tracking-widest text-slate-400 italic">Cargando
                            usuarios...</p>
                    </div>

                    <div v-else-if="users.length === 0" class="text-center py-20">
                        <span class="material-icons text-slate-300 dark:text-slate-700 text-6xl mb-4">group_off</span>
                        <p class="text-slate-500 font-bold uppercase tracking-widest text-sm">No hay usuarios
                            registrados</p>
                    </div>

                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr
                                    class="bg-slate-50/50 dark:bg-white/5 border-b border-slate-200 dark:border-white/10 uppercase tracking-widest text-[10px] font-black text-slate-400">
                                    <th class="px-8 py-5">Usuario</th>
                                    <th class="px-8 py-5">Contacto</th>
                                    <th class="px-8 py-5">Rol</th>
                                    <th class="px-8 py-5 text-right flex justify-end">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100 dark:divide-white/5">
                                <tr v-for="user in users" :key="user.id"
                                    class="group hover:bg-primary/[0.02] dark:hover:bg-primary/[0.05] transition-colors">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-white/10 flex items-center justify-center font-black text-lg text-primary-dark dark:text-white uppercase transition-transform group-hover:scale-110">
                                                {{ user.name.charAt(0) }}
                                            </div>
                                            <div>
                                                <p class="text-primary-dark dark:text-white font-bold font-display">{{
                                                    user.name }}</p>
                                                <p class="text-[10px] text-slate-400 tracking-wider">Creado {{
                                                    user.created_at_human }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex flex-col gap-1">
                                            <span class="text-sm text-slate-600 dark:text-slate-300 font-medium">{{
                                                user.email }}</span>
                                            <span v-if="user.phone" class="text-xs text-slate-400">{{ user.phone
                                            }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <span
                                            class="inline-flex px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border"
                                            :class="getRoleClass(user.displayRole)">
                                            {{ user.displayRole }}
                                        </span>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div
                                            class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button @click="openModal('edit', user)"
                                                class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-500 hover:bg-blue-500 hover:text-white flex items-center justify-center transition-colors">
                                                <span class="material-icons text-sm">edit</span>
                                            </button>
                                            <button @click="deleteUser(user.id, user.name)"
                                                class="w-8 h-8 rounded-lg bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center transition-colors">
                                                <span class="material-icons text-sm">delete_outline</span>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </main>
        </div>

        <!-- Create/Edit Modal Overlay -->
        <transition enter-active-class="transition-opacity duration-300"
            leave-active-class="transition-opacity duration-300" enter-from-class="opacity-0"
            leave-to-class="opacity-0">
            <div v-if="isModalOpen"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">

                <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] w-full max-w-2xl shadow-2xl border border-slate-200 dark:border-white/10 overflow-hidden transform transition-all duration-300 scale-100"
                    @click.stop>

                    <div
                        class="px-10 py-6 border-b border-slate-100 dark:border-white/5 flex items-center justify-between bg-slate-50/50 dark:bg-white/5">
                        <h2
                            class="text-2xl font-display font-black text-primary-dark dark:text-white uppercase italic tracking-tight">
                            {{ modalMode === 'create' ? 'Nuevo' : 'Editar' }} <span
                                class="text-aqua-400 not-italic">Usuario</span>
                        </h2>
                        <button @click="closeModal"
                            class="w-10 h-10 rounded-full hover:bg-slate-200 dark:hover:bg-white/10 flex items-center justify-center text-slate-400 hover:text-primary transition-colors">
                            <span class="material-icons">close</span>
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="p-10 space-y-6">
                        <div v-if="formError"
                            class="p-4 bg-red-500/10 text-red-600 dark:text-red-400 rounded-2xl border border-red-500/20 text-sm font-bold text-center">
                            {{ formError }}
                        </div>

                        <div class="grid md:grid-cols-2 gap-6">
                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Nombre
                                    Completo</label>
                                <input type="text" v-model="formData.name" required
                                    class="w-full bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 rounded-2xl px-5 py-3 text-slate-800 dark:text-white font-bold focus:outline-none focus:ring-2 focus:ring-aqua-400/50 focus:border-aqua-400 transition-all">
                            </div>

                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Correo
                                    Electrónico</label>
                                <input type="email" v-model="formData.email" required
                                    class="w-full bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 rounded-2xl px-5 py-3 text-slate-800 dark:text-white font-bold focus:outline-none focus:ring-2 focus:ring-aqua-400/50 focus:border-aqua-400 transition-all">
                            </div>

                            <div class="space-y-2">
                                <label
                                    class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Teléfono</label>
                                <input type="tel" v-model="formData.phone"
                                    class="w-full bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 rounded-2xl px-5 py-3 text-slate-800 dark:text-white font-bold focus:outline-none focus:ring-2 focus:ring-aqua-400/50 focus:border-aqua-400 transition-all">
                            </div>

                            <div class="space-y-2">
                                <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Rol
                                    del Sistema</label>
                                <select v-model="formData.role" required
                                    class="w-full bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 rounded-2xl px-5 py-3 text-slate-800 dark:text-white font-bold focus:outline-none focus:ring-2 focus:ring-aqua-400/50 focus:border-aqua-400 transition-all appearance-none cursor-pointer">
                                    <option value="alumno">Alumno (Atleta)</option>
                                    <option value="profesor">Personal Trainer</option>
                                    <option value="moderador">Moderador</option>
                                </select>
                            </div>

                            <!-- Contraseña -->
                            <div class="space-y-2 md:col-span-2">
                                <div class="flex items-center justify-between px-2">
                                    <label
                                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest">Contraseña
                                        {{ modalMode === 'edit' ? '(Opcional)' : '' }}</label>
                                    <button type="button" @click="generatePassword"
                                        class="text-[10px] font-black text-aqua-400 uppercase tracking-widest hover:underline">
                                        Generar Automática
                                    </button>
                                </div>
                                <input type="text" v-model="formData.password" :required="modalMode === 'create'"
                                    class="w-full bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 rounded-2xl px-5 py-3 text-slate-800 dark:text-white font-bold focus:outline-none focus:ring-2 focus:ring-aqua-400/50 focus:border-aqua-400 transition-all font-mono tracking-widest">
                            </div>
                        </div>

                        <div class="pt-6 flex justify-end gap-4">
                            <button type="button" @click="closeModal"
                                class="px-8 py-3 rounded-2xl font-bold uppercase tracking-widest text-xs text-slate-500 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-white/10 transition-colors">
                                Cancelar
                            </button>
                            <button type="submit" :disabled="formLoading"
                                class="bg-aqua-400 hover:bg-aqua-500 text-slate-900 font-black uppercase tracking-widest text-xs py-3 px-8 rounded-2xl shadow-lg transition-all disabled:opacity-50 flex items-center gap-2">
                                <span v-if="formLoading" class="material-icons animate-spin text-sm">sync</span>
                                {{ modalMode === 'create' ? 'Crear Usuario' : 'Guardar Cambios' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </transition>
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
