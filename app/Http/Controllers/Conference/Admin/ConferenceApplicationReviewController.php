<?php

namespace App\Http\Controllers\Conference\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conference\ConferenceApplication;
use App\Models\Conference\ConferenceCall;
use App\Models\User;
use App\Services\Conference\ConferenceEligibility;
use App\Services\Conference\ConferenceNotifications;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

/** The committee's view: review submitted applications and record the decision. */
class ConferenceApplicationReviewController extends Controller
{
    private const STATUS_FILTERS = ['submitted', 'approved', 'not_approved'];
    private const BENEFICIARY_FILTERS = ['first', 'previous'];
    private const SORTS = ['newest', 'oldest', 'priority'];

    public function index(Request $request, ConferenceCall $call)
    {
        $search = trim((string) $request->query('q', ''));
        $status = in_array($request->query('status'), self::STATUS_FILTERS, true) ? $request->query('status') : null;
        $beneficiary = in_array($request->query('beneficiary'), self::BENEFICIARY_FILTERS, true) ? $request->query('beneficiary') : null;
        $sort = in_array($request->query('sort'), self::SORTS, true) ? $request->query('sort') : 'newest';

        $base = ConferenceApplication::where('conference_call_id', $call->id);

        // the figures describe the whole call, whatever is searched or filtered
        $stats = [
            'submitted' => (clone $base)->where('status', '!=', ConferenceApplication::STATUS_DRAFT)->count(),
            'awaiting' => (clone $base)->where('status', ConferenceApplication::STATUS_SUBMITTED)->count(),
            'approved' => (clone $base)->where('status', ConferenceApplication::STATUS_APPROVED)->count(),
            'not_approved' => (clone $base)->where('status', ConferenceApplication::STATUS_NOT_APPROVED)->count(),
            'drafts' => (clone $base)->where('status', ConferenceApplication::STATUS_DRAFT)->count(),
        ];

        // drafts are not the committee's business until they are submitted
        $query = (clone $base)->where('status', '!=', ConferenceApplication::STATUS_DRAFT);

        if ($search !== '') {
            $like = '%'.addcslashes($search, '%_\\').'%';
            $query->where(function ($q) use ($like) {
                $q->where('candidate_name', 'like', $like)
                  ->orWhere('conference_title', 'like', $like)
                  ->orWhere('department', 'like', $like)
                  ->orWhere('funaab_email', 'like', $like)
                  ->orWhere('account_email', 'like', $like);
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($beneficiary) {
            $query->where('previously_sponsored', $beneficiary === 'previous');
        }

        // first-time beneficiaries are given preference, so "priority" lists them first
        match ($sort) {
            'oldest' => $query->orderBy('submitted_at', 'asc'),
            'priority' => $query->orderBy('previously_sponsored', 'asc')->orderBy('submitted_at', 'asc'),
            default => $query->orderBy('submitted_at', 'desc'),
        };

        $applications = $query->paginate(10)->withQueryString();

        return view('conference.admin.applications.index', compact('call', 'applications', 'stats', 'search', 'status', 'beneficiary', 'sort'));
    }

    public function show(ConferenceApplication $application)
    {
        $application->load(['call', 'documents', 'events.actor']);

        $call = $application->call;
        $checks = ConferenceEligibility::checks($application, $call);
        $decided_by = $application->decided_by ? User::find($application->decided_by) : null;

        return view('conference.admin.applications.show', compact('application', 'call', 'checks', 'decided_by'));
    }

    public function decide(Request $request, ConferenceApplication $application)
    {
        if ($application->status !== ConferenceApplication::STATUS_SUBMITTED) {
            return redirect()->route('admin.conference.applications.show', ['application' => $application->uuid])
                             ->with('error', 'Only a submitted application can be decided. This one is '.strtolower($application->status_label).'.');
        }

        $data = $request->validate([
            'decision' => ['required', Rule::in([ConferenceApplication::STATUS_APPROVED, ConferenceApplication::STATUS_NOT_APPROVED])],
            'remark' => ['nullable', 'string', 'max:2000'],
        ]);

        $share = $request->boolean('share_remark');

        if ($share && blank($data['remark'] ?? null)) {
            throw ValidationException::withMessages([
                'remark' => 'Write the remark you want the applicant to see, or untick "Show this remark to the applicant".',
            ]);
        }

        DB::connection(config('conference.connection', 'conference'))->transaction(function () use ($application, $data, $share) {
            $application->status = $data['decision'];
            $application->decided_at = now();
            $application->decided_by = Auth::id();
            $application->decision_remark = $data['remark'] ?? null;
            $application->decision_remark_shared = $share;
            $application->save();

            $application->recordEvent($data['decision'], Auth::id(), $data['remark'] ?? null);
        });

        // The decision is recorded. Telling the applicant is a courtesy and can never undo it.
        $notified = ConferenceNotifications::decided($application);

        // the audit trail shows whether the applicant was told
        $application->recordEvent(
            $notified ? 'applicant_notified' : 'notification_failed',
            Auth::id(),
            implode(', ', ConferenceNotifications::applicantAddresses($application))
        );

        return redirect()->route('admin.conference.applications.show', ['application' => $application->uuid])
                         ->with('success', 'The decision has been recorded: '.strtolower($application->status_label).'. '
                             .($notified
                                 ? 'The applicant has been notified by email.'
                                 : 'The applicant could not be notified by email, so please let them know directly.'));
    }

    /** A decision is final, but mistakes happen: reopening is allowed, with a reason, and is recorded. */
    public function reopen(Request $request, ConferenceApplication $application)
    {
        if (! in_array($application->status, [ConferenceApplication::STATUS_APPROVED, ConferenceApplication::STATUS_NOT_APPROVED], true)) {
            return redirect()->route('admin.conference.applications.show', ['application' => $application->uuid])
                             ->with('error', 'Only a decided application can be reopened.');
        }

        $data = $request->validate(['reason' => ['required', 'string', 'max:500']], [
            'reason.required' => 'Give a reason for reopening this decision. It is kept in the audit trail.',
        ]);

        $previous = $application->status_label;

        DB::connection(config('conference.connection', 'conference'))->transaction(function () use ($application, $data, $previous) {
            $application->status = ConferenceApplication::STATUS_SUBMITTED;
            $application->decided_at = null;
            $application->decided_by = null;
            $application->decision_remark = null;
            $application->decision_remark_shared = false;
            $application->save();

            $application->recordEvent('reopened', Auth::id(), 'Was '.strtolower($previous).'. Reason: '.$data['reason']);
        });

        return redirect()->route('admin.conference.applications.show', ['application' => $application->uuid])
                         ->with('success', 'The application has been reopened for a new decision.');
    }
}
