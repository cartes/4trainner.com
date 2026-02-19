<script setup>
import { ref, computed } from 'vue';
import { useAuthStore } from '../stores/auth';

const props = defineProps({
    isDark: Boolean
});

const emit = defineEmits(['toggle-dark']);

const authStore = useAuthStore();
const user = computed(() => authStore.user);

// State for open sub-menus
const openMenus = ref(['GESTIÓN', 'BIBLIOTECA', 'COMUNIDAD']);

const toggleMenu = (groupName) => {
    if (openMenus.value.includes(groupName)) {
        openMenus.value = openMenus.value.filter(m => m !== groupName);
    } else {
        openMenus.value.push(groupName);
    }
};

const menuGroups = computed(() => {
    const groups = [];

    // Core / Gestion Group
    const gestionItems = [
        { name: 'Panel Principal', icon: 'dashboard', route: '/dashboard' }
    ];
    if (authStore.isTrainer || authStore.isAdmin) {
        gestionItems.push({ name: 'Mis Alumnos', icon: 'groups', route: '/trainer/dashboard' });
    }
    groups.push({ name: 'GESTIÓN', items: gestionItems });

    // Biblioteca Group
    const bibliotecaItems = [];
    if (authStore.isTrainer || authStore.isAdmin) {
        bibliotecaItems.push({ name: 'Rutinas', icon: 'fitness_center', route: '/trainer/routines' });
        bibliotecaItems.push({ name: 'Ejercicios', icon: 'sports_gymnastics', route: '/trainer/exercises' });
    }
    if (authStore.isStudent) {
        bibliotecaItems.push({ name: 'Mis Rutinas', icon: 'assignment', route: '/student/dashboard' });
    }
    if (bibliotecaItems.length > 0) {
        groups.push({ name: 'BIBLIOTECA', items: bibliotecaItems });
    }

    // Comunidad Group
    const comunidadItems = [
        { name: 'Progreso', icon: 'trending_up', route: '/student/progress' },
        { name: 'Retos', icon: 'emoji_events', route: '/challenges' }
    ];
    groups.push({ name: 'COMUNIDAD', items: comunidadItems });

    return groups;
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
    <aside
        class="fixed left-0 top-0 h-screen w-72 bg-white dark:bg-slate-900 border-r border-slate-200 dark:border-white/5 z-50 flex flex-col transition-all duration-500 overflow-y-auto custom-scrollbar">
        <!-- Logo -->
        <div class="p-8 mb-4">
            <h1
                class="text-3xl font-display font-black text-primary-dark dark:text-white tracking-tighter uppercase italic flex items-center gap-2">
                <div class="w-10 h-10 bg-primary rounded-xl flex items-center justify-center not-italic">
                    <span class="material-icons text-primary-dark text-2xl">bolt</span>
                </div>
                <span class="text-primary not-italic">Fox</span>Fit
            </h1>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 px-4 space-y-6">
            <div v-for="group in menuGroups" :key="group.name" class="space-y-2">
                <!-- Group Header -->
                <button @click="toggleMenu(group.name)"
                    class="w-full flex items-center justify-between px-6 py-2 group">
                    <span
                        class="text-[10px] font-black text-slate-400 dark:text-slate-500 uppercase tracking-[0.2em] group-hover:text-primary transition-colors">
                        {{ group.name }}
                    </span>
                    <span class="material-icons text-sm text-slate-300 transition-transform duration-300"
                        :class="{ 'rotate-180': openMenus.includes(group.name) }">
                        expand_more
                    </span>
                </button>

                <!-- Group Items -->
                <div v-show="openMenus.includes(group.name)"
                    class="space-y-1 animate-in fade-in slide-in-from-top-1 duration-300">
                    <a v-for="item in group.items" :key="item.name" :href="item.route"
                        class="flex items-center gap-4 px-6 py-4 rounded-[1.5rem] transition-all group hover:bg-primary/10 dark:hover:bg-primary/5 text-slate-600 dark:text-slate-400 font-bold uppercase tracking-widest text-[10px] relative overflow-hidden active:scale-95">
                        <span class="material-icons text-xl group-hover:text-primary transition-colors">{{ item.icon
                            }}</span>
                        <span class="group-hover:translate-x-1 transition-transform">{{ item.name }}</span>

                        <!-- Active Indicator (Dot) -->
                        <div
                            class="absolute right-6 w-1.5 h-1.5 bg-primary rounded-full opacity-0 group-hover:opacity-100 shadow-[0_0_8px_#FF5500]">
                        </div>
                    </a>
                </div>
            </div>
        </nav>

        <!-- Footer -->
        <div class="p-6">
            <!-- User Profile (Simplified) -->
            <div class="p-5 bg-slate-50 dark:bg-white/5 rounded-[2rem] border border-slate-100 dark:border-white/5">
                <button @click="handleLogout"
                    class="w-full py-4 bg-white dark:bg-slate-800 rounded-2xl text-[10px] font-black uppercase tracking-[0.2em] text-red-500 hover:bg-red-500 hover:text-white transition-all shadow-sm flex items-center justify-center gap-2">
                    <span class="material-icons text-sm">logout</span>
                    Cerrar Sesión
                </button>
            </div>
        </div>
    </aside>
</template>

<style scoped>
.font-display {
    font-family: 'Outfit', sans-serif;
}

.custom-scrollbar::-webkit-scrollbar {
    width: 4px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(190, 242, 100, 0.1);
    border-radius: 10px;
}
</style>
