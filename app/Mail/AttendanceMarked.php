<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use App\Models\Attendance;

class AttendanceMarked extends Mailable
{
    use Queueable, SerializesModels;

    public $attendance;

    /**
     * Create a new message instance.
     */
    public function __construct(Attendance $attendance)
    {
        $this->attendance = $attendance;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Attendance Marked Successfully',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.attendance_marked',
            with: [
                'studentName' => $this->attendance->student->user->full_name,
                'courseCode' => $this->attendance->classroom->course->course_code,
                'courseTitle' => $this->attendance->classroom->course->course_title,
                'sessionCode' => $this->attendance->attendanceSession->code,
                'capturedAt' => $this->attendance->captured_at,
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
        return [];
    }
}
