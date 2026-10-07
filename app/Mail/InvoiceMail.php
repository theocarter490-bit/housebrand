<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Attachment;


class InvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $pdf;
    public $messageContent;
    public $subject;
    public $settings;

    public function __construct($order, $pdf, $messageContent, $subject, $settings)
    {
        $this->order = $order;
        $this->pdf = $pdf;
        $this->messageContent = $messageContent;
        $this->subject = $subject;
        $this->settings = $settings;
    }

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'order.invoice',
            with: [
                'order' => $this->order,
                'messageContent' => $this->messageContent,
                'setting' => $this->settings
            ],
        );
    }

    public function attachments(): array
    {
        return [
            Attachment::fromData(fn() => $this->pdf, 'invoice-' . $this->order->code . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
