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
                background-color: #111827 !important;
                color: #f9fafb !important;
            }

            [data-theme="dark"] #app {
                background-color: #111827 !important;
            }

            [data-theme="dark"] main {
                background-color: #111827 !important;
            }

            [data-theme="dark"] header {
                background-color: #1f2937 !important;
                border-color: #374151 !important;
            }

            [data-theme="dark"] .bg-white {
                background-color: #1f2937 !important;
            }

            [data-theme="dark"] .text-black {
                color: #f9fafb !important;
            }

            [data-theme="dark"] .border-gray-200,
            [data-theme="dark"] .border-zinc-200 {
                border-color: #374151 !important;
            }

            [data-theme="dark"] .bg-lightOrange {
                background-color: #1f2937 !important;
            }

            [data-theme="dark"] .text-zinc-500 {
                color: #9ca3af !important;
            }

            [data-theme="dark"] .text-zinc-600 {
                color: #d1d5db !important;
            }

            [data-theme="dark"] .bg-zinc-50,
            [data-theme="dark"] .bg-zinc-100 {
                background-color: #374151 !important;
            }

            [data-theme="dark"] input,
            [data-theme="dark"] select,
            [data-theme="dark"] textarea {
                background-color: #374151 !important;
                border-color: #4b5563 !important;
                color: #f9fafb !important;
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
                border-color: #4b5563 !important;
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

            /* Keep Poppins only for specific branded elements */
            .font-poppins, .font-dmserif, .font-unbounded {
                font-family: 'Unbounded', sans-serif !important;
            }

            /* Ensure header and footer use Urbanist */
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
            <main id="main" class="bg-white">
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
