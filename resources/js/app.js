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
import Settings from "./Components/Settings.vue";
import AdminUsers from "./Components/Admin/AdminUsers.vue";
import AdminSettings from "./Components/Admin/AdminSettings.vue";
import AdminAudit from "./Components/Admin/AdminAudit.vue";
import ChannelList from "./Components/Student/ChannelList.vue";
import ChannelPlayer from "./Components/Student/ChannelPlayer.vue";
import TrainerStudio from "./Components/Trainer/TrainerStudio.vue";

const components = {
    Welcome,
    Pricing,
    TrainerDashboard,
    StudentDashboard,
    ModeratorDashboard,
    SuperAdminDashboard,
    Settings,
    AdminUsers,
    AdminSettings,
    AdminAudit,
    ChannelList,
    ChannelPlayer,
    TrainerStudio,
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
