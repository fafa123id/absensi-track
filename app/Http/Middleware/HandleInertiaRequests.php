<?php

namespace App\Http\Middleware;

use Illuminate\Http\Request;
use Inertia\Middleware;
use Jenssegers\Agent\Agent;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that is loaded on the first page visit.
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determine the current asset version.
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $agent = new Agent();
        return [
            ...parent::share($request),
            'device' => [
                'isMobile' => $agent->isPhone(),
                'isTablet' => $agent->isTablet(),
                'isDesktop' => $agent->isDesktop(),
            ],
            'flash' => [
                'success' => fn() => $request->session()->get('success'),
                'error' => fn() => $request->session()->get('error'),
            ],
            'auth' => [
                'user' => fn() => $request->user()
                    ? $request->user()
                    : null,
            ],
            'currentSessionId' => $request->user() ? $request->session()->getId() : null,

        ];
    }
}
