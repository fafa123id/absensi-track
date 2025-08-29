<?php

namespace App\Policies;

use App\Models\Company;
use App\Models\Departement;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class EmployeePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Company $company): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user, Company $company, Departement $departement)
    {
        return $user->company_id === $company->id
            ? true
            : false;

    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Company $company, Departement $departement)
    {
        if ($departement->name === 'Master') {
            return false;
        }
        return $user->company_id === $company->id
            ? true
            : false;

    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Company $company, Departement $departement)
    {
        if ($departement->name === 'Master') {
            return false;
        }
        return $user->company_id === $company->id
            ? true
            : false;

    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Company $company): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Company $company): bool
    {
        return false;
    }
}
