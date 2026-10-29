<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Department extends Model
{
    use HasFactory;

    protected $fillable = [
        'directorate_id',
        'name',
        'code'
    ];

    public function directorate(){
        return $this->belongsTo(Directorate::class, 'directorate_id', 'id');
    }

    public function staff()
    {
        return $this->hasMany(Staff::class, 'department_id', 'id');
    }

   
}
