<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreProductRequest;
use App\Http\Traits\FileUploadTrait;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductClipper;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Unit;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductClipperController extends Controller
{
    use FileUploadTrait;

    /**
     * List all product clippers for the authenticated user
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $search = trim((string) $request->input('search'));
        
        // Check if it's an API request
        if ($request->expectsJson()) {
            $productClippers = ProductClipper::where('user_id', $user->id)
                ->when($search !== '', function ($query) use ($search) {
                    $query->where(function ($subQuery) use ($search) {
                        $subQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('product_url', 'like', "%{$search}%");
                    });
                })
                ->with(['category', 'brand'])
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'data' => $productClippers
            ], 200);
        }
        
        // Web request - return view with all product clippers
        $productClippers = ProductClipper::with(['user'])
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($subQuery) use ($search) {
                    $subQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('product_url', 'like', "%{$search}%")
                        ->orWhereHas('user', function ($userQuery) use ($search) {
                            $userQuery->where('name', 'like', "%{$search}%");
                        });
                });
            })
            ->latest()
            ->paginate(10);

        $attributeIds = $productClippers->getCollection()
            ->pluck('attributes')
            ->flatten()
            ->filter(fn ($value) => filled($value) && is_numeric($value))
            ->map(fn ($value) => (int) $value)
            ->unique()
            ->values();

        $attributeMap = Attribute::whereIn('id', $attributeIds)
            ->pluck('name', 'id')
            ->toArray();

        return view('product.clipper.index', compact('productClippers', 'attributeMap'));
    }

    /**
     * Store the product clipper data in the database
     */
    public function store(Request $request)
    {
        // validate the request data
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'product_url' => 'required|url',
            'product_image' => 'nullable',
            'price' => 'nullable',
            'description' => 'nullable|string',
            'attribute' => 'nullable|array',
            'attributes' => 'nullable|array',
            'warranty_policy' => 'nullable|string',
            'is_published' => 'nullable|boolean',
        ]);

        $clipperAttributes = $validated['attributes'] ?? $validated['attribute'] ?? null;

        // Get authenticated user
        $user = Auth::user();

        // Generate unique slug
        $slug = ProductClipper::generateSlug($validated['name']);

        // create a new product clipper record
        $productClipper = new ProductClipper();
        $productClipper->name = $validated['name'];
        $productClipper->slug = $slug;
        $productClipper->user_id = $user->id;
        $productClipper->thumbnail_img = $validated['product_image'] ?? null;
        $productClipper->product_url = $validated['product_url'];
        $productClipper->unit_price = $validated['price'] ?? null;
        $productClipper->description = $validated['description'] ?? null;
        $productClipper->attributes = $clipperAttributes;
        $productClipper->warranty_policy = $validated['warranty_policy'] ?? null;
        $productClipper->is_approved = 0;
        $productClipper->is_published = $validated['is_published'] ?? false;
        $productClipper->save();

        // return a response
        return response()->json([
            'message' => 'Product clipper data stored successfully.',
            'data' => $productClipper
        ], 201);
    }

    

    /**
     * Get a specific product clipper
     */
    public function show(Request $request, $id)
    {
        $user = Auth::user();
        
        $productClipper = ProductClipper::where('id', $id)
            ->where('user_id', $user->id)
            ->with(['category', 'brand'])
            ->first();

        if (!$productClipper) {
            return response()->json([
                'message' => 'Product clipper not found.'
            ], 404);
        }

        return response()->json([
            'data' => $productClipper
        ], 200);
    }

    /**
     * Update a product clipper
     */
    public function update(Request $request, $id)
    {
        $user = Auth::user();
        
        $productClipper = ProductClipper::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$productClipper) {
            return response()->json([
                'message' => 'Product clipper not found.'
            ], 404);
        }

        // validate the request data
        $validated = $request->validate([
            'name' => 'sometimes|string|max:255',
            'product_url' => 'sometimes|url',
            'product_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp|max:2048',
            'unit_price' => 'nullable|numeric',
            'purchase_price' => 'nullable|numeric',
            'description' => 'nullable|string',
            'attribute' => 'nullable|array',
            'attributes' => 'nullable|array',
            'attribute.*' => 'nullable',
            'attributes.*' => 'nullable',
            'privacy_policy' => 'nullable|string',
            'category_id' => 'nullable|exists:categories,id',
            'brand_id' => 'nullable|exists:brands,id',
            'discount_type' => 'nullable|in:1,2',
            'discount' => 'nullable|numeric',
            'unit' => 'nullable|string|max:50',
            'quantity' => 'nullable|integer|min:0',
            'is_published' => 'nullable|boolean',
        ]);

        // Update fields
        if (isset($validated['name'])) {
            $productClipper->name = $validated['name'];
            $productClipper->slug = ProductClipper::generateSlug($validated['name']);
        }
        if (isset($validated['category_id'])) {
            $productClipper->category_id = $validated['category_id'];
        }
        if (isset($validated['brand_id'])) {
            $productClipper->brand_id = $validated['brand_id'];
        }
        
        // Handle file upload
        if ($request->hasFile('product_image')) {
            // Delete old file if exists
            if ($productClipper->thumbnail_img) {
                $this->deleteFile($productClipper->thumbnail_img);
            }
            $imagePath = $this->uploadFile($request->file('product_image'), 'product-clippers');
            $productClipper->thumbnail_img = $imagePath;
            $productClipper->product_image = $imagePath;
        }
        
        if (isset($validated['product_url'])) {
            $productClipper->product_url = $validated['product_url'];
        }
        if (isset($validated['unit_price'])) {
            $productClipper->unit_price = $validated['unit_price'];
        }
        if (isset($validated['purchase_price'])) {
            $productClipper->purchase_price = $validated['purchase_price'];
        }
        if (isset($validated['description'])) {
            $productClipper->description = $validated['description'];
        }
        if (array_key_exists('attributes', $validated) || array_key_exists('attribute', $validated)) {
            $productClipper->attributes = $validated['attributes'] ?? $validated['attribute'];
        }
        if (array_key_exists('privacy_policy', $validated)) {
            $productClipper->privacy_policy = $validated['privacy_policy'];
        }
        if (isset($validated['discount_type'])) {
            $productClipper->discount_type = $validated['discount_type'];
        }
        if (isset($validated['discount'])) {
            $productClipper->discount = $validated['discount'];
        }
        if (isset($validated['unit'])) {
            $productClipper->unit = $validated['unit'];
        }
        if (isset($validated['quantity'])) {
            $productClipper->quantity = $validated['quantity'];
        }
        if (isset($validated['is_published'])) {
            $productClipper->is_published = $validated['is_published'];
        }

        $productClipper->save();

        return response()->json([
            'message' => 'Product clipper updated successfully.',
            'data' => $productClipper
        ], 200);
    }

    /**
     * Delete a product clipper
     */
    public function destroy(Request $request, $id)
    {
        $user = Auth::user();
        
        $productClipper = ProductClipper::where('id', $id)
            ->where('user_id', $user->id)
            ->first();

        if (!$productClipper) {
            return response()->json([
                'message' => 'Product clipper not found.'
            ], 404);
        }

        $productClipper->delete();

        return response()->json([
            'message' => 'Product clipper deleted successfully.'
        ], 200);
    }

    public function edit($id)
    {

        $product = ProductClipper::find($id);
        $categories = Category::where('active_status', 1)->get();
        $brands = Brand::where('active_status', 1)->get();
        $units = Unit::where('is_active', 1)->get();
        $attributes = Attribute::where('status', 1)->get();
        return view('product.clipper.edit', compact('categories', 'brands', 'units', 'attributes', 'product'));
    }


    public function mapWeightAndDimensions(array $weightAndDimensions): array
    {
        if (empty($weightAndDimensions['title'][0]) && empty($weightAndDimensions['details'][0])) {
            return [];
        }

        return collect($weightAndDimensions['title'] ?? [])
            ->map(function ($title, $key) use ($weightAndDimensions) {
                return [
                    'title' => $title,
                    'details' => $weightAndDimensions['details'][$key] ?? null,
                ];
            })
            ->filter(fn($item) => $item['title'] && $item['details'])
            ->toArray();
    }

    public function mapSpecifications(array $specifications): array
    {
        if (empty($specifications['title'][0]) && empty($specifications['value'][0])) {
            return [];
        }

        return collect($specifications['title'] ?? [])
            ->map(function ($title, $key) use ($specifications) {
                return [
                    'title' => $title,
                    'details' => $specifications['value'][$key] ?? null,
                ];
            })
            ->filter(fn($item) => $item['title'] && $item['details'])
            ->toArray();
    }

    public function migrate(Request $request)
    {
        try {

            DB::beginTransaction();
            $weightAndDimensions = $this->mapWeightAndDimensions($request['weightAndDiamensions'] ?? []);
            $specifications = $this->mapSpecifications($request['specifications'] ?? []);

            $product = new Product();
            $product->name = $request->title;
            $product->slug = createSlug($request->title);
            $product->user_id = getUserId();
            $product->category_id = $request->category;
            $product->brand_id = $request->brand_id;
            $product->unit = $request->unit;
            $product->video_link = $request->video_link;
            $product->attributes = $request['attributes'] ?? [];
            $product->choice_options = $request['attribute_values'] ?? [];
            $product->barcode = $request->barcode;
            $product->num_of_sale = 0;
            $product->description = $request->description;
            $product->unit_price = abs($request->unit_price);
            $product->weight_dimensions = $weightAndDimensions;
            $product->specifications = $specifications;
            $product->shipping_policy = $request->shipping_policy;
            $product->return_policy = $request->return_policy;
            $product->disclaimer = $request->disclaimer;
            $product->discount_type = $request->discount_type;
            $product->discount = $request->discount_value;
            $product->meta_title = $request->title;
            $product->meta_description = Str::limit(strip_tags($request->description), 160);

            $product->is_price_hidden = isset($request->is_price_hidden) ? true : false;

            if ($request->has('status')) {
                $product->is_published = $request->status;
            }
            $product->save();

            $clipperProduct = ProductClipper::where('id', $request->product_clipper_id)->first();
            $clipperProduct->attributes = $request['attributes'] ?? [];
            $clipperProduct->privacy_policy = $request->privacy_policy;
            if ($request->hasFile('thumbnail')) {
                $path = $this->uploadFile($request->file('thumbnail'), 'products/' . $product->id);
                $product->thumbnail_img = $path;
            }else {
                $client = new \GuzzleHttp\Client([
                    'headers' => ['User-Agent' => 'Mozilla/5.0'] // Helps prevent blocking
                ]);
    
                try {
                    $response = $client->get($clipperProduct->thumbnail_img, ['allow_redirects' => true]);
    
                    if ($response->getStatusCode() === 200) {
                        $contentType = $response->getHeaderLine('Content-Type');
    
                        if (strpos($contentType, 'image/') === 0) {
                            $tempFilePath = tempnam(sys_get_temp_dir(), 'image_');
                            file_put_contents($tempFilePath, $response->getBody()->getContents());
    
                            $tempFile = new \Illuminate\Http\File($tempFilePath);
                            $product = Product::find($product->id);
                            $path = $this->uploadFile($tempFile, 'products/' . $product->id);
                            $product->thumbnail_img = $path;
                            $product->save();
                            unlink($tempFilePath);
                        }
                    }
                } catch (\Exception $e) {
                    \Log::error("Image download failed: " . $e->getMessage());
                }
            }

            $product->save();
            $clipperProduct->is_migrate = 1;
            $clipperProduct->save();


            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $this->uploadFile($image, 'products/' . $product->id);
                    $productImage = new ProductImage();
                    $productImage->path = $path;
                    $productImage->product_id = $product->id;
                    $productImage->save();
                }
            }

            if ($request->variant) {

                foreach ($request['variant'] as $key => $combination) {
                    $product_variant = new ProductVariant();
                    $product_variant->product_id = $product->id;
                    $product_variant->variant = $combination['name'];
                    $product_variant->price = abs($combination['price'] ?? 0);
                    $product_variant->discount_type = $request->discount_type;
                    $product_variant->discount_amount = $combination['discount'];
                    $product_variant->qty = abs($combination['quantity']);
                    if (!array_key_exists("active", $combination)) {
                        $product_variant->is_active = 0;
                    }
                    $product_variant->save();


                    if ($request['variant'][$key]['image'] != 'undefined') {
                        $path = $this->uploadFile($combination['image'], 'products/' . $product->id);
                        $product_variant->image = $path;
                    }

                    $product_variant->save();
                }
            }

            DB::commit();

            removeDataFromRedisAPI(['product_list']);
            removeDataFromRedisAPI(['special_section_list']);
            removeDataFromRedisAPI(['slider_list']);

            Toastr::success('Product Created Successfully');

            return response()->json(['message' => 'Product Created Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            dd($e);
            DB::rollBack();
            return response()->json(['message' => "Something went wrong"], 500);
        }
    }
}
