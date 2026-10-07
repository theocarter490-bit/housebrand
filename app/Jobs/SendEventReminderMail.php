<?php

namespace App\Jobs;

use App\Facades\SendMail;
use App\Mail\EventReminderMail;
use App\Models\EmailSentLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendEventReminderMail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public $user;
    public $event;
    public $senderId;

    public function __construct($user, $event, $senderId)
    {
        $this->user = $user;
        $this->event = $event;
        $this->senderId = $senderId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        SendMail::sender($this->senderId)->to($this->user->email)->send(new EventReminderMail($this->event));
        EmailSentLog::create([
            'source_id' => $this->event->id,
            'source' => EmailSentLog::EVENT,
            'email' => $this->user->email,
            'sent_at' => now(),
            'status' => 1,
            'created_by' => $this->event->user_id,
            'updated_by' => $this->event->user_id
        ]);
    }
}
