<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class ContactMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * The contact form data.
     */
    public array $contactData;

    /**
     * Create a new message instance.
     */
    public function __construct(array $contactData)
    {
        $this->contactData = $contactData;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Новое сообщение с сайта Mounteam',
            replyTo: [
                $this->contactData['email'] => $this->contactData['name'] ?? 'Клиент'
            ]
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.contact',
            with: [
                'name' => $this->contactData['name'] ?? 'Не указано',
                'email' => $this->contactData['email'],
                'phone' => $this->contactData['phone'] ?? 'Не указан',
                'service' => $this->contactData['service'] ?? 'Не указана',
                'message' => $this->contactData['message'],
                'submittedAt' => now()->format('d.m.Y H:i'),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [];
    }
}
