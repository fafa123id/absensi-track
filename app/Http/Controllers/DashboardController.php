<?php

namespace App\Http\Controllers;

use App\Models\Departement;
use App\Models\User;
use Illuminate\Http\Request;
use Inertia\Inertia;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $company = auth()->user()->company()->with('departements')->firstOrFail();
        $authUserId = auth()->id();
        $departements = Departement::query()->where('company_id', $company->id)
            ->with([
                'users' => function ($query) use ($authUserId) {
                    $query->where('id', '!=', $authUserId);
                }
            ])
            ->orderBy('id')->get();

        return Inertia::render(
            'Dashboard',
            [
                'departements' => $departements,
                'company' => $company,
            ]
        );
    }
}
