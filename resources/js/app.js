import './bootstrap';
import Alpine from 'alpinejs';
import $ from 'jquery';

window.Alpine = Alpine;

Alpine.start();

// Asegurarse de que jQuery esté disponible globalmente
window.$ = $;
window.jQuery = $;
