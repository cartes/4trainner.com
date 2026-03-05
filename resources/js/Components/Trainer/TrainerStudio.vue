<script setup>
import { ref, onMounted, onUnmounted, computed } from 'vue';
import axios from 'axios';
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

// Live status polling
let pollInterval = null;

const pollStatus = async () => {
    try {
        const { data } = await axios.get('/api/v1/trainer/channel/status');
        channelObj.value.is_live = data.is_live;
    } catch (_) { /* silent */ }
};

// Stream key visibility toggle
const showKey = ref(false);

// VOD list (loaded from API on mount)
const vodList = ref(channelObj.value.videos ?? []);
const loadingVods = ref(false);

const loadChannel = async () => {
    loadingVods.value = true;
    try {
        const { data } = await axios.get('/api/v1/trainer/channel');
        channelObj.value = data.channel;
        vodList.value = data.channel.videos ?? [];
    } catch (_) { /* silent */ } finally {
        loadingVods.value = false;
    }
};

// VOD Upload
const uploadForm = ref({ title: '', description: '', file: null });
const uploading = ref(false);
const uploadProgress = ref(0);
const uploadError = ref('');
const uploadSuccess = ref('');

const onFileChange = (e) => {
    uploadForm.value.file = e.target.files[0] ?? null;
};

const submitUpload = async () => {
    if (!uploadForm.value.file || !uploadForm.value.title) {
        uploadError.value = 'El título y el archivo son obligatorios.';
        return;
    }
    uploadError.value = '';
    uploadSuccess.value = '';
    uploading.value = true;
    uploadProgress.value = 0;

    const formData = new FormData();
    formData.append('title', uploadForm.value.title);
    formData.append('description', uploadForm.value.description);
    formData.append('video', uploadForm.value.file);

    try {
        const { data } = await axios.post('/api/v1/trainer/channel/videos', formData, {
            headers: { 'Content-Type': 'multipart/form-data' },
            onUploadProgress: (e) => {
                uploadProgress.value = Math.round((e.loaded * 100) / e.total);
            },
        });
        vodList.value.unshift(data.video);
        uploadSuccess.value = '¡Video subido correctamente!';
        uploadForm.value = { title: '', description: '', file: null };
        uploadProgress.value = 0;
    } catch (err) {
        uploadError.value = err.response?.data?.message ?? 'Error al subir el video.';
    } finally {
        uploading.value = false;
    }
};

// Delete VOD
const deletingId = ref(null);
const deleteVideo = async (video) => {
    if (!confirm(`¿Eliminar "${video.title}"?`)) return;
    deletingId.value = video.id;
    try {
        await axios.delete(`/api/v1/trainer/channel/videos/${video.id}`);
        vodList.value = vodList.value.filter(v => v.id !== video.id);
    } catch (_) {
        alert('No se pudo eliminar el video.');
    } finally {
        deletingId.value = null;
    }
};

const copyKey = () => {
    navigator.clipboard.writeText(channelObj.value.stream_key);
    alert('¡Clave copiada al portapapeles!');
};

const copyRtmp = () => {
    navigator.clipboard.writeText('rtmp://stream.4trainner.com/live');
    alert('¡URL RTMP copiada al portapapeles!');
};

const formatBytes = (bytes) => {
    if (!bytes) return '—';
    if (bytes < 1024 * 1024) return (bytes / 1024).toFixed(1) + ' KB';
    return (bytes / (1024 * 1024)).toFixed(1) + ' MB';
};

const activeVideo = computed(() => vodList.value.find(v => v.status === 'live') ?? null);

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

    // Start polling live status every 5 seconds
    pollStatus();
    pollInterval = setInterval(pollStatus, 5000);

    // Load full channel data (VODs)
    loadChannel();
});

onUnmounted(() => {
    clearInterval(pollInterval);
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

                <!-- Left: Control Room -->
                <div class="w-full lg:w-2/3 flex flex-col gap-6">
                    <!-- Header -->
                    <div class="flex items-center justify-between mb-2">
                        <div class="flex items-center gap-4">
                            <a href="/trainer/dashboard"
                                class="w-10 h-10 rounded-full bg-slate-100 dark:bg-white/5 flex items-center justify-center hover:bg-slate-200 dark:hover:bg-white/10 transition text-slate-500">
                                <span class="material-icons">arrow_back</span>
                            </a>
                            <h1
                                class="text-4xl font-display font-black text-primary-dark dark:text-white uppercase tracking-tight italic">
                                <span class="text-primary">4</span><span class="text-slate-600 dark:text-slate-400 italic">TRAINER</span> <span class="text-primary not-italic">STUDIO</span>
                            </h1>
                        </div>
                        <!-- Live badge (auto-updates via polling) -->
                        <div v-if="channelObj.is_live"
                            class="flex items-center gap-2 bg-red-500/90 backdrop-blur-md px-4 py-2 rounded-full shadow-lg shadow-red-500/20">
                            <span class="w-2.5 h-2.5 rounded-full bg-white animate-pulse"></span>
                            <span class="text-[12px] font-black tracking-widest text-white uppercase">Transmitiendo</span>
                        </div>
                        <div v-else
                            class="flex items-center gap-2 bg-slate-200 dark:bg-slate-800 px-4 py-2 rounded-full border border-slate-300 dark:border-white/10">
                            <span class="w-2.5 h-2.5 rounded-full bg-slate-400"></span>
                            <span class="text-[12px] font-black tracking-widest text-slate-500 dark:text-slate-400 uppercase">Fuera de Línea</span>
                        </div>
                    </div>

                    <!-- Connection Credentials -->
                    <div
                        class="glass-panel rounded-3xl p-8 border border-slate-200 dark:border-white/5 shadow-xl relative overflow-hidden group">
                        <div class="absolute inset-0 bg-gradient-to-r from-primary/5 to-transparent pointer-events-none"></div>
                        <h3 class="text-sm font-black uppercase tracking-widest text-slate-400 mb-6">Credenciales de Conexión (OBS)</h3>

                        <div class="space-y-4 relative z-10">
                            <!-- RTMP URL -->
                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-bold text-slate-500">Servidor RTMP</span>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-slate-100 dark:bg-slate-900/50 p-3 rounded-xl border border-slate-200 dark:border-white/10 text-sm font-mono text-primary-dark dark:text-gray-300">
                                        rtmp://stream.4trainner.com/live
                                    </div>
                                    <button @click="copyRtmp"
                                        class="h-[46px] px-4 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-white font-bold rounded-xl hover:bg-slate-300 dark:hover:bg-slate-600 transition flex items-center gap-1 text-sm">
                                        <span class="material-icons text-sm">content_copy</span>
                                    </button>
                                </div>
                            </div>
                            <!-- Stream Key -->
                            <div class="flex flex-col gap-1">
                                <span class="text-xs font-bold text-slate-500">Clave de Transmisión</span>
                                <div class="flex items-center gap-2">
                                    <div class="flex-1 bg-slate-100 dark:bg-slate-900/50 p-3 rounded-xl border border-slate-200 dark:border-white/10 text-sm font-mono text-primary dark:text-gray-300 truncate">
                                        {{ showKey ? channelObj.stream_key : '●●●●●●●●●●●●●●●●●●●●●●●●●●●●' }}
                                    </div>
                                    <button @click="showKey = !showKey"
                                        class="h-[46px] px-4 bg-slate-200 dark:bg-slate-700 text-slate-700 dark:text-white font-bold rounded-xl hover:bg-slate-300 dark:hover:bg-slate-600 transition flex items-center gap-1 text-sm"
                                        :title="showKey ? 'Ocultar' : 'Mostrar'">
                                        <span class="material-icons text-sm">{{ showKey ? 'visibility_off' : 'visibility' }}</span>
                                    </button>
                                    <button @click="copyKey"
                                        class="h-[46px] px-6 bg-primary text-white font-bold rounded-xl shadow-lg hover:bg-primary-dark transition flex items-center gap-2">
                                        <span class="material-icons text-sm">content_copy</span> COPIAR
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Stream Monitor -->
                    <div
                        class="rounded-3xl overflow-hidden mt-2 bg-black aspect-video relative shadow-2xl border border-slate-800 flex items-center justify-center">
                        <div v-if="!channelObj.is_live" class="text-center">
                            <span class="material-icons text-white/20 text-7xl mb-4 block">videocam_off</span>
                            <p class="text-white/50 font-bold uppercase tracking-widest text-sm">Esperando Recepción de Video...</p>
                        </div>
                        <video v-else-if="activeVideo && activeVideo.status === 'live'"
                            class="w-full h-full object-cover" controls autoplay muted>
                            <source :src="`/api/stream/${activeVideo.id}`" type="application/x-mpegURL">
                        </video>
                    </div>

                    <!-- VOD Management -->
                    <div class="glass-panel rounded-3xl p-8 border border-slate-200 dark:border-white/5 shadow-xl">
                        <h3 class="text-sm font-black uppercase tracking-widest text-slate-400 mb-6">Videos Grabados (VOD)</h3>

                        <!-- Upload Form -->
                        <div class="bg-slate-50 dark:bg-slate-900/40 rounded-2xl p-6 mb-6 border border-slate-200 dark:border-white/5">
                            <h4 class="text-sm font-bold text-slate-600 dark:text-slate-300 mb-4">Subir nuevo video</h4>
                            <div class="space-y-3">
                                <input v-model="uploadForm.title" type="text" placeholder="Título del video *"
                                    class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50" />
                                <textarea v-model="uploadForm.description" placeholder="Descripción (opcional)" rows="2"
                                    class="w-full bg-white dark:bg-slate-800 border border-slate-200 dark:border-white/10 rounded-xl px-4 py-3 text-sm focus:outline-none focus:ring-2 focus:ring-primary/50 resize-none"></textarea>
                                <input @change="onFileChange" type="file" accept=".mp4,.mov,.avi,.mkv"
                                    class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-bold file:bg-primary file:text-white hover:file:bg-primary-dark cursor-pointer" />
                                <!-- Progress bar -->
                                <div v-if="uploading" class="w-full bg-slate-200 dark:bg-slate-700 rounded-full h-2 overflow-hidden">
                                    <div class="bg-primary h-2 rounded-full transition-all duration-300" :style="{ width: uploadProgress + '%' }"></div>
                                </div>
                                <div v-if="uploadError" class="text-red-500 text-sm">{{ uploadError }}</div>
                                <div v-if="uploadSuccess" class="text-green-500 text-sm font-bold">{{ uploadSuccess }}</div>
                                <button @click="submitUpload" :disabled="uploading"
                                    class="px-6 py-3 bg-primary text-white font-bold rounded-xl hover:bg-primary-dark transition disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 text-sm">
                                    <span class="material-icons text-sm">cloud_upload</span>
                                    {{ uploading ? `Subiendo... ${uploadProgress}%` : 'Subir Video' }}
                                </button>
                            </div>
                        </div>

                        <!-- VOD List -->
                        <div v-if="loadingVods" class="text-center py-8 text-slate-400">
                            <span class="material-icons animate-spin text-3xl">autorenew</span>
                        </div>
                        <div v-else-if="vodList.length === 0" class="text-center py-8 text-slate-400">
                            <span class="material-icons text-4xl mb-2 block">video_library</span>
                            <p class="text-sm">No hay videos grabados aún.</p>
                        </div>
                        <div v-else class="space-y-3">
                            <div v-for="video in vodList" :key="video.id"
                                class="flex items-center gap-4 bg-white dark:bg-slate-800/50 rounded-2xl p-4 border border-slate-100 dark:border-white/5">
                                <span class="material-icons text-slate-400">{{ video.status === 'live' ? 'live_tv' : 'ondemand_video' }}</span>
                                <div class="flex-1 min-w-0">
                                    <p class="font-bold text-sm truncate">{{ video.title }}</p>
                                    <p class="text-xs text-slate-400">{{ formatBytes(video.size) }} · {{ video.duration ?? '—' }}</p>
                                </div>
                                <span :class="{
                                    'bg-red-100 text-red-600 dark:bg-red-500/20 dark:text-red-400': video.status === 'live',
                                    'bg-green-100 text-green-600 dark:bg-green-500/20 dark:text-green-400': video.status === 'vod',
                                    'bg-yellow-100 text-yellow-700 dark:bg-yellow-500/20 dark:text-yellow-400': video.status === 'processing',
                                }" class="text-[11px] font-black uppercase tracking-widest px-3 py-1 rounded-full">
                                    {{ video.status }}
                                </span>
                                <button @click="deleteVideo(video)" :disabled="deletingId === video.id"
                                    class="w-9 h-9 rounded-full flex items-center justify-center text-slate-400 hover:text-red-500 hover:bg-red-50 dark:hover:bg-red-500/10 transition disabled:opacity-40">
                                    <span class="material-icons text-sm">{{ deletingId === video.id ? 'hourglass_empty' : 'delete' }}</span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Live Chat -->
                <div class="w-full lg:w-1/3 h-[calc(100vh-140px)] sticky top-28 hidden lg:block">
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

