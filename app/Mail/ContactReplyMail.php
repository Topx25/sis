<?php

namespace App\Mail;

use App\Models\ContactRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactReplyMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public ContactRequest $contactRequest,
        public string $replyMessage
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reply to your inquiry - Superior Integrated Solutions (SiS)',
        );
    }

    public function content(): Content
    {
        return new Content(
            htmlString: "<div style='font-family: Arial, sans-serif; line-height: 1.6; color: #333;'>
                <h2>Dear " . e($this->contactRequest->name) . ",</h2>
                <p>" . nl2br(e($this->replyMessage)) . "</p>
                <br>
                <hr style='border: 0; border-top: 1px solid #eee;' />
                <p style='font-size: 12px; color: #777;'>Superior Integrated Solutions (SiS) Support Team</p>
            </div>"
        );
    }

    public function attachments(): array
    {
        return [];
    }
}
