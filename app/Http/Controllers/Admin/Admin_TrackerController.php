<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Document;

class Admin_TrackerController extends Controller
{
    //
    public function index(Request $request)
    {
        $search_data = null;
        $isPostBack = false;

        if ($request->input('q') != '')
        {
            $isPostBack = true;
            $search = $request->get('q');
            
            /* $search_data = DB::table("documents")
                            ->join("users", "documents.uploader","=", "users.id")
                            ->join("staff", "users.id", "=", "staff.user_id")
                            ->join("departments", "staff.department_id", "=", "departments.id")
                            ->join("ministries", "departments.ministry_id", "=", "ministries.id")
                            ->select("documents.*")
                            ->where('documents.title', 'LIKE', "%{$search}%" )
                            ->orWhere('documents.filetype', 'LIKE', "%{$search}%")
                            ->orWhere('staff.surname', 'LIKE', "%{$search}%")
                            ->orWhere('staff.firstname', 'LIKE', "%{$search}%")
                            ->orWhere('departments.department_name', 'LIKE', "%{$search}%")
                            ->orWhere('departments.department_code', 'LIKE', "%{$search}%")
                            ->orWhere('ministries.name', 'LIKE', "%{$search}%")
                            ->orWhere('ministries.code', 'LIKE', "%{$search}%")
                            ->paginate(50);            */ 

            $search_data = Document::query()
                           ->with(['owner.staff.department.ministry'])
                           ->where('title', 'LIKE', "%{$search}%")
                           ->orWhere('filetype', 'LIKE', "%{$search}%")
                           ->orWhereHas('owner.staff', function($query) use ($search){
                                $query->where('surname', 'LIKE', "%{$search}%")
                                ->orWhere('firstname', 'LIKE', "%{$search}%");
                           })
                           ->orWhereHas('owner.staff.department', function($query) use ($search){
                                $query->where('department_name', 'LIKE', "%{$search}%")
                                ->orWhere('department_code', 'LIKE', "%{$search}%");
                           })
                           ->orWhereHas('owner.staff.department.ministry', function($query) use ($search){
                                $query->where('name', 'LIKE', "%{$search}%")
                                ->orWhere('code', 'LIKE', "%{$search}%");
                           })
                           ->paginate();

            //dd($search_data);
                            
        }

        return view('admin.tracker.index')->with(['documents' => $search_data, "isPostBack" => $isPostBack]);
    }
}
