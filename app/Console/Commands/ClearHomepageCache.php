<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Spatie\ResponseCache\Facades\ResponseCache;

class ClearHomepageCache extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'cache:homepage {--force : Force clear all homepage cache}';

    /**
     * The console command.
     *
     * @var string
     */
    protected $description = 'Clear homepage cache specifically';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Clearing homepage cache...');

        if ($this->option('force')) {
            // Clear all response cache
            ResponseCache::clear();
            $this->info('✅ All response cache cleared');
        } else {
            // Clear only homepage cache
            ResponseCache::selectCachedItems()
                ->forUrls(config('app.url').'/')
                ->forget();
            $this->info('✅ Homepage cache cleared');
        }

        $this->info('💡 Remember to refresh your browser with Ctrl+F5');
    }
}
