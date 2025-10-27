<?php

namespace App\Http\Controllers;

use App\Models\WifiCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class WifiController extends Controller
{
    public function store(Request $request)
    {
        $company = auth()->user()->company;
        Gate::authorize("create", auth()->user()->company);
        $request->validate([
            "name" => "string|required",
            "ip" => "string|required|unique:". WifiCompany::class,
        ]);
        $company->wifis()->create($request->all());
        broadcast(new \App\Events\updatedDashboardData($company->id))->toOthers();
        return redirect()->back()->with("success", "Wifi Berhasil Ditambahkan");
    }
    public function update(Request $request, $id)
    {
        $company = auth()->user()->company;
        Gate::authorize("update", auth()->user()->company);
        $request->validate([
            "name" => "string|required",
            "ip" => "string|required|unique:". WifiCompany::class,
        ]);
        $company->wifis()->where("id", $id)->update($request->all());
        broadcast(new \App\Events\updatedDashboardData($company->id))->toOthers();
        return redirect()->back()->with("success", "Wifi Berhasil Diedit");
    }
    public function destroy($id)
    {
        $company = auth()->user()->company;
        Gate::authorize("delete", auth()->user()->company);
        $company->wifis()->where("id", $id)->delete();
        broadcast(new \App\Events\updatedDashboardData($company->id))->toOthers();
        return redirect()->back()->with("success", "Wifi Berhasil Dihapus");
    }
    public function getWifi(Request $request)
    {
        $ip = $request->header('CF-Connecting-IP')        // Cloudflare (tunnel/CDN)
            ?? $request->header('True-Client-IP')          // Beberapa proxy/CDN lain
            ?? $request->ip();
        return response()->json([
            "ip" => $ip
        ]);
    }
}
