<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Office;

class Admin_OfficeController extends Controller
{
    //
    public function index()
    {
        $offices = Office::orderBy('id','asc')->paginate(2);
        return view('admin.offices.index', compact('offices'));
    }

    public function create()
    {
        $offices = Office::orderBy('id','asc')->get();
        return view('admin.offices.create');
    }


    public function store(Request $request)
    {
        $formFields = $request->validate([
            'name' => 'required|string|unique:offices,name'
        ]);


        $formFields['parent_id'] = $request->input('parent');
       
        DB::beginTransaction();

            try
            {
                    $create = Office::create($formFields);

                    if ($create)
                    {
                            $data = [
                                'error' => true,
                                'status' => 'success',
                                'message' => 'Office has been successfully created'
                            ];
                    }
                    else
                    {
                        /* $data = [
                            'error' => true,
                            'status' => 'fail',
                            'message' => 'An error occurred creating the Directorate. '
                        ]; */
                        throw new \Exception("fatal creation of Office");
                    }

                    DB::commit();
            }
            catch(\Exception $e)
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred creating the Office '.$e->getMessage()
                ];

                DB::rollBack();
            }        
        
        

            return redirect()->back()->with($data);
    }

    public function show(Office $office)
    {
        return view('admin.offices.show', compact('office'));
    }


    public function edit(Office $office)
    {
        $offices = Office::orderBy('name', 'asc')->get();
        return view('admin.offices.edit', compact('office', 'offices'));
    }

    public function update(Request $request, Office $office)
    {
        $formFields = $request->validate([
            'name' => 'required | string'
        ]);

        
        try
        {
            $formFields['parent_id'] =  $request->parent;

            $update = $office->update($formFields);

            if ($update)
            {
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'The Office has been successfully updated'
                ];
            }
            else
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred updating the Office'
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

    public function confirm_delete(Office $office)
    {
        return view('admin.offices.confirm_delete', compact('office'));

    }

    public function destroy(Request $request, Office $office)
    {

    }

}
