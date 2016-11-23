<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Queue\SerializesModels;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;

use App\PlayerStat;

class TeamGameChartUpdated implements ShouldBroadcast
{
    use InteractsWithSockets, SerializesModels;

    public $player_stat;

    public function __construct(PlayerStat $player_stat)
    {
        $this->player_stat = $player_stat;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return Channel|array
     */
    public function broadcastOn()
    {
        return ['game.'.$this->player_stat->game_id];
    }
}
