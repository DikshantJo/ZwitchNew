{!! view_render_event('bagisto.shop.layout.footer.before') !!}

<!--
    The category repository is injected directly here because there is no way
    to retrieve it from the view composer, as this is an anonymous component.
-->
@inject('themeCustomizationRepository', 'Webkul\Theme\Repositories\ThemeCustomizationRepository')

<!--
    This code needs to be refactored to reduce the amount of PHP in the Blade
    template as much as possible.
-->
@php
    $channel = core()->getCurrentChannel();

    $customization = $themeCustomizationRepository->findOneWhere([
        'type'       => 'footer_links',
        'status'     => 1,
        'theme_code' => $channel->theme,
        'channel_id' => $channel->id,
    ]);

    // Get footer links data with proper fallback and validation
    $footerLinks = [];
    if ($customization && isset($customization->options) && is_array($customization->options)) {
        $footerLinks = $customization->options;
        
        // Validate and sanitize each link
        foreach ($footerLinks as $columnKey => &$columnLinks) {
            if (is_array($columnLinks)) {
                $columnLinks = array_filter($columnLinks, function($link) {
                    return is_array($link) && 
                           isset($link['url']) && 
                           isset($link['title']) && 
                           !empty(trim($link['url'])) && 
                           !empty(trim($link['title'])) &&
                           filter_var($link['url'], FILTER_VALIDATE_URL);
                });
                
                // Sort by sort_order if available
                usort($columnLinks, function($a, $b) {
                    $sortA = isset($a['sort_order']) ? (int)$a['sort_order'] : 0;
                    $sortB = isset($b['sort_order']) ? (int)$b['sort_order'] : 0;
                    return $sortA - $sortB;
                });
            }
        }
    }

    // Define column titles (these could be made configurable in admin later)
    $columnTitles = [
        'column_1' => 'Quick Links',
        'column_2' => 'Quick Links', 
        'column_3' => 'Quick Links'
    ];
@endphp

<footer class="bg-black dark:bg-[#000000] text-white font-['Urbanist']">
    <!-- Logo and Social Media Section -->
    <div class="flex justify-between items-start px-[162px] pt-[100px] py-[50px] max-xl:px-16 max-lg:px-8 max-md:flex-col max-md:gap-8 max-md:px-6 max-sm:px-4">
        <!-- Logo and Description -->
        <div class="flex flex-col gap-6 max-md:gap-4" style="align-items: flex-start;">
            <div class="text-white text-6xl font-semibold flex items-center max-lg:text-3xl max-md:text-2xl max-sm:text-xl" style="font-family: 'Unbounded', sans-serif !important;">
                <span style="font-family: 'Unbounded', sans-serif !important;">Zwitch</span>
                <div class="w-2 h-2 bg-[#d6cdc2] rounded-full mx-6 max-sm:mx-3"></div>
                <span style="font-family: 'Unbounded', sans-serif !important;">Originals</span>
            </div>
            <p class="text-[18px] text-[#81807e] font-normal leading-[1.5] font-['Roboto_Mono'] max-lg:text-base max-sm:text-sm" style= 'width:80%'>
                Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos. Lorem ipsum dolor sit amet consectetur adipisicing elit. Quisquam, quos.
            </p>
            
            <!-- Social Media Buttons -->
            <div class="flex gap-5 max-sm:gap-3 mt-8 max-md:mt-6">
                <div class="bg-[#ffffff] p-4 rounded-xl max-sm:p-3" style='background-color: #ffffff;'>
                    <div class="w-[34px] h-[34px] flex items-center justify-center max-sm:w-[28px] max-sm:h-[28px]">
                        <svg class="w-full h-full" fill="#000000" viewBox="0 0 24 24" aria-hidden="true" style="color: #000000 !important;">
                            <path fill-rule="evenodd" d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <div class="bg-[#ffffff] p-4 rounded-xl max-sm:p-3" style='background-color: #ffffff;'>
                    <div class="w-[34px] h-[34px] flex items-center justify-center max-sm:w-[28px] max-sm:h-[28px]">
                        <svg class="w-full h-full" fill="#000000" viewBox="0 0 24 24" aria-hidden="true" style="color: #000000 !important;">
                            <path fill-rule="evenodd" d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z" clip-rule="evenodd" />
                        </svg>
                    </div>
                </div>
                <div class="bg-[#ffffff] p-4 rounded-xl max-sm:p-3" style='background-color: #ffffff;'>
                    <div class="w-[34px] h-[34px] flex items-center justify-center max-sm:w-[28px] max-sm:h-[28px]">
                        <svg class="w-full h-full" fill="#000000" viewBox="0 0 24 24" aria-hidden="true" style="color: #000000 !important;">
                            <path d="M8.29 20.251c7.547 0 11.675-6.253 11.675-11.675 0-.178 0-.355-.012-.53A8.348 8.348 0 0022 5.92a8.19 8.19 0 01-2.357.646 4.118 4.118 0 001.804-2.27 8.224 8.224 0 01-2.605.996 4.107 4.107 0 00-6.993 3.743 11.65 11.65 0 01-8.457-4.287 4.106 4.106 0 001.27 5.477A4.072 4.072 0 012.8 9.713v.052a4.105 4.105 0 003.292 4.022 4.095 4.095 0 01-1.853.07 4.108 4.108 0 003.834 2.85A8.233 8.233 0 012 18.407a11.616 11.616 0 006.29 1.84" />
                        </svg>
                    </div>
                </div>
                <div class="bg-[#ffffff] p-4 rounded-xl max-sm:p-3" style='background-color: #ffffff;'>
                    <div class="w-[34px] h-[34px] flex items-center justify-center max-sm:w-[28px] max-sm:h-[28px]">
                        <svg class="w-full h-full" fill="#000000" viewBox="0 0 24 24" aria-hidden="true" style="color: #000000 !important;">
                            <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/>
                        </svg>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Main Footer Content -->
    <div class="flex gap-20 px-[162px] max-xl:px-16 max-lg:px-8 max-lg:gap-12 max-md:flex-col max-md:gap-8 max-md:px-6 max-sm:px-4 max-sm:gap-6">
        <!-- Dynamic Footer Links Section -->
        <div class="flex-1 flex flex-col gap-10 max-lg:gap-8 max-md:gap-6">
            @foreach(['column_1', 'column_2', 'column_3'] as $columnKey)
                @if(isset($footerLinks[$columnKey]) && !empty($footerLinks[$columnKey]))
                    <div class="flex flex-col gap-[30px] max-md:gap-6">
                        <div class="text-[22px] font-medium text-white leading-normal font-['Roboto'] max-md:text-xl max-sm:text-lg">
                            {{ $columnTitles[$columnKey] ?? 'Links' }}
                        </div>
                        <div class="flex items-center gap-4 max-md:flex-wrap max-md:gap-2 max-sm:flex-col max-sm:items-start max-sm:gap-1">
                            @foreach($footerLinks[$columnKey] as $index => $link)
                                @if(isset($link['url']) && isset($link['title']))
                                    <a href="{{ $link['url'] }}" class="text-[20px] text-[#676665] font-normal leading-[1.5] font-['Roboto_Mono'] max-md:text-lg max-sm:text-base hover:text-white transition-colors duration-200">
                                        {{ $link['title'] }}
                                    </a>
                                    @if($index < count($footerLinks[$columnKey]) - 1)
                                        <div class="w-1.5 h-1.5 max-md:hidden">
                                            <div class="w-full h-full bg-[#676665] rounded-full"></div>
                                        </div>
                                    @endif
                                @endif
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>

        <!-- Right Column - Newsletter -->
        <div class="flex-1 flex flex-col gap-[30px] max-md:gap-6">
            <div class="text-[22px] font-medium text-white leading-normal font-['Roboto'] max-md:text-xl max-sm:text-lg">
                Subscribe to Newsletter
            </div>
        {!! view_render_event('bagisto.shop.layout.footer.newsletter_subscription.before') !!}

        @if (core()->getConfigData('customer.settings.newsletter.subscription'))
                    <x-shop::form
                        :action="route('shop.subscription.store')"
                    class="w-full"
                    >
                    <div class="bg-[#1a1a1a] flex items-center justify-between px-6 py-[18px] rounded-xl w-full max-sm:px-4 max-sm:py-3">
                            <x-shop::form.control-group.control
                                type="email"
                            class="bg-transparent border-none text-[18px] text-[#676665] placeholder-[#676665] focus:outline-none flex-1 font-['Roboto_Mono'] max-sm:text-base"
                                name="email"
                                rules="required|email"
                            placeholder="Your Email"
                            style="max-width: 400px;"
                            />
                            <button
                                type="submit"
                            class="w-6 h-6 flex items-center justify-center max-sm:w-5 max-sm:h-5"
                            >
                            <svg class="w-full h-full text-[#676665]" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                <path fill-rule="evenodd" d="M12.97 3.97a.75.75 0 011.06 0l7.5 7.5a.75.75 0 010 1.06l-7.5 7.5a.75.75 0 11-1.06-1.06l6.22-6.22H3a.75.75 0 010-1.5h16.19l-6.22-6.22a.75.75 0 010-1.06z" clip-rule="evenodd" />
                            </svg>
                            </button>
                        </div>
                    </x-shop::form>
        @endif

        {!! view_render_event('bagisto.shop.layout.footer.newsletter_subscription.after') !!}
        </div>
    </div>

    <!-- Bottom Footer -->
    <div class="flex justify-between items-start px-[162px] py-[50px] max-xl:px-16 max-lg:px-8 max-md:flex-col max-md:gap-4 max-md:px-6 max-sm:px-4 max-sm:py-6">
        <div class="text-[18px] text-[#81807e] font-normal leading-[1.5] font-['Roboto_Mono'] max-md:text-center max-sm:text-sm">
            © {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
        </div>
        <div class="flex items-start gap-[11px] max-sm:flex-col max-sm:gap-2">
            <a href="#" class="text-[18px] text-[#81807e] font-normal leading-[1.5] font-['Roboto_Mono'] max-sm:text-sm">Terms & Conditions</a>
            <div class="w-[27px] h-[1px] max-sm:hidden">
                                        <div class="w-full h-full bg-[#676665]"></div>
            </div>
            <a href="#" class="text-[18px] text-[#81807e] font-normal leading-[1.5] font-['Roboto_Mono'] max-sm:text-sm">Privacy Policy</a>
        </div>
    </div>
</footer>

{!! view_render_event('bagisto.shop.layout.footer.after') !!}
