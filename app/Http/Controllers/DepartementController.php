<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Inertia\Inertia;

class DepartementController extends Controller
{
    public function store(Request $request)
    {

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:departements,name',
        ]);

        $company = $request->user()->company;
        Gate::authorize('create', $company);
        $company->departements()->create([
            'name' => $validated['name'],
            'token' => Str::uuid(),
        ]);
        broadcast(new \App\Events\updatedDashboardData($company->id))->toOthers();
        return Redirect::back()->with('success', 'Departemen ' . $validated['name'] . ' berhasil ditambahkan.');
    }


    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $departement = Departement::findOrFail($id);
        Gate::authorize('update', auth()->user()->company);
        if(Gate::denies('update', $departement)){
            return redirect()->back()->with('error', 'Aksi tidak diizinkan');
        }
        $departement->update($validated);
        broadcast(new \App\Events\updatedDashboardData($departement->company_id))->toOthers();
        return Redirect::back()->with('success', 'Departemen berhasil diperbarui.');
    }
    public function regenerateToken($id)
    {
        Gate::authorize('update', auth()->user()->company);
        $departement = Departement::findOrFail($id);
        if(Gate::denies('regenerateToken', $departement)){
            return redirect()->back()->with('error', 'Aksi tidak diizinkan');
        }
        $departement->update(
            [
                'token' => Str::uuid(),
            ]
        );
        broadcast(new \App\Events\updatedDashboardData($departement->company_id))->toOthers();

        return Redirect::back()->with('success', 'Token departemen ' . $departement->name . ' berhasil diperbarui.');
    }
    public function destroy($id)
    {
        Gate::authorize('delete', auth()->user()->company);
        $departement = Departement::findOrFail($id);
        $idCompany = $departement->company_id;
        if(Gate::denies('delete', $departement)){
            return redirect()->back()->with('error', 'Aksi tidak diizinkan');
        }
        $departement->delete();
        broadcast(new \App\Events\updatedDashboardData($idCompany))->toOthers();
        return Redirect::back()->with('success', 'Departemen ' . $departement->name . ' berhasil dihapus.');
    }
}
