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
                    <h2 class="zob-steps-faq__steps-title">Your Zwitch Shopping Journey</h2>
                    <p class="zob-steps-faq__steps-subtitle">Discover, select, and create your impact with our exclusive collection of handmade, limited-edition t-shirts and apparel.</p>
                </div>

                <div class="zob-steps-faq__steps-cards">
                    <div class="zob-steps-faq__steps-card">
                        <div class="zob-steps-faq__steps-step-number">Step 01</div>
                        <div class="zob-steps-faq__steps-card-content">
                            <h3 class="zob-steps-faq__steps-card-title">Discover Originals</h3>
                            <p class="zob-steps-faq__steps-card-description">Explore our exclusive collection of handmade, limited-edition t-shirts and apparel - each design tells a story and turns heads.</p>
                        </div>
                    </div>

                    <div class="zob-steps-faq__steps-card">
                        <div class="zob-steps-faq__steps-step-number">Step 02</div>
                        <div class="zob-steps-faq__steps-card-content">
                            <h3 class="zob-steps-faq__steps-card-title">Seamless Selection</h3>
                            <p class="zob-steps-faq__steps-card-description">Use intuitive filters to shop by style, category, or fit. Choose between our iconic black or white tees, and pick from regular or oversized fits for the look that's uniquely yours.</p>
                        </div>
                    </div>

                    <div class="zob-steps-faq__steps-card">
                        <div class="zob-steps-faq__steps-step-number">Step 03</div>
                        <div class="zob-steps-faq__steps-card-content">
                            <h3 class="zob-steps-faq__steps-card-title">Secure Checkout</h3>
                            <p class="zob-steps-faq__steps-card-description">Enjoy safe, encrypted transactions and all major online payment options - UPI, net banking, cards, or wallets. No cash on delivery, for a smoother, faster shopping experience.</p>
                        </div>
                    </div>

                    <div class="zob-steps-faq__steps-card">
                        <div class="zob-steps-faq__steps-step-number">Step 04</div>
                        <div class="zob-steps-faq__steps-card-content">
                            <h3 class="zob-steps-faq__steps-card-title">Create Your Impact</h3>
                            <p class="zob-steps-faq__steps-card-description">Start making memories in your statement piece. Zwitch isn't just apparel-it's your invitation to own the spotlight.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- FAQ Section -->
            <div class="zob-steps-faq__faq">
                <div class="zob-steps-faq__faq-header">
                    <div class="zob-steps-faq__faq-text-container">
                        <h2 class="zob-steps-faq__faq-title">Have Questions? We Have Answers.</h2>
                        <p class="zob-steps-faq__faq-subtitle">Ease into the world of Zwitch Originals with clarity. Our FAQs cover a spectrum of topics.</p>
                    </div>
                    
                    <div class="zob-steps-faq__faq-tabs">
                        <button class="zob-steps-faq__faq-tab zob-steps-faq__faq-tab--active" data-category="all">All</button>
                        <button class="zob-steps-faq__faq-tab" data-category="orders">Orders & Products</button>
                        <button class="zob-steps-faq__faq-tab" data-category="shipping">Shipping & Delivery</button>
                        <button class="zob-steps-faq__faq-tab" data-category="returns">Returns & Refunds</button>
                        <button class="zob-steps-faq__faq-tab" data-category="general">General</button>
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
                    <!-- Left Column -->
                    <div class="zob-steps-faq__faq-column" id="faq-left-column">
                        <div class="zob-steps-faq__faq-item" data-category="orders">
                            <div class="zob-steps-faq__faq-question">
                                <h3 class="zob-steps-faq__faq-question-text">How do I place an order with Zwitch Originals?</h3>
                                <button class="zob-steps-faq__faq-toggle">+</button>
                            </div>
                            <div class="zob-steps-faq__faq-answer">
                                <p>Simply browse our website, select your favorite design, choose size, color, and fit, then proceed to secure online checkout. You'll receive an order confirmation and tracking details by email once your purchase is complete.</p>
                            </div>
                        </div>

                        <div class="zob-steps-faq__faq-item" data-category="orders">
                            <div class="zob-steps-faq__faq-question">
                                <h3 class="zob-steps-faq__faq-question-text">How do I choose the right size?</h3>
                                <button class="zob-steps-faq__faq-toggle">+</button>
                            </div>
                            <div class="zob-steps-faq__faq-answer">
                                <p>Always refer to our detailed size chart available on each product page. If unsure, measure your chest, waist, and length, and compare with our chart for the best fit.</p>
                            </div>
                        </div>

                        <div class="zob-steps-faq__faq-item" data-category="orders">
                            <div class="zob-steps-faq__faq-question">
                                <h3 class="zob-steps-faq__faq-question-text">Are all Zwitch Originals t-shirts handmade and limited edition?</h3>
                                <button class="zob-steps-faq__faq-toggle">+</button>
                            </div>
                            <div class="zob-steps-faq__faq-answer">
                                <p>Yes! All our designs are hand-illustrated by our in-house artist and released in limited quantities, ensuring exclusivity in every piece.</p>
                            </div>
                        </div>

                        <div class="zob-steps-faq__faq-item" data-category="orders">
                            <div class="zob-steps-faq__faq-question">
                                <h3 class="zob-steps-faq__faq-question-text">Can I customize my t-shirt or place a bulk order for events?</h3>
                                <button class="zob-steps-faq__faq-toggle">+</button>
                            </div>
                            <div class="zob-steps-faq__faq-answer">
                                <p>Currently, we offer limited editions only. For customization or bulk inquiries, please contact our support team - we'd love to discuss your needs!</p>
                            </div>
                        </div>

                        <div class="zob-steps-faq__faq-item" data-category="shipping">
                            <div class="zob-steps-faq__faq-question">
                                <h3 class="zob-steps-faq__faq-question-text">Do you ship across India?</h3>
                                <button class="zob-steps-faq__faq-toggle">+</button>
                            </div>
                            <div class="zob-steps-faq__faq-answer">
                                <p>Absolutely! We deliver PAN India to all states and union territories. Shipping charges are calculated at checkout.</p>
                            </div>
                        </div>

                        <div class="zob-steps-faq__faq-item" data-category="shipping">
                            <div class="zob-steps-faq__faq-question">
                                <h3 class="zob-steps-faq__faq-question-text">How long will it take to receive my order?</h3>
                                <button class="zob-steps-faq__faq-toggle">+</button>
                            </div>
                            <div class="zob-steps-faq__faq-answer">
                                <p>Standard delivery takes 4–6 business days from order processing. You'll get a tracking number via email/SMS for real-time updates on your shipment status.</p>
                            </div>
                        </div>

                        <div class="zob-steps-faq__faq-item" data-category="shipping">
                            <div class="zob-steps-faq__faq-question">
                                <h3 class="zob-steps-faq__faq-question-text">Which payment methods do you accept?</h3>
                                <button class="zob-steps-faq__faq-toggle">+</button>
                            </div>
                            <div class="zob-steps-faq__faq-answer">
                                <p>We accept UPI, net banking, debit/credit cards, and wallets. We do not currently offer Cash on Delivery.</p>
                            </div>
                        </div>

                        <div class="zob-steps-faq__faq-item" data-category="returns">
                            <div class="zob-steps-faq__faq-question">
                                <h3 class="zob-steps-faq__faq-question-text">What is your return policy?</h3>
                                <button class="zob-steps-faq__faq-toggle">+</button>
                            </div>
                            <div class="zob-steps-faq__faq-answer">
                                <p>Returns are accepted within 7 days of delivery for unworn and unwashed items with tags intact. Customized and limited-edition drops are final sale unless defective. Please contact us to initiate a return.</p>
                            </div>
                        </div>

                        <div class="zob-steps-faq__faq-item" data-category="returns">
                            <div class="zob-steps-faq__faq-question">
                                <h3 class="zob-steps-faq__faq-question-text">How do refunds work?</h3>
                                <button class="zob-steps-faq__faq-toggle">+</button>
                            </div>
                            <div class="zob-steps-faq__faq-answer">
                                <p>Eligible returns are refunded to the original payment method within 7–10 business days after we receive and inspect the product. Refunds exclude shipping fees unless the product is faulty or incorrect.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column -->
                    <div class="zob-steps-faq__faq-column" id="faq-right-column">
                        <div class="zob-steps-faq__faq-item" data-category="returns">
                            <div class="zob-steps-faq__faq-question">
                                <h3 class="zob-steps-faq__faq-question-text">What if I receive a defective or wrong item?</h3>
                                <button class="zob-steps-faq__faq-toggle">+</button>
                            </div>
                            <div class="zob-steps-faq__faq-answer">
                                <p>We apologize for any inconvenience! Please contact us within 2 days of receiving your order. We'll arrange a return, reimburse your shipping costs, and send a replacement or refund after review.</p>
                            </div>
                        </div>

                        <div class="zob-steps-faq__faq-item" data-category="returns">
                            <div class="zob-steps-faq__faq-question">
                                <h3 class="zob-steps-faq__faq-question-text">Can I cancel my order?</h3>
                                <button class="zob-steps-faq__faq-toggle">+</button>
                            </div>
                            <div class="zob-steps-faq__faq-answer">
                                <p>Yes, cancellations are allowed within 24 hours of purchase only. After that, your order will be processed and shipped as per schedule.</p>
                            </div>
                        </div>

                        <div class="zob-steps-faq__faq-item" data-category="general">
                            <div class="zob-steps-faq__faq-question">
                                <h3 class="zob-steps-faq__faq-question-text">Do you offer Cash on Delivery (COD)?</h3>
                                <button class="zob-steps-faq__faq-toggle">+</button>
                            </div>
                            <div class="zob-steps-faq__faq-answer">
                                <p>No, we currently accept only prepaid online payments for faster, safer transactions.</p>
                            </div>
                        </div>

                        <div class="zob-steps-faq__faq-item" data-category="general">
                            <div class="zob-steps-faq__faq-question">
                                <h3 class="zob-steps-faq__faq-question-text">Are your prices inclusive of GST and other taxes?</h3>
                                <button class="zob-steps-faq__faq-toggle">+</button>
                            </div>
                            <div class="zob-steps-faq__faq-answer">
                                <p>Yes, all listed prices are inclusive of GST and applicable taxes.</p>
                            </div>
                        </div>

                        <div class="zob-steps-faq__faq-item" data-category="general">
                            <div class="zob-steps-faq__faq-question">
                                <h3 class="zob-steps-faq__faq-question-text">Will my personal information be safe?</h3>
                                <button class="zob-steps-faq__faq-toggle">+</button>
                            </div>
                            <div class="zob-steps-faq__faq-answer">
                                <p>Absolutely. We value your privacy and use your personal details only for order processing and updates. Read our Privacy Policy for more info.</p>
                            </div>
                        </div>

                        <div class="zob-steps-faq__faq-item" data-category="general">
                            <div class="zob-steps-faq__faq-question">
                                <h3 class="zob-steps-faq__faq-question-text">Can minors (under 18) shop at Zwitch Originals?</h3>
                                <button class="zob-steps-faq__faq-toggle">+</button>
                            </div>
                            <div class="zob-steps-faq__faq-answer">
                                <p>Yes, minors may shop on our site, but we advise parental supervision for safe purchases.</p>
                            </div>
                        </div>

                        <div class="zob-steps-faq__faq-item" data-category="general">
                            <div class="zob-steps-faq__faq-question">
                                <h3 class="zob-steps-faq__faq-question-text">How can I contact customer care?</h3>
                                <button class="zob-steps-faq__faq-toggle">+</button>
                            </div>
                            <div class="zob-steps-faq__faq-answer">
                                <p>For any queries or issues, reach out to us at your support email or use the contact form on our website. Our team is ready to help!</p>
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
                    
                    // Get column containers
                    const leftColumn = document.getElementById('faq-left-column');
                    const rightColumn = document.getElementById('faq-right-column');
                    
                    // Filter FAQ items and redistribute them
                    const visibleItems = [];
                    faqItems.forEach(item => {
                        const itemCategory = item.getAttribute('data-category');
                        console.log('Filtering item:', itemCategory, 'for category:', category);
                        
                        if (category === 'all' || itemCategory === category) {
                            item.classList.remove('hidden');
                            item.style.display = 'block';
                            visibleItems.push(item);
                        } else {
                            item.classList.add('hidden');
                            item.style.display = 'none';
                        }
                    });
                    
                    // Redistribute visible items evenly between columns
                    if (leftColumn && rightColumn) {
                        // Clear both columns
                        leftColumn.innerHTML = '';
                        rightColumn.innerHTML = '';
                        
                        // Redistribute items
                        visibleItems.forEach((item, index) => {
                            const clone = item.cloneNode(true);
                            if (index % 2 === 0) {
                                leftColumn.appendChild(clone);
                            } else {
                                rightColumn.appendChild(clone);
                            }
                        });
                        
                        // Reinitialize click handlers for new elements
                        setTimeout(() => {
                            const newFaqItems = document.querySelectorAll('.zob-steps-faq__faq-item');
                            newFaqItems.forEach((item, index) => {
                                const question = item.querySelector('.zob-steps-faq__faq-question');
                                const toggle = item.querySelector('.zob-steps-faq__faq-toggle');
                                
                                if (question && toggle) {
                                    question.removeEventListener('click', question.faqClickHandler);
                                    
                                    question.faqClickHandler = function(e) {
                                        e.preventDefault();
                                        e.stopPropagation();
                                        
                                        const isActive = item.classList.contains('active');
                                        
                                        // Close all other FAQ items
                                        newFaqItems.forEach(otherItem => {
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
                        }, 10);
                    }
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
