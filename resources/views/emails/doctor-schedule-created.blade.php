<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Jadwal Operasi Baru</title>
</head>
<body style="font-family: Arial, sans-serif; color: #0f172a; line-height: 1.5;">
    <h2 style="margin-bottom: 12px;">Jadwal operasi baru telah dibuat</h2>

    <p>
        Anda mendapatkan jadwal operasi baru untuk pasien
        <strong>{{ $surgerySchedule->patient?->name ?? 'pasien' }}</strong>.
    </p>

    <ul>
        <li>Tanggal: {{ $surgerySchedule->surgery_date?->format('d M Y') ?? '-' }}</li>
        <li>Waktu: {{ substr((string) $surgerySchedule->start_time, 0, 5) }} - {{ substr((string) $surgerySchedule->end_time, 0, 5) }}</li>
        <li>Kamar operasi: {{ $surgerySchedule->operatingRoom?->room_name ?? '-' }}</li>
        <li>Diagnosa: {{ $surgerySchedule->surgeryRequest?->diagnosis_text ?? '-' }}</li>
        <li>Tindakan: {{ $surgerySchedule->surgeryRequest?->procedure_text ?? '-' }}</li>
    </ul>

    <p>Silakan cek dashboard dokter untuk melihat detail jadwal operasi.</p>
</body>
</html>
