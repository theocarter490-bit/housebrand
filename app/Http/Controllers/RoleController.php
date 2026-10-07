<?php

namespace App\Http\Controllers;

use Exception;
use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Brian2694\Toastr\Facades\Toastr;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RoleStoreRequest;
use App\Http\Requests\RoleUpdateRequest;
use Illuminate\Support\Facades\Validator;
use App\Http\Requests\IdValidationRequest;
use Illuminate\Validation\ValidationException;

class RoleController extends Controller
{
    //

    public function index()
    {
        $roles = Role::where('user_id', Auth::user()->id)->with('users')->where('id', '!=', Role::CUSTOMER)->withCount('users')->get();
        return view('role.index', compact('roles'));
    }

    public function store(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'name' => [
                'required',
                'max:255',
                Rule::unique('roles')->where(function ($query) {
                    return $query->where('user_id', Auth::id());
                }),
            ],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                Toastr::error($error, 'Validation Error');
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        try {
            $role = new Role();
            $role->name = $request->name;
            $role->type = 1;
            $role->active_status = 1;
            $role->user_id = Auth::id();
            $role->created_by = Auth::id();
            $role->updated_by = Auth::id();
            $role->save();

            Toastr::success('Role Created Successfully');
            return redirect()->route('role.assignPermission', $role->id);
        } catch (\Exception $e) {
            Toastr::error('Something went wrong');
            return redirect()->back();
        }
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|unique:roles,name,' . $request->role_id . '|max:255',
            'role_id' => 'required|exists:roles,id|integer',
        ]);
        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                Toastr::error($error, 'Validation Error');
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }
        try {
            $role = Role::find($request->role_id);
            $role->name = $request->name;
            $role->updated_by = Auth::user()->id;
            $role->save();
            Toastr::success('Role Updated Successfully');
        } catch (Exception $e) {
            Toastr::error('Something went wrong');
        }
        return redirect()->back();
    }


    public function destroy(IdValidationRequest $request)
    {
        try {
            $role = Role::withCount('users')->find($request->role_id);

            if ($role->users_count == 0) {
                $role->delete();
                return response()->json(['text' => 'Role has been deleted.', 'icon' => 'success']);
            } else {
                return response()->json(['text' => "Role can't be deleted. Because role has users", 'icon' => 'error']);
            }
        } catch (Exception $e) {
            return response()->json(['text' => "Something went wrong"]);
        }
    }

    public function assignPermission($id)
    {
        try {
            $role = Role::find($id);
            if ($role->id == Role::SUPER_ADMIN) {
                Toastr::warning('You can not modify permission to this role');
                return redirect()->route('role.index');
            }
            $permissions = [];
            if (Auth::user()->role_id == Role::SUPER_ADMIN) {
                $permissions = Permission::all();
            } elseif (Auth::user()->role_id == Role::DESIGNER || Auth::user()->role_id == Role::MANUFACTURER) {
                $permissionsData = Auth::user()->role->permissions;
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


            return view('role.assign_permission', compact('role', 'permissions'));
        } catch (\Throwable $th) {
            Toastr::error(__('Something went wrong!'), 'Error', ['timeOut' => 2000]);
            return redirect()->back();
        }
    }


    public function permissionUpdate(Request $request)
    {

        try {
            $permissionUpdate = Role::findOrFail($request->role_id);
            $permissionUpdate->permissions = $request->permissions;
            $permissionUpdate->save();

            Toastr::success(__('Permission Update Successfully'), 'Success', ['timeOut' => 2000]);
            return redirect()->route('role.index');
        } catch (\Throwable $th) {
            Toastr::error(__('Something went wrong!'), 'Error', ['timeOut' => 2000]);
            return redirect()->back();
        }
    }
}
