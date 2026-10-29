<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminCategoryType;
use App\Models\AdminDocumentCategory;

class Admin_AdminCategoryController extends Controller
{
    //
    public function index()
    {
        $admin_categories = AdminDocumentCategory::orderBy('name','asc')->get();
        return view('admin.admin_categories.index', compact('admin_categories'));
    }

    public function create()
    {
        $admin_category_types = AdminCategoryType::orderBy('name', 'asc')->get();
        return view('admin.admin_categories.create', compact('admin_category_types'));
    }

    public function store(Request $request)
    {
        $formFields = $request->validate([
            'category_type' => 'required',
            'name' => 'required|unique:admin_document_categories,name',
            'code' => 'required|unique:admin_document_categories,code'
        ]);

        try{
            $formFields['admin_category_type_id'] = $formFields['category_type'];

            $create = AdminDocumentCategory::create($formFields);

            if ($create)
            {
                $data = [
                    'error'=>true,
                    'status'=>'success',
                    'message'=> 'The Category has been successfully added'
                ];
            }
            else
            {
                $data = [
                    'error'=>true,
                    'status'=>'fail',
                    'message'=> 'An error occurred creating the Category'
                ];
            }
        }
        catch(\Exception $e)
        {
                $data = [
                    'error'=>true,
                    'status'=>'fail',
                    'message'=> $e->getMessage()
                ];
        }

        return redirect()->back()->with($data);
    }

    public function edit(AdminDocumentCategory $admin_category)
    {

        $admin_category_types = AdminCategoryType::orderBy('name', 'asc')->get();
        return view('admin.admin_categories.edit', compact('admin_category','admin_category_types'));
    }

    public function update(Request $request, AdminDocumentCategory $admin_category)
    {
        $formFields = $request->validate([
            'category_type' => 'required',
            'name' => 'required',
            'code' => 'required'
        ]);

        try
        {
            $formFields['admin_category_type_id'] = $request->input('category_type');

            $update = $admin_category->update($formFields);
            
            if ($update)
            {
                $data = [
                    'error'=> true,
                    'status'=>'success',
                    'message'=>'The Admin Category has been successfully updated'
                ];
            }
            else
            {
                $data = [
                    'error'=>true,
                    'status'=>'fail',
                    'message'=>'An error occurred updating the Admin Category'
                ];
            }
        }
        catch(\Exception $e)
        {
                $data = [
                    'error'=>true,
                    'status'=>'fail',
                    'message'=>$e->getMessage()
                ];
        }

        return redirect()->back()->with($data);
    }
}
