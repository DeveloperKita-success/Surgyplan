<?php

namespace App\Mail;

use App\Models\SurgeryRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SurgeryRequestSubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SurgeryRequest $surgeryRequest,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pengajuan Operasi Baru',
        );
    }

    public function build(): self
    {
        return $this->view('emails.surgery-request-submitted');
    }
}
