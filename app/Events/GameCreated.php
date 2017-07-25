<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

class GameCreated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $message;
    public $link;

    public function __construct($message, $link)
    {
        $this->message = $message;
        $this->link = $link;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return ['auth.info'];
    }
}
