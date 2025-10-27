<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\Inertia;

class ProjectController extends Controller
{
    public function create()
    {
        Gate::authorize("create", auth()->user()->company);
        return Inertia::render('Project/AddForm');
    }

    public function store(Request $request)
    {
        
        $company=auth()->user()->company->with('projects')->firstOrFail();
        Gate::authorize("create", $company);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scope' => 'nullable|string',
            'start_date' => 'nullable|date|before_or_equal:end_date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        $validated['token'] = Str::uuid();
       
        $company->projects()->create($validated);
        broadcast(new \App\Events\updatedDashboardData($company->id))->toOthers();
        return redirect()->route('dashboard')->with('success', 'Proyek berhasil dibuat.');
    }

    public function edit(Project $project)
    {
        Gate::authorize("update", auth()->user()->company);
        return Inertia::render('Project/EditForm', ['project' => $project]);
    }

    public function update(Request $request, Project $project)
    {
        Gate::authorize("update", auth()->user()->company);
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'scope' => 'nullable|string',
            'start_date' => 'nullable|date|before_or_equal:end_date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
        $project->update($validated);
        broadcast(new \App\Events\updatedDashboardData($project->company_id))->toOthers();
        return redirect()->route('dashboard')->with('success', 'Proyek berhasil diperbarui.');
    }

    public function destroy(Project $project)
    {
        Gate::authorize("delete", auth()->user()->company);
        $idCompany = $project->company_id;
        $project->delete();
        broadcast(new \App\Events\updatedDashboardData($idCompany))->toOthers();
        return back()->with('success', 'Proyek berhasil dihapus.');
    }

    public function refreshToken(Project $project)
    {
        Gate::authorize("update", auth()->user()->company);
        $project->update(['token' => Str::uuid()->toString()]);
        broadcast(new \App\Events\updatedDashboardData($project->company_id))->toOthers();
        return back()->with('success', 'Token proyek berhasil disegarkan.');
    }
}