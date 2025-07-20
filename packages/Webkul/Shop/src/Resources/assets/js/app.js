/**
 * This will track all the images and fonts for publishing.
 */
import.meta.glob(["../images/**", "../fonts/**"]);

/**
 * Main vue bundler.
 */
import { createApp } from "vue/dist/vue.esm-bundler";

/**
 * Main root application registry.
 */
window.app = createApp({
    data() {
        return {
            currentTheme: document.documentElement.getAttribute('data-theme') || 'light'
        };
    },

    mounted() {
        this.lazyImages();
        this.initializeTheme();
        // Mark that our app has initialized so theme changes are allowed
        window.bagistoAppInitialized = true;
    },

    methods: {
        onSubmit() {},

        onInvalidSubmit() {},

        lazyImages() {
            var lazyImages = [].slice.call(document.querySelectorAll('img.lazy'));

            let lazyImageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        let lazyImage = entry.target;

                        lazyImage.src = lazyImage.dataset.src;

                        lazyImage.classList.remove('lazy');

                        lazyImageObserver.unobserve(lazyImage);
                    }
                });
            });

            lazyImages.forEach(function(lazyImage) {
                lazyImageObserver.observe(lazyImage);
            });
        },

        initializeTheme() {
            // Get server-side theme setting first (from admin panel)
            const serverTheme = document.documentElement.getAttribute('data-theme') || 'light';
            
            console.log('Bagisto App: Initializing theme. Server theme:', serverTheme);
            
            // Clear ALL localStorage entries to prevent conflicts
            localStorage.removeItem('theme');
            localStorage.removeItem('bagisto-theme');
            
            // Always use server-side setting as the primary source
            this.currentTheme = serverTheme;
            document.documentElement.setAttribute('data-theme', serverTheme);
            
            console.log('Bagisto App: Theme set to:', this.currentTheme);
            
            // Force the theme to persist by setting it again after a short delay
            // This prevents other scripts from overriding it
            setTimeout(() => {
                document.documentElement.setAttribute('data-theme', this.currentTheme);
                console.log('Bagisto App: Theme forced to:', this.currentTheme);
            }, 100);
        },

        toggleTheme() {
            this.currentTheme = this.currentTheme === 'light' ? 'dark' : 'light';
            document.documentElement.setAttribute('data-theme', this.currentTheme);
            
            // Store theme preference in localStorage for user preference
            localStorage.setItem('bagisto-theme', this.currentTheme);
        },

        setTheme(theme) {
            this.currentTheme = theme;
            document.documentElement.setAttribute('data-theme', theme);
            localStorage.setItem('bagisto-theme', theme);
        },
    },
});

/**
 * Global plugins registration.
 */
import Axios from "./plugins/axios";
import Emitter from "./plugins/emitter";
import Shop from "./plugins/shop";
import VeeValidate from "./plugins/vee-validate";
import Flatpickr from "./plugins/flatpickr";

[
    Axios,
    Emitter,
    Shop,
    VeeValidate,
    Flatpickr,
].forEach((plugin) => app.use(plugin));

/**
 * Global directives.
 */
import Debounce from "./directives/debounce";

app.directive("debounce", Debounce);

export default app;
