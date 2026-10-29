<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;

class Staff_AuthController extends Controller
{
    public function login(LoginRequest $request)
    {
        // 5 wrong attempts for the same email from the same address locks it for a minute
        $request->ensureIsNotRateLimited();

        $email = $request->input('email');
        $password = $request->input('password');
        $remember = $request->boolean('remember');

        if (Auth::attempt(['email' => $email, 'password' => $password, 'role' => 'staff'], $remember) ||
            Auth::attempt(['email' => $email, 'password' => $password, 'role' => 'manager'], $remember)) {
            RateLimiter::clear($request->throttleKey());
            $request->session()->regenerate();

            return redirect()->route('staff.dashboard.index');
        }

        RateLimiter::hit($request->throttleKey());

        // only the email and the checkbox are kept, never the password
        return redirect()->to(route('welcome').'#signin')
                         ->withErrors(['email' => 'The email address or password is not correct.'])
                         ->withInput($request->only('email', 'remember'));
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome');
    }
}
