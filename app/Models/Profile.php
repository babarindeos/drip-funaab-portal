<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'designation',
        'phone',
        'avatar',
        'bio'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function staff()
    {
        return $this->belongsTo(Staff::class, 'user_id', 'user_id');
    }
}
