<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\ProjectRole;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ProjectRoleController extends Controller
{
    public function store(Request $request, Project $project)
    {
        Gate::authorize("create", auth()->user()->company);
        $validated = $request->validate(['name' => 'required|string|max:255', 'description' => 'nullable|string']);
        $project->projectRoles()->create($validated);
        broadcast(new \App\Events\updatedDashboardData($project->company_id))->toOthers();
        return back()->with('success', 'Role berhasil ditambahkan.');
    }

    public function update(Request $request, ProjectRole $projectRole)
    {
        Gate::authorize("update", auth()->user()->company);
        $validated = $request->validate(['name' => 'required|string|max:255', 'description' => 'nullable|string']);
        $projectRole->update($validated);
        broadcast(new \App\Events\updatedDashboardData($projectRole->project->company_id))->toOthers();
        return back()->with('success', 'Role berhasil diperbarui.');
    }

    public function destroy(ProjectRole $projectRole)
    {
        Gate::authorize("delete", auth()->user()->company);
        if ($projectRole->project->users()->wherePivot('project_role_id', $projectRole->id)->exists()) {
            return back()->withErrors(['error' => 'Role tidak dapat dihapus karena sedang digunakan oleh user.']);
        }
        $idCompany = $projectRole->project->company_id;
        $projectRole->delete();
        broadcast(new \App\Events\updatedDashboardData($idCompany))->toOthers();
        return back()->with('success', 'Role berhasil dihapus.');
    }
}