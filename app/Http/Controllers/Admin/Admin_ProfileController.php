<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Staff;

class Admin_ProfileController extends Controller
{
    public function user_profile($email)
    {

        $userprofile = Staff::with('user')
                             ->whereHas('user', function($q) use ($email){
                                $q->where('email', $email);
                             })
                             ->first();
       
        
        return view('admin.profile.user_profile', compact('userprofile'));
    }

}
