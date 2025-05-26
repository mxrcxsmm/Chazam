<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NuevoMensajeComunidad implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $mensaje;
    public $id_chat;

    public function __construct($mensaje, $id_chat)
    {
        $this->mensaje = $mensaje;
        $this->id_chat = $id_chat;
    }

    public function broadcastOn()
    {
        return new PresenceChannel('comunidad.' . $this->id_chat);
    }
} 