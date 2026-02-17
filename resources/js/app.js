import './bootstrap';
import Alpine from 'alpinejs';
import $ from 'jquery';
import 'flowbite';
import { createApp } from 'vue';
import Welcome from './Components/Welcome.vue';

window.Alpine = Alpine;

Alpine.start();

// Ensure jQuery is available globally
window.$ = $;
window.jQuery = $;

const appElement = document.getElementById('app');
if (appElement) {
    const app = createApp(Welcome, { ...appElement.dataset });
    app.mount('#app');
}
