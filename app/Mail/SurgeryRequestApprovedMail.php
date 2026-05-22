<?php

namespace App\Mail;

use App\Models\SurgeryRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class SurgeryRequestApprovedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SurgeryRequest $surgeryRequest,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pengajuan Operasi Disetujui',
        );
    }

    public function build(): self
    {
        return $this->view('emails.surgery-request-approved');
    }
}
