import "./bootstrap";
import Alpine from "alpinejs";
import $ from "jquery";
import "flowbite";
import { createApp } from "vue";
import { createPinia } from "pinia";
import Welcome from "./Components/Welcome.vue";

window.Alpine = Alpine;
Alpine.start();

// Ensure jQuery is available globally
window.$ = $;
window.jQuery = $;

// Create Pinia instance
const pinia = createPinia();

const appElement = document.getElementById("app");
if (appElement) {
    const app = createApp(Welcome, { ...appElement.dataset });
    app.use(pinia);
    app.mount("#app");
}
