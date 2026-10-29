<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CallForProposal;
use App\Models\Conference\ConferenceApplication;
use App\Models\Conference\ConferenceCall;
use App\Models\ProposalApplication;
use App\Models\ProposalReviewers;
use App\Models\Reviewer;

class Admin_DashboardController extends Controller
{
    /** Overview of both modules: what is open and what is waiting for the admin. */
    public function index()
    {
        // proposals still waiting for a decision (status was never set, or is pending / acknowledged)
        $awaiting = fn ($query) => $query->where(function ($q) {
            $q->whereNull('status')->orWhereIn('status', ['pending', 'acknowledged']);
        });

        $proposals = [
            'calls' => CallForProposal::count(),
            'open' => CallForProposal::computedStatus('open')->count(),
            'applications' => ProposalApplication::count(),
            'awaiting' => $awaiting(ProposalApplication::query())->count(),
            'reviews_outstanding' => ProposalReviewers::doesntHave('is_reviewed')->count(),
            'reviewers' => Reviewer::count(),
        ];

        $recent_proposals = $awaiting(ProposalApplication::with(['owner', 'call_for_proposal']))
                                ->orderBy('created_at', 'asc')   // the ones waiting longest first
                                ->take(5)
                                ->get();

        // The conference module has its own database. If it is not set up or not reachable,
        // the dashboard still opens and simply leaves that part out.
        $conference = ['available' => false];
        $recent_conference = collect();

        try {
            $conference = [
                'available' => true,
                'calls' => ConferenceCall::count(),
                'open' => ConferenceCall::computedStatus('open')->count(),
                'submitted' => ConferenceApplication::where('status', '!=', ConferenceApplication::STATUS_DRAFT)->count(),
                'awaiting' => ConferenceApplication::where('status', ConferenceApplication::STATUS_SUBMITTED)->count(),
            ];

            $recent_conference = ConferenceApplication::with('call')
                                    ->where('status', ConferenceApplication::STATUS_SUBMITTED)
                                    ->orderBy('submitted_at', 'asc')
                                    ->take(5)
                                    ->get();
        } catch (\Throwable $e) {
            report($e);
        }

        return view('admin.dashboard', compact('proposals', 'recent_proposals', 'conference', 'recent_conference'));
    }
}
