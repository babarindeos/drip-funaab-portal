<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Directorate;
use App\Models\Department;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\Segment;
use App\Models\SegmentOrgan;
class Admin_DirectorateController extends Controller
{

    //
    public function index()
    {
        $directorates = Directorate::orderBy('name','asc')->orderBy('code','asc')->paginate(2);
        return view('admin.directorates.index', compact('directorates'));
    }

    public function create()
    {
        return view('admin.directorates.create');
    }


    public function store(Request $request)
    {
        $formFields = $request->validate([
            'name' => ['required', 'string'],
            'code' => ['required', 'string']
        ]);

        $directorate_exist = Directorate::where('name', $request->input('name'))
                                    ->Orwhere('code', $request->input('code'))
                                    ->exists();
        
        if ($directorate_exist == false)
        {
            DB::beginTransaction();

            try
            {
                $create = Directorate::create($formFields);

                if ($create)
                {
                    $data = [
                        'error' => true,
                        'status' => 'success',
                        'message' => 'Directorate has been successfully created'
                    ];

                    $current_segment = Segment::findOrFail(1);

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
                        'message' => 'An error occurred creating the Directorate. '
                    ]; */
                    throw new \Exception("fatal creation of Directorate");
                }

                DB::commit();
            }
            catch(\Exception $e)
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred creating the Directorate '.$e->getMessage()
                ];

                DB::rollBack();
            }
            
        }
        else
        {
            $data = [
                'error' => true,
                'status' => 'fail',
                'message' => 'A Directorate with that name or code already exist'
            ];
        }

        return redirect()->back()->with($data);
    }

    public function show(Directorate $directorate)
    {
        $departments = Department::where('directorate_id', $directorate->id)->paginate(2);
        
        return view('admin.directorates.show', compact('directorate','departments'));
    }


    public function edit(Directorate $directorate)
    {
        return view('admin.directorates.edit', compact('directorate'));
    }

    public function update(Request $request, Directorate $directorate)
    {
        $formFields = $request->validate([
            'name' => 'required | string',
            'code' => 'required | string'
        ]);

        try
        {
            $update = $directorate->update($formFields);

            if ($update)
            {
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'The Directorate has been successfully updated'
                ];
            }
            else
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred updating the Directorate'
                ];
            }
        }
        catch(\Exception $e)
        {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred updating the record: '.$e->getMessage()
                ];
        }

        return redirect()->back()->with($data);
        
    }

    public function confirm_delete(Directorate $directorate)
    {
        return view('admin.directorates.confirm_delete', compact('directorate'));

    }

    public function destroy(Request $request, Directorate $directorate)
    {

    }




}
