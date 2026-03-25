import '../css/app.css';
import './bootstrap';
import PrimeVue from "primevue/config";
import Aura from '@primevue/themes/aura';
import ToastService from "primevue/toastservice";
import ConfirmationService from 'primevue/confirmationservice';
import { brandSettings } from './plugins/BrandSettings';
import VueApexCharts from 'vue3-apexcharts';
import CSRFService from './Services/CSRFService';

import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} | ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue")
        ),
    setup({ el, App, props, plugin }) {
        // Initialize global CSRF protection
        CSRFService.initCSRFProtection();
        
        const app = createApp({ render: () => h(App, props) });

        app.config.globalProperties.$t = (key) => {
            const page = app.config.globalProperties.$page;
            return page?.props?.translations?.[key] || key;
        };

        return app
            .use(plugin)
            .use(ZiggyVue)
            .use(PrimeVue, {
                theme: {
                    preset: Aura,
                    options: {
                        prefix: "p",
                        darkModeSelector: "light",
                        cssLayer: false,
                    },
                },
            })
            .use(ToastService)
            .use(ConfirmationService)
            .use(brandSettings)
            .use(VueApexCharts)
            .mount(el);
    },
    progress: {
        color: "#4bc05e",
    },
});
