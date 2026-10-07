<?php

namespace App\Events;

use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public $message;
    public $from;
    public $to;
    public $created_at;
    public $file;

    public function __construct($message, $from, $to, $file, $created_at = null)
    {
        $this->message = $message;
        $this->from = $from;
        $this->to = $to;
        $this->created_at = Carbon::now()->format('M j, g:i A');
        $this->file = $file;
    }

    public function broadcastOn()
    {
        $id = $this->to['id'];
        return new channel("message.{$id}");
    }

    public function broadcastAs()
    {
        return 'MessageSent';
    }
}
