<?php

namespace App\Mail;

use App\Models\ContactRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public ContactRequest $contactRequest) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(config('mail.from.address'), $this->contactRequest->name),
            subject: 'New Contact Request: ' . $this->contactRequest->name,
            replyTo: [
                new Address($this->contactRequest->email, $this->contactRequest->name),
            ],
        );
    }

    public function content(): Content
    {
        return new Content(
            view: 'emails.contact-request',
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
