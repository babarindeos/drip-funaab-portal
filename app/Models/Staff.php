<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Staff extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'office_id',
        'title',
        'surname',
        'firstname',
        'middlename',
        'fileno'
    ];


    public function segment()
    {
        return $this->belongsTo(Segment::class, 'segment_id', 'id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id', 'id');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function profile()
    {
        return $this->hasOne(Profile::class, 'user_id', 'user_id');
    }

    public function office()
    {
        return $this->belongsTo(Office::class, 'office_id', 'id');
    }

    public function organ()
    {            

        $organ = '';
        $organ_item = '';

        //dd($this->segment_id);

        switch ($this->segment_id)
        {
            case 1:
                $organ = "Directorate";
                $organ_item = Directorate::where('id', $this->organ_id)->first();
                break;
            case 2:
                $organ = "Department";
                $organ_item = Department::where('id', $this->organ_id)->first();
                break;
            case 3:
                $organ = "Division";
                $organ_item = Division::where('id', $this->organ_id)->first();
                break;
            case 4:
                $organ = "Branch";
                $organ_item = Branch::where('id', $this->organ_id)->first();
                break;                
            case 5:
                $organ = "Section";
                $organ_item = Section::where('id', $this->organ_id)->first();
                break;
            case 6:
                $organ = "Unit";
                $organ_item = Unit::where('id', $this->organ_id)->first();
        }

        return $organ_item;

    }
}
