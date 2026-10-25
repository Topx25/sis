<?php

namespace App\Services;

use App\Mail\ContactRequestMail;
use App\Models\ContactRequest;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Mail;

class ContactService
{
    public function createContactRequest(array $data): ContactRequest
    {
        $contactRequest = ContactRequest::create($data);

        $recipient = env('MAIL_TO_ADDRESS', config('mail.from.address', 'sales@sis-eg.org'));
        Mail::to($recipient)->send(new ContactRequestMail($contactRequest));

        return $contactRequest;
    }

    public function getPaginatedRequests(int $perPage = 15): LengthAwarePaginator
    {
        return ContactRequest::latest()->paginate($perPage);
    }

    public function updateStatus(ContactRequest $contactRequest, string $status): ContactRequest
    {
        $contactRequest->update(['status' => $status]);
        return $contactRequest;
    }

    public function replyToRequest(ContactRequest $contactRequest, string $message): ContactRequest
    {
        Mail::to($contactRequest->email)->send(new \App\Mail\ContactReplyMail($contactRequest, $message));
        $contactRequest->update([
            'status' => 'read',
            'reply' => $message,
            'replied_at' => now(),
        ]);
        return $contactRequest;
    }
}


