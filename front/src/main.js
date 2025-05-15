import { createApp } from 'vue';
import App from './App.vue';
import router from './router';
import { createPinia } from 'pinia';
import './assets/main.css'; // Import Tailwind CSS

// Crear la instancia de Vue
const app = createApp(App);
// Crear la instancia de Pinia
const pinia = createPinia();

// Usar los plugins
app.use(pinia);
app.use(router);

// Montar la aplicación
app.mount('#app');
