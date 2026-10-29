<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Division;
use App\Models\Department;
use App\Models\Segment;
use App\Models\SegmentOrgan;
use Illuminate\Support\Facades\DB;

class Admin_DivisionController extends Controller
{
    //
    public function index()
    {
        $divisions = Division::orderBy('id', 'desc')->paginate(5);
        return view('admin.divisions.index', compact('divisions'));
    }

    public function create()
    {
        $departments = Department::orderBy('name', 'desc')->get();
        return view('admin.divisions.create')->with('departments', $departments);
    }

    public function store(Request $request)
    {
        $formFields = $request->validate([
            'department' => ['required', 'string'],
            'name' => ['required', 'string', 'unique:divisions,name'],
            'code' => ['required', 'string', 'unique:divisions,code']
        ]);

        $formFields['department_id'] = $formFields['department'];

        DB::beginTransaction();

        try
        {
            $create = Division::create($formFields);

            if ($create)
            {
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'Division has been successfully created'
                ];

                $current_segment = Segment::findOrFail(3);

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
                    'message' => 'An error occurred creating the Division'
                ]; */

                throw new \Exception("fatal error creating Division");
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

    public function edit(Division $division)
    {
        $departments = Department::orderBy('name', 'asc')->get();

        return view('admin.divisions.edit', ['departments' => $departments, 'division' => $division]);
    }

    public function update(Request $request, Division $division)
    {
        $formFields = $request->validate([
            'department' => ['required', 'string'],
            'name' => ['required', 'string'],
            'code' => ['required', 'string'],
        ]);

        $formFields['department_id'] = $formFields['department'];

        try
        {
            $update = $division->update($formFields);

            if ($update)
            {
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'Division has been successfully updated'
                ];
            }
            else
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred updating the Division'
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

    public function confirm_delete(Division $division)
    {
        if ($division == null)
        {
            return redirect()->back();
        }

        return view('admin.divisions.confirm_delete', compact('division'));
    }

    public function destroy()
    {

    }


}
