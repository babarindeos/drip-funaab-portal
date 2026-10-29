<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Unit extends Model
{
    use HasFactory;

    protected $fillable = ['section_id', 'name', 'code'];

    public function section()
    {
        return $this->belongsTo(Section::class, 'section_id', 'id');
    }
}
