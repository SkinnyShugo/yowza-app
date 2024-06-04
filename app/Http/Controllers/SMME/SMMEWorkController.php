<?php

namespace App\Http\Controllers\SMME;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use App\Models\SMMEWorkspace;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Pagination\LengthAwarePaginator;


class SMMEWorkController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        $workspaces = SMMEWorkspace::paginate(3);

        $purchased_courses = [];
        if (auth()->check()) {
            $purchased_courses = Course::whereHas('students', function($query) {
                $query->where('users.id', auth()->id());
            })
                ->with('lessons')
                ->orderBy('id', 'desc')
                ->get();
        }
        $courses =  Course::paginate(10);

        return view('smme.index', compact('workspaces', 'courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
        return view('smme.create');

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Define validation rules for the SMMEWorkspace fields
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'smme_business_name' => 'required|string|max:255',
            'smme_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'smme_description_of_business' => 'required|string',
            'smme_industry' => 'nullable|string|max:255',
            'smme_business_registration_number' => 'nullable|string|max:255',
            'smme_company_size' => 'nullable|string|max:255',
            'smme_company_address' => 'nullable|string',
            'smme_company_email' => 'nullable|string|email|max:255',
            'smme_landline_number' => 'nullable|string|max:255',
            'smme_business_classification' => 'nullable|string|max:255',
            'smme_established_in_year' => 'nullable|string|max:255',
            'smme_do_you_have_a_b_bbee_certificate' => 'nullable|string|max:255',
            'smme_b_bbee_level' => 'nullable|string|max:255',
            'smme_black_woman_ownership' => 'nullable|string|max:255',
            'smme_growth_in_profit' => 'nullable|string|max:255',
            'smme_growth_in_jobs' => 'nullable|string|max:255',
            'smme_growth_in_economic_participation' => 'nullable|string|max:255',
        ]);

        // Store the uploaded file in the specified directory
        if ($request->hasFile('smme_logo')) {
            $validatedData['smme_logo'] = $request->file('smme_logo')->store('smme_workspace/logo', 'public');
        }

        // Create a new SMMEWorkspace instance with the validated data
        $workspace = SMMEWorkspace::create($validatedData);

        // Assign the role of 'leader' to the authenticated user
        if (Auth::check()) {
            $workspace->assignRoleToUser(Auth::user()->id, 'leader');
        }

        // Redirect to the index page with a success message
        return redirect()->route('smme.index', ['prefix' => 'admin'])
            ->with('success', 'SMMEWorkspace created successfully');
    }


    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SMMEWorkspace $workspace)
    {
        return view('smme.edit', compact('workspace'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SMMEWorkspace $workspace)
    {
        // Define validation rules for the SMMEWorkspace fields
        $validatedData = $request->validate([
            // Your validation rules here...
            'name' => 'required|string|max:255',
            'smme_business_name' => 'required|string|max:255',
            'smme_logo' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'smme_description_of_business' => 'required|string',
            'smme_industry' => 'nullable|string|max:255',
            'smme_business_registration_number' => 'nullable|string|max:255',
            'smme_company_size' => 'nullable|string|max:255',
            'smme_company_address' => 'nullable|string',
            'smme_company_email' => 'nullable|string|email|max:255',
            'smme_landline_number' => 'nullable|string|max:255',
            'smme_business_classification' => 'nullable|string|max:255',
            'smme_established_in_year' => 'nullable|string|max:255',
            'smme_do_you_have_a_b_bbee_certificate' => 'nullable|string|max:255',
            'smme_b_bbee_level' => 'nullable|string|max:255',
            'smme_black_woman_ownership' => 'nullable|string|max:255',
            'smme_growth_in_profit' => 'nullable|string|max:255',
            'smme_growth_in_jobs' => 'nullable|string|max:255',
            'smme_growth_in_economic_participation' => 'nullable|string|max:255',
        ]);

        // Handle file upload for smme_logo field if a new file is provided
        if ($request->hasFile('smme_logo')) {
            // Delete the old logo file if it exists
            if ($workspace->smme_logo) {
                Storage::disk('public')->delete($workspace->smme_logo);
            }
            // Store the new uploaded file in the specified directory
            $validatedData['smme_logo'] = $request->file('smme_logo')->store('smme_workspace/logo', 'public');
        }

        // Update the SMMEWorkspace instance with the validated data
        $workspace->update($validatedData);

        // Redirect to the index page with a success message
        return redirect()->route('smme.index', ['prefix' => 'admin'])
            ->with('success', 'SMMEWorkspace updated successfully');
    }

    public function join($id)
    {
        // Find the SMMEWorkspace by its ID
        $workspace = SMMEWorkspace::findOrFail($id);

        // Logic to allow the user to join the workspace
        // For example, you might want to associate the user with the workspace

        // Return a response indicating successful join
        return redirect()->route('smme.index')->with('success', 'Joined workspace successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
