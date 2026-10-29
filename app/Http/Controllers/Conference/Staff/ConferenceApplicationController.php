<?php

namespace App\Http\Controllers\Conference\Staff;

use App\Http\Controllers\Controller;
use App\Models\Conference\ConferenceApplication;
use App\Models\Conference\ConferenceCall;
use App\Services\Conference\ConferenceEligibility;
use App\Services\Conference\ConferenceNotifications;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;

class ConferenceApplicationController extends Controller
{
    /** Calls staff can see, with the signed-in person's own application for each. */
    public function index()
    {
        $calls = ConferenceCall::visible()->orderBy('close_date', 'desc')->paginate(10);

        $mine = ConferenceApplication::forUser(Auth::id())
                    ->whereIn('conference_call_id', $calls->pluck('id'))
                    ->get()
                    ->keyBy('conference_call_id');

        return view('conference.staff.calls.index', compact('calls', 'mine'));
    }

    public function show(ConferenceCall $call)
    {
        abort_unless($call->status === null, 404);

        $mine = ConferenceApplication::forUser(Auth::id())->where('conference_call_id', $call->id)->first();

        return view('conference.staff.calls.show', compact('call', 'mine'));
    }

    /** Create the person's draft (once per call) and open the form. */
    public function start(ConferenceCall $call)
    {
        abort_unless($call->status === null, 404);

        $user = Auth::user();

        $existing = ConferenceApplication::forUser($user->id)->where('conference_call_id', $call->id)->first();
        if ($existing) {
            return $this->continueExisting($existing);
        }

        if (! $call->isOpen()) {
            return redirect()->route('staff.conference.calls.show', ['call' => $call->uuid])
                             ->with('error', 'This call is not open for applications.');
        }

        // pre-fill what we already know from the account and profile
        $categories = array_values(array_filter([
            $call->allow_teaching ? 'teaching' : null,
            $call->allow_non_teaching ? 'non_teaching' : null,
        ]));

        $application = new ConferenceApplication([
            'institution' => config('conference.institution'),
            'candidate_name' => trim($user->surname.' '.$user->firstname.' '.$user->middlename),
            'funaab_email' => $user->email,
            'phone' => optional($user->profile)->phone,
            'staff_rank' => optional($user->profile)->designation,
            'staff_category' => count($categories) === 1 ? $categories[0] : null,
            'cost_currency' => 'NGN',
        ]);
        $application->uuid = (string) Str::orderedUuid();
        $application->conference_call_id = $call->id;
        $application->user_id = $user->id;
        $application->status = ConferenceApplication::STATUS_DRAFT;
        $application->account_email = $user->email;

        try {
            $application->save();
        } catch (QueryException $e) {
            // a double-click can race past the check above; the database allows only one
            $existing = ConferenceApplication::forUser($user->id)->where('conference_call_id', $call->id)->first();
            if ($existing) {
                return $this->continueExisting($existing);
            }
            throw $e;
        }

        $application->recordEvent('created', $user->id);

        return redirect()->route('staff.conference.applications.edit', ['application' => $application->uuid]);
    }

    /** Read-only view of an application (the only view once it has been submitted). */
    public function application(ConferenceApplication $application)
    {
        $this->authorizeOwner($application);

        $application->load('call', 'documents', 'events');

        return view('conference.staff.applications.show', ['application' => $application, 'call' => $application->call]);
    }

    /** Uploads, plus the checklist and Submit button. */
    public function documents(ConferenceApplication $application)
    {
        $this->authorizeOwner($application);

        if ($blocked = $this->blockIfNotEditable($application)) {
            return $blocked;
        }

        $call = $application->call;

        // eligibility first, then the form: documents are added once both are done
        if (! ConferenceEligibility::entry($application)['passed']) {
            return redirect()->route('staff.conference.applications.edit', ['application' => $application->uuid])
                             ->with('error', 'Answer the eligibility questions first.');
        }

        $progress = ConferenceEligibility::progress($application);
        if (! empty($progress['missing'])) {
            return redirect()->route('staff.conference.applications.edit', ['application' => $application->uuid])
                             ->with('error', 'Fill in the whole form and save it before adding documents. Still missing: '.implode(', ', $progress['missing']).'.');
        }

        $application->load('documents');

        $types = $application->requiredDocumentTypes();
        $issues = ConferenceEligibility::submissionIssues($application, $call);

        return view('conference.staff.applications.documents', compact('application', 'call', 'types', 'issues'));
    }

    public function submit(ConferenceApplication $application)
    {
        $this->authorizeOwner($application);

        if ($blocked = $this->blockIfNotEditable($application)) {
            return $blocked;
        }

        // checked again here, whatever the page showed, so nothing incomplete can be submitted
        $issues = ConferenceEligibility::submissionIssues($application, $application->call);

        if (! $issues['ok']) {
            return redirect()->route('staff.conference.applications.documents', ['application' => $application->uuid])
                             ->with('error', 'Your application cannot be submitted yet. Please complete the items listed below.');
        }

        DB::connection(config('conference.connection', 'conference'))->transaction(function () use ($application) {
            $application->status = ConferenceApplication::STATUS_SUBMITTED;
            $application->submitted_at = now();
            $application->save();

            $application->recordEvent('submitted', Auth::id());
        });

        // The application is saved. The emails are a courtesy and can never undo or block it.
        $confirmed = ConferenceNotifications::submitted($application);

        return redirect()->route('staff.conference.applications.show', ['application' => $application->uuid])
                         ->with('success', 'Your application has been submitted. The committee will review it.'.($confirmed ? ' A confirmation has been emailed to you.' : ''));
    }

    public function edit(ConferenceApplication $application)
    {
        $this->authorizeOwner($application);

        if ($blocked = $this->blockIfNotEditable($application)) {
            return $blocked;
        }

        $call = $application->call;
        $checks = ConferenceEligibility::checks($application, $call);
        $progress = ConferenceEligibility::progress($application);
        $entry = ConferenceEligibility::entry($application);

        return view('conference.staff.applications.edit', compact('application', 'call', 'checks', 'progress', 'entry'));
    }

    /**
     * The entry criteria. Until these are answered and met, the rest of the form stays closed.
     * Saved on their own (and validated in their own error bag), so they can never wipe the rest of the draft.
     */
    public function eligibility(Request $request, ConferenceApplication $application)
    {
        $this->authorizeOwner($application);

        if ($blocked = $this->blockIfNotEditable($application)) {
            return $blocked;
        }

        $request->validateWithBag('eligibility', [
            'first_appointment_date' => ['required', 'date', 'before_or_equal:today', 'after_or_equal:1950-01-01'],
            'is_confirmed_staff' => ['required', Rule::in(['yes', 'no'])],
            'confirmation_date' => ['nullable', 'required_if:is_confirmed_staff,yes', 'date', 'before_or_equal:today', 'after_or_equal:first_appointment_date'],
        ], [
            'first_appointment_date.required' => 'Enter the date of your FUNAAB appointment.',
            'first_appointment_date.before_or_equal' => 'The date of appointment cannot be in the future.',
            'is_confirmed_staff.required' => 'Say whether you are a confirmed member of staff.',
            'confirmation_date.required_if' => 'Enter the date you were confirmed.',
            'confirmation_date.before_or_equal' => 'The date of confirmation cannot be in the future.',
            'confirmation_date.after_or_equal' => 'The date of confirmation cannot be before your date of appointment.',
        ]);

        $confirmed = $request->input('is_confirmed_staff') === 'yes';

        $application->first_appointment_date = $request->input('first_appointment_date');
        $application->is_confirmed_staff = $confirmed;
        $application->confirmation_date = $confirmed ? $request->input('confirmation_date') : null;
        // always worked out from the date, never typed, so it cannot be misstated
        $application->years_in_institution = (int) $application->first_appointment_date->diffInYears(now());
        $application->save();

        $entry = ConferenceEligibility::entry($application);

        return redirect()->route('staff.conference.applications.edit', ['application' => $application->uuid])
                         ->with($entry['passed'] ? 'success' : 'error', $entry['passed']
                             ? 'You meet the eligibility requirements. Please complete the rest of the form below.'
                             : 'You do not meet the eligibility requirements, so you cannot apply for this call.');
    }

    /** Save the draft. Only the FORMAT is checked here; completeness is enforced when submitting. */
    public function update(Request $request, ConferenceApplication $application)
    {
        $this->authorizeOwner($application);

        if ($blocked = $this->blockIfNotEditable($application)) {
            return $blocked;
        }

        if (! ConferenceEligibility::entry($application)['passed']) {
            return redirect()->route('staff.conference.applications.edit', ['application' => $application->uuid])
                             ->with('error', 'Answer the eligibility questions first. The rest of the form opens when you qualify.');
        }

        $call = $application->call;
        $data = $this->validatedDraft($request, $call);

        $data['previously_sponsored'] = $request->boolean('previously_sponsored');
        $data['declaration_accepted'] = $request->boolean('declaration_accepted');

        // bank details are stored encrypted and never shown again, so leaving a box blank keeps the saved value
        foreach (['account_number', 'sort_code'] as $secret) {
            if (blank($request->input($secret))) {
                unset($data[$secret]);
            }
        }

        // details that no longer apply are cleared, so stale answers are never submitted
        if (! $data['previously_sponsored']) {
            foreach (['last_conference_title', 'last_conference_date', 'last_conference_venue', 'last_conference_publication', 'publication_evidence_note'] as $field) {
                $data[$field] = null;
            }
        }

        if (($data['staff_category'] ?? null) === 'non_teaching') {
            $data['presentation_type'] = 'none'; // workshops have no paper to present
        }

        $application->fill($data);

        // worked out here so the numbers always agree with the dates
        $application->duration_days = null;
        if ($application->conference_start_date && $application->conference_end_date) {
            $days = $application->conference_start_date->diffInDays($application->conference_end_date) + 1;

            if ($days > 200) {
                throw ValidationException::withMessages(['conference_end_date' => 'The end date is too far after the start date.']);
            }

            $application->duration_days = $days;
        }

        $application->declared_at = $application->declaration_accepted ? ($application->declared_at ?? now()) : null;

        $application->save();

        return redirect()->route('staff.conference.applications.edit', ['application' => $application->uuid])
                         ->with('success', 'Your draft has been saved.');
    }

    // ------------------------------------------------------------------

    private function validatedDraft(Request $request, ConferenceCall $call): array
    {
        $allowed_categories = array_values(array_filter([
            $call->allow_teaching ? 'teaching' : null,
            $call->allow_non_teaching ? 'non_teaching' : null,
        ]));

        return $request->validate([
            'funaab_email' => ['nullable', 'email', 'max:255'],

            'candidate_name' => ['nullable', 'string', 'max:255'],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'date_of_birth' => ['nullable', 'date', 'before:today'],
            'present_position' => ['nullable', 'string', 'max:255'],
            'staff_category' => ['nullable', Rule::in($allowed_categories)],
            'phone' => ['nullable', 'regex:/^[0-9+()\-\s]{7,20}$/'],

            'department' => ['nullable', 'string', 'max:255'],
            'highest_qualification' => ['nullable', 'string', 'max:255'],
            'area_of_specialisation' => ['nullable', 'string', 'max:255'],
            'staff_rank' => ['nullable', 'string', 'max:255'],
            'grade_level' => ['nullable', 'string', 'max:50'],
            'work_experience_years' => ['nullable', 'integer', 'between:0,60'],

            'conference_organiser' => ['nullable', 'string', 'max:255'],
            'conference_title' => ['nullable', 'string', 'max:255'],
            'conference_venue' => ['nullable', 'string', 'max:255'],
            'conference_scope' => ['nullable', Rule::in(['local', 'overseas'])],
            'conference_mode' => ['nullable', Rule::in(['in_person', 'online'])],
            'presentation_type' => ['nullable', Rule::in(['oral', 'poster'])],
            'conference_website' => ['nullable', 'url', 'max:255'],
            'conference_email' => ['nullable', 'email', 'max:255'],
            'conference_start_date' => ['nullable', 'date'],
            'conference_end_date' => ['nullable', 'date', 'after_or_equal:conference_start_date'],

            'cost_currency' => ['required', Rule::in(array_keys(config('conference.currencies')))],
            'registration_fee' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'flight_cost' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'accommodation_cost' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'feeding_cost' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],
            'excursion_cost' => ['nullable', 'numeric', 'min:0', 'max:999999999999'],

            'last_conference_title' => ['nullable', 'string', 'max:255'],
            'last_conference_date' => ['nullable', 'date', 'before_or_equal:today'],
            'last_conference_venue' => ['nullable', 'string', 'max:255'],
            'last_conference_publication' => ['nullable', 'string', 'max:2000'],
            'publication_evidence_note' => ['nullable', 'string', 'max:2000'],

            'bank_name' => ['nullable', 'string', 'max:255'],
            'bank_branch' => ['nullable', 'string', 'max:255'],
            'account_name' => ['nullable', 'string', 'max:255'],
            'account_number' => ['nullable', 'regex:/^[0-9]{10}$/'],
            'sort_code' => ['nullable', 'regex:/^[0-9]{6,12}$/'],
        ], [
            'work_experience_years.integer' => 'Enter a whole number of years, without decimals.',
            'work_experience_years.between' => 'Enter a number of years between 0 and 60.',
            'phone.regex' => 'Enter a valid phone number (digits, spaces, + ( ) and - only).',
            'account_number.regex' => 'An account number has 10 digits.',
            'sort_code.regex' => 'A sort code is made of 6 to 12 digits.',
            'staff_category.in' => 'This call is not open to that staff category.',
            'conference_end_date.after_or_equal' => 'The end date cannot be before the start date.',
            'date_of_birth.before' => 'The date of birth must be in the past.',
        ]);
    }

    private function authorizeOwner(ConferenceApplication $application): void
    {
        // 404, not 403, so nobody can probe which applications exist
        abort_unless((int) $application->user_id === (int) Auth::id(), 404);
    }

    private function blockIfNotEditable(ConferenceApplication $application)
    {
        if (! $application->isEditable()) {
            return redirect()->route('staff.conference.applications.show', ['application' => $application->uuid]);
        }

        if (! $application->call->isOpen()) {
            return redirect()->route('staff.conference.calls.index')
                             ->with('error', 'This call has closed, so the draft can no longer be edited.');
        }

        return null;
    }

    private function continueExisting(ConferenceApplication $existing)
    {
        if ($existing->isEditable()) {
            return redirect()->route('staff.conference.applications.edit', ['application' => $existing->uuid]);
        }

        return redirect()->route('staff.conference.applications.show', ['application' => $existing->uuid]);
    }
}
