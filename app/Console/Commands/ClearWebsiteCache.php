<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\ResponseCache\Facades\ResponseCache;

class ClearWebsiteCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:website {--homepage : Clear only homepage cache} {--force : Force clear all website cache}';

    /**
     * The console command.
     *
     * @var string
     */
    protected $description = 'Clear website cache (entire site or homepage only)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        if ($this->option('homepage')) {
            $this->info('Clearing homepage cache...');
            ResponseCache::selectCachedItems()
                ->forUrls(config('app.url').'/')
                ->forget();
            $this->info('✅ Homepage cache cleared');
        } else {
            $this->info('Clearing entire website cache...');
            ResponseCache::clear();
            $this->info('✅ Entire website cache cleared');
        }

        $this->info('💡 Remember to refresh your browser with Ctrl+F5');
    }
}
