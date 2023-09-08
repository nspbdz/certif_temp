<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CertifBlastMail extends Mailable
{
    use Queueable, SerializesModels;

    protected $data, $file;
    /**
     * Create a new message instance.
     */
    public function __construct($data, $file)
    {
        //
        $this->data = $data;
        $this->file = $file;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address($this->data['sender_email'], $this->data['sender_name']),
            subject: $this->data['subject'],

        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.certif',
            with: [
                'header_image' => $this->data['header_image'],
                'body' => $this->data['body']
            ],
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if ($this->file) {
            return [
                Attachment::fromData(fn () => $this->file, $this->data['certificate_name'] . '.pdf')
                    ->withMime('application/pdf')
            ];
        } else {
            return [];
        }
    }
}
