<script setup>
import { ref, computed } from 'vue';
import LoginForm from './LoginForm.vue';

const props = defineProps({
    loginUrl: { type: String, default: '/login' },
    registerUrl: { type: String, default: '/register' },
    dashboardUrl: { type: String, default: '/dashboard' },
    canLogin: { type: String, default: 'true' },
    authCheck: { type: String, default: 'false' },
    canRegister: { type: String, default: 'true' },
    pricingUrl: { type: String, default: '/planes' }
});

const showLoginModal = ref(false);
const isLoggedIn = computed(() => props.authCheck === 'true');
const hasLogin = computed(() => props.canLogin === 'true');
const hasRegister = computed(() => props.canRegister === 'true');

const toggleDarkMode = () => {
    const isDark = document.documentElement.classList.toggle('dark');
    localStorage.setItem('theme', isDark ? 'dark' : 'light');
};

import { onMounted } from 'vue';
onMounted(() => {
    if (localStorage.getItem('theme') === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
        document.documentElement.classList.add('dark');
    } else {
        document.documentElement.classList.remove('dark');
    }
});
</script>

<template>
    <div
        class="bg-background-light dark:bg-background-dark text-slate-800 dark:text-slate-200 font-sans transition-colors duration-300">
        <!-- Login Modal -->
        <LoginForm v-if="showLoginModal" @close="showLoginModal = false" />
        <div class="bg-primary text-black text-center py-2 text-sm font-semibold tracking-wide">
            10% off online booking! Use the code <span class="font-bold">FOXFIT</span>
        </div>
        <nav class="sticky top-0 z-50 glass-nav border-b border-slate-200 dark:border-slate-800">
            <div class="max-w-7xl mx-auto px-6 h-20 flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div
                        class="w-10 h-10 bg-gradient-to-tr from-aqua-400 to-primary rounded-full flex items-center justify-center">
                        <span class="material-icons text-white">bolt</span>
                    </div>
                    <span
                        class="font-display text-2xl font-bold tracking-tight text-primary-dark dark:text-white">FoxFit</span>
                </div>
                <div class="hidden md:flex items-center gap-8 text-sm font-medium">
                    <a class="nav-link-aqua" href="#">Meet Emily</a>
                    <a class="nav-link-aqua" href="#">FoxFit</a>
                    <a :href="pricingUrl" class="nav-link-aqua">Plans & Pricing</a>
                    <a class="nav-link-aqua" href="#">Challenges</a>
                    <a class="nav-link-aqua" href="#">Book a Class</a>

                    <template v-if="hasLogin">
                        <a v-if="isLoggedIn" :href="dashboardUrl"
                            class="px-5 py-2.5 bg-primary-dark dark:bg-primary text-white dark:text-primary-dark rounded-full transition-all hover:scale-105">Dashboard</a>
                        <template v-else>
                            <a href="#" @click.prevent="showLoginModal = true" class="nav-link-aqua">Log in</a>
                            <a v-if="hasRegister" :href="pricingUrl"
                                class="px-5 py-2.5 bg-primary-dark dark:bg-primary text-white dark:text-primary-dark rounded-full transition-all hover:scale-105">Join
                                Now</a>
                        </template>
                    </template>

                    <button class="p-2 rounded-full hover:bg-slate-100 dark:hover:bg-slate-800" @click="toggleDarkMode">
                        <span class="material-icons text-xl">dark_mode</span>
                    </button>
                </div>
            </div>
        </nav>
        <header class="relative overflow-hidden hero-gradient">
            <div
                class="max-w-7xl mx-auto px-6 pt-12 lg:pt-24 pb-32 min-h-[700px] flex flex-col lg:flex-row items-center">
                <div class="lg:w-1/2 z-10 text-center lg:text-left">
                    <h1
                        class="font-display text-5xl lg:text-8xl font-bold text-primary-dark dark:text-white leading-[1.1] mb-6">
                        Empower Your <br />Fitness Journey
                    </h1>
                    <p class="text-lg lg:text-xl text-slate-600 dark:text-slate-400 mb-10 max-w-xl mx-auto lg:mx-0">
                        Discover your power through world-class online coaching. Access hundreds of premium classes
                        designed for
                        every level.
                    </p>
                    <div class="flex flex-col sm:flex-row items-center gap-4 justify-center lg:justify-start">
                        <div class="flex gap-4 mb-8 lg:mb-0">
                            <a class="p-3 bg-white dark:bg-slate-800 rounded-full shadow-sm hover:shadow-md transition-all"
                                href="#">
                                <img alt="Facebook" class="w-6 h-6 dark:invert"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuCukGrByWmFndsT6UqtlzGO48NdbjD342oxAfeJ8KipaE3fZLKVXSYHNH5lVU6LzqmZ73ULwyzDdVrAdih-DlvumEiT9wf76rAHpzVhdWsmFw9_bVrhA1k5pznehdt5kKBFarRTimR5adYBPNlcuqW23T-AOM9o-b3iDbcqN0k6g_fteiCu6ylqFTy-fMdaWP28o_GBoWmppu5isQyb_nYq79CtdTt0p4qM1G8yIe5TQ5JgMvVe417rnnFGteFTTbGWjCRK8F1Rwogh" />
                            </a>
                            <a class="p-3 bg-white dark:bg-slate-800 rounded-full shadow-sm hover:shadow-md transition-all"
                                href="#">
                                <img alt="Twitter" class="w-6 h-6 dark:invert"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuBAq7nafSF4vnU2jgaf5Vo-i3ysfum4LzyBY1oq0ImrhQoaHlsh0d7_bhPc7hmwFujhZX5ZecJJaLUPny7V4tPDQfQTWlr2KtuXTliIrVK39kYdOtDPQPC47lXMlcbJotvi1IJij967g5dnHhBq0ZQS0DG3TImaUTbJ7gT2larjeDt6GoY2tJVhoE6W0nhnLyUItmzt3kE47JdGiVIQtf7E019C1qluMR8Hk-lPIRGIotCkqXR5ki9E9CFsVKiXUE9iweRQnqSbvEVY" />
                            </a>
                            <a class="p-3 bg-white dark:bg-slate-800 rounded-full shadow-sm hover:shadow-md transition-all"
                                href="#">
                                <img alt="YouTube" class="w-6 h-6 dark:invert"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuDSSl9cIHonPfPgqtyRGIodo4vzztIM1h-92rUEzjiwCugf7g6NcPgLXGnwF-ESdihz1wBtRa2d-weAPChhszN-CMxKN5u5LAjEtEerWDSAaWGotvC4jhJZvh2C0MQH_NhtB346IV3Tf-PoxBC1NdKqgwdX9gRLnmfykcVr6NFHjuDCiJ-V-pEEwAhneyzk3z4_lmUDEUVX0n2IOTeJLRaq1uepGlUXpav5auoNcpH-eg-mJylzIpjDryLXQfjoj6hwhrJoCCSrS1QO" />
                            </a>
                            <a class="p-3 bg-white dark:bg-slate-800 rounded-full shadow-sm hover:shadow-md transition-all"
                                href="#">
                                <img alt="Instagram" class="w-6 h-6 dark:invert"
                                    src="https://lh3.googleusercontent.com/aida-public/AB6AXuBV1JqDXFJdP-hdBb3lcG5nHSKRHVj0lNi0kvR0VUFuh2YdkNZzjsqXBRwJvPCaQ-ZfwYE1qGDlhWZpIa2lr2Qesm8tKqtxEJB9VWHdG7fQ-ww9Bdw0fly_Zv5L4aQyCu_7uhbfQ2wnUqcYMAQr6Uxslv9xBUSas_02FlzccMETN6iVwkz6mfcIA9mfhMoYp9cPOu3qnaw42NHu2WHiFEniC8V4P-jkrM9vJ89ftw3qRmeqWEw9AdS54kbQeA3bXrEehTEJUugYjkzg" />
                            </a>
                        </div>
                    </div>
                </div>
                <div class="lg:w-1/2 relative mt-12 lg:mt-0 flex justify-end">
                    <img alt="Fitness Trainer" class="w-full max-w-lg object-cover mask-image-fade rounded-b-none"
                        src="https://lh3.googleusercontent.com/aida-public/AB6AXuA3S3R4fSaJp2exiDVOCFO23seJmfqkjLBwyX04gucTlHsFgHcWyGdbPMnUS4G0b_H3JteMb4iNuL0m2s2VZqY7puYcNopn3w4BDhyutFXdRDEU7dcNuLNPHwD1DmzJQYK57F-q5n3vZf7iDbB-_lOCFhnJGpBbCe-2V7rmwOIEGT-qwWEnKebZyF4bPibIWiGYO6cfklUja_IOKVa-E9qboAo5N6yjVDEXhVyoZR9sGQdonleRe5x3u1578z3x1HdiP1xKn7813_Wx"
                        style="mask-image: linear-gradient(to bottom, black 80%, transparent 100%);" />
                </div>
            </div>
            <div class="absolute bottom-0 left-0 w-full flex">
                <a class="flex-1 py-10 bg-aqua-200 dark:bg-aqua-500/20 text-center text-primary-dark dark:text-white font-semibold flex items-center justify-center gap-2 hover:bg-aqua-300 transition-colors"
                    href="#">
                    Discover FoxFit <span class="material-icons">chevron_right</span>
                </a>
                <a class="flex-1 py-10 bg-primary-dark dark:bg-slate-900 text-center text-white font-semibold flex items-center justify-center gap-2 hover:bg-slate-800 transition-colors"
                    href="#">
                    Book a Class <span class="material-icons">calendar_today</span>
                </a>
            </div>
        </header>
        <section class="py-24 bg-white dark:bg-slate-950">
            <div class="max-w-7xl mx-auto px-6">
                <div class="flex items-center justify-between mb-16">
                    <div>
                        <h2 class="text-4xl font-display font-bold text-slate-900 dark:text-white mb-4">Featured
                            Channels</h2>
                        <p class="text-slate-500 dark:text-slate-400">Find the perfect workout style for your mood and
                            goals.
                        </p>
                    </div>
                    <button
                        class="hidden md:flex items-center gap-2 text-aqua-500 font-semibold hover:gap-3 transition-all">
                        View All Categories <span class="material-icons">arrow_forward</span>
                    </button>
                </div>
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-8">
                    <div class="group cursor-pointer">
                        <div
                            class="relative aspect-square rounded-3xl overflow-hidden mb-4 ring-4 ring-transparent group-hover:ring-primary transition-all duration-300">
                            <img alt="Yoga"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuA2F8o3glov-EPt-yqlxjZzmy_X8kN8L9YV8mp1JJdrX2yIZpm2zxdL3xFAKr9OuEAU9061_k5gY59v-o4h0JEgZjOvnWE-HY1q1jnL7UHLV9Pn40coQ3mmN_UIe9GIkP-yi6cxeN_hSk4vHJe1sA8dhFiREkJQ_hH3fWGMmpTtXROiwAZq2WmVNZbMXtBWHa0T7YTugWd-wNGpHR9JQX0opsQTgO433I6XWz6WhaVUGxyA1IGbuQEw6QZUWibaYZEVeMzfXNNjWbVM" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        </div>
                        <h3 class="font-bold text-center group-hover:text-aqua-500 transition-colors">Yoga Flow</h3>
                    </div>
                    <div class="group cursor-pointer">
                        <div
                            class="relative aspect-square rounded-3xl overflow-hidden mb-4 ring-4 ring-transparent group-hover:ring-primary transition-all duration-300">
                            <img alt="HIIT"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCSGyPEQlcf2IeZWTGbB3k0pSXSOkYvcQDeVzyrq8GYLPg4VaLU3EnR1OvwiY93I8F4B_1_m2mTCwcnTeVbfphluMlhrqsdiiLka7y00fiesxwZLID097pwujFkKFnVys-Tl-G0CaMQ8HUyjcY2UR1wIbWxTV8pQmZF8BNO1QrRW4eW9VQsytr9dmb86rhVINFpTn1zSYm4zRXFyMMuHCLCvUK9brW0XkAO2Qd5jiwK8LhkPYQ4Q-3VAkjt4RTaupirtlmIVbwyGnKu" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        </div>
                        <h3 class="font-bold text-center group-hover:text-aqua-500 transition-colors">HIIT Cardio</h3>
                    </div>
                    <div class="group cursor-pointer">
                        <div
                            class="relative aspect-square rounded-3xl overflow-hidden mb-4 ring-4 ring-transparent group-hover:ring-primary transition-all duration-300">
                            <img alt="Strength"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuDUSDrMqUAn6guUnVLIgSBpunW5P5ZChCbZ4CE7ELjbuAoOEzEWCW_8gkwE80QdIqKQ6bUDcRGw00srNASwUC65Cnpmu_R5e-gLHtpDsBP7mLMwf0tUsCxBxIeU_m9HkrBFchDGqsSHfZgS8J5Ms1CnW-Q8oXbj-ej9JmSZblMBFu7FbZk25ZeKAx18aaO9vF_oSKN8kkxXoIw359h303gG1ig6f1BZP5ioJtIthgy0Mnsl4nDgzXN1bk9BfMPMotL1o4SYOVXbno_G" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        </div>
                        <h3 class="font-bold text-center group-hover:text-aqua-500 transition-colors">Strength Training
                        </h3>
                    </div>
                    <div class="group cursor-pointer">
                        <div
                            class="relative aspect-square rounded-3xl overflow-hidden mb-4 ring-4 ring-transparent group-hover:ring-primary transition-all duration-300">
                            <img alt="Pilates"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuCLU3udYx9AwMIirsQaMmIcRTElEnaoiTuUg7rtHDuGhOrj_UE0gbhilqlggV6vxg6g5jjD9wkVHQZKI6CmFBbMeuSx7DTQN3HvCGeNRw2Krzo2AaZYbt8-LSRgNwl5NYs0FgG0-x-dsz0EhpRkKJsn-pHfWQ1CWGNaXKOTvRxJIsC19HiBvNJqOrkwiuBAqSMEaNvFo_gBkgN2jQ7c2-EfD9zWnmvGC2JcVUq-Wz4atpqZo3m7KU9njCFj56eierMtZ8hYayEtdbAV" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        </div>
                        <h3 class="font-bold text-center group-hover:text-aqua-500 transition-colors">Pilates</h3>
                    </div>
                    <div class="group cursor-pointer">
                        <div
                            class="relative aspect-square rounded-3xl overflow-hidden mb-4 ring-4 ring-transparent group-hover:ring-primary transition-all duration-300">
                            <img alt="Meditation"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuAY6zL7STel9VmsabjOEX8Bz-izAKWUmGCFhQ6EzcodgzlSnPrzDAAgVypcls5zcNmIVr_w_g9BUxSF-x7XdWiEXXWwLSGBUALfsui19VlRHrG6PAgCByQ4rLIPCYwe56krcGff9euzG4Gyea_mDGpns-K87WZXnIYdyl-aMl1AhHYZK0tVh4_6csB_jPLq_eSH6yPedfeYG6xAmUe7k-WGjEWww7VQCHovbLlt2boa6C6-qHdqbZ2dL4IXR0PMiWkp3L7MnbPa0nVw" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        </div>
                        <h3 class="font-bold text-center group-hover:text-aqua-500 transition-colors">Meditation</h3>
                    </div>
                    <div class="group cursor-pointer">
                        <div
                            class="relative aspect-square rounded-3xl overflow-hidden mb-4 ring-4 ring-transparent group-hover:ring-primary transition-all duration-300">
                            <img alt="Running"
                                class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500"
                                src="https://lh3.googleusercontent.com/aida-public/AB6AXuD2eay5eMyvboCfDUSHXhKCXEfHL4uYxi7LK1PajTBjs154xRITMOWY9Iu1vHsbCyfO5WtoF7gHjGO2Eo0aY1Q0Yv8LFfQgSAwDLCXA5YnH14WKdYvuq4NQRzUkTHIlLFevXnmTGitSsRrsed6bEw1qzKn5QFVa_uF3ADLnOzYgJZhjLRNy5zO8lbPRtbSNEUluhbMsbB8xNuAjX9v7WyM5ZsmNpTyB4rXBReV0OlWE6rol33FIxNOucTiq0GXEUVRp8WS2ATp3D_DN" />
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent"></div>
                        </div>
                        <h3 class="font-bold text-center group-hover:text-aqua-500 transition-colors">Running</h3>
                    </div>
                </div>
            </div>
        </section>
        <section class="py-20 bg-slate-50 dark:bg-slate-900/50">
            <div class="max-w-7xl mx-auto px-6 text-center">
                <p class="uppercase tracking-widest text-sm font-bold text-slate-400 dark:text-slate-500 mb-12">Trusted
                    by
                    50,000+ members worldwide</p>
                <div
                    class="flex flex-wrap justify-center items-center gap-12 opacity-50 grayscale hover:grayscale-0 transition-all">
                    <span class="text-2xl font-display font-black text-slate-800 dark:text-white">FITBIT</span>
                    <span class="text-2xl font-display font-black text-slate-800 dark:text-white">NIKE</span>
                    <span class="text-2xl font-display font-black text-slate-800 dark:text-white">PELOTON</span>
                    <span class="text-2xl font-display font-black text-slate-800 dark:text-white">ADIDAS</span>
                    <span class="text-2xl font-display font-black text-slate-800 dark:text-white">LULULEMON</span>
                </div>
            </div>
        </section>
        <footer class="bg-primary-dark text-white py-20">
            <div class="max-w-7xl mx-auto px-6">
                <div class="grid md:grid-cols-4 gap-12">
                    <div class="col-span-1 md:col-span-2">
                        <div class="flex items-center gap-3 mb-8">
                            <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center">
                                <span class="material-icons text-primary-dark">bolt</span>
                            </div>
                            <span class="font-display text-2xl font-bold tracking-tight">FoxFit</span>
                        </div>
                        <p class="text-slate-400 max-w-sm mb-8">
                            Transforming lives through digital fitness since 2020. Join our community and unleash your
                            potential
                            with Emily Fox.
                        </p>
                        <div class="flex gap-4">
                            <a class="w-10 h-10 rounded-full border border-slate-700 flex items-center justify-center hover:bg-primary hover:border-primary hover:text-primary-dark transition-all"
                                href="#">
                                <i class="material-icons text-sm">facebook</i>
                            </a>
                            <a class="w-10 h-10 rounded-full border border-slate-700 flex items-center justify-center hover:bg-primary hover:border-primary hover:text-primary-dark transition-all"
                                href="#">
                                <i class="material-icons text-sm">alternate_email</i>
                            </a>
                        </div>
                    </div>
                    <div>
                        <h4 class="font-bold mb-6">Quick Links</h4>
                        <ul class="space-y-4 text-slate-400">
                            <li><a class="hover:text-primary transition-colors" href="#">Workouts</a></li>
                            <li><a class="hover:text-primary transition-colors" href="#">Nutrition Plans</a></li>
                            <li><a class="hover:text-primary transition-colors" href="#">Community</a></li>
                            <li><a class="hover:text-primary transition-colors" href="#">Support</a></li>
                        </ul>
                    </div>
                    <div>
                        <h4 class="font-bold mb-6">Newsletter</h4>
                        <p class="text-slate-400 text-sm mb-4">Get weekly fitness tips and special offers.</p>
                        <form class="flex flex-col gap-3">
                            <input
                                class="bg-slate-800 border-none rounded-lg px-4 py-3 focus:ring-2 focus:ring-primary transition-all"
                                placeholder="Your email address" type="email" />
                            <button
                                class="bg-primary text-primary-dark font-bold py-3 rounded-lg hover:bg-white transition-all">Subscribe</button>
                        </form>
                    </div>
                </div>
                <div class="mt-20 pt-8 border-t border-slate-800 text-center text-slate-500 text-sm">
                    © 2023 FoxFit Video Management. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
</template>

<style scoped>
/* Aqua Hover Effect - Animated Underline */
.nav-link-aqua {
    position: relative;
    display: inline-block;
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
    background: linear-gradient(90deg, #5fa9b8, #92cbd4, #438d9c);
    transition: width 0.4s cubic-bezier(0.25, 0.46, 0.45, 0.94);
}

.nav-link-aqua:hover {
    color: #5fa9b8;
}

.nav-link-aqua:hover::after {
    width: 100%;
}

/* Glass Navigation Effect */
.glass-nav {
    background: rgba(248, 250, 252, 0.8);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}

.dark .glass-nav {
    background: rgba(15, 23, 42, 0.8);
}
</style>
