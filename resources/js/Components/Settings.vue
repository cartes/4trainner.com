<script setup>
import { ref, onMounted } from 'vue';
import Sidebar from './Sidebar.vue';
import Topbar from './Topbar.vue';
import { useAuthStore } from '../stores/auth';

const props = defineProps({
    user: {
        type: [Object, String],
        required: true
    },
    status: String
});

const userObj = typeof props.user === 'string' ? JSON.parse(props.user) : props.user;
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');

// Form state using data passed from blade
const formData = ref({
    name: userObj.name || '',
    email: userObj.email || '',
    phone: userObj.meta?.phone || '',
    bio: userObj.meta?.bio || '',
    notify_new_routines: userObj.meta?.notify_new_routines === '1',
    notify_messages: userObj.meta?.notify_messages === '1',
    notify_marketing: userObj.meta?.notify_marketing === '1',
    notify_new_students: userObj.meta?.notify_new_students === '1',
    notify_system_alerts: userObj.meta?.notify_system_alerts === '1',
    current_password: '',
    password: '',
    password_confirmation: ''
});

const authStore = useAuthStore();

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

onMounted(() => {
    if (userObj && !authStore.user) {
        authStore.user = userObj;
        authStore.isAuthenticated = true;
    }

    // Initialize theme
    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        isDark.value = true;
        document.documentElement.classList.add('dark');
    } else {
        isDark.value = false;
        document.documentElement.classList.remove('dark');
    }
});
</script>

<template>
    <div
        class="min-h-screen bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-200 font-sans transition-colors duration-500 relative flex overflow-x-hidden">
        <!-- Sidebar -->
        <Sidebar :is-dark="isDark" @toggle-dark="toggleDarkMode" />

        <!-- Main Content Area -->
        <div class="ml-72 flex-1 flex flex-col min-h-screen relative">
            <!-- Topbar -->
            <Topbar :is-dark="isDark" @toggle-dark="toggleDarkMode" />

            <!-- Main Content -->
            <main class="mt-24 p-8 lg:p-12 flex-1 relative z-10 w-full max-w-7xl mx-auto">
                <h1
                    class="text-4xl font-display font-black text-primary-dark dark:text-white mb-8 uppercase tracking-tight italic">
                    Configuración <span class="text-primary not-italic">de Cuenta</span>
                </h1>

                <!-- Success Message -->
                <transition enter-active-class="transition-all duration-500 ease-out"
                    enter-from-class="opacity-0 translate-y-[-20px]" enter-to-class="opacity-100 translate-y-0"
                    leave-active-class="transition-all duration-300 ease-in" leave-from-class="opacity-100"
                    leave-to-class="opacity-0">
                    <div v-if="status === 'profile-updated'"
                        class="mb-8 p-4 bg-green-500/10 text-green-700 dark:text-green-400 rounded-2xl border border-green-500/20 flex items-center gap-3 backdrop-blur-md">
                        <span class="material-icons text-green-500">check_circle</span>
                        <span class="font-bold text-sm">Los cambios se han guardado correctamente.</span>
                    </div>
                </transition>

                <div class="grid lg:grid-cols-3 gap-8 items-start">

                    <!-- Left Column: Settings Form -->
                    <div class="lg:col-span-2 space-y-8">
                        <form action="/settings" method="POST"
                            class="glass-panel rounded-[2rem] p-8 lg:p-12 border border-slate-200 dark:border-white/5 shadow-xl shadow-slate-200/50 dark:shadow-none space-y-12 relative overflow-hidden">
                            <!-- Background Accent -->
                            <div
                                class="absolute -top-32 -right-32 w-64 h-64 bg-primary/10 rounded-full blur-3xl pointer-events-none">
                            </div>

                            <input type="hidden" name="_token" :value="csrfToken">
                            <input type="hidden" name="_method" value="PATCH">

                            <!-- Perfil Básico -->
                            <section class="relative z-10">
                                <div class="flex items-center gap-3 mb-6">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-primary/20 flex items-center justify-center text-primary-dark dark:text-primary">
                                        <span class="material-icons">account_circle</span>
                                    </div>
                                    <h2
                                        class="text-xl font-display font-black text-primary-dark dark:text-white uppercase italic tracking-tight">
                                        Información <span class="text-primary not-italic">Personal</span>
                                    </h2>
                                </div>

                                <div class="grid md:grid-cols-2 gap-8">
                                    <div class="space-y-2">
                                        <label
                                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Nombre
                                            Completo</label>
                                        <input type="text" name="name" v-model="formData.name" required
                                            class="w-full bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 rounded-2xl px-6 py-4 text-slate-800 dark:text-white font-bold focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all placeholder-slate-400 dark:placeholder-slate-600">
                                    </div>
                                    <div class="space-y-2">
                                        <label
                                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Correo
                                            Electrónico</label>
                                        <input type="email" name="email" v-model="formData.email" required
                                            class="w-full bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 rounded-2xl px-6 py-4 text-slate-800 dark:text-white font-bold focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all placeholder-slate-400 dark:placeholder-slate-600">
                                    </div>
                                    <div class="space-y-2">
                                        <label
                                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Teléfono
                                            de Contacto</label>
                                        <input type="tel" name="phone" v-model="formData.phone"
                                            placeholder="+56 9 1234 5678"
                                            class="w-full bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 rounded-2xl px-6 py-4 text-slate-800 dark:text-white font-bold focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all placeholder-slate-400 dark:placeholder-slate-600">
                                    </div>
                                    <div class="space-y-2 md:col-span-2">
                                        <label
                                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Biografía
                                            / Notas</label>
                                        <textarea name="bio" v-model="formData.bio" rows="3"
                                            placeholder="Cuéntanos un poco sobre ti y tus objetivos..."
                                            class="w-full bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 rounded-2xl px-6 py-4 text-slate-800 dark:text-white font-bold focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all placeholder-slate-400 dark:placeholder-slate-600 resize-none"></textarea>
                                    </div>
                                </div>
                            </section>

                            <!-- Seguridad -->
                            <section class="pt-8 border-t border-slate-100 dark:border-white/5 relative z-10">
                                <div class="flex items-center gap-3 mb-6">
                                    <div
                                        class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-white/5 flex items-center justify-center text-slate-600 dark:text-slate-400">
                                        <span class="material-icons">lock</span>
                                    </div>
                                    <h2
                                        class="text-xl font-display font-black text-primary-dark dark:text-white uppercase italic tracking-tight">
                                        Seguridad <span class="text-slate-400 not-italic">de Acceso</span>
                                    </h2>
                                </div>

                                <div class="space-y-6">
                                    <div class="space-y-2 max-w-md">
                                        <label
                                            class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Contraseña
                                            Actual</label>
                                        <input type="password" name="current_password"
                                            v-model="formData.current_password"
                                            placeholder="Requerida para cambiar contraseña"
                                            class="w-full bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 rounded-2xl px-6 py-4 text-slate-800 dark:text-white font-bold focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all placeholder-slate-400 dark:placeholder-slate-600">
                                    </div>
                                    <div class="grid md:grid-cols-2 gap-8">
                                        <div class="space-y-2">
                                            <label
                                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Nueva
                                                Contraseña</label>
                                            <input type="password" name="password" v-model="formData.password"
                                                placeholder="Mínimo 8 caracteres"
                                                class="w-full bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 rounded-2xl px-6 py-4 text-slate-800 dark:text-white font-bold focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all placeholder-slate-400 dark:placeholder-slate-600">
                                        </div>
                                        <div class="space-y-2">
                                            <label
                                                class="text-[10px] font-black text-slate-400 uppercase tracking-widest pl-2">Confirmar
                                                Contraseña</label>
                                            <input type="password" name="password_confirmation"
                                                v-model="formData.password_confirmation"
                                                placeholder="Repite la nueva contraseña"
                                                class="w-full bg-slate-50 dark:bg-black/20 border border-slate-200 dark:border-white/10 rounded-2xl px-6 py-4 text-slate-800 dark:text-white font-bold focus:outline-none focus:ring-2 focus:ring-primary/50 focus:border-primary transition-all placeholder-slate-400 dark:placeholder-slate-600">
                                        </div>
                                    </div>
                                </div>
                            </section>

                            <!-- Form Actions -->
                            <div
                                class="pt-8 border-t border-slate-100 dark:border-white/5 flex items-center justify-between relative z-10">
                                <p class="text-xs text-slate-500 font-medium">Todos los campos marcados son
                                    obligatorios.</p>
                                <button type="submit"
                                    class="bg-primary hover:bg-primary-dark text-primary-dark font-black uppercase tracking-widest text-sm py-4 px-10 rounded-2xl shadow-lg shadow-primary/20 hover:shadow-primary/40 hover:-translate-y-1 transition-all duration-300 flex items-center gap-2 group">
                                    <span>Guardar</span>
                                    <span
                                        class="material-icons text-sm group-hover:translate-x-1 transition-transform">arrow_forward</span>
                                </button>
                            </div>

                        </form>
                    </div>

                    <!-- Right Column: Preferences & Notifications -->
                    <div class="space-y-8">
                        <div
                            class="glass-panel rounded-[2rem] p-8 border border-slate-200 dark:border-white/5 shadow-xl shadow-slate-200/50 dark:shadow-none">
                            <div class="flex items-center gap-3 mb-8">
                                <div
                                    class="w-10 h-10 rounded-xl bg-aqua-400/20 flex items-center justify-center text-aqua-600 dark:text-aqua-400">
                                    <span class="material-icons">notifications_active</span>
                                </div>
                                <h3
                                    class="text-lg font-display font-black text-primary-dark dark:text-white uppercase italic tracking-tight">
                                    Preferencias <span class="text-aqua-400 not-italic">Push</span>
                                </h3>
                            </div>

                            <p class="text-sm text-slate-500 dark:text-slate-400 mb-8 font-medium">
                                Selecciona qué tipo de actividad de FoxFit quieres recibir en tu correo o dispositivo
                                móvil.
                            </p>
                            <input type="hidden" name="notify_new_routines"
                                :value="formData.notify_new_routines ? 1 : 0">
                            <input type="hidden" name="notify_messages" :value="formData.notify_messages ? 1 : 0">
                            <input type="hidden" name="notify_marketing" :value="formData.notify_marketing ? 1 : 0">
                            <input type="hidden" name="notify_new_students"
                                :value="formData.notify_new_students ? 1 : 0">
                            <input type="hidden" name="notify_system_alerts"
                                :value="formData.notify_system_alerts ? 1 : 0">

                            <!-- These toggles update hidden inputs in the main form or sync state -->
                            <!-- Note: HTML forms submit checkboxes as 'on' only if checked. We use hidden inputs to properly handle 0/1 boolean state for Laravel backend -->
                            <div class="space-y-4">

                                <!-- ONLY STUDENTS -->
                                <div v-if="authStore.isStudent"
                                    class="flex items-center justify-between gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-transparent cursor-pointer hover:border-aqua-400/30 transition-colors"
                                    @click="formData.notify_new_routines = !formData.notify_new_routines">
                                    <div class="flex-1">
                                        <p class="text-primary-dark dark:text-white font-bold text-sm mb-1">Nuevas
                                            Rutinas</p>
                                        <p class="text-xs text-slate-500 font-medium">Avisarme cuando se asigne una
                                            nueva rutina a mi plan.</p>
                                    </div>
                                    <div class="relative w-11 h-6 rounded-full transition-colors duration-300 shrink-0"
                                        :class="formData.notify_new_routines ? 'bg-aqua-400' : 'bg-slate-300 dark:bg-slate-600'">
                                        <div class="absolute top-[2px] left-[2px] bg-white w-5 h-5 rounded-full transition-transform duration-300 shadow-sm"
                                            :class="formData.notify_new_routines ? 'translate-x-[20px]' : 'translate-x-0'">
                                        </div>
                                    </div>
                                </div>

                                <!-- ONLY TRAINERS -->
                                <div v-if="authStore.isTrainer || authStore.isAdmin"
                                    class="flex items-center justify-between gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-transparent cursor-pointer hover:border-blue-500/30 transition-colors"
                                    @click="formData.notify_new_students = !formData.notify_new_students">
                                    <div class="flex-1">
                                        <p class="text-primary-dark dark:text-white font-bold text-sm mb-1">Nuevos
                                            Alumnos</p>
                                        <p class="text-xs text-slate-500 font-medium">Recibir alerta cuando se me asigne
                                            un nuevo cliente.</p>
                                    </div>
                                    <div class="relative w-11 h-6 rounded-full transition-colors duration-300 shrink-0"
                                        :class="formData.notify_new_students ? 'bg-blue-500' : 'bg-slate-300 dark:bg-slate-600'">
                                        <div class="absolute top-[2px] left-[2px] bg-white w-5 h-5 rounded-full transition-transform duration-300 shadow-sm"
                                            :class="formData.notify_new_students ? 'translate-x-[20px]' : 'translate-x-0'">
                                        </div>
                                    </div>
                                </div>

                                <!-- ALL / DIRECT MESSAGES -->
                                <div class="flex items-center justify-between gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-transparent cursor-pointer hover:border-primary/30 transition-colors"
                                    @click="formData.notify_messages = !formData.notify_messages">
                                    <div class="flex-1">
                                        <p class="text-primary-dark dark:text-white font-bold text-sm mb-1">Mensajes
                                            directos</p>
                                        <p class="text-xs text-slate-500 font-medium">Recibir notificaciones por nuevos
                                            mensajes.</p>
                                    </div>
                                    <div class="relative w-11 h-6 rounded-full transition-colors duration-300 shrink-0"
                                        :class="formData.notify_messages ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600'">
                                        <div class="absolute top-[2px] left-[2px] bg-white w-5 h-5 rounded-full transition-transform duration-300 shadow-sm"
                                            :class="formData.notify_messages ? 'translate-x-[20px]' : 'translate-x-0'">
                                        </div>
                                    </div>
                                </div>

                                <!-- ONLY ADMINS -->
                                <div v-if="authStore.isAdmin"
                                    class="flex items-center justify-between gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-transparent cursor-pointer hover:border-red-500/30 transition-colors"
                                    @click="formData.notify_system_alerts = !formData.notify_system_alerts">
                                    <div class="flex-1">
                                        <p class="text-primary-dark dark:text-white font-bold text-sm mb-1">Alertas
                                            Generales</p>
                                        <p class="text-xs text-slate-500 font-medium">Reportes de seguridad y métricas
                                            del sistema.</p>
                                    </div>
                                    <div class="relative w-11 h-6 rounded-full transition-colors duration-300 shrink-0"
                                        :class="formData.notify_system_alerts ? 'bg-red-500' : 'bg-slate-300 dark:bg-slate-600'">
                                        <div class="absolute top-[2px] left-[2px] bg-white w-5 h-5 rounded-full transition-transform duration-300 shadow-sm"
                                            :class="formData.notify_system_alerts ? 'translate-x-[20px]' : 'translate-x-0'">
                                        </div>
                                    </div>
                                </div>

                                <!-- MARKETING / NEWS -->
                                <div class="flex items-center justify-between gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-white/5 border border-slate-100 dark:border-transparent cursor-pointer hover:border-primary/30 transition-colors"
                                    @click="formData.notify_marketing = !formData.notify_marketing">
                                    <div class="flex-1">
                                        <p class="text-primary-dark dark:text-white font-bold text-sm mb-1">Noticias &
                                            Eventos</p>
                                        <p class="text-xs text-slate-500 font-medium">Novedades, marketing y
                                            recomendaciones exclusivas.</p>
                                    </div>
                                    <div class="relative w-11 h-6 rounded-full transition-colors duration-300 shrink-0"
                                        :class="formData.notify_marketing ? 'bg-primary' : 'bg-slate-300 dark:bg-slate-600'">
                                        <div class="absolute top-[2px] left-[2px] bg-white w-5 h-5 rounded-full transition-transform duration-300 shadow-sm"
                                            :class="formData.notify_marketing ? 'translate-x-[20px]' : 'translate-x-0'">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- User Info Card -->
                        <div
                            class="glass-panel rounded-[2rem] p-8 border border-slate-200 dark:border-white/5 bg-gradient-to-br from-primary/5 to-transparent text-center">
                            <div
                                class="w-20 h-20 mx-auto rounded-3xl bg-primary text-primary-dark flex items-center justify-center text-4xl font-black font-display shadow-lg shadow-primary/30 mb-4">
                                {{ userObj.name.charAt(0) }}
                            </div>
                            <h3 class="text-xl font-bold text-primary-dark dark:text-white mb-1">{{ userObj.name }}</h3>
                            <div class="flex flex-wrap justify-center gap-2 mb-4">
                                <span v-for="role in userObj.roles" :key="role"
                                    class="px-2 py-1 bg-white/10 dark:bg-white/5 rounded-full text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 border border-slate-200 dark:border-white/10">
                                    {{ role.replace('-', ' ') }}
                                </span>
                            </div>
                            <p class="text-xs text-slate-500 font-medium">Miembro desde {{ new Date(userObj.created_at
                                || Date.now()).getFullYear() }}</p>
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
