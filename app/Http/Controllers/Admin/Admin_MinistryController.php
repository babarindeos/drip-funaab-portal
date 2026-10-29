<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Ministry;
use App\Models\Department;
use Illuminate\Validation\Rule;

class Admin_MinistryController extends Controller
{

    //
    public function index()
    {
        $ministries = Ministry::orderBy('name','asc')->orderBy('code','asc')->paginate(2);
        return view('admin.ministries.index', compact('ministries'));
    }

    public function create()
    {
        return view('admin.ministries.create');
    }


    public function store(Request $request)
    {
        $formFields = $request->validate([
            'name' => ['required', 'string'],
            'code' => ['required', 'string']
        ]);

        $ministry_exist = Ministry::where('name', $request->input('name'))
                                    ->Orwhere('code', $request->input('code'))
                                    ->exists();
        
        if ($ministry_exist == false)
        {
            $create = Ministry::create($formFields);

            if ($create)
            {
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'Ministry has been successfully created'
                ];
            }
            else
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred creating the Ministry. '
                ];
            }
        }
        else
        {
            $data = [
                'error' => true,
                'status' => 'fail',
                'message' => 'A Ministry with that name or code already exist'
            ];
        }

        return redirect()->back()->with($data);
    }

    public function show(Ministry $ministry)
    {
        $departments = Department::where('ministry_id', $ministry->id)->paginate(2);
        
        return view('admin.ministries.show', compact('ministry','departments'));
    }


    public function edit(Ministry $ministry)
    {
        return view('admin.ministries.edit', compact('ministry'));
    }

    public function update(Request $request, Ministry $ministry)
    {
        $formFields = $request->validate([
            'name' => 'required | string',
            'code' => 'required | string'
        ]);

        try
        {
            $update = $ministry->update($formFields);

            if ($update)
            {
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'The Ministry has been successfully updated'
                ];
            }
            else
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred updating the Ministry'
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

    public function destroy(Ministry $ministry)
    {
        return view('admin.ministries.destroy', compact('ministry'));

    }

    public function confirm_delete(Request $request, Ministry $ministry)
    {

    }




}
