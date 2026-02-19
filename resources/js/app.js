import "./bootstrap";
import Alpine from "alpinejs";
import $ from "jquery";
import "flowbite";
import { createApp } from "vue";
import { createPinia } from "pinia";

// Import components
import Welcome from "./Components/Welcome.vue";
import Pricing from "./Components/Pricing.vue";
import TrainerDashboard from "./Components/TrainerDashboard.vue";
import StudentDashboard from "./Components/StudentDashboard.vue";
import ModeratorDashboard from "./Components/ModeratorDashboard.vue";
import SuperAdminDashboard from "./Components/SuperAdminDashboard.vue";

// New Teacher components from main
import StudentIndex from "./Components/Teacher/Students/Index.vue";
import StudentShow from "./Components/Teacher/Students/Show.vue";

const components = {
    Welcome,
    Pricing,
    TrainerDashboard,
    StudentDashboard,
    ModeratorDashboard,
    SuperAdminDashboard,
    StudentIndex,
    StudentShow,
};

window.Alpine = Alpine;
Alpine.start();

// Ensure jQuery is available globally
window.$ = $;
window.jQuery = $;

// Create Pinia instance
const pinia = createPinia();

const appElement = document.getElementById("app");
if (appElement) {
    const componentName = appElement.dataset.component || "Welcome";
    const Component = components[componentName];

    if (Component) {
        const app = createApp(Component, { ...appElement.dataset });
        app.use(pinia);
        app.mount("#app");
    } else {
        console.error(`Component "${componentName}" not found in registry.`);
    }
}
