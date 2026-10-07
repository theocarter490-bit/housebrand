<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Models\FreeSignupCode;
use App\Models\TrailCodeUse;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class FreeTrailController extends Controller
{

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'code' => 'required|string',
            ]);

            if ($validator->fails()) {
                return sendError('Validation Error', $validator->errors(), 403);
            }

            $freeSignupCode = FreeSignupCode::where('code', $request->code)
                ->withCount('uses')
                ->where('active_status', 1)
                ->first();

            if (!$freeSignupCode) {
                return sendError('Invalid invitation code. Please check and try again.');
            }

            if ($freeSignupCode->uses_count >= $freeSignupCode->no_of_use) {
                return sendError('This invitation code has reached its usage limit.');
            }

            $user = Auth::user();

            TrailCodeUse::where('user_id', $user->id)
                ->where('is_active', 1)
                ->update(['is_active' => 0]);

            // Calculate start and expiry dates
            $startAt = Carbon::now();
            $expireAt = $startAt->copy()->addDays($freeSignupCode->validity ?? 0);

            $freeSignupCodeUses = new TrailCodeUse();
            $freeSignupCodeUses->free_signup_code_id = $freeSignupCode->id;
            $freeSignupCodeUses->user_id = $user->id;
            $freeSignupCodeUses->code = $request->code;
            $freeSignupCodeUses->is_active = 1;
            $freeSignupCodeUses->start_at = $startAt;
            $freeSignupCodeUses->expire_at = $expireAt;
            $freeSignupCodeUses->save();

            // Disable subscription requirement for user
            $user->subscription_required = 0;
            $user->save();

            return sendResponse('Invitation code applied successfully.');
        } catch (Exception $exception) {
            return sendError('Something went wrong. Please try again later.');
        }
    }


}
