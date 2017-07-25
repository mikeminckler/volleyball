<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\Stat;

class StatUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $stat;
    public $message;

    public function __construct(Stat $stat, $message)
    {
        $this->stat = $stat;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return ['stat.'.$this->stat->id];
    }

}
