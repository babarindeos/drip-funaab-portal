<?php

namespace App\Models\Conference;

class ConferenceApplicationDocument extends ConferenceModel
{
    protected $table = 'conference_application_documents';

    protected $fillable = [
        'conference_application_id', 'type', 'path', 'original_name', 'mime', 'size',
    ];

    public function application()
    {
        return $this->belongsTo(ConferenceApplication::class, 'conference_application_id');
    }

    public function getLabelAttribute(): string
    {
        return config('conference.document_types.'.$this->type.'.label', ucfirst(str_replace('_', ' ', $this->type)));
    }
}
