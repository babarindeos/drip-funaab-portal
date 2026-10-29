<?php

namespace App\Models\Conference;

use App\Models\User;

class ConferenceApplicationEvent extends ConferenceModel
{
    protected $table = 'conference_application_events';

    // the audit trail is write-once, so there is no updated_at column
    const UPDATED_AT = null;

    protected $fillable = ['conference_application_id', 'user_id', 'event', 'note'];

    public function application()
    {
        return $this->belongsTo(ConferenceApplication::class, 'conference_application_id');
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
}
