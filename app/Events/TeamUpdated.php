<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\Team;

class TeamUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $team;
    public $message;

    public function __construct(Team $team, $message)
    {
        $this->team = $team;
        $this->message = $message;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return ['team.'.$this->team->id];
    }
}
