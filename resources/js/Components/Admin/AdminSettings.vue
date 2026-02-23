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

const activeTab = ref('general'); // 'general' or 'categories'

// ========================
// GLOBAL SETTINGS STATE
// ========================
const settings = ref({
    site_name: '4Trainner App',
    support_email: '',
    maintenance_mode: false,
    public_registration_enabled: true,
});

const settingsLoading = ref(true);
const settingsSaving = ref(false);
const settingsMessage = ref(null);

const fetchSettings = async () => {
    settingsLoading.value = true;
    try {
        const response = await axios.get('/admin/settings/data');
        if (Object.keys(response.data).length > 0) {
            settings.value = { ...settings.value, ...response.data };
        }
    } catch (e) {
        console.error("Error fetching settings", e);
    } finally {
        settingsLoading.value = false;
    }
};

const saveSettings = async () => {
    settingsSaving.value = true;
    settingsMessage.value = null;
    try {
        await axios.post('/admin/settings/batch', settings.value);
        settingsMessage.value = { type: 'success', text: 'Configuraciones guardadas exitosamente.' };
        setTimeout(() => settingsMessage.value = null, 3000);
    } catch (e) {
        console.error("Error saving settings", e);
        settingsMessage.value = { type: 'error', text: 'Error al intentar guardar las configuraciones.' };
    } finally {
        settingsSaving.value = false;
    }
};

// ========================
// CATEGORIES STATE
// ========================
const categories = ref([]);
const categoriesLoading = ref(true);

const catModalOpen = ref(false);
const catMode = ref('create'); // 'create', 'edit'
const catForm = ref({ id: null, name: '', parent_id: '', order: 0 });

const fetchCategories = async () => {
    categoriesLoading.value = true;
    try {
        const response = await axios.get('/admin/categories');
        categories.value = response.data; // Now the controller returns array directly
    } catch (e) {
        console.error("Error fetching categories", e);
    } finally {
        categoriesLoading.value = false;
    }
};

const openCatModal = (mode = 'create', cat = null) => {
    catMode.value = mode;
    if (mode === 'edit' && cat) {
        catForm.value = { id: cat.id, name: cat.name, parent_id: cat.parent_id || '', order: cat.order || 0 };
    } else {
        catForm.value = { id: null, name: '', parent_id: '', order: 0 };
    }
    catModalOpen.value = true;
};

const saveCategory = async () => {
    try {
        // nullify empty parent_id
        const payload = { ...catForm.value };
        if (payload.parent_id === '') payload.parent_id = null;

        if (catMode.value === 'create') {
            await axios.post('/admin/categories', payload);
        } else {
            await axios.put(`/admin/categories/${payload.id}`, payload);
        }
        await fetchCategories();
        catModalOpen.value = false;
    } catch (e) {
        console.error("Error saving category", e);
        alert(e.response?.data?.message || 'Error al guardar la categoría. Asegúrese de que el nombre sea único.');
    }
};

const deleteCategory = async (id, name) => {
    if (confirm(`¿Estás seguro de eliminar la categoría "${name}"?`)) {
        try {
            await axios.delete(`/admin/categories/${id}`);
            await fetchCategories();
        } catch (e) {
            console.error("Error deleting category", e);
            alert("Error al eliminar la categoría.");
        }
    }
};


// ========================
// START UP
// ========================
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

    fetchSettings();
    fetchCategories();
});
</script>

<template>
    <div
        class="min-h-screen bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-200 font-sans transition-colors duration-500 relative flex overflow-x-hidden">
        <Sidebar :is-dark="isDark" @toggle-dark="toggleDarkMode" />

        <div class="ml-72 flex-1 flex flex-col min-h-screen relative">
            <Topbar :is-dark="isDark" @toggle-dark="toggleDarkMode" />

            <main
                class="mt-24 p-4 md:p-8 lg:p-12 flex-1 relative z-10 w-full max-w-7xl mx-auto flex flex-col lg:flex-row gap-8 items-start">

                <!-- Left Tabs Menu -->
                <div class="w-full lg:w-64 shrink-0 space-y-2">
                    <h1
                        class="text-3xl font-display font-black text-primary-dark dark:text-white uppercase tracking-tight italic mb-8">
                        Config. <span class="text-aqua-400 not-italic">Sistema</span>
                    </h1>

                    <nav class="flex flex-col gap-2">
                        <button @click="activeTab = 'general'"
                            class="flex items-center gap-4 px-6 py-4 rounded-2xl font-bold uppercase tracking-widest text-xs transition-all text-left"
                            :class="activeTab === 'general' ? 'bg-primary text-primary-dark shadow-lg shadow-primary/20 scale-105 z-10' : 'bg-white dark:bg-slate-800/50 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800'">
                            <span class="material-icons">settings</span>
                            Generales
                        </button>
                        <button @click="activeTab = 'categories'"
                            class="flex items-center gap-4 px-6 py-4 rounded-2xl font-bold uppercase tracking-widest text-xs transition-all text-left"
                            :class="activeTab === 'categories' ? 'bg-primary text-primary-dark shadow-lg shadow-primary/20 scale-105 z-10' : 'bg-white dark:bg-slate-800/50 text-slate-500 hover:bg-slate-50 dark:hover:bg-slate-800'">
                            <span class="material-icons">category</span>
                            Categorías
                        </button>
                    </nav>
                </div>

                <!-- Right Content Area -->
                <div
                    class="flex-1 w-full glass-panel rounded-[2.5rem] border border-slate-200 dark:border-white/5 shadow-xl shadow-slate-200/50 dark:shadow-none overflow-hidden relative min-h-[500px]">

                    <!-- TAB: GENERAL SETTINGS -->
                    <div v-show="activeTab === 'general'" class="animate-in fade-in slide-in-from-right-4 duration-500">
                        <div
                            class="px-10 py-8 border-b border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-white/5">
                            <h2
                                class="text-xl font-display font-black text-primary-dark dark:text-white uppercase tracking-tight italic">
                                Variables <span class="text-primary not-italic">Globales</span>
                            </h2>
                            <p class="text-xs text-slate-500 font-medium mt-1">Modifica los ajustes base de
                                comportamiento de la plataforma.</p>
                        </div>

                        <div v-if="settingsLoading" class="p-20 flex justify-center">
                            <div
                                class="w-10 h-10 border-4 border-primary/20 border-t-primary rounded-full animate-spin">
                            </div>
                        </div>

                        <form v-else @submit.prevent="saveSettings" class="p-10 space-y-8">

                            <!-- Success/Error MSG -->
                            <transition enter-active-class="transition-opacity duration-300"
                                leave-active-class="transition-opacity duration-300" enter-from-class="opacity-0"
                                leave-to-class="opacity-0">
                                <div v-if="settingsMessage"
                                    class="p-4 rounded-2xl flex items-center gap-3 border text-sm font-bold"
                                    :class="settingsMessage.type === 'success' ? 'bg-green-500/10 text-green-600 border-green-500/20' : 'bg-red-500/10 text-red-600 border-red-500/20'">
                                    <span class="material-icons">{{ settingsMessage.type === 'success' ? 'check_circle'
                                        : 'error' }}</span>
                                    {{ settingsMessage.text }}
                                </div>
                            </transition>

                            <div class="grid md:grid-cols-2 gap-8">
                                <div class="space-y-2">
                                    <label
                                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Nombre
                                        del Sitio (Brand)</label>
                                    <input type="text" v-model="settings.site_name"
                                        class="w-full bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 rounded-2xl px-5 py-3 text-slate-800 dark:text-white font-bold focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                                </div>

                                <div class="space-y-2">
                                    <label
                                        class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Email
                                        de Soporte Contacto</label>
                                    <input type="email" v-model="settings.support_email"
                                        class="w-full bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 rounded-2xl px-5 py-3 text-slate-800 dark:text-white font-bold focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all">
                                </div>
                            </div>

                            <div class="space-y-4 pt-4 border-t border-slate-100 dark:border-white/5">
                                <h3 class="text-sm font-black text-slate-800 dark:text-white uppercase tracking-widest">
                                    Características / Toggles</h3>

                                <!-- Toggle 1 -->
                                <div
                                    class="flex items-center justify-between p-4 rounded-2xl bg-slate-50 dark:bg-black/20 border border-slate-100 dark:border-white/5">
                                    <div>
                                        <p class="font-bold text-sm text-slate-800 dark:text-white">Registro Público</p>
                                        <p class="text-xs text-slate-500">Permite a los usuarios crear cuentas
                                            libremente la web.</p>
                                    </div>
                                    <button type="button"
                                        @click="settings.public_registration_enabled = !settings.public_registration_enabled"
                                        class="relative w-12 h-6 rounded-full transition-colors duration-300 focus:outline-none shrink-0"
                                        :class="settings.public_registration_enabled ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600'">
                                        <div class="absolute top-1 left-1 bg-white w-4 h-4 rounded-full transition-transform duration-300"
                                            :class="settings.public_registration_enabled ? 'translate-x-6' : 'translate-x-0'">
                                        </div>
                                    </button>
                                </div>

                                <!-- Toggle 2 -->
                                <div
                                    class="flex items-center justify-between p-4 rounded-2xl bg-amber-50 dark:bg-amber-900/10 border border-amber-100 dark:border-amber-500/20">
                                    <div>
                                        <p class="font-bold text-sm text-amber-800 dark:text-amber-500">Modo
                                            Mantenimiento</p>
                                        <p class="text-xs text-amber-600/70 dark:text-amber-500/70">Desactiva el sitio
                                            temporalmente. Solo Super-Admin puede acceder.</p>
                                    </div>
                                    <button type="button"
                                        @click="settings.maintenance_mode = !settings.maintenance_mode"
                                        class="relative w-12 h-6 rounded-full transition-colors duration-300 focus:outline-none shrink-0"
                                        :class="settings.maintenance_mode ? 'bg-amber-500' : 'bg-slate-300 dark:bg-slate-600'">
                                        <div class="absolute top-1 left-1 bg-white w-4 h-4 rounded-full transition-transform duration-300"
                                            :class="settings.maintenance_mode ? 'translate-x-6' : 'translate-x-0'">
                                        </div>
                                    </button>
                                </div>
                            </div>

                            <div class="flex justify-end pt-6 border-t border-slate-100 dark:border-white/5">
                                <button type="submit" :disabled="settingsSaving"
                                    class="bg-primary hover:bg-primary-dark text-primary-dark font-black uppercase tracking-widest text-xs py-3 px-8 rounded-2xl shadow-lg transition-all disabled:opacity-50 flex items-center gap-2">
                                    <span v-if="settingsSaving" class="material-icons animate-spin text-sm">sync</span>
                                    Guardar Ajustes
                                </button>
                            </div>
                        </form>
                    </div>

                    <!-- TAB: CATEGORIES -->
                    <div v-show="activeTab === 'categories'"
                        class="animate-in fade-in slide-in-from-right-4 duration-500 flex flex-col h-full min-h-[600px]">
                        <div
                            class="px-10 py-8 border-b border-slate-100 dark:border-white/5 bg-slate-50/50 dark:bg-white/5 flex items-center justify-between">
                            <div>
                                <h2
                                    class="text-xl font-display font-black text-primary-dark dark:text-white uppercase tracking-tight italic">
                                    Jerarquía de <span class="text-aqua-400 not-italic">Categorías</span>
                                </h2>
                                <p class="text-xs text-slate-500 font-medium mt-1">Organiza el sistema de ejercicios,
                                    rutinas y biblioteca general.</p>
                            </div>
                            <button @click="openCatModal('create')"
                                class="bg-aqua-400 hover:bg-aqua-500 text-slate-900 font-black uppercase tracking-widest text-xs py-3 px-6 rounded-2xl shadow-lg transition-all flex items-center gap-2">
                                <span class="material-icons text-sm">add</span> Nva. Categoría
                            </button>
                        </div>

                        <div v-if="categoriesLoading" class="flex-1 flex items-center justify-center p-20">
                            <div
                                class="w-10 h-10 border-4 border-aqua-400/20 border-t-aqua-400 rounded-full animate-spin">
                            </div>
                        </div>

                        <div v-else-if="categories.length === 0"
                            class="flex-1 flex flex-col items-center justify-center py-20">
                            <span
                                class="material-icons text-slate-300 dark:text-slate-700 text-6xl mb-4">folder_off</span>
                            <p class="text-slate-500 font-bold uppercase tracking-widest text-sm">No existen categorías
                                base</p>
                        </div>

                        <!-- Categories Tree List -->
                        <div v-else class="flex-1 overflow-y-auto p-4 md:p-8 space-y-4">
                            <div v-for="cat in categories" :key="cat.id" class="space-y-2">
                                <!-- Parent Item -->
                                <div
                                    class="flex items-center gap-4 p-4 rounded-2xl bg-white dark:bg-slate-800/80 border border-slate-200 dark:border-white/10 group hover:border-aqua-400/50 transition-colors shadow-sm">
                                    <div
                                        class="w-10 h-10 bg-aqua-400/10 text-aqua-400 rounded-xl flex items-center justify-center">
                                        <span class="material-icons">folder</span>
                                    </div>
                                    <div class="flex-1">
                                        <p class="font-bold text-slate-800 dark:text-white text-lg leading-tight">{{
                                            cat.name }}</p>
                                        <p class="text-[10px] text-slate-400 uppercase tracking-widest">/{{ cat.slug }}
                                        </p>
                                    </div>
                                    <div
                                        class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <!-- Actions -->
                                        <button @click="openCatModal('edit', cat)"
                                            class="w-8 h-8 rounded-lg bg-blue-500/10 text-blue-500 hover:bg-blue-500 hover:text-white flex items-center justify-center"><span
                                                class="material-icons text-sm">edit</span></button>
                                        <button @click="deleteCategory(cat.id, cat.name)"
                                            class="w-8 h-8 rounded-lg bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center"><span
                                                class="material-icons text-sm">delete_outline</span></button>
                                    </div>
                                </div>

                                <!-- Children Items -->
                                <div v-if="cat.children && cat.children.length > 0" class="pl-8 md:pl-14 space-y-2">
                                    <div v-for="child in cat.children" :key="child.id"
                                        class="flex items-center gap-4 p-3 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-transparent group hover:border-slate-300 dark:hover:border-white/20 transition-colors">
                                        <span
                                            class="material-icons text-slate-300 dark:text-slate-600">subdirectory_arrow_right</span>
                                        <div class="flex-1">
                                            <p class="font-bold text-slate-700 dark:text-slate-300 text-sm">{{
                                                child.name }}</p>
                                        </div>
                                        <div
                                            class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                            <button @click="openCatModal('edit', child)"
                                                class="w-7 h-7 rounded-md bg-blue-500/10 text-blue-500 hover:bg-blue-500 hover:text-white flex items-center justify-center"><span
                                                    class="material-icons text-[14px]">edit</span></button>
                                            <button @click="deleteCategory(child.id, child.name)"
                                                class="w-7 h-7 rounded-md bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white flex items-center justify-center"><span
                                                    class="material-icons text-[14px]">delete_outline</span></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>

        <!-- CATEGORY MODAL -->
        <transition enter-active-class="transition-opacity duration-300"
            leave-active-class="transition-opacity duration-300" enter-from-class="opacity-0"
            leave-to-class="opacity-0">
            <div v-if="catModalOpen"
                class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm">
                <div class="bg-white dark:bg-slate-800 rounded-[2.5rem] w-full max-w-md shadow-2xl border border-slate-200 dark:border-white/10 overflow-hidden transform transition-all duration-300 scale-100"
                    @click.stop>

                    <div
                        class="px-8 py-6 border-b border-slate-100 dark:border-white/5 flex items-center justify-between bg-slate-50/50 dark:bg-white/5">
                        <h2
                            class="text-xl font-display font-black text-primary-dark dark:text-white uppercase italic tracking-tight">
                            {{ catMode === 'create' ? 'Nueva' : 'Editar' }} <span
                                class="text-aqua-400 not-italic">Categoría</span>
                        </h2>
                        <button @click="catModalOpen = false"
                            class="text-slate-400 hover:text-primary transition-colors"><span
                                class="material-icons">close</span></button>
                    </div>

                    <form @submit.prevent="saveCategory" class="p-8 space-y-5">
                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Nombre
                                Modalidad</label>
                            <input type="text" v-model="catForm.name" required
                                class="w-full bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 rounded-2xl px-5 py-3 text-slate-800 dark:text-white font-bold focus:outline-none focus:ring-2 focus:ring-aqua-400/50 focus:border-aqua-400 transition-all">
                        </div>

                        <div class="space-y-2">
                            <label
                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Categoría
                                Padre (Opcional)</label>
                            <select v-model="catForm.parent_id"
                                class="w-full bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 rounded-2xl px-5 py-3 text-slate-800 dark:text-white font-bold focus:outline-none focus:ring-2 focus:ring-aqua-400/50 focus:border-aqua-400 transition-all appearance-none cursor-pointer">
                                <option value="">Ninguna (Nivel Raíz)</option>
                                <option v-for="p in categories" :key="p.id" :value="p.id"
                                    :disabled="p.id === catForm.id">
                                    📁 {{ p.name }}
                                </option>
                            </select>
                        </div>

                        <div class="space-y-2">
                            <label class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Orden de
                                Visualización</label>
                            <input type="number" v-model="catForm.order"
                                class="w-full bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 rounded-2xl px-5 py-3 text-slate-800 dark:text-white font-bold focus:outline-none focus:ring-2 focus:ring-aqua-400/50 focus:border-aqua-400 transition-all">
                        </div>

                        <div class="pt-4 flex justify-end gap-3">
                            <button type="button" @click="catModalOpen = false"
                                class="px-6 py-3 rounded-2xl font-bold uppercase tracking-widest text-xs text-slate-500 hover:text-slate-700 dark:hover:text-white transition-colors">Cancelar</button>
                            <button type="submit"
                                class="bg-aqua-400 hover:bg-aqua-500 text-slate-900 font-black uppercase tracking-widest text-xs py-3 px-6 rounded-2xl shadow-lg transition-all">
                                {{ catMode === 'create' ? 'Añadir Categoría' : 'Actualizar' }}
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
