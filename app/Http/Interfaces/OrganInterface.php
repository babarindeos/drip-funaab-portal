<?php

namespace App\Http\Interfaces;

use App\Models\Staff;

interface OrganInterface
{
    public static function getOrganBySegment(Staff $staff);
}