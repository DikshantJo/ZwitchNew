<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DarkThemeConfigSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now();

        // Check if the configuration already exists
        $existingConfig = DB::table('core_config')
            ->where('code', 'general.design.frontend_theme.mode')
            ->first();

        if (!$existingConfig) {
            DB::table('core_config')->insert([
                'code'         => 'general.design.frontend_theme.mode',
                'value'        => 'dark', // Set to dark mode by default
                'channel_code' => null,
                'locale_code'  => null,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);

            $this->command->info('Dark theme configuration added successfully!');
        } else {
            $this->command->info('Dark theme configuration already exists.');
        }
    }
} 