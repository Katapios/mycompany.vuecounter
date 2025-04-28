import { createApp } from 'vue'
import App from './App.vue'

// Дождись готовности BX, а не только DOM!
BX.ready(function() {
    createApp(App).mount('#app');
});