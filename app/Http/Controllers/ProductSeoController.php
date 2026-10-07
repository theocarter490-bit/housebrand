<?php

namespace App\Http\Controllers;

use App\Http\Requests\MetaDetailsUpdateRequest;
use App\Http\Traits\FileUploadTrait;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Traits\CommonQueryTraits;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Http\Request;

class ProductSeoController extends Controller
{
    use FileUploadTrait;
    use CommonQueryTraits;

    public function index(Request $request)
    {
        $products = Product::with('category', 'brand', 'shop')->isClient()->latest();

        if ($request->filled('search')) {
            $products->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('status_type')) {
            $products->where('is_published', (int)$request->status_type);
        }

        if ($request->filled('category_type')) {
            $products->where('category_id', $request->category_type);
        }

        if ($request->filled('brand_type')) {
            $products->where('brand_id', $request->brand_type);
        }

        $products = $products->paginate(perPage());

        $categories = Category::where('active_status', 1)->get();
        $brands = Brand::where('active_status', 1)->get();
        return view('seo_content.product.index', compact('products', 'categories', 'brands'));
    }


    public function edit($id)
    {
        $product = Product::find($id);

        $product = [
            'id' => $product->id,
            'meta_title' => $product->meta_title,
            'meta_description' => $product->meta_description,
            'meta_img' => getFilePath($product->meta_img),
        ];
        return response()->json($product);
    }

    public function update(MetaDetailsUpdateRequest $request)
    {
        try {
            $data = Product::where('id', $request->product_id)
                ->isClient()->first();
            if ($request->hasFile('meta_image')) {
                $this->deleteFile($data->meta_img);
                $data->meta_img = $this->uploadFile($request->file('meta_image'), 'product-meta');
            }
            $data->meta_title = $request->meta_title;
            $data->meta_description = $request->meta_description;
            $data->save();
            return response()->json(['message' => 'Meta Details Updated Successfully', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong! Please try again.', 'status' => 500]);
        }
    }
}
