<?php

use Webkul\Theme\Facades\Themes;
use Webkul\Theme\ViewRenderEventManager;

if (! function_exists('themes')) {
    /**
     * Themes.
     *
     * @return \Webkul\Theme\Themes
     */
    function themes()
    {
        return Themes::getFacadeRoot();
    }
}

if (! function_exists('bagisto_asset')) {
    /**
     * Bagisto asset.
     *
     * @return string
     */
    function bagisto_asset(string $path, ?string $namespace = null)
    {
        return themes()->url($path, $namespace);
    }
}

if (! function_exists('process_theme_html')) {
    /**
     * Process theme HTML content to fix image URLs.
     *
     * @param  string  $html
     * @return string
     */
    function process_theme_html(string $html): string
    {
        // Use Laravel's asset() helper to get the correct base URL
        $baseUrl = rtrim(asset(''), '/');
        
        // Handle URLs that start with port numbers (like 8008/storage/theme/...)
        $html = preg_replace('/src="(\d+)\/storage\/theme\//', 'src="' . $baseUrl . '/storage/theme/', $html);
        $html = preg_replace('/data-src="(\d+)\/storage\/theme\//', 'data-src="' . $baseUrl . '/storage/theme/', $html);
        
        // Convert relative storage paths to absolute URLs
        $html = preg_replace('/src="storage\/theme\//', 'src="' . $baseUrl . '/storage/theme/', $html);
        $html = preg_replace('/data-src="storage\/theme\//', 'data-src="' . $baseUrl . '/storage/theme/', $html);
        
        return $html;
    }
}

if (! function_exists('view_render_event')) {
    /**
     * View render event.
     *
     * @param  string  $eventName
     * @param  mixed  $params
     * @return mixed
     */
    function view_render_event($eventName, $params = null)
    {
        app()->singleton(ViewRenderEventManager::class);

        $viewEventManager = app()->make(ViewRenderEventManager::class);

        $viewEventManager->handleRenderEvent($eventName, $params);

        return $viewEventManager->render();
    }
}
