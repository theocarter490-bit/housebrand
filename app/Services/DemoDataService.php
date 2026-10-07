<?php

namespace App\Services;

use App\Http\Traits\FileUploadTrait;
use App\Models\Category;
use App\Models\Gallery;
use App\Models\GalleryDetails;
use App\Models\SpecialSection;
use App\Models\SpecialSectionCategory;
use App\Models\User;
use App\Models\Slider;
use App\Models\Product;
use App\Models\SpecialSectionDetail;
use App\Models\SpecialSectionDetailItem;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class DemoDataService
{
    use FileUploadTrait;

    public function generateInitialDataForDesigner(User $user): void
    {
        DB::transaction(function () use ($user) {
            $this->generateSliders($user->id);
            $this->generateProducts($user->id);
            $this->generateGalleries($user->id);
            $this->generateSpecialSection($user->id);
        });
    }

    public function uploadDemoImage(string $relativePublicPath, string $folder): ?string
    {
        $fullPath = public_path($relativePublicPath);

        if (!File::exists($fullPath)) {
            return null;
        }

        $file = new UploadedFile(
            $fullPath,
            basename($fullPath),
            File::mimeType($fullPath),
            null,
            true
        );
        return $this->uploadFile($file, $folder);
    }
    private function generateSliders(int $userId): void
    {
        if (Slider::where('user_id', $userId)->exists()) return;

        for ($i = 1; $i <= 2; $i++) {
            $imagePath = $this->uploadDemoImage("default/slider/demo-$i.jpg", 'slider/demo-slider');

            Slider::create([
                'title' => "Demo Slider $i",
                'description' => "This is a sample description for slider $i.",
                'user_id' => $userId,
                'type' => 0,
                'active_status' => 1,
                'file_type' => 0,
                'image' => $imagePath,
            ]);
        }
    }
    private function generateProducts(int $userId): void
    {
        if (Product::where('user_id', $userId)->exists()) return;

        $categories = Category::where('active_status', 1)->take(4)->get();

        foreach ($categories as $key => $cat) {
            $product                    = new Product();
            $product->name              = "Demo Product " . ($key + 1);
            $product->slug              = createSlug($product->name . '-' . $userId);
            $product->thumbnail_img     = $this->uploadDemoImage("default/product/demo-" . ($key + 1) . ".jpg", 'products/demo-product');
            $product->description       = "This is a sample product description for demo " . ($key + 1) . ".";
            $product->category_id       = $cat->id;
            $product->unit              = 1;
            $product->unit_price        = rand(1000, 5000);
            $product->user_id           = $userId;
            $product->is_published      = 1;
            $product->attributes        = [];
            $product->choice_options    = [];
            $product->specifications    = [];
            $product->weight_dimensions = [];
            $product->save();
        }
    }

    private function generateSpecialSection(int $userId)
    {

        $categories = ['Category 1', 'Category 2'];

        foreach ($categories as $key => $ca) {
            if (!SpecialSectionCategory::where('name', $ca)->where('user_id', $userId)->exists()) {
                $cat                = new SpecialSectionCategory();
                $cat->name          = $ca;
                $cat->slug          = Str::slug($ca);
                $cat->user_id       = $userId;
                $cat->is_active     = 1;
                $cat->created_by    = $userId;
                $cat->save();
            }
        }

        if (!SpecialSection::where('user_id', $userId)->exists()) {
            $types = [1, 2]; // 1: Portfolio, 2: Inspiration
                foreach ($types as $type) {
                    for ($i = 1; $i <= 6; $i++) {
                        $title = ($type == 1 ? 'Demo Portfolio' : 'Demo Inspiration');

                        $imagePath = $this->uploadDemoImage(
                            "default/special-section/demo-" . $i . ".jpg",
                            'specialSection'
                        );

                        $section            = new SpecialSection();
                        $section->title     = $title;
                        $section->image     = $imagePath;
                        $section->slug      = Str::slug($title);
                        $section->user_id   = $userId;
                        $section->type      = $type;
                        $section->is_active = 1;
                        $section->created_by= $userId;
                        $section->special_section_category_id = SpecialSectionCategory::where('user_id', $userId)->get()->random()->id;
                        $section->save();
                        
                        if($section){
                            $details = new SpecialSectionDetail();
                            $details->special_section_id    = $section->id;
                            $details->section_type          = 'description';
                            $details->created_by            = $userId;
                            $details->save();

                            $item = new SpecialSectionDetailItem();
                            $item->special_section_detail_id = $details->id;
                            $item->title        = 'Luxurious Living Room Design';
                            $item->description  = 'This design fuses sophistication with comfort, featuring a sleek velvet sofa, marble-top coffee table, and metallic-accented sideboard. Rich jewel tones combined with gold details create an opulent yet inviting atmosphere. Plush textures, such as a faux fur throw and silk cushions, add depth and warmth, while abstract wall art and sculptural décor complete the luxurious aesthetic.';
                            $item->save();
                            
                            $details = new SpecialSectionDetail();
                            $details->special_section_id    = $section->id;
                            $details->section_type          = 'banner_image';
                            $details->created_by            = $userId;
                            $details->save();

                            $item = new SpecialSectionDetailItem();
                            $item->special_section_detail_id = $details->id;
                            $item->image = $this->uploadDemoImage(
                                "default/special-section/details/1.jpg",
                                'specialSection'
                            );
                            $item->product_ids = Product::where('user_id', $userId)->pluck('id')->toArray();
                            $item->save();

                        }

                    }
                }
        }

    }

    private function generateGalleries(int $userId): void
    {
        if (Gallery::where('user_id', $userId)->exists()) {
            return;
        }
        $names = ['Gallery 2', 'Gallery 1'];

        foreach ($names as $key => $name) {
            if (!Gallery::where('name', $name)->where('user_id', $userId)->exists()) {
                $gallery = new Gallery();
                $gallery->name        = $name;
                $gallery->slug        = Str::slug($name);
                $gallery->image       = $this->uploadDemoImage("default/gallery/demo-" . ($key + 1) . ".jpg", 'gallery/demo-gallery');
                $gallery->user_id     = $userId;
                $gallery->is_active   = 1;
                $gallery->created_by  = $userId;
                $gallery->save();

                if ($gallery) {
                    for ($i = 1; $i <= 6; $i++) {
                        $details = new GalleryDetails();
                        $details->title      = "Gallery Item " . $i;
                        $details->image      = $this->uploadDemoImage("default/gallery/details/demo-" . (($key * 6) + $i) . ".jpg", 'gallery/demo-gallery/items');
                        $details->gallery_id = $gallery->id;
                        $details->save();
                    }
                }
            }
        }
    }
}
