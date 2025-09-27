<?php

namespace Webkul\FPC\Listeners;

use Spatie\ResponseCache\Facades\ResponseCache;

class AdminSEO
{
    /**
     * After admin SEO update
     *
     * @param  mixed  $seo
     * @return void
     */
    public function afterUpdate($seo)
    {
        // Clear homepage cache when admin SEO is updated
        ResponseCache::selectCachedItems()
            ->forUrls(config('app.url').'/')
            ->forget();
    }
}
