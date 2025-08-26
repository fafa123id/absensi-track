<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules;
use Inertia\Inertia;
use Inertia\Response;
class RegisteredAdminController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'user_name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required'],
            'password_confirmation' => ['required', 'string', 'min:8', 'max:255', 'same:password'],
            'company_name' => 'required|string|max:255',
            'company_email' => 'required|string|lowercase|email|max:255|unique:companies,email',
            'company_phone' => 'required|string|max:255',
            'company_address' => 'required|string|max:255',
        ]);

        $company = Company::create([
            'name' => $request->company_name,
            'email' => $request->company_email,
            'phone' => $request->company_phone,
            'address' => $request->company_address,
        ]);

        $departemen = $company->departements()->create([
            'name' => 'Master',
            'token' => (string) Str::uuid(),
        ]);

        $user = $company->users()->create([
            'name' => $request->user_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 0,
            'departement_id' => $departemen->id,
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
