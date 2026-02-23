<script setup>
import { ref, onMounted, onUnmounted, nextTick } from 'vue';
import api from '../../services/api';

const props = defineProps({
    videoId: {
        type: [Number, String],
        default: null
    },
    currentUser: {
        type: Object,
        required: true
    },
    isDark: {
        type: Boolean,
        default: false
    }
});

const messages = ref([]);
const newMessage = ref('');
const isSubmitting = ref(false);
const chatContainer = ref(null);
let pollInterval = null;

const fetchMessages = async () => {
    if (!props.videoId) return;
    try {
        const response = await api.get(`/videos/${props.videoId}/chat`);

        // Only trigger scroll if we received NEW messages to prevent annoying auto-scrolls
        const prevCount = messages.value.length;
        messages.value = response.data.data;

        if (messages.value.length > prevCount) {
            scrollToBottom();
        }
    } catch (error) {
        console.error('Error fetching chat messages:', error);
    }
};

const sendMessage = async () => {
    if (!newMessage.value.trim() || isSubmitting.value || !props.videoId) return;

    isSubmitting.value = true;
    try {
        const response = await api.post(`/videos/${props.videoId}/chat`, {
            message: newMessage.value
        });

        messages.value.push(response.data.data);
        newMessage.value = '';
        scrollToBottom();
    } catch (error) {
        console.error('Error sending message:', error);
    } finally {
        isSubmitting.value = false;
    }
};

const scrollToBottom = async () => {
    await nextTick();
    if (chatContainer.value) {
        chatContainer.value.scrollTop = chatContainer.value.scrollHeight;
    }
};

const formatTime = (isoString) => {
    const date = new Date(isoString);
    return date.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });
};

// Start Polling
onMounted(() => {
    fetchMessages();
    pollInterval = setInterval(fetchMessages, 3000); // Poll every 3 seconds
});

onUnmounted(() => {
    if (pollInterval) clearInterval(pollInterval);
});

</script>

<template>
    <div
        class="flex flex-col h-full bg-white dark:bg-slate-900 border border-slate-200 dark:border-white/10 rounded-3xl overflow-hidden shadow-2xl relative">

        <!-- Chat Header -->
        <div
            class="px-6 py-4 bg-slate-50 dark:bg-white/5 border-b border-slate-200 dark:border-white/5 flex items-center justify-between">
            <h3
                class="font-display font-black uppercase tracking-widest text-slate-800 dark:text-white text-sm flex items-center gap-2">
                <span class="material-icons text-primary">chat</span>
                Chat en Vivo
            </h3>
            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase flex items-center gap-1">
                <span class="w-1.5 h-1.5 rounded-full" :class="videoId ? 'bg-green-500' : 'bg-slate-300'"></span>
                {{ videoId ? 'Conectado' : 'Esperando Video' }}
            </span>
        </div>

        <!-- Messages Area -->
        <div ref="chatContainer" class="flex-1 overflow-y-auto p-6 space-y-4 scroll-smooth">
            <div v-if="!videoId" class="h-full flex flex-col items-center justify-center text-center opacity-50">
                <span class="material-icons text-4xl mb-2 text-slate-400">hourglass_empty</span>
                <p class="text-xs font-bold uppercase tracking-widest text-slate-500">El chat iniciará con el video</p>
            </div>
            <div v-else-if="messages.length === 0" class="h-full flex items-center justify-center">
                <p class="text-xs font-bold uppercase tracking-widest text-slate-400">Sé el primero en saludar 👋</p>
            </div>

            <!-- Message Bubbles -->
            <div v-for="msg in messages" :key="msg.id" class="flex flex-col"
                :class="msg.user_id === currentUser.id ? 'items-end' : 'items-start'">
                <div class="flex items-center gap-2 mb-1"
                    :class="msg.user_id === currentUser.id ? 'flex-row-reverse' : 'flex-row'">
                    <span class="text-[10px] font-black uppercase text-slate-400 tracking-wider">
                        {{ msg.user.id === currentUser.id ? 'Tú' : msg.user.name.split(' ')[0] }}
                    </span>
                    <span v-if="msg.user.roles && msg.user.roles[0]?.name === 'profesor'"
                        class="px-1.5 py-0.5 rounded text-[8px] font-black uppercase bg-primary/20 text-primary-dark tracking-widest">
                        Profesor
                    </span>
                    <span class="text-[9px] font-medium text-slate-300 dark:text-slate-600">{{
                        formatTime(msg.created_at) }}</span>
                </div>

                <div class="px-4 py-2.5 rounded-2xl max-w-[85%] text-sm font-medium shadow-sm transition-all"
                    :class="msg.user_id === currentUser.id ? 'bg-primary text-white rounded-tr-sm' : 'bg-slate-100 dark:bg-white/5 text-slate-700 dark:text-slate-300 rounded-tl-sm'">
                    {{ msg.message }}
                </div>
            </div>
        </div>

        <!-- Input Area -->
        <div class="p-4 bg-slate-50 dark:bg-black/20 border-t border-slate-200 dark:border-white/5">
            <form @submit.prevent="sendMessage" class="relative">
                <input v-model="newMessage" type="text" placeholder="Escribe un mensaje..."
                    :disabled="!videoId || isSubmitting"
                    class="w-full bg-white dark:bg-slate-800 border-none rounded-xl pl-4 pr-12 py-3 text-sm focus:ring-2 focus:ring-primary/50 outline-none text-slate-700 dark:text-white placeholder-slate-400 disabled:opacity-50 transition">
                <button type="submit" :disabled="!newMessage.trim() || !videoId || isSubmitting"
                    class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-lg bg-primary text-white hover:bg-primary-dark hover:scale-105 disabled:opacity-50 disabled:hover:scale-100 transition-all">
                    <span class="material-icons text-[16px]">send</span>
                </button>
            </form>
        </div>

    </div>
</template>

<style scoped>
/* Custom Scrollbar */
::-webkit-scrollbar {
    width: 6px;
}

::-webkit-scrollbar-track {
    background: transparent;
}

::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}

.dark ::-webkit-scrollbar-thumb {
    background: #334155;
}
</style>
