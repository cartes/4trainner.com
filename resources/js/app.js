import "./bootstrap";
import Alpine from "alpinejs";
import $ from "jquery";
import "flowbite";
import { createApp } from "vue";
import { createPinia } from "pinia";
import Welcome from "./Components/Welcome.vue";
import StudentIndex from "./Components/Teacher/Students/Index.vue";
import StudentShow from "./Components/Teacher/Students/Show.vue";

window.Alpine = Alpine;
Alpine.start();

// Ensure jQuery is available globally
window.$ = $;
window.jQuery = $;

// Create Pinia instance
const pinia = createPinia();

const appElement = document.getElementById("app");
if (appElement) {
    const componentName = appElement.dataset.component;
    let component = Welcome;

    if (componentName === 'StudentIndex') {
        component = StudentIndex;
    } else if (componentName === 'StudentShow') {
        component = StudentShow;
    }

    const app = createApp(component, { ...appElement.dataset });
    app.use(pinia);
    app.mount("#app");
}
