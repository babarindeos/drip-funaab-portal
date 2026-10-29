<?php

namespace App\Http\Controllers\Guest;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\CallForProposal;
use App\Models\Conference\ConferenceCall;
use Illuminate\Pagination\LengthAwarePaginator;
use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use App\Mail\NewUserMail;

class Guest_WelcomeController extends Controller
{
    /** How many calls the homepage shows, newest first, across both kinds. */
    private const HOME_LIMIT = 6;

    /**
     * Homepage: the latest Calls for IBR Proposals and Calls for Conference/Workshop
     * in one list, each marked with its kind, plus links to the two full lists.
     */
    public function index()
    {
        $items = $this->publishedProposalCalls()
                      ->orderBy('created_at', 'desc')
                      ->take(self::HOME_LIMIT)
                      ->get()
                      ->map(fn ($call) => ['type' => 'ibr', 'call' => $call]);

        // the conference module has its own database: if it cannot be reached the page still opens
        $conference_available = true;
        try {
            $items = $items->concat(
                ConferenceCall::visible()
                    ->orderBy('created_at', 'desc')
                    ->take(self::HOME_LIMIT)
                    ->get()
                    ->map(fn ($call) => ['type' => 'conference', 'call' => $call])
            );
        } catch (\Throwable $e) {
            report($e);
            $conference_available = false;
        }

        $latest_calls = $items->sortByDesc(fn ($item) => optional($item['call']->created_at)->timestamp ?? 0)
                              ->take(self::HOME_LIMIT)
                              ->values();

        return view('welcome', compact('latest_calls', 'conference_available'));
    }


    /** Every published Call for IBR Proposals (open, upcoming and closed). */
    public function call_for_proposals()
    {
        $call_for_proposals = $this->publishedProposalCalls()
                                   ->orderBy('close_date', 'desc')
                                   ->paginate(10);

        return view('call_for_proposals', compact('call_for_proposals'));
    }


    /** Every published Call for Conference/Workshop (open, upcoming and closed). */
    public function conference_calls()
    {
        $conference_available = true;

        try {
            $conference_calls = ConferenceCall::visible()
                                    ->orderBy('close_date', 'desc')
                                    ->paginate(10);
        } catch (\Throwable $e) {
            report($e);
            $conference_available = false;
            $conference_calls = new LengthAwarePaginator([], 0, 10);
        }

        return view('conference_calls', compact('conference_calls', 'conference_available'));
    }


    /** Calls the public may see: drafts and archived calls are hidden. */
    private function publishedProposalCalls()
    {
        return CallForProposal::where(function ($q) {
            $q->whereNull('status')
              ->orWhereNotIn('status', [CallForProposal::STATUS_DRAFT, CallForProposal::STATUS_ARCHIVED]);
        });
    }


    public function register()
    {
        return view('register');
    }

    public function store(Request $request)
    {
            $formFields = $request->validate([
                'surname' => 'required|string|max:255',
                'firstname' => 'required|string|max:255',
                'email' => 'required|string|email|max:255|unique:users'
            ], [
                'surname.required' => 'Enter your surname.',
                'firstname.required' => 'Enter your first name.',
                'email.required' => 'Enter your email address.',
                'email.email' => 'Enter a valid email address.',
                'email.unique' => 'An account already uses this email address. Sign in, or use "Forgot password?" if you cannot remember the password.',
            ]);

            // Keep the plain-text password: it is emailed to the user below.
            // (Only the hash is stored, so it can't be recovered afterwards.)
            $plain_password = Str::random(10);

            try
            {
                DB::beginTransaction();

                $user = new User();
                $user->surname = $formFields['surname'];
                $user->firstname = $formFields['firstname'];
                $user->middlename = $request->input('middlename');
                $user->email = $formFields['email'];
                $user->password = bcrypt($plain_password);
                $user->role = 'staff';
                $user->save();

                // Unlike other notifications, this email IS the point of
                // registering - it's the only way the person learns their
                // password. If it can't be sent, the account is rolled back
                // so they can simply register again.
                Mail::to($user->email)->send(new NewUserMail([
                    'fullname' => trim($user->firstname.' '.$user->surname),
                    'firstname' => $user->firstname,
                    'username' => $user->email,
                    'password' => $plain_password,
                ]));

                DB::commit();

                return redirect()->route('guest.auth.register')->with('success', 'Your login details have been sent to '.$user->email.'. Please check your inbox (and spam folder).');
            }
            catch (\Throwable $e)
            {
                DB::rollBack();
                report($e);

                return back()->withInput()->withErrors(['error' => 'We could not email your login details, so your account was not created. Please try again shortly, or contact DRIP if this keeps happening.']);
            }
    }   


    public function check_auth()
    {
        if(Auth::check())
        {
            $role = (auth()->user()->role);

            if ($role=='admin')
            {
                return redirect()->route('admin.dashboard.index');
            }
            else
            {
                return redirect()->route('staff.dashboard.index');
            }
            
        }
        else
        {
            return redirect()->route('welcome');
        }
    }


    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('welcome');
    }


}
