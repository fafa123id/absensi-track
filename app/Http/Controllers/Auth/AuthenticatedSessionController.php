<?php

namespace App\Http\Controllers\Auth;

use App\Events\SessionLoggedIn;
use App\Events\SessionLoggedOut;
use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Notifications\WebPushLogin;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;
use Inertia\Response;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): Response
    {
        return Inertia::render('Auth/Login', [
            'canResetPassword' => Route::has('password.request'),
            'status' => session('status'),
        ]);
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        broadcast(new SessionLoggedIn(
            Auth::id(),
            request()->ip(),
            now()->format('d M Y H:i'),
        ))->toOthers();
        // $deviceName = $this->guessDeviceName($request);
        // $ip = $request->ip();
        // $at = now()->format('d M Y H:i');

        // $request->user()->notify(new WebPushLogin(
        //     'Login baru terdeteksi',
        //     "{$deviceName} @ {$ip} • {$at}",
        //     route('profile.edit')
        // ));
        return redirect()->intended(route('dashboard', absolute: false))->withHeaders(['X-Inertia-Replace' => 'true']);;
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $idAuth = Auth::id();
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        broadcast(new SessionLoggedOut("test", $idAuth))->toOthers();

        return redirect('/')->withHeaders(['X-Inertia-Replace' => 'true']);;
    }
    private function guessDeviceName(Request $request): string
    {
        $ua = $request->userAgent() ?? '';
        if (str_contains($ua, 'Windows')) return 'Windows';
        if (str_contains($ua, 'Android')) return 'Android';
        if (str_contains($ua, 'iPhone') || str_contains($ua, 'iPad')) return 'iOS';
        if (str_contains($ua, 'Mac OS') || str_contains($ua, 'Macintosh')) return 'macOS';
        if (str_contains($ua, 'Linux')) return 'Linux';
        return 'Unknown device';
    }
}
