@props([
    'hasHeader'  => true,
    'hasFeature' => true,
    'hasFooter'  => true,
])

<!DOCTYPE html>

@php
    $globalTheme = core()->getConfigData('general.design.frontend_theme.mode') ?? 'light';
    
    // Christmas theme logic: when Christmas is selected, apply dark theme
    $isChristmasTheme = $globalTheme === 'christmas';
    $effectiveTheme = $isChristmasTheme ? 'dark' : $globalTheme;
@endphp

<html
    lang="{{ app()->getLocale() }}"
    dir="{{ core()->getCurrentLocale()->direction }}"
    data-theme="{{ $effectiveTheme }}"
    @if($isChristmasTheme) data-christmas="true" @endif
>
    <head>
        <!-- CRITICAL: Prevent other theme switchers from overriding admin panel setting -->
        <script>
            (function() {
                // Store the server-side theme setting immediately
                const serverTheme = '{{ $globalTheme }}';
                const effectiveTheme = '{{ $effectiveTheme }}';
                const isChristmas = {{ $isChristmasTheme ? 'true' : 'false' }};
                
                console.log('Bagisto: Server theme set to:', serverTheme, 'Effective theme:', effectiveTheme, 'Christmas:', isChristmas);
                
                // Clear ALL conflicting localStorage entries immediately
                localStorage.removeItem('theme');
                localStorage.removeItem('bagisto-theme');
                
                // Ensure the data-theme attribute is set correctly
                document.documentElement.setAttribute('data-theme', effectiveTheme);
                if (isChristmas) {
                    document.documentElement.setAttribute('data-christmas', 'true');
                }
                
                // Override any existing theme switchers
                window.bagistoServerTheme = serverTheme;
                window.bagistoEffectiveTheme = effectiveTheme;
                window.bagistoIsChristmas = isChristmas;
                
                // Prevent other scripts from changing the theme
                const originalSetAttribute = document.documentElement.setAttribute;
                document.documentElement.setAttribute = function(name, value) {
                    if (name === 'data-theme') {
                        console.log('Bagisto: Theme change attempted:', value, 'Server theme:', serverTheme);
                        if (value === effectiveTheme || window.bagistoAppInitialized) {
                            originalSetAttribute.call(this, name, value);
                        } else {
                            console.log('Bagisto: Blocked unauthorized theme change');
                        }
                    } else {
                        originalSetAttribute.call(this, name, value);
                    }
                };
                
                // Force the theme to persist
                setTimeout(() => {
                    document.documentElement.setAttribute('data-theme', effectiveTheme);
                    if (isChristmas) {
                        document.documentElement.setAttribute('data-christmas', 'true');
                    }
                    console.log('Bagisto: Theme forced to:', effectiveTheme);
                }, 50);
            })();
        </script>

        {!! view_render_event('bagisto.shop.layout.head.before') !!}

        <title>{{ $title ?? '' }}</title>

        <meta charset="UTF-8">

        <meta
            http-equiv="X-UA-Compatible"
            content="IE=edge"
        >
        <meta
            http-equiv="content-language"
            content="{{ app()->getLocale() }}"
        >

        <meta
            name="viewport"
            content="width=device-width, initial-scale=1"
        >
        <meta
            name="base-url"
            content="{{ url()->to('/') }}"
        >
        <meta
            name="currency"
            content="{{ core()->getCurrentCurrency()->toJson() }}"
        >

        @stack('meta')

        <link
            rel="icon"
            sizes="16x16"
            href="{{ core()->getCurrentChannel()->favicon_url ?? bagisto_asset('images/favicon.ico') }}"
        />

        @bagistoVite(['src/Resources/assets/css/app.css', 'src/Resources/assets/js/app.js'])

        <link
            rel="preload"
            href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
            as="style"
        >
        <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700;800&display=swap"
        >

        <link
            rel="preload"
            href="https://fonts.googleapis.com/css2?family=Unbounded:wght@300;400;500;600;700;800;900&display=swap"
            as="style"
        >
        <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Unbounded:wght@300;400;500;600;700;800;900&display=swap"
        >

        <link
            rel="preload"
            href="https://fonts.googleapis.com/css2?family=Urbanist:wght@300;400;500;600;700&display=swap"
            as="style"
        >
        <link
            rel="stylesheet"
            href="https://fonts.googleapis.com/css2?family=Urbanist:wght@300;400;500;600;700&display=swap"
        >

        @stack('styles')

        <style>
            {!! core()->getConfigData('general.content.custom_scripts.custom_css') !!}
        </style>

        <style>

            
            /* Dark Mode Styles - Inline for immediate effect */
            [data-theme="dark"] {
                color-scheme: dark;
            }

            [data-theme="dark"] body {
                background-color: #1a1a1a !important;
                color: #f9fafb !important;
            }

            [data-theme="dark"] #app {
                background-color: #1a1a1a !important;
            }

            [data-theme="dark"] main {
                background-color: #1a1a1a !important;
            }

            [data-theme="dark"] .container {
                background-color: #1a1a1a !important;
            }

            [data-theme="dark"] header {
                background-color: #1a1a1a !important;
                border-color: #404040 !important;
            }

            [data-theme="dark"] .bg-white {
                background-color: #1a1a1a !important;
            }

            [data-theme="dark"] .text-black {
                color: #f9fafb !important;
            }

            [data-theme="dark"] .border-gray-200,
            [data-theme="dark"] .border-zinc-200 {
                border-color: #404040 !important;
            }

            [data-theme="dark"] .bg-lightOrange {
                background-color: #1a1a1a !important;
            }

            [data-theme="dark"] .text-zinc-500 {
                color: #9ca3af !important;
            }

            [data-theme="dark"] .text-zinc-600 {
                color: #d1d5db !important;
            }

            [data-theme="dark"] .bg-zinc-50,
            [data-theme="dark"] .bg-zinc-100 {
                background-color: #404040 !important;
            }

            [data-theme="dark"] input,
            [data-theme="dark"] select,
            [data-theme="dark"] textarea {
                background-color: #404040 !important;
                border-color: #555555 !important;
                color: #f9fafb !important;
            }

            [data-theme="dark"] input::placeholder,
            [data-theme="dark"] textarea::placeholder {
                color: #9ca3af !important;
            }

            [data-theme="dark"] input:focus,
            [data-theme="dark"] select:focus,
            [data-theme="dark"] textarea:focus {
                border-color: #2563eb !important;
                outline: none !important;
                box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;
            }

            [data-theme="dark"] .form-control-group label {
                color: #f9fafb !important;
            }

            [data-theme="dark"] .form-control-group .required::after {
                color: #ef4444 !important;
            }

            [data-theme="dark"] label {
                color: #f9fafb !important;
            }

            [data-theme="dark"] .text-red-500 {
                color: #f87171 !important;
            }

            [data-theme="dark"] .g-recaptcha {
                background-color: #404040 !important;
            }

            [data-theme="dark"] iframe {
                background-color: #404040 !important;
            }

            [data-theme="dark"] .dark\\:bg-gray-800 {
                background-color: #1a1a1a !important;
            }

            [data-theme="dark"] .dark\\:bg-gray-700 {
                background-color: #404040 !important;
            }

            [data-theme="dark"] .dark\\:bg-gray-900 {
                background-color: #1a1a1a !important;
            }

            [data-theme="dark"] .dark\\:text-white {
                color: #f9fafb !important;
            }

            [data-theme="dark"] .dark\\:text-gray-300 {
                color: #d1d5db !important;
            }

            [data-theme="dark"] .dark\\:border-gray-600 {
                border-color: #555555 !important;
            }

            /* Logo styling for both themes */
            .rounded-full {
                border-radius: 50% !important;
            }

            /* Ensure logo has proper sizing and maintains aspect ratio */
            img[alt*="logo"], img[alt*="Logo"] {
                object-fit: cover;
                border-radius: 50%;
                transition: transform 0.2s ease-in-out;
                box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            }

            img[alt*="logo"]:hover, img[alt*="Logo"]:hover {
                transform: scale(1.05);
            }

            /* Dark theme logo adjustments */
            [data-theme="dark"] img[alt*="logo"], 
            [data-theme="dark"] img[alt*="Logo"] {
                filter: brightness(1.1);
                box-shadow: 0 2px 8px rgba(255, 255, 255, 0.1);
            }

            /* Override form control hardcoded classes for dark theme */
            [data-theme="dark"] .text-gray-600 {
                color: #f9fafb !important;
            }

            [data-theme="dark"] .border-gray-200 {
                border-color: #555555 !important;
            }

            [data-theme="dark"] .hover\\:border-gray-400:hover {
                border-color: #666666 !important;
            }

            [data-theme="dark"] .focus\\:border-gray-400:focus {
                border-color: #2563eb !important;
            }

            /* Search input styling with 50px border radius */
            input[name="query"] {
                border-radius: 50px !important;
            }

            /* Ensure search input works in both themes */
            [data-theme="dark"] input[name="query"] {
                background-color: #404040 !important;
                border-color: #555555 !important;
                color: #f9fafb !important;
            }

            [data-theme="dark"] input[name="query"]::placeholder {
                color: #9ca3af !important;
            }

            [data-theme="dark"] input[name="query"]:focus {
                border-color: #2563eb !important;
                box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1) !important;
            }

            [data-theme="dark"] .primary-button {
                background-color: #2563eb !important;
                color: #ffffff !important;
            }

            [data-theme="dark"] .secondary-button {
                background-color: #4b5563 !important;
                color: #f9fafb !important;
                border-color: #4b5563 !important;
            }

            [data-theme="dark"] .text-gray-900 {
                color: #f9fafb !important;
            }

            [data-theme="dark"] .text-gray-700 {
                color: #d1d5db !important;
            }

            [data-theme="dark"] .text-gray-600 {
                color: #9ca3af !important;
            }

            [data-theme="dark"] .border-gray-300,
            [data-theme="dark"] .border-gray-400 {
                border-color: #555555 !important;
            }

            [data-theme="dark"] .bg-navyBlue {
                background-color: #2563eb !important;
            }

            [data-theme="dark"] .text-navyBlue {
                color: #60a5fa !important;
            }

            [data-theme="dark"] .border-navyBlue {
                border-color: #2563eb !important;
            }

            /* Urbanist Font - Global Application */
            * {
                font-family: 'Urbanist', sans-serif !important;
            }

            /* Unbounded Font for Titles */
            h1, h2, h3, h4, h5, h6,
            .font-poppins, .font-dmserif, .font-unbounded,
            .text-2xl, .text-3xl, .text-4xl, .text-5xl, .text-6xl,
            .text-xl, .text-lg,
            .font-semibold, .font-bold,
            .product-name, .product-title,
            .category-name, .category-title,
            .brand-name, .brand-title,
            .section-title, .page-title,
            .card-title, .item-title,
            .nav-title, .menu-title,
            .footer-title, .header-title {
                font-family: 'Unbounded', sans-serif !important;
            }

            /* Ensure header and footer use Urbanist for body text */
            header, footer, nav, .header, .footer {
                font-family: 'Urbanist', sans-serif !important;
            }

            /* Apply to all text elements in header and footer */
            header *, footer *, nav *, .header *, .footer * {
                font-family: 'Urbanist', sans-serif !important;
            }

            /* Override font-dmserif class to use Unbounded */
            .font-dmserif {
                font-family: 'Unbounded', sans-serif !important;
            }

            /* Specific title overrides for better coverage */
            .product-name, .product-title {
                font-family: 'Unbounded', sans-serif !important;
            }

            .category-name, .category-title {
                font-family: 'Unbounded', sans-serif !important;
            }

            .brand-name, .brand-title {
                font-family: 'Unbounded', sans-serif !important;
            }

            .section-title, .page-title {
                font-family: 'Unbounded', sans-serif !important;
            }

            .card-title, .item-title {
                font-family: 'Unbounded', sans-serif !important;
            }

            .nav-title, .menu-title {
                font-family: 'Unbounded', sans-serif !important;
            }

            .footer-title, .header-title {
                font-family: 'Unbounded', sans-serif !important;
            }

            /* Steps & FAQ Section Styles */
            .zob-steps-faq {
                width: 100%;
                min-height: 100vh;
                background: #1a1a1a;
                padding: 40px 20px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 24px;
                margin: 40px 0;
            }

            /* Steps Section Styles */
            .zob-steps-faq__steps {
                margin-bottom: 80px;
            }

            .zob-steps-faq__steps-header {
                position: relative;
                padding: 80px 80px 80px 80px;
                margin-bottom: 0;
            }

            .zob-steps-faq__steps-title {
                font-family: 'Unbounded', sans-serif;
                font-size: 48px;
                font-weight: 600;
                color: #ffffff;
                margin-bottom: 30px;
                line-height: 1;
                text-transform: uppercase;
                letter-spacing: 0.63px;
                max-width: 634px;
            }

            .zob-steps-faq__steps-subtitle {
                font-family: 'Urbanist', sans-serif;
                font-size: 18px;
                font-weight: 400;
                color: #676665;
                line-height: 1.5;
                max-width: 634px;
                letter-spacing: 0.63px;
            }

            .zob-steps-faq__steps-decoration {
                position: absolute;
                top: 50%;
                right: -165px;
                transform: translateY(-50%);
                width: 446px;
                height: 446px;
                opacity: 0.1;
                pointer-events: none;
            }

            .zob-steps-faq__steps-decoration svg {
                width: 100%;
                height: 100%;
            }

            .zob-steps-faq__steps-cards {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 0;
            }

            .zob-steps-faq__steps-card {
                position: relative;
                background: transparent;
                padding: 50px;
                border-radius: 0;
                overflow: hidden;
            }

            .zob-steps-faq__steps-card:not(:last-child)::after {
                content: '';
                position: absolute;
                top: 0;
                right: 0;
                bottom: 0;
                width: 2px;
                border-right: 2px dashed #333;
                pointer-events: none;
            }

            .zob-steps-faq__steps-step-number {
                font-family: 'Urbanist', sans-serif;
                font-size: 20px;
                font-weight: 400;
                color: #676665;
                margin-bottom: 30px;
                line-height: 1.5;
            }

            .zob-steps-faq__steps-card-content {
                margin-bottom: 0;
            }

            .zob-steps-faq__steps-card-title {
                font-family: 'Unbounded', sans-serif;
                font-size: 28px;
                font-weight: 600;
                color: #ffffff;
                margin-bottom: 16px;
                line-height: 1.5;
            }

            .zob-steps-faq__steps-card-description {
                font-family: 'Urbanist', sans-serif;
                font-size: 18px;
                font-weight: 400;
                color: #81807e;
                line-height: 1.5;
            }

            .zob-steps-faq__container {
                width: 95%;
                max-width: 1400px;
                margin: 0 auto;
            }

            .zob-steps-faq__faq {
                border-top: 2px dashed #333;
                padding-top: 80px;
            }

            .zob-steps-faq__faq-header {
                text-align: left;
                margin-bottom: 60px;
                padding: 0px;
                position: relative;
                max-width: 600px;
            }

            .zob-steps-faq__faq-header::before {
                content: '';
                position: absolute;
                bottom: 0;
                left: 80px;
                right: 300px;
                height: 2px;
                border-bottom: 2px dashed #333;
                pointer-events: none;
                display: none;
            }

            .zob-steps-faq__faq-title {
                font-family: 'Unbounded', sans-serif;
                font-size: 48px;
                font-weight: 600;
                color: #ffffff;
                margin-bottom: 20px;
                line-height: 1;
                text-transform: uppercase;
                letter-spacing: 0.63px;
            }

            .zob-steps-faq__faq-subtitle {
                font-family: 'Urbanist', sans-serif;
                font-size: 18px;
                font-weight: 400;
                color: #676665;
                line-height: 1.5;
                max-width: 634px;
                letter-spacing: 0.63px;
            }

            .zob-steps-faq__faq-decoration {
                position: absolute;
                top: 50%;
                right: -165px;
                transform: translateY(-50%);
                width: 446px;
                height: 446px;
                opacity: 0.1;
                pointer-events: none;
            }

            .zob-steps-faq__faq-decoration svg {
                width: 100%;
                height: 100%;
            }

            .zob-steps-faq__faq-content {
                display: flex;
                justify-content: space-between;
                gap: 40px;
            }

            .zob-steps-faq__faq-column {
                flex: 1;
            }

            .zob-steps-faq__faq-item {
                background: #1a1a1a;
                border-radius: 12px;
                margin-bottom: 15px;
                overflow: hidden;
                border: 1px solid #333;
            }

            .zob-steps-faq__faq-item:last-child {
                margin-bottom: 0;
            }

            .zob-steps-faq__faq-question {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 20px 25px;
                cursor: pointer;
                background: #1a1a1a;
                border-bottom: 1px solid #333;
                user-select: none;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
            }

            .zob-steps-faq__faq-question:hover {
                background: #2a2a2a;
            }

            .zob-steps-faq__faq-question-text {
                font-family: 'Unbounded', sans-serif;
                font-size: 18px;
                font-weight: 600;
                color: #ffffff;
                margin-bottom: 0;
                line-height: 1.5;
            }

            .zob-steps-faq__faq-toggle {
                background: none;
                border: none;
                font-size: 24px;
                color: #676665;
                cursor: pointer;
                transition: transform 0.3s ease;
                user-select: none;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
                pointer-events: none;
            }

            .zob-steps-faq__faq-answer {
                padding: 0 25px 20px;
                font-family: 'Urbanist', sans-serif;
                font-size: 16px;
                font-weight: 400;
                color: #81807e;
                line-height: 1.6;
                display: none;
            }

            .zob-steps-faq__faq-item.active .zob-steps-faq__faq-answer {
                display: block;
            }

            .zob-steps-faq__faq-item.active .zob-steps-faq__faq-toggle {
                transform: rotate(180deg);
            }

            .zob-steps-faq__faq-tabs {
                display: flex;
                gap: 14px;
                margin-top: 30px;
                flex-wrap: wrap;
            }

            .zob-steps-faq__faq-tab {
                background: transparent;
                border: 2px dashed #333;
                border-radius: 12px;
                padding: 18px 24px;
                font-family: 'Urbanist', sans-serif;
                font-size: 18px;
                font-weight: 400;
                color: #b3b3b2;
                cursor: pointer;
                transition: all 0.3s ease;
                white-space: nowrap;
                user-select: none;
                -webkit-user-select: none;
                -moz-user-select: none;
                -ms-user-select: none;
            }

            .zob-steps-faq__faq-tab:hover {
                background: rgba(194, 180, 163, 0.1);
                color: #ffffff;
            }

            .zob-steps-faq__faq-tab--active {
                background: #c2b4a3;
                border-color: #c2b4a3;
                color: #0f0f0f;
            }

            .zob-steps-faq__faq-item.hidden {
                display: none !important;
            }

            /* Show FAQ answers when parent is active */
            .zob-steps-faq__faq-item.active .zob-steps-faq__faq-answer {
                display: block !important;
            }

            /* Mobile responsive design for Steps & FAQ */
            @media (max-width: 768px) {
                .zob-steps-faq {
                    padding: 20px 15px;
                    min-height: auto;
                }

                .zob-steps-faq__container {
                    width: 100%;
                }

                /* Steps Section Mobile */
                .zob-steps-faq__steps-header {
                    padding: 40px 20px;
                }

                .zob-steps-faq__steps-title {
                    font-size: 32px;
                    margin-bottom: 20px;
                }

                .zob-steps-faq__steps-subtitle {
                    font-size: 16px;
                }

                .zob-steps-faq__steps-decoration {
                    width: 200px;
                    height: 200px;
                    right: -50px;
                }

                .zob-steps-faq__steps-cards {
                    grid-template-columns: 1fr;
                }

                .zob-steps-faq__steps-card {
                    padding: 30px 20px;
                }

                .zob-steps-faq__steps-card::after {
                    display: none;
                }

                .zob-steps-faq__steps-card:nth-child(n+2)::before {
                    content: '';
                    position: absolute;
                    top: 0;
                    left: 0;
                    right: 0;
                    height: 2px;
                    border-top: 2px dashed #333;
                    pointer-events: none;
                }

                .zob-steps-faq__steps-step-number {
                    font-size: 16px;
                    margin-bottom: 20px;
                }

                .zob-steps-faq__steps-card-title {
                    font-size: 22px;
                    margin-bottom: 10px;
                }

                .zob-steps-faq__steps-card-description {
                    font-size: 15px;
                }

                /* FAQ Section Mobile */
                .zob-steps-faq__faq-header {
                    padding: 30px 15px;
                }

                .zob-steps-faq__faq-title {
                    font-size: 28px;
                    margin-bottom: 15px;
                }

                .zob-steps-faq__faq-subtitle {
                    font-size: 14px;
                }

                .zob-steps-faq__faq-decoration {
                    width: 150px;
                    height: 150px;
                    right: -30px;
                }

                .zob-steps-faq__faq-content {
                    flex-direction: column;
                    gap: 20px;
                }

                .zob-steps-faq__faq-tabs {
                    gap: 6px;
                    margin-top: 15px;
                }

                .zob-steps-faq__faq-tab {
                    padding: 10px 12px;
                    font-size: 12px;
                }

                .zob-steps-faq__faq-question {
                    padding: 12px 15px;
                }

                .zob-steps-faq__faq-question-text {
                    font-size: 13px;
                }

                .zob-steps-faq__faq-answer {
                    padding: 0 15px 12px;
                    font-size: 12px;
                }
            }
        </style>

        @if(core()->getConfigData('general.content.speculation_rules.enabled'))
            <script type="speculationrules">
                @json(core()->getSpeculationRules(), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE)
            </script>
        @endif

        {!! view_render_event('bagisto.shop.layout.head.after') !!}

    </head>

    <body>
        {!! view_render_event('bagisto.shop.layout.body.before') !!}

        <!-- Christmas Snowflakes Component -->
        @if($isChristmasTheme)
            <x-shop::snowflakes />
        @endif

        <a
            href="#main"
            class="skip-to-main-content-link"
        >
            Skip to main content
        </a>

        <div id="app">
            <!-- Flash Message Blade Component -->
            <x-shop::flash-group />

            <!-- Confirm Modal Blade Component -->
            <x-shop::modal.confirm />

            <!-- Page Header Blade Component -->
            @if ($hasHeader)
                <x-shop::layouts.header />
            @endif

            @if(
                core()->getConfigData('general.gdpr.settings.enabled')
                && core()->getConfigData('general.gdpr.cookie.enabled')
            )
                <x-shop::layouts.cookie />
            @endif

            {!! view_render_event('bagisto.shop.layout.content.before') !!}

            <!-- Page Content Blade Component -->
            <main id="main" class="bg-white dark:bg-[#1a1a1a]">
                {{ $slot }}
            </main>

            {!! view_render_event('bagisto.shop.layout.content.after') !!}


            <!-- Page Services Blade Component -->
            @if ($hasFeature)
                <x-shop::layouts.services />
            @endif

            <!-- Page Footer Blade Component -->
            @if ($hasFooter)
                <x-shop::layouts.footer />
            @endif
        </div>

        {!! view_render_event('bagisto.shop.layout.body.after') !!}

        @stack('scripts')

        {!! view_render_event('bagisto.shop.layout.vue-app-mount.before') !!}
        <script>
            /**
             * Load event, the purpose of using the event is to mount the application
             * after all of our `Vue` components which is present in blade file have
             * been registered in the app. No matter what `app.mount()` should be
             * called in the last.
             */
            window.addEventListener("load", function (event) {
                app.mount("#app");
            });
        </script>

        {!! view_render_event('bagisto.shop.layout.vue-app-mount.after') !!}

        <script type="text/javascript">
            {!! core()->getConfigData('general.content.custom_scripts.custom_javascript') !!}
        </script>
    </body>
</html>
