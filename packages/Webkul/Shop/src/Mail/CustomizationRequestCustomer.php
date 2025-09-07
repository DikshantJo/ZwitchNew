<?php

namespace Webkul\Shop\Mail;

use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\Storage;

class CustomizationRequestCustomer extends Mailable
{
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public array $data) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $referenceNumber = $this->getReferenceNumber();
        
        return new Envelope(
            to: [
                new Address(
                    $this->data['email'],
                    $this->data['name']
                ),
            ],
            subject: 'Customization Request Confirmation - Reference #' . $referenceNumber,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'shop::emails.customization-request-customer',
            with: [
                'referenceNumber' => $this->getReferenceNumber(),
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
        $attachments = [];

        if (!empty($this->data['files'])) {
            foreach ($this->data['files'] as $file) {
                try {
                    if (Storage::disk('public')->exists($file['path'])) {
                        $attachments[] = Attachment::fromStorageDisk('public', $file['path'])
                            ->as($file['original_name'])
                            ->withMime($file['mime_type'] ?? 'application/octet-stream');
                    }
                } catch (\Exception $e) {
                    // Log attachment errors but don't fail the email
                    report($e);
                }
            }
        }

        return $attachments;
    }

    /**
     * Get the reference number for this request.
     *
     * @return string
     */
    private function getReferenceNumber(): string
    {
        return 'CUST-' . date('Ymd') . '-' . strtoupper(substr(md5($this->data['email'] . time()), 0, 6));
    }
}
