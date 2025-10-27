<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SessionLoggedOut implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public string $sessionId;
    public int $userId; // <-- TAMBAHKAN PROPERTY INI

    /**
     * Create a new event instance.
     */
    // Modifikasi constructor untuk menerima $userId
    public function __construct(string $sessionId, int $userId)
    {
        $this->sessionId = $sessionId;
        $this->userId = $userId; // <-- SIMPAN USER ID
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
}