/**
 * This will track all the images and fonts for publishing.
 */
import.meta.glob(["../images/**", "../fonts/**"]);

/**
 * Main vue bundler.
 */
import { createApp } from "vue/dist/vue.esm-bundler";

/**
 * Canvas-based Snowflake Manager Class
 */
class SnowflakeManager {
    constructor() {
        this.canvas = document.getElementById('snowflakes-canvas');
        this.ctx = null;
        this.dropsArray = [];
        this.isActive = true;
        this.animationId = null;
        
        this.init();
    }
    
    init() {
        if (!this.canvas) {
            console.error('Snowflake canvas not found!');
            return;
        }
        
        this.ctx = this.canvas.getContext('2d');
        this.setDimensions();
        this.createSnowflakes();
        this.animate();
        
        // Handle window resize
        window.addEventListener('resize', this.handleResize.bind(this));
        
        // Handle visibility change for performance
        document.addEventListener('visibilitychange', this.handleVisibilityChange.bind(this));
        
        // Handle reduced motion preference
        if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
            this.isActive = false;
        }
        
        console.log('Canvas snowflake manager initialized');
    }
    
    setDimensions() {
        this.canvas.width = window.innerWidth;
        this.canvas.height = window.innerHeight;
    }
    
    createSnowflakes() {
        // Clear existing array
        this.dropsArray = [];
        
        // Calculate snowflake count based on screen size (sparse, natural effect)
        const width = window.innerWidth;
        const height = window.innerHeight;
        let snowflakeCount;
        
        if (width <= 480) {
            snowflakeCount = Math.min(50, Math.floor((width * height) / 8000)); // Mobile: very sparse
        } else if (width <= 768) {
            snowflakeCount = Math.min(100, Math.floor((width * height) / 6000)); // Tablet: sparse
        } else {
            snowflakeCount = Math.min(200, Math.floor((width * height) / 4000)); // Desktop: moderate
        }
        
        // Create snowflakes using the working drop function
        for (let i = 0; i < snowflakeCount; i++) {
            this.dropsArray[i] = new this.drop();
        }
        
        console.log(`Created ${snowflakeCount} snowflakes`);
    }
    
    drop() {
        this.x = Math.random() * this.canvas.width;
        this.y = -Math.random() * 500;    // Starting point (top of screen)
        this.r = 0.2 + 1.5 * Math.random();
        this.s = Math.random() + 0.5; // Speed
        this.d = Math.random() * this.canvas.width;  // The distance the snowflake will travel
        
        this.draw = () => {
            this.ctx.beginPath();
            this.ctx.fillStyle = '#ECE9E6';
            this.ctx.shadowColor = '#ffffff30';
            this.ctx.shadowOffsetX = -2;
            this.ctx.shadowOffsetY = -2;
            this.ctx.shadowBlur = 2;
            this.ctx.arc(this.x, this.y, this.r, 0, 2 * Math.PI, true);
            this.ctx.fill();
            this.y += this.s;   // Snowflakes falling
            this.x += Math.sin(this.y / this.d) * 2;   // Snowflakes swinging side to side
            if (this.y >= this.canvas.height) {
                this.y = -10;    // Re-positioning fallen snowflake to "respawn" it.
            }
        };
    }
    
    animate() {
        if (!this.isActive) return;
        
        // Clear canvas
        this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        
        // Draw all snowflakes
        this.dropsArray.forEach((drop) => drop.draw());
        
        this.animationId = requestAnimationFrame(() => this.animate());
    }
    
    handleResize() {
        this.setDimensions();
        this.createSnowflakes();
    }
    
    handleVisibilityChange() {
        if (document.hidden) {
            // Pause animation when tab is not visible
            if (this.animationId) {
                cancelAnimationFrame(this.animationId);
                this.animationId = null;
            }
        } else {
            // Resume animation when tab becomes visible
            if (!this.animationId) {
                this.animate();
            }
        }
    }
    
    destroy() {
        this.isActive = false;
        if (this.animationId) {
            cancelAnimationFrame(this.animationId);
        }
        
        // Clear canvas
        if (this.ctx) {
            this.ctx.clearRect(0, 0, this.canvas.width, this.canvas.height);
        }
        
        this.dropsArray = [];
        console.log('Snowflake manager destroyed');
    }
}

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
        this.initializeSnowflakes();
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
            const isChristmas = document.documentElement.hasAttribute('data-christmas');
            
            console.log('Bagisto App: Initializing theme. Server theme:', serverTheme, 'Christmas:', isChristmas);
            
            // Clear ALL localStorage entries to prevent conflicts
            localStorage.removeItem('theme');
            localStorage.removeItem('bagisto-theme');
            
            // Always use server-side setting as the primary source
            this.currentTheme = serverTheme;
            document.documentElement.setAttribute('data-theme', serverTheme);
            
            // Handle Christmas theme
            if (isChristmas) {
                document.documentElement.setAttribute('data-christmas', 'true');
            }
            
            console.log('Bagisto App: Theme set to:', this.currentTheme);
            
            // Force the theme to persist by setting it again after a short delay
            // This prevents other scripts from overriding it
            setTimeout(() => {
                document.documentElement.setAttribute('data-theme', this.currentTheme);
                if (isChristmas) {
                    document.documentElement.setAttribute('data-christmas', 'true');
                }
                console.log('Bagisto App: Theme forced to:', this.currentTheme);
            }, 100);
        },

        toggleTheme() {
            // Toggle through themes: light -> dark -> christmas -> light
            const themes = ['light', 'dark', 'christmas'];
            const currentIndex = themes.indexOf(this.currentTheme);
            const nextIndex = (currentIndex + 1) % themes.length;
            this.currentTheme = themes[nextIndex];
            
            document.documentElement.setAttribute('data-theme', this.currentTheme);
            
            // Handle Christmas theme
            if (this.currentTheme === 'christmas') {
                document.documentElement.setAttribute('data-christmas', 'true');
            } else {
                document.documentElement.removeAttribute('data-christmas');
            }
            
            // Store theme preference in localStorage for user preference
            localStorage.setItem('bagisto-theme', this.currentTheme);
        },

        setTheme(theme) {
            this.currentTheme = theme;
            document.documentElement.setAttribute('data-theme', theme);
            
            // Handle Christmas theme
            if (theme === 'christmas') {
                document.documentElement.setAttribute('data-christmas', 'true');
                this.initializeSnowflakes();
            } else {
                document.documentElement.removeAttribute('data-christmas');
                this.destroySnowflakes();
            }
            
            localStorage.setItem('bagisto-theme', theme);
        },

        initializeSnowflakes() {
            // Snowflakes are now handled by the component itself
            // No need for Vue.js integration
        },

        destroySnowflakes() {
            // Snowflakes are now handled by the component itself
            // No need for Vue.js integration
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
