<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ZwitchProductContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Update product descriptions with Zwitch-specific content
        $zwitchDescription = "Discover the essence of Zwitch Originals - where every piece tells a story and turns heads. Our exclusive collection features handmade, limited-edition t-shirts and apparel that go beyond fashion to create statements. Each design is carefully crafted to reflect the unique spirit of those who dare to stand out. Whether you choose our iconic black or white tees, or opt for regular or oversized fits, every piece is designed to be uniquely yours. Zwitch isn't just apparel - it's your invitation to own the spotlight and create lasting memories with every wear.";
        
        $zwitchShortDescription = "Exclusive handmade, limited-edition t-shirts and apparel that tell stories and turn heads. Choose from our iconic black or white tees in regular or oversized fits.";
        
        // Update all products with the new Zwitch content
        DB::table('product_flat')->update([
            'description' => $zwitchDescription,
            'short_description' => $zwitchShortDescription,
        ]);
        
        // Also update the product_attribute_values table for description and short_description
        $descriptionAttributeId = DB::table('attributes')
            ->where('code', 'description')
            ->value('id');
            
        $shortDescriptionAttributeId = DB::table('attributes')
            ->where('code', 'short_description')
            ->value('id');
        
        if ($descriptionAttributeId) {
            DB::table('product_attribute_values')
                ->where('attribute_id', $descriptionAttributeId)
                ->update(['text_value' => $zwitchDescription]);
        }
        
        if ($shortDescriptionAttributeId) {
            DB::table('product_attribute_values')
                ->where('attribute_id', $shortDescriptionAttributeId)
                ->update(['text_value' => $zwitchShortDescription]);
        }
        
        // Update category descriptions with Zwitch-specific content
        $zwitchCategoryDescription = "Welcome to Zwitch Originals - your destination for exclusive handmade, limited-edition t-shirts and apparel. Each piece in our collection is carefully crafted to tell a unique story and turn heads wherever you go. Browse through our curated selection of iconic black and white tees, available in regular and oversized fits, all designed to be uniquely yours. Discover the perfect blend of style, comfort, and individuality that defines the Zwitch experience.";
        
        // Update category translations with the new Zwitch content
        DB::table('category_translations')->update([
            'description' => $zwitchCategoryDescription,
        ]);
        
        $this->command->info('Zwitch product and category content updated successfully!');
    }
}
