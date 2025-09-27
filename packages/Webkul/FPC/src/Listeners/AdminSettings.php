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
        // Clear entire website cache when admin settings are updated
        ResponseCache::clear();
    }
}
