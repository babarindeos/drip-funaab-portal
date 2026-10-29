<?php

namespace App\Http\Controllers\Conference\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conference\ConferenceApplication;
use App\Models\Conference\ConferenceCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ConferenceCallController extends Controller
{
    private const STATUS_FILTERS = ['open', 'upcoming', 'closed', 'draft', 'archived'];

    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));
        $status = $request->query('status');
        if (! in_array($status, self::STATUS_FILTERS, true)) {
            $status = null;
        }

        // summary figures describe everything, regardless of the search / filter
        $stats = [
            'total' => ConferenceCall::count(),
            'open' => ConferenceCall::computedStatus('open')->count(),
            'submitted' => ConferenceApplication::where('status', '!=', ConferenceApplication::STATUS_DRAFT)->count(),
        ];

        $query = ConferenceCall::withCount([
            'applications as applications_total',
            'applications as applications_submitted' => fn ($q) => $q->where('status', '!=', ConferenceApplication::STATUS_DRAFT),
        ]);

        if ($search !== '') {
            $query->where('title', 'like', '%'.addcslashes($search, '%_\\').'%');
        }

        if ($status) {
            $query->computedStatus($status);
        }

        $calls = $query->orderBy('created_at', 'desc')->paginate(10)->withQueryString();

        return view('conference.admin.calls.index', compact('calls', 'stats', 'search', 'status'));
    }

    public function create()
    {
        return view('conference.admin.calls.create', ['call' => new ConferenceCall([
            'max_duration_days' => 5,
            'allow_teaching' => true,
            'allow_non_teaching' => true,
        ])]);
    }

    public function store(Request $request)
    {
        $data = $this->validated($request);

        $call = new ConferenceCall($data);
        $call->uuid = (string) Str::orderedUuid();
        $call->created_by = Auth::id();

        if ($request->hasFile('advert')) {
            $call->advert = $request->file('advert')->store('conference_adverts', 'public');
        }

        $call->save();

        return redirect()->route('admin.conference.calls.index')
                         ->with('success', 'The call "'.$call->title.'" has been created.');
    }

    public function edit(ConferenceCall $call)
    {
        return view('conference.admin.calls.edit', compact('call'));
    }

    public function update(Request $request, ConferenceCall $call)
    {
        $data = $this->validated($request, $call);

        $call->fill($data);

        if ($request->hasFile('advert')) {
            if ($call->advert) {
                Storage::disk('public')->delete($call->advert);
            }
            $call->advert = $request->file('advert')->store('conference_adverts', 'public');
        }

        $call->save();

        return redirect()->route('admin.conference.calls.index')
                         ->with('success', 'The call "'.$call->title.'" has been updated.');
    }

    public function destroy(ConferenceCall $call)
    {
        // Applications (even drafts) belong to people - archive the call instead.
        if ($call->applications()->exists()) {
            return redirect()->route('admin.conference.calls.index')
                             ->with('error', 'This call already has applications and cannot be deleted. Set its status to Archived instead.');
        }

        if ($call->advert) {
            Storage::disk('public')->delete($call->advert);
        }

        $title = $call->title;
        $call->delete();

        return redirect()->route('admin.conference.calls.index')
                         ->with('success', 'The call "'.$title.'" has been deleted.');
    }

    /**
     * Shared by create and update.
     */
    private function validated(Request $request, ?ConferenceCall $call = null): array
    {
        $connection = config('conference.connection', 'conference');

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:255', Rule::unique($connection.'.conference_calls', 'title')->ignore($call?->id)],
            'description' => ['nullable', 'string', 'max:5000'],
            'open_date' => ['required', 'date'],
            'close_date' => ['required', 'date', 'after_or_equal:open_date'],
            'earliest_conference_date' => ['required', 'date'],
            'max_duration_days' => ['required', 'integer', 'between:1,30'],
            'advert' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,doc,docx', 'max:10240'],
            'approval_instructions' => ['nullable', 'string', 'max:3000'],
            'status' => ['nullable', 'in:draft,archived'],
        ], [
            'close_date.after_or_equal' => 'The application deadline cannot be before the opening date.',
            'max_duration_days.between' => 'The maximum duration must be between 1 and 30 days.',
        ]);

        // checkboxes are absent from the request when unticked
        $validated['allow_teaching'] = $request->boolean('allow_teaching');
        $validated['allow_non_teaching'] = $request->boolean('allow_non_teaching');

        if (! $validated['allow_teaching'] && ! $validated['allow_non_teaching']) {
            throw ValidationException::withMessages([
                'allow_teaching' => 'Select at least one staff category that may apply.',
            ]);
        }

        $validated['status'] = $validated['status'] ?? null;

        // the file itself is stored separately in store() / update()
        unset($validated['advert']);

        return $validated;
    }
}
