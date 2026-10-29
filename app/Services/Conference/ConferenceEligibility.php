<?php

namespace App\Services\Conference;

use App\Models\Conference\ConferenceApplication;
use App\Models\Conference\ConferenceCall;

/**
 * The rules from the TETFund conference memo, applied to one application:
 *  - the applicant's staff category may apply to this call
 *  - the conference is held in person (not online)
 *  - teaching staff present orally (poster presentations are not supported)
 *  - it starts on or after the call's earliest eligible date
 *  - it lasts no longer than the call's maximum duration
 *  - previous TETFund beneficiaries provide evidence of Q1 / Q2 publications
 *
 * Used to show live feedback while the form is a draft, and (in the next step)
 * to block submission when a rule is broken.
 */
class ConferenceEligibility
{
    public const PASS = 'pass';
    public const FAIL = 'fail';
    public const PENDING = 'pending';

    /** @return array<int, array{key:string,label:string,status:string,message:?string}> */
    public static function checks(ConferenceApplication $a, ConferenceCall $call): array
    {
        $noun = strtolower($a->event_noun);
        $checks = [];

        // 0. who may apply at all (applications submitted before this rule existed are not judged by it)
        if ($a->isEditable() || $a->is_confirmed_staff !== null) {
            $min = self::minYears();
            $years = self::serviceYears($a);
            $service_label = 'You have served FUNAAB for more than '.($min - 1).' '.(($min - 1) === 1 ? 'year' : 'years');

            if ($years === null) {
                $checks[] = self::item('service', $service_label, self::PENDING, 'Enter the date of your FUNAAB appointment.');
            } elseif ($years >= $min) {
                $checks[] = self::item('service', $service_label, self::PASS, "You have served {$years} ".($years === 1 ? 'year.' : 'years.'));
            } else {
                $checks[] = self::item('service', $service_label, self::FAIL, "You have served {$years} ".($years === 1 ? 'year' : 'years').'. Staff with '.($min - 1).' '.(($min - 1) === 1 ? 'year' : 'years').' of service or less are not qualified.');
            }

            if ($a->is_confirmed_staff === null) {
                $checks[] = self::item('confirmed', 'You are a confirmed member of staff', self::PENDING, 'Say whether you are a confirmed member of staff.');
            } elseif ($a->is_confirmed_staff === true) {
                $checks[] = self::item('confirmed', 'You are a confirmed member of staff', self::PASS);
            } else {
                $checks[] = self::item('confirmed', 'You are a confirmed member of staff', self::FAIL, 'Only confirmed members of staff can apply.');
            }
        }

        // 1. staff category
        if (! $a->staff_category) {
            $checks[] = self::item('category', 'Your staff category can apply to this call', self::PENDING, 'Choose your staff category.');
        } elseif ($call->allowsCategory($a->staff_category)) {
            $checks[] = self::item('category', 'Your staff category can apply to this call', self::PASS);
        } else {
            $label = config('conference.staff_categories.'.$a->staff_category, $a->staff_category);
            $checks[] = self::item('category', 'Your staff category can apply to this call', self::FAIL, "This call is not open to {$label}.");
        }

        // 2. in person
        if (! $a->conference_mode) {
            $checks[] = self::item('in_person', "The {$noun} is held in person", self::PENDING, 'Choose in person or online.');
        } elseif ($a->conference_mode === 'in_person') {
            $checks[] = self::item('in_person', "The {$noun} is held in person", self::PASS);
        } else {
            $checks[] = self::item('in_person', "The {$noun} is held in person", self::FAIL, 'Online or virtual events are not supported.');
        }

        // 3. oral presentation (teaching staff only - workshops have no paper)
        if ($a->staff_category === 'teaching') {
            if (! $a->presentation_type) {
                $checks[] = self::item('oral', 'Your paper is presented orally', self::PENDING, 'Choose oral or poster presentation.');
            } elseif ($a->presentation_type === 'oral') {
                $checks[] = self::item('oral', 'Your paper is presented orally', self::PASS);
            } else {
                $checks[] = self::item('oral', 'Your paper is presented orally', self::FAIL, 'Poster presentations are not supported.');
            }
        }

        // 4. start date
        $earliest = $call->earliest_conference_date;
        $start_label = 'Starts on or after '.$earliest->format('F j, Y');
        if (! $a->conference_start_date) {
            $checks[] = self::item('start_date', $start_label, self::PENDING, 'Enter the start date.');
        } elseif ($a->conference_start_date->lt($earliest)) {
            $checks[] = self::item('start_date', $start_label, self::FAIL, 'Your start date is before the earliest eligible date.');
        } else {
            $checks[] = self::item('start_date', $start_label, self::PASS);
        }

        // 5. duration
        $max = $call->max_duration_days;
        $duration_label = "Lasts no more than {$max} ".($max === 1 ? 'day' : 'days');
        if (! $a->conference_start_date || ! $a->conference_end_date) {
            $checks[] = self::item('duration', $duration_label, self::PENDING, 'Enter the start and end dates.');
        } elseif ($a->conference_end_date->lt($a->conference_start_date)) {
            $checks[] = self::item('duration', $duration_label, self::FAIL, 'The end date is before the start date.');
        } else {
            $days = $a->conference_start_date->diffInDays($a->conference_end_date) + 1;
            $checks[] = $days <= $max
                ? self::item('duration', $duration_label, self::PASS, "Your {$noun} lasts {$days} ".($days === 1 ? 'day.' : 'days.'))
                : self::item('duration', $duration_label, self::FAIL, "Your {$noun} lasts {$days} days.");
        }

        // 6. previous beneficiaries must evidence their publications
        if ($a->previously_sponsored) {
            $label = 'Evidence of one Q1 or two Q2 articles from your last TETFund-sponsored conference';
            $a->loadMissing('documents');
            $provided = filled($a->publication_evidence_note) && $a->document('publication_evidence') !== null;

            $checks[] = $provided
                ? self::item('evidence', $label, self::PASS)
                : self::item('evidence', $label, self::PENDING, 'List the articles and upload the evidence in the documents step.');
        }

        return $checks;
    }

    /** True if any rule is definitely broken (not merely unanswered yet). */
    public static function hasFailures(array $checks): bool
    {
        foreach ($checks as $check) {
            if ($check['status'] === self::FAIL) {
                return true;
            }
        }

        return false;
    }

    /** Completed years of service at FUNAAB, worked out from the date of appointment as of today. */
    public static function serviceYears(ConferenceApplication $a): ?int
    {
        return $a->first_appointment_date ? (int) $a->first_appointment_date->diffInYears(now()) : null;
    }

    public static function minYears(): int
    {
        return max(1, (int) config('conference.min_service_years', 3));
    }

    /**
     * The entry criteria: the questions an applicant must answer, and pass, before the
     * rest of the form opens.
     *
     * @return array{answered:bool,passed:bool,reasons:array<int,string>}
     */
    public static function entry(ConferenceApplication $a): array
    {
        $min = self::minYears();
        $years = self::serviceYears($a);
        $reasons = [];

        if ($years !== null && $years < $min) {
            $reasons[] = "You have served FUNAAB for {$years} ".($years === 1 ? 'year' : 'years').'. Staff with '.($min - 1).' '.(($min - 1) === 1 ? 'year' : 'years').' of service or less are not qualified.';
        }

        if ($a->is_confirmed_staff === false) {
            $reasons[] = 'Only confirmed members of staff can apply.';
        }

        $answered = $a->first_appointment_date !== null
            && $a->is_confirmed_staff !== null
            && ($a->is_confirmed_staff === false || $a->confirmation_date !== null);

        return [
            'answered' => $answered,
            'passed' => $answered && empty($reasons),
            'reasons' => $reasons,
        ];
    }

    /**
     * Everything that still stands between a draft and submission.
     * 'ok' is true only when nothing is outstanding.
     *
     * @return array{call_closed:bool,fields:array<string,string>,rules:array,documents:array<int,string>,declaration:bool,ok:bool}
     */
    public static function submissionIssues(ConferenceApplication $a, ConferenceCall $call): array
    {
        $a->loadMissing('documents');

        $progress = self::progress($a);

        // every rule must be met. The evidence rule is reported under documents instead.
        $rules = array_values(array_filter(
            self::checks($a, $call),
            fn ($check) => $check['status'] !== self::PASS && $check['key'] !== 'evidence'
        ));

        $documents = array_map(
            fn ($type) => config('conference.document_types.'.$type.'.label', $type),
            $a->missingDocumentTypes()
        );

        $issues = [
            'call_closed' => ! $call->isOpen(),
            'fields' => $progress['missing'],
            'rules' => $rules,
            'documents' => $documents,
            'declaration' => ! $a->declaration_accepted,
        ];

        $issues['ok'] = ! $issues['call_closed']
            && empty($issues['fields'])
            && empty($issues['rules'])
            && empty($issues['documents'])
            && ! $issues['declaration'];

        return $issues;
    }

    /**
     * Fields the form needs before it can be submitted: field name => label.
     */
    public static function requiredFields(ConferenceApplication $a): array
    {
        $fields = [
            'funaab_email' => 'FUNAAB email',
            'candidate_name' => 'Name of candidate',
            'gender' => 'Gender',
            'date_of_birth' => 'Date of birth',
            'staff_category' => 'Staff category',
            'phone' => 'Phone number',
            'department' => 'Department',
            'highest_qualification' => 'Highest qualification',
            'area_of_specialisation' => 'Area of specialisation',
            'staff_rank' => 'Rank',
            'grade_level' => 'Grade level',
            'first_appointment_date' => 'Date of first appointment',
            'work_experience_years' => 'Duration of entire work experience',
            'years_in_institution' => 'Years spent in the institution',
            'conference_organiser' => 'Conference organiser(s)',
            'conference_title' => 'Title of the conference',
            'conference_venue' => 'Venue',
            'conference_scope' => 'Local or overseas',
            'conference_mode' => 'In person or online',
            'conference_start_date' => 'Start date',
            'conference_end_date' => 'End date',
            'registration_fee' => 'Registration fee',
            'bank_name' => 'Bank name',
            'bank_branch' => 'Bank branch',
            'account_name' => 'Account name',
            'account_number' => 'Account number',
            'sort_code' => 'Sort code',
        ];

        $fields['is_confirmed_staff'] = 'Confirmed staff (Yes or No)';

        if ($a->is_confirmed_staff) {
            $fields['confirmation_date'] = 'Date of confirmation';
        }

        if ($a->staff_category === 'teaching') {
            $fields['presentation_type'] = 'Presentation type';
        }

        if ($a->previously_sponsored) {
            $fields += [
                'last_conference_title' => 'Title of last TETFund-sponsored conference',
                'last_conference_date' => 'Date of last TETFund-sponsored conference',
                'last_conference_venue' => 'Venue of last TETFund-sponsored conference',
                'last_conference_publication' => 'Publication from that conference',
                'publication_evidence_note' => 'Q1 / Q2 articles offered as evidence',
            ];
        }

        return $fields;
    }

    /** @return array{filled:int,total:int,percent:int,missing:array<string,string>} */
    public static function progress(ConferenceApplication $a): array
    {
        $required = self::requiredFields($a);
        $missing = [];

        foreach ($required as $field => $label) {
            // encrypted fields are checked without decrypting them
            $value = in_array($field, ['account_number', 'sort_code'], true)
                ? ($a->hasStoredSecret($field) ? 'stored' : null)
                : $a->{$field};

            if ($value === null || $value === '') {
                $missing[$field] = $label;
            }
        }

        $total = count($required);
        $filled = $total - count($missing);

        return [
            'filled' => $filled,
            'total' => $total,
            'percent' => $total > 0 ? (int) round($filled / $total * 100) : 0,
            'missing' => $missing,
        ];
    }

    private static function item(string $key, string $label, string $status, ?string $message = null): array
    {
        return ['key' => $key, 'label' => $label, 'status' => $status, 'message' => $message];
    }
}
