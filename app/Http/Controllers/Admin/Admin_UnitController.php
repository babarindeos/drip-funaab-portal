<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Unit;
use App\Models\Section;
use App\Models\Segment;
use App\Models\SegmentOrgan;
use Illuminate\Support\Facades\DB;


class Admin_UnitController extends Controller
{
    //
    public function index()
    {
        $units = Unit::orderBy('name', 'desc')->paginate(10);
        return view('admin.units.index', compact('units'));
    }


    public function create()
    {
        $sections = Section::orderBy('name', 'asc')->get();
        return view('admin.units.create', compact('sections'));
    }


    public function store(Request $request)
    {
       
        $formFields = $request->validate([
            'section' => ['required', 'string'],
            'name' => ['required', 'string', 'unique:units,name'],
            'code' => ['required', 'string', 'unique:units,code']
        ]);

        $formFields['section_id'] = $formFields['section'];

        DB::beginTransaction();

        try
        {
            $create = Unit::create($formFields);

            if ($create)
            {
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'Unit has been successfully created'
                ];

                $current_segment = Segment::findOrFail(6);

                $segment_organs_data = [
                    'segment_id' => $current_segment->id,
                    'organ_id' => $create->id
                ];

                SegmentOrgan::create($segment_organs_data);
            }
            else
            {
                /* $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred creating the Unit'
                ]; */

                throw new \Exception("fatal error creating the Unit");
            }

            DB::commit();
            
        }
        catch(\Exception $e)
        {
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'An error occurred'.$e->getMessage()
                ];

                DB::rollBack();
        }
        
        return redirect()->back()->with($data);
    }


    
    public function edit(Unit $unit)
    {
        $sections = Section::orderBy('name', 'asc')->get();        

        return view('admin.units.edit', ['sections' => $sections, 'unit' => $unit]);
    }


    public function update(Request $request, Unit $unit)
    {
        $formFields = $request->validate([
            'section' => ['required', 'string'],
            'name' => ['required', 'string'],
            'code' => ['required', 'string'],
        ]);

        $formFields['section_id'] = $formFields['section'];

        try
        {
            $update = $unit->update($formFields);

            if ($update)
            {
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'Unit has been successfully updated'
                ];
            }
            else
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred updating the Unit'
                ];
            }
        }
        catch(\Exception $e)
        {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred '.$e->getMessage()
                ];
        }
        
        return redirect()->back()->with($data);

    }


    public function confirm_delete(Unit $unit)
    {
        if ($unit == null)
        {
            return redirect()->back();
        }

        return view('admin.units.confirm_delete', compact('unit'));
    }

    public function destroy()
    {

    }

}
