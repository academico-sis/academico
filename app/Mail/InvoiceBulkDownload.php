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
     *
     * @return \Illuminate\Mail\Mailables\Envelope
     */
    public function envelope()
    {
        return new Envelope(
            subject: 'Téléchargement de factures Academico',
        );
    }

    /**
     * Get the message content definition.
     *
     * @return \Illuminate\Mail\Mailables\Content
     */
    public function content()
    {
        return new Content(
            view: 'emails.invoice-bulk-download',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array
     */
    public function attachments()
    {
        return [
            Attachment::fromPath(storage_path($this->attachmentFilename)),
        ];
    }
}
