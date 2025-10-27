<?php

namespace App\Http\Controllers;

use App\Events\SessionLoggedOut;
use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Inertia\Inertia;
use Inertia\Response;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): Response
    {
        $session = DB::table('sessions')->where('user_id', $request->user()->id)->orderBy('last_activity', 'desc')->get()->map(function ($session) {
            $session->last_active = Carbon::createFromTimestamp($session->last_activity)
                ->timezone('Asia/Jakarta') // optional, sesuaikan timezone kamu
                ->toDateTimeString();
            return $session;
        });
        $thisSession = session()->getId();
        return Inertia::render('Profile/Edit', [
            'mustVerifyEmail' => $request->user() instanceof MustVerifyEmail,
            'status' => session('status'),
            'session' => $session,
            'thisSession' => $thisSession,
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validate([
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        $idUserCompany = $user->company->id;
        Auth::logout();
        if ($user->getIsAdminAttribute()) {
            $user->company()->delete();
        } else
            $user->delete();
        broadcast(new \App\Events\updatedDashboardData($idUserCompany))->toOthers();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return Redirect::to('/');
    }
    public function destroySession(Request $request, $sessionId): RedirectResponse
    {
        $user = auth()->user();
        if ($sessionId === session()->getId()) {
            Auth::guard('web')->logout();
            broadcast(new SessionLoggedOut("ping", $user->id))->toOthers();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
        } else {
            $deleted = DB::table('sessions')
                ->where('id', $sessionId)
                ->where('user_id', $user->id)
                ->delete();
            if ($deleted) {
                broadcast(new SessionLoggedOut($sessionId, $user->id))->toOthers();
            }
        }
        return Redirect::route('profile.edit')->with('success', 'Session berhasil dihapus.');
    }
}
