<?php

namespace App\Http\Controllers;

use App\Facades\SendMail;
use App\Http\Requests\CustomerRequest;
use App\Mail\DesignerContactReply;
use App\Mail\SendUserCredentialMail;
use App\Models\DesignerContact;
use App\Models\EmailSentLog;
use App\Models\Page;
use App\Models\Plan;
use App\Models\SubscriptionCancelRequest;
use App\Models\SubscriptionItem;
use Exception;
use App\Models\Cart;
use App\Models\Role;
use App\Models\User;
use App\Models\Order;
use App\Models\Product;
use App\Models\Wishlist;
use App\Models\Permission;
use App\Models\ShopSetting;
use Illuminate\Http\Request;
use App\Http\Traits\FileUploadTrait;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Cache;
use App\Mail\DefaultPasswordResetMail;
use App\Http\Requests\UserStoreRequest;
use Illuminate\Support\Str;
use Laravel\Sanctum\PersonalAccessToken;
use Yajra\DataTables\Facades\DataTables;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\ShopInfoUpdateRequest;
use App\Http\Requests\ShopLinkUpdateRequest;
use App\Http\Requests\ShopLogoUpdateRequest;
use Illuminate\Validation\ValidationException;
use App\Http\Requests\UserProfileUpdateRequest;
use App\Models\Task;

class UserController extends Controller
{
    use FileUploadTrait;

    public function index(Request $request, $roleId)
    {
        if (!in_array($roleId, [Role::DESIGNER, Role::CUSTOMER, Role::MANUFACTURER])) {
            abort(403);
        }
        try {
            $roleName = Role::find($roleId)->name;
        } catch (Exception $e) {
            Toastr::error('Invalid Role!');
            return redirect()->route('user.index', Role::CUSTOMER);
        }

        $designers = [];

        if ($request->ajax()) {
            $data = User::with(['shop', 'designer.shop'])->isClient('designer_id')->where('role_id', $roleId)->latest();
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('avatar', function ($row) {
                    return "<img src='" . getFilePath($row->avatar) . "' alt='' width='50px' height='50px' />";
                })
                ->editColumn('name', function ($row) {
                    $info = "<span>Name: " . $row->name . "</span>
                    <br>
                    <span>Phone: " . $row->phone . " </span><br>
                    <span>Email: " . $row->email . " </span><br>";
                    $source = $row->user_source;
                    $info .= "<span>Signup Date: " . dateFormatwithTime($row->created_at) . " </span><br>Signup Source:<span class='badge bg-label-warning'>" . $source . "</span>";

                    return $info;
                })
                ->addColumn('shop_info', function ($row) {
                    if ($row->role_id == Role::DESIGNER) {

                        // Using optional to safely access properties
                        $info = "<span>Shop Name: <span class='badge bg-info'>" . optional($row->shop)->shop_name . "</span></span><br>
                                 <span>Location: " . optional($row->shop)->location . "</span><br>
                                 <span>Phone: " . optional($row->shop)->phone . "</span><br>
                                 <span>Email: " . optional($row->shop)->email . "</span>";
                        return $info;
                    }
                    return '';
                })
                ->editColumn('role_id', function ($row) {
                    if ($row->role_id == Role::SUPER_ADMIN) {
                        return '<span class="badge bg-danger">Super Admin</span>';
                    } else if ($row->role_id == Role::ADMIN) {
                        return '<span class="badge bg-warning">Admin</span>';
                    } else if ($row->role_id == Role::DESIGNER) {
                        return '<span class="badge bg-success">Designer</span>';
                    } else if ($row->role_id == Role::CUSTOMER) {
                        return '<span class="badge bg-primary">Customer</span>';
                    } else if ($row->role_id == Role::MANUFACTURER) {
                        return '<span class="badge bg-info">Manufacturer</span>';
                    }
                })
                ->editColumn('designer', function ($row) {
                    if ($row->role_id == Role::CUSTOMER) {
                        $info = "<span>Name: " . optional($row->designer)->name . "</span>
                    <br><span>Shop Name: <a class='badge bg-label-info' href='" . env('APP_FRONTEND_URL') . '/designer/' . optional($row->designer->shop)->slug . "' target='_blank'>" . optional($row->designer->shop)->shop_name . " </a> </span><br>
                    <span>Phone: " . optional($row->designer->shop)->phone . " </span><br>
                    <span>Email: " . optional($row->designer->shop)->email . " </span>";
                        return $info;
                    }
                    return '';
                })
                ->filter(function ($instance, $row) use ($roleId, $request) {
                    if ($request->get('status') == '0' || $request->get('status') == '1') {
                        $instance->where('active_status', $request->get('status'));
                    }
                    if ($request->get('designer_id') != '') {
                        $instance->where('designer_id', $request->get('designer_id'));
                    }
                    if ($request->search['value'] != '') {

                        $search = $request->search['value'];

                        $instance->where(function ($q) use ($search) {

                            // Search in main user (customer / designer itself)
                            $q->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");

                            // Search in designer (when listing customers)
                            $q->orWhereHas('designer', function ($d) use ($search) {
                                $d->where('name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            });
                            $q->orWhereHas('designer.shop', function ($s) use ($search) {
                                $s->where('shop_name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            });
                            $q->orWhereHas('shop', function ($s) use ($search) {
                                $s->where('shop_name', 'like', "%{$search}%")
                                    ->orWhere('email', 'like', "%{$search}%");
                            });

                        });
                    }

                    if ($request->get('subscription_status') != '') {
                        if ($request->get('subscription_status') == 1) {
                            $instance->where('is_subscribed', 1);
                        } else if ($request->get('subscription_status') == 0) {
                            $instance->where('is_subscribed', 0);
                        } else if ($request->get('subscription_status') == 4) {
                            $instance->where('trail_mode', 1);
                        } else {
                            $instance->where('subscription_required', 0);
                        }

                    }
                    if ($request->get('user_source') != '') {
                        $instance->where('user_source', $request->get('user_source'));
                    }

                    if ($request->get('dateRange') != '') {
                        $dateString = $request->dateRange;
                        $dates = explode(" to ", $dateString);
                        if (isset($dates[0])) {
                            $instance->where('created_at', '>=', $dates[0]);
                        }
                        if (isset($dates[1])) {
                            $instance->where('created_at', '<=', $dates[1]);
                        }
                    }


                }, true)
                ->addColumn('status', function ($row) {
                    $statusLabel = $row->active_status == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->active_status == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)
                    if (hasPermission('user_status_change')) {
                        $isChecked = $row->active_status == 1 ? 'checked' : '';
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
                ->addColumn('is_subscribed', function ($row) {
                    $statusHtml = '<div class="custom-status-container">';

                    if ($row->subscription_required == 1) {
                        $statusLabel = $row->is_subscribed == 1 ? 'Subscribed' : 'Not Subscribed';
                        $statusBadgeClass = $row->is_subscribed == 1 ? 'custom-bg-success' : 'custom-bg-danger';
                    } else {
                        $statusLabel = 'Subscription Not Required';
                        $statusBadgeClass = 'bg-label-warning';
                    }

                    $statusHtml .= '<div>';
                    $statusHtml .= '<span class="badge ' . $statusBadgeClass . '">' . $statusLabel . '</span>';
                    if ($row->trail_mode == 1) {
                        $statusHtml .= '<span class="ms-1 badge bg-label-warning">free trial</span>';
                    }
                    $statusHtml .= '</div>';

                    return $statusHtml;
                })
                ->addColumn('action', function ($row) use ($roleId) {

                    $btn = '<div class="d-inline-block text-nowrap">' .
                        '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                        '<div class="dropdown-menu dropdown-menu-end m-0">';

                    if ($roleId == Role::CUSTOMER && hasPermission('customers_profile')) {
                        $btn .= '<a href="' . route('user.profile', $row->id) . '" class="dropdown-item"><i class="tf-icons ti ti-id" ></i> Profile</a>';
                        $btn .= '<a href="javascript:0;" class="dropdown-item reply_button" data-bs-toggle="modal" data-bs-target="#replyModal" data-id="' . $row->id . '"><i class="ti ti-send"></i> Send Credentials</a>';
                    } else if ($roleId == Role::MANUFACTURER && hasPermission('manufacturer_profile')) {
                        $btn .= '<a href="' . route('user.profile', $row->id) . '" class="dropdown-item"><i class="tf-icons ti ti-id" ></i> Profile</a>';
                    } else if ($roleId == Role::DESIGNER && hasPermission('designer_profile')) {
                        $btn .= '<a href="' . route('user.profile', $row->id) . '" class="dropdown-item"><i class="tf-icons ti ti-id" ></i> Profile</a>';
                    }

                    if ($roleId == Role::CUSTOMER && hasPermission('customers_delete')) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item user_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> Delete</a>' .
                            '</div>' .
                            '</div>';
                    } else if ($roleId == Role::MANUFACTURER && hasPermission('manufacturer_delete')) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item user_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> Delete</a>' .
                            '</div>' .
                            '</div>';
                    } else if ($roleId == Role::DESIGNER && hasPermission('designer_delete')) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item user_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> Delete</a>' .
                            '</div>' .
                            '</div>';
                    }


                    return $btn;
                })
                ->rawColumns(['action', 'avatar', 'status', 'role_id', 'designer', 'name', 'shop_info', 'is_subscribed'])
                ->make(true);
        }
        if ($roleId == Role::CUSTOMER) {
            $designers = User::select('id', 'name')->where('role_id', Role::DESIGNER)->where('active_status', 1)->get();
        }
        return view('user.index', compact('roleId', 'roleName', 'designers'));
    }


    public function employeeList(Request $request)
    {
        $roleName = 'Employee';
        $roles = Role::where('user_id', auth()->id())->where('active_status', 1)->whereNotIn('id', [Role::SUPER_ADMIN, Role::ADMIN, Role::DESIGNER, Role::MANUFACTURER, Role::CUSTOMER])->get();

        if ($request->ajax()) {
            $data = User::select('*')->where('supervisor_id', auth()->id());
            return DataTables::of($data)
                ->addIndexColumn()
                ->editColumn('avatar', function ($row) {
                    return "<img src='" . getFilePath($row->avatar) . "' alt='' width='50px' height='50px' />";
                })
                ->editColumn('role_id', function ($row) {
                    if ($row->role_id == Role::ADMIN) {
                        return '<span class="badge bg-warning">' . $row->role->name . '</span>';
                    } else {
                        return '<span class="badge bg-info">' . $row->role->name . '</span>';
                    }
                })
                ->addColumn('status', function ($row) {
                    $statusLabel = $row->active_status == 1 ? 'Active' : 'Inactive';
                    $statusBadgeClass = $row->active_status == 1 ? 'custom-bg-success' : 'custom-bg-danger';

                    // Start with the container for both switch and status label
                    $statusHtml = '<div class="custom-status-container">';

                    // Toggle switch (visible only if the user has permission)
                    if (hasPermission('user_status_change')) {
                        $isChecked = $row->active_status == 1 ? 'checked' : '';
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
                        $instance->where('active_status', $request->get('status'));
                    }
                }, true)
                ->addColumn('action', function ($row) {

                    $btn = '';
                    if (hasPermission('employees_profile') || hasPermission('employees_delete')) {
                        $btn = '<div class="d-inline-block text-nowrap">' .
                            '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                            '<div class="dropdown-menu dropdown-menu-end m-0">';
                    }

                    if (hasPermission('employees_profile')) {
                        $btn .= '<a href="' . route('user.assignPermission', $row->id) . '" class="dropdown-item"><i class="tf-icons ti ti-id" ></i> Assign Permission</a>';
                    }
                    if (hasPermission('employees_profile')) {
                        $btn .= '<a href="' . route('user.profile', $row->id) . '" class="dropdown-item"><i class="tf-icons ti ti-id" ></i>Profile</a>';
                        $btn .= '<a href="javascript:0;" class="dropdown-item reply_button" data-bs-toggle="modal" data-bs-target="#replyModal" data-id="' . $row->id . '"><i class="ti ti-send"></i> Send Credentials</a>';
                    }
                    if (hasPermission('employees_delete')) {
                        $btn .= '<a href="javascript:0;" class="dropdown-item user_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> Delete</a>' .
                            '</div>' .
                            '</div>';
                    }

                    return $btn;
                })
                ->rawColumns(['action', 'avatar', 'status', 'role_id'])
                ->make(true);
        }
        return view('user.employees', compact('roleName', 'roles'));
    }

    public function profile(User $user)
    {
        $user = $user->loadCount('products', 'orders', 'sellerOrders', 'shop', 'role')->load('lastSubscription.latestItem', 'freeTrailCode');
        $latestCancelRequest = SubscriptionCancelRequest::where('user_id', $user->id)
            ->orderBy('id', 'desc')
            ->first();
        $cancelRequests = SubscriptionCancelRequest::where('user_id', $user->id)->paginate(3)->appends(['tab' => 'subscription']);
        $subscriptionPaymentList = SubscriptionItem::whereHas('subscription', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with('plan', 'subscription')->orderBy('id', 'desc')->paginate(10)->appends(['tab' => 'subscription']);
        $freeTrailPlan = Plan::where('role_id', null)->latest()->first();
        return view('user.profile', compact('user', 'latestCancelRequest', 'cancelRequests', 'subscriptionPaymentList', 'freeTrailPlan'));
    }

    public function assignPermission($id)
    {
        $user = User::find($id);
        $permissions = [];
        if ($user && $user->supervisor && $user->supervisor->role && $user->supervisor->role->id == Role::SUPER_ADMIN) {
            $permissions = Permission::all();
        } elseif ($user && $user->supervisor && $user->supervisor->role) {
            $permissionsData = $user->supervisor->role->permissions;

            $permissions = Permission::all()->map(function ($permission) use ($permissionsData) {
                $jsonData = $permission->keywords;
                $filteredKeywords = array_filter($jsonData, function ($value) use ($permissionsData) {
                    return in_array($value, $permissionsData);
                });

                if (empty($filteredKeywords)) {
                    return null;
                }

                $permission->keywords = $filteredKeywords;
                return $permission;
            })->filter();
        }
        return view('user.assign_permission', compact('user', 'permissions'));
    }

    public function permissionUpdate(Request $request)
    {
        try {
            $permissionUpdate = User::findOrFail($request->user_id);
            $permissionUpdate->permissions = $request->permissions;
            $permissionUpdate->save();

            Toastr::success(__('Permission Update Successfully'), 'Success', ['timeOut' => 2000]);
            return redirect()->route('employee.employeeList');
        } catch (\Throwable $th) {
            Toastr::error(__('Something went wrong!'), 'Error', ['timeOut' => 2000]);
            return redirect()->back();
        }
    }

    public function orders(Request $request, $user_id)
    {
        $data = Order::where('user_id', $user_id)->withCount('items')->with('designer');
        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('order_date', function ($row) {
                return dateFormat($row->order_date);
            })
            ->addColumn('name', function ($row) {
                return $row->designer->name;
            })
            ->addColumn('action', function ($row) {
                $btn = '<a href="' . route('order.details', $row) . '" class="btn btn-primary text-white px-3 py-2 me-1" >Details</a>';
                return $btn;
            })
            ->rawColumns(['action'])
            ->make(true);
    }

    /**
     * @param Request $request
     * @param mixed $user_id
     *
     * @return [response]
     */
    public function carts(Request $request, $user_id)
    {
        $data = Cart::where('carts.user_id', $user_id)->with(['seller', 'product']);
        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('variation', function ($row) {
                $variation = '';
                foreach ($row->variation as $key => $item) {
                    $variation .= "<span><b class='me-1'>" . $item['attribute'] . ":</b><span class='text-primary'>" . $item['value'] . "</span></span><br>";
                }
                return $variation;
            })
            ->addColumn('thumbnail_img', function ($row) {
                return '<img src="' . getFilePath(@$row->product->thumbnail_img) . '" width="50px" height="50px"/>';
            })
            ->addColumn('product_name', function ($row) {
                return $row->product->name;
            })
            ->addColumn('seller', function ($row) {
                return $row->seller->name;
            })
            ->addColumn('action', function ($row) use ($user_id) {
                $url = env('APP_FRONTEND_URL') . '/product/' . $row->product_id;
                if ($row->product->user->role_id == Role::DESIGNER) {
                    $url = env('APP_FRONTEND_URL') . '/designer/' . $row->product->shop->slug . '/product/' . $row->product_id;
                }
                return '<a href="' . $url . '" class="btn btn-primary text-white">View Product</a>';
            })
            ->rawColumns(['action', 'thumbnail_img', 'variation'])
            ->make(true);
    }

    /**
     * @param Request $request
     * @param mixed $user_id
     *
     * @return [response]
     */
    public function wishlist(Request $request, $user_id)
    {
        $data = Wishlist::where('wishlists.user_id', $user_id)->with('product');
        return DataTables::of($data)
            ->addIndexColumn()
            ->addColumn('thumbnail_img', function ($row) {
                return '<img src="' . getFilePath(@$row->product->thumbnail_img) . '" width="50px" height="50px"/>';
            })
            ->addColumn('product_name', function ($row) {
                return $row->product->name;
            })
            ->addColumn('action', function ($row) {
                $url = env('APP_FRONTEND_URL') . '/product/' . $row->product_id;
                if ($row->product->user->role_id == Role::DESIGNER) {
                    $url = env('APP_FRONTEND_URL') . '/designer/' . $row->product->shop->slug . '/product/' . $row->product_id;
                }
                return '<a href="' . $url . '" class="btn btn-primary text-white">View Product</a>';
            })
            ->rawColumns(['action', 'thumbnail_img'])
            ->make(true);
    }

    public function products(Request $request, $user_id)
    {
        $data = Product::select('*')->where('user_id', $user_id);

        return DataTables::of($data)
            ->addIndexColumn()
            ->editColumn('thumbnail_img', function ($row) {
                return '<img src="' . getFilePath($row->thumbnail_img) . '" width="50px" height="50px"/>';
            })
            ->addColumn('status', function ($row) {
                if ($row->is_published == 1) {
                    return '<span class="badge bg-label-success">Published</span>';
                } else {
                    return '<span class="badge bg-label-danger">Unpublished</span>';
                }
            })
            ->editColumn('unit_price', function ($row) {
                return getPriceFormat($row->unit_price);
            })
            ->addColumn('action', function ($row) {

                $btn = '<div class="d-inline-block text-nowrap">' .
                    '<button class="btn btn-sm btn-icon dropdown-toggle hide-arrow" data-bs-toggle="dropdown"><i class="ti ti-dots-vertical me-2"></i></button>' .
                    '<div class="dropdown-menu dropdown-menu-end m-0">' .
                    '<a href="' . route('product.edit', $row->id) . '" class="dropdown-item product_edit_button"><i class="ti ti-edit" ></i> Edit</a>' .
                    '<a href="javascript:0;" class="dropdown-item product_delete_button text-danger" data-id="' . $row->id . '"><i class="ti ti-trash"></i> Delete</a>' .
                    '</div>' .
                    '</div>';

                return $btn;
            })
            ->rawColumns(['action', 'thumbnail_img', 'status'])
            ->make(true);
    }


    public function passwordReset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'current_password' => 'required',
            'password' => 'required|confirmed|min:8',
            'user_id' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 403], 200);
        }

        try {

            $user = User::find($request->user_id);
            if (!Hash::check($request->current_password, $user->password)) {
                $validator->errors()->add('current_password', 'Current password does not match!');
                return response()->json(['errors' => $validator->errors(), 'status' => 403], 200);
            }

            $user->password = Hash::make($request->password);
            $user->save();
            return response()->json(['message' => 'Password Changed Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }


    public function update(UserProfileUpdateRequest $request)
    {
        try {
            $user = User::find($request->user_id);

            $user->name = $request->name;
            $user->address = $request->address;
            $user->phone = $request->phone;
            if ($request->hasFile('avatar')) {

                $path = $this->uploadFile($request->file('avatar'), 'user');
                if ($user->avatar) {
                    $this->deleteFile($user->avatar);
                }
                $user->avatar = $path;
            }

            $user->save();

            return response()->json(['message' => 'User Information Updated Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }


    public function changeStatus(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'exists:users,id'
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors(), 'status' => 403], 200);
        }

        try {

            $user = User::find($request->user_id);

            $user->active_status = !$user->active_status;
//            $user->tokens()->delete();
            $user->save();
            return response()->json(['text' => 'User Status Updated Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function shopInfoUpdate(ShopInfoUpdateRequest $request)
    {
        try {
            $shop_setting = ShopSetting::where('user_id', $request->user_id)->first();

            $shop_setting->shop_name = $request->shop_name;

            $shop_setting->location = $request->address;
            $shop_setting->phone = $request->phone;
            $shop_setting->email = $request->email;
            $shop_setting->map_location = $request->map_location;
            $shop_setting->save();
            $this->forgetCache($shop_setting->user_id, $shop_setting->designer);

            // Toastr::success('General Setting Updated');
            return response()->json(['message' => 'Shop Setting Updated Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function shoplogoUpdate(ShopLogoUpdateRequest $request)
    {

        $general_setting = ShopSetting::where('user_id', $request->user_id)->first();

        try {
            if ($request->hasFile('light_logo')) {
                $path = $this->uploadFile($request->file('light_logo'), 'designer/' . $general_setting->user_id . '/icon');

                if ($general_setting->logo) {
                    $this->deleteFile($general_setting->logo);
                }
                $general_setting->logo = $path;
            }
            if ($request->hasFile('banner')) {

                $path = $this->uploadFile($request->file('banner'), 'designer/' . $general_setting->user_id . '/icon');
                if ($general_setting->banner) {
                    $this->deleteFile($general_setting->banner);
                }
                $general_setting->banner = $path;
            }
            if ($request->hasFile('favicon')) {

                $path = $this->uploadFile($request->file('favicon'), 'designer/' . $general_setting->user_id . '/icon');
                if ($general_setting->favicon) {
                    $this->deleteFile($general_setting->favicon);
                }
                $general_setting->favicon = $path;
            }

            $general_setting->save();
            $this->forgetCache($general_setting->user_id, $general_setting->designer);

            return response()->json(['message' => 'Shop Logo Updated Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function shoplinkUpdate(ShopLinkUpdateRequest $request)
    {
        try {
            $general_setting = ShopSetting::where('user_id', getUserId())->first();

            $general_setting->twitter_url = $request->twitter;
            $general_setting->facebook_url = $request->facebook;
            $general_setting->instagram_url = $request->instagram;
            $general_setting->linkedin = $request->linkedin;
            $general_setting->youtube_url = $request->youtube;
            $general_setting->tiktok_url = $request->tiktok;

            $general_setting->save();
            $this->forgetCache($general_setting->user_id, $general_setting->designer);

            return response()->json(['message' => 'Social Link Updated Successfully', 'status' => 200], 200);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500], 500);
        }
    }

    public function store(UserStoreRequest $request)
    {
        if (moduleConditionLimitCheck('employee-management', 'App\Models\User', 'supervisor_id') == false) {
            Toastr::error('You have reached the maximum quantity for this module.');
            return redirect()->back();
        }
        try {
            $user = new User();
            $user->name = $request->name;
            $user->role_id = $request->role;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->active_status = $request->status;
            $user->address = $request->address;

            if (in_array(Auth::user()->role_id, [Role::SUPER_ADMIN, Role::DESIGNER, Role::MANUFACTURER])) {
                $superVisor = Auth::user()->id;
            } else {
                $superVisor = Auth::user()->supervisor_id;
            }
            $user->supervisor_id = $superVisor;

            if ($request->hasFile('avatar')) {
                $path = $this->uploadFile($request->file('avatar'), 'user');
                $user->avatar = $path;
            }
            $user->permissions = '';
            $user->save();
            Toastr::success('User created Successfully');
        } catch (Exception $e) {
            Toastr::error('Something went wrong!');
        }
        return back();
    }

    public function customerStore(CustomerRequest $request)
    {
        try {
            \DB::beginTransaction();
            $user = new User();
            $user->name = $request->name;
            $user->role_id = Role::CUSTOMER;
            $user->email = $request->email;
            $user->email_verified_at = now();
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->active_status = $request->status;
            $user->address = $request->address;
            $user->designer_id = Auth::user()->id;
            $user->user_source = "HB";

            if ($request->hasFile('image')) {
                $path = $this->uploadFile($request->file('image'), 'user');
                $user->avatar = $path;
            }
            $user->permissions = '';
            $user->save();
            \DB::commit();
            Toastr::success('Customer created Successfully');
        } catch (Exception $e) {
            \DB::rollBack();
            Toastr::error('Something went wrong!');
        }
        return response()->json(['message' => 'Customer created Successfully', 'status' => 200], 200);
    }


    // public function destroy(Request $request)
    // {
    //     try {
    //         $data = User::find($request->user_id);
    //         if ($data->image) {
    //             $this->deleteFile($data->image);
    //         }
    //         $data->delete();
    //     } catch (Exception $e) {
    //         Toastr::error('Something Went Wrong!');
    //     }
    // }

    public function destroy(Request $request)
    {
        try {
            $user = User::findOrFail($request->user_id);

            $relations = [
                'orders' => 'orders',
                'products' => 'products',
                'reviews' => 'reviews',
                'subscription' => 'subscription',
                'projects' => 'projects',
                'wishlist' => 'wishlist',
                'managerProjects' => 'manager projects',
                'shippingAddress' => 'shipping address',
                'gatewayCredentials' => 'gateway credentials',
                'paymentMethodStatus' => 'payment methods',
                'sharedProduct' => 'shared products',
                'carts' => 'carts',
                'shop' => 'shop',
                'appointments' => 'appointments',
            ];

            foreach ($relations as $relation => $label) {
                if ($user->$relation()->exists()) {
                    return response()->json([
                        'icon' => 'error',
                        'message' => "Cannot delete user because already exist in {$label} ."
                    ], 400);
                }
            }

            if (Task::whereJsonContains('assigned_users', $user->id)->exists()) {
                return response()->json([
                    'icon' => 'error',
                    'message' => "Cannot delete user because already exist in tasks."
                ], 400);
            }

            // ✅ Delete image if exists
            if ($user->image) {
                $this->deleteFile($user->image);
            }

            $user->delete();

            return response()->json([
                'icon' => 'success',
                'message' => 'User deleted successfully.'
            ]);

        } catch (\Exception $e) {
            if ($e instanceof \Illuminate\Database\QueryException) {
                if ($e->getCode() == "23000") {
                    return response()->json([
                        'icon' => 'error',
                        'message' => 'Cannot delete this user because related records exist.'
                    ], 500);
                }
            }

            return response()->json([
                'icon' => 'error',
                'message' => 'Something went wrong!',
            ], 500);
        }
    }


    public function loginAs(Request $request)
    {
        try {
            $user = User::find($request->user_id);
            \Auth::login($user);
            Toastr::info('You logged in successfully as user:' . $user->name);
            return response()->json(['message' => 'You are logged in successfully as ' . $user->name]);
        } catch (Exception $e) {
            Toastr::error('Something Went Wrong!');
        }

    }


    // accessAdminPortal from frontend
    public function accessAdminPortal(Request $request)
    {
        $token = $request->query('token');

        if (!$token) {
            return redirect()->route('login')->with('error_login', 'Invalid access token.');
        }

        $personalAccessToken = PersonalAccessToken::findToken($token);

        if (!$personalAccessToken || !$personalAccessToken->tokenable) {
            return redirect()->route('login')->with('error_login', 'Authentication failed.');
        }

        $user = $personalAccessToken->tokenable;
        if (!$user->active_status) {
            return redirect()->route('login')->with('error_login', 'Your account has been deactivated.');
        }

        if ($user->subscription_required == 1 && $user->is_subscribed == 0) {
            if ($user->trail_mode == 0) {
                return redirect()->route('login')
                    ->with('error_login', 'Your subscription is inactive. Please subscribe to continue.');
            }
        }

        if ($user->role_id == Role::DESIGNER || $user->role_id == Role::MANUFACTURER) {
            Auth::login($user);
            return redirect()->route('dashboard')->with('success', 'Logged in successfully.');
        }

        return redirect()->route('login')->with('error_login', 'Authentication failed.');
    }

    public function resetDefaultPassword(Request $request)
    {
        try {
            $user = User::find($request->user_id);

            if (!$user) {
                return response()->json(['message' => 'User not found.', 'status' => 404], 404);
            }

            $user->update(['password' => Hash::make('12345678')]);

            $data = [
                'username' => $user->name,
                'subject' => 'Password changed to Default Password',
                'message' => 'Your password has been reset to "12345678" as the default. Please change your password for security reasons.'
            ];

            SendMail::to($user->email)->send(new DefaultPasswordResetMail($data));

            return response()->json(['message' => 'Password reset to default successfully.', 'status' => 200]);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Something went wrong.', 'status' => 500], 500);
        }
    }

    private function forgetCache(mixed $id, $slug): void
    {
        Cache::forget("shop_Setting_$id");
        Cache::forget("shop_Setting_$slug");
    }

    public function accessHouseBrandsAI()
    {
        try {
            $user = Auth::user();
            $response = Http::post(env('APP_HBAI_URL') . '/api/get-houseBrands-ai-token', [
                'email' => $user->email,
                'name' => $user->name,
            ]);

            if ($response->successful()) {
                $accessToken = $response->json('token');


                // Redirect user to the external system with the token
                return redirect()->away(env('APP_HBAI_URL')
                    .
                    "/get-access-with-token?token={$accessToken}");
            }
        } catch (Exception $e) {
            return back()->with('error', 'Failed to access external system.');
        }
    }

    public function getUser($user_id)
    {
        $user = User::find($user_id);

        return response()->json($user);
    }

    public function sendCredential(Request $request)
    {
        $data = [
            'subject' => $request->subject,
            'message' => $request->message,
            'username' => $request->user_name
        ];
        $shopInfo = Auth::user()->shop;

        $userInfo = User::find($request->id);

        try {
            SendMail::sender(Auth::user()->id)->to($request->to_email)->send(new SendUserCredentialMail($data, $shopInfo, $userInfo));
            return response()->json(['message' => 'Credential Send Successfully', 'status' => 200]);
        } catch (Exception $e) {
            return response()->json(['message' => 'Something went wrong', 'status' => 500]);
        }
    }


}
