<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\UniqueIdentityQr;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Inertia\Inertia;

class QrIdentityController extends Controller
{
    public function show(Request $request, $id)
    {
        $user = auth()->user();
        $identity = UniqueIdentityQr::where('public_id', $id)->with('user.company')->firstOrFail();

        if ($user) {
            if ($request->wantsJson()) {
                return response()->json([
                    'identity' => $identity->unique_code,
                ]);
            }
            $activeSession = DB::table('sessions')
                ->where('user_id', $user->id)
                ->count();
            return Inertia::render('Auth/IdentityUser', [
                'company' => $identity->user->company,
                'user' => $user->with('departement')->first(),
                'sessionActive' => $activeSession,
            ]);
        }
        return Inertia::render('PublicIdentity', ['company' => $identity->user->company]);
    }
    public function getQr(Request $request)
    {
        $user = auth()->user();
        $identity = UniqueIdentityQr::where('user_id', $user->id)->with('user.company')->firstOrFail();
        return response()->json([
            'identity' => $identity->public_id,
        ]);
    }
    public function index()
    {
        return Inertia::render('Identity/QrTest');
    }
    public function absen(Request $request)
    {
        $request->validate([
            'id' => 'required|uuid',
        ]);

        $identity = UniqueIdentityQr::where('unique_code', $request->id)->firstOrFail();
        return response()->json(['message' => 'Absensi berhasil dicatat untuk ' . $identity->user->name]);
    }
    public function indexAbsen()
    {
        return Inertia::render('Identity/AbsensiTest');
    }
}
