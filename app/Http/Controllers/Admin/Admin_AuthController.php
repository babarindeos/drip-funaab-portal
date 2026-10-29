<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class Admin_AuthController extends Controller
{
    public function index()
    {
        return view('admin.auth.login');
    }

    public function login(LoginRequest $request)
    {
        // 5 wrong attempts for the same email from the same address locks it for a minute
        $request->ensureIsNotRateLimited();

        $credentials = [
            'email' => $request->input('email'),
            'password' => $request->input('password'),
            'role' => 'admin',
        ];

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            RateLimiter::clear($request->throttleKey());
            $request->session()->regenerate();

            return redirect()->route('admin.dashboard.index');
        }

        RateLimiter::hit($request->throttleKey());

        // only the email and the checkbox are kept, never the password
        return back()->withErrors(['email' => 'The email address or password is not correct.'])
                     ->withInput($request->only('email', 'remember'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.auth.index');
    }
}
