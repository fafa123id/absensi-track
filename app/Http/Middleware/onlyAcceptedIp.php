<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class onlyAcceptedIp
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $wifiIps = auth()->user()->company->wifis()->pluck('ip')->toArray();

        if (in_array($request->ip(), $wifiIps)) {
            return $next($request);
        }
        return redirect()->route("dashboard")->with("error", "Anda Tidak Berada di Wifi Perusahaan");
    }
}
