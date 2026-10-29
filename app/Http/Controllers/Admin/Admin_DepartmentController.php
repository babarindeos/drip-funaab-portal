<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\College;
use App\Models\Department;
use App\Models\Ministry;
use App\Models\Directorate;
use App\Models\Segment;
use App\Models\SegmentOrgan;
use Illuminate\Support\Facades\DB;

class Admin_DepartmentController extends Controller
{
    //
    public function index(){
        $departments = Department::orderBy('name', 'asc')->paginate(2);
        return view('admin.departments.index', compact('departments'));
    }

    public function create(){
        
        $directorates = Directorate::orderBy('name', 'asc')->get();
        return view('admin.departments.create', compact('directorates'));
    }

    public function store(Request $request){
        
        
        $formFields = $request->validate([
            'directorate' => 'required',
            'name' => 'required|string|unique:departments,name',
            'code' => ['required', 'string', 'unique:departments,code']
        ]);

        $formFields['directorate_id'] = $formFields['directorate'];        

        DB::beginTransaction();

        try{

            $create = Department::create($formFields);

            if ($create){
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'The Department has been successfully created'
                ];

                $current_segment = Segment::findOrFail(2);

                $segment_organs_data = [
                    'segment_id' => $current_segment->id,
                    'organ_id' => $create->id
                ];

                SegmentOrgan::create($segment_organs_data);

            }else{
                /* $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred creating the Department'
                ]; */

                throw new \Exception("fatal error creating Department");
            }
    

            DB::commit();

        }catch(\Exception $e)
        {
            $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred creating the Department'.$e->getMessage()
            ];

            DB::rollBack();
        }
        
        return redirect()->back()->with($data);
        
    }


    public function edit(Department $department){
        //$colleges = College::orderBy('college_name', 'asc')->get();
        $directorates = Directorate::orderBy('name', 'asc')->get();
        

        return view('admin.departments.edit', compact('directorates', 'department'));
    }

    public function update(Request $request, Department $department){
        $formFields = $request->validate([
            'directorate' => 'required',
            'name' => ['required', 'string'],
            'code' => 'required | string'
        ]);

        $formFields['directorate_id'] = $formFields['directorate'];

        try{
            $update = $department->update($formFields);

            if ($update){
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'The Department has been successfully updated'
                ];
            }else{
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred updating the Department'
                ];
            }
        }catch(\Exception $e){
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred '.$e->getMessage()
                ];
        }

        return redirect()->back()->with($data);
        
    }


    public function confirm_delete(Department $department)
    {
        
        return view('admin.departments.confirm_delete', compact('department'));

    }

    public function destroy(Request $request, Department $department)
    {

    }

}
