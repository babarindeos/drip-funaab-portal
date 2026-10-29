<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\CallForProposal;
use App\Models\Conference\ConferenceApplication;
use App\Models\Conference\ConferenceCall;
use App\Models\Profile;
use App\Models\ProposalApplication;
use Illuminate\Support\Facades\Auth;

class Staff_DashboardController extends Controller
{
    public function index()
    {
        $user_id = Auth::id();

        // a new user fills in their profile first
        if (! Profile::where('user_id', $user_id)->exists()) {
            return redirect()->route('staff.profile.create');
        }

        $open_proposal_calls = CallForProposal::computedStatus('open')->orderBy('close_date', 'asc')->take(5)->get();

        $my_proposal_applications = ProposalApplication::with('call_for_proposal')
                                        ->where('user_id', $user_id)
                                        ->latest()
                                        ->take(5)
                                        ->get();

        $proposal_application_count = ProposalApplication::where('user_id', $user_id)->count();

        // the conference module has its own database: if it is not reachable the page still opens without it
        $conference_available = false;
        $open_conference_calls = collect();
        $my_conference_applications = collect();
        $conference_application_count = 0;

        try {
            $open_conference_calls = ConferenceCall::visible()->computedStatus('open')->orderBy('close_date', 'asc')->take(5)->get();

            $my_conference_applications = ConferenceApplication::with('call')
                                                ->forUser($user_id)
                                                ->latest('updated_at')
                                                ->take(5)
                                                ->get();

            $conference_application_count = ConferenceApplication::forUser($user_id)->count();
            $conference_available = true;
        } catch (\Throwable $e) {
            report($e);
        }

        return view('staff.dashboard', compact(
            'open_proposal_calls', 'my_proposal_applications', 'proposal_application_count',
            'open_conference_calls', 'my_conference_applications', 'conference_application_count', 'conference_available'
        ));
    }
}
