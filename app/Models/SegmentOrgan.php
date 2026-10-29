<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SegmentOrgan extends Model
{
    use HasFactory;

    protected $fillable = [ 'segment_id', 'organ_id'];
}
