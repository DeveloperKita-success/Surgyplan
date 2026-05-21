<?php

namespace App\Mail;

use App\Models\SurgeryRequest;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Collection;

class DoctorScheduleConflictMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * @param  Collection<int, \App\Models\SurgerySchedule>  $conflictingSchedules
     */
    public function __construct(
        public SurgeryRequest $surgeryRequest,
        public Collection $conflictingSchedules,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Jadwal Dokter Bentrok dengan Pengajuan Operasi Baru',
        );
    }

    public function build(): self
    {
        return $this->view('emails.doctor-schedule-conflict');
    }
}
