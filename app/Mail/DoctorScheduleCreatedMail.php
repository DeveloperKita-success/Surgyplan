<?php

namespace App\Mail;

use App\Models\SurgerySchedule;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DoctorScheduleCreatedMail extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(
        public SurgerySchedule $surgerySchedule,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Jadwal Operasi Baru',
        );
    }

    public function build(): self
    {
        return $this->view('emails.doctor-schedule-created');
    }
}
