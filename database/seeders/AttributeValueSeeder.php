<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AttributeValueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $attributes = \App\Models\Attribute::all();
        foreach ($attributes as $attribute) {
            $colors = ['Red', 'Green', 'Blue','Black'];
            $sizes = ['S', 'M', 'L', 'XL', 'XXL'];
            $fabrics = ['Cotton', 'Polyester', 'Wool', 'Silk'];
            $materials = ['Leather', 'Plastic', 'Wood', 'Metal'];

            if ($attribute->name == 'Color') {
                foreach ($colors as $key => $color) {
                    $attributeValue = new \App\Models\AttributeValue();
                    $attributeValue->attribute_id = $attribute->id;
                    $attributeValue->name = $color;
                    $attributeValue->save();
                }
            } elseif ($attribute->name == 'Size') {
                foreach ($sizes as $key => $size) {
                    $attributeValue = new \App\Models\AttributeValue();
                    $attributeValue->attribute_id = $attribute->id;
                    $attributeValue->name = $size;
                    $attributeValue->save();
                }
            } elseif ($attribute->name == 'Fabric') {
                foreach ($fabrics as $key => $fabric) {
                    $attributeValue = new \App\Models\AttributeValue();
                    $attributeValue->attribute_id = $attribute->id;
                    $attributeValue->name = $fabric;
                    $attributeValue->save();
                }
            } elseif ($attribute->name == 'Material') {
                foreach ($materials as $key => $material) {
                    $attributeValue = new \App\Models\AttributeValue();
                    $attributeValue->attribute_id = $attribute->id;
                    $attributeValue->name = $material;
                    $attributeValue->save();
                }
            }
        }
    }
}
