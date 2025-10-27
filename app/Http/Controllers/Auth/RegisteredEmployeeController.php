<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Departement;
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

class RegisteredEmployeeController extends Controller
{
    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|lowercase|email|max:255|unique:' . User::class,
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'token' => 'required|string|max:255|exists:departements,token',
        ]);
        $departement = Departement::where('token', $request->token)->firstOrFail();
        $user = $departement->users()->create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role_id' => 1,
            'company_id' => $departement->company_id,
        ]);

        $user->uniqueIdentityQr()->create([
            'public_id' => Str::uuid(),
            'unique_code' => Str::uuid(),
        ]);
        event(new Registered($user));
        broadcast(new \App\Events\updatedDashboardData($user->company_id))->toOthers();
        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}
