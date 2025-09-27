<?php

namespace Webkul\FPC\Listeners;

use Spatie\ResponseCache\Facades\ResponseCache;
use Webkul\Theme\Repositories\ThemeCustomizationRepository;

class ThemeCustomization
{
    /**
     * Create a new listener instance.
     *
     * @return void
     */
    public function __construct(protected ThemeCustomizationRepository $themeCustomizationRepository) {}

    /**
     * After theme customization create
     *
     * @param  \Webkul\Shop\Contracts\ThemeCustomization  $themeCustomization
     * @return void
     */
    public function afterCreate($themeCustomization)
    {
        // Clear entire website cache for all theme customizations
        ResponseCache::clear();
    }

    /**
     * After theme customization update
     *
     * @param  \Webkul\Shop\Contracts\ThemeCustomization  $themeCustomization
     * @return void
     */
    public function afterUpdate($themeCustomization)
    {
        // Clear entire website cache for all theme customizations
        ResponseCache::clear();
    }

    /**
     * Before theme customization delete
     *
     * @param  int  $themeCustomizationId
     * @return void
     */
    public function beforeDelete($themeCustomizationId)
    {
        // Clear entire website cache for all theme customizations
        ResponseCache::clear();
    }
}
