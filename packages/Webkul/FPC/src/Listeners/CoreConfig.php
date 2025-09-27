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
        // Clear entire website cache for core configuration changes
        ResponseCache::clear();
    }
}
