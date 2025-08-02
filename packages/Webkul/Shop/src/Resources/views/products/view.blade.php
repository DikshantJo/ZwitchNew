@inject ('reviewHelper', 'Webkul\Product\Helpers\Review')
@inject ('productViewHelper', 'Webkul\Product\Helpers\View')

@php
    $avgRatings = $reviewHelper->getAverageRating($product);

    $percentageRatings = $reviewHelper->getPercentageRating($product);

    $customAttributeValues = $productViewHelper->getAdditionalData($product);

    $attributeData = collect($customAttributeValues)->filter(fn ($item) => ! empty($item['value']));
@endphp

<!-- SEO Meta Content -->
@push('meta')
    <meta name="description" content="{{ trim($product->meta_description) != "" ? $product->meta_description : \Illuminate\Support\Str::limit(strip_tags($product->description), 120, '') }}"/>

    <meta name="keywords" content="{{ $product->meta_keywords }}"/>

    @if (core()->getConfigData('catalog.rich_snippets.products.enable'))
        <script type="application/ld+json">
            {!! app('Webkul\Product\Helpers\SEO')->getProductJsonLd($product) !!}
        </script>
    @endif

    <?php $productBaseImage = product_image()->getProductBaseImage($product); ?>

    <meta name="twitter:card" content="summary_large_image" />

    <meta name="twitter:title" content="{{ $product->name }}" />

    <meta name="twitter:description" content="{!! htmlspecialchars(trim(strip_tags($product->description))) !!}" />

    <meta name="twitter:image:alt" content="" />

    <meta name="twitter:image" content="{{ $productBaseImage['medium_image_url'] }}" />

    <meta property="og:type" content="og:product" />

    <meta property="og:title" content="{{ $product->name }}" />

    <meta property="og:image" content="{{ $productBaseImage['medium_image_url'] }}" />

    <meta property="og:description" content="{!! htmlspecialchars(trim(strip_tags($product->description))) !!}" />

    <meta property="og:url" content="{{ route('shop.product_or_category.index', $product->url_key) }}" />
@endPush

<!-- Page Layout -->
<x-shop::layouts>
    <!-- Page Title -->
    <x-slot:title>
        {{ trim($product->meta_title) != "" ? $product->meta_title : $product->name }}
    </x-slot>

    {!! view_render_event('bagisto.shop.products.view.before', ['product' => $product]) !!}

    <!-- Breadcrumbs -->
    @if ((core()->getConfigData('general.general.breadcrumbs.shop')))
        <div class="flex justify-start px-16 max-lg:hidden">
            <x-shop::breadcrumbs
                name="product"
                :entity="$product"
            />
        </div>
    @endif

    <!-- Product Information Vue Component -->
    <v-product>
        <x-shop::shimmer.products.view />
    </v-product>

    <!-- Information Section -->
    <div class="1180:mt-20">
        <div class="max-1180:hidden">
            <x-shop::tabs
                position="center"
                ref="productTabs"
            >
                <!-- Description Tab -->
                {!! view_render_event('bagisto.shop.products.view.description.before', ['product' => $product]) !!}

                <x-shop::tabs.item
                    id="descritpion-tab"
                    class="container mt-[60px] !p-0"
                    :title="trans('shop::app.products.view.description')"
                    :is-selected="true"
                >
                    <div class="container mt-[60px] max-1180:px-5">
                        <p class="text-lg text-zinc-500 max-1180:text-sm">
                            {!! $product->description !!}
                        </p>
                    </div>
                </x-shop::tabs.item>

                {!! view_render_event('bagisto.shop.products.view.description.after', ['product' => $product]) !!}

                <!-- Additional Information Tab -->
                @if(count($attributeData))
                    <x-shop::tabs.item
                        id="information-tab"
                        class="container mt-[60px] !p-0"
                        :title="trans('shop::app.products.view.additional-information')"
                        :is-selected="false"
                    >
                        <div class="container mt-[60px] max-1180:px-5">
                            <div class="mt-8 grid max-w-max grid-cols-[auto_1fr] gap-4">
                                @foreach ($customAttributeValues as $customAttributeValue)
                                    @if (! empty($customAttributeValue['value']))
                                        <div class="grid">
                                            <p class="text-base text-black">
                                                {!! $customAttributeValue['label'] !!}
                                            </p>
                                        </div>

                                        @if ($customAttributeValue['type'] == 'file')
                                            <a
                                                href="{{ Storage::url($product[$customAttributeValue['code']]) }}"
                                                download="{{ $customAttributeValue['label'] }}"
                                            >
                                                <span class="icon-download text-2xl"></span>
                                            </a>
                                        @elseif ($customAttributeValue['type'] == 'image')
                                            <a
                                                href="{{ Storage::url($product[$customAttributeValue['code']]) }}"
                                                download="{{ $customAttributeValue['label'] }}"
                                            >
                                                <img
                                                    class="h-5 min-h-5 w-5 min-w-5"
                                                    src="{{ Storage::url($customAttributeValue['value']) }}"
                                                />
                                            </a>
                                        @else
                                            <div class="grid">
                                                <p class="text-base text-zinc-500">
                                                    {!! $customAttributeValue['value'] !!}
                                                </p>
                                            </div>
                                        @endif
                                    @endif
                                @endforeach
                            </div>
                        </div>
                    </x-shop::tabs.item>
                @endif

                <!-- Reviews Tab -->
                <x-shop::tabs.item
                    id="review-tab"
                    class="container mt-[60px] !p-0"
                    :title="trans('shop::app.products.view.review')"
                    :is-selected="false"
                >
                    @include('shop::products.view.reviews')
                </x-shop::tabs.item>
            </x-shop::tabs>
        </div>
    </div>

    <!-- Information Section -->
    <div class="container mt-6 grid gap-3 !p-0 max-1180:px-5 1180:hidden">
        <!-- Description Accordion -->
        <x-shop::accordion
            class="max-md:border-none"
            :is-active="true"
        >
            <x-slot:header class="bg-gray-100 max-md:!py-3 max-sm:!py-2">
                <p class="text-base font-medium 1180:hidden">
                    @lang('shop::app.products.view.description')
                </p>
            </x-slot>

            <x-slot:content class="max-sm:px-0">
                <div class="mb-5 text-lg text-zinc-500 max-1180:text-sm max-md:mb-1 max-md:px-4">
                    {!! $product->description !!}
                </div>
            </x-slot>
        </x-shop::accordion>

        <!-- Additional Information Accordion -->
        @if (count($attributeData))
            <x-shop::accordion
                class="max-md:border-none"
                :is-active="false"
            >
                <x-slot:header class="bg-gray-100 max-md:!py-3 max-sm:!py-2">
                    <p class="text-base font-medium 1180:hidden">
                        @lang('shop::app.products.view.additional-information')
                    </p>
                </x-slot>

                <x-slot:content class="max-sm:px-0">
                    <div class="container max-1180:px-5">
                        <div class="grid max-w-max grid-cols-[auto_1fr] gap-4 text-lg text-zinc-500 max-1180:text-sm">
                            @foreach ($customAttributeValues as $customAttributeValue)
                                @if (! empty($customAttributeValue['value']))
                                    <div class="grid">
                                        <p class="text-base text-black">
                                            {{ $customAttributeValue['label'] }}
                                        </p>
                                    </div>

                                    @if ($customAttributeValue['type'] == 'file')
                                        <a
                                            href="{{ Storage::url($product[$customAttributeValue['code']]) }}"
                                            download="{{ $customAttributeValue['label'] }}"
                                        >
                                            <span class="icon-download text-2xl"></span>
                                        </a>
                                    @elseif ($customAttributeValue['type'] == 'image')
                                        <a
                                            href="{{ Storage::url($product[$customAttributeValue['code']]) }}"
                                            download="{{ $customAttributeValue['label'] }}"
                                        >
                                            <img
                                                class="h-5 min-h-5 w-5 min-w-5"
                                                src="{{ Storage::url($customAttributeValue['value']) }}"
                                                alt="Product Image"
                                            />
                                        </a>
                                    @else
                                        <div class="grid">
                                            <p class="text-base text-zinc-500">
                                                {{ $customAttributeValue['value'] ?? '-' }}
                                            </p>
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    </div>
                </x-slot>
            </x-shop::accordion>
        @endif

        <!-- Reviews Accordion -->
        <x-shop::accordion
            class="max-md:border-none"
            :is-active="false"
        >
            <x-slot:header
                class="bg-gray-100 max-md:!py-3 max-sm:!py-2"
                id="review-accordian-button"
            >
                <p class="text-base font-medium">
                    @lang('shop::app.products.view.review')
                </p>
            </x-slot>

            <x-slot:content>
                @include('shop::products.view.reviews')
            </x-slot>
        </x-shop::accordion>

    </div>

    <!-- Featured Products -->
    <x-shop::products.carousel
        :title="trans('shop::app.products.view.related-product-title')"
        :src="route('shop.api.products.related.index', ['id' => $product->id])"
    />

    <!-- Up-sell Products -->
    <x-shop::products.carousel
        :title="trans('shop::app.products.view.up-sell-title')"
        :src="route('shop.api.products.up-sell.index', ['id' => $product->id])"
    />

    {!! view_render_event('bagisto.shop.products.view.after', ['product' => $product]) !!}

    <!-- Steps & FAQ Section -->
    <div class="zob-steps-faq">
        <div class="zob-steps-faq__container">
            <!-- Steps Section -->
            <div class="zob-steps-faq__steps">
                <div class="zob-steps-faq__steps-header">
                    <h2 class="zob-steps-faq__steps-title">Navigating the StyleLoom Fashion Journey.</h2>
                    <p class="zob-steps-faq__steps-subtitle">At StyleLoom, we've designed a straightforward shopping experience to make fashion accessible.</p>
                    <div class="zob-steps-faq__steps-decoration" style="display: none;">
                        <svg width="446" height="446" viewBox="0 0 446 446" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <defs>
                                <clipPath id="steps-clip">
                                    <path d="M223 0C100.5 0 0 100.5 0 223C0 345.5 100.5 446 223 446C345.5 446 446 345.5 446 223C446 100.5 345.5 0 223 0ZM223 400C120.5 400 40 319.5 40 223C40 126.5 120.5 46 223 46C319.5 46 400 126.5 400 223C400 319.5 319.5 400 223 400Z"/>
                                </clipPath>
                            </defs>
                            <g clip-path="url(#steps-clip)">
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
                                
                                <!-- Additional geometric elements -->
                                <rect x="120" y="140" width="40" height="2" fill="#FFFFFF" fill-opacity="0.06"/>
                                <rect x="280" y="180" width="40" height="2" fill="#FFFFFF" fill-opacity="0.06"/>
                                <rect x="160" y="220" width="40" height="2" fill="#FFFFFF" fill-opacity="0.06"/>
                                <rect x="320" y="260" width="40" height="2" fill="#FFFFFF" fill-opacity="0.06"/>
                            </g>
                        </svg>
                    </div>
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
                                
                                <!-- Additional geometric elements -->
                                <rect x="120" y="140" width="40" height="2" fill="#FFFFFF" fill-opacity="0.06"/>
                                <rect x="280" y="180" width="40" height="2" fill="#FFFFFF" fill-opacity="0.06"/>
                                <rect x="160" y="220" width="40" height="2" fill="#FFFFFF" fill-opacity="0.06"/>
                                <rect x="320" y="260" width="40" height="2" fill="#FFFFFF" fill-opacity="0.06"/>
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
                    </div>
                </div>
            </div>
        </div>
    </div>

    @pushOnce('scripts')
        <script
            type="text/x-template"
            id="v-product-template"
        >
            <x-shop::form
                v-slot="{ meta, errors, handleSubmit }"
                as="div"
            >
                <form
                    ref="formData"
                    @submit="handleSubmit($event, addToCart)"
                >
                    <input
                        type="hidden"
                        name="product_id"
                        value="{{ $product->id }}"
                    >

                    <input
                        type="hidden"
                        name="is_buy_now"
                        v-model="is_buy_now"
                    >

                    <div class="container px-[60px] max-1180:px-0">
                        <div class="mt-12 flex gap-9 max-1180:flex-wrap max-lg:mt-0 max-sm:gap-y-4">
                            <!-- Gallery Blade Inclusion -->
                            @include('shop::products.view.gallery')

                            <!-- Details -->
                            <div class="relative max-w-[590px] max-1180:w-full max-1180:max-w-full max-1180:px-5 max-sm:px-4">
                                {!! view_render_event('bagisto.shop.products.name.before', ['product' => $product]) !!}

                                <div class="flex justify-between gap-4">
                                    <h1 class="break-words text-3xl font-medium max-sm:text-xl">
                                        {{ $product->name }}
                                    </h1>

                                    @if (core()->getConfigData('customer.settings.wishlist.wishlist_option'))
                                        <div
                                            class="flex max-h-[46px] min-h-[46px] min-w-[46px] cursor-pointer items-center justify-center rounded-full border bg-white text-2xl transition-all hover:opacity-[0.8] max-sm:max-h-7 max-sm:min-h-7 max-sm:min-w-7 max-sm:text-base"
                                            role="button"
                                            aria-label="@lang('shop::app.products.view.add-to-wishlist')"
                                            tabindex="0"
                                            :class="isWishlist ? 'icon-heart-fill text-red-600' : 'icon-heart'"
                                            @click="addToWishlist"
                                        >
                                        </div>
                                    @endif
                                </div>

                                {!! view_render_event('bagisto.shop.products.name.after', ['product' => $product]) !!}

                                <!-- Rating -->
                                {!! view_render_event('bagisto.shop.products.rating.before', ['product' => $product]) !!}

                                @if ($totalRatings = $reviewHelper->getTotalFeedback($product))
                                    <!-- Scroll To Reviews Section and Activate Reviews Tab -->
                                    <div
                                        class="mt-1 w-max cursor-pointer max-sm:mt-1.5"
                                        role="button"
                                        tabindex="0"
                                        @click="scrollToReview"
                                    >
                                        <x-shop::products.ratings
                                            class="transition-all hover:border-gray-400 max-sm:px-3 max-sm:py-1"
                                            :average="$avgRatings"
                                            :total="$totalRatings"
                                            ::rating="true"
                                        />
                                    </div>
                                @endif

                                {!! view_render_event('bagisto.shop.products.rating.after', ['product' => $product]) !!}

                                <!-- Pricing -->
                                {!! view_render_event('bagisto.shop.products.price.before', ['product' => $product]) !!}

                                <p class="mt-[22px] flex items-center gap-2.5 text-2xl !font-medium max-sm:mt-2 max-sm:gap-x-2.5 max-sm:gap-y-0 max-sm:text-lg">
                                    {!! $product->getTypeInstance()->getPriceHtml() !!}
                                </p>

                                @if (\Webkul\Tax\Facades\Tax::isInclusiveTaxProductPrices())
                                    <span class="text-sm font-normal text-zinc-500 max-sm:text-xs">
                                        (@lang('shop::app.products.view.tax-inclusive'))
                                    </span>
                                @endif

                                @if (count($product->getTypeInstance()->getCustomerGroupPricingOffers()))
                                    <div class="mt-2.5 grid gap-1.5">
                                        @foreach ($product->getTypeInstance()->getCustomerGroupPricingOffers() as $offer)
                                            <p class="text-zinc-500 [&>*]:text-black">
                                                {!! $offer !!}
                                            </p>
                                        @endforeach
                                    </div>
                                @endif

                                {!! view_render_event('bagisto.shop.products.price.after', ['product' => $product]) !!}

                                {!! view_render_event('bagisto.shop.products.short_description.before', ['product' => $product]) !!}

                                <p class="mt-6 text-lg text-zinc-500 max-sm:mt-1.5 max-sm:text-sm">
                                    {!! $product->short_description !!}
                                </p>

                                {!! view_render_event('bagisto.shop.products.short_description.after', ['product' => $product]) !!}

                                @include('shop::products.view.types.simple')

                                @include('shop::products.view.types.configurable')

                                @include('shop::products.view.types.grouped')

                                @include('shop::products.view.types.bundle')

                                @include('shop::products.view.types.downloadable')

                                @include('shop::products.view.types.booking')

                                <!-- Product Actions and Quantity Box -->
                                <div class="mt-8 flex max-w-[470px] gap-4 max-sm:mt-4">

                                    {!! view_render_event('bagisto.shop.products.view.quantity.before', ['product' => $product]) !!}

                                    @if ($product->getTypeInstance()->showQuantityBox())
                                        <x-shop::quantity-changer
                                            name="quantity"
                                            value="1"
                                            class="gap-x-4 rounded-xl px-7 py-4 max-md:py-3 max-sm:gap-x-5 max-sm:rounded-lg max-sm:px-4 max-sm:py-1.5"
                                        />
                                    @endif

                                    {!! view_render_event('bagisto.shop.products.view.quantity.after', ['product' => $product]) !!}

                                    @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                                        <!-- Add To Cart Button -->
                                        {!! view_render_event('bagisto.shop.products.view.add_to_cart.before', ['product' => $product]) !!}

                                        <x-shop::button
                                            type="submit"
                                            class="secondary-button w-full max-w-full max-md:py-3 max-sm:rounded-lg max-sm:py-1.5"
                                            button-type="secondary-button"
                                            :loading="false"
                                            :title="trans('shop::app.products.view.add-to-cart')"
                                            :disabled="! $product->isSaleable(1)"
                                            ::loading="isStoring.addToCart"
                                            ::disabled="isStoring.addToCart"
                                            @click="is_buy_now=0;"
                                        />

                                        {!! view_render_event('bagisto.shop.products.view.add_to_cart.after', ['product' => $product]) !!}
                                    @endif
                                </div>

                                <!-- Buy Now Button -->
                                @if (core()->getConfigData('sales.checkout.shopping_cart.cart_page'))
                                    {!! view_render_event('bagisto.shop.products.view.buy_now.before', ['product' => $product]) !!}

                                    @if (core()->getConfigData('catalog.products.storefront.buy_now_button_display'))
                                        <x-shop::button
                                            type="submit"
                                            class="primary-button mt-5 w-full max-w-[470px] max-md:py-3 max-sm:mt-3 max-sm:rounded-lg max-sm:py-1.5"
                                            button-type="primary-button"
                                            :title="trans('shop::app.products.view.buy-now')"
                                            :disabled="! $product->isSaleable(1)"
                                            ::loading="isStoring.buyNow"
                                            @click="is_buy_now=1;"
                                            ::disabled="isStoring.buyNow"
                                        />
                                    @endif

                                    {!! view_render_event('bagisto.shop.products.view.buy_now.after', ['product' => $product]) !!}
                                @endif

                                {!! view_render_event('bagisto.shop.products.view.additional_actions.before', ['product' => $product]) !!}

                                <!-- Share Buttons -->
                                <div class="mt-10 flex gap-9 max-md:mt-4 max-md:flex-wrap max-sm:justify-center max-sm:gap-3">
                                    {!! view_render_event('bagisto.shop.products.view.compare.before', ['product' => $product]) !!}

                                    <div
                                        class="flex cursor-pointer items-center justify-center gap-2.5 max-sm:gap-1.5 max-sm:text-base"
                                        role="button"
                                        tabindex="0"
                                        @click="is_buy_now=0; addToCompare({{ $product->id }})"
                                    >
                                        @if (core()->getConfigData('catalog.products.settings.compare_option'))
                                            <span
                                                class="icon-compare text-2xl"
                                                role="presentation"
                                            ></span>

                                            @lang('shop::app.products.view.compare')
                                        @endif
                                    </div>

                                    {!! view_render_event('bagisto.shop.products.view.compare.after', ['product' => $product]) !!}
                                </div>

                                {!! view_render_event('bagisto.shop.products.view.additional_actions.after', ['product' => $product]) !!}
                            </div>
                        </div>
                    </div>
                </form>
            </x-shop::form>
        </script>

        <script type="module">
            app.component('v-product', {
                template: '#v-product-template',

                data() {
                    return {
                        isWishlist: Boolean("{{ (boolean) auth()->guard()->user()?->wishlist_items->where('channel_id', core()->getCurrentChannel()->id)->where('product_id', $product->id)->count() }}"),

                        isCustomer: '{{ auth()->guard('customer')->check() }}',

                        is_buy_now: 0,

                        isStoring: {
                            addToCart: false,

                            buyNow: false,
                        },
                    }
                },

                methods: {
                    addToCart(params) {
                        const operation = this.is_buy_now ? 'buyNow' : 'addToCart';

                        this.isStoring[operation] = true;

                        let formData = new FormData(this.$refs.formData);

                        this.ensureQuantity(formData);

                        this.$axios.post('{{ route("shop.api.checkout.cart.store") }}', formData, {
                                headers: {
                                    'Content-Type': 'multipart/form-data'
                                }
                            })
                            .then(response => {
                                if (response.data.message) {
                                    this.$emitter.emit('update-mini-cart', response.data.data);

                                    this.$emitter.emit('add-flash', { type: 'success', message: response.data.message });

                                    if (response.data.redirect) {
                                        window.location.href= response.data.redirect;
                                    }
                                } else {
                                    this.$emitter.emit('add-flash', { type: 'warning', message: response.data.data.message });
                                }

                                this.isStoring[operation] = false;
                            })
                            .catch(error => {
                                this.isStoring[operation] = false;

                                this.$emitter.emit('add-flash', { type: 'warning', message: error.response.data.message });
                            });
                    },

                    addToWishlist() {
                        if (this.isCustomer) {
                            this.$axios.post('{{ route('shop.api.customers.account.wishlist.store') }}', {
                                    product_id: "{{ $product->id }}"
                                })
                                .then(response => {
                                    this.isWishlist = ! this.isWishlist;

                                    this.$emitter.emit('add-flash', { type: 'success', message: response.data.data.message });
                                })
                                .catch(error => {});
                        } else {
                            window.location.href = "{{ route('shop.customer.session.index')}}";
                        }
                    },

                    addToCompare(productId) {
                        /**
                         * This will handle for customers.
                         */
                        if (this.isCustomer) {
                            this.$axios.post('{{ route("shop.api.compare.store") }}', {
                                    'product_id': productId
                                })
                                .then(response => {
                                    this.$emitter.emit('add-flash', { type: 'success', message: response.data.data.message });
                                })
                                .catch(error => {
                                    if ([400, 422].includes(error.response.status)) {
                                        this.$emitter.emit('add-flash', { type: 'warning', message: error.response.data.data.message });

                                        return;
                                    }

                                    this.$emitter.emit('add-flash', { type: 'error', message: error.response.data.message});
                                });

                            return;
                        }

                        /**
                         * This will handle for guests.
                         */
                        let existingItems = this.getStorageValue(this.getCompareItemsStorageKey()) ?? [];

                        if (existingItems.length) {
                            if (! existingItems.includes(productId)) {
                                existingItems.push(productId);

                                this.setStorageValue(this.getCompareItemsStorageKey(), existingItems);

                                this.$emitter.emit('add-flash', { type: 'success', message: "@lang('shop::app.products.view.add-to-compare')" });
                            } else {
                                this.$emitter.emit('add-flash', { type: 'warning', message: "@lang('shop::app.products.view.already-in-compare')" });
                            }
                        } else {
                            this.setStorageValue(this.getCompareItemsStorageKey(), [productId]);

                            this.$emitter.emit('add-flash', { type: 'success', message: "@lang('shop::app.products.view.add-to-compare')" });
                        }
                    },

                    updateQty(quantity, id) {
                        this.isLoading = true;

                        let qty = {};

                        qty[id] = quantity;

                        this.$axios.put('{{ route('shop.api.checkout.cart.update') }}', { qty })
                            .then(response => {
                                if (response.data.message) {
                                    this.cart = response.data.data;
                                } else {
                                    this.$emitter.emit('add-flash', { type: 'warning', message: response.data.data.message });
                                }

                                this.isLoading = false;
                            }).catch(error => this.isLoading = false);
                    },

                    getCompareItemsStorageKey() {
                        return 'compare_items';
                    },

                    setStorageValue(key, value) {
                        localStorage.setItem(key, JSON.stringify(value));
                    },

                    getStorageValue(key) {
                        let value = localStorage.getItem(key);

                        if (value) {
                            value = JSON.parse(value);
                        }

                        return value;
                    },

                    scrollToReview() {
                        let accordianElement = document.querySelector('#review-accordian-button');

                        if (accordianElement) {
                            accordianElement.click();

                            accordianElement.scrollIntoView({
                                behavior: 'smooth'
                            });
                        }

                        let tabElement = document.querySelector('#review-tab-button');

                        if (tabElement) {
                            tabElement.click();

                            tabElement.scrollIntoView({
                                behavior: 'smooth'
                            });
                        }
                    },

                    ensureQuantity(formData) {
                        if (! formData.has('quantity')) {
                            formData.append('quantity', 1);
                        }
                    },
                },
            });
        </script>
    @endPushOnce

    <!-- FAQ JavaScript Functionality -->
    <script>
        // FAQ Functionality - Run after everything is loaded
        function initFAQ() {
            console.log('FAQ JavaScript loaded');
            
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
            document.addEventListener('DOMContentLoaded', initFAQ);
        } else {
            initFAQ();
        }

        // Also try on window load as backup
        window.addEventListener('load', function() {
            setTimeout(initFAQ, 100);
        });
    </script>
</x-shop::layouts>
