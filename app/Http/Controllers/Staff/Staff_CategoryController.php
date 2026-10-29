<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;

class Staff_CategoryController extends Controller
{
    //
    public function index()
    {

    }

    public function create()
    {
        $my_categories = Category::where('owner', Auth::id())->get();       
        return view('staff.categories.create')->with('categories', $my_categories);
    }

    public function store(Request $request)
    {
        $formFields = $request->validate([
            'name' => 'required | string'
        ]);

        $categoryExist = Category::where('name', $request->name)
                                  ->where('owner', Auth::id())
                                  ->exists();
        if ($categoryExist)
        {
            $data = [
                'error' => true,
                'status' => 'fail',
                'message' => 'A Category with that name already exist'
            ];

            return redirect()->back()->with($data);
        }


        $formFields['owner'] = Auth::id();

        try
        {
            $create = Category::create($formFields);

            if ($create)
            {
                $data = [
                    'error' => true,
                    'status' => 'success',
                    'message' => 'The Category has been successfully created'
                ];
            }
            else
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred creating the Category'
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
}
