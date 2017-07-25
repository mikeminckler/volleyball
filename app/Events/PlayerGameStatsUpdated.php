<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\Player;
use App\Game;
use App\Stat;

class PlayerGameStatsUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $player;
    public $game;
    public $stat;

    public function __construct(Player $player, Game $game, Stat $stat)
    {
        $this->player = $player;
        $this->game = $game;
        $this->stat = $stat;
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
