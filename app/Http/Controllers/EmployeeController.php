<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Departement;
use App\Models\User;
use Hash;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;

class EmployeeController extends Controller
{
    public function create($id)
    {
        $department = Departement::findOrFail($id);
        $company = $department->company;
        if (Gate::denies('create', [$company, $department])) {
            return redirect()->back()->with('error', 'Aksi tidak diizinkan');
        }
        return Inertia::render('Employee/AddForm', [
            'department' => $department
        ]);
    }
    public function store(Request $request, $id)
    {
        $department = Departement::findOrFail($id)->load('users');
        $company = $department->company;

        if (Gate::denies('create', [$company, $department])) {
            return redirect()->back()->with('error', 'Aksi tidak diizinkan');
        }
        ;
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'password_confirmation' => 'required|string|min:8|same:password',
        ]);

        $user = $department->users()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 2,
            'company_id' => $department->company_id,
        ]);

        if ($department->name === 'Master') {
            $user->update(['role_id' => 0]);
        }

        return redirect()->route('dashboard')->with('success', 'Karyawan ditambahkan ke departemen ' . $department->name . '.');
    }
    public function update(Request $request, $id)
    {
        $employee = User::findOrFail($id)->load('departement');

        $company = $employee->company;
        if (Gate::denies('update', [$company, $employee->departement])) {
            return redirect()->back()->with('error', 'Aksi tidak diizinkan');
        }
        $request->validate([
            'departement_id' => 'required|exists:departements,id',
        ]);
        $employee->update([
            'departement_id' => $request->departement_id,
        ]);
        $employee->refresh();
        return Redirect::back()->with('success', 'Karyawan ' . $employee->name . ' berhasil dimutasikan ke departemen ' . $employee->departement->name . '.');
    }
    public function destroy($id)
    {
        $employee = User::findOrFail($id);
        $company = $employee->company;
        if (Gate::denies('delete', [$company, $employee->departement])) {
            return redirect()->back()->with('error', 'Aksi tidak diizinkan');
        }
        $employee->delete();
        return Redirect::back()->with('success', 'Karyawan ' . $employee->name . ' berhasil dihapus.');
    }
}
