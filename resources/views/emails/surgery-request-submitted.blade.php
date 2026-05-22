<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <title>Pengajuan Operasi Baru</title>
</head>
@php
    $priority = $surgeryRequest->patient_priority ?? '-';
    $priorityStyles = [
        'Imminent' => 'background-color: #fff1f2; color: #be123c; border-color: #fecdd3;',
        'Cito' => 'background-color: #fff7ed; color: #c2410c; border-color: #fed7aa;',
        'Urgent' => 'background-color: #fffbeb; color: #b45309; border-color: #fde68a;',
        'Expedited' => 'background-color: #f0f9ff; color: #0369a1; border-color: #bae6fd;',
        'Elektif' => 'background-color: #ecfdf5; color: #047857; border-color: #a7f3d0;',
    ];
    $priorityStyle = $priorityStyles[$priority] ?? 'background-color: #f8fafc; color: #475569; border-color: #e2e8f0;';
    $requestTime = substr((string) $surgeryRequest->requested_start_time, 0, 5);

    if ($surgeryRequest->requested_end_time) {
        $requestTime .= ' - '.substr((string) $surgeryRequest->requested_end_time, 0, 5);
    }
@endphp
<body style="margin: 0; background-color: #f1f5f9; font-family: Arial, sans-serif; color: #0f172a; line-height: 1.5;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color: #f1f5f9; padding: 32px 16px;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width: 640px; overflow: hidden; border-radius: 18px; background-color: #ffffff; box-shadow: 0 20px 45px rgba(15, 23, 42, 0.08);">
                    <tr>
                        <td style="background-color: #0e7490; padding: 28px 32px;">
                            <p style="margin: 0 0 8px; color: #cffafe; font-size: 13px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase;">
                                SurgyPlan
                            </p>
                            <h1 style="margin: 0; color: #ffffff; font-size: 26px; line-height: 1.25;">
                                Pengajuan Operasi Baru
                            </h1>
                            <p style="margin: 12px 0 0; color: #ecfeff; font-size: 15px;">
                                Perawat ruangan telah membuat pengajuan yang perlu ditinjau oleh tim perawat OK.
                            </p>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 30px 32px 12px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="vertical-align: top;">
                                        <p style="margin: 0; color: #64748b; font-size: 13px; font-weight: 700; text-transform: uppercase;">
                                            Pasien
                                        </p>
                                        <h2 style="margin: 6px 0 0; color: #0f172a; font-size: 22px; line-height: 1.3;">
                                            {{ $surgeryRequest->patient?->name ?? 'Pasien' }}
                                        </h2>
                                        <p style="margin: 6px 0 0; color: #64748b; font-size: 14px;">
                                            No RM {{ $surgeryRequest->patient?->medical_record_number ?? '-' }}
                                        </p>
                                    </td>
                                    <td align="right" style="vertical-align: top;">
                                        <span style="display: inline-block; border: 1px solid; border-radius: 999px; padding: 7px 12px; font-size: 13px; font-weight: 700; {{ $priorityStyle }}">
                                            {{ $priority }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 10px 32px 26px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="border: 1px solid #e2e8f0; border-radius: 14px; overflow: hidden;">
                                <tr>
                                    <td style="width: 42%; border-bottom: 1px solid #e2e8f0; background-color: #f8fafc; padding: 13px 16px; color: #64748b; font-size: 13px; font-weight: 700;">Ruang asal</td>
                                    <td style="border-bottom: 1px solid #e2e8f0; padding: 13px 16px; color: #0f172a; font-size: 14px;">{{ $surgeryRequest->patient?->origin_room ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 42%; border-bottom: 1px solid #e2e8f0; background-color: #f8fafc; padding: 13px 16px; color: #64748b; font-size: 13px; font-weight: 700;">Tanggal pengajuan</td>
                                    <td style="border-bottom: 1px solid #e2e8f0; padding: 13px 16px; color: #0f172a; font-size: 14px;">{{ $surgeryRequest->requested_date?->format('d M Y') ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 42%; border-bottom: 1px solid #e2e8f0; background-color: #f8fafc; padding: 13px 16px; color: #64748b; font-size: 13px; font-weight: 700;">Waktu</td>
                                    <td style="border-bottom: 1px solid #e2e8f0; padding: 13px 16px; color: #0f172a; font-size: 14px;">{{ $requestTime ?: '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 42%; border-bottom: 1px solid #e2e8f0; background-color: #f8fafc; padding: 13px 16px; color: #64748b; font-size: 13px; font-weight: 700;">Dokter</td>
                                    <td style="border-bottom: 1px solid #e2e8f0; padding: 13px 16px; color: #0f172a; font-size: 14px;">{{ $surgeryRequest->requestedDoctor?->user?->name ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 42%; border-bottom: 1px solid #e2e8f0; background-color: #f8fafc; padding: 13px 16px; color: #64748b; font-size: 13px; font-weight: 700;">Diagnosa</td>
                                    <td style="border-bottom: 1px solid #e2e8f0; padding: 13px 16px; color: #0f172a; font-size: 14px;">{{ $surgeryRequest->diagnosis_text ?? '-' }}</td>
                                </tr>
                                <tr>
                                    <td style="width: 42%; background-color: #f8fafc; padding: 13px 16px; color: #64748b; font-size: 13px; font-weight: 700;">Tindakan</td>
                                    <td style="padding: 13px 16px; color: #0f172a; font-size: 14px;">{{ $surgeryRequest->procedure_text ?? '-' }}</td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <tr>
                        <td style="padding: 0 32px 32px;">
                            <div style="border-radius: 14px; background-color: #ecfeff; padding: 18px 20px;">
                                <p style="margin: 0; color: #155e75; font-size: 14px;">
                                    Silakan buka dashboard perawat OK untuk meninjau checklist, memverifikasi kesiapan pasien, dan memproses pengajuan ini.
                                </p>
                            </div>
                        </td>
                    </tr>

                    <tr>
                        <td style="border-top: 1px solid #e2e8f0; padding: 18px 32px; color: #94a3b8; font-size: 12px;">
                            Email ini dikirim otomatis oleh SurgyPlan.
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
