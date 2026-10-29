<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;

class PasswordResetLinkController extends Controller
{
    /** "Forgot your password?": ask for the email address. */
    public function create(): View
    {
        return view('auth.forgot-password');
    }

    /**
     * Email a reset link. The answer is the same whether or not the address has an
     * account, so the page cannot be used to find out who is registered.
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ], [
            'email.required' => 'Enter the email address of your account.',
            'email.email' => 'Enter a valid email address.',
        ]);

        try {
            $status = Password::sendResetLink($request->only('email'));
        } catch (\Throwable $e) {
            // the mail server could not be reached: say so, rather than claim it was sent
            report($e);

            return back()->withInput($request->only('email'))
                         ->withErrors(['email' => 'We could not send the email just now. Please try again in a few minutes, or contact DRIP.']);
        }

        if ($status === Password::RESET_THROTTLED) {
            return back()->withInput($request->only('email'))
                         ->withErrors(['email' => 'A link was sent to this address a moment ago. Please wait a minute before asking again.']);
        }

        return back()->with('status', 'If an account uses '.$request->input('email').', a link to set a new password is on its way. It works for 60 minutes. Check your spam folder if it does not arrive.');
    }
}
