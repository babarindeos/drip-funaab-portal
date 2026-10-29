<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrivateMessage extends Model
{
    use HasFactory;

    protected $fillable = [
        'doc_id',
        'sender_id',
        'recipient_id',
        'chat_users_id',
        'chat_uuid',
        'message'
    ];

    public function document()
    {
        return $this->belongsTo(Document::class, 'doc_id', 'id');
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id', 'id');
    }

    public function recipient()
    {
        return $this->belongsTo(User::class, 'recipient_id', 'id');
    }
}
