<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;

class LowStockNotification implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets;

    public $barangId;
    public $barangNama;
    public $stok;
    public $message;

    /**
     * Create a new event instance.
     */
    public function __construct($barangId, $barangNama, $stok)
    {
        $this->barangId = $barangId;
        $this->barangNama = $barangNama;
        $this->stok = $stok;
        $this->message = "Stok barang '{$barangNama}' tersisa {$stok} unit!";
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('low-stock'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'low-stock-alert';
    }

    /**
     * Get the data to broadcast.
     */
    public function broadcastWith(): array
    {
        return [
            'barang_id' => $this->barangId,
            'barang_nama' => $this->barangNama,
            'stok' => $this->stok,
            'message' => $this->message,
            'timestamp' => now()->toISOString(),
        ];
    }
}
