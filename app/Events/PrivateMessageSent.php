<?php

namespace App\Events;

use Carbon\Carbon;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PrivateMessageSent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

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
        $ids = [$this->from['id'], $this->to['id']];
        sort($ids);

        return new channel("chat.{$ids[0]}.{$ids[1]}");
    }

    public function broadcastAs()
    {
        return 'PrivateMessageSent';
    }
}
