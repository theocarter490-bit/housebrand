<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ProposalInvoiceMail extends Mailable
{
    use Queueable, SerializesModels;

    public $invoice;
    public $pdf;
    public $messageContent;
    public $subject;
    public $settings;


    public function __construct($invoice, $pdf, $messageContent, $subject, $settings)
    {
        $this->invoice = $invoice;
        $this->pdf = $pdf;
        $this->messageContent = $messageContent;
        $this->subject = $subject;
        $this->settings = $settings;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
//        dd($this->invoice);
        return new Content(
            view: 'project-management.project.projects.invoice.invoice',
            with: [
                'invoice' => $this->invoice,
                'messageContent' => $this->messageContent,
                'setting' => $this->settings
            ],
        );
    }


    public function attachments(): array
    {
        return [
            Attachment::fromData(fn() => $this->pdf, 'invoice-' . $this->invoice->code . '.pdf')
                ->withMime('application/pdf'),
        ];
    }
}
