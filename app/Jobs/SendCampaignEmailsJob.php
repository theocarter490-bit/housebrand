<?php

namespace App\Jobs;

use App\Facades\SendMail;
use App\Mail\EmailCampaignMail;
use App\Mail\EventReminderMail;
use App\Models\CampaignEmailLog;
use App\Models\EmailSentLog;
use Auth;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCampaignEmailsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $emails;
    protected $data;
    protected $senderId;

    public function __construct(array $emails, array $data, $senderId)
    {
        $this->emails = $emails;
        $this->data = $data;
        $this->senderId = $senderId;
    }

    public function handle(): void
    {
        foreach ($this->emails as $email) {

            $subject = $this->data['subject'];
            $campaign_name = $this->data['subject'];
            $Campmessages = $this->data['message'];
            $attachment = $this->data['attachment'];
            $shopSetting = shopSetting();

            SendMail::sender($this->senderId)->to($email)->send(new EmailCampaignMail($subject, $Campmessages, $attachment, $shopSetting));
            EmailSentLog::create([
                'source_id' => $this->data['campaign_id'],
                'source' => EmailSentLog::CAMPAIGN,
                'email' => $email,
                'sent_at' => now(),
                'status' => 1,
                'created_by' => $this->data['created_by'],
                'updated_by' => $this->data['created_by']
            ]);
        }
    }
}
