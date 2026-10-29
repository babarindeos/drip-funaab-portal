<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Directorate extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'code'];

    public function staff()
    {
        return $this->hasMany(Staff::class, 'directorate_id', 'id');
    }

    public function department()
    {
        return $this->hasMany(Department::class, 'directorate_id', 'id');
    }
}
