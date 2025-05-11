import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import { createPinia } from 'pinia';
import './assets/main.css'; // Import Tailwind CSS

// importar jQuery correctamente
//import $ from 'jquery';
//import 'https://code.jquery.com/jquery-3.4.1.slim.min.js';

// jQuery disponible globalmente 
//window.$ = $;

// Crear la instancia de Vue
const app = createApp(App);
// Crear la instancia de Pinia
const pinia = createPinia();

// Usar los plugins
app.use(pinia);
app.use(router);

// Montar la aplicación
app.mount('#app');
