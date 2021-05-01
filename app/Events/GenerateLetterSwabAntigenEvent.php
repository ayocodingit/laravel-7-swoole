<?php

namespace App\Events;

use App\Models\SwabAntigen;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class GenerateLetterSwabAntigenEvent
{
    use Dispatchable;
    use InteractsWithSockets;
    use SerializesModels;

    public $swab_antigen;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(SwabAntigen $swab_antigen)
    {
        $this->swab_antigen = $swab_antigen;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-generate-letter-swab-antigen');
    }
}
