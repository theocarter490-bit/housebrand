<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\FreeSignupCode;
use Brian2694\Toastr\Facades\Toastr;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FreeSignupController extends Controller
{
    public function index(Request $request)
    {
        $search = '';
        $active_status = '';
        $signUpKeys = FreeSignupCode::withCount('uses');
        if ($request->get('active_status') != '') {
            $signUpKeys->where('active_status', $request->get('active_status'));
            $active_status = $request->get('active_status');
        }
        if ($request->get('search') != '') {
            $signUpKeys->where('code', 'like', '%' . $request->get('search') . '%');
            $search = $request->get('search');
        }
        $signUpKeys = $signUpKeys->latest()->get();
        return view('setting.free-signup', compact('signUpKeys', 'search', 'active_status'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'code' => 'required',
            'no_of_use' => 'required|numeric',
            'status' => 'required',
            'validity' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }


        try {
            $freeSignupCode = new FreeSignupCode();
            $freeSignupCode->code = $request->code;
            $freeSignupCode->no_of_use = $request->no_of_use;
            $freeSignupCode->validity = $request->validity;
            $freeSignupCode->active_status = $request->status;
            $freeSignupCode->save();
            Toastr::success('Invitation Code Added Successfully');
            return back();
        } catch (\Exception $exception) {
            Toastr::error('Something Went Wrong', 'Error');
            return back();
        }
    }

    public function changeStatus(Request $request)
    {
        $data = FreeSignupCode::find($request->id);
        if ($data) {
            $data->active_status = !$data->active_status;
            $data->save();
            Toastr::success('Status Updated Successfully');
            return response()->json(['message' => 'Status Updated Successfully', 'status' => 200]);
        } else {
            Toastr::error('Something Went Wrong', 'Error');
            return response()->json(['message' => 'Data Not Found!', 'status' => 404]);
        }
    }

    public function destroy(Request $request)
    {
        try {
            $freeSignup = FreeSignupCode::findOrFail($request->id);

            if ($freeSignup->uses()->exists()) {
                Toastr::error('This Invitation Code is already used and cannot be deleted.');
                return response()->json([
                    'text' => 'This Invitation Code is already used and cannot be deleted.',
                    'icon' => 'error'
                ], 500);
            }

            $freeSignup->delete();

            Toastr::success('Invitation Code Deleted Successfully');
            return response()->json([
                'text' => 'Invitation Code has been deleted.',
                'icon' => 'success'
            ], 200);
        } catch (Exception $e) {
            Toastr::error('Something Went Wrong', 'Error');
            return response()->json([
                'text' => 'Something went wrong',
                'icon' => 'error'
            ], 500);
        }
    }

    public function generateKey()
    {
        $key = strtoupper(bin2hex(random_bytes(8)));
        $formattedKey = implode('-', str_split($key, 4));

        return response(['key' => $formattedKey, 'status' => 200], 200);
    }

    public function userList($id)
    {
        $code = FreeSignupCode::with('uses.user.shop')->where('id', $id)->first();
        return response(['code' => $code, 'status' => 200], 200);
    }
}
