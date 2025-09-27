<?php

namespace Webkul\FPC\Listeners;

use Spatie\ResponseCache\Facades\ResponseCache;

class AdminContent
{
    /**
     * After admin content update
     *
     * @param  mixed  $content
     * @return void
     */
    public function afterUpdate($content)
    {
        // Clear homepage cache when admin content is updated
        ResponseCache::selectCachedItems()
            ->forUrls(config('app.url').'/')
            ->forget();
    }
}
