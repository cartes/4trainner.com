<script setup>
import { computed } from 'vue';

const props = defineProps({
    authCheck: { type: String, default: 'false' },
    loginUrl: { type: String, default: '/login' },
    registerUrl: { type: String, default: '/register' },
    dashboardUrl: { type: String, default: '/dashboard' }
});

const isLoggedIn = computed(() => props.authCheck === 'true');

const toggleDarkMode = () => {
    document.documentElement.classList.toggle('dark');
};
</script>

<template>
    <div
        class="min-h-screen bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-200 font-sans transition-colors duration-300">
        <!-- Aqua Background Accents -->
        <div class="fixed top-0 left-0 w-full h-full overflow-hidden -z-10 pointer-events-none">
            <div
                class="absolute -top-[10%] -left-[10%] opacity-20 dark:opacity-30 blur-[120px] w-[50%] h-[50%] bg-aqua-400 rounded-full">
            </div>
            <div
                class="absolute top-[60%] -right-[5%] opacity-10 dark:opacity-20 blur-[100px] w-[40%] h-[40%] bg-primary rounded-full">
            </div>
        </div>

        <!-- Navigation Placeholder (Keeping standard bar for consistency) -->
        <nav class="sticky top-0 z-50 glass-nav border-b border-slate-200 dark:border-slate-800">
            <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
                <a href="/" class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-gradient-to-tr from-aqua-400 to-primary rounded-full flex items-center justify-center">
                        <span class="material-icons text-white">bolt</span>
                    </div>
                    <span
                        class="font-display text-2xl font-bold tracking-tight text-primary-dark dark:text-white">FoxFit</span>
                </a>
                <div class="flex items-center gap-6">
                    <a href="/" class="nav-link-aqua font-medium text-sm">Volver al Inicio</a>
                    <button class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800" @click="toggleDarkMode">
                        <span class="material-icons text-xl">dark_mode</span>
                    </button>
                </div>
            </div>
        </nav>

        <main class="max-w-7xl mx-auto px-6 py-20 relative">
            <div class="text-center mb-16 space-y-4">
                <h1
                    class="text-5xl md:text-7xl font-display font-extrabold tracking-tight text-slate-900 dark:text-white uppercase">
                    Elige tu <span
                        class="text-transparent bg-clip-text bg-gradient-to-r from-aqua-400 to-aqua-600">Plan</span>
                </h1>
                <p class="text-xl text-slate-600 dark:text-slate-400 max-w-2xl mx-auto font-medium">
                    Prepárate para llevar tu entrenamiento al siguiente nivel con nuestras membresías premium.
                </p>
            </div>

            <!-- Pricing Grid Placeholder -->
            <div class="grid md:grid-cols-3 gap-8 mt-12">
                <div v-for="i in 3" :key="i" class="glass-card p-1 rounded-[32px] overflow-hidden group">
                    <div
                        class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl rounded-[31px] p-8 h-full border border-white/20 dark:border-slate-800/50 transition-all group-hover:border-aqua-400/50">
                        <div class="h-40 bg-slate-100 dark:bg-slate-800 rounded-2xl mb-6 animate-pulse"></div>
                        <div class="h-8 bg-slate-100 dark:bg-slate-800 rounded-lg w-1/2 mb-4 animate-pulse"></div>
                        <div class="h-4 bg-slate-100 dark:bg-slate-800 rounded-lg w-full mb-2 animate-pulse"></div>
                        <div class="h-4 bg-slate-100 dark:bg-slate-800 rounded-lg w-3/4 mb-12 animate-pulse"></div>
                        <div class="h-14 bg-slate-200 dark:bg-slate-700/50 rounded-xl w-full animate-pulse"></div>
                    </div>
                </div>
            </div>

            <!-- Coming Soon Overlay Effect -->
            <div class="mt-20 text-center">
                <div
                    class="inline-flex items-center gap-3 px-6 py-3 bg-aqua-400/10 border border-aqua-400/30 rounded-full text-aqua-600 dark:text-aqua-400 font-bold uppercase tracking-widest text-sm animate-pulse">
                    <span class="material-icons text-sm">rocket_launch</span>
                    Próximamente - Fase 4
                </div>
            </div>
        </main>
    </div>
</template>

<style scoped>
.glass-nav {
    background: rgba(248, 250, 252, 0.8);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}

.dark .glass-nav {
    background: rgba(15, 23, 42, 0.8);
}

.glass-card {
    background: linear-gradient(135deg, rgba(255, 255, 255, 0.2), rgba(255, 255, 255, 0.05));
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.1);
}

.dark .glass-card {
    background: linear-gradient(135deg, rgba(95, 169, 184, 0.1), rgba(15, 23, 42, 0.4));
    box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
}

.nav-link-aqua {
    position: relative;
    color: inherit;
    text-decoration: none;
    transition: color 0.3s ease;
}

.nav-link-aqua::after {
    content: '';
    position: absolute;
    bottom: -4px;
    left: 0;
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, #5fa9b8, #92cbd4);
    transition: width 0.3s ease;
}

.nav-link-aqua:hover {
    color: #5fa9b8;
}

.nav-link-aqua:hover::after {
    width: 100%;
}

.font-display {
    font-family: 'Outfit', sans-serif;
}
</style>
