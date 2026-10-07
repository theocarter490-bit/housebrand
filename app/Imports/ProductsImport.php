<?php

namespace App\Imports;

use App\Http\Traits\FileUploadTrait;
use App\Models\Product;
use App\Models\ShopSetting;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\Importable;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Maatwebsite\Excel\Concerns\SkipsFailures;
use Illuminate\Support\Str;

class ProductsImport implements ToModel, WithHeadingRow, WithValidation
{
    use Importable, SkipsFailures, FileUploadTrait;

    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    public function model(array $row)
    {
        $new_tag = str_replace(' ', '', $row['meta_keywords']);
        $new_tag = explode(",", $new_tag);
        $shop_setting = ShopSetting::where('user_id', getUserId())->first();


        $productID = Product::insertGetId([
            'name' => $row['name'],
            'user_id' => getUserId(),
            'slug' => Str::slug($row['name']) . md5(uniqid(rand(), true)),
            'category_id' => $row['category_id'],
            'brand_id' => $row['brand_id'],
            'video_link' => $row['video_link'],
            'description' => $row['description'],
            'unit_price' => $row['unit_price'],
            'shipping_policy' => $shop_setting->shipping_policy,
            'return_policy' => $shop_setting->return_policy,
            'disclaimer' => $shop_setting->disclaimer,
            'discount_type' => $row['discount_type'],
            'discount' => $row['discount'],
            'meta_title' => $row['name'],
            'meta_description' => $row['description'],
            'meta_keywords' => json_encode($new_tag),
            'is_published' => 0,
            'attributes' => json_encode([]), // Serialize empty array as JSON
            'choice_options' => json_encode([]), // Serialize empty array as JSON
            'weight_dimensions' => json_encode([]), // Serialize empty array as JSON
            'specifications' => json_encode([]), // Serialize empty array as JSON
        ]);


        $product = Product::find($productID);
        if ($row['thumbnail_image']) {
            $originalUrl = $row['thumbnail_image'];

            // Check if it's a Google Drive link
            if (strpos($originalUrl, 'drive.google.com') !== false) {
                // Try to extract file ID from shareable link
                if (preg_match('/\/d\/(.*?)\//', $originalUrl, $matches)) {
                    $fileId = $matches[1];
                    $downloadUrl = "https://drive.google.com/uc?export=download&id={$fileId}";
                } else {
                    // If format is invalid, skip processing
                    return;
                }
            } else {
                // Assume it's a direct link
                $downloadUrl = $originalUrl;
            }

            $client = new \GuzzleHttp\Client([
                'headers' => ['User-Agent' => 'Mozilla/5.0'] // Helps prevent blocking
            ]);

            try {
                $response = $client->get($downloadUrl, ['allow_redirects' => true]);

                if ($response->getStatusCode() === 200) {
                    $contentType = $response->getHeaderLine('Content-Type');

                    if (strpos($contentType, 'image/') === 0) {
                        $tempFilePath = tempnam(sys_get_temp_dir(), 'image_');
                        file_put_contents($tempFilePath, $response->getBody()->getContents());

                        $tempFile = new \Illuminate\Http\File($tempFilePath);
                        $product = Product::find($productID);
                        $path = $this->uploadFile($tempFile, 'products/' . $productID);
                        $product->thumbnail_img = $path;
                        $product->save();
                        unlink($tempFilePath);
                    }
                }
            } catch (\Exception $e) {
                \Log::error("Image download failed: " . $e->getMessage());
            }
        }

        return $product;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'thumbnail_image' => 'nullable|url',
            'shipping_policy' => 'nullable',
            'return_policy' => 'nullable',
            'disclaimer' => 'nullable',
            'unit_price' => 'required|numeric',
            'discount_type' => 'required|in:0,1,2',
            'discount' => 'required_if:discount_type,1,2',
            'category_id' => 'required|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
        ];
    }
}
