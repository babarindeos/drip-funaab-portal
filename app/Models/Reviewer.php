<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reviewer extends Model
{
    use HasFactory;

    /** The proposals this reviewer has been sent. */
    public function assignments()
    {
        return $this->hasMany(ProposalReviewers::class, 'reviewer_id', 'id');
    }
}
