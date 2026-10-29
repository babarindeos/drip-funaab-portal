<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\View\View;

class NewPasswordController extends Controller
{
    /** The page the emailed link opens: choose a new password. */
    public function create(Request $request): View
    {
        return view('auth.reset-password', ['request' => $request]);
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'token' => ['required'],
            'email' => ['required', 'email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'password.min' => 'Use at least 8 characters.',
            'password.confirmed' => 'The two passwords do not match.',
        ]);

        $role = null;

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user) use ($request, &$role) {
                $user->forceFill([
                    'password' => Hash::make($request->password),
                    'remember_token' => Str::random(60),
                ])->save();

                $role = $user->role;

                event(new PasswordReset($user));
            }
        );

        if ($status === Password::PASSWORD_RESET) {
            // send each person to the sign-in page they normally use
            $target = $role === 'admin' ? route('admin.auth.index') : route('welcome').'#signin';

            return redirect()->to($target)->with('status', 'Your password has been changed. You can now sign in with the new one.');
        }

        $message = $status === Password::INVALID_TOKEN
            ? 'This link has expired or has already been used. Please ask for a new one.'
            : 'This link does not match that email address. Use the address the email was sent to.';

        return back()->withInput($request->only('email'))->withErrors(['email' => $message]);
    }
}
