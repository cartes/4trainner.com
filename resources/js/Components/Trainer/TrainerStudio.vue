<script setup>
import { ref, onMounted, computed } from 'vue';
import { useAuthStore } from '../../stores/auth';
import Sidebar from '../Sidebar.vue';
import Topbar from '../Topbar.vue';
import LiveChat from './LiveChat.vue';

const props = defineProps({
    channel: {
        type: [Object, String],
        required: true
    },
    user: {
        type: [Object, String],
        required: true
    }
});

const channelObj = ref(typeof props.channel === 'string' ? JSON.parse(props.channel) : props.channel);
const userObj = typeof props.user === 'string' ? JSON.parse(props.user) : props.user;
const authStore = useAuthStore();
const isDark = ref(false);
const activeVideo = computed(() => channelObj.value.videos && channelObj.value.videos.length > 0 ? channelObj.value.videos[0] : null);

const copyKey = () => {
    navigator.clipboard.writeText(channelObj.value.stream_key);
    alert('¡Clave copiada al portapapeles!');
};

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

    // Check Dark Mode
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

        <Sidebar :is-dark="isDark" @toggle-dark="toggleDarkMode" />

        <!-- Main Content Area -->
        <div class="ml-72 flex-1 flex flex-col min-h-screen relative">
            <Topbar :is-dark="isDark" @toggle-dark="toggleDarkMode" />

            <!-- Studio Area -->
            <main
                class="mt-24 p-8 lg:p-12 flex-1 relative z-10 w-full mx-auto max-w-[1600px] flex flex-col lg:flex-row gap-8 items-start">

                <!-- Left: Control Room (Video Monitor & Details) -->
                <div class="w-full lg:w-2/3 flex flex-col gap-6">
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-4">
                            <a href="/trainer/dashboard"
                                class="w-10 h-10 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center hover:bg-slate-200 dark:hover:bg-white/10 transition text-slate-500">
                                <span class="material-icons">arrow_back</span>
                            </a>
                            <h1
                                class="text-4xl font-display font-black text-primary-dark dark:text-white uppercase tracking-tight italic">
                                FOXFIT <span class="text-primary not-italic">STUDIO</span>
                            </h1>
                        </div>
                        <div v-if="channelObj.is_live"
                            class="flex items-center gap-2 bg-red-500/90 backdrop-blur-md px-4 py-2 rounded-full shadow-lg shadow-red-500/20">
                            <span class="w-2.5 h-2.5 rounded-full bg-white animate-pulse"></span>
                            <span
                                class="text-[12px] font-black tracking-widest text-white uppercase">Transmitiendo</span>
                        </div>
                        <div v-else
                            class="flex items-center gap-2 bg-slate-200 dark:bg-slate-800 px-4 py-2 rounded-full border border-slate-300 dark:border-white/10">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                            <span
                                class="text-[12px] font-black tracking-widest text-slate-500 dark:text-slate-400 uppercase">Fuera
                                de Línea</span>
                        </div>
                    </div>

                    <!-- Connection Details Block -->
                    <div
                        class="glass-panel rounded-3xl p-8 border border-slate-200 dark:border-white/5 shadow-xl relative overflow-hidden group">
                        <div
                            class="absolute inset-0 bg-gradient-to-r from-primary/5 to-transparent pointer-events-none">
                        </div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-slate-400 mb-6">Credenciales de
                            Conexión (OBS)</h3>

                        <div class="space-y-4 relative z-10">
                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-bold text-slate-500">Servidor RTMP</span>
                                <div
                                    class="bg-slate-100 dark:bg-slate-900/50 p-3 rounded-xl border border-slate-200 dark:border-white/10 text-sm font-mono text-primary-dark dark:text-gray-300">
                                    rtmp://stream.4trainner.com/live
                                </div>
                            </div>
                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-bold text-slate-500">Clave de Transmisión</span>
                                <div class="flex items-center gap-2">
                                    <div
                                        class="flex-1 bg-slate-100 dark:bg-slate-900/50 p-3 rounded-xl border border-slate-200 dark:border-white/10 text-sm font-mono text-primary dark:text-gray-300 truncate">
                                        ***************************
                                    </div>
                                    <button @click="copyKey"
                                        class="h-[46px] px-6 bg-primary text-white font-bold rounded-xl shadow-lg hover:bg-primary-dark transition flex items-center justify-center gap-2">
                                        <span class="material-icons text-sm">content_copy</span> COPIAR
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stream Monitor Placeholder -->
                    <div
                        class="rounded-3xl overflow-hidden mt-2 bg-black aspect-video relative shadow-2xl border border-slate-800 flex items-center justify-center">
                        <div v-if="!channelObj.is_live" class="text-center">
                            <span class="material-icons text-white/20 text-7xl mb-4 block">videocam_off</span>
                            <p class="text-white/50 font-bold uppercase tracking-widest text-sm">Esperando Recepción de
                                Video...</p>
                        </div>
                        <!-- If Live, you could place a tiny VideoJS player here hitting the M3U8 for self-monitoring -->
                        <video v-else-if="activeVideo && activeVideo.status === 'live'"
                            class="w-full h-full object-cover" controls autoplay muted>
                            <!-- Monitor source -->
                            <source :src="`/api/stream/${activeVideo.id}`" type="application/x-mpegURL">
                        </video>
                    </div>

                </div>

                <!-- Right: Interactive Live Chat -->
                <div class="w-full lg:w-1/3 h-[calc(100vh-140px)] sticky top-28 hidden lg:block">
                    <!-- Componente Hilo de Mensajes -->
                    <LiveChat :video-id="activeVideo ? activeVideo.id : null" :is-dark="isDark"
                        :current-user="authStore.user" />
                </div>
            </main>
        </div>
    </div>
</template>

<style scoped>
@import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&family=Outfit:wght@400;700;900&display=swap');

.font-display {
    font-family: 'Outfit', sans-serif;
}

.font-sans {
    font-family: 'Plus Jakarta Sans', sans-serif;
}
</style>
