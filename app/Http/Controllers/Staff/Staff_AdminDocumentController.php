<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\AdminDocument;
use App\Models\AdminCategoryType;
use App\Models\AdminDocumentCategory;
use Illuminate\Support\Str;
use Carbon\Carbon;
use App\Http\Classes\Document;
use Illuminate\Support\Facades\Auth;


class Staff_AdminDocumentController extends Controller
{
    //
    public function index()
    {
        $documents = AdminDocument::where('uploader', Auth::id())
                                    ->orderby('id', 'desc')->paginate(10);
        
        return view('staff.admin_documents.index', compact('documents'));
    }

    public function create()
    {
        $admin_category_types = AdminCategoryType::orderBy('name', 'desc')->get();
        
        return view('staff.admin_documents.create', compact('admin_category_types') );
    }

    public function get_category_by_category_type(Request $request, $category_type)
    {
       //  $request->query('category_type');    -- by GET 
       $categories = AdminDocumentCategory::where('admin_category_type_id', $category_type)->get();

       $data = "<option value=''>-- Select Category --</option>";

       foreach( $categories as $category)
       {
            $data .= "<option value='{$category->id}'>{$category->name}</option>"; 
       }

       return $data;
    }

    public function store(Request $request)
    {
        $formFields = $request->validate([
            'document_title' => 'required|string',
            'category_type' => 'required',
            'category' => 'required',
            'document' => 'required|file|mimes:pdf,doc,docx,jpg,jpeg,png',
        ]);

        
        $formFields['category_id'] = $request->category;
        $formFields['comment'] = $request->input('comment');

        $document = '';
        $new_filename = '';
        $document_size = '';
        $document_type = '';

        if ($request->hasFile('document'))
        {
            $currentDateTime = Carbon::now()->format('Ymd_His');
            //$user_fileno = auth()->user()->fileno;   //alternate
            $user_fileno = Auth::user()->fileno;
            $user_fileno = strtolower($user_fileno);

            $user_id = auth()->user()->id;
            $user_id = "user".$user_id;

            $filename = $user_id.'_'.$user_fileno.'_'.$currentDateTime.'.';
            
            $document_file = $request->file('document');

            $document_size = Document::getDocumentSize($document_file);
            $document_type = Document::getDocumentType($document_file);

            $new_filename = $filename.$document_file->getClientOriginalExtension();

            $document_file->storeAs('documents', $new_filename);
            
        }


        $uuid = Str::uuid();

            $store_data = [
                'uuid' => $uuid,
                'title' => $formFields['document_title'],
                'admin_category_id' =>$formFields['category'],
                'document' => 'documents/'.$new_filename,
                'comment' => $formFields['comment'],
                'uploader' => auth()->user()->id,
                'filesize' => $document_size,
                'filetype' => $document_type
            ];

            


            try
            {
                $create = AdminDocument::create($store_data);

                if ($create)
                {
                    $data = [
                        'error' => true,
                        'status' => 'success',
                        'message' => 'Document has been successfully submitted'
                    ];


                    
                    
                }
                else
                {
                    $data = [
                        'error' => true,
                        'status' => 'fail',
                        'message' => 'An error occurred submitting the document'
                    ];
                }
            }
            catch(\Exception $e)
            {
                $data = [
                    'error' => true,
                    'status' => 'fail',
                    'message' => 'An error occurred submitting the document: '.$e->getMessage()
                ];
            }



            return redirect()->back()->with($data);




    }
}
