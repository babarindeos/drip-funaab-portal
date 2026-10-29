<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\College;

class Admin_CollegeController extends Controller
{
    //
    public function index(){
        $colleges = College::orderBy('id', 'desc')->get();
        return view('admin.college.index', compact('colleges'));
    }

    public function create(){
        return view('admin.college.create');
    }

    public function store(Request $request){
        $formFields = $request->validate([
            'college_name' => 'required | string',
            'college_code' => 'required | string'
        ]);

        $college_name = $request->input('college_name');
        $college_code = strtoupper($request->input('college_code'));

        $isCollegeExist = College::where('college_name',$college_name)
                                    ->where('college_code', $college_code)->exists();
        
        if ($isCollegeExist){
       
            $data = [
                'error' => true,
                'status' => 'fail',
                'message' => 'A College with that name already exist'
            ];
            return redirect()->back()->with($data)->withInput();
        }

        $create = College::create($formFields);

        if (!$create){
            $data = [
                'error' => true,
                'status' => 'fail',
                'message' => 'An error occurred creating the College'
            ];

            return redirect()->back()->with($data);
        }

            $data = [
                'error' => true,
                'status' => 'success',
                'message' => 'The College has been successfully created'
            ];

            return redirect()->back()->with($data);

    }



    public function edit(Request $request, College $college)
    {
        return view('admin.college.edit', compact('college'));
    }

    public function update(Request $request, College $college)
    {
        $formFields = $request->validate([
            'college_name' => 'required | string',
            'college_code' => ['required', 'string']
        ]);

        $update = $college->update($formFields);

        if ($update){
            $data = [
                'error' => true,
                'status' => 'success',
                'message' => 'Record has been succesfully updated'
            ];
        }else{
            $data = [
                'error' => true,
                'status' => 'fail',
                'message' => 'An error occurred updating the record'
            ];
        }

        return redirect()->back()->with($data);
    }

    public function destroy(Request $request, College $college){

    }
}
