import './bootstrap';
import '../css/app.css';

import { createApp, h, DefineComponent } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import {createPinia} from "pinia";
import Toast, {PluginOptions, POSITION} from "vue-toastification";
import "vue-toastification/dist/index.css";
import PrimeVue from "primevue/config";
import Editor from 'primevue/editor';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

const options: PluginOptions = {
    transition: "Vue-Toastification__fade",
    timeout: 4000,
    icon: false,
    position: POSITION.BOTTOM_RIGHT,
    maxToasts: 5,
};

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob<DefineComponent>('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {

        const pinia = createPinia()
        const app = createApp(App)

        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(pinia)
            .use(Toast, options)
            .use(PrimeVue)
            .component('Editor', Editor)
            .mount(el);
    },
    progress: {
        color: '#dafc08',
    },
});
