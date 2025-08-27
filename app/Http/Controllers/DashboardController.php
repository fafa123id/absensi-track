<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index()
    {
        $company = auth()->user()->company->firstOrFail();
        $departements = Departement::where('company_id', $company->id)->paginate(10);
        $employees = User::where('company_id', $company->id)->paginate(10);
        return Inertia::render('Dashboard',
            [
                'employees' => $employees,
                'departements' => $departements,
                'company' => $company
            ]
        );
    }
}
