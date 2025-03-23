<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ReportUpdated implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $report;
    public $changeType;

    /**
     * Create a new event instance.
     *
     * @param array $report Data report
     * @param string|null $changeType Tipe perubahan (security, smartcab, dll)
     * @return void
     */
    public function __construct($report, $changeType = null)
    {
        $this->report = $report;
        $this->changeType = $changeType ?? ($report['change_type'] ?? null);
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new Channel('reports');
    }
    
    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'report' => $this->report,
            'change_type' => $this->changeType,
            'timestamp' => now()->toIso8601String()
        ];
    }
} 