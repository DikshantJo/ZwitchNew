<!-- SEO Meta Content -->
@push('meta')
    <meta
        name="description"
        content="{{ trim($category->meta_description) != "" ? $category->meta_description : \Illuminate\Support\Str::limit(strip_tags($category->description), 120, '') }}"
    />

    <meta
        name="keywords"
        content="{{ $category->meta_keywords }}"
    />

    @if (core()->getConfigData('catalog.rich_snippets.categories.enable'))
        <script type="application/ld+json">
            {!! app('Webkul\Product\Helpers\SEO')->getCategoryJsonLd($category) !!}
        </script>
    @endif
@endPush

<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        {{ trim($category->meta_title) != "" ? $category->meta_title : $category->name }}
    </x-slot>

    {!! view_render_event('bagisto.shop.categories.view.banner_path.before') !!}

    <!-- Hero Image -->
    @if ($category->banner_path)
        <div class="container mt-8 px-[60px] max-lg:px-8 max-md:mt-4 max-md:px-4">
            <x-shop::media.images.lazy
                class="aspect-[4/1] max-h-full max-w-full rounded-xl category-banner-img"
                src="{{ $category->banner_url }}"
                alt="{{ $category->name }}"
                width="1320"
                height="300"
            />
        </div>
    @endif

    {!! view_render_event('bagisto.shop.categories.view.banner_path.after') !!}

    <!-- Category Name -->
    <div class="container mt-20 px-[60px] max-lg:px-8 max-md:mt-16 max-md:px-4 category-name-container">
        <div class="text-center">
            <h1 class="text-5xl font-bold mb-10 max-md:text-3xl max-sm:text-2xl" style="color: var(--category-title-color, #111827) !important;">
                {{ $category->name }}
            </h1>
            
            @if ($category->description)
                <p class="text-lg text-gray-700 dark:text-white/80 max-w-4xl mx-auto leading-relaxed mb-16 max-md:text-base max-sm:text-sm category-description" style="font-family: 'Urbanist', sans-serif !important;margin-top: 20px;margin-bottom: 80px;">
                    {!! strip_tags($category->description) !!}
                </p>
            @endif
        </div>
    </div>

    {!! view_render_event('bagisto.shop.categories.view.description.before') !!}

    {!! view_render_event('bagisto.shop.categories.view.description.after') !!}

    @if (in_array($category->display_mode, [null, 'products_only', 'products_and_description']))
        <!-- Category Vue Component -->
        <v-category>
            <!-- Category Shimmer Effect -->
            <x-shop::shimmer.categories.view />
        </v-category>
    @endif

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-category-template"
        >
            <div class="container px-[60px] max-lg:px-8 max-md:px-4">
                <div class="flex items-start gap-10 max-lg:gap-5 md:mt-10">
                    <!-- Product Listing Filters -->
                    @include('shop::categories.filters')

                    <!-- Product Listing Container -->
                    <div class="flex-1">
                        <!-- Desktop Product Listing Toolbar -->
                        <div class="max-md:hidden">
                            @include('shop::categories.toolbar')
                        </div>

                        <!-- Product List Card Container -->
                        <div
                            class="mt-8 grid grid-cols-1 gap-6"
                            v-if="(filters.toolbar.applied.mode ?? filters.toolbar.default.mode) === 'list'"
                        >
                            <!-- Product Card Shimmer Effect -->
                            <template v-if="isLoading">
                                <x-shop::shimmer.products.cards.list count="12" />
                            </template>

                            <!-- Product Card Listing -->
                            {!! view_render_event('bagisto.shop.categories.view.list.product_card.before') !!}

                            <template v-else>
                                <template v-if="products.length">
                                    <x-shop::products.card
                                        ::mode="'list'"
                                        v-for="product in products"
                                    />
                                </template>

                                <!-- Empty Products Container -->
                                <template v-else>
                                    <div class="m-auto grid w-full place-content-center items-center justify-items-center py-32 text-center">
                                        <img
                                            class="max-md:h-[100px] max-md:w-[100px]"
                                            src="{{ bagisto_asset('images/thank-you.png') }}"
                                            alt="@lang('shop::app.categories.view.empty')"
                                        />

                                        <p
                                            class="text-xl max-md:text-sm"
                                            role="heading"
                                        >
                                            @lang('shop::app.categories.view.empty')
                                        </p>
                                    </div>
                                </template>
                            </template>

                            {!! view_render_event('bagisto.shop.categories.view.list.product_card.after') !!}
                        </div>

                        <!-- Product Grid Card Container -->
                        <div v-else class="mt-8 max-md:mt-5">
                            <!-- Product Card Shimmer Effect -->
                            <template v-if="isLoading">
                                <div class="grid grid-cols-3 gap-4 max-1060:grid-cols-2 max-md:justify-items-center max-md:gap-x-4">
                                    <x-shop::shimmer.products.cards.grid count="12" />
                                </div>
                            </template>

                            {!! view_render_event('bagisto.shop.categories.view.grid.product_card.before') !!}

                            <!-- Product Card Listing -->
                            <template v-else>
                                <template v-if="products.length">
                                    <div class="grid grid-cols-3 gap-4 max-1060:grid-cols-2 max-md:justify-items-center max-md:gap-x-4">
                                        <x-shop::products.card
                                            ::mode="'grid'"
                                            v-for="product in products"
                                        />
                                    </div>
                                </template>

                                <!-- Empty Products Container -->
                                <template v-else>
                                    <div class="m-auto grid w-full place-content-center items-center justify-items-center py-32 text-center">
                                        <img
                                            class="max-md:h-[100px] max-md:w-[100px]"
                                            src="{{ bagisto_asset('images/thank-you.png') }}"
                                            alt="@lang('shop::app.categories.view.empty')"
                                        />

                                        <p
                                            class="text-xl max-md:text-sm"
                                            role="heading"
                                        >
                                            @lang('shop::app.categories.view.empty')
                                        </p>
                                    </div>
                                </template>
                            </template>

                            {!! view_render_event('bagisto.shop.categories.view.grid.product_card.after') !!}
                        </div>

                        {!! view_render_event('bagisto.shop.categories.view.load_more_button.before') !!}

                        <!-- Load More Button -->
                        <button
                            class="secondary-button mx-auto mt-14 block w-max rounded-2xl px-11 py-3 text-center text-base max-md:rounded-lg max-sm:mt-6 max-sm:px-6 max-sm:py-1.5 max-sm:text-sm"
                            @click="loadMoreProducts"
                            v-if="links.next && ! loader"
                        >
                            @lang('shop::app.categories.view.load-more')
                        </button>

                        <button
                            v-else-if="links.next"
                            class="secondary-button mx-auto mt-14 block w-max rounded-2xl px-[74.5px] py-3.5 text-center text-base max-md:rounded-lg max-md:py-3 max-sm:mt-6 max-sm:px-[50.8px] max-sm:py-1.5"
                        >
                            <!-- Spinner -->
                            <img
                                class="h-5 w-5 animate-spin text-navyBlue"
                                src="{{ bagisto_asset('images/spinner.svg') }}"
                                alt="Loading"
                            />
                        </button>

                        {!! view_render_event('bagisto.shop.categories.view.grid.load_more_button.after') !!}
                    </div>
                </div>
            </div>
        </script>

        <script type="module">
            app.component('v-category', {
                template: '#v-category-template',

                data() {
                    return {
                        isMobile: window.innerWidth <= 767,

                        isLoading: true,

                        isDrawerActive: {
                            toolbar: false,

                            filter: false,
                        },

                        filters: {
                            toolbar: {
                                default: {},

                                applied: {},
                            },

                            filter: {},
                        },

                        products: [],

                        links: {},

                        loader: false,
                    }
                },

                computed: {
                    queryParams() {
                        let queryParams = Object.assign({}, this.filters.filter, this.filters.toolbar.applied);

                        return this.removeJsonEmptyValues(queryParams);
                    },

                    queryString() {
                        return this.jsonToQueryString(this.queryParams);
                    },
                },

                watch: {
                    queryParams() {
                        this.getProducts();
                    },

                    queryString() {
                        window.history.pushState({}, '', '?' + this.queryString);
                    },
                },

                methods: {
                    setFilters(type, filters) {
                        this.filters[type] = filters;
                    },

                    clearFilters(type, filters) {
                        this.filters[type] = {};
                    },

                    getProducts() {
                        this.isDrawerActive = {
                            toolbar: false,

                            filter: false,
                        };

                        document.body.style.overflow ='scroll';

                        this.$axios.get("{{ route('shop.api.products.index', ['category_id' => $category->id]) }}", {
                            params: this.queryParams
                        })
                            .then(response => {
                                this.isLoading = false;

                                this.products = response.data.data;

                                this.links = response.data.links;
                            }).catch(error => {
                                console.log(error);
                            });
                    },

                    loadMoreProducts() {
                        if (! this.links.next) {
                            return;
                        }

                        this.loader = true;

                        this.$axios.get(this.links.next)
                            .then(response => {
                                this.loader = false;

                                this.products = [...this.products, ...response.data.data];

                                this.links = response.data.links;
                            }).catch(error => {
                                console.log(error);
                            });
                    },

                    removeJsonEmptyValues(params) {
                        Object.keys(params).forEach(function (key) {
                            if ((! params[key] && params[key] !== undefined)) {
                                delete params[key];
                            }

                            if (Array.isArray(params[key])) {
                                params[key] = params[key].join(',');
                            }
                        });

                        return params;
                    },

                    jsonToQueryString(params) {
                        let parameters = new URLSearchParams();

                        for (const key in params) {
                            parameters.append(key, params[key]);
                        }

                        return parameters.toString();
                    }
                },
            });
        </script>
    @endPushOnce

    {!! view_render_event('bagisto.shop.categories.view.after', ['category' => $category]) !!}

    <!-- Steps & FAQ Section -->
    <div class="zob-steps-faq">
        <div class="zob-steps-faq__container">
            <!-- Steps Section -->
            <div class="zob-steps-faq__steps">
                <div class="zob-steps-faq__steps-header">
                    <h2 class="zob-steps-faq__steps-title">Navigating the StyleLoom Fashion Journey.</h2>
                    <p class="zob-steps-faq__steps-subtitle">At StyleLoom, we've designed a straightforward shopping experience to make fashion accessible.</p>
                </div>

                <div class="zob-steps-faq__steps-cards">
                    <div class="zob-steps-faq__steps-card">
                        <div class="zob-steps-faq__steps-step-number">Step 01</div>
                        <div class="zob-steps-faq__steps-card-content">
                            <h3 class="zob-steps-faq__steps-card-title">Discover Trends</h3>
                            <p class="zob-steps-faq__steps-card-description">Explore our curated collection of over 1000 styles, spanning global fashion trends.</p>
                        </div>
                    </div>

                    <div class="zob-steps-faq__steps-card">
                        <div class="zob-steps-faq__steps-step-number">Step 02</div>
                        <div class="zob-steps-faq__steps-card-content">
                            <h3 class="zob-steps-faq__steps-card-title">Effortless Navigation</h3>
                            <p class="zob-steps-faq__steps-card-description">Intuitive filters and categories help you find the perfect pieces tailored to your style.</p>
                        </div>
                    </div>

                    <div class="zob-steps-faq__steps-card">
                        <div class="zob-steps-faq__steps-step-number">Step 03</div>
                        <div class="zob-steps-faq__steps-card-content">
                            <h3 class="zob-steps-faq__steps-card-title">Secure Checkout</h3>
                            <p class="zob-steps-faq__steps-card-description">Multiple payment options and encrypted transactions ensure a safe and hassle-free purchase.</p>
                        </div>
                    </div>

                    <div class="zob-steps-faq__steps-card">
                        <div class="zob-steps-faq__steps-step-number">Step 04</div>
                        <div class="zob-steps-faq__steps-card-content">
                            <h3 class="zob-steps-faq__steps-card-title">Unbox Happiness</h3>
                            <p class="zob-steps-faq__steps-card-description">Unbox a fashion-forward experience delivered right to your door, ready to elevate your style.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="zob-steps-faq__faq">
                <div class="zob-steps-faq__faq-header">
                    <div class="zob-steps-faq__faq-text-container">
                        <h2 class="zob-steps-faq__faq-title">Have Questions? We Have Answers.</h2>
                        <p class="zob-steps-faq__faq-subtitle">Ease into the world of StyleLoom with clarity. Our FAQs cover a spectrum of topics.</p>
                    </div>
                    
                    <div class="zob-steps-faq__faq-tabs">
                        <button class="zob-steps-faq__faq-tab zob-steps-faq__faq-tab--active" data-category="all">All</button>
                        <button class="zob-steps-faq__faq-tab" data-category="ordering">Ordering</button>
                        <button class="zob-steps-faq__faq-tab" data-category="shipping">Shipping</button>
                        <button class="zob-steps-faq__faq-tab" data-category="returns">Returns</button>
                        <button class="zob-steps-faq__faq-tab" data-category="support">Support</button>
                    </div>
                    
                    <div class="zob-steps-faq__faq-decoration">
                        <svg width="446" height="446" viewBox="0 0 446 446" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <clipPath id="faq-clip">
                                    <path d="M223 0C100.5 0 0 100.5 0 223C0 345.5 100.5 446 223 446C345.5 446 446 345.5 446 223C446 100.5 345.5 0 223 0ZM223 400C120.5 400 40 319.5 40 223C40 126.5 120.5 46 223 46C319.5 46 400 126.5 400 223C400 319.5 319.5 400 223 400Z"/>
                                </clipPath>
                            </defs>
                            <g clip-path="url(#faq-clip)">
                                <circle cx="223" cy="223" r="223" fill="#FFFFFF" fill-opacity="0.03"/>
                                <!-- Abstract geometric patterns -->
                                <path d="M100 100C100 100 150 130 200 100C250 70 300 100 300 100" stroke="#FFFFFF" stroke-width="1.5" stroke-opacity="0.08" fill="none"/>
                                <path d="M80 180C80 180 130 210 180 180C230 150 280 180 280 180" stroke="#FFFFFF" stroke-width="1.5" stroke-opacity="0.08" fill="none"/>
                                <path d="M160 260C160 260 210 290 260 260C310 230 360 260 360 260" stroke="#FFFFFF" stroke-width="1.5" stroke-opacity="0.08" fill="none"/>
                                <path d="M120 320C120 320 170 350 220 320C270 290 320 320 320 320" stroke="#FFFFFF" stroke-width="1.5" stroke-opacity="0.08" fill="none"/>
                                
                                <!-- Decorative dots -->
                                <circle cx="150" cy="120" r="2" fill="#FFFFFF" fill-opacity="0.15"/>
                                <circle cx="250" cy="160" r="2" fill="#FFFFFF" fill-opacity="0.15"/>
                                <circle cx="200" cy="200" r="2" fill="#FFFFFF" fill-opacity="0.15"/>
                                <circle cx="300" cy="240" r="2" fill="#FFFFFF" fill-opacity="0.15"/>
                                <circle cx="180" cy="280" r="2" fill="#FFFFFF" fill-opacity="0.15"/>
                                <circle cx="280" cy="320" r="2" fill="#FFFFFF" fill-opacity="0.15"/>
                                <circle cx="220" cy="360" r="2" fill="#FFFFFF" fill-opacity="0.15"/>
                                <circle cx="320" cy="400" r="2" fill="#FFFFFF" fill-opacity="0.15"/>
                            </g>
                        </svg>
                    </div>
                </div>

                <div class="zob-steps-faq__faq-content">
                    <div class="zob-steps-faq__faq-column">
                        <div class="zob-steps-faq__faq-item" data-category="ordering">
                            <div class="zob-steps-faq__faq-question">
                                <h3 class="zob-steps-faq__faq-question-text">How can I place an order on StyleLoom?</h3>
                                <button class="zob-steps-faq__faq-toggle">+</button>
                            </div>
                            <div class="zob-steps-faq__faq-answer">
                                <p>Ordering is easy! Simply browse our website, add items to your cart, and proceed to checkout. Follow the prompts to enter your details and complete your purchase.</p>
                            </div>
                        </div>

                        <div class="zob-steps-faq__faq-item" data-category="ordering">
                            <div class="zob-steps-faq__faq-question">
                                <h3 class="zob-steps-faq__faq-question-text">What payment methods do you accept?</h3>
                                <button class="zob-steps-faq__faq-toggle">+</button>
                            </div>
                            <div class="zob-steps-faq__faq-answer">
                                <p>We accept a variety of payment methods, including credit/debit cards, net banking, and select digital wallets. Choose the option that suits you best during checkout.</p>
                            </div>
                        </div>

                        <div class="zob-steps-faq__faq-item" data-category="shipping">
                            <div class="zob-steps-faq__faq-question">
                                <h3 class="zob-steps-faq__faq-question-text">How can I track my order?</h3>
                                <button class="zob-steps-faq__faq-toggle">+</button>
                            </div>
                            <div class="zob-steps-faq__faq-answer">
                                <p>Once your order is dispatched, you'll receive a tracking number via email. Use this number to track your package in real-time on our website.</p>
                            </div>
                        </div>

                        <div class="zob-steps-faq__faq-item" data-category="returns">
                            <div class="zob-steps-faq__faq-question">
                                <h3 class="zob-steps-faq__faq-question-text">How do I initiate a return?</h3>
                                <button class="zob-steps-faq__faq-toggle">+</button>
                            </div>
                            <div class="zob-steps-faq__faq-answer">
                                <p>Visit our Returns page and follow the provided instructions. Ensure your item meets our return criteria, and our team will guide you through the process.</p>
                            </div>
                        </div>

                        <div class="zob-steps-faq__faq-item" data-category="returns">
                            <div class="zob-steps-faq__faq-question">
                                <h3 class="zob-steps-faq__faq-question-text">Do you offer exchanges for products?</h3>
                                <button class="zob-steps-faq__faq-toggle">+</button>
                            </div>
                            <div class="zob-steps-faq__faq-answer">
                                <p>At this time, we don't offer direct product exchanges. If you'd like a different item, please initiate a return and place a new order.</p>
                            </div>
                        </div>
                    </div>

                    <div class="zob-steps-faq__faq-column">
                        <div class="zob-steps-faq__faq-item" data-category="ordering">
                            <div class="zob-steps-faq__faq-question">
                                <h3 class="zob-steps-faq__faq-question-text">Can I modify or cancel my order after placing it?</h3>
                                <button class="zob-steps-faq__faq-toggle">+</button>
                            </div>
                            <div class="zob-steps-faq__faq-answer">
                                <p>Unfortunately, once an order is confirmed, modifications or cancellations may not be possible. Please review your order carefully before completing the purchase.</p>
                            </div>
                        </div>

                        <div class="zob-steps-faq__faq-item" data-category="shipping">
                            <div class="zob-steps-faq__faq-question">
                                <h3 class="zob-steps-faq__faq-question-text">What are your shipping options?</h3>
                                <button class="zob-steps-faq__faq-toggle">+</button>
                            </div>
                            <div class="zob-steps-faq__faq-answer">
                                <p>We offer standard and express shipping options. Standard delivery typically takes 3-5 business days, while express delivery takes 1-2 business days.</p>
                            </div>
                        </div>

                        <div class="zob-steps-faq__faq-item" data-category="support">
                            <div class="zob-steps-faq__faq-question">
                                <h3 class="zob-steps-faq__faq-question-text">How can I contact customer support?</h3>
                                <button class="zob-steps-faq__faq-toggle">+</button>
                            </div>
                            <div class="zob-steps-faq__faq-answer">
                                <p>You can reach our customer support team through email, live chat, or phone. We're available 24/7 to assist you with any questions or concerns.</p>
                            </div>
                        </div>

                        <div class="zob-steps-faq__faq-item" data-category="support">
                            <div class="zob-steps-faq__faq-question">
                                <h3 class="zob-steps-faq__faq-question-text">Do you have a size guide?</h3>
                                <button class="zob-steps-faq__faq-toggle">+</button>
                            </div>
                            <div class="zob-steps-faq__faq-answer">
                                <p>Yes! We provide detailed size guides for all our products. You can find the size guide on each product page to help you choose the perfect fit.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- FAQ JavaScript Functionality -->
    <script>
        // FAQ Functionality - Run after everything is loaded
        function initCategoryFAQ() {
            console.log('Category FAQ JavaScript loaded');
            
            const faqItems = document.querySelectorAll('.zob-steps-faq__faq-item');
            const faqTabs = document.querySelectorAll('.zob-steps-faq__faq-tab');
            
            console.log('Found FAQ items:', faqItems.length);
            console.log('Found FAQ tabs:', faqTabs.length);

            // FAQ Toggle Functionality
            faqItems.forEach((item, index) => {
                const question = item.querySelector('.zob-steps-faq__faq-question');
                const toggle = item.querySelector('.zob-steps-faq__faq-toggle');
                const answer = item.querySelector('.zob-steps-faq__faq-answer');
                
                console.log(`FAQ item ${index}:`, { question: !!question, toggle: !!toggle, answer: !!answer });

                if (question && toggle) {
                    // Remove any existing listeners
                    question.removeEventListener('click', question.faqClickHandler);
                    
                    // Create new click handler
                    question.faqClickHandler = function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        console.log('FAQ question clicked');
                        const isActive = item.classList.contains('active');
                        
                        // Close all other FAQ items
                        faqItems.forEach(otherItem => {
                            if (otherItem !== item) {
                                otherItem.classList.remove('active');
                                const otherToggle = otherItem.querySelector('.zob-steps-faq__faq-toggle');
                                if (otherToggle) {
                                    otherToggle.textContent = '+';
                                }
                            }
                        });

                        // Toggle current item
                        if (isActive) {
                            item.classList.remove('active');
                            toggle.textContent = '+';
                        } else {
                            item.classList.add('active');
                            toggle.textContent = '−';
                        }
                    };
                    
                    question.addEventListener('click', question.faqClickHandler);
                }
            });

            // Tab Filtering Functionality
            faqTabs.forEach((tab, index) => {
                console.log(`FAQ tab ${index}:`, tab.getAttribute('data-category'));
                
                // Remove any existing listeners
                tab.removeEventListener('click', tab.faqTabClickHandler);
                
                // Create new click handler
                tab.faqTabClickHandler = function(e) {
                    e.preventDefault();
                    e.stopPropagation();
                    
                    console.log('FAQ tab clicked:', this.getAttribute('data-category'));
                    const category = this.getAttribute('data-category');
                    
                    // Update active tab
                    faqTabs.forEach(t => t.classList.remove('zob-steps-faq__faq-tab--active'));
                    this.classList.add('zob-steps-faq__faq-tab--active');
                    
                    // Filter FAQ items
                    faqItems.forEach(item => {
                        const itemCategory = item.getAttribute('data-category');
                        console.log('Filtering item:', itemCategory, 'for category:', category);
                        
                        if (category === 'all' || itemCategory === category) {
                            item.classList.remove('hidden');
                            item.style.display = 'block';
                        } else {
                            item.classList.add('hidden');
                            item.style.display = 'none';
                        }
                    });
                };
                
                tab.addEventListener('click', tab.faqTabClickHandler);
            });
        }

        // Try multiple ways to ensure it runs
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', initCategoryFAQ);
        } else {
            initCategoryFAQ();
        }

        // Also try on window load as backup
        window.addEventListener('load', function() {
            setTimeout(initCategoryFAQ, 100);
        });
    </script>
</x-shop::layouts>
