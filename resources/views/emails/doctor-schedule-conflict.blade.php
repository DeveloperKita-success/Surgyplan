<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Jadwal Dokter Bentrok</title>
</head>
<body style="font-family: Arial, sans-serif; color: #0f172a; line-height: 1.5;">
    <h2 style="margin-bottom: 12px;">Jadwal dokter bentrok dengan pengajuan baru</h2>

    <p>
        Ada pengajuan operasi baru untuk
        <strong>{{ $surgeryRequest->patient?->name ?? 'pasien' }}</strong>
        pada {{ $surgeryRequest->requested_date?->format('d M Y') }}
        pukul {{ substr((string) $surgeryRequest->requested_start_time, 0, 5) }}
        - {{ substr((string) $surgeryRequest->requested_end_time, 0, 5) }}.
    </p>

    <p>Dokter yang dipilih sudah memiliki jadwal operasi aktif pada waktu berikut:</p>

    <ul>
        @foreach ($conflictingSchedules as $schedule)
            <li>
                {{ $schedule->surgery_date?->format('d M Y') }}
                pukul {{ substr((string) $schedule->start_time, 0, 5) }}
                - {{ substr((string) $schedule->end_time, 0, 5) }}
                @if ($schedule->patient)
                    untuk {{ $schedule->patient->name }}
                @endif
                @if ($schedule->operatingRoom)
                    di {{ $schedule->operatingRoom->room_name }}
                @endif
            </li>
        @endforeach
    </ul>

    <p>
        Mohon cek kembali ketersediaan dokter sebelum pengajuan ini disetujui menjadi jadwal operasi.
    </p>
</body>
</html>
