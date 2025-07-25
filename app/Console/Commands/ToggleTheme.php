<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ToggleTheme extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'theme:toggle {mode? : The theme mode (light/dark)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Toggle between light and dark themes';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $mode = $this->argument('mode');
        
        if (!$mode) {
            // Get current theme
            $currentTheme = DB::table('core_config')
                ->where('code', 'general.design.frontend_theme.mode')
                ->value('value') ?? 'light';
            
            // Toggle to opposite
            $mode = $currentTheme === 'light' ? 'dark' : 'light';
        }
        
        if (!in_array($mode, ['light', 'dark'])) {
            $this->error('Invalid mode. Use "light" or "dark".');
            return 1;
        }

        // Update or insert the configuration
        DB::table('core_config')->updateOrInsert(
            ['code' => 'general.design.frontend_theme.mode'],
            [
                'value' => $mode,
                'channel_code' => null,
                'locale_code' => null,
                'updated_at' => now(),
            ]
        );

        $this->info("Theme switched to {$mode} mode successfully!");
        
        return 0;
    }
} 