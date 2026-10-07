<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class CacheSetupController extends Controller
{
    public function index()
    {

        return view('setting.cache-setup');
    }

    public function cacheClear(Request $request)
    {
        if ($request->type === 'all') {
            Cache::flush();
        } else {
            removeDataFromRedisAPI([$request->type]);
        }
        return response()->json(['success' => 'Cache cleared'], 200);

    }
}
