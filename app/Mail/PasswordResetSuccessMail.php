<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Superadmin;

class PasswordResetSuccessMail extends Mailable
{
    use Queueable, SerializesModels;

    public $userData;
    public $userType;
    public $identifier;
    public $isSuperadminNotification;
    public $changedAt;

    /**
     * Create a new message instance.
     */
    public function __construct(array $userData, string $userType, string $identifier, ?Superadmin $superadmin = null)
    {
        $this->userData = $userData;
        $this->userType = $userType;
        $this->identifier = $identifier;
        $this->isSuperadminNotification = $superadmin !== null;
        $this->changedAt = now();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = $this->isSuperadminNotification
            ? 'Password Reset Completed Alert - NSUK Biometric Attendance System'
            : 'Password Reset Successful - NSUK Biometric Attendance System';

        return new Envelope(
            subject: $subject,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.password-reset-success',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
