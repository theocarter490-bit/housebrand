<?php

namespace Database\Seeders;

use Faker\Factory;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 100; $i++) {
            $product = new \App\Models\Product();
            $product->name = $faker->text(30);
            $product->slug = Str::slug($product->name);
            $product->user_id = 5;
            $product->category_id = rand(1, 5);
            $product->brand_id = rand(1, 5);
            $product->video_link = 'https://www.youtube.com/watch?v=6v2L2UGZJAM';
            $product->meta_keywords = '["Tag1","Tag2","Tag3"]';
            $product->barcode = rand(10, 12);
            $product->description = "<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>";
            $product->unit_price = rand(1, 1000);
            $product->purchase_price = rand(1, 1000);
            $product->attributes = [1, 3];
            $product->choice_options = [['attribute_id' => "1", 'value' => ['S', 'M']],['attribute_id' => "3", 'value' => ['Red', 'Green']]];
            $product->weight_dimensions = [];
            $product->specifications = [];
            $product->shipping_policy = "<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>";
            $product->return_policy = "<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>";
            $product->disclaimer = "<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>";
            $product->discount_type = rand(1, 2);
            $product->discount = rand(1, 20);
            $product->tax_type = rand(1, 2);
            $product->tax = rand(1, 20);
            $product->unit = "PC";
            $product->num_of_sale = 0;
            $product->meta_title = 'meta title for product ' . $i;
            $product->meta_description = "<p>Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book.</p>";
            $product->is_approved = 1;
            $product->is_published = 1;
            $product->save();

            $variants = ['S-Red', 'S-Green', 'M-Red', 'M-Green'];

            foreach ($variants as $variant) {
                $productVariant = new \App\Models\ProductVariant();
                $productVariant->product_id = $product->id;
                $productVariant->variant = $variant;
                $productVariant->price = rand(1, 1000);
                $productVariant->sku = 'SKU' . rand(100, 999);
                $productVariant->qty = rand(1, 100);
                $productVariant->save();
            }
        }
    }
}
