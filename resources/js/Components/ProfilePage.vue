<script setup>
import { ref, onMounted } from 'vue';
import axios from 'axios';
import Sidebar from './Sidebar.vue';
import Topbar from './Topbar.vue';
import { useAuthStore } from '../stores/auth';

const props = defineProps({
    user: { type: [Object, String], required: true },
});

const userObj = ref(typeof props.user === 'string' ? JSON.parse(props.user) : props.user);
const authStore = useAuthStore();
const isDark = ref(false);

// ─── Profile form ──────────────────────────────────────────────────────────
const profileForm = ref({ name: userObj.value.name, email: userObj.value.email });
const profileStatus = ref('');
const profileError = ref('');
const profileSaving = ref(false);

const saveProfile = async () => {
    profileStatus.value = '';
    profileError.value = '';
    profileSaving.value = true;
    try {
        await axios.patch('/profile', profileForm.value, {
            withCredentials: true,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        });
        profileStatus.value = '¡Perfil actualizado correctamente!';
    } catch (err) {
        profileError.value = err.response?.data?.message
            ?? Object.values(err.response?.data?.errors ?? {})[0]?.[0]
            ?? 'Error al guardar.';
    } finally {
        profileSaving.value = false;
    }
};

// ─── Password form ─────────────────────────────────────────────────────────
const passwordForm = ref({ current_password: '', password: '', password_confirmation: '' });
const passwordStatus = ref('');
const passwordError = ref('');
const passwordSaving = ref(false);

const savePassword = async () => {
    passwordStatus.value = '';
    passwordError.value = '';
    passwordSaving.value = true;
    try {
        await axios.put('/password', passwordForm.value, {
            withCredentials: true,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        });
        passwordStatus.value = '¡Contraseña actualizada!';
        passwordForm.value = { current_password: '', password: '', password_confirmation: '' };
    } catch (err) {
        passwordError.value = err.response?.data?.message
            ?? Object.values(err.response?.data?.errors ?? {})[0]?.[0]
            ?? 'Error al actualizar la contraseña.';
    } finally {
        passwordSaving.value = false;
    }
};

// ─── Delete account ────────────────────────────────────────────────────────
const deleteForm = ref({ password: '' });
const showDeleteConfirm = ref(false);
const deleteError = ref('');
const deleting = ref(false);

const deleteAccount = async () => {
    deleteError.value = '';
    deleting.value = true;
    try {
        await axios.delete('/profile', {
            data: { password: deleteForm.value.password },
            withCredentials: true,
            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' },
        });
        authStore.clearAuth();
        window.location.href = '/';
    } catch (err) {
        deleteError.value = err.response?.data?.message
            ?? Object.values(err.response?.data?.errors ?? {})[0]?.[0]
            ?? 'Error al eliminar la cuenta.';
    } finally {
        deleting.value = false;
    }
};

const toggleDarkMode = () => {
    isDark.value = !isDark.value;
    document.documentElement.classList.toggle('dark', isDark.value);
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
};

onMounted(() => {
    if (userObj.value && !authStore.user) {
        authStore.user = userObj.value;
        authStore.isAuthenticated = true;
    }
    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    }
});
</script>

<template>
    <div class="min-h-screen bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-200 font-sans transition-colors duration-500 flex overflow-x-hidden">

        <Sidebar :is-dark="isDark" @toggle-dark="toggleDarkMode" />

        <div class="ml-72 flex-1 flex flex-col min-h-screen">
            <Topbar :is-dark="isDark" @toggle-dark="toggleDarkMode" />

            <main class="mt-24 p-8 lg:p-12 max-w-3xl w-full mx-auto flex flex-col gap-8">

                <!-- Header -->
                <div class="mb-2">
                    <h1 class="text-4xl font-display font-black text-primary-dark dark:text-white uppercase tracking-tight italic">
                        Mi <span class="text-primary not-italic">Perfil</span>
                    </h1>
                    <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm font-medium">
                        {{ userObj.email }} &mdash;
                        <span class="capitalize font-bold text-primary">{{ Array.isArray(userObj.roles) ? userObj.roles.join(', ') : userObj.roles }}</span>
                    </p>
                </div>

                <!-- Profile Info -->
                <div class="glass-panel rounded-3xl p-8 border border-slate-200 dark:border-white/5 shadow-xl">
                    <h2 class="text-sm font-black uppercase tracking-widest text-slate-400 mb-6 flex items-center gap-2">
                        <span class="material-icons text-primary text-base">person</span>
                        Información Personal
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1 uppercase tracking-widest">Nombre</label>
                            <input v-model="profileForm.name" type="text"
                                class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1 uppercase tracking-widest">Email</label>
                            <input v-model="profileForm.email" type="email"
                                class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40" />
                        </div>
                        <div v-if="profileStatus" class="text-green-500 text-sm font-bold">{{ profileStatus }}</div>
                        <div v-if="profileError" class="text-red-500 text-sm">{{ profileError }}</div>
                        <button @click="saveProfile" :disabled="profileSaving"
                            class="px-6 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary-dark transition disabled:opacity-50 flex items-center gap-2 text-sm">
                            <span class="material-icons text-sm">save</span>
                            {{ profileSaving ? 'Guardando...' : 'Guardar Cambios' }}
                        </button>
                    </div>
                </div>

                <!-- Password -->
                <div class="glass-panel rounded-3xl p-8 border border-slate-200 dark:border-white/5 shadow-xl">
                    <h2 class="text-sm font-black uppercase tracking-widest text-slate-400 mb-6 flex items-center gap-2">
                        <span class="material-icons text-primary text-base">lock</span>
                        Cambiar Contraseña
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1 uppercase tracking-widest">Contraseña Actual</label>
                            <input v-model="passwordForm.current_password" type="password"
                                class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1 uppercase tracking-widest">Nueva Contraseña</label>
                            <input v-model="passwordForm.password" type="password"
                                class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40" />
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-slate-500 mb-1 uppercase tracking-widest">Confirmar Nueva Contraseña</label>
                            <input v-model="passwordForm.password_confirmation" type="password"
                                class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/40" />
                        </div>
                        <div v-if="passwordStatus" class="text-green-500 text-sm font-bold">{{ passwordStatus }}</div>
                        <div v-if="passwordError" class="text-red-500 text-sm">{{ passwordError }}</div>
                        <button @click="savePassword" :disabled="passwordSaving"
                            class="px-6 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary-dark transition disabled:opacity-50 flex items-center gap-2 text-sm">
                            <span class="material-icons text-sm">key</span>
                            {{ passwordSaving ? 'Actualizando...' : 'Actualizar Contraseña' }}
                        </button>
                    </div>
                </div>

                <!-- Delete Account -->
                <div class="glass-panel rounded-3xl p-8 border border-red-200 dark:border-red-900/30 shadow-xl">
                    <h2 class="text-sm font-black uppercase tracking-widest text-red-400 mb-2 flex items-center gap-2">
                        <span class="material-icons text-red-400 text-base">delete_forever</span>
                        Zona de Peligro
                    </h2>
                    <p class="text-xs text-slate-400 mb-6">Eliminar tu cuenta es irreversible. Todos tus datos serán borrados permanentemente.</p>

                    <div v-if="!showDeleteConfirm">
                        <button @click="showDeleteConfirm = true"
                            class="px-6 py-3 bg-red-500 text-white font-bold rounded-xl hover:bg-red-600 transition text-sm flex items-center gap-2">
                            <span class="material-icons text-sm">warning</span>
                            Eliminar mi cuenta
                        </button>
                    </div>
                    <div v-else class="space-y-4">
                        <p class="text-sm text-slate-600 dark:text-slate-300 font-medium">Ingresa tu contraseña para confirmar:</p>
                        <input v-model="deleteForm.password" type="password" placeholder="Tu contraseña actual"
                            class="w-full bg-white dark:bg-slate-800 border border-red-200 dark:border-red-900/30 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-red-400/40" />
                        <div v-if="deleteError" class="text-red-500 text-sm">{{ deleteError }}</div>
                        <div class="flex gap-3">
                            <button @click="deleteAccount" :disabled="deleting"
                                class="px-6 py-3 bg-red-500 text-white font-bold rounded-xl hover:bg-red-600 transition disabled:opacity-50 text-sm">
                                {{ deleting ? 'Eliminando...' : 'Confirmar eliminación' }}
                            </button>
                            <button @click="showDeleteConfirm = false; deleteForm.password = ''"
                                class="px-6 py-3 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-white font-bold rounded-xl hover:bg-slate-300 dark:hover:bg-slate-600 transition text-sm">
                                Cancelar
                            </button>
                        </div>
                    </div>
                </div>

            </main>
        </div>
    </div>
</template>

<style scoped>
.font-display { font-family: 'Outfit', sans-serif; }
.font-sans { font-family: 'Plus Jakarta Sans', sans-serif; }
</style>
