<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\College;
use App\Models\Dean;
use App\Models\Staff;
use Illuminate\Support\Facades\Session;

class Admin_DeanController extends Controller
{
    //
    public function index()
    {
        return view('admin.deans.index');
    }

    public function create()
    {
        $colleges = College::orderBy('college_name', 'asc')->get();
        
        $current_dean = null;

        //Session::forget('isPostBack');

        return view('admin.deans.create', compact('colleges', 'current_dean'));
    }

    public function get_assigned_dean(Request $request)
    {
        $formFields = $request->validate([
            'college' => 'required|exists:colleges,id'
        ]);        
        
        //$request->session()->put('isPostBack', true);
        //Session::put('isPostBack', 'Babarinde');
       
        return redirect()->route('admin.deans.assign_dean')->with(['college_id' => $request->input('college')]);
    }


    public function assign_dean()
    {
        $college_id = Session::get('college_id');
        if (Session::has('college_id'))
        {
            $college = College::find($college_id);
            
            $current_dean = Dean::where('college_id', $college_id)
                              ->where('current', true)->first();
            
            $college_staff = Staff::where('college_id', $college_id)->orderBy('surname', 'asc')->get();

            return view('admin.deans.assign_dean')->with(['college' => $college, 'current_dean' => $current_dean, 'staff' => $college_staff]);

        }
        else
        {
            $data = [
                'error' => true,
                'status' => 'fail',
                'message' => 'The College is not defined'
            ];

            return redirect()->route('admin.deans.create')->with($data);
        }
       
        
    }


    public function store_assign_dean(Request $request)
    {
        dd($request);
        $formFields = $request->validate([


        ]);
    }
}

