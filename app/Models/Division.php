<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Division extends Model
{
    use HasFactory;

    protected $fillable = ['department_id', 'name', 'code'];

    public function Department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }
}
