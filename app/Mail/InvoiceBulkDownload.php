<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Attachment;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class InvoiceBulkDownload extends Mailable
{
    use Queueable, SerializesModels;

    private string $attachmentFilename;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(string $attachmentFilename)
    {
        $this->attachmentFilename = $attachmentFilename;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Téléchargement de factures Academico',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.invoice-bulk-download',
        );
    }

    /**
     * Get the attachments for the message.
     */
    public function attachments(): array
    {
        return [
            Attachment::fromPath(storage_path($this->attachmentFilename)),
        ];
    }
}
