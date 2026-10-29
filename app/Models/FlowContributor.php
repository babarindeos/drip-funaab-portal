<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlowContributor extends Model
{
    use HasFactory;

    protected $fillable = [
        'doc_id',
        'user_id',
        'added_by'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function add_initiator()
    {
        return $this->belongsTo(User::class, 'added_by', 'id');
    }

    public function document()
    {
        return $this->belongsTo(Document::class, 'doc_id', 'id');
    }
}
