<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminCategoryType;


class Admin_AdminCategoryTypeController extends Controller
{
    //
    public function index()
    {
        $admin_categories_types = AdminCategoryType::orderBy('id', 'desc')->get();
        return view('admin.admin_category_types.index', compact('admin_categories_types'));
    }

    public function create()
    {
        return view('admin.admin_category_types.create');
    }

    public function store(Request $request)
    {
        $formFields = $request->validate([
            'name' => "required|unique:admin_category_types,name"
        ]);

        try
        {
            $create = AdminCategoryType::create($formFields);

            if ($create)
            {
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'The Admin Category Type has been successfully created'
                ];
            }
            else
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred Creating the Category Type'
                ];
            }
        }
        catch(\Exception $e)
        {   
            $data = [
                'error' => true,
                'status' => 'fail',
                'message' => $e->getMessage()
            ];

        }

        return redirect()->back()->with($data);
    }

    public function edit(AdminCategoryType $admin_category_type)
    {
        return view('admin.admin_category_types.edit', compact('admin_category_type'));
    }

    public function update(Request $request, AdminCategoryType $admin_category_type)
    {
        $formFields = $request->validate([
            'name' => 'required'
        ]);

        try
        {
            $update = $admin_category_type->update($formFields);

            if ($update)
            {
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'Admin Category Type update has been successfully updated'
                ];
            }
            else
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred updating the Admin Category Type'
                ];

            }
        }
        catch(\Exception $e)
        {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => $e->getMessage()
                ];
        }

        return redirect()->back()->with($data);
    }

    public function show(AdminCategoryType $admin_category_type)
    {
        return view('admin.admin_category_types.show', compact('admin_category_type'));
    }
}
