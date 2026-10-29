<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\College;
use App\Models\Department;
use App\Models\Staff;
use App\Models\Ministry;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Directorate;
use App\Models\Division;
use App\Models\Branch;
use App\Models\Section;
use App\Models\Unit;
use Illuminate\Support\Facades\DB;
use App\Models\Segment;
use App\Models\Office;

use App\Mail\NewUserEmail;

use Mail;



class Admin_StaffController extends Controller
{
    //
    public function index(){
       
        $staffs = Staff::orderBy('surname', 'asc')
                        ->orderBy('firstname', 'asc')
                        ->orderBy('middlename', 'asc')
                        ->paginate(2);
        return view('admin.staff.index', compact('staffs'));

    }

    public function select_organ(Request $request)
    {        
        return redirect()->route('admin.staff.create', ['organ'=> $request->input('organ')]);
    }

    public function create()
    {        
        $offices = Office::orderBy('name', 'asc')->get();
        return view('admin.staff.create', compact('offices'));
    }

    public function store(Request $request){

        //dd($request);
        // generate a 6 character passsword
        $password = Str::substr(Str::uuid(), 0,6);

       //dd($password);

        $formFields = $request->validate([ 
            'title' => 'required',
            'surname' => 'required | string',
            'firstname' => ['required', 'string'], 
            'middlename' => ['required', 'string'],   
            'fileno' => 'required|unique:staff,fileno',         
            'email' => 'required|email|unique:users,email',
            'office' => 'required',
            'role' => 'required | string'
        ]);

        $formFields['title'] = strtoupper($formFields['title']);
        $formFields['surname'] = strtoupper($formFields['surname']);
        $formFields['firstname'] = ucfirst($formFields['firstname']);
        $formFields['middlename'] = ucfirst($formFields['middlename']);
        $formFields['email'] = strtolower($formFields['email']);
        $formFields['office_id'] = $request->input('office');
        
        //dd($formFields);

        DB::beginTransaction();

        try{
            
            // create user login for the user
            /* $user = new User();
            $user->fileno = $formFields['fileno'];
            $user->surname = $formFields['surname'];
            $user->firstname = $formFields['firstname'];
            $user->middlename = $formFields['middlename'];
            $user->email = $formFields['email'];
            $user->password = bcrypt($password);
            $user->role = "staff";
            $user->save(); */

            $userData = [
                'surname' => $formFields['surname'],
                'firstname' => $formFields['firstname'],
                'middlename' => $formFields['middlename'],
                'email' => $formFields['email'],
                'password' => bcrypt($password),
                'role' => $formFields['role']
            ];

            
            $createUser = User::create($userData);           


            if ($createUser){                      

                try
                {

                    $formFields['user_id'] = $createUser->id;

                    $createStaff = Staff::create($formFields);

                    if ($createStaff)
                    {
                            $data = [
                                'error' => true,
                                'status' => 'success',
                                'message' => 'The Staff has been successfully created'
                            ];                      

                            // send email
                            $fullname = $formFields['surname'].' '.$formFields['firstname'];
                            $username = $formFields['email'];
                            $recipient = $fullname;
                            $recipient_email = $formFields['email'];

                            $payload = array("fullname"=>$fullname, "username"=>$username, "password"=>$password);

                            /* old implementation - discontinued

                            Mail::send('emails.onboarding', $payload, function($message) use($recipient_email, $recipient){
                                $message->to($recipient_email, $recipient)
                                        ->subject("Welcome to O-ORBDA EDMS");
                                $message->from("clearanceinfo@funaab.edu.ng", "O-ORBDA EDMS");
                                        
                            });        

                            */
                            
                            DB::commit();
                    }
                    else
                    {
                            $data = [
                                'error' => true,
                                'status' => 'success',
                                'message' => 'An error occurred creating the Staff'
                            ];   
                            
                            DB::rollBack();
                    }

                    
    
                }
                catch(\Exception $e)
                {
                    $data = [
                        'error' => true,
                        'status' => 'fail',
                        'message' => 'An error occurred '.$e->getMessage()
                    ];   

                        DB::rollBack();
                }

            }
            else
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred creating the User Account'
                ];

                DB::rollBack();
            }
        }
        catch(\Exception $e)
        {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred creating the Staff '.$e->getMessage()
                ];

                DB::rollBack();
        }

        return redirect()->back()->with($data);

    }


    public function edit(Request $request, Staff $staff)
    {
        $offices = Office::orderBy('name', 'asc')->get();
        return view('admin.staff.edit', compact('staff', 'offices'));
    }

    
    public function update(Request $request, Staff $staff)
    {
        $formFields = $request->validate([            
            
            'title' => ['required', 'string'],
            'fileno' => 'required | string',
            'surname' => 'required | string',
            'firstname' => 'required | string',
            'middlename' => 'required | string',
            'office' => 'required'
        ]);

    
        
        $formFields['office_id'] = $request->input('office');
        

        try
        {
            $update = $staff->update($formFields);

            if ($update){
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'Staff Information has been successfully updated'
                ];
            }else{
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred updating the staff information'
                ];
            }

        }
        catch(\Exception $e)
        {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred updating the staff information: '.$e->getMessage()
                ];
        }
       
        return redirect()->back()->with($data);
    }

    public function fetch_organ(Request $request)
    {
        $segment_id = $request->query('segment_id');

        $organ = '';
        $organ_items = '';

        if ($segment_id != null)
        {
                switch ($segment_id)
                {
                        case 1:
                            $organ = "Directorate";
                            $organ_items = Directorate::orderBy('name', 'asc')->get();
                            break;
                        case 2:
                            $organ = "Department";
                            $organ_items = Department::orderBy('name', 'asc')->get();
                            break;
                        case 3:
                            $organ = "Division";
                            $organ_items = Division::orderBy('name', 'asc')->get();
                            break;
                        case 4:
                            $organ = "Branch";
                            $organ_items = Branch::orderBy('name', 'asc')->get();
                            break;                
                        case 5:
                            $organ = "Section";
                            $organ_items = Section::orderBy('name', 'asc')->get();
                            break;
                        case 6:
                            $organ = "Unit";
                            $organ_items = Unit::orderBy('name', 'asc')->get();
                }
        }


        $select_options = "<option value=''>--Select Organ --</option>";

        foreach($organ_items as $item)
        {
            $select_options .="<option value='".$item->id."'>{$item->name}</option>";
        }
        

        return $select_options;
    }
}
