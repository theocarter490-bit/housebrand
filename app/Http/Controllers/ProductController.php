<?php

namespace App\Http\Controllers;


use Exception;
use App\Models\Role;
use App\Models\Unit;
use App\Models\User;
use App\Models\Brand;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\Category;
use App\Models\Attribute;
use App\Models\ShopSetting;
use Illuminate\Support\Str;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\App;
use App\Http\Traits\FileUploadTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Validator;
use Illuminate\Support\Facades\Cache;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Requests\IdValidationRequest;
use App\Http\Requests\StoreProductRequest;
use App\Http\Requests\UpdateProductRequest;
use App\Models\DesignerSharedProduct;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    use FileUploadTrait;

    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = Product::with(['reviews', 'category', 'brand', 'whiteLableProduct', 'user' => function ($query) {
                $query->with('role', 'shop');
            }])->isClient();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('thumbnail_img', function ($row) {
                    return '<img src="' . getFilePath($row->thumbnail_img) . '" width="50px" height="50px"/>';
                })
                ->editColumn('name', function ($row) {

                    $html = '';

                    // Determine URL based on role
                    if ($row->user->role_id == 3) {
                        // Designer
                        $productUrl = env('APP_FRONTEND_URL') . '/designer/' . $row->shop->slug . '/product/' . $row->id . '-' . $row->slug;
                    } else {
                        // Normal user
                        $productUrl = env('APP_FRONTEND_URL') . '/product/' . $row->id . '-' . $row->slug;
                    }

                    // Main product link
                    $html .= '<a href="' . $productUrl . '" target="_blank">' . $row->name . '</a>';

                    // White label product link
                    if ($row->whiteLableProduct) {

                        // White-label product URL (also role dependent)
                        if ($row->whiteLableProduct->user->role_id == 3) {
                            $whiteLabelUrl = env('APP_FRONTEND_URL') . '/designer/' . $row->whiteLableProduct->shop->slug . '/product/' . $row->whiteLableProduct->id . '-' . $row->whiteLableProduct->slug;
                        } else {
                            $whiteLabelUrl = env('APP_FRONTEND_URL') . '/product/' . $row->whiteLableProduct->id . '-' . $row->whiteLableProduct->slug;
                        }

                        $html .= '<a href="' . $whiteLabelUrl . '" class="badge bg-label-warning d-flex mt-1 align-items-center justify-content-center" style="width: fit-content !important; margin-left:6px;">White Label</a>';
                    }

                    return $html;
                })
                ->addColumn('added_by', function ($row) {
                    $html = '<p class="m-0">Name: ' . optional($row->user)->name . '</p>';
                    $html .= '<p class="m-0">Shop Name: ' . optional(optional($row->user)->shop)->shop_name . '</p>';
                    $html .= '<p class="m-0">Role: <span class="badge bg-label-info">' . optional(optional($row->user)->role)->name . '</span></p>';
                    return $html;
                })
                ->editColumn('category_name', function ($row) {
                    $html = "";
                    if ($row->category) {
                        if ($row->category->active_status == 0) {
                            $html .= '<span class="text-danger" title="Inactive category — the product will not appear on the frontend.">' . $row->category->name . '</span>';
                        } else {
                            $html = $row->category->name ?? '-------';
                        }
                    }
                    return $html;
                })
                ->editColumn('brand_name', function ($row) {
                    $html = "";
                    if ($row->brand) {
                        if ($row->brand->active_status == 0) {
                            $html .= '<span class="text-danger" title="Inactive Brand — the product will not appear on the frontend.">' . $row->brand->name . '</span>';
                        } else {
                            $html .= $row->brand->name ?? '-------';
                        }
                    }
                    return $html;
                })
                ->addColumn('review', function ($row) {
                    $averageRating = getProductAverageRating($row);
                    ob_start();
                    renderStarRating($averageRating);
                    $starHtml = ob_get_clean();
                    return '<div class="rating rating-sm mt-1">'
                        . '<a href="' . route('product.reviews.details', $row->id) . '" class="review-link" style="text-decoration: none; color: inherit;">' // Anchor tag
                        . $starHtml
                        . '</a>'
                        . '<p style="margin: 0; margin-top: 5px;"><strong>' . _trans('keyword.Avg Rating') . ':</strong> ' . $averageRating . '</p>'
                        . '</div>';
                })
                ->addColumn('status', function ($row) {
                    $statusLabel = $row->is_published == 1 ? 'Published' : 'Unpublished';
                    $statusBadgeClass = $row->is_published == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)
                    if (hasPermission('product_status_change')) {
                        $isChecked = $row->is_published == 1 ? 'checked' : '';
                        $statusHtml .= '
                        <label class="switch switch-success" style="margin-bottom: 5px;">
                            <input type="checkbox" class="switch-input changeStatus" data-id="' . $row->id . '" ' . $isChecked . ' />
                            <span class="switch-toggle-slider">
                                <span class="switch-on">
                                    <i class="ti ti-check"></i>
                                </span>
                                <span class="switch-off">
                                    <i class="ti ti-x"></i>
                                </span>
                            </span>
                        </label>
                    ';
                    }

                    // Status badge, displayed below the toggle switch if it’s shown
                    $statusHtml .= '<div><span class="badge ' . $statusBadgeClass . '">' . $statusLabel . '</span></div>';

                    $statusHtml .= '</div>'; // Closing the main container

                    return $statusHtml;
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('status') == '0' || $request->get('status') == '1') {
                        $instance->where('is_published', $request->get('status'));
                    }

                    if ($request->get('category') != '') {
                        $instance->where('category_id', $request->get('category'));
                    }
                    if ($request->get('brand') != '') {
                        $instance->where('brand_id', $request->get('brand'));
                    }

                    if ($request->get('role') != '') {
                        $instance->whereHas('user.role', function ($query) use ($request) {
                            $query->where('role_id', $request->get('role'));
                        });
                    }

                    if ($request->get('user') != '') {
                        $instance->where('user_id', $request->get('user'));
                    }
                }, true)
                ->editColumn('unit_price', function ($row) {
                    return getPriceFormat($row->unit_price);
                })
                ->addColumn('action', function ($row) {
                    $btn = '';
                    if (hasPermission('product_update') || hasPermission('product_delete')) {
                        $btn = '<div class="d-inline-block text-nowrap">' .
                            '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                            '<div class="dropdown-menu dropdown-menu-end m-0">';
                    }

                    if (hasPermission('assign_visitors')) {
                        $btn .= '<a href="' . route('product.assignVisitor', $row->id) . '" class="dropdown-item assign_visitors text-primary" data-id="' . $row->id . '"><i class="ti ti-users-plus"></i> ' . _trans('keyword.Assign Visitors') . '</a>';
                    }

                    if (hasPermission('product_update')) {
                        $btn .= '<a href="' . route('product.edit', $row->id) . '" class="dropdown-item product_edit_button"><i class="ti ti-edit"></i> ' . _trans('keyword.Edit') . '</a>';
                    }

                    if (hasPermission('product_delete')) {
                        if ($row->user_id == getUserId()) {
                            $btn .= '<a href="javascript:void(0);" class="dropdown-item product_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> ' . _trans('keyword.Delete') . '</a>';
                        }
                    }


                    if (hasPermission('product_update') || hasPermission('product_delete')) {
                        $btn .= '</div>' .
                            '</div>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'thumbnail_img', 'name', 'status', 'added_by', 'review', 'brand_name', 'category_name'])
                ->make(true);
        }

        $categories = Category::where('active_status', 1)->get();
        $brands = Brand::where('active_status', 1)->get();
        $sellers = User::whereIn('role_id', [Role::DESIGNER, Role::MANUFACTURER])->get();

        return view('product.index', compact('categories', 'sellers', 'brands'));
    }

    public function create()
    {
        $setting = shopSetting();
        $categories = Category::where('active_status', 1)->get();
        $brands = Brand::where('active_status', 1)->get();
        $units = Unit::where('is_active', 1)->get();
        $attributes = Attribute::where('status', 1)->get();
        return view('product.create', compact('categories', 'brands', 'units', 'attributes', 'setting'));
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

    public function store(StoreProductRequest $request)
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
            if ($request->hasFile('thumbnail')) {
                $path = $this->uploadFile($request->file('thumbnail'), 'products/' . $product->id);
                $product->thumbnail_img = $path;
            }

            $product->save();


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
            DB::rollBack();
            return response()->json(['message' => "Something went wrong"], 500);
        }
    }

    public function edit($id)
    {

        $product = Product::with(['variants', 'choiceOptions.values', 'images'])->find($id);
        hasPermissionForOperation($product, 'user_id');
        $categories = Category::where('active_status', 1)->get();
        $brands = Brand::where('active_status', 1)->get();
        $units = Unit::where('is_active', 1)->get();
        $attributes = Attribute::where('status', 1)->get();
        return view('product.edit', compact('categories', 'brands', 'units', 'attributes', 'product'));
    }

    public function update(UpdateProductRequest $request)
    {
        try {

            $product = Product::find($request->product_id);
            hasPermissionForOperation($product, 'user_id');

            $weightAndDimensions = $this->mapWeightAndDimensions($request['weightAndDiamensions'] ?? []);
            $specifications = $this->mapSpecifications($request['specifications'] ?? []);

            DB::beginTransaction();
            $product = Product::where('id', $request->product_id)->first();
            $product->name = $request->title;
            $product->slug = createSlug($request->title);
            $product->category_id = $request->category;
            $product->brand_id = $request->brand_id;
            $product->unit = $request->unit;
            $product->video_link = $request->video_link;
            $product->attributes = $request['attributes'] ?? [];
            $product->choice_options = $request['attribute_values'] ?? [];
            $product->barcode = $request->barcode;
            $product->description = $request->description;
            $product->unit_price = abs($request->unit_price);
            $product->weight_dimensions = $weightAndDimensions;
            $product->specifications = $specifications;
            $product->shipping_policy = $request->shipping_policy;
            $product->return_policy = $request->return_policy;
            $product->disclaimer = $request->disclaimer;
            $product->discount_type = $request->discount_type;
            $product->discount = $request->discount_value;
            $product->is_price_hidden = isset($request->is_price_hidden) ? true : false;
            if ($request->has('status')) {
                $product->is_published = $request->status;
            }
            if ($request->hasFile('thumbnail')) {
                $path = $this->uploadFile($request->file('thumbnail'), 'products/' . $product->id);
                $this->deleteFile($product->thumbnail_img);
                $product->thumbnail_img = $path;
            }

            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $image) {
                    $path = $this->uploadFile($image, 'products/' . $product->id);
                    $productImage = new ProductImage();
                    $productImage->path = $path;
                    $productImage->product_id = $product->id;
                    $productImage->save();
                }
            }
            $product->save();


            $existing_product_variant = ProductVariant::where('product_id', $request->product_id)->pluck('id')->toArray();
            $new_product_variant = [];
            if ($request->variant) {

                foreach ($request['variant'] as $key => $combination) {
                    $product_variant = new ProductVariant();
                    if (array_key_exists('combination_id', $combination) && $combination['combination_id'] != null) {
                        $product_variant = ProductVariant::find($combination['combination_id']);
                        $new_product_variant[] = $combination['combination_id'];
                    }

                    $product_variant->variant = $combination['name'];
                    $product_variant->price = abs($combination['price'] ?? 0);
                    $product_variant->qty = abs($combination['quantity']);
                    $product_variant->discount_type = $request->discount_type;
                    $product_variant->discount_amount = $combination['discount'];
                    $product_variant->product_id = $product->id;
                    if (!array_key_exists("active", $combination)) {
                        $product_variant->is_active = 0;
                    } else {
                        $product_variant->is_active = 1;
                    }
                    $product_variant->save();
                    if ($request['variant'][$key]['image'] != 'undefined') {
                        $path = $this->uploadFile($combination['image'], 'products/' . $product->id);
                        if ($product_variant->image) {
                            $this->deleteFile($product_variant->image);
                        }
                        $product_variant->image = $path;
                    }

                    $product_variant->save();
                }
            }

            $deleted_product_variant = array_diff($existing_product_variant, $new_product_variant);
            $variantToDelete = ProductVariant::findMany($deleted_product_variant);
            foreach ($variantToDelete as $variant) {
                if ($variant->image) {
                    $this->deleteFile($product_variant->image);
                }
                $variant->delete();
            }

            DB::commit();
            removeDataFromRedisAPI(['product_list']);
            removeDataFromRedisAPI(['special_section_list']);
            removeDataFromRedisAPI(['slider_list']);
            Toastr::success('Product Updated Successfully');

            return response()->json(['message' => 'Product Updated Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json(['message' => "Something went wrong"], 500);
        }
    }

    public function destroy(IdValidationRequest $request)
    {
        try {
            $product = Product::find($request->product_id);

            hasPermissionForOperation($product, 'user_id');

            if (isProductInUse($product)) {
                return response()->json(['text' => 'Cannot Delete this product, The product is in used', 'icon' => 'error', 'title' => 'error!']);
            }

            if ($product->thumbnail_img != null && $product->thumbnail_img) {
                $this->deleteFile($product->thumbnail_img);
            }
            if ($product->meta_image != null && $product->meta_image) {
                $this->deleteFile($product->meta_image);
            }

            $product_variants = ProductVariant::where('product_id', $request->product_id)->get();

            foreach ($product_variants as $product_variant) {
                $this->deleteFile($product_variant->image);
                $product_variant->delete();
            }

            $product->delete();
            removeDataFromRedisAPI(['product_list']);
            removeDataFromRedisAPI(['special_section_list']);
            removeDataFromRedisAPI(['slider_list']);
            return response()->json(['text' => 'Product Deleted Successfully.', 'icon' => 'success', 'title' => 'Deleted!']);
        } catch (Exception $e) {
            return response()->json(['text' => "Something went wrong", 'icon' => 'error', 'title' => 'error!']);
        }
    }

    public function imageDestroy(IdValidationRequest $request)
    {
        try {
            $productImage = ProductImage::find($request->product_image_id);

            if ($productImage->path != null && $productImage->path) {
                $this->deleteFile($productImage->path);
            }

            $productImage->delete();
            removeDataFromRedisAPI(['product_list']);
            removeDataFromRedisAPI(['special_section_list']);
            removeDataFromRedisAPI(['slider_list']);
            return response()->json(['text' => 'Product Image deleted Successfully.', 'icon' => 'success']);
        } catch (Exception $e) {
            return response()->json(['message' => "Something went wrong"]);
        }
    }

    public function attributeValueList(Request $request)
    {
        $validated = $request->validate([
            'attibute_ids' => 'array',
            'attibute_ids.*' => 'exists:attributes,id',
        ]);

        $attributes = Attribute::where('status', 1)
            ->with('values')
            ->findMany($request->attibute_ids);

        return response()->json(['message' => 'Attribute List With Value', 'data' => $attributes, 'status' => 200], 200);
    }

    public function changeStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:products,id',
        ]);

        try {

            $product = Product::find($request->id);
            $product->is_published = !$product->is_published;
            $product->save();

            removeDataFromRedisAPI(['product_list']);
            removeDataFromRedisAPI(['special_section_list']);
            removeDataFromRedisAPI(['slider_list']);
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => "Something went Wrong!"], 500);
        }
    }

    public function user($id)
    {
        $users = User::where('role_id', $id)->get();
        return response()->json(['users' => $users]);
    }

    public function assignVisitor($id)
    {
        $product = Product::whereId($id)->first();
        $types = Role::whereIn('id', [Role::CUSTOMER])->get();
        $visitors = [];
        if ($product->visitors) {
            $visitors_id = json_decode($product->visitors);
            $visitors = User::whereIn('id', $visitors_id)->get();
        }
        return view('product.assign-visitor.index', compact('product', 'types', 'visitors'));
    }

    public function getUsersByType(Request $request)
    {
        try {
            $type = $request->input('type');
            $users = [];

            $role = Role::find($type);
            if ($role) {
                $users = $role->users()->whereDesignerId(getUserId())->select('name', 'email', 'id')->get()->toArray();
            }

            return response()->json($users);
        } catch (\Exception $e) {
            return response()->json(["message" => "Something Went Wrong!", 'status' => 500]);
        }
    }

    public function storeVisitor(Request $request, $id)
    {
        try {
            $product = Product::whereId($id)->first();
            $selectedUserIds = $request->input('users', []);
            $product->visitors = json_encode($selectedUserIds);
            $product->save();
            removeDataFromRedisAPI(['product_list']);
            return response()->json(['message' => 'Visitors assign Successfully', 'status' => 200]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong!' . $e->getMessage(), 'status' => 500]);
        }
    }


    // duplicate
    public function duplicate($productId, $sharedId)
    {

        DB::beginTransaction();

        try {
            $product = Product::with(['images', 'variants'])->where('id', $productId)->first();
            if (!$product) {
                return response()->json(['text' => 'Product not found', 'icon' => 'error', 'title' => 'error!']);
            }
            $newProduct = $product->replicate();
            $newProduct->name = $product->name . ' (Copy)' . '-' . time();
            $newProduct->slug = createSlug($newProduct->name) . '-' . time();
            $newProduct->num_of_sale = 0;
            $newProduct->is_published = 0;
            $newProduct->user_id = getUserId();
            $newProduct->parent_id = $productId;
            $newProduct->save();

            if ($product->thumbnail_img) {
                $newThumb = $this->copyExistingFile(
                    $product->thumbnail_img,
                    'products/' . $newProduct->id
                );

                if ($newThumb) {
                    $newProduct->thumbnail_img = $newThumb;
                    $newProduct->save();
                }
            }

            if ($product->images && $product->images->count() > 0) {
                foreach ($product->images as $image) {

                    $newImagePath = $this->copyExistingFile(
                        $image->path,
                        'products/' . $newProduct->id
                    );

                    if ($newImagePath) {
                        ProductImage::create([
                            'product_id' => $newProduct->id,
                            'path' => $newImagePath,
                        ]);
                    }
                }
            }

            if ($product->variants && $product->variants->count() > 0) {
                foreach ($product->variants as $variant) {

                    $newVariant = $variant->replicate();
                    $newVariant->product_id = $newProduct->id;
                    $newVariant->save();

                    if ($variant->image) {
                        $newVariantPath = $this->copyExistingFile(
                            $variant->image,
                            'products/' . $newProduct->id
                        );

                        if ($newVariantPath) {
                            $newVariant->image = $newVariantPath;
                            $newVariant->save();
                        }
                    }
                }
            }

            $shareedProduct = DesignerSharedProduct::where('id', $sharedId)->first();
            $shareedProduct->is_cloned = true;
            $shareedProduct->save();

            DB::commit();

            return response()->json(['text' => 'Product duplicated Successfully.', 'icon' => 'success', 'title' => 'Duplicated Product!', 'status' => 200], 200);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['text' => "Something went wrong", 'icon' => 'error', 'title' => 'error!']);
        }
    }
}
