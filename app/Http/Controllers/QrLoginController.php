<?php

namespace App\Http\Controllers;

use App\Events\QrSessionScanned;
use App\Events\SessionLoggedIn;
use App\Models\qr_login as QrLoginSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class QrLoginController extends Controller
{
    public function generate()
    {

        QrLoginSession::where('expires_at', '<', now())->delete();
        $session = QrLoginSession::create(['id' => Str::uuid(), 'expires_at' => now()->addMinutes(0.5)]);
        Log::info('QR Generated: ' . $session->id);
        return response()->json($session);
    }

    public function status($id)
    {
        $qrLoginSession = QrLoginSession::findOrFail($id);
        if ($qrLoginSession->status === 'scanned' && $qrLoginSession->user_id) {
            Auth::loginUsingId($qrLoginSession->user_id);
            broadcast(new SessionLoggedIn(
                Auth::id(),
                request()->ip(),
                now()->format('d M Y H:i'),
            ))->toOthers();
            $qrLoginSession->delete();
            Log::info('User Logged In via QR: ' . $qrLoginSession->user_id);
            return redirect()->route('dashboard')->with('success', 'Login Berhasil melalui QR Code.');
        }
        Log::info('QR Status Checked: ' . $qrLoginSession->status);
        return back()->with('error', 'QR Code Belum Dipindai.');
    }

    // Dipanggil oleh HP yang sudah login
    public function scan(Request $request)
    {
        Log::info('QR Scanned: ' . $request->id);
        $request->validate(['id' => 'required|uuid|exists:qr_logins,id']);
        $session = QrLoginSession::find($request->id);

        if ($session->expires_at < now()) {
            return response()->json(['error' => 'QR Code sudah kedaluwarsa.'], 400);
        }

        $user = auth()->user();
        $session->update(['user_id' => $user->id, 'status' => 'scanned']);

        // INTI LOGIKA WEBSOCKET: Pancarkan event!
        broadcast(new QrSessionScanned($session->id));

        return response()->json(['message' => 'Verifikasi berhasil.']);
    }
}
