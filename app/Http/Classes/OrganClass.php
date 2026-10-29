<?php

namespace App\Http\Classes;

use App\Http\Interfaces\OrganInterface;
use App\Models\Segment;
use App\Models\SegmentOrgan;

use App\Models\Directorate;
use App\Models\Department;
use App\Models\Division;
use App\Models\Branch;
use App\Models\Section;
use App\Models\Unit;

use App\Models\Staff;

class OrganClass implements OrganInterface
{
    public static function getOrganBySegment(Staff $staff)
    {
        $segment = $staff->segment_id;

        switch($segment)
        {
            case 1:
                $organ = Directorate::find($staff->organ_id);
                break;
            case 2:
                $organ = Department::find($staff->organ_id);
                break;
            case 3:
                $organ = Division::find($staff->organ_id);
                break;
            case 4:
                $organ = Branch::find($staff->organ_id);
                break;
            case 5:
                $organ = Section::find($staff->organ_id);
                break;
            case 6:
                $organ = Unit::find($staff->organ_id);
                break;
        }
        
        return $organ;

    }
}