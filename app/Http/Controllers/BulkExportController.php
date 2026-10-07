<?php

namespace App\Http\Controllers;

use App\Exports\ProductBulkExport;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\Role;
use App\Models\User;

use Brian2694\Toastr\Facades\Toastr;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Http\Request;

class BulkExportController extends Controller
{

    public function index()
    {
        try {

            $categories = Category::where('active_status', 1)->get();
            $brands = Brand::where('active_status', 1)->get();
            $designers = User::where('role_id', Role::DESIGNER)->get();
            $manufacturers = User::where('role_id', Role::MANUFACTURER)->get();
            return view('bulk-import.product-export', compact('categories', 'brands', 'designers', 'manufacturers'));
        } catch (\Exception $exception) {
            Toastr::error($exception->getMessage());
        }
    }

    public function export(Request $request)
    {
        try {
            $products = Product::isClient();

            if ($request->filled('isPublishedList')) {
                $products->where('is_published', $request->isPublishedList);
            }

            if ($request->filled('categoryList')) {
                $products->where('category_id', $request->categoryList);
            }

            if ($request->filled('brandList')) {
                $products->where('brand_id', $request->brandList);
            }

            if ($request->filled('manufacturerList')) {
                $products->where('user_id', $request->manufacturerList);
            }

            if ($request->filled('designerList')) {
                $products->where('user_id', $request->designerList);
            }
            if ($request->filled('TagifyProductList')) {
                $productList = [];
                foreach (json_decode($request->TagifyProductList) as $tag) {
                    array_push($productList, (int)$tag->value);
                }
                $products->whereIn('id', $productList);
            }

            $products = $products->get();

            return Excel::download(new ProductBulkExport($products), 'products.xlsx');
        } catch (\Exception $exception) {
            Toastr::error($exception->getMessage());
        }
    }

    public function getProductList(Request $request)
    {
        $products = Product::isClient();
        if ($request->filled('is_published')) {
            $products = $products->where('is_published', $request->is_published);
        }
        if ($request->filled('category')) {
            $products = $products->where('category_id', $request->category);
        }
        if ($request->filled('brand')) {
            $products = $products->where('brand_id', $request->brand);
        }
        if ($request->filled('manufacturer')||$request->filled('designer')) {
            $products = $products->where('user_id', $request->{$request->typeSelect});
        }


        $products = $products->get();

        $products = $products->map(function ($product) {
            return [
                'id' => $product->id,
                'name' => $product->name,
                'thumbnail_img' => getFilePath($product->thumbnail_img),
            ];
        });

        return response()->json([$products]);

    }
}
