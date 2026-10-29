<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CallForProposal;
use App\Models\ProposalApplication;
use App\Models\ScoringGuide;

class Admin_AnalyticsController extends Controller
{
    /**
     * Overview of every call: applications, decisions and reviewer progress.
     */
    public function index()
    {
        $max_obtainable_score = ScoringGuide::sum('mark_obtainable');

        $calls = CallForProposal::orderBy('created_at', 'desc')->get();

        // one pass over all applications, grouped by call, with each
        // application's reviewers and their scoring sheets pre-loaded
        $applications_by_call = ProposalApplication::with('reviews.is_reviewed')
                                                     ->get()
                                                     ->groupBy('call_for_proposal_id');

        $per_call = $calls->map(function ($call) use ($applications_by_call) {
            $applications = $applications_by_call->get($call->id, collect());
            $status_counts = $applications->groupBy(fn ($a) => $a->status ?: 'pending')->map->count();

            $assigned = $applications->sum(fn ($a) => $a->reviews->count());
            $completed = $applications->sum(fn ($a) => $a->reviews->filter(fn ($r) => $r->is_reviewed)->count());

            // average of each application's average score
            $application_averages = $applications->map(function ($a) {
                $sheets = $a->reviews->map(fn ($r) => $r->is_reviewed)->filter();
                return $sheets->isEmpty() ? null : $sheets->avg(fn ($s) => $s->total_score);
            })->filter(fn ($average) => ! is_null($average));

            return (object) [
                'call' => $call,
                'applications' => $applications->count(),
                'pending' => $status_counts->get('pending', 0),
                'acknowledged' => $status_counts->get('acknowledged', 0),
                'accepted' => $status_counts->get('accepted', 0),
                'rejected' => $status_counts->get('rejected', 0),
                'reviewers_assigned' => $assigned,
                'reviewers_completed' => $completed,
                'average_score' => $application_averages->isEmpty() ? null : round($application_averages->avg(), 2),
            ];
        });

        $totals = (object) [
            'calls' => $calls->count(),
            'open_calls' => $calls->filter(fn ($c) => $c->isOpen())->count(),
            'applications' => $per_call->sum('applications'),
            'awaiting_decision' => $per_call->sum('pending') + $per_call->sum('acknowledged'),
            'accepted' => $per_call->sum('accepted'),
            'rejected' => $per_call->sum('rejected'),
            'reviewers_assigned' => $per_call->sum('reviewers_assigned'),
            'reviewers_completed' => $per_call->sum('reviewers_completed'),
        ];

        return view('admin.analytics.index', compact('per_call', 'totals', 'max_obtainable_score'));
    }
}
