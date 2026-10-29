<?php

namespace App\Models\Conference;

class ConferenceCall extends ConferenceModel
{
    protected $table = 'conference_calls';

    const STATUS_DRAFT = 'draft';
    const STATUS_ARCHIVED = 'archived';

    protected $fillable = [
        'uuid', 'title', 'description', 'open_date', 'close_date', 'earliest_conference_date',
        'max_duration_days', 'allow_teaching', 'allow_non_teaching', 'advert',
        'approval_instructions', 'status', 'created_by',
    ];

    protected $casts = [
        'open_date' => 'date',
        'close_date' => 'date',
        'earliest_conference_date' => 'date',
        'max_duration_days' => 'integer',
        'allow_teaching' => 'boolean',
        'allow_non_teaching' => 'boolean',
    ];

    public function applications()
    {
        return $this->hasMany(ConferenceApplication::class, 'conference_call_id');
    }

    /**
     * Draft / Archived when set by an admin, otherwise worked out from the
     * dates (same behaviour as the Call for Proposals module).
     */
    public function getComputedStatusAttribute(): string
    {
        if ($this->status === self::STATUS_DRAFT) {
            return 'Draft';
        }

        if ($this->status === self::STATUS_ARCHIVED) {
            return 'Archived';
        }

        $today = now()->startOfDay();

        if ($today->lt($this->open_date)) {
            return 'Upcoming';
        }

        if ($today->gt($this->close_date)) {
            return 'Closed';
        }

        return 'Open';
    }

    public function isOpen(): bool
    {
        return $this->computed_status === 'Open';
    }

    /** Calls that staff and the public may see: published ones only (not draft, not archived). */
    public function scopeVisible($query)
    {
        return $query->whereNull('status');
    }

    /**
     * Filter by the same status the accessor above works out, but in the
     * database, so lists can be searched, filtered and paginated.
     * $label is one of: open, upcoming, closed, draft, archived.
     */
    public function scopeComputedStatus($query, string $label)
    {
        $today = now()->toDateString();

        if ($label === 'draft') {
            return $query->where('status', self::STATUS_DRAFT);
        }

        if ($label === 'archived') {
            return $query->where('status', self::STATUS_ARCHIVED);
        }

        // open / upcoming / closed only apply to calls that are published
        $query->where(function ($q) {
            $q->whereNull('status')->orWhereNotIn('status', [self::STATUS_DRAFT, self::STATUS_ARCHIVED]);
        });

        return match ($label) {
            'open' => $query->whereDate('open_date', '<=', $today)->whereDate('close_date', '>=', $today),
            'upcoming' => $query->whereDate('open_date', '>', $today),
            'closed' => $query->whereDate('close_date', '<', $today),
            default => $query,
        };
    }

    /** Whether this call accepts applications from the given staff category. */
    public function allowsCategory(?string $category): bool
    {
        return match ($category) {
            'teaching' => $this->allow_teaching,
            'non_teaching' => $this->allow_non_teaching,
            default => false,
        };
    }
}
