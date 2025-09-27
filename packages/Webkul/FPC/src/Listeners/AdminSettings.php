<?php

namespace Webkul\FPC\Listeners;

use Spatie\ResponseCache\Facades\ResponseCache;

class AdminSettings
{
    /**
     * After admin settings update
     *
     * @param  mixed  $settings
     * @return void
     */
    public function afterUpdate($settings)
    {
        // Clear homepage cache when admin settings are updated
        ResponseCache::selectCachedItems()
            ->forUrls(config('app.url').'/')
            ->forget();
    }
}
