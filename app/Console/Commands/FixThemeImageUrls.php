<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class FixThemeImageUrls extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bagisto:fix-theme-images';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Fix theme image URLs in the database';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Fixing theme image URLs...');

        $customizations = DB::table('theme_customizations')->get();
        $fixedCount = 0;

        foreach ($customizations as $customization) {
            $translations = DB::table('theme_customization_translations')
                ->where('theme_customization_id', $customization->id)
                ->get();

            foreach ($translations as $translation) {
                if ($translation->options) {
                    $options = json_decode($translation->options, true);
                    $updated = false;

                    if (isset($options['images']) && is_array($options['images'])) {
                        foreach ($options['images'] as &$image) {
                            if (isset($image['image'])) {
                                $originalPath = $image['image'];
                                
                                // If it's a relative storage path, convert to full URL
                                if (str_starts_with($originalPath, 'storage/')) {
                                    $imagePath = str_replace('storage/', '', $originalPath);
                                    $image['image'] = Storage::url($imagePath);
                                    $updated = true;
                                    $this->line("Fixed: {$originalPath} -> {$image['image']}");
                                }
                            }
                        }

                        if ($updated) {
                            DB::table('theme_customization_translations')
                                ->where('id', $translation->id)
                                ->update(['options' => json_encode($options)]);
                            $fixedCount++;
                        }
                    }
                }
            }
        }

        $this->info("Fixed {$fixedCount} theme customizations.");
        $this->info('Theme image URLs have been updated successfully!');
    }
} 