<?php
// app/Http/Controllers/WebPushController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Notifications\TestWebPush;
use App\Models\UserPushDevice;
use NotificationChannels\WebPush\PushSubscription;
use Illuminate\Support\Str;

class WebPushController extends Controller
{
    public function subscribe(Request $request)
    {
        $request->validate([
            'endpoint'   => 'required|string',
            'keys.auth'  => 'required|string',
            'keys.p256dh' => 'required|string',
            'device_name' => 'nullable|string',
            'device_id'  => 'nullable|string',
        ]);

        $user = $request->user();

        // simpan subscription (disediakan oleh package)
        $user->updatePushSubscription(
            $request->endpoint,
            $request->input('keys.p256dh'),
            $request->input('keys.auth')
        );
       
        // ambil subscription id
        $sub = PushSubscription::query()
            ->where('subscribable_type', get_class($user))
            ->where('subscribable_id', $user->getKey())
            ->where('endpoint', $request->endpoint)
            ->latest('id')->first();
         
        if ($sub) {
            UserPushDevice::updateOrCreate(
                ['push_subscription_id' => $sub->id],
                [
                    'user_id'     => $user->id,
                    'device_name' => $request->string('device_name')->toString() ?: $this->guessDeviceName($request),
                    'device_id'   => $request->string('device_id')->toString() ?: Str::limit(sha1($request->userAgent() . $request->ip()), 24, ''),
                    'ip'          => $request->ip(),
                ]
            );
        }
        return response()->json(['status' => 'subscribed']);
    }

    public function unsubscribe(Request $request)
    {
        $request->validate(['endpoint' => 'required|string']);
        $request->user()->deletePushSubscription($request->endpoint);
        return response()->json(['status' => 'unsubscribed']);
    }

    public function test(Request $request)
    {
        $request->user()->notify(new TestWebPush(
            title: 'Halo!',
            body: 'Tes Web Push berhasil 🎉',
            url: url('/settings/sessions')
        ));
        return response()->json(['status' => 'sent']);
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
