<?php

namespace App\Models\Conference;

use App\Models\User;

class ConferenceApplication extends ConferenceModel
{
    protected $table = 'conference_applications';

    const STATUS_DRAFT = 'draft';
    const STATUS_SUBMITTED = 'submitted';
    const STATUS_APPROVED = 'approved';
    const STATUS_NOT_APPROVED = 'not_approved';

    const STATUS_LABELS = [
        self::STATUS_DRAFT => 'Draft',
        self::STATUS_SUBMITTED => 'Submitted',
        self::STATUS_APPROVED => 'Approved',
        self::STATUS_NOT_APPROVED => 'Not approved',
    ];

    /**
     * Only the fields an applicant fills in. Status, decision and ownership
     * fields are always set explicitly by the code, never from a form.
     */
    protected $fillable = [
        'funaab_email',
        // I - vii
        'institution', 'candidate_name', 'gender', 'date_of_birth', 'present_position',
        'staff_category', 'phone', 'passport_photo',
        // 1 - 7
        'department', 'highest_qualification', 'area_of_specialisation', 'staff_rank', 'grade_level',
        'first_appointment_date', 'work_experience_years', 'years_in_institution',
        // 8
        'conference_organiser', 'conference_title', 'conference_venue', 'conference_scope',
        'conference_mode', 'presentation_type', 'conference_website', 'conference_email',
        'conference_start_date', 'conference_end_date', 'duration_days',
        'cost_currency', 'registration_fee', 'flight_cost', 'accommodation_cost', 'feeding_cost', 'excursion_cost',
        // 9
        'previously_sponsored', 'last_conference_title', 'last_conference_date', 'last_conference_venue',
        'last_conference_publication', 'publication_evidence_note',
        // 10
        'bank_name', 'bank_branch', 'account_name', 'account_number', 'sort_code',
        // declaration
        'declaration_accepted',
        // eligibility
        'is_confirmed_staff', 'confirmation_date',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
        'declared_at' => 'datetime',
        'decided_at' => 'datetime',
        'date_of_birth' => 'date',
        'first_appointment_date' => 'date',
        'conference_start_date' => 'date',
        'conference_end_date' => 'date',
        'last_conference_date' => 'date',
        'previously_sponsored' => 'boolean',
        'declaration_accepted' => 'boolean',
        'is_confirmed_staff' => 'boolean',
        'confirmation_date' => 'date',
        'decision_remark_shared' => 'boolean',
        'work_experience_years' => 'integer',
        'years_in_institution' => 'integer',
        'registration_fee' => 'decimal:2',
        'flight_cost' => 'decimal:2',
        'accommodation_cost' => 'decimal:2',
        'feeding_cost' => 'decimal:2',
        'excursion_cost' => 'decimal:2',
        // encrypted at rest; decrypted automatically when read
        'account_number' => 'encrypted',
        'sort_code' => 'encrypted',
    ];

    /** Never include the bank details when a record is converted to an array / JSON. */
    protected $hidden = ['account_number', 'sort_code'];

    public function call()
    {
        return $this->belongsTo(ConferenceCall::class, 'conference_call_id');
    }

    public function documents()
    {
        return $this->hasMany(ConferenceApplicationDocument::class, 'conference_application_id');
    }

    public function events()
    {
        return $this->hasMany(ConferenceApplicationEvent::class, 'conference_application_id')->orderBy('created_at');
    }

    /** The applicant's portal account (in the main database). */
    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function scopeForUser($query, $user_id)
    {
        return $query->where('user_id', $user_id);
    }

    public function isEditable(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? ucfirst((string) $this->status);
    }

    /** The memo calls it a "Conference" for academic staff and a "Workshop" for non-teaching staff. */
    public function getEventNounAttribute(): string
    {
        return $this->staff_category === 'non_teaching' ? 'Workshop' : 'Conference';
    }

    /** Account number with all but the last four digits hidden, for lists. */
    public function getMaskedAccountNumberAttribute(): string
    {
        try {
            $number = (string) $this->account_number;
        } catch (\Throwable $e) {
            return '(cannot be read)'; // e.g. APP_KEY changed since it was saved
        }

        if ($number === '') {
            return '';
        }

        return str_repeat('*', max(strlen($number) - 4, 0)).substr($number, -4);
    }

    /** An encrypted field's real value, or a note if it cannot be decrypted (e.g. APP_KEY changed). */
    public function secret(string $field): string
    {
        try {
            return (string) $this->{$field};
        } catch (\Throwable $e) {
            return '(cannot be read)';
        }
    }

    /** Whether a value is stored, checked WITHOUT decrypting it. */
    public function hasStoredSecret(string $field): bool
    {
        $raw = $this->getRawOriginal($field);

        return $raw !== null && $raw !== '';
    }

    /** Sum of every cost the applicant entered, in the application's currency. */
    public function getTotalCostAttribute(): float
    {
        return (float) $this->registration_fee
             + (float) $this->flight_cost
             + (float) $this->accommodation_cost
             + (float) $this->feeding_cost
             + (float) $this->excursion_cost;
    }

    /**
     * The document types this applicant must provide, from the memo:
     * invitation letter and announcement for everyone, acceptance letter and
     * abstract for teaching staff, publication evidence for previous
     * beneficiaries, plus the signed nomination form.
     */
    public function requiredDocumentTypes(): array
    {
        $required = [];

        foreach (config('conference.document_types') as $type => $definition) {
            $needed = match ($definition['required']) {
                'all' => true,
                'teaching' => $this->staff_category === 'teaching',
                'previous_beneficiary' => (bool) $this->previously_sponsored,
                'confirmed' => $this->is_confirmed_staff === true,
                default => false,
            };

            if ($needed) {
                $required[] = $type;
            }
        }

        return $required;
    }

    public function document(string $type): ?ConferenceApplicationDocument
    {
        return $this->documents->firstWhere('type', $type);
    }

    /** Required document types that have not been uploaded yet. */
    public function missingDocumentTypes(): array
    {
        $this->loadMissing('documents');

        return array_values(array_filter(
            $this->requiredDocumentTypes(),
            fn ($type) => $this->document($type) === null
        ));
    }

    public function recordEvent(string $event, ?int $user_id = null, ?string $note = null): ConferenceApplicationEvent
    {
        return $this->events()->create([
            'user_id' => $user_id,
            'event' => $event,
            'note' => $note,
        ]);
    }
}
