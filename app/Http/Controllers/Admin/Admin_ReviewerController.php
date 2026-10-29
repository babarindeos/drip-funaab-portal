<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Reviewer;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class Admin_ReviewerController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = trim((string) $request->query('q', ''));

        $query = Reviewer::withCount('assignments');

        if ($search !== '') {
            $like = '%'.addcslashes($search, '%_\\').'%';
            $query->where(function ($q) use ($like) {
                $q->where('name', 'like', $like)
                  ->orWhere('email', 'like', $like)
                  ->orWhere('expertise_area', 'like', $like);
            });
        }

        $total = Reviewer::count();
        $reviewers = $query->orderBy('name', 'asc')->paginate(10)->withQueryString();

        return view('admin.reviewers.index', compact('reviewers', 'search', 'total'));
    }

    public function create()
    {
        return view('admin.reviewers.create', ['reviewer' => new Reviewer()]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'expertise_area' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:reviewers,email',
        ], [
            'email.unique' => 'A reviewer with this email address already exists.',
        ]);

        try
        {
            $reviewer = new Reviewer();
            $reviewer->uuid = Str::orderedUuid();
            $reviewer->name = $request->name;
            $reviewer->expertise_area = $request->expertise_area;
            $reviewer->email = $request->email;
            $reviewer->save();
        }
        catch(\Exception $e)
        {
            report($e);

            return redirect()->back()->withInput()->with(['error' => true, 'status' => 'fail', 'message' => 'An error occurred creating the reviewer.']);
        }

        return redirect()->route('admin.reviewers.index')
                         ->with(['error' => true, 'status' => 'success', 'message' => 'The reviewer '.$reviewer->name.' has been added.']);
    }

    public function edit(Reviewer $reviewer)
    {
        return view('admin.reviewers.edit', compact('reviewer'));
    }

    public function update(Request $request, Reviewer $reviewer)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'expertise_area' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('reviewers', 'email')->ignore($reviewer->id)],
        ], [
            'email.unique' => 'Another reviewer already uses this email address.',
        ]);

        $reviewer->name = $request->name;
        $reviewer->expertise_area = $request->expertise_area;
        $reviewer->email = $request->email;
        $reviewer->save();

        return redirect()->route('admin.reviewers.index')
                         ->with(['error' => true, 'status' => 'success', 'message' => 'The reviewer '.$reviewer->name.' has been updated.']);
    }

    public function destroy(Reviewer $reviewer)
    {
        // A reviewer who has been sent proposals is part of the review records.
        if ($reviewer->assignments()->exists()) {
            return redirect()->route('admin.reviewers.index')
                             ->with(['error' => true, 'status' => 'fail', 'message' => $reviewer->name.' has been sent proposals to review, so they cannot be deleted.']);
        }

        $name = $reviewer->name;
        $reviewer->delete();

        return redirect()->route('admin.reviewers.index')
                         ->with(['error' => true, 'status' => 'success', 'message' => 'The reviewer '.$name.' has been deleted.']);
    }

    public function generate_uuid()
    {
        
        $reviewers = Reviewer::get();
        
        foreach($reviewers as $reviewer)
        {
            if ($reviewer->uuid == null)
            {
                do
                {
                    $uuid = Str::orderedUuid();
                }while(Reviewer::where('uuid', $uuid)->exists());

                
                $reviewer->uuid = $uuid;
                $reviewer->save();
            }
            
        }   

        return redirect()->route('admin.reviewers.index');
    }

    public function fetch_reviewer(Request $request)
    {

        $reviewer_options = '';
        $query = $request->get('search_term');

        
        $reviewers = Reviewer::where('name', 'LIKE', "%{$query}%")->get();

         $reviewer_options = '';

        if ($reviewers->count() > 0)
        {
            foreach($reviewers as $reviewer)
            {
                $staff = $reviewer->name;
                $reviewer_options .= "<div class='py-3 border border-gray-500 cursor-pointer px-2' id='".$reviewer->id."'> ".$staff."</div>";
            }
        }
        else
        {
            $reviewer_options = "<div class='py-3 border border-gray-500 cursor-pointer px-2'> No Reviewer found </div>";
        }

        return $reviewer_options;
       
    }
}
