<script setup>
import { ref, onMounted, computed } from 'vue';
import Sidebar from '../Sidebar.vue';
import Topbar from '../Topbar.vue';
import LiveChat from '../Trainer/LiveChat.vue';
import { useAuthStore } from '../../stores/auth';

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

const channelObj = typeof props.channel === 'string' ? JSON.parse(props.channel) : props.channel;
const userObj = typeof props.user === 'string' ? JSON.parse(props.user) : props.user;
const videos = computed(() => channelObj.videos || []);

// Setup the active video (default to a live one, or the first VOD)
const activeVideo = ref(null);

const selectVideo = (video) => {
    activeVideo.value = video;
};

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
    }

    // Initialize active video prioritizing LIVE
    const liveVideo = videos.value.find(v => v.status === 'live');
    if (liveVideo) {
        activeVideo.value = liveVideo;
    } else if (videos.value.length > 0) {
        activeVideo.value = videos.value[0];
    }
});

const getStatusColor = (status) => {
    switch (status) {
        case 'live': return 'bg-red-500 text-white';
        case 'processing': return 'bg-orange-500 text-white';
        default: return 'bg-slate-200 dark:bg-white/10 text-slate-700 dark:text-slate-300';
    }
};
const getStatusLabel = (status) => {
    switch (status) {
        case 'live': return 'EN VIVO';
        case 'processing': return 'Procesando...';
        default: return 'Recuperando VOD';
    }
};
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
            <main class="mt-24 p-8 lg:p-12 flex-1 relative z-10 w-full mx-auto max-w-[1700px]">

                <!-- Breadcrumbs -->
                <nav class="flex items-center gap-2 text-sm font-bold text-slate-500 dark:text-slate-400 mb-8">
                    <a href="/channels" class="hover:text-primary transition-colors flex items-center gap-1">
                        <span class="material-icons text-lg">arrow_back</span>
                        FoxFit TV
                    </a>
                    <span class="material-icons text-sm">chevron_right</span>
                    <span class="text-slate-800 dark:text-white uppercase tracking-widest text-xs">{{ channelObj.name
                        }}</span>
                </nav>

                <!-- Main Player and Chat Container -->
                <div class="flex flex-col lg:flex-row gap-8 items-start mb-12">

                    <!-- Left Column: Video Player Area -->
                    <div class="flex-1 w-full space-y-6">

                        <!-- Video Player Area -->
                        <div class="w-full aspect-video bg-black rounded-3xl overflow-hidden shadow-2xl relative group">
                            <!-- Real Stream HLS/VOD HTML5 Player -->
                            <div v-if="activeVideo" class="absolute inset-0">
                                <video v-if="activeVideo.file_path" :src="`/stream/${activeVideo.id}`"
                                    controlsList="nodownload pwa noplaybackrate"
                                    :autoplay="activeVideo.status === 'live'" controls
                                    class="w-full h-full object-contain bg-black z-10 relative">
                                </video>
                                <img v-else-if="activeVideo.thumbnail_path" :src="activeVideo.thumbnail_path"
                                    class="w-full h-full object-cover opacity-70">
                                <div v-else
                                    class="w-full h-full flex flex-col items-center justify-center bg-slate-900 border border-slate-800">
                                    <span
                                        class="material-icons text-6xl text-slate-700 animate-pulse mb-4">videocam</span>
                                    <span
                                        class="text-white text-sm font-bold uppercase tracking-widest text-slate-500">Transmisión
                                        terminada</span>
                                </div>
                            </div>
                            <div v-else
                                class="w-full h-full flex flex-col items-center justify-center bg-slate-900 border border-slate-800 p-12 text-center">
                                <span class="material-icons text-6xl text-slate-700 mb-4">tv_off</span>
                                <h3 class="text-xl font-bold text-white">No hay videos</h3>
                                <p class="text-slate-400 mt-2">Este canal no tiene transmisiones registradas aún.</p>
                            </div>
                        </div>

                        <!-- Channel Info & Current Video Details -->
                        <div v-if="activeVideo"
                            class="glass-panel p-8 rounded-3xl border border-slate-200 dark:border-white/5 shadow-xl shadow-slate-200/50 dark:shadow-none">
                            <h1
                                class="text-2xl lg:text-3xl items-center font-display font-black text-primary-dark dark:text-white uppercase tracking-tight italic mb-4">
                                {{ activeVideo.title }}
                            </h1>

                            <!-- Professor Bar -->
                            <div
                                class="flex items-center gap-4 pb-6 border-b border-slate-100 dark:border-white/5 mb-6">
                                <div
                                    class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary to-aqua-400 text-primary-dark flex items-center justify-center font-display font-black text-2xl shadow-lg border-[3px] border-white dark:border-slate-800">
                                    {{ channelObj.user?.name.charAt(0) || 'F' }}
                                </div>
                                <div>
                                    <h3 class="font-bold text-lg leading-tight">{{ channelObj.name }}</h3>
                                    <p class="text-sm text-slate-500 font-medium">Con {{ channelObj.user?.name }} • {{
                                        videos.length }} transmisiones</p>
                                </div>
                                <!-- Subscribe Button Placeholder -->
                                <button
                                    class="ml-auto bg-slate-800 dark:bg-white text-white dark:text-slate-900 px-6 py-2.5 rounded-xl font-bold text-sm tracking-wide flex items-center gap-2 hover:scale-105 transition-transform shadow-lg">
                                    <span class="material-icons text-[18px]">notifications</span>
                                    Seguir Canal
                                </button>
                            </div>

                            <!-- Video Description -->
                            <p
                                class="text-slate-600 dark:text-slate-300 font-medium max-w-4xl text-sm leading-relaxed whitespace-pre-line">
                                {{ activeVideo.description || channelObj.description || 'Sin descripción disponible.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Right Column: Live Chat (Only visible if Live or Desktop) -->
                    <div class="w-full lg:w-1/3 h-[500px] lg:h-[calc(100vh-140px)] lg:sticky lg:top-28">
                        <LiveChat :video-id="activeVideo ? activeVideo.id : null" :is-dark="isDark"
                            :current-user="authStore.user" />
                    </div>

                </div>

                <!-- VOD Section -->
                <div class="mt-16">
                    <h3
                        class="text-xl font-display font-black text-slate-800 dark:text-white uppercase tracking-widest mb-8 flex items-center gap-2">
                        <span class="material-icons text-primary">video_library</span>
                        Clases Anteriores
                    </h3>

                    <!-- Playlist Container -->
                    <div
                        class="glass-panel rounded-3xl border border-slate-200 dark:border-white/5 shadow-xl shadow-slate-200/50 dark:shadow-none flex-1 flex flex-col h-[800px] overflow-hidden">

                        <!-- Header -->
                        <div
                            class="p-6 border-b border-slate-100 dark:border-white/5 shrink-0 bg-slate-50/50 dark:bg-black/20">
                            <h3
                                class="font-display font-black text-lg uppercase tracking-widest text-primary-dark dark:text-white flex items-center gap-2">
                                <span class="material-icons text-primary">video_library</span>
                                Clases Anteriores
                            </h3>
                            <p class="text-xs text-slate-500 mt-1 font-medium">Selecciona una transmisión para
                                reproducir</p>
                        </div>

                        <!-- Scrollable List -->
                        <div class="flex-1 overflow-y-auto p-4 space-y-3 custom-scrollbar">

                            <button v-for="video in videos" :key="video.id" @click="selectVideo(video)"
                                class="w-full text-left p-3 rounded-2xl flex gap-4 transition-all duration-300 group relative border"
                                :class="activeVideo?.id === video.id ? 'bg-primary/10 border-primary shadow-sm' : 'bg-transparent border-transparent hover:bg-slate-50 dark:hover:bg-white/5'">

                                <!-- Miniature -->
                                <div
                                    class="w-32 rounded-xl overflow-hidden aspect-video bg-slate-200 dark:bg-slate-800 relative shrink-0 shadow-sm">
                                    <img v-if="video.thumbnail_path" :src="video.thumbnail_path"
                                        class="w-full h-full object-cover">
                                    <div v-if="video.status === 'vod' && video.duration"
                                        class="absolute bottom-1 right-1 bg-black/80 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-md backdrop-blur-sm">
                                        {{ video.duration }}
                                    </div>
                                    <div v-if="video.status === 'live'"
                                        class="absolute bottom-1 right-1 bg-red-500 text-white text-[9px] font-bold px-1.5 py-0.5 rounded-md flex items-center gap-1">
                                        <span class="w-1.5 h-1.5 rounded-full bg-white animate-pulse"></span> LIVE
                                    </div>
                                </div>

                                <!-- List Video Info -->
                                <div class="flex-1 min-w-0 flex flex-col justify-center">
                                    <h4 class="font-bold text-sm text-slate-800 dark:text-white leading-snug line-clamp-2 group-hover:text-primary transition-colors"
                                        :class="{ 'text-primary': activeVideo?.id === video.id }">
                                        {{ video.title }}
                                    </h4>
                                    <div class="flex items-center gap-2 mt-2">
                                        <span
                                            class="text-[10px] font-black uppercase tracking-widest px-2 py-0.5 rounded-md"
                                            :class="getStatusColor(video.status)">
                                            {{ getStatusLabel(video.status) }}
                                        </span>
                                    </div>

                                    <!-- Active indicator pip -->
                                    <div v-if="activeVideo?.id === video.id"
                                        class="absolute left-0 top-1/2 -translate-y-1/2 w-1.5 h-8 bg-primary rounded-r-md">
                                    </div>
                                </div>
                            </button>

                            <div v-if="videos.length === 0"
                                class="py-12 text-center text-slate-500 dark:text-slate-400 text-sm font-medium">
                                No hay historial disponible.
                            </div>

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

/* Custom Scrollbar for VOD playlist */
.custom-scrollbar::-webkit-scrollbar {
    width: 6px;
}

.custom-scrollbar::-webkit-scrollbar-track {
    background: transparent;
}

.custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(148, 163, 184, 0.3);
    border-radius: 10px;
}

.dark .custom-scrollbar::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.1);
}

.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: rgba(148, 163, 184, 0.6);
}
</style>
