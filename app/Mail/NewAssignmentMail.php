<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class NewAssignmentMail extends Mailable
{
    use Queueable, SerializesModels;

    public $workerName;
    public $meterCount;
    public $lotName;

    /**
     * Create a new message instance.
     */
    public function __construct($workerName, $meterCount, $lotName = null)
    {
        $this->workerName = $workerName;
        $this->meterCount = $meterCount;
        $this->lotName = $lotName;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New Meter Assignment Alert',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.assignments.new',
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
