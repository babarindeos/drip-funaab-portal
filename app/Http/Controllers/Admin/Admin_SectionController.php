<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Section;
use App\Models\Branch;
use App\Models\Segment;
use App\Models\SegmentOrgan;
use Illuminate\Support\Facades\DB;

class Admin_SectionController extends Controller
{
    //
    public function index()
    {
        $sections = Section::orderBy('name', 'desc')->paginate(10);
        return view('admin.sections.index', compact('sections'));
    }


    public function create()
    {
        $branches = Branch::orderBy('name', 'asc')->get();
        return view('admin.sections.create', compact('branches'));
    }


    public function store(Request $request)
    {
        $formFields = $request->validate([
            'branch' => ['required', 'string'],
            'name' => ['required', 'string', 'unique:sections,name'],
            'code' => ['required', 'string', 'unique:sections,code']
        ]);

        $formFields['branch_id'] = $formFields['branch'];

        DB::beginTransaction();

        try
        {
            $create = Section::create($formFields);

            if ($create)
            {
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'Section has been successfully created'
                ];

                $current_segment = Segment::findOrFail(5);

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
                    'message' => 'An error occurred creating the Section'
                ]; */

                throw new \Exception("fatal error creating the Section");
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


    
    public function edit(Section $section)
    {
        $branches = Branch::orderBy('name', 'asc')->get();        

        return view('admin.sections.edit', ['branches' => $branches, 'section' => $section]);
    }


    public function update(Request $request, Section $section)
    {
        $formFields = $request->validate([
            'branch' => ['required', 'string'],
            'name' => ['required', 'string'],
            'code' => ['required', 'string'],
        ]);

        $formFields['branch_id'] = $formFields['branch'];

        try
        {
            $update = $section->update($formFields);

            if ($update)
            {
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'Section has been successfully updated'
                ];
            }
            else
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred updating the Section'
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


    public function confirm_delete(Section $section)
    {
        if ($section == null)
        {
            return redirect()->back();
        }

        return view('admin.sections.confirm_delete', compact('section'));
    }

    public function destroy()
    {

    }

}
