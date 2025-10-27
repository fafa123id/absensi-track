<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SessionLoggedIn implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public int $userId; // <-- TAMBAHKAN PROPERTY INI
    public string $ip;
    public string $at;
    /**
     * Create a new event instance.
     */
    // Modifikasi constructor untuk menerima $userId
    public function __construct(
        int $userId,
        ?string $ip = null,
        ?string $at = null,
    ) {
        $this->userId = $userId; // <-- SIMPAN USER ID
        $this->ip = $ip;
        $this->at = $at;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        // Gunakan $this->userId yang sudah disimpan, BUKAN auth()->id()
        return [
            new PrivateChannel('App.User.' . $this->userId),
        ];
    }
    public function broadcastWith(): array
    {
        return [
            'title' => 'Aktivitas Login',
            'body'  => "Seseorang baru login • IP {$this->ip} Tidak mengenali aktivitas?",
            'url'   => route('profile.edit'),
            'meta'  => [
                'ip'    => $this->ip,
                'at'    => $this->at,
            ],
        ];
    }
}
