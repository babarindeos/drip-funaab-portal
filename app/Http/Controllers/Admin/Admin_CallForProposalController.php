<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\CallForProposal;
use Illuminate\Support\Str;
use App\Models\ProposalApplication;
use App\Models\ScoringGuide;
use App\Http\Classes\Notifier;
use App\Http\Classes\ResultsReport;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Mail\ApplicationStatusUpdatedMail;

class Admin_CallForProposalController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $status = in_array($request->query('status'), ['open', 'upcoming', 'closed', 'draft', 'archived'], true) ? $request->query('status') : null;

        // the summary figures describe everything, whatever is searched or filtered
        $stats = [
            'total' => CallForProposal::count(),
            'open' => CallForProposal::computedStatus('open')->count(),
            'applications' => ProposalApplication::count(),
        ];

        $query = CallForProposal::withCount(['proposal_applications', 'reviews']);

        if ($search !== '') {
            $query->where('title', 'like', '%'.addcslashes($search, '%_\\').'%');
        }

        if ($status) {
            $query->computedStatus($status);
        }

        $call_for_proposals = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('admin.call_for_proposals.index', compact('call_for_proposals', 'stats', 'search', 'status'));
    }


    public function create()
    {
        return view('admin.call_for_proposals.create');
    }


    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:call_for_proposals,title',
            'open_date' => 'required|date',
            'close_date' => 'required|date|after:open_date',
            'status' => 'nullable|in:draft,archived',
            'advert' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);


        $uuid = Str::orderedUuid();

        $formFields['uuid'] = $uuid;
        $formFields['title'] = $request->title;
        $formFields['description'] = $request->description;
        $formFields['open_date'] = $request->open_date;
        $formFields['close_date'] = $request->input('close_date');
        // Publishing a call live is the default; admins can save it as a
        // draft (not yet visible to staff) via the status field.
        $formFields['status'] = $request->input('status');
        

        if ($request->hasFile('advert'))
        {
            $advertFile = $request->file('advert');

            $new_advert_filename = $uuid.".".$advertFile->getClientOriginalExtension();

            $advertFile->storeAs('adverts', $new_advert_filename);

            $formFields['advert'] = "adverts/".$new_advert_filename;
        }


        
        try
        {
             $create = CallForProposal::create($formFields);

             if ($create)
             {
                    $data = [
                        'error' => true,
                        'status' => 'success',
                        'message' => 'Call for Proposal has been successfully created'
                    ];
             }
             else
             {
                     $data = [
                        'error' => true,
                        'status' => 'fail',
                        'message' => 'An error occurred creating the Call for Proposal'
                    ];
             }
        }
        catch(\Exception $e)
        {
                    $data = [
                        'error' => true,
                        'status' => 'fail',
                        'message' => $e->getMessage()
                    ];
        }


        if (($data['status'] ?? '') === 'success') {
            return redirect()->route('admin.call_for_proposals.index')->with($data);
        }

        return redirect()->back()->withInput()->with($data);
    
    }

    public function show(CallForProposal $call_for_proposal)
    {
        $assignments = $call_for_proposal->reviews();

        $counts = [
            'applications' => $call_for_proposal->proposal_applications()->count(),
            'sent_for_review' => $assignments->count(),
            'reviews_done' => \App\Models\ScoringSheet::whereIn('proposal_reviewers_id', $call_for_proposal->reviews()->pluck('proposal_reviewers.id'))->count(),
        ];

        return view('admin.call_for_proposals.show', compact('call_for_proposal', 'counts'));
    }


    public function submissions(Request $request, CallForProposal $call_for_proposal)
    {
        $statuses = ['pending', 'acknowledged', 'accepted', 'rejected'];

        $search = trim((string) $request->query('q', ''));
        $status = $request->query('status');
        if (! in_array($status, $statuses, true)) {
            $status = null;
        }

        $base = ProposalApplication::where('call_for_proposal_id', $call_for_proposal->id);

        // The summary figures describe the whole call, so they ignore the search and filter.
        $stats = [
            'total' => (clone $base)->count(),
            'acknowledged' => (clone $base)->where('status', 'acknowledged')->count(),
            'pending' => (clone $base)->where(function ($q) {
                                            $q->where('status', 'pending')->orWhereNull('status');
                                        })->count(),
        ];

        $query = (clone $base)->with(['owner', 'reviews.is_reviewed']);

        if ($search !== '') {
            $like = '%'.addcslashes($search, '%_\\').'%';

            $query->where(function ($q) use ($like) {
                $q->where('proposal_title', 'like', $like)
                  ->orWhere('principal_investigator', 'like', $like)
                  ->orWhereHas('owner', function ($owner) use ($like) {
                      $owner->where('surname', 'like', $like)
                            ->orWhere('firstname', 'like', $like)
                            ->orWhere('email', 'like', $like);
                  });
            });
        }

        if ($status === 'pending') {
            $query->where(function ($q) {
                $q->where('status', 'pending')->orWhereNull('status');
            });
        } elseif ($status) {
            $query->where('status', $status);
        }

        $applications = $query->orderBy('created_at', 'desc')
                              ->paginate(10)
                              ->withQueryString();

        return view('admin.call_for_proposals.proposal_application_submissions',
                    compact('call_for_proposal', 'applications', 'stats', 'search', 'status'));
    }


    /**
     * Ranked results for a call: every application with how many reviewers
     * it was sent to, how many have scored it, and its average score —
     * aggregated from potentially several reviewers' scoring sheets.
     */
    public function results(CallForProposal $call_for_proposal)
    {
        $proposal_applications = ProposalApplication::where('call_for_proposal_id', $call_for_proposal->id)
                                                     ->get()
                                                     ->sortByDesc(fn ($application) => $application->average_score ?? -1)
                                                     ->values();

        $max_obtainable_score = ScoringGuide::sum('mark_obtainable');

        return view('admin.call_for_proposals.results', compact('call_for_proposal', 'proposal_applications', 'max_obtainable_score'));
    }


    /**
     * Ranked results as a CSV file (opens directly in Excel).
     */
    public function export_results_csv(CallForProposal $call_for_proposal)
    {
        $rows = ResultsReport::rows(ProposalApplication::where('call_for_proposal_id', $call_for_proposal->id)->get());
        $max_obtainable_score = ScoringGuide::sum('mark_obtainable');
        $filename = Str::slug($call_for_proposal->title).'-results-'.now()->format('Y-m-d').'.csv';

        return response()->streamDownload(function () use ($rows, $max_obtainable_score) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 marker so Excel shows accented characters correctly

            fputcsv($out, ['Rank', 'Proposal Title', 'Principal Investigator', 'Applicant', 'Applicant Email',
                           'Reviewers Assigned', 'Reviewers Completed', 'Average Score', 'Maximum Score',
                           'Reviewer Scores', 'Decision']);

            foreach ($rows as $index => $row) {
                fputcsv($out, [
                    $index + 1,
                    ResultsReport::csv_safe($row->title),
                    ResultsReport::csv_safe($row->principal_investigator),
                    ResultsReport::csv_safe($row->applicant),
                    ResultsReport::csv_safe($row->email),
                    $row->assigned,
                    $row->completed,
                    $row->average ?? '',
                    $max_obtainable_score,
                    ResultsReport::csv_safe($row->reviewer_scores),
                    ucfirst($row->status),
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }


    /**
     * Ranked results as a printable PDF for committee records.
     */
    public function export_results_pdf(CallForProposal $call_for_proposal)
    {
        $rows = ResultsReport::rows(ProposalApplication::where('call_for_proposal_id', $call_for_proposal->id)->get());
        $max_obtainable_score = ScoringGuide::sum('mark_obtainable');
        $filename = Str::slug($call_for_proposal->title).'-results-'.now()->format('Y-m-d').'.pdf';

        return Pdf::loadView('admin.call_for_proposals.results_pdf', [
                        'call_for_proposal' => $call_for_proposal,
                        'rows' => $rows,
                        'max_obtainable_score' => $max_obtainable_score,
                        'generated_at' => now(),
                    ])
                    ->setPaper('a4', 'landscape')
                    ->download($filename);
    }


    public function proposal_application(CallForProposal $call_for_proposal, ProposalApplication $proposal_application)
    {
        return view('admin.call_for_proposals.proposal_application', compact('call_for_proposal', 'proposal_application'));

    }


    public function status_update(Request $request, ProposalApplication $proposal_application)
    {
        $request->validate([
            'status' => 'required|in:pending,acknowledged,accepted,rejected',
            'remark' => 'nullable|string|max:2000',
        ]);

        $previous_status = $proposal_application->status;

        $proposal_application->status = $request->status;
        $proposal_application->remark = $request->remark;
        $proposal_application->save();

        $message = 'Status has been updated successfully.';

        // Tell the applicant when the decision changes. Not for "pending"
        // (that's the starting state). The remark is internal ("office use
        // only") so it is only included if the admin ticked the box.
        if ($previous_status !== $proposal_application->status && $proposal_application->status !== 'pending')
        {
            $proposal_application->load('owner', 'call_for_proposal');

            $sent = Notifier::send(
                $proposal_application->owner->email,
                new ApplicationStatusUpdatedMail($proposal_application, $request->boolean('share_remark')),
                'status update - applicant'
            );

            $message .= $sent
                ? ' The applicant has been notified by email.'
                : ' The applicant could not be notified by email - please let them know directly.';
        }

        return redirect()->back()->with(['error' => true, 'status' => 'success', 'message' => $message]);
    }

    public function edit(CallForProposal $call_for_proposal)
    {
        return view('admin.call_for_proposals.edit', compact('call_for_proposal'));
    }

    public function update(Request $request, CallForProposal $call_for_proposal)
    {
        $request->validate([
            'title' => 'required|unique:call_for_proposals,title,'.$call_for_proposal->id,
            'open_date' => 'required|date',
            'close_date' => 'required|date|after:open_date',
            'status' => 'nullable|in:draft,archived',
            'advert' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:10240',
        ]);

        $formFields['title'] = $request->title;
        $formFields['description'] = $request->description;
        $formFields['open_date'] = $request->open_date;
        $formFields['close_date'] = $request->input('close_date');
        $formFields['status'] = $request->input('status');
        

        if ($request->hasFile('advert'))
        {
            $advertFile = $request->file('advert');

            $new_advert_filename = $call_for_proposal->uuid.".".$advertFile->getClientOriginalExtension();

            $advertFile->storeAs('adverts', $new_advert_filename);

            $formFields['advert'] = "adverts/".$new_advert_filename;
        }
        else
        {
            $formFields['advert'] = $call_for_proposal->advert;
        }

        try
        {
             $update = $call_for_proposal->update($formFields);

             if ($update)
             {
                    $data = [
                        'error' => true,
                        'status' => 'success',
                        'message' => 'Call for Proposal has been successfully updated'
                    ];
             }
             else
             {
                     $data = [
                        'error' => true,
                        'status' => 'fail',
                        'message' => 'An error occurred updating the Call for Proposal'
                    ];
             }
        }
        catch(\Exception $e)
        {
                    $data = [
                        'error' => true,
                        'status' => 'fail',
                        'message' => $e->getMessage()
                    ];
        }

        if (($data['status'] ?? '') === 'success') {
            return redirect()->route('admin.call_for_proposals.index')->with($data);
        }

        return redirect()->back()->withInput()->with($data);
    }


    public function destroy(CallForProposal $call_for_proposal)
    {
        // Don't allow a call to be deleted once staff have applied — archive
        // it instead so existing submissions and reviews stay intact.
        if ($call_for_proposal->proposal_applications()->exists())
        {
            return redirect()->route('admin.call_for_proposals.index')
                              ->with([
                                  'error' => true,
                                  'status' => 'fail',
                                  'message' => 'This call already has submissions and cannot be deleted. Set its status to Archived instead.'
                              ]);
        }

        $call_for_proposal->delete();

        return redirect()->route('admin.call_for_proposals.index')
                          ->with([
                              'error' => true,
                              'status' => 'success',
                              'message' => 'Call for Proposal has been deleted.'
                          ]);
    }

}
