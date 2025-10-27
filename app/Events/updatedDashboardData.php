<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class updatedDashboardData implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $companyId;
    // Modifikasi constructor untuk menerima $userId
    public function __construct(int $companyId)
    {
        $this->companyId = $companyId;
    }

    /**
     * Get the channels the event should broadcast on.
     */
    public function broadcastOn(): array
    {
        // Gunakan $this->userId yang sudah disimpan, BUKAN auth()->id()
        return [
            new PrivateChannel('Dashboard.'. $this->companyId),
        ];
    }
}