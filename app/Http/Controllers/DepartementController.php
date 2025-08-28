<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use Illuminate\Http\Request;
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

        return redirect()->route('dashboard')->with('success', 'Departemen ' . $validated['name'] . ' berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $departement = Departement::findOrFail($id);
        if ($departement->name === 'Master') {
            return redirect()->route('dashboard')->with('error', 'Tidak dapat mengedit departemen Master.');

        }
        return Inertia::render('Departements/Edit', [
            'departement' => $departement
        ]);
    }

    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
        ]);

        $departement = Departement::findOrFail($id);
        if ($departement->name === 'Master') {
            return redirect()->back()->with('error', 'Tidak dapat mengedit departemen Master.');

        }
        $departement->update($validated);

        return redirect()->route('departements.index')->with('success', 'Departemen berhasil diperbarui.');
    }
    public function regenerateToken($id)
    {
        $departement = Departement::findOrFail($id);
        $departement->update(
            [
                'token' => Str::uuid(),
            ]
        );

        return redirect()->back()->with('success', 'Token departemen ' . $departement->name . ' berhasil diperbarui.');
    }
    public function destroy($id)
    {
        $departement = Departement::findOrFail($id);
        if ($departement->name === 'Master') {
            return redirect()->back()->with('error', 'Tidak dapat menghapus departemen Master.');
        }
        $departement->delete();

        return redirect()->back()->with('success', 'Departemen ' . $departement->name . ' berhasil dihapus.');
    }
}
