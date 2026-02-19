<script setup>
import { computed, ref, onMounted, onUnmounted } from 'vue';
import { useAuthStore } from '../stores/auth';

const props = defineProps({
    isDark: Boolean
});

const emit = defineEmits(['toggle-dark']);

const authStore = useAuthStore();
const user = computed(() => authStore.user);

// Dropdown state
const isProfileOpen = ref(false);
const profileRef = ref(null);

const toggleProfile = () => isProfileOpen.value = !isProfileOpen.value;
const closeProfile = (e) => {
    if (profileRef.value && !profileRef.value.contains(e.target)) {
        isProfileOpen.value = false;
    }
};

onMounted(() => window.addEventListener('click', closeProfile));
onUnmounted(() => window.removeEventListener('click', closeProfile));

const navLinks = computed(() => {
    const links = [
        { name: 'Dashboard', route: '/dashboard' }
    ];

    if (authStore.isAdmin) {
        links.push(
            { name: 'Usuarios', route: '/admin/users' },
            { name: 'Categorías', route: '/admin/categories' }
        );
    } else if (authStore.isTrainer) {
        links.push(
            { name: 'Mis Alumnos', route: '/trainer/dashboard' },
            { name: 'Rutinas', route: '/trainer/routines' }
        );
    } else if (authStore.isStudent) {
        links.push(
            { name: 'Mis Rutinas', route: '/student/dashboard' },
            { name: 'Progreso', route: '/student/progress' }
        );
    }

    return links;
});

const handleLogout = async () => {
    try {
        await authStore.logout();
        window.location.href = '/';
    } catch (error) {
        console.error('Logout failed:', error);
    }
};
</script>

<template>
    <header
        class="fixed top-0 right-0 left-72 h-20 z-40 px-8 flex items-center justify-between bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-200 dark:border-white/5 transition-all duration-500">
        <!-- Left: Search & Nav Links -->
        <div class="flex items-center gap-10">
            <!-- Search Bar -->
            <div class="relative group hidden lg:block">
                <span
                    class="material-icons absolute left-4 top-1/2 -translate-y-1/2 text-slate-400 group-focus-within:text-primary transition-colors text-xl">search</span>
                <input type="text" placeholder="Buscar recursos..."
                    class="w-64 h-11 pl-11 pr-4 bg-slate-100 dark:bg-white/5 border-none rounded-2xl text-xs font-medium focus:ring-2 focus:ring-primary/20 transition-all outline-none">
            </div>

            <!-- Horizontal Menu -->
            <nav class="flex items-center gap-2">
                <a v-for="link in navLinks" :key="link.name" :href="link.route"
                    class="px-4 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest text-slate-500 dark:text-slate-400 hover:text-primary dark:hover:text-primary hover:bg-primary/10 dark:hover:bg-primary/5 transition-all">
                    {{ link.name }}
                </a>
            </nav>
        </div>

        <!-- Right: Utilities & Profile -->
        <div class="flex items-center gap-4">
            <!-- Theme Toggle -->
            <button @click="$emit('toggle-dark')"
                class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-white/5 border border-transparent dark:border-white/5 flex items-center justify-center hover:scale-110 transition-all text-slate-500 dark:text-slate-400">
                <span class="material-icons text-lg">{{ isDark ? 'light_mode' : 'dark_mode' }}</span>
            </button>

            <!-- Notifications -->
            <button
                class="w-10 h-10 rounded-xl bg-slate-100 dark:bg-white/5 border border-transparent dark:border-white/5 flex items-center justify-center hover:scale-110 transition-all text-slate-500 dark:text-slate-400 relative">
                <span class="material-icons text-lg">notifications</span>
                <span
                    class="absolute top-2.5 right-2.5 w-1.5 h-1.5 bg-primary rounded-full ring-2 ring-white dark:ring-slate-900"></span>
            </button>

            <!-- User Profile Dropdown -->
            <div class="relative ml-2" ref="profileRef">
                <button @click="toggleProfile"
                    class="flex items-center gap-3 p-1 rounded-2xl hover:bg-slate-100 dark:hover:bg-white/5 transition-all border border-transparent"
                    :class="{ 'bg-slate-100 dark:bg-white/5 border-slate-200 dark:border-white/10': isProfileOpen }">
                    <div
                        class="w-10 h-10 rounded-xl bg-primary flex items-center justify-center text-primary-dark font-black text-sm shadow-lg shadow-primary/20">
                        {{ user?.name?.charAt(0) || 'U' }}
                    </div>
                    <div class="text-left hidden xl:block pr-2">
                        <p
                            class="text-[10px] font-black text-primary-dark dark:text-white uppercase tracking-tighter leading-none">
                            {{ user?.name }}</p>
                        <p class="text-[8px] text-slate-400 font-bold uppercase tracking-widest mt-1">{{
                            user?.roles?.[0] }}</p>
                    </div>
                    <span class="material-icons text-slate-400 text-sm transition-transform"
                        :class="{ 'rotate-180': isProfileOpen }">expand_more</span>
                </button>

                <!-- Dropdown Menu -->
                <div v-show="isProfileOpen"
                    class="absolute top-full right-0 mt-2 w-56 bg-white dark:bg-slate-800 rounded-[1.5rem] border border-slate-200 dark:border-white/10 shadow-2xl py-2 z-50 animate-in fade-in slide-in-from-top-2 duration-300">
                    <div class="px-5 py-3 border-b border-slate-100 dark:border-white/5 mb-2">
                        <p class="text-[10px] font-black uppercase text-slate-400 tracking-[0.2em] mb-1">Identidad</p>
                        <p class="text-sm font-bold text-primary-dark dark:text-white truncate">{{ user?.email }}</p>
                    </div>

                    <a href="/profile"
                        class="flex items-center gap-3 px-5 py-3 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-primary/10 dark:hover:bg-primary/5 hover:text-primary transition-all">
                        <span class="material-icons text-lg">person</span>
                        Mi Perfil
                    </a>
                    <a href="/settings"
                        class="flex items-center gap-3 px-5 py-3 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-primary/10 dark:hover:bg-primary/5 hover:text-primary transition-all">
                        <span class="material-icons text-lg">settings</span>
                        Configuración
                    </a>

                    <div class="h-px bg-slate-100 dark:bg-white/5 my-2"></div>

                    <button @click="handleLogout"
                        class="w-full flex items-center gap-3 px-5 py-3 text-xs font-bold text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition-all text-left">
                        <span class="material-icons text-lg">logout</span>
                        Cerrar Sesión
                    </button>
                </div>
            </div>
        </div>
    </header>
</template>

<style scoped>
header {
    font-family: 'Plus Jakarta Sans', sans-serif;
}
</style>
