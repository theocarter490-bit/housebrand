<?php

namespace App\Http\Controllers\Auth;

use App\Models\Role;
use App\Models\User;
use Illuminate\View\View;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use App\Providers\RouteServiceProvider;
use App\Http\Requests\Auth\LoginRequest;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        $demo_users=[];
        if (config('app.demo_mode') == true) {
            $demo_users= User::with('role')->select('email','role_id')->where('active_status',1)->whereIn('role_id',[Role::SUPER_ADMIN,Role::DESIGNER,Role::MANUFACTURER])->get()->groupBy('role_id');
        }
        return view('auth.login',compact('demo_users'));
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();
        if ($request->user()->active_status == 0) {
            Auth::guard('web')->logout();
            return redirect()->route('login')->with('error_login', 'Your account is not active. Please contact with Administrator.');
        }

        return redirect()->intended(RouteServiceProvider::HOME);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
