<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class EmailCampaignMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public $subject;
    public $Campmessages;
    public $attachment;
    public $shopSetting;

    public function __construct($subject, $Campmessages, $attachment, $shopSetting)
    {
        $this->subject = $subject;
        $this->Campmessages = $Campmessages;
        $this->attachment = $attachment;
        $this->shopSetting = $shopSetting;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject ?? 'Campaign',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'email.email-campaign-mail',
            with: [
                'Campmessages' => $this->Campmessages,
                'shopSetting' => $this->shopSetting,
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [
            Attachment::fromData(fn() => $this->attachment, 'campaign-attachment.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
