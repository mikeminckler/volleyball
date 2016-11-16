<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\Game;

class GameUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $game;
    public $message;
    public $user;

    public function __construct(Game $game, $message)
    {
        $this->game = $game;
        $this->message = $message;
        $this->user = auth()->user();
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return ['game.'.$this->game->id];
    }
}
