{!! view_render_event('bagisto.shop.layout.header.before') !!}

@if(core()->getCurrentChannel()->locales()->count() > 1 || core()->getCurrentChannel()->currencies()->count() > 1 )
    <div class="max-lg:hidden">
        <x-shop::layouts.header.desktop.top />
    </div>
@endif

<header class="shadow-gray sticky top-0 z-10 bg-white dark:bg-[#1a1a1a]" style="margin-top: 10px; border-radius: 20px; width: 98%; margin: auto; margin-top: 20px;background: transparent !important;">
    <x-shop::layouts.header.desktop />

    <x-shop::layouts.header.mobile />
</header>

{!! view_render_event('bagisto.shop.layout.header.after') !!}
