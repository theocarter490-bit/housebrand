<?php

namespace App\Http\Controllers;

use App\Http\Traits\FileUploadTrait;
use App\Models\Attribute;
use App\Models\Brand;
use App\Models\Category;
use App\Models\DesignerSharedProduct;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Role;
use App\Models\Unit;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Yajra\DataTables\Facades\DataTables;
use App\Http\Controllers\ProductController;

class DesignerSharedProductController extends Controller
{

    use FileUploadTrait;

    public function index(Request $request)
    {
        if ($request->ajax()) {

            $data = DesignerSharedProduct::with(['user.shop', 'product', 'seller.shop']);

            $data = match (Auth::user()->role_id) {
                Role::DESIGNER => $data->where('designer_id', Auth::user()->id),
                Role::MANUFACTURER => $data->where('seller_id', Auth::user()->id),
                default => $data,
            };

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('product_name', function ($row) {
                    return '<a href="' . env('APP_FRONTEND_URL') . '/product/' . $row->product->id . '-' . $row->product->slug . '" target="_blank">' . $row->product->name . '</a>';
                })
                ->addColumn('product_image', function ($row) {
                    return '<img src="' . getFilePath($row->product->thumbnail_img) . '" class="img-fluid" style="max-width: 50px;" />';
                })
                ->addColumn('product_owner_info', function ($row) {

                    $info = "<span>Name: " . optional($row->seller->shop)->shop_name . "</span><br>
                    <span>Phone: " . optional($row->seller->shop)->phone . " </span><br>
                    <span>Email: " . optional($row->seller->shop)->email . " </span>";
                    return $info;
                })
                ->addColumn('designer_info', function ($row) {
                    $info = "<span>Seller Name: " . optional($row->user->shop)->shop_name . "</span>
                    <br><span>Shop Name: <a class='badge bg-label-info' href='" . env('APP_FRONTEND_URL') . '/designer/' . optional($row->user->shop)->slug . "' target='_blank'>" . optional($row->user->shop)->shop_name . " </a> </span><br>
                    <span>Phone: " . optional($row->user->shop)->phone . " </span><br>
                    <span>Email: " . optional($row->user->shop)->email . " </span>";
                    return $info;
                })
                ->filter(function ($instance) use ($request) {
                    if ($request->get('publish_status') == '0' || $request->get('publish_status') == '1') {
                        $instance->where('is_published', $request->get('publish_status'));
                    }

                    if ($request->get('status') == '0' || $request->get('status') == '1' || $request->get('status') == '2') {
                        $instance->where('status', $request->get('status'));
                    }
                }, true)
                ->addColumn('status', function ($row) {
                    $btn = '<div class="d-flex align-items-center gap-2">';
                    if ($row->getRawOriginal('status') == DesignerSharedProduct::PENDING) {
                        $btn .= '<span href="#" class="badge bg-label-primary p-2 c-h-32 d-flex align-items-center justify-content-center" title="Wating for Manufacturer Approval"  >Pending</span>';
                    }
                    if ($row->getRawOriginal('status') == DesignerSharedProduct::APPROVED) {

                        $btn .= '<span class="badge bg-label-success p-2 c-h-32 d-flex align-items-center justify-content-center" title="Approved by Manufacturer" >Approved</span>';
                    }
                    if ($row->getRawOriginal('status') == DesignerSharedProduct::CANCELED) {
                        $btn .= '<span class="badge bg-label-danger p-2" title="Cancelled by Manufacturer" >Cancelled</span>';
                    }
                    return $btn;
                })
                ->addColumn('action', function ($row) {
                    $btn = '<div class="d-flex align-items-center gap-2">';
                    if ($row->getRawOriginal('status') == DesignerSharedProduct::PENDING) {
                        if (Auth::user()->role_id == Role::MANUFACTURER) {
                            if (hasPermission('white_list_product_approve')) {
                                $btn .= '<a href="#" class="btn btn-success text-white product_request_approve_button c-h-32 d-flex align-items-center justify-content-center" data-id="' . $row->id . '">Approve</a>';
                            }

                            if (hasPermission('white_list_product_cancel')) {
                                $btn .= '<a href="#" class="btn btn-danger text-white ms-2 product_request_cancel_button c-h-32 d-flex align-items-center justify-content-center" data-id="' . $row->id . '">Cancel</a>';
                            }
                        }
                    }
                    if (isSeller() && hasPermission('white_list_product_delete')) {
                        if (Auth::user()->role_id == Role::DESIGNER) {
                            if ($row->getRawOriginal('status') == DesignerSharedProduct::APPROVED) {
                                if ($row->is_cloned == false) {
                                    $btn .= '<a href="#" class="clone_product badge bg-label-warning ms-2 c-h-32 d-flex align-items-center justify-content-center" data-id="' . $row->product_id . '" data-shared-id="' . $row->id . '" title="Clone the product and publish it on your site." ><i class="ti ti-copy"></i>Clone</a>';
                                } else {
                                    // $btn .= '<a href="#" class="badge bg-label-primary ms-2 c-h-32 d-flex align-items-center justify-content-center" ><i class="ti ti-copy-off" title="Product already cloned on your site."></i>Cloned</a>';
                                    $btn .= '<span class="badge bg-label-warning p-2" title="Product already cloned on your site." >Cloned</span>';
                                }
                            }
                        }
                        $btn .= '<a href="#" class="badge bg-label-danger ms-2 delete_shared_product c-h-32 d-flex align-items-center justify-content-center" data-id="' . $row->id . '" title="Remove white label request"><i class="ti ti-trash"></i></a>';
                    }
                    return $btn;
                })
                ->rawColumns(['action', 'product_name', 'product_name', 'product_owner_info', 'designer_info', 'publish', 'status', 'product_image'])
                ->make(true);
        }
        return view('shared-product.index');
    }


    public function approve(Request $request)
    {
        $request->validate([
            'product_request_id' => 'required|exists:designer_shared_products,id',
        ]);
        try {
            $productRequest = DesignerSharedProduct::find($request->product_request_id);

            $productRequest->status = DesignerSharedProduct::APPROVED;

            $productRequest->save();
            return response()->json(['text' => 'Request Approved.', 'icon' => 'success']);
        } catch (Exception $e) {
            return response()->json(['text' => "Something went wrong"]);
        }
    }

    public function cancel(Request $request)
    {
        $request->validate([
            'product_request_id' => 'required|exists:designer_shared_products,id',
        ]);
        try {
            $productRequest = DesignerSharedProduct::find($request->product_request_id);

            $productRequest->status = DesignerSharedProduct::CANCELED;

            $productRequest->save();
            return response()->json(['text' => 'Request Canceled.', 'icon' => 'success']);
        } catch (Exception $e) {
            return response()->json(['text' => "Something went wrong"]);
        }
    }

    public function changeStatus(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:designer_shared_products,id',
        ]);

        try {

            $product = DesignerSharedProduct::find($request->id);
            $product->is_published = !$product->is_published;
            $product->save();

            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => "Something went Wrong!"], 500);
        }
    }

    public function destroy(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:designer_shared_products,id',
        ]);
        try {
            $product = DesignerSharedProduct::find($request->id);
            if (!in_array(getUserId(), [$product->seller_id, $product->designer_id]) && getUserId() != 1) {
                return response()->json(['text' => 'Not Authorized.'], 403);
            }
            $product->delete();
            return response()->json(['text' => 'Item has been deleted.', 'icon' => 'error', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => "Something went wrong"]);
        }
    }


}
