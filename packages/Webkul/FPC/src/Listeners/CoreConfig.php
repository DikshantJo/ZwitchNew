<?php

namespace Webkul\FPC\Listeners;

use Spatie\ResponseCache\Facades\ResponseCache;

class CoreConfig
{
    /**
     * After core configuration update.
     *
     * @return void
     */
    public function afterUpdate()
    {
        // Clear homepage cache specifically for core config changes
        ResponseCache::selectCachedItems()
            ->forUrls(config('app.url').'/')
            ->forget();
            
        // Also clear any other cached pages that might be affected
        ResponseCache::clear();
    }
}
