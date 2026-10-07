<?php

namespace App\Jobs;

use App\Facades\SendMail;
use App\Mail\EmailCampaignMail;
use App\Mail\NoticeMail;
use App\Models\EmailSentLog;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendNoticeEmailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emails;
    protected $data;
    protected $senderId;

    /**
     * Create a new job instance.
     */
    public function __construct(array $emails, array $data, $senderId)
    {
        $this->emails = $emails;
        $this->data = $data;
        $this->senderId = $senderId;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        foreach ($this->emails as $email) {

            $subject = $this->data['subject'];
            $content = $this->data['message'];

            $attachment = $this->data['attachment'];
            $shopSetting = $this->data['shopSetting'];
            Log::warning($shopSetting);

            SendMail::sender($this->senderId)->to($email)->send(new NoticeMail($subject, $content, $attachment, $shopSetting));

            EmailSentLog::create([
                'source_id' => $this->data['notice_id'],
                'source' => EmailSentLog::NOTICE,
                'email' => $email,
                'sent_at' => now(),
                'status' => 1,
                'created_by' => $this->data['created_by'],
                'updated_by' => $this->data['created_by']
            ]);

        }
    }
}
