<script setup>
import { ref, onMounted } from 'vue';
import Sidebar from '../Sidebar.vue';
import Topbar from '../Topbar.vue';
import { useAuthStore } from '../../stores/auth';

const props = defineProps({
    channels: {
        type: [Array, String],
        default: () => []
    },
    user: {
        type: [Object, String],
        required: true
    }
});

const channelList = ref(typeof props.channels === 'string' ? JSON.parse(props.channels) : props.channels);
const userObj = typeof props.user === 'string' ? JSON.parse(props.user) : props.user;

const authStore = useAuthStore();
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
            <main class="mt-24 p-8 lg:p-12 flex-1 relative z-10 w-full mx-auto max-w-[1600px]">
                <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-12">
                    <div>
                        <h1
                            class="text-4xl lg:text-5xl font-display font-black text-primary-dark dark:text-white uppercase tracking-tight italic">
                            FoxFit <span class="text-primary not-italic">TV</span>
                        </h1>
                        <p class="text-slate-500 dark:text-slate-400 mt-2 font-medium max-w-2xl">
                            Entrena en vivo y repasa las clases anteriores de los mejores profesores. Elige tu canal
                            favorito.
                        </p>
                    </div>

                    <!-- Search/Filters Placeholder -->
                    <div class="glass-panel p-2 rounded-2xl flex items-center gap-2">
                        <div
                            class="bg-white dark:bg-black/20 rounded-xl px-4 py-3 flex items-center gap-3 border border-slate-100 dark:border-white/5 w-full md:w-auto">
                            <span class="material-icons text-slate-400">search</span>
                            <input type="text" placeholder="Buscar canal o profesor..."
                                class="bg-transparent border-none outline-none text-sm font-bold text-slate-700 dark:text-white placeholder-slate-400 w-full min-w-[250px]">
                        </div>
                    </div>
                </div>

                <!-- Channels Grid -->
                <div v-if="channelList.length > 0"
                    class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 2xl:grid-cols-4 gap-8">
                    <a :href="`/channels/${channel.slug}`" v-for="channel in channelList" :key="channel.id"
                        class="group glass-panel rounded-[2rem] overflow-hidden border border-slate-200 dark:border-white/5 shadow-xl shadow-slate-200/50 dark:shadow-none hover:-translate-y-2 transition-all duration-500 flex flex-col hover:shadow-primary/20 hover:border-primary/30 relative">

                        <!-- LIVE Badge absolute -->
                        <div v-if="channel.is_live"
                            class="absolute top-4 left-4 z-20 flex items-center gap-2 bg-red-500/90 backdrop-blur-md px-3 py-1.5 rounded-full shadow-lg shadow-red-500/20 border border-red-400/30">
                            <span class="w-2 h-2 rounded-full bg-white animate-pulse"></span>
                            <span class="text-[10px] font-black tracking-widest text-white uppercase">EN VIVO</span>
                        </div>

                        <!-- Cover Image -->
                        <div class="relative h-48 sm:h-56 overflow-hidden bg-slate-100 dark:bg-white/5 w-full">
                            <img v-if="channel.cover_image" :src="channel.cover_image" :alt="channel.name"
                                class="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110">
                            <!-- Overlay gradient -->
                            <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/20 to-transparent">
                            </div>

                            <!-- Professor Avatar Overlay -->
                            <div class="absolute bottom-4 left-6 flex items-center gap-3">
                                <div
                                    class="w-12 h-12 rounded-xl bg-primary text-primary-dark flex items-center justify-center font-display font-black text-xl shadow-lg border-[3px] border-white dark:border-slate-800">
                                    {{ channel.user?.name.charAt(0) || 'F' }}
                                </div>
                                <div>
                                    <h3
                                        class="text-white font-bold mb-0.5 leading-tight group-hover:text-primary transition-colors line-clamp-1">
                                        {{ channel.name }}</h3>
                                    <p class="text-xs text-white/70 font-medium">Profesor: {{ channel.user?.name ||
                                        'FoxFit' }}</p>
                                </div>
                            </div>
                        </div>

                        <!-- Content Info -->
                        <div class="p-6 flex-1 flex flex-col justify-between gap-4">
                            <p class="text-sm text-slate-500 dark:text-slate-400 font-medium line-clamp-3">
                                {{ channel.description || 'Acompáñame en mis entrenamientos diarios.' }}
                            </p>

                            <div
                                class="flex items-center justify-between pt-4 border-t border-slate-100 dark:border-white/5">
                                <div
                                    class="flex items-center gap-2 text-slate-500 dark:text-slate-400 text-xs font-bold bg-slate-50 dark:bg-white/5 py-1.5 px-3 rounded-lg border border-slate-100 dark:border-transparent">
                                    <span class="material-icons text-[16px] text-primary">video_library</span>
                                    <span>{{ channel.videos_count }} Videos</span>
                                </div>
                                <span
                                    class="material-icons text-slate-300 dark:text-slate-600 group-hover:text-primary transition-colors group-hover:translate-x-1 duration-300">arrow_forward</span>
                            </div>
                        </div>
                    </a>
                </div>

                <!-- Empty State -->
                <div v-else
                    class="glass-panel rounded-[2rem] p-16 text-center border border-slate-200 dark:border-white/5 shadow-xl shadow-slate-200/50 dark:shadow-none flex flex-col items-center justify-center max-w-2xl mx-auto mt-12">
                    <div
                        class="w-24 h-24 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center mb-6">
                        <span class="material-icons text-5xl text-slate-400 dark:text-slate-500">live_tv</span>
                    </div>
                    <h3
                        class="text-2xl font-display font-black text-primary-dark dark:text-white uppercase italic tracking-tight mb-2">
                        No hay Canales Disponibles</h3>
                    <p class="text-slate-500 font-medium">Aún no se han configurado canales de transmisión en esta
                        plataforma. Vuelve pronto.</p>
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
