<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use Gate;
use Illuminate\Http\Request;
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

        $company->departements()->create([
            'name' => $validated['name'],
            'token' => Str::uuid(),
        ]);

        return Redirect::back()->with('success', 'Departemen ' . $validated['name'] . ' berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $departement = Departement::findOrFail($id);
        if(Gate::denies('update', $departement)){
            return redirect()->back()->with('error', 'Aksi tidak diizinkan');
        }
        return Inertia::render('Departements/Edit', [
            'departement' => $departement
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $departement = Departement::findOrFail($id);
        if(Gate::denies('update', $departement)){
            return redirect()->back()->with('error', 'Aksi tidak diizinkan');
        }
        $departement->update($validated);

        return Redirect::back()->with('success', 'Departemen berhasil diperbarui.');
    }
    public function regenerateToken($id)
    {
        $departement = Departement::findOrFail($id);
        if(Gate::denies('regenerateToken', $departement)){
            return redirect()->back()->with('error', 'Aksi tidak diizinkan');
        }
        $departement->update(
            [
                'token' => Str::uuid(),
            ]
        );

        return Redirect::back()->with('success', 'Token departemen ' . $departement->name . ' berhasil diperbarui.');
    }
    public function destroy($id)
    {
        $departement = Departement::findOrFail($id);
        if(Gate::denies('delete', $departement)){
            return redirect()->back()->with('error', 'Aksi tidak diizinkan');
        }
        $departement->delete();

        return Redirect::back()->with('success', 'Departemen ' . $departement->name . ' berhasil dihapus.');
    }
}
